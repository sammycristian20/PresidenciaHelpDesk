<?php


namespace App\Http\Controllers\SLA;

use App\Http\Controllers\Common\NotificationController;
use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Manage\Sla\SlaApproachEscalate;
use App\Model\helpdesk\Manage\Sla\SlaViolatedEscalate;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\TicketSla;
use App\Traits\FaveoDateParser;
use App\User;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Lang;

/**
 * handle sending of reminder approaching or escalated to concerned people
 * NOTE: need a mark
 *
 * REMINDERS FOR APPROACHING RESPONSE TICKETS
 *  - Getting non-responded non-overdue ticket (getPendingResolutionSlaTickets)
 *  - Getting their SLA and then the list of reminders
 *  - Getting the members and sending them mails
 */
class Reminders
{
    use FaveoDateParser;

    /***
     * @param Tickets $ticket
     * @throws Exception
     */
    public function createSlaReminders(Tickets $ticket)
    {
        // removing old reminders so that it doesn't conflict with new ones
        $this->reminderCleanup($ticket);

        if (!$ticket->duedate) {
            // in case of halting, no reminder has to be created
            return;
        }
        // remove old reminders if any
        // create new by checking SLA
        // ['sla_id', 'reminder_id', 'trigger_at']
        $type = $ticket->firstResponseIsDone() ? 'resolution' : 'response';

        $this->createReminders($type, $ticket->sla, $ticket->duedate, $ticket->id);
    }

    /**
     * creates reminder ids by sla id
     * @param string $type possible values 'response' and 'resolution'
     * @param int $slaId
     * @param Carbon $duedate
     * @param int $ticketId
     * @throws Exception
     */
    private function createReminders(string $type, int $slaId, Carbon $duedate, int $ticketId)
    {
        if (!in_array($type, ['response', 'resolution'])) {
            throw new InvalidArgumentException("Invalid type passed. Allowed types are resolution and response");
        }

        $this->createApproachingReminders($type, $slaId, $duedate, $ticketId);

        $this->createViolatedReminders($type, $slaId, $duedate, $ticketId);
    }

    /**
     * Creates approaching reminders by sla id
     * @param string $type possible values 'response' and 'resolution'
     * @param int $slaId
     * @param Carbon $duedate
     * @param int $ticketId
     * @throws Exception
     */
    private function createApproachingReminders(string $type, int $slaId, Carbon $duedate, int $ticketId)
    {
        // creating approaching reminders
        $approachingReminders = SlaApproachEscalate::where('sla_plan', $slaId)
            ->where('escalate_type', $type)
            ->select('id', 'escalate_time')
            ->get();

        foreach ($approachingReminders as $reminder) {
            // trying to format "diff::~minute" , which does not have any number
            // ignoring the string without number
            if((int) filter_var($reminder->escalate_time, FILTER_SANITIZE_NUMBER_INT)) {
                $triggerTime = $this->getReminderTriggerTime($reminder, $duedate);

                // not creating reminder which is in past
                if($this->shallCreateReminder($triggerTime)){
                    DB::table('ticket_sla_approach_escalate')->insert(['ticket_id'=> $ticketId,
                        'sla_approach_escalate_id'=> $reminder->id, 'trigger_at'=> $triggerTime]);
                }
            }
        }
    }

    /**
     * Creates approaching reminders by sla id
     * @param string $type possible values 'response' and 'resolution'
     * @param int $slaId
     * @param Carbon $duedate
     * @param int $ticketId
     * @throws Exception
     */
    private function createViolatedReminders(string $type, int $slaId, Carbon $duedate, int $ticketId)
    {

        // creating violated reminders
        $violatingReminders = SlaViolatedEscalate::where('sla_plan', $slaId)
            ->where('escalate_type', $type)
            ->select('id', 'escalate_time')
            ->get();

        foreach ($violatingReminders as $reminder) {
            $triggerTime = $this->getReminderTriggerTime($reminder, $duedate);
            // not creating reminder which is in past
            if($this->shallCreateReminder($triggerTime)){
                DB::table('ticket_sla_violated_escalate')->insert(['ticket_id'=> $ticketId,
                    'sla_violated_escalate_id'=> $reminder->id, 'trigger_at'=> $triggerTime]);
            }
        }
    }

