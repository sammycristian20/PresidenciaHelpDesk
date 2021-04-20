<?php
namespace App\Http\Controllers\Agent\helpdesk\TicketsView;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ticket\Tickets;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\Plugin;
use App\Model\helpdesk\Settings\CommonSettings;
use Lang;
use DB;
use Config;
use Cache;
use Auth;
use App\Model\helpdesk\Workflow\ApprovalWorkflowTicket;
use App\Model\helpdesk\Manage\UserType;
use Event;
use App\User;

/**
 * Handles all the actions or action-related data for a user, while handling a ticket
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketsActionOptionsController extends Controller
{

    /**
     * Request from the API call will be assigned to this property
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware(['auth', 'role.agent']);
    }

    /**
     * gets subject of mergable tickets
     * @return array    array of mergable tickets subject or error response
     */
    public function getSubjectOfTicketsToBeMerged()
    {
        //if recieves a single ticket-id
        $ticketId = $this->request->input('ticket-id');

        //if recieves array
        $ticketIds = $this->request->input('ticket-ids');
        if ($ticketId) {
            return $this->getMergeableTicketsByTicketId($ticketId);
        }

        if ($ticketIds) {
            return $this->getMergeableTicketsByTicketIds($ticketIds);
        }

        return errorResponse(Lang::get('lang.fails'));
    }

    /**
     * finds mergable tickets based on the given ticket Id.
     * @param integer $ticketId     ticket id
     * @return Response             success response with tickets data if success else error response
     */
    private function getMergeableTicketsByTicketId($ticketId)
    {
        //first get userId of ticket
        $ticket = Tickets::where('id', $ticketId)->select('user_id', 'status')->first();
        $userId = $ticket->user_id;
        $statusId = $ticket->statuses()->first()->type()->first()->name;

        $tickets = Tickets::with(['firstThread' => function($q) {
                        $q->select('ticket_id', 'title');
                    }])->where('user_id', $userId)
                ->whereIn('status', getStatusArray($statusId))->select('id')->get();

        $ticketsCount = $tickets->count();
        if ($ticketsCount == 1) {
            return errorResponse(Lang::get('lang.no_mergeable_tickets_found'));
        }

        $formattedTickets = [];
        foreach ($tickets as $ticket) {
            array_push($formattedTickets, $this->formatTicketNoAndFirstThread($ticket->id));
        }

        return successResponse('', $formattedTickets);
    }

    /**
     * Checks if the given ticket Ids are mergable or not. If they are, it gives back the data related to those tickets
     *
     * @param array $ticketIds      array of ticketIds which are supposed to be merged.
     * @return Response             success response with tickets data if success else error response
     */
    private function getMergeableTicketsByTicketIds($ticketIds)
    {
        $ticketsCountInRequest = count($ticketIds);
        $firstTicketId = $ticketIds[0]; //getting first ticketId
        $firstTicket = Tickets::where('id', $firstTicketId)->select('user_id', 'status')->first();
        $userId = $firstTicket->user_id;
        $statusId = $firstTicket->statuses()->first()->type()->first()->name;
        $tickets = Tickets::with(['firstThread' => function($q) {
                        $q->select('ticket_id', 'title');
                    }])->whereIn('id', $ticketIds)
                ->whereIn('status', getStatusArray($statusId))
                ->get();

        $ticketsCountInDB = $tickets->count();
        $ticketsCount = $tickets->count();
        if ($ticketsCount == 1) {
            return errorResponse(Lang::get('lang.no_mergeable_tickets_found'));
        }
        $formattedTickets = [];
        foreach ($tickets as $ticket) {
            array_push($formattedTickets, $this->formatTicketNoAndFirstThread($ticket->id));
        }
        return successResponse('', $formattedTickets);
    }
    /**
     * Format ticket response
     *
     * @param string $ticketId
     * @return Response
     */
    private function formatTicketNoAndFirstThread(string $ticketId){

      $tickets = Tickets::where('id',$ticketId)->get();

        foreach ($tickets as $ticket) {
          $response = "#".$ticket->ticket_number.'('.$ticket['firstThread']->title.')';
          $formattedTickets = (['ticket_id'=>$ticketId,'title'=>$response, 'name'=>$ticket['firstThread']->title]);
        }
      return $formattedTickets;
    }

    /**
     * Gets list of actions as allowed(true) or not-allowed(false) for logged in user. for eg. if the logged in user is allowed to change status
     * @param Request $request
     * @return Response array       success response with array of permissions
     */
    public function getActionList(Request $request)
    {
        $ticketId = $request->ticket_id;
        //check if calander plugin is activated
        $calender = Plugin::where('name', 'Calendar')->where('status', 1)->first();
        $hasCalanderActivated = $calender ? true : false;

        $ticket = Tickets::where('id', $ticketId)->select('id','assigned_to', 'team_id','dept_id','team_id', "status")->first();

        if(!$ticket){
          return errorResponse('invalid request');
        }

        $allowedActions = [
            'assign' => $this->isAssignable($ticket),
            'transfer' => User::has('transfer_ticket'),
            'edit' => User::has('edit_ticket'),
            'change_duedate' => $this->isDuedateChangeable($ticket),
            'has_calender' => $hasCalanderActivated,
            'time_track_enabled' => isTimeTrack(),
            'show_thread_worktime' => (bool)(new CommonSettings)->getStatus('time_track_option'),
            'allowed_enforce_approval_workflow'=> $this->isEnforceApprovalWorkflowAllowed($ticket->id),
            'allowed_approval_action'=> $this->isApprovalActionAllowed($ticket),
            'view_approval_progress'=> $this->isApprovalProgressVisible($ticket->id),
            'block_ticket_actions'=> $this->shallBlockAllTicketActions($ticket->id),
            // if any approval workflow is pending for approval and agent or admin has permission for apply approval workflow
            'remove_approval_workflow'=> $this->isRemovalApprovalWorkflowActionAllowed($ticket->id),
            'surrender' => $this->isSurrenderable($ticket->assigned_to),
        ];

        // any plugin can inject more actions if required using ticketId
        Event::dispatch('timeline-actions-visibility-dispatch', [&$allowedActions, $ticketId]);

        return successResponse('', ['actions' => $allowedActions]);
    }

    /**
     * Checks if duedate is changeable
     * @param Tickets $ticket
     * @return bool
     */
    private function isDuedateChangeable($ticket)
    {
        // if change-duedate permission is there and ticket is not in halted status
        return User::has('change_duedate') && !$ticket->statuses->halt_sla;
    }

    /**
     * If ticket is is surrenderable, i.e ticket is assigned to the logged in agent
     * @param int $assigneeId person to whom ticket is assigned
     * @return boolean
     */
    private function isSurrenderable($assigneeId) : bool
    {
      return Auth::user()->id == $assigneeId;
    }

    /**
     * If ticket can be assigned.
     * @param  int $assigneeId
     * @return bool
     */
    private function isAssignable(Tickets $ticket) : bool
    {
      /*
       If ticket is not assigned to anyone, only assign permission will be checked
       If ticket is already assigned, reassign permission will be checked
      */
      if(!$ticket->assigned_to && !$ticket->team_id){
        return User::has('assign_ticket');
      }
      return User::has('re_assigning_tickets');
    }

    /**
     * If all ticket actions needed to be blocked
     * @param  int $ticketId
     * @return bool
     */
    private function shallBlockAllTicketActions($ticketId) : bool
    {
      return ApprovalWorkflowTicket::where('ticket_id', $ticketId)->where('status','PENDING')->count();
    }

    /**
     * If enforcing approval workflow is allowed
     * NOTE: this method is seperate because there are some future enhancements(permissions) that has to be
     * built in PHASE-2
     * @param  int $ticketId
     * @return boolean
     */
    private function isEnforceApprovalWorkflowAllowed($ticketId)
    {
      $isApprovalWorkflowAllowed = ApprovalWorkflowTicket::where('ticket_id', $ticketId)
          ->where('status','PENDING')->count();

      return (!$isApprovalWorkflowAllowed && User::has('apply_approval_workflow'));
    }

    /**
     * If approving/denying a ticket is allowed
     * LOGIC:
     * - first check, approval workflow is enforced on the ticket
     * - if not, return false
     * - if yes, check if any approval level is active
     * - if not, return false
     * - if yes, first check if current user is added as user approver
     * - if not, check if current user is added as department manager approver
     * - if not, check if added as team lead
     * - if not, return false
     * @param Tickets $ticket
     * @return boolean
     */
    private function isApprovalActionAllowed(Tickets $ticket)
    {
        //will be only allowed if there is a workflow for the ticket and user role `admin`
        // if the user is there in the list of action takers in current level, (check team lead and department manager also)
        $userId = Auth::user()->id;
        $ticketId = $ticket->id;

        $approvalWorflowForTicket = ApprovalWorkflowTicket::where('ticket_id', $ticketId)
          ->where('status','PENDING')->first();

        if(!$approvalWorflowForTicket){
            return false;
        }

        $activelevelForTicket = $approvalWorflowForTicket->approvalLevels()->where('is_active', 1)->first();
        if(!$activelevelForTicket){
            return false;
        }

        $approverStatuses = $activelevelForTicket->approverStatuses()->where('status','PENDING')->get();

        if($approverStatuses->where('approver_id',$userId)->where('approver_type','App\User')->count()){
            return true;
        }

        if(isDepartmentManager($ticket->dept_id, $userId) && $this->ifUserTypeIsApprover("department_manager", $approverStatuses)){
            return true;
        }

        if(isTeamLead($ticket->team_id, $userId) && $this->ifUserTypeIsApprover("team_lead", $approverStatuses)){
            return true;
        }

        return false;
    }

    /**
     * If passed user type id is approver is given approver status collection
     * @param string $userType
     * @param Collection $approverStatuses
     * @return bool
     */
    private function ifUserTypeIsApprover(string $userType, Collection $approverStatuses):bool
    {
        // either team_lead or department manager
        $userTypeId = UserType::where('key', $userType)->first()->id;
        return $approverStatuses->where('approver_id',$userTypeId)
            ->where('approver_type','App\Model\helpdesk\Manage\UserType')
            ->count();
    }

    /**
     * If approval prgress should be visible
     * NOTE: this method is seperate because there are some future enhancements(permissions) that has to be
     * built in PHASE-2
     * @param  int  $ticketId
     * @return boolean
     */
    private function isApprovalProgressVisible($ticketId)
    {
      //will be only allowed if there is a workflow for the ticket
      return (bool)ApprovalWorkflowTicket::where('ticket_id', $ticketId)->count();
    }

    /**
     * Delete all selected tickets and related data from database forever
     * @param Request $request  request object
     * @return Response         success response with message if success else error response
     */
    public function deleteTicketsAndRelatedDataFromDB(Request $request)
    {
        if ($request->filled('ticket-ids')) {
            if (!is_array($request->get('ticket-ids')) || count($request->get('ticket-ids')) == 0) {
                return errorResponse('incorrect input format');
            }
            $tickets = Tickets::whereIn('id', $request->get('ticket-ids'));
            if ($tickets->count() == 0) {
                return errorResponse(Lang::get('lang.not_found'));
            }
            $tickets->each(function($ticket) {
                $ticket->delete(); //Elequent event is handling deletion of related models
            });
            return successResponse(Lang::get('lang.hard-delete-success-message'));
        }
        return errorResponse(Lang::get('lang.select-ticket'));
    }

    /**
     * gets ticket related setting data
     * @return Response       contains ticket settings data
     */
    public function ticketSettings()
    {
        $headerClass = DB::table('system_portal')->select('agent_header_color')->first()->agent_header_color;

        //tickets table bar color
        $tableBarColor = Config::get("theme.header-color.$headerClass");

        //tickets per page
        $ticketsPerPage = (Cache::has('ticket_per_page')) ? Cache::get('ticket_per_page') : 10;

        return successResponse('', ['table_bar_color' => $tableBarColor, 'ticket_per_page' => $ticketsPerPage]);
    }

    /**
     * if approval workflow remove is allowed
     * @param  int $ticketId
     * @return boolean
     */
    private function isRemovalApprovalWorkflowActionAllowed($ticketId)
    {
        $isApprovalWorkflowRemoveActionAllowed = ApprovalWorkflowTicket::where('ticket_id', $ticketId)
          ->where('status','PENDING')->count();

        return ($isApprovalWorkflowRemoveActionAllowed && User::has('apply_approval_workflow'));
    }
}
