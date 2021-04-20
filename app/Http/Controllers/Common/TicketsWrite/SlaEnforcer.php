<?php


namespace App\Http\Controllers\Common\TicketsWrite;

use App\Events\Ticket\TicketSlaEnforcing;
use App\Http\Controllers\SLA\BusinessHourCalculation;
use App\Http\Controllers\SLA\Reminders;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Filters\Filter;
use App\Model\helpdesk\Filters\Label;
use App\Model\helpdesk\Filters\Tag;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Ticket\Halt;
use App\Model\helpdesk\Ticket\Ticket_Status as Status;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\TicketSla;
use App\Model\helpdesk\Ticket\TicketSlaMeta;
use App\Traits\FaveoDateParser;
use Carbon\Carbon;
use DB;
use Request;

class SlaEnforcer extends TicketWorkflowController
{
    use FaveoDateParser;

    /**
     * Ticket instance
     * @var Tickets
     */
    private $ticket;

    /**
     * If first response is done on ticket or not
     * @var bool
     */
    private $isFirstResponseHappening;


    /**
     * If ticket needs to be halt
     * @var bool
     */
    private $shallHalt;

    /**
     * Business hour which is getting enforced on the ticket
     * @var BusinessHours
     */
    private $businessHour;

    /**
     * Instance of ticket before getting fresh copy from database
     * USE: so that it can be used to inspect data which was there before saving to database
     * @var Tickets
     */
    private $oldTicket;

    /**
     * SlaEnforcer constructor.
     * @param Tickets $ticket
     * to this class)
     */
    public function __construct(Tickets &$ticket)
    {
        $this->oldTicket = clone $ticket;

        $this->ticket = $ticket->fresh();

        $this->ticket->sla = $this->getSlaByTicket();

        $this->isFirstResponseHappening = $this->isFirstResponseHappening();

        $this->shallHalt = $this->isHaltedStatus($ticket->status);

        $this->updateHaltData();

        $this->businessHour = BusinessHours::find($this->getBusinessHourByTicket());
    }

    /**
     * Updates ticket SLA related fields (including flags) in ticket instance which can be saved by the caller
     * @throws \Exception
     */
    public function handleSlaRelatedUpdates()
    {
        if(!$this->shallEnforceSla()){
            return;
        }

        $estimatedDueDate = $this->getDueDate();

        $this->ticket->duedate = $this->shallHalt ? null : $estimatedDueDate;

        $this->handleResponseOperations($estimatedDueDate);

        // means ticket is closed
        if($this->ticket->statuses->purpose_of_status == 2){
            $this->handleCloseOperations($estimatedDueDate);
        }

        if($this->ticket->statuses->purpose_of_status == 1){
            $this->handleOpenOperations($estimatedDueDate);
        }

        // creating reminders for the ticket
        (new Reminders())->createSlaReminders($this->ticket);

        // updating table without using model to avoid any event dispatch
        if (count($this->ticket->getDirty())) {

            event(new TicketSlaEnforcing($this->ticket->getDirty(), $this->ticket->sla));

            DB::table('tickets')->where('id', $this->ticket->id)->update($this->ticket->getDirty());
        }
    }

    /**
     * Handles updating of all columns that requires after ticket is closed
     * @param Carbon $estimatedDueDate
     */
    protected function handleCloseOperations($estimatedDueDate)
    {
        // there shouldn't be any action if ticket is already closed
        if(!$this->ticket->closed) {
            $this->ticket->resolution_due_by = null;
            $this->ticket->closed = 1;
            $this->ticket->resolution_due_by = null;
            $this->ticket->reopened = 0;
            $this->ticket->reopened_at = null;
            $this->ticket->closed_at = Carbon::now();
            $this->ticket->resolution_time = $this->getResolutionTime();
            $this->ticket->is_resolution_sla = Carbon::now()->lt($estimatedDueDate) ? 1 : 0;
        }
    }

