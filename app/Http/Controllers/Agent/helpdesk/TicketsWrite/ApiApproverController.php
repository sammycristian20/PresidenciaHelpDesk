<?php

namespace App\Http\Controllers\Agent\helpdesk\TicketsWrite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\Model\helpdesk\Workflow\ApprovalLevel;
use App\Model\helpdesk\Manage\UserType;
use Lang;
use Auth;
use App\User;
use App\Model\helpdesk\Ticket\Tickets;
use Illuminate\Database\Eloquent\Collection;
use App\Model\helpdesk\Workflow\ApprovalWorkflowTicket;
use App\Model\helpdesk\Workflow\ApproverStatus;
use App\Http\Controllers\Agent\helpdesk\TicketsWrite\BaseApproverController;
use App\Http\Controllers\Client\helpdesk\ClientTicketController;
use App\Model\helpdesk\Workflow\ApprovalLevelStatus;
use Exception;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Agent_panel\User_org as UserOrganization;
use App\Http\Controllers\Agent\helpdesk\TicketController;

/**
 * Contains logic to enforce approval workflow
 */
class ApiApproverController extends BaseApproverController
{
  private $errors;

  public function __construct()
  {
      $this->middleware(['auth','role.agent'])->except(
        ['getConversationByHash','approvalActionByHash']
      );
  }

  /**
   * Applies workflow to given ticket by creating a copy of the entire approval workflow, so that in case
   * when approval_workflows table gets updated, it doesn't effect the enforced tickets
   * @param  Request $request  with parameters `ticket_id` and `workflow_id`
   * @return Response
   */
  public function applyApprovalWorkflow(Request $request, string $actionPerformer = null)
  {
      $ticketId = $request->ticket_id;
      $workflowId = $request->workflow_id;
      // get data from approval_workflow tables
      // update approval_workflow_tickets
      $approvalWorkflow = ApprovalWorkflow::find($workflowId);
      $this->ticket = Tickets::find($ticketId);
      $approvalWorkflowTicket = ApprovalWorkflowTicket::create([
        'approval_workflow_id' => $approvalWorkflow->id,
        'ticket_id'=>$ticketId,
        'status'=>'PENDING',
        'name'=> $approvalWorkflow->name,
        'user_id'=> $approvalWorkflow->user_id,
        'action_on_approve'=> $approvalWorkflow->action_on_approve,
        'action_on_deny'=>$approvalWorkflow->action_on_deny,
        'ticket_status_id' => $this->ticket->status,
      ]);

      // get approval_level data
      // update approval_level_statuses
      $approvalLevels = $approvalWorkflow->approvalLevels()->orderBy('order','asc')->get();

      //creating entries in approval_level_statuses in pending state
      $approvalLevelStatuses = $this->createPendingApprovalLevels($approvalLevels ,$approvalWorkflowTicket);

      //get approvers for first level only
      $approvers = $this->getApproversList($this->ticket, $approvalLevelStatuses->first());

      foreach ($approvers as $approver) {
        // hash proprty has been additionally added, that's why sending it as an additional  parameter
        // for consistency reasons
        $this->sendMailToApprover($approver, $this->ticket, $approver->hash);
      }
      //assign current logged in user
      $actionPerformer = ($actionPerformer)? : Auth::user()->full_name;
      //create thread that this workflow has been applied and change status to unapproved
      $this->createActivity('workflow', $approvalWorkflowTicket,'ENFORCED', '', $actionPerformer);

      //saving thread
      $this->activity->save();

      $this->markTicketAsUnapproved($this->ticket);

      return successResponse(Lang::get('lang.updated_successfully'));
  }


  /**
   * Removes a workflow applied on ticket based on ticketId
   * @param  $approvalWorkFlowId
   * @return Response
   */
  public function removeApprovalWorkflow($ticketId)
  {
    $workFlow = ApprovalWorkflowTicket::where('ticket_id', $ticketId)->orderBy("id", "desc")->first();
    if (is_null($workFlow)) {
      return errorResponse(Lang::get('lang.no_proper_workflow'));
    }
    $this->ticket = Tickets::find($ticketId);
    //create thread that this workflow has been applied and change status to unapproved
    $this->createActivity('workflow', $workFlow,'REMOVED', '',  Auth::user()->full_name); //currenlty workflow can be removed only by Authenticated users so passing Auth user as $actionPerfomer
    // shifting to previous status
    $this->changeStatus($workFlow->ticket_status_id);
    //saving thread
    $this->activity->save();

    $workFlow->delete();

    return successResponse(Lang::get('lang.workflow_removed_successfully'));
  }

