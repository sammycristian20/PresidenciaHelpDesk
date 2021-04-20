<?php

namespace App\Http\Controllers\Agent\helpdesk\TicketsWrite;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Workflow\ApprovalLevelStatus;
use App\Model\helpdesk\Workflow\ApprovalWorkflowTicket;
use DB;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Workflow\ApprovalLevel;
use App\Model\helpdesk\Agent\Teams as Team;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Agent\Department;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\Common\PhpMailController;
use App\User;
use App\Traits\ApprovalActivityHandler;
use App\Model\helpdesk\Ticket\TicketStatusType as StatusType;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;
use Config;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Exception;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Model\helpdesk\Ticket\Ticket_Status as Status;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;

/**
 * Contains all helper methods that is required by approver module
 */
class BaseApproverController extends Controller
{
    use ApprovalActivityHandler;

    /**
     * Ticket on which workflow has been applied
     * @var Tickets
     */
    protected $ticket;

    /**
     * Thread when any workflow action happens
     * @var Thread
     */
    protected $activity;

    /**
     * currently logged in user or the user who is performing the action(like approve/deny)
     * @var User
     */
    protected $user;

    /**
     * Creates required levels in approval_level_statuses based on parent workflow by copying levels from
     * approval_levels table and marking each of level status as PENDING (which will later be updated as approved)
     * @param  ApprovalWorkflowTicket $parentWorkflow   parent workflow for which levels are required to be created
     * @return array
     */
    protected function createPendingApprovalLevels($approvalLevels, ApprovalWorkflowTicket $parentApprovalWorkflow)
    {
        $approvalLevelStatuses = collect();
        foreach ($approvalLevels as $key => $level) {

          //by default for the first level we mark level as active
            $isActive = $key == 0 ? 1 : 0;
            $approvalLevelStatuses->push($parentApprovalWorkflow->approvalLevels()->create(['approval_level_id' => $level->id, 'is_active'=>$isActive,
              'status'=>'PENDING', 'name'=>$level->name, 'order'=>$level->order, 'match'=> $level->match]));

            $approvers = DB::table('approval_level_approvers')->where('approval_level_id', $level->id)->get();
            $this->createPendingApproverStatuses($approvers, $approvalLevelStatuses[$key]);
        }
        return $approvalLevelStatuses;
    }

    /**
     * Creates required approvers in `approver_statuses` based on parent level by copying approvers from
     * `ticket_approvers` table and marking each of level status as PENDING (which will later be updated as approved)
     * @param  array              $approvers          array of objects from `approval_level_approvers` table
     * @param  ApprovalLevelStatus $parentApprovalLevelStatus parent level status under which users has be be created
     * @return null
     */
    private function createPendingApproverStatuses($approvers, ApprovalLevelStatus $parentApprovalLevelStatus)
    {
        foreach ($approvers as $approver) {
            // a random hash
            $hash = str_random(40);
            $parentApprovalLevelStatus->approverStatuses()->create(['approver_id' => $approver->approval_level_approver_id,
            'approver_type'=> $approver->approval_level_approver_type, 'hash'=>$hash, 'status'=>'PENDING']);
        }
    }

    /**
     * Gets list of approvers based on ticket and approval level along with hash
     * @param  Tickets       $ticket
     * @param  ApprovalLevel $approvalWorkflowLevel
     * @return Collection
     */
    protected function getApproversList(Tickets $ticket, ApprovalLevelStatus $approvalWorkflowLevel) : Collection
    {
        // get all approvers, get department id of the ticket and send mail to all department managers
        //query into ApprovalLevelStatues instead of ApprovalLevel, so that we can get hash also
        $approverUsers = $approvalWorkflowLevel->approveUsers()
            ->select('users.id','first_name','last_name','email','user_name')->get();

        //adding hash property to each approverUser so that it can be symmtric with users coming from department and team
        foreach ($approverUsers as $user) {
          $user->hash = $user->pivot->hash;
        }

        $approverTypes = $approvalWorkflowLevel->approveUserTypes()->get();

        // check $approverTypes and if it has department manager, get all the users who are department
        foreach ($approverTypes as $approverType) {

          //insert hash in each of them
          if($approverType->key == 'department_manager'){
            $approverUsers = $approverUsers->merge($this->getDepartmentManagers($ticket->dept_id, $approverType->pivot->hash));
          }

          if($approverType->key == 'team_lead' && $ticket->team_id){
            $approverUsers = $approverUsers->merge($this->getTeamLeads($ticket->team_id, $approverType->pivot->hash));
          }

          $organizationIds = $ticket->ticketOrganizations()->pluck('org_id')->toArray();
          $organizationDepartmentIds = $ticket->ticketOrganizations()->pluck('org_department')->toArray();

          if($approverType->key == 'organization_manager'){
            $approverUsers = $approverUsers->merge($this->getOrganizationManagers($organizationIds, $approverType->pivot->hash));
          }

          if($approverType->key == 'organization_department_manager' && isMicroOrg()){
            $approverUsers = $approverUsers->merge($this->getOrganizationDepartmentsManager($organizationDepartmentIds, $approverType->pivot->hash));
          }
        }

        return $approverUsers;
    }

