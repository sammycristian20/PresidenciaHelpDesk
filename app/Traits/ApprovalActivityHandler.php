<?php

namespace App\Traits;

use App\Model\helpdesk\Workflow\ApprovalLevelStatus;
use App\Model\helpdesk\Workflow\ApprovalWorkflowTicket;
use App\Model\helpdesk\Workflow\ApproverStatus;
use App\Repositories\TicketActivityLogRepository;
use Auth;
use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;
use App\Model\helpdesk\Manage\UserType;
use App\User;
use Exception;
use Config;

/**
 * Handles all thread related operations during approval workflow updation
 */
trait ApprovalActivityHandler
{

  /**
   * Creates thread based on the category
   * For eg. if category is passed as `workflow_applied`, it will create a thread that the given workflow_name
   * has been enforced (as internal notes).
   * if category is passed as `approver_approved`, it will create thread saying that particular approver has
   * approved
   * if category is passed as `level_approved`, it will create thread saying this level_name has approved
   * if new level is applied, it will say that level_name has been applied, with approver names
   * @param string $category  `workflow`, `level` or `approver`
   * @param object $referer an object of `ApprovalWorkflowTicket`,ApprovalLevelStatus` or `ApproverStatus`
   * @param string $action `ENFORCED`,`APPROVED`,'DENIED'
   * @param string $actionPerformer Full name of the user/workflow/listener which performs the actions
   * @return null
   */
  protected function createActivity($category, $referer, string $action, $comment = "", string $actionPerformer = null)
  {
      switch ($category) {

        case 'workflow':
          $internalNoteBody = $this->noteForWorkflow($referer, $action, $actionPerformer);
          break;

        case 'level':
          $internalNoteBody = $this->noteForLevel($referer, $action);
          break;

        case 'approver':
          $internalNoteBody = $this->noteForApprover($referer, $action, $comment);
          break;

        default:
          throw new Exception('invalid category passed to `createThread` method');
      }
      //if thread is null we populate $this->thread and then keep appending thread body
      $this->activity = $this->activity ?: TicketActivityLogRepository::log('', $this->ticket->id, 'user', $this->user->id ?? null);
      $this->activity->value = $this->activity->value.$internalNoteBody;
  }

  /**
   * Construct thread body for workflow apply and remove
   * NOTE: the reason for making it a seperate method is because it might come as enhancement to put
   * more details in internal thread
   * @param  ApprovalWorkflow $approvalWorkflow
   * @param  string $actionPerformer Full name of the user/workflow/listener which performs the actions
   * @return string
   */
  private function noteForWorkflow(ApprovalWorkflowTicket $approvalWorkflow, string $action, string $actionPerformer = null) : string
  {
      if($action == 'ENFORCED'){
        return "<b>$approvalWorkflow->name</b> approval workflow has been enforced on the ticket by <b>$actionPerformer</b><br>";
      } else if($action == 'REMOVED'){
        return "<b>$approvalWorkflow->name</b> approval workflow has been removed from the ticket by <b>$actionPerformer</b><br>";
     }
        return "<br/><br/><b>$approvalWorkflow->name</b> approval workflow has been <b>$action</b>";
  }

  /**
   * Construct thread body for workflow apply
   * NOTE: the reason for making it a seperate method is because it might come as enhancement to put
   * more details in internal thread
   * @param  ApprovalLevel $approvalLevel
   * @return string
   */
  private function noteForLevel(ApprovalLevelStatus $approvalLevel, string $action) : string
  {
      return "<br/><br/><b>$approvalLevel->name</b> approval level has been <b>$action</b>";
  }

  /**
   * Construct thread body for workflow apply
   * NOTE: the reason for making it a seperate method is because it might come as enhancement to put
   * more details in internal thread
   * @param  ApprovalStatus $approvalLevel
   * @param string $action   `APPROVED` or DENIED
   * @return string
   */
  private function noteForApprover(ApproverStatus $approver, string $action, $comment) : string
  {

      $user = User::where('id',$this->user->id)->select('first_name','last_name','email','user_name')->first();
      $userProfilePath = Config::get('app.url').'/user'."/".$this->user->id;
      $name = "<a href=$userProfilePath>$user->full_name</a>";

      //get approver name and mention he has approved or denied
      $body = "Ticket approval request has been <b>$action</b> by $name<br/>";

      if($comment){
        $body = $body."<b>REASON :</b> <i>$comment</i>";
      }

      return $body;
  }

}
