<?php

namespace App\Http\Controllers\Common\TicketsWrite;

use App\Model\helpdesk\Ticket\Tickets;

use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Requests\helpdesk\Common\ReplyRequest;
use App\Bill\Models\Bill;
use Auth;
/**
 * 
 */
class TicketReplyController extends TicketController
{

	/**
	 * @var bool  tells if the reply is being added as an internal note or not
	 */
	private $isInternalNote;

	/**
	 *@var bool   tells if the reply is being added by the requester/user or not
	 */
	private $isRequester;

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		$this->middleware('auth');
		parent::__construct();
	}

	/**
	 * Function which handles reply API and addes reply or internal notes on the tickets
	 *
	 * NOTE : This method uses saveReply() of TicketController and assumes it handles reply
	 * process including sending notifications/changing status/response time etc without errors.
	 * 
	 * @param  ReplyRequest  $request  reply requets containing required data for reply
	 * @param  Tickets       $ticket   ticket on which reply will be added
	 *
	 * @return Illuminate\Http\JsonResponse 
	 */
	public function postReply(ReplyRequest $request, Tickets $ticket)
	{
		try {
			$this->isInternalNote = $request->get('is_internal_note', 0) == 1;
			$this->isRequester  = $this->isRequesterOfTheTicket($ticket);
			$this->canAuthenticatedUserProceedToReply($ticket);
			if(!$this->isReplyAllowed($ticket)) return errorResponse(trans('lang.can_not_reply_on_inactive_ticket'),FAVEO_ERROR_CODE);


			$body = $request->input('content');
            $inline = $request->input('inline');
            $attachment = $request->input('attachment')?:$request->file('attachment');//
            
            $poster = $this->getThreadPoster();

            $replyToRecipent = $this->getReplyToRecipents($request);
            $collaborators = $request->cc ? : [];

            // if cc is there in the request, we have to delete the collaborators which are not there in the request
            $syncCollaborators = $this->shallSyncCollaborator($request, $ticket);

            $formattedCollaborators = [];
            array_map(function($collaborator) use (&$formattedCollaborators){
              $formattedCollaborators[$collaborator] = $collaborator;
            }, $collaborators);
            $this->dispatchReplyEvent($ticket, $body, $attachment);
            $thread = $this->saveReply($ticket->id, $body, Auth::user()->id, true, $attachment, $inline, true, $poster, [], $formattedCollaborators, $replyToRecipent, $syncCollaborators, $this->isRequester, $this->isInternalNote);
            /**
             * @todo Remove below code snippet if billing not used anymore
             */
            $this->updateBillingHours($request, $ticket);

            return successResponse(trans('lang.successfully_replied'),$thread->makeHidden(['ticket', 'user', 'poster', 'user_id', 'response_time', 'thread_type', 'id', 'attach']));
		} catch(\Exception $e) {

			return errorResponse($e->getMessage(), $e->getCode()?:FAVEO_ERROR_CODE);
		}
	}

	/**
	 * Function denotes if the authenticated user should be considered as a Requester
	 * of the ticket or not.
	 * Note: As single API is accessible by all users irrespectice of their role we
	 * must classify authenticated user as requester or non requester.
	 *
	 * @param  Tickets  $ticket  ticket on which reply will be added
	 *
	 * @return bool              True : If authenticated user's role is user
	 *                                  If ticket requester Id is same as authenticated user's id
	 *                  			    else it will always return False  
	 */
	private function isRequesterOfTheTicket(Tickets $ticket):bool
	{
		return Auth::user()->role == 'user' || Auth::user()->id == $ticket->user_id;
	}

	/**
	 * Function throws an exception with FAVEO_ERROR_CODE if authenticated user
	 * is not allowed to process and add the reply on the tickets
	 * Example of such cases are
	 * - When a requester of the ticket tries to add an internal note on the ticket
	 * - When any arbitrary client user tries to make a reply or add a note on any
	 *   arbitrary ticket that does not belong to them by any of the below mean
	 *   a. They are added as CC on the ticket
	 *   b. They are organisation manager of the requester of the ticket
	 *   c. They are organisation member of the requester of the ticket
	 *
	 * @param  Tickets  $tickets         ticket on which reply will be added
	 *
	 * @return void
	 * @throws Exception
	 */
	private function canAuthenticatedUserProceedToReply(Tickets $ticket):void
	{
		if(2 != $this->isInternalNote+(Auth::user()->role=='user')) {
			if($this->canMakeReplyOnTheTicket($ticket->id))return;
		}
		throw new \Exception(trans('lang.unauthorized_access_for_reply'), FAVEO_ERROR_CODE);
	}

	/**
	 * Function to detect if auth user can add reply on the ticket or not.
	 *
	 * NOTE: This method uses getTicketForEmailCheck() of TicketController and assumes it
	 * handles if "user" can reply on the ticket or not without errors.
	 *
	 * @param   int   $id  integer id of ticket on which reply will be added
	 * @return  bool       true if reply is being added by admin/agents else
	 *                     false if user is not linked to the ticket by any mean otherwise true
	 */
	private function canMakeReplyOnTheTicket(int $id):bool
	{
		if(Auth::user()->role != 'user') return true;
		return (bool)$this->getTicketForEmailCheck(Tickets::where('id', $id), Auth::user()->id);
	}

	/**
	 * Function tell if a reply is allowed on the ticket or not. Incase of closed
	 * ticket requester and users can only reply within the limit of closed waiting
	 * time period else they will be asked to reopen the ticket or create a new one.
	 *
	 * NOTE: This method uses checkReplyTime() of TicketController and assumes it
	 * handles if close waiting time check for tickets without erros.
	 *
	 * @param   Tickets  $tickets      ticket on which reply will be added
	 *
	 * @return  bool                   true if reply is not by the requester else true/false 
	 *                                 based on closed waiting time settings
	 */
	private function isReplyAllowed(Tickets $ticket)
	{
		if(!$this->isRequester) return true;

		return !$this->checkReplyTime($ticket->id);
	}

	/**
	 * Function returns string value of poster of the reply thread.
	 *
	 * @return  string                 "support"     :if agents/admins are replying
	 *                                 "client"      :if users are replying
	 *                                 "admin/agent" :if reply is an internal note 
	 */
	private function getThreadPoster():string
	{
		if($this->isInternalNote) return Auth::user()->role;
		
		return ($this->isRequester)?'client': 'support';
	}

	/**
	 * Function returns an array with recipents to reply
	 *
	 * @param   ReplyRequest  $request
	 * @param   bool          $isRequester  tells if auth user is requester of the ticket or not
	 *
	 * @return  array                       an empty array or array value of parameter 
	 *                                      "to" passed in the $request 
	 */
	private function getReplyToRecipents(ReplyRequest $request):array
	{
		if($this->isRequester) return [];
		return $request->to ? $request->to : [];
	}

	/**
	 * Function return boolean flag denoting if the CC in request shall be synced or not.
	 *
	 * NOTE: Syncing collaborator is done in further execution of reply process and only actual
	 * requester and admin/agents are allowed to sync collaborator if CC is present in the request. 
	 *
	 * @param  ReplyRequest  $request
	 * @param  Tickets       $ticket
	 *
	 * @return bool          true if auth user is either 
	 */
	private function shallSyncCollaborator(ReplyRequest $request, Tickets $ticket):bool
	{
		$sync = $request->has('cc');
		if($this->isRequester) {
			$sync = (Auth::user()->id == $ticket->user_id)? $sync :false;
		}
		
		return $sync;
	}

	/**
	 * Function to dispatch ticket reply event so custom plugins and modules can perform
	 * certain actions after reply has been added. E.g. posting reply on facebook/whatsapp
	 * NOTE: Currenlty we are only listening for the reply event when a reply is from agent
	 * and reply type is not an internal note. 
	 *
	 * @param  Tickets  $ticket      ticket on which reply was added
	 * @param  string   $body        reply content
	 * @param  array    $attachment  attachment filed added during reply
	 *
	 * @return void
	 */
	private function dispatchReplyEvent(Tickets $ticket, string $body, array $attachment=null):void
	{
		if(!$this->isInternalNote && !$this->isRequester) {
			\Event::dispatch('Reply-Ticket', [['ticket_id' => $ticket->id, 'body' => $body,'attachment' => $attachment]]);
		}
	}

	/**
     * @todo Remove below code snippet if billing not used anymore
     */
	private function updateBillingHours(ReplyRequest $request, Tickets $ticket):void
	{
		if($request->billable && !$this->isRequesterOfTheTicket($ticket)) {
			$bill = new Bill();
            $bill->level = 'thread';
            $bill->model_id = $ticket->id;
            $bill->agent = Auth::user()->id;
            $bill->ticket_id = $ticket->id;
            $bill->hours = $request->hours;
            $bill->billable = $request->billable;
            $bill->amount_hourly = $request->amount_hourly;
            $bill->note = $request->content;
            $bill->save();
		}
	}
}