    /**
     * Decides whether to create reminder or not
     * @param Carbon $triggerTime
     * @return bool
     */
    private function shallCreateReminder(Carbon $triggerTime)
    {
        // creating reminders only for future time
        $currentTime = Carbon::now();
        return $triggerTime->gte($currentTime);
    }

    /**
     * Cleans up all reminder linked with the given ticketId
     * @param Tickets $ticket
     * @return void
     */
    private function reminderCleanup(Tickets $ticket)
    {
        $ticket->approachingReminders()->detach();

        $ticket->violatedReminders()->detach();
    }

    /**
     * Gets trigger time
     * @param $reminder
     * @param Carbon $duedate
     * @return Carbon
     * @throws \Exception
     */
    private function getReminderTriggerTime($reminder, Carbon $duedate) : Carbon
    {
        $timeDiffInMinutes = $this->getTimeDiffInMinutes($reminder->escalate_time);
        // if instance of escalate
        if ($reminder instanceof SlaApproachEscalate) {
            $triggerTime = $duedate->copy()->subMinute($timeDiffInMinutes);
        } else {
            $triggerTime = $duedate->copy()->addMinute($timeDiffInMinutes);
        }

        return $triggerTime;
    }



    /**
     * Sends ticket reminders
     */
    public function sendReminders()
    {
        $this->sendApproachingReminders();
        $this->sendViolatedReminders();
    }

    /**
     * Sends approaching reminders to required people
     */
    private function sendApproachingReminders()
    {
        $currentTime = Carbon::now();

        $approachingReminders = DB::table('ticket_sla_approach_escalate')
            ->where('trigger_at', '<', $currentTime)
            ->get();

        foreach ($approachingReminders as $approachingReminder) {
            $ticket = Tickets::find($approachingReminder->ticket_id);

            // should send approaching reminder only when due date is greater than current time. Else it is already approached and
            // there's no point in sending
            if($ticket->duedate->gt(Carbon::now())){
                $reminder = SlaApproachEscalate::find($approachingReminder->sla_approach_escalate_id);
                $template = $this->getTemplateScenario($reminder->escalate_type, 'approaching');
                $this->sendNotificationToReceiversByReminder($ticket, $reminder, $template);
            }

            $ticket->approachingReminders()->detach($approachingReminder->sla_approach_escalate_id);
        }
    }

    /**
     * Sends violated reminders to required people
     */
    private function sendViolatedReminders()
    {
        // sending violated
        $currentTime = Carbon::now();

        $violatedReminders = DB::table('ticket_sla_violated_escalate')
            ->where('trigger_at', '<', $currentTime)
            ->get();

        foreach ($violatedReminders as $violatedReminder) {
            $ticket = Tickets::find($violatedReminder->ticket_id);
            $reminder = SlaViolatedEscalate::find($violatedReminder->sla_violated_escalate_id);
            $template = $this->getTemplateScenario($reminder->escalate_type, 'violated');
            $this->sendNotificationToReceiversByReminder($ticket, $reminder, $template);
            $ticket->violatedReminders()->detach($violatedReminder->sla_violated_escalate_id);
        }
    }


