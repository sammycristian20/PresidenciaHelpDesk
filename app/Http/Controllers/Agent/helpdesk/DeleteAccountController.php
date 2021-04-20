<?php
namespace App\Http\Controllers\Agent\helpdesk;

use App\Http\Controllers\Agent\helpdesk\HandleAccountController;
use App\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\helpdesk\AccountDeletionRequest;
use App\Events\AccountDeletionEvent;
use App\Jobs\DeleteAccountJob;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Collaborator;
use File;

/**
 * The class to handle permanentn deletion of the user record in the system. This class
 * handles and processes the account deletion request and removes linking of the users with
 * core related models like tickets, collaborators etc.
 *
 * WHAT ACCOUNT DELETION PROCESS DOES?
 *
 * 1: An account can only be deleted once it has been deactivated, we shall not allow account deletion
 *    without deactivating the account, reason being this class only focuses on unlinking the related
 *    attributes like tickets, replies etc.
 * 2: If we already deactivate accounts we will ensure the pre-required actions such as surrendering
 *    or changing the assignee of the ticket has already been taken care of. All we need to do is handle
 *    the tickets owned by the user in which case we only have to delete the tickets or change their owner.
 * 3: Deleting the user permanently does not delete the user record from the database completely which can
 *    actually cause various issues due to linking of user with n number of related modules. So deleting
 *    user permanently does the following things:
 *    - Delete all tickets and all related records like replies, attachments, collaborators
 *    - Delete all personal information of users like their name, profile picture,email address,
 *      contact detail etc and replaces them with generic deleted user's information.
 *    - Delete user from all collaborating tickets
 *    - Delete custom fields data of requester form
 *    - Delete their notifications (@todo refine more after notification revamping)
 *    - Log them out of the devices
 *       - delete their oAuth tokens
 *       - delete FCM tokens of their mobile devices
 * 4: Emits event informing account deletion requested or being processed so that other developers
 *    can listen to these events and take necessary actions
 *
 * WHAT ACCOUNT DELETION PROCESS DOES NOT DO?
 *
 * 1: This class currently does not handle any other dependency which is not listed above reason some of them
 * should already be handled in deactivation process and other are just references to the deleted account so
 * it will not affect the system in any way. Example while deleting if we do not select any action for owned
 * tickets then ticket will not be deleted and will still be linked to deleted account.
 * 2: For business continuity we do not deleted the followings
 *  - Replies made by the users on the tickets he does not own or the tickets he collaborated on.
 *  - Comments made by the user on KB articles    
 *  - If user is an agent then custom filters, reports, articles added by the user.
 *
 * @package App\Http\Controllers\Agent\helpdesk
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @since v4.0.0
 */
class DeleteAccountController extends HandleAccountController
{
	public function __construct()
	{
		parent::__construct();
	}

    /**
     * Method to handle account and process account deletion request
     * @param  User                          $user     User to deactivate
     * @param  AccountDeactivationRequest    $request  request preference data
     * @return \Illuminate\Http\JsonResponse
     */
	public function handleAccountDeleteRequest(User $user, AccountDeletionRequest $request)
    {
        try {
            $this->user = $user;
            $this->request = $request;
            $this->authUserCanPerformTheAction();
            $this->checkIsAccountActive();
            $this->validateTrasnferringTickets('action_on_owned_tickets', 'change_owner', 'set_owner_to');
            event(new AccountDeletionEvent($user, Auth::user(), 'requesting'));
            $user->processing_account_disabling = 1;
            $user->save();
            dispatch(new DeleteAccountJob($this->user, Auth::user(), $this->request->all()));
            return successResponse(trans('lang.account_deletion_requested_successfully'));
        } catch(\Exception $e) {

            return errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Method checks and throws exception if we try to delete an active account.
     * We do not want to delete/handle dependencies and set account as deleted before
     * deactivating them. Deactivating the account prior to delete will let us handle the
     * dependencies at HandleAccountController and will help us to focus only on removing
     * only personal data of the user using this controller.
     *
     * @throws Exception
     */
    private function checkIsAccountActive()
    {
        if($this->user->active == 1) {
            throw new Exception(trans('lang.cannot_delete_an_active_account'), 401);
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
    public function processDeleteJob(User $user, User $actor, array $request):void
    {
        $this->user = $user;
        $this->request = $request;
        $this->actor = $actor;
        $this->handleOwnedTicketsForDeactivation();
        event(new AccountDeletionEvent($user, $actor, 'processing'));
        $this->detachUserPersonalInformations();
        foreach ($this->getDeletedUserAttributes() as $key => $value) {
            $user->{$key} = $value;
        }
        $user->permissions()->detach();
        $user->is_delete = 1;
        $user->processing_account_disabling = 0;
        $user->save();
    }

    private function detachUserPersonalInformations()
    {
        $this->deleteUserProfilePic();
        $this->user->customFieldValues()->delete();
        Ticket_Collaborator::where('user_id', $this->user->id)->delete();
        $this->user->notification()->delete();
        $this->logoutUserAccount();
    }

    /**
     * Method deletes user profile picture from file system
     */
    private function deleteUserProfilePic():void
    {
        File::delete(public_path('uploads/profilepic/'.$this->user->profile_pic));
    }

    /**
     * Method returns the array containing columns which save user's personal information
     * in the database and their default values to update and clear user personal info
     *
     * @return array
     */
    private function getDeletedUserAttributes():array
    {
        return [
            'email' => null,
            'mobile'=> null,
            'first_name' => 'Deleted Account',
            'last_name' => null,
            'user_name' => 'deleted_'.$this->user->id,
            'password' => '',
            'agent_sign' => '',
            'phone_number' => null,
            'internal_note' => null,
            'country_code' => null,
            'ext' => null,
            'profile_pic' => url('uploads/profilepic/deletedcontacthead.png')
        ];
    }

    /**
     * Method to handle tickets owned by the user who is getting deleted based on the
     * preference of the user who is deactivating/deleting the user which they set in the
     * deactivation/deletion request
     *
     * @param string  $action  type of action being taken on user "deactivated" or "deleted"
     * @return void
     */
    protected function handleOwnedTicketsForDeactivation(string $action="delete", bool $updateAllTickets=true)
    {
        if($this->request['action_on_owned_tickets'] == 'delete'){
            return $this->deleteUserTicketsAndDependencies();
        }

        return parent::handleOwnedTicketsForDeactivation($action, $updateAllTickets);
    }

    /**
     * Method to handle hard deleting of tickets owned by users and delete tickets
     * @return void
     */
    private function deleteUserTicketsAndDependencies():void
    {
        $this->user->ticketsRequester()->chunkById(100, function($tickets) {
            foreach ($tickets as $ticket) {
                $this->deleteThreads($ticket);
                $ticket->collaborator()->delete();
            }
        });
        $this->user->ticketsRequester()->delete();
    }

    /**
     * Method to handle hard deleting of threads of the ticket and deletes threads
     * @param   Tickets  $ticket
     * @return  void
     */
    private function deleteThreads(Tickets $ticket)
    {
        $ticket->thread()->chunkById(100, function($threads) {
            foreach ($threads as $thread) {
                $this->deleteAttachments($thread);
            }
        });
        $ticket->thread()->delete();
    }

    /**
     * Method to delete attachments of the ticket owned by the user from file system
     * and their references in the database. 
     */
    private function deleteAttachments(Ticket_Thread $thread)
    {
        $files = $thread->attach()->pluck('name', 'path')->toArray();
        foreach ($files as $path => $name) {
            File::delete($path.DIRECTORY_SEPARATOR.$name);
        }
        $thread->attach()->delete();
    }
}