  /**
   * Approves or denies a ticket approval
   * @param  string $hash
   * @param Request
   * @return Response
   */
  public function approvalActionByHash($hash, Request $request)
  {
    $actionType = $request->action_type;
    $comment = $request->comment ? $request->comment : '';

    $hashString = $this->decryptHash($hash);

    $this->user = User::select('id','first_name','last_name','email','user_name')
        ->where('email', $hashString->email)->where('email','!=', null)->first();

    if(!$this->user) {
      return errorResponse(Lang::get('invalid_user'));
    }

    $hash = $hashString->hash;

    // either should return that this hash been used already (if has status as APPROVED)
    $approver = ApproverStatus::where('hash', $hash)->first();

    if(!$approver){
      return errorResponse(Lang::get('lang.invalid_hash'), 404);
    }

    // //when hash is found but expired
    if($approver->status != 'PENDING'){
      return errorResponse(Lang::get('lang.hash_expired'), 404);
    }

    $action = ($actionType == 'approve') ? 'APPROVED' : 'DENIED';

    //get ticket Id from approver
    $ticketId = $approver->approvalLevelStatus()->first()->approvalWorkflow()->first()->ticket_id;
    $this->ticket = Tickets::find($ticketId);

    $this->handleTicketApprovalAction($action, $ticketId, $comment);

    return successResponse(Lang::get('lang.updated_successfully'));
  }

  /**
   * Approves or denies a ticket approval
   * @param  string $hash
   * @param Request
   * @return Response
   */
  public function approvalActionByTicketId($ticketId, Request $request)
  {
    $action = ($request->action_type == 'approve') ? 'APPROVED' : 'DENIED';
    $comment = $request->comment ? $request->comment : '';

    $this->ticket = Tickets::find($ticketId);

    if(!$this->ticket){
        return errorResponse(Lang::get('lang.ticket_not_found'));
    }

    try {
      $this->user = Auth::user();
      $this->handleTicketApprovalAction($action, $ticketId, $comment);
      return successResponse(Lang::get('lang.updated_successfully'));
    }
    catch(Exception $e){
      // log the exception
      loging('approval-workflow', $e->getMessage(), 'error');
      return errorResponse(Lang::get('lang.some_error_occured'));
    }
  }

  /**
   * Handles approval/denial of the ticket
   * @param  string $actionType `APPROVE` OR `DENY`
   * @param  int $ticketId
   * @return Boolean
   */
  private function handleTicketApprovalAction(string $action, $ticketId, string $comment)
  {
    $this->ticket = Tickets::find($ticketId);

    // user can be taken from $this->user
    //active workflow
    $workflow = ApprovalWorkflowTicket::where('ticket_id', $this->ticket->id)
            ->where('status','PENDING')->first();

    //active level
    $activeLevel = $workflow->approvalLevels()->where('status','PENDING')
        ->where('is_active', 1)->first();

    $userId = $this->user->id;

    // first check if currently logged in user is department manager if the ticket
    // if yes, send that as hash
    // if no, check if ticket is assigned to a team, if yes, then check if user is
    // team lead of the ticket, if yes, send team lead hash,
    // if no, send the hash corresponding to the user
    if(isDepartmentManager($this->ticket->dept_id, $userId)){
        $userTypeId = UserType::where('key','department_manager')->first()->id;
        $this->handleApproverUpdate($activeLevel, $action, $userTypeId, 'App\Model\helpdesk\Manage\UserType', $comment);
    }

    if(isTeamLead($this->ticket->team_id, $userId)){
      $userTypeId = UserType::where('key','team_lead')->first()->id;
      $this->handleApproverUpdate($activeLevel, $action, $userTypeId, 'App\Model\helpdesk\Manage\UserType', $comment);
    }

    $organizationIds = $this->ticket->ticketOrganizations()->pluck('org_id')->toArray();
    $organizationDepartmentIds = $this->ticket->ticketOrganizations()->pluck('org_department')->toArray();

    if($this->isOrganizationManager($organizationIds, $userId)){
      $userTypeId = UserType::where('key','organization_manager')->first()->id;
      $this->handleApproverUpdate($activeLevel, $action, $userTypeId, 'App\Model\helpdesk\Manage\UserType', $comment);
    }

    if(isMicroOrg() && $this->isOrganizationDepartmentManager($organizationDepartmentIds, $userId)){
      $userTypeId = UserType::where('key','organization_department_manager')->first()->id;
      $this->handleApproverUpdate($activeLevel, $action, $userTypeId, 'App\Model\helpdesk\Manage\UserType', $comment);
    }

    $this->handleApproverUpdate($activeLevel, $action, $userId, 'App\User', $comment);
    $this->handleLevelUpdate($activeLevel);
    $this->handleWorlflowUpdate($workflow);
    $this->activity->save();
  }

