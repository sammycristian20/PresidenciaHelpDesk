<?php
namespace App\Http\Controllers\Agent\helpdesk;

use App\Http\Controllers\Controller;
use App\Repositories\TicketActivityLogRepository;
use App\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\helpdesk\AccountDeactivationRequest;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\Tickets;
use App\Events\AccountDeactivationEvent;
use App\Jobs\DeactiveAccountJob;
/**
 * This class deals with the actions which can be taken on account level
 * in the system. Such actions can be as following
 * - Deactivating user's account
 * - Restoring user's account
 * - Deleting the user's account
 *
 * This class handles only necessary actions for processing above actions on the
 * user accounts. It also handles and performs required actions on the dependent
 * model of User. 
 *
 * @package App\Http\Controllers\Agent\helpdesk
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @since v4.0.0
 */
class HandleAccountController extends Controller
{

    protected $request;

    protected $user;

    protected $actor;

	public function __construct()
	{
		/**
		 * checking if the role is agent as all actions of this class can only be
		 * performed by the agents and admins.
		 */
        $this->middleware('role.agent');
	}

	/**
     * This method restores user account by simply updating value of 'active' to 1.
     *
     * All other actions will not be reverted like if while deactivating a user's account
     * we changed the owner of the tickets owned by the user, then restoring the user account
     * will not set owner back to the user.  
     *
     * @param type $userId of User
     * @return \Illuminate\Http\JsonResponse json response
     */
    public function restoreUser(User $user)
    {
        try {
            $user->active = 1 ;
            $user->save();
            return successResponse(trans('lang.user_restore_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Method to handle account and process account deactivation request
     * @param  User                          $user     User to deactivate
     * @param  AccountDeactivationRequest    $request  request preference data
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleAccountDeactiveRequest(User $user, AccountDeactivationRequest $request)
    {
        try {
            $this->user = $user;
            $this->request = $request;
            $this->authUserCanPerformTheAction();
            $this->validateTrasnferringTickets('action_on_owned_tickets', 'change_owner', 'set_owner_to');
            $this->validateTrasnferringTickets('action_on_assigned_tickets', 'change_assignee', 'set_assignee_to');
            event(new AccountDeactivationEvent($user, Auth::user(), 'requesting'));
            $user->processing_account_disabling = 1;
            $user->save();
            dispatch(new DeactiveAccountJob($this->user, Auth::user(), $this->request->all()));
            return successResponse(trans('lang.account_deactivation_requested_successfully'));
        } catch(\Exception $e) {

            return errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Method to process and deactivate the user
     * @param   User   $user    User to deactivate
     * @param   User   $actor   User who is deactivaing
     * @param   array  $request
     *
     * @return  void
     */
    public function processDeactivationJob(User $user, User $actor, array $request):void
    {
        $this->user = $user;
        $this->request = $request;
        $this->actor = $actor;
        $this->handleOwnedTicketsForDeactivation();
        if($user->role != 'user') {
            $this->handleAssignedTicketsForDeactivation();
        }
        $this->logoutUserAccount();
        event(new AccountDeactivationEvent($user, $actor, 'processing'));
        $user->active = 0;
        $user->processing_account_disabling = 0;
        $user->save();
    }

    /**
     * In any case agents should not be able to delete/deactivate admin
     * accouts so this check will restrict only admin to be able to deactivate
     * or delete other admin accounts.
     *
     * @throws Exception
     * @return void
     */
    protected function authUserCanPerformTheAction():void
    {
        $authIsNotAdmin = in_array(Auth::user()->role, ['agent', 'user']);
        $userIsAdmin = $this->user->role == "admin";
        $deletingThemselves = Auth::user()->id == $this->user->id;
        /**
         * Checking the sum of all the above variables to 2 will help
         * 1: restrict agents from deleting admins account
         *  $authIsNotAdmin = 1,  $userIsAdmin=1, $deletingThemselves=0
         *
         * 2: restrict agents from deleting own account
         *  $authIsNotAdmin = 1, $userIsAdmin=0, $deletingThemselves=1
         *
         * 3: allow agents to delete other agent's account
         *  $authIsNotAdmin = 1, $userIsAdmin=0, $deletingThemselves=0
         *
         * 4: restrict admin from deleting own account
         * $authIsNotAdmin = 0, $userIsAdmin=1, $deletingThemselves=1
         *
         * 5: allow admin to delete agent accounts
         * $authIsNotAdmin = 0, $userIsAdmin=0, $deletingThemselves=0
         *
         * 6: allow admin to delete other admin accounts
         *  $authIsNotAdmin = 0, $userIsAdmin=1, $deletingThemselves=0
         */
        if($authIsNotAdmin + $userIsAdmin + $deletingThemselves >= 2) {
            throw new Exception(trans('lang.permission_denied'), 401);
        }
    }

    /**
     * While deactivating or deleting the user we can select to change owner of the tickets
     * owned by the user and change assignee of the tickets assigned to the user. This method
     * checks and restrict us from reassigning or setting the user as the same user we are
     * deleting/deactivating.
     *
     * @throws Exception 
     */
    protected function validateTrasnferringTickets($action, $expectedActionValue, $transferToKey)
    {
        if($this->request->{$action} == $expectedActionValue) {
            if($this->request->{$transferToKey} == $this->user->id) {
                throw new Exception(trans('lang.select_another_user'), 400);
            }
        }
    }

    /**
     * Method to handle tickets owned by the user who is getting deactivated based on the
     * preference of the user who is deactivating/deleting the user which they set in the
     * deactivation/deletion request
     *
     * @param string  $action  type of action being taken on user "deactivated" or "deleted"
     * @return null
     */
    protected function handleOwnedTicketsForDeactivation(string $action='deactivated', bool $updateAllTickets=false)
    {
        if($this->request['action_on_owned_tickets'] == 'change_owner'){

            return $this->changeOwnerOfUserTickets($action, $updateAllTickets);
        }

        return $this->closeUserTickets($action);
    }

    /**
     * Method closes open tickets of the user and add the logs about user deletion/deactivation
     * on all tickets owned by the user.
     * @param string $action
     * @return void
     */
    protected function closeUserTickets(string $action)
    {
        $openStatusIds = getStatusArray('open');
        $closeStatusId = Ticket_Status::whereIn('id', getStatusArray('closed'))->where('default',1)->value('id');
        $this->user->ticketsRequester()->chunkById(10, function($tickets) use($openStatusIds, $closeStatusId, $action){
            foreach ($tickets as $ticket) {
                $this->addTicketThreadLog($ticket, $action, 'owner');
                // close tickets only if they are in status with open purpose
                if(in_array($ticket->status, $openStatusIds)) {
                    $ticket->status = $closeStatusId;
                    $ticket->save();
                }
            }
        });
    }

    /**
     * Method changes requester of tickets of the user and add the logs about user
     * deletion/deactivation on all tickets owned by the user.
     * @param string $action
     * @return void
     */
    protected function changeOwnerOfUserTickets($action, $updateAllTickets):void
    {
        $openStatusIds = $updateAllTickets ? Ticket_Status::pluck('id')->toArray() : getStatusArray('open');
        $this->user->ticketsRequester()->chunkById(10, function($tickets) use($openStatusIds, $action){
            foreach ($tickets as $ticket) {
                $this->addTicketThreadLog($ticket, $action, 'owner');
                // change owner of the only if they are in status with open purpose
                if(in_array($ticket->status, $openStatusIds)) {
                    $ticket->user_id = $this->request['set_owner_to'];
                    $ticket->save();
                }
            }
        });
    }

    /**
     * Method to handle tickets assigned to the user who is getting deactivated based on the
     * preference of the user who is deactivating/deleting the user which he set in the
     * deactivation/deletion request. Either assign ticket to new user or surrender the tickets
     *
     * @return null
     */
    private function handleAssignedTicketsForDeactivation()
    {
        if($this->request['action_on_assigned_tickets'] == 'change_assignee') {
            return $this->changeAsigneeOfUserTickets($this->request['set_assignee_to']);
        }

        return $this->changeAsigneeOfUserTickets();
    }

    /**
     * Method changes the assignee of the tickets assigned to user and adds the log
     * about account deactivation.
     *
     * @param  int  $newUserId
     * @return void
     */
    private function changeAsigneeOfUserTickets(int $newUserId=null):void
    {
        $this->user->ticketsAssigned()->chunkById(10, function($tickets) use($newUserId){
            foreach ($tickets as $ticket) {
                $this->addTicketThreadLog($ticket, 'deactivated', 'assignee');
                $ticket->assigned_to = $newUserId;
                $ticket->save();
            }
        });
    }

    /**
     * Method add internal note on the ticket about user deactivation/deletion
     *
     * @param   Tickets  $ticket  ticket to add the log as internal note
     * @param   string   $action  action being takes on the account
     * @param   string   $role    relation of user with the ticket "assignee" or "owner"
     * @return  void
     */
    protected function addTicketThreadLog(Tickets $ticket, string $action, string $role):void
    {
        TicketActivityLogRepository::log(trans('lang.account_deactivate_delete_ticket_thread', [
            'profileURL' => url('/agent/'.$this->actor->id),
            'actor' => $this->actor->full_name,
            'action' => $action,
            'role' => $role,
        ]), $ticket->id);
    }

    /**
     * Method handles login session and tokes of the user being deleted or deactivated.
     * Since when user is deactivated and deleted we should discard all their session so
     * they cannot access the system and perform any actions on the system.
     *
     * @return void
     */
    protected function logoutUserAccount():void
    {
        //delete notification tokens to stop mobile notifications
        $this->user->notificationTokens()->delete();
        //delete oAuth tokens to discard user's session
        $this->user->tokens->each(function($token, $key) {
            $token->delete();
        });
    }
}