    /**
     * gets department managers of the given department and appends passed hash to each manager
     * @param  int $departmentId
     * @return array
     */
    private function getDepartmentManagers($departmentId, $hash){
        $users = Department::select('id')->find($departmentId)->managers()
          ->select('users.id','first_name','last_name','email','user_name')->get();

        //adding hash property to each user
        foreach ($users as $user) {
          $user->hash = $hash;
        }
        return $users;
    }

    /**
     * Gets team lead of the given team and appends hash to each team lead
     * @param int $teamId
     * @return array
     */
    private function getTeamLeads($teamId, $hash){
        $users = Team::select('id','team_lead')->find($teamId)->lead()
          ->select('users.id','first_name','last_name','email','user_name')->get();

        //adding hash property to each user
        foreach ($users as $user) {
          $user->hash = $hash;
        }
        return $users;
    }

     /**
     * gets organization managers of the given organization and appends passed hash to each manager
     * @param  array $organizationId
     * @param $hash
     * @return array
     */
    private function getOrganizationManagers(array $organizationIds, $hash){
      $organizations = Organization::whereIn('id', $organizationIds)->get();

      $usersCollection = new Collection();
      foreach ($organizations as $organization) {
        $users = $organization->managers()->select('users.id', 'first_name', 'last_name', 'email', 'user_name')->get();
        foreach ($users as $user) {
          $user->hash = $hash;
        }
        $usersCollection = $usersCollection->merge($users);
      }

      return $usersCollection;
    }

    /**
     * gets organization department managers of the given organization departments and appends passed hash to each manager
     * @param  array $organizationDepartmentIds
     * @param $hash
     * @return array
     */
    private function getOrganizationDepartmentsManager(array $organizationDepartmentIds, $hash){
      $organizationDepartments = OrganizationDepartment::whereIn('id', $organizationDepartmentIds)->whereNotNull('org_dept_manager')->get();
      $usersCollection = new Collection();
      foreach ($organizationDepartments as $organizationDepartment) {
        $user = $organizationDepartment->manager()->select('users.id', 'first_name', 'last_name', 'email', 'user_name')->first();
        $user->hash = $hash;
        $usersCollection = $usersCollection->push($user);
      }

      return $usersCollection;
    }

    /**
     * Send mail to approver with approval link
     * @param User $approver
     * @param Tickets $ticket
     * @param string $hash     unique hash which will be used to recognize who has approved the ticket
     * @return null
     */
    protected function sendMailToApprover(User $approver, Tickets $ticket, string $hash)
    {
        //get template, pass required parameter and check if job is getting created for the required template
        $phpMailController = new PhpMailController;

        $from = $phpMailController->mailfrom('0', $ticket->dept_id);

        $to = ['name' => $approver->full_name, 'email' => $approver->email];

        $encodeHash = $this->encryptHashWithEmail($approver->email, $hash);

        //get it from database
        $approvalLink = Config::get('app.url')."/ticket-conversation"."/$encodeHash";

        $ticketLink = Config::get('app.url').'/thread'.'/'.$ticket->id;

        $message = ['message'=>'','scenario'=>'ticket-approval'];

        // fetching template variables
        $templateVariables = array_merge($ticket->ticketTemplateVariables(), ['receiver_name'=> $approver->full_name, 'approval_link'=> $approvalLink]);
        
        $phpMailController->sendmail($from, $to, $message, $templateVariables);
    }

    /**
     * Creates an associative array of email and hash and encryts that
     * @param  string $email
     * @param  string $hash
     * @return string
     */
    private function encryptHashWithEmail(string $email = null, string $hash) : string
    {
        $hashString = json_encode(['hash'=> $hash, 'email' => $email]);
        $encryptedHashString = Crypt::encrypt($hashString);
        return $encryptedHashString;
    }


    /**
     * Decrypts hash and gives an object in structure {email:'email',hash:'hash'}
     * @param  string $encryptedHash
     * @return object {email:'email',hash:'hash'}
     */
    protected function decryptHash($encryptedHash)
    {
      try{
        return json_decode(Crypt::decrypt($encryptedHash));
      }catch(DecryptException $e){
          throw new Exception($e);
      }
    }

    /**
     * Changes the status of ticket to unapproved by and saves it
     * @param  Tickets $ticket
     * @return null
     */
    protected function markTicketAsUnapproved(Tickets &$ticket)
    {
        $statusId = StatusType::where('name', 'unapproved')->first()->status()->first()->id;
        $this->changeStatus($statusId);
    }