    /**
     * Sends mail to receivers by checking reminder instance
     * @param Tickets $ticket
     * @param SlaApproachEscalate|SlaViolatedEscalate $reminder
     * @param string $template
     */
    private function sendNotificationToReceiversByReminder(Tickets $ticket, $reminder, string $template)
    {

        // if assigned_to is same as reminder person, it will
        $reminderReceivers = $reminder->escalate_person;

        $userIds = [];

        foreach ($reminderReceivers as $reminderReceiver) {

            if ($reminderReceiver == 'assignee' && $ticket->assigned_to) {
                $userIds[] = $ticket->assigned_to;
            }

            if ($reminderReceiver == 'department_manager') {
                $userIds = array_merge($userIds, array_filter(DepartmentAssignManager::where('department_id', $ticket->dept_id)->pluck('manager_id')->toArray()));
            }

            if ($reminderReceiver == 'team_lead') {
                $userIds = array_merge($userIds, array_filter(Teams::where('id', $ticket->team_id)->pluck('team_lead')->toArray()));
            }

            if ($reminderReceiver == 'admin') {
                $userIds = array_merge($userIds, User::where('role', 'admin')->pluck('id')->toArray());
            }

            // if reminder receiver is an integer, we look for that user in db and append the id if found
            if (User::where('id', $reminderReceiver)->count()) {
                $userIds[] = $reminderReceiver;
            }
        }

        $userIds = array_unique($userIds);
        $this->sendMailToReceivers($ticket, $userIds, $template);
        $this->sendNotificationToReceivers($ticket, $userIds, $template);
    }

    /**
     * Gets template scenario based on reminder and rem
     * @param SlaApproachEscalate|SlaViolatedEscalate $reminderCategory reminder can be either resolution or response
     * @param string $reminderType reminder type can be escalated or violated
     * @return string
     */
    private function getTemplateScenario($reminderCategory, $reminderType) : string
    {
        if ($reminderCategory == "response") {
            if ($reminderType == "approaching") {
                return "response_due_approach";
            }
            return "response_due_violate";
        }

        if ($reminderType == "approaching") {
            return "resolve_due_approach";
        }
        return "resolve_due_violate";
    }

    /**
     * Sends mails to receivers
     * @param Tickets $ticket
     * @param array $userIds
     * @param string $scenario
     */
    private function sendMailToReceivers(Tickets $ticket, array $userIds, string $scenario)
    {
        // check if sending mail is configured as true
        if (!in_array($scenario, ['response_due_approach','response_due_violate','resolve_due_approach','resolve_due_violate'])) {
            throw new InvalidArgumentException("invalid template scenario given");
        }

        $slaMetaData = TicketSla::getSlaMetaDataByTicket($ticket);

        // create email notification only if it is enabled
        if($slaMetaData->send_email_notification) {

            foreach ($userIds as $userId) {
                $agent = User::whereId($userId)->select('id', 'first_name', 'last_name', 'email', 'user_name', 'user_language')->first();

                // if user doesn't have an email, it won't get processed further
                if (!$agent->email) {
                    continue;
                }

                // send a single mail based on template
                $templateVariables = $ticket->ticketTemplateVariables();

                $phpMailObject = new PhpMailController();

                $from = $phpMailObject->mailfrom('1', $ticket->dept_id);

                $to = ['email' => $agent->email, 'name' => $agent->full_name, 'preferred_language' => $agent->user_language, 'role' => $agent->role];

                $message = ['scenario' => $scenario];
                // send email
                $phpMailObject->sendmail($from, $to, $message, $templateVariables);
            }
        }
    }

    /**
     * Creates notification for users for response approach and violate
     * @param Tickets $ticket
     * @param array $userIds
     * @param string $scenario
     */
    private function sendNotificationToReceivers(Tickets $ticket, array $userIds, string $scenario)
    {
        $slaMetaData = TicketSla::getSlaMetaDataByTicket($ticket);

        // create in-app notification only if it is enabled
        if($slaMetaData->send_app_notification){

            foreach ($userIds as $userId) {
                (new NotificationController)->createNotification([
                    'message' => Lang::get("lang.{$scenario}_notification",
                        ["ticketId"=>$ticket->ticket_number, "duedate"=>faveodate($ticket->duedate)]),
                    'to'      => $userId,
                    'by'      => null, //for system
                    'table'   => "tickets",
                    'row_id'  => $ticket->id,
                    'url'     => url("/thread/$ticket->id")
                ]);
            }
        }
    }
}