    /**
     * Handles updating all columns that requires after ticket is opened
     * @param Carbon $estimatedDuedate
     */
    protected function handleOpenOperations($estimatedDuedate)
    {
        if($this->ticket->closed){
            // means its getting reopened
            $this->ticket->reopened = 1;
            $this->ticket->reopened_at = Carbon::now();
        }

        $this->ticket->closed = 0;
        $this->ticket->closed_at = null;
        $this->ticket->resolution_due_by = $estimatedDuedate;
        $this->ticket->is_resolution_sla = null;
        $this->ticket->resolution_time = null;
    }

    /**
     * Handles updating all columns that requires after a response is made
     * @param $estimatedDueDate
     */
    private function handleResponseOperations($estimatedDueDate)
    {
        $this->ticket->response_due_by = $this->isFirstResponsePending() ? $this->ticket->duedate : null;

        if($this->isFirstResponseHappening){
            $this->ticket->is_response_sla = Carbon::now()->lt($estimatedDueDate) ? 1 : 0;
            $this->ticket->first_response_time = $this->getFirstResponseTime();
            $this->ticket->resolution_due_by = $this->ticket->duedate;
            $this->ticket->response_due_by = null;
        }
    }

    /**
     * @return Carbon|null
     */
    private function getFirstResponseTime()
    {
        return Thread::where("ticket_id", $this->ticket->id)->where("thread_type", "first_reply")->value("created_at");
    }

    /**
     * Checks it ticket has first reply thread or not and returns boolean values
     * @param  void
     * @return boolean  $isResponded (true if first reply thread exists false otherwise)
     */
    private function isFirstResponseHappening()
    {
        $lastThreadType = Thread::where('ticket_id', $this->ticket->id)
            ->orderBy("id", "desc")
            ->value("thread_type");

        return $lastThreadType == "first_reply";
    }

    /**
     * Checks it ticket has first reply thread or not and returns boolean values
     * @param  void
     * @return boolean  $isResponded (true if first reply thread exists false otherwise)
     */
    private function isFirstResponsePending()
    {
        return !(bool)Thread::where('ticket_id', $this->ticket->id)->where('thread_type', 'first_reply')->where('poster', 'support')->count();
    }

    /**
     * Gets duedate
     * @return Carbon
     * @throws \Exception
     */
    private function getDueDate()
    {
        if($this->ticket->is_manual_duedate || Request::input("duedate")) {
            return $this->ticket->duedate;
        }

        $haltedTimeInMinutes = Halt::where('ticket_id', $this->ticket->id)->sum('halted_time');

        $startTimeForDueDateCalculation = $this->ticket->created_at->addMinutes($haltedTimeInMinutes);

        $businessHourCalculation = new BusinessHourCalculation($this->businessHour);

        $slaMeta = TicketSlaMeta::where(['ticket_sla_id'=> $this->ticket->sla, 'priority_id'=> $this->ticket->priority_id])
            ->select('resolve_within', 'respond_within')->first();

        $actionTime = $this->isFirstResponsePending() ? $slaMeta->respond_within: $slaMeta->resolve_within ;

        return $businessHourCalculation->getDueDate($startTimeForDueDateCalculation, $this->getTimeDiffInMinutes($actionTime) * 60);
    }

    /**
     * Gets SLA by its ticket by checking enforcements
     * @return int
     */
    private function getSlaByTicket() : int
    {
        // convert ticket to old key,
        $ticketValuesArray = $this->ticket->toArray();

        // once ticket is created/updated, we do not need to re-fetch its fields and it can be kept in memory
        // merging tags and labels to it
        $ticketValuesArray['tag_ids'] = $this->getTicketTagIds();
        $ticketValuesArray['label_ids'] = $this->getTicketLabelIds();

        $ticketValuesArray = array_merge(
            $ticketValuesArray,
            $this->getUserDetails($this->ticket->user_id),
            $this->getCustomTicketCustomFields(),
            $this->getTitleAndBody()
        );

        $this->formatTicketsArrayFromOldToNewKey($ticketValuesArray);

        // if yes, enforce that SLA
        $slas = TicketSla::orderBy('order', 'asc')->where('status', 1)->get();

        // loop over SLAs to get which SLA to enforce
        foreach ($slas as $sla) {
            $rules = $this->getRules($sla->id, 'sla');

            if ($this->checkRulesAndValues($rules, $ticketValuesArray, $sla->matcher)) {
                return $sla->id;
            }
        }
        return TicketSla::where('is_default', 1)->value('id');
    }