  /** method to check organization manager count
   * @param Tickets $ticket
   * @param $organizationIds
   * @return boolean
   */
  private function isOrganizationManager($organizationIds, $userId)
  {
    $organizationManagerCount = UserOrganization::whereIn('org_id', $organizationIds)->where([['role', 'manager'], ['user_id', $userId]])->count();
  
    return $organizationManagerCount;
  }

  /** method to check organization department manager count
   * @param Tickets $ticket
   * @param $organizationDepartmentIds
   * @return boolean
   */
  private function isOrganizationDepartmentManager($organizationDepartmentIds, $userId)
  {
    $organizationDepartmentManagerCount = OrganizationDepartment::whereIn('id', $organizationDepartmentIds)->where('org_dept_manager', $userId)->count();
     
    return $organizationDepartmentManagerCount;
  }




  /**
   * gets conversation by hash
   * @param  string  $hash
   * @param  Request $request
   * @return null
   */
  public function getConversationByHash($hash, Request $request)
  {
    $hashString = $this->decryptHash($hash);
    $hash = $hashString->hash;

    $approver = ApproverStatus::where('hash', $hash)->first();

    if(!$approver){
      return errorResponse(Lang::get('lang.invalid_hash'), 404);
    }

    $isApproverPending = (bool)($approver->status == 'PENDING');
    $isLevelPending = (bool)($approver->approvalLevelStatus()->first()->status == 'PENDING');

    // //when hash is found but expired
    if(!$isLevelPending || ($isLevelPending && !$isApproverPending)){
      return errorResponse(Lang::get('lang.hash_expired'), 404);
    }

    $currentPage = $request->input('page') ? $request->input('page') : 1;

    //get to ApprovalWorkflowStatus table to get ticketId
    $ticketId = $approver->approvalLevelStatus()->first()->approvalWorkflow()->first()->ticket_id;
    // get information in ticket
    $ticket = (new ClientTicketController)->getTicketById($ticketId, $currentPage);

    return successResponse('', ['ticket'=>$ticket]);
  }

  /**
   * Gets current approval state of the ticket
   * @param  int $ticketId
   * @return null
   */
  public function getTicketAppovalStatus($ticketId)
  {
    //if ticket id is not found in ApprovalStatus, it should send errorResponse
    $approvalWorkflowTickets = ApprovalWorkflowTicket::with([
      'approvalLevels'=> function($q){
        return $q->select('id','status','name','approval_level_id','approval_workflow_ticket_id','is_active')
          ->with(['approveUsers','approveUserTypes'])->orderBy("id", "asc");
      }
    ])->where('ticket_id',$ticketId)->orderBy('id','asc')->get()->toArray();

    if(!$approvalWorkflowTickets){
      return errorResponse(Lang::get('lang.no_approval_workflow_applied'));
    }

    $this->formatTicketApprovalData($approvalWorkflowTickets);
    return successResponse('',$approvalWorkflowTickets);
  }

  /**
   * Formats approval workflow data in the required format
   * @param  Collection $approvalWorkflowData
   * @return null
   */
  private function formatTicketApprovalData(&$approvalWorkflowData)
  {
      foreach ($approvalWorkflowData as &$workflow) {

        foreach ($workflow['approval_levels'] as &$level) {

          foreach ($level['approve_users'] as &$user) {
            $user['status'] = $user['pivot']['status'];
            $user['name'] = !$user['first_name'] ? $user['user_name'] : $user['first_name'].' '.$user['last_name'];
            unset($user['pivot'], $user['first_name'], $user['user_name'], $user['last_name'], $user['email']);
          }

          foreach ($level['approve_user_types'] as &$userTypes) {
            $userTypes['status'] = $userTypes['pivot']['status'];
            unset($userTypes['pivot']);
          }
        }
     }
  }
}