    /**
     * Handles approver related operations when a ticket approval action is performed
     * @param string $action
     * @param int $approverId
     * @param string $approverType
     * @return Boolean    if approver is updated or not
     */
    protected function handleApproverUpdate(ApprovalLevelStatus $activeLevel, string $action, $approverId,
      string $approverType, string $comment = null)
    {
      $approver = $activeLevel->approverStatuses()->where('approver_id', $approverId)
          ->where('approver_type',$approverType)->first();

      if($approver){
        $approver->update(['status' => $action, 'comment' => $comment]);
        $this->createActivity('approver',$approver, $action, $comment);
        return true;
      }
      return false;
    }

    /**
     * Handles update of active level by checking its approvers status. Once level is completed,
     * it marks that as approved or denied
     * @param  ApprovalLevelStatus $activeLevelStatus
     * @return null
     */
    protected function handleLevelUpdate(ApprovalLevelStatus $activeLevelStatus)
    {
      // It checks if the current level is approved, it marks current level as inactive
      // and next level(by order) as active
      //query for the active one, check its approvers and update accordingly
      //get if it is any or all
      $approvedCount = $activeLevelStatus->approverStatuses()->where('approver_statuses.status','APPROVED')->count();
      $deniedCount = $activeLevelStatus->approverStatuses()->where('approver_statuses.status','DENIED')->count();
      $pendingCount = $activeLevelStatus->approverStatuses()->where('approver_statuses.status','PENDING')->count();
      if($activeLevelStatus->match == 'any'){
          if($approvedCount > 0){
            $this->approveLevel($activeLevelStatus);
          }

          if($deniedCount > 0 && $pendingCount + $approvedCount == 0){
            //level has been denied, so workflow will be denied too
            $this->denyLevel($activeLevelStatus);
          }
      }

      if($activeLevelStatus->match == 'all'){
          if($deniedCount > 0){
            $this->denyLevel($activeLevelStatus);
          }

          if($approvedCount > 0 && $pendingCount + $deniedCount == 0){
            $this->approveLevel($activeLevelStatus);
          }
      }
    }

    /**
     * marks active level as in_active and APPROVED and if next level exists, it marks that as active
     * @param ApprovalLevelStatus $activeLevelStatus  active level status
     * @return null
     */
    private function approveLevel(ApprovalLevelStatus $activeLevelStatus)
    {
      $activeLevelStatus->update(['is_active'=> 0,'status'=>'APPROVED']);

      $this->createActivity('level',$activeLevelStatus, 'APPROVED');

      $nextLevel = ApprovalLevelStatus::orderBy('order', 'asc')->where('order', '>', $activeLevelStatus->order)
          ->where('approval_workflow_ticket_id', $activeLevelStatus->approval_workflow_ticket_id)->first();

      if($nextLevel){
        $nextLevel->update(['is_active'=>1]);
        $this->createActivity('level',$nextLevel, 'ENFORCED');

        //send mail to next level people
        $approvers = $this->getApproversList($this->ticket, $nextLevel);

        foreach ($approvers as $approver) {
          $this->sendMailToApprover($approver, $this->ticket, $approver->hash);
        }
      }
    }

    /**
     * Marks a level as denied
     * @param  ApprovalLevelStatus $activeLevelStatus Active level
     * @return null
     */
    private function denyLevel(ApprovalLevelStatus $activeLevelStatus)
    {
      $activeLevelStatus->update(['is_active'=> 0,'status'=>'DENIED']);
      $this->createActivity('level',$activeLevelStatus, 'DENIED');
    }


    /**
     * Updates workflow status according to the active levels
     * @param  ApprovalWorkflowTicket $workflow
     * @param  string                 $action    `APPROVED` or `DENIED`
     * @return null
     */
    protected function handleWorlflowUpdate(ApprovalWorkflowTicket $workflow)
    {
      //if all levels are approved, it will mark workflow as approved
      //query for all levels and update workflow accordingly
      if(!$workflow->approvalLevels()->where('is_active', 1)->first()){

        //if even a single level is denied, it will mark workflow as denied
        $deniedCount = $workflow->approvalLevels()->where('approval_level_statuses.status','DENIED')->count();
        $workflowStatus = $deniedCount > 0 ? 'DENIED' : 'APPROVED';
        //updating ticket
        $statusId = $workflowStatus == 'APPROVED' ? $workflow->action_on_approve : $workflow->action_on_deny;
        $this->changeStatus($statusId);
        //updating workflow
        $workflow->update(['status' => $workflowStatus]);
        $this->createActivity('workflow', $workflow, $workflowStatus);
      }
    }

    /**
     * Handles status change
     * @param  int $statusId
     * @return null
     */
    protected function changeStatus($statusId)
    {
      if($this->ticket->status != $statusId){

        $ticketControllerInstance = new TicketController;

        $this->ticket->status = $statusId;

        $this->ticket->save();

        $status = Status::find($statusId);
        //sending notifications
        $ticketControllerInstance->sendNotification($this->ticket, $status);
      }
    }

}