    /**
     * Gets title and body of the ticket
     * @return array
     */
    private function getTitleAndBody()
    {
        $firstThread = Ticket_Thread::where('ticket_id', $this->ticket->id)->orderBy('id', 'asc')->select('title', 'body')->first();

        // if firstThread is null
        if(!$firstThread){
            return [];
        }
        return ["subject"=>$firstThread->title, "body"=>$firstThread->body];
    }

    /**
     * Gets custom field value for ticket
     * @return array
     */
    private function getCustomTicketCustomFields()
    {
        return CustomFormValue::where("custom_type", "App\Model\helpdesk\Ticket\Tickets")
            ->where("custom_id", $this->ticket->id)
            ->get()
            ->map(function ($data) {
                return [ "custom_" . $data->form_field_id => $data->value ];
            })->collapse()->toArray();
    }

    /**
     * Gets business hours according to the ticket passed
     * @return int
     */
    private function getBusinessHourByTicket() : int
    {
        // check user org department business hour
        $organisationDepartmentId = $this->ticket->user->getUsersOrganisations()->where('org_department', '!=', null)->value('org_department');

        // if organisation department is present
        $businessHourId = OrganizationDepartment::where('id', $organisationDepartmentId)->value('business_hours_id');
        if ($businessHourId) {
            return $businessHourId;
        }

        // check department business hour
        $businessHourId = $this->ticket->department->business_hour;

        if ($businessHourId) {
            return $businessHourId;
        }

        $slaMetaData = TicketSla::getSlaMetaDataByTicket($this->ticket);

        if($slaMetaData && $slaMetaData->business_hour_id) {
            return $slaMetaData->business_hour_id;
        }

        return BusinessHours::where('is_default', 1)->value('id');
    }

    /**
     * Gets linked tag Ids with the ticket
     * @return array
     */
    private function getTicketTagIds()
    {
        $tagNames = Filter::where('ticket_id', $this->ticket->id)->where('key', 'tag')->pluck('value')->toArray();
        return Tag::whereIn('name', $tagNames)->pluck('id')->toArray();
    }

    /**
     * Gets linked label Ids with the ticket
     * @return array
     */
    private function getTicketLabelIds()
    {
        $labelNames = Filter::where('ticket_id', $this->ticket->id)->where('key', 'label')->pluck('value')->toArray();
        return Label::whereIn('title', $labelNames)->pluck('id')->toArray();
    }

    /**
     * ##################################### EXPLANATION OF HALT LOGIC #############################################################
     *
     * - When a ticket goes in halted status at time t1, we create an entry in halts table with halted_at as t1 and halted_time as 0
     *
     * - When the same ticket goes in non-halted status at time t2, we check for old halted entry and updates
     *  its halted time to t2-t1 (without considering business hours).
     *
     * - Normally duedate is calculated by adding resolution/response time to created_at in business hour. Here, we will
     *  calculate it by adding resolution/response time in (created_at + (t2-t1)) in business hour
     *
     * ##################################### EXPLANATION OF HALT LOGIC #############################################################
     */

    /**
     * Handles actions/changes which is required once a ticket is halted
     */
    private function updateHaltData()
    {
        $this->updateOldHaltIfNeeded();

        $this->createNewHaltIfNeeded();
    }

    /**
     * Creates new halt based on ticket current status
     */
    private function createNewHaltIfNeeded()
    {
        // or during a new ticket creation process
        if ($this->isStatusChangingFromNonHaltedToHalted()) {
            // once ticket comes from open to close, duedate should be overridden by SLA duedate, since
            // it should be NULL
            $this->ticket->is_manual_duedate = 0;
            return Halt::create(['ticket_id'=>$this->ticket->id, 'halted_time'=>0, 'time_used'=>100]);
        }
    }

    /**
     * Updates old halt time difference in non-business hour
     */
    private function updateOldHaltIfNeeded()
    {
        // if current one in non halt and previous status was halted, in that case we need to update halts table's halted_time
        // or may be we need to record only the time when status is set from halted to non-halted
        if ($this->isStatusChangingFromHaltedToNonHalted()) {
            $haltObj = Halt::where('ticket_id', $this->ticket->id)->orderBy('id', 'desc')->first();
            if (!$haltObj) {
                return false;
            }
            // using halt table created_at column instead of using halted_at
            // that column is not required anymore and can be removed in future versions
            $haltObj->halted_time = Carbon::now()->diffInMinutes($haltObj->created_at);

            // once ticket comes from close to open, duedate should be overridden by SLA duedate, since
            // a new cycle of duedate should start
            $this->ticket->is_manual_duedate = 0;
            return $haltObj->save();
        }
    }

    /**
     * Tells if status is changing from halted to non-halted
     * @return bool
     */
    private function isStatusChangingFromHaltedToNonHalted()
    {
        if (!isset($this->oldTicket->getOriginal()['status'])) {
            return false;
        }
        $originalStatus = $this->oldTicket->getOriginal()['status'];
        $currentStatus = $this->oldTicket->status;

        $isOriginalHalted = $this->isHaltedStatus($originalStatus);
        $isCurrentHalted = $this->isHaltedStatus($currentStatus);

        return ($isOriginalHalted && !$isCurrentHalted);
    }

    /**
     * Tells if status is changing from non-halted to halted
     * @return bool
     */
    private function isStatusChangingFromNonHaltedToHalted()
    {
        // during new ticket creation, old status will not exist. in that case if current status is halted,
        // it should be considered as status changing from non-halted to halted
        $isNewTicket = !isset($this->oldTicket->getOriginal()['status']);

        if ($isNewTicket) {
            return $this->shallHalt;
        }

        $originalStatus = $this->oldTicket->getOriginal()['status'];
        $currentStatus = $this->ticket->status;

        $isOriginalHalted = $this->isHaltedStatus($originalStatus);
        $isCurrentHalted = $this->isHaltedStatus($currentStatus);

        return (!$isOriginalHalted && $isCurrentHalted);
    }

    /**
     * Checks if status is halted
     * @param $statusId
     * @return mixed
     */
    private function isHaltedStatus($statusId)
    {
        return persistentCache('is_status_halted', function() use ($statusId){
            return Status::where('id', $statusId)->value('halt_sla');
        }, 20, [$statusId]);
    }

    /**
     * Gets response time for the last response.
     * Response time is difference between client reply and agent reply
     * @return int
     */
    public function getResponseTime() : ?int
    {
        // start date should be client's last reply
        $startTime = Thread::where('ticket_id', $this->ticket->id)
            ->where('poster', 'client')
            ->orderBy('id', 'desc')
            ->value('created_at');

        $endTime = Carbon::now();

        if (!$startTime) {
            return null;
        }
        return $this->getTimeDifferenceInBH($startTime, $endTime);
    }

    /**
     * Gives resolution time of the ticket
     * @return int|null
     */
    private function getResolutionTime() : ?int
    {
        $startTime = $this->ticket->created_at;
        $endTime = Carbon::now();
        return $this->getTimeDifferenceInBH($startTime, $endTime);
    }

    /**
     * Gets time difference in business hour
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return int
     */
    public function getTimeDifferenceInBH(Carbon $startTime, Carbon $endTime) : int
    {
        return (new BusinessHourCalculation($this->businessHour))->getTimeDiffInBH($startTime, $endTime);
    }

    /**
     * Decides if SLA related calculations should happen or not
     * @return bool
     */
    private function shallEnforceSla()
    {
        // if a ticket is older than 1 year
        if($this->oldTicket->created_at instanceof Carbon){
            return $this->oldTicket->created_at->gte(Carbon::now()->subYear());
        }
        return true;
    }
}