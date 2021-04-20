<?php

namespace App\Http\Controllers\Common\TicketsWrite;

use App\Events\Ticket\TicketListenerEnforcing;
use App\Events\Ticket\TicketWorkflowEnforcing;
use App\Model\helpdesk\Ticket\TicketWorkflow;
use App\Model\helpdesk\Ticket\TicketListener;
use App\Http\Controllers\Controller;
use App\Traits\EnforceRuleAndAction;
use App\Traits\TicketKeyMutator;
use Illuminate\Database\Eloquent\Collection;
use App\User;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use Auth;
use Illuminate\Http\Request;

/**
 *
 */
class TicketWorkflowController extends Controller
{
    use EnforceRuleAndAction, TicketKeyMutator;

    /**
     * The person who is the requester of the ticket
     * @var array
     */
    private $requester;

    /**
     * Performer of action
     * @var string
     */
    private $performer;
    /**
     * Array containing original values for ticket data
     * @var array
     */
    private $original;
    /**
     * Array containing changed values for ticket data
     * @var array
     */
    private $changed;
    /**
     *                  !!!!!!!!!!!!IMPORTANT!!!!!!!!!!!!
     *
     * As we are not rewriting ticket create module and just updating custom form field data.
     * I am writing this new class assuming we are using same class and control structure to modify ticket data using workflow.
     * Method process() of TicketWorkflowController class was/is called from WorkflowListen.php while
     * handling the event called from TicketController. It accepts an array of ticket data fields and values as its
     * key pair. It only checks that array against workflow rules in sorted order and applies the action of first matching
     * workflow. It updates the ticket data array and return it back to WorkflowListen where it is actually saved.
     */

    /**
     * Function to process workflows and modify ticket values before creating tickets
     * @param  array $ticketValuesArray  Array containing ticket fields and their values as key=>pair
     * @return array
     */
    public function process(array $ticketValuesArray):array
    {
        if(empty($ticketValuesArray)) return $ticketValuesArray;

        $workflows = TicketWorkflow::where('status', 1)->orderBy('order')->get();

        if (!$workflows) return $ticketValuesArray;

        // converting ticket array into new key, so that workflow should be able to process that
        $this->formatTicketsArrayFromOldToNewKey($ticketValuesArray);

        $ticketValuesArray = $this->enforceAfterCheckingRules($workflows, $ticketValuesArray);

        // converting ticket array back into old keys so that rest of the code should be able to handle that
        $this->formatTicketsArrayFromNewToOldKey($ticketValuesArray);

        // removing user keys from ticket array
        $this->removeUserCustomField($ticketValuesArray);

        return $ticketValuesArray;
    }

    /**
     * Function to check rules of each workflow/listener and enforce actions of that
     * workflow/listener to update ticket values
     * @param  Collection  $workflows            Collection of workflows or listeners
     * @param  array       $ticketValuesArray    Value of tickets data
     * @return array
     */
    private function enforceAfterCheckingRules (Collection $workflows,
        array $ticketValuesArray, string $type = 'workflow'): array
    {
        foreach ($workflows as $workflow) {

            if(!$this->ifListenerShouldItApply($workflow, $this->original, $this->changed)) {
                // check next listener as listener does not pass event or performer check
                continue;
            }
            $rules = $this->getRules($workflow->id, $type);

            if ($this->checkRulesAndValues($rules, $ticketValuesArray, $workflow->matcher)){

                $actions = $this->getActions($workflow->id, $type);

                $ticketValuesArrayBeforeActions = $ticketValuesArray;

                $this->enforceActions($actions, $ticketValuesArray);

                \Event::dispatch('workflow-processed', [&$ticketValuesArray, $workflow, $actions]);

                $this->broadcastEvent($ticketValuesArray, $ticketValuesArrayBeforeActions, $workflow->id, $type);
            }
        }
        return $ticketValuesArray;
    }

    /**
     * dispatches event which tell that workflow/listener has been enforced
     * @param $ticketValuesArray
     * @param $ticketValuesArrayBeforeActions
     * @param $enforcerId
     * @param $type
     */
    private function broadcastEvent($ticketValuesArray, $ticketValuesArrayBeforeActions, $enforcerId, $type)
    {
        // in case of listener it becomes listener
        if($type == "workflow"){
            event(new TicketWorkflowEnforcing(array_diff_recursive($ticketValuesArray, $ticketValuesArrayBeforeActions), $enforcerId));
        } else {
            event(new TicketListenerEnforcing(array_diff_recursive($ticketValuesArray, $ticketValuesArrayBeforeActions), $enforcerId));
        }
    }

    /**
     * Function returns the action performer role as string
     * @param  int     $ownerId  id of ticket owner
     * @param  User    $user     Authenticated user or user who is performing action default null
     * @return String            agent/requester/system
     */
    private function getActionPerformer(int $ownerId, User $user = null) :string
    {
        if(!$user) {
            return 'system';
        }

        return ($ownerId == $user->id) ? 'requester' : 'agent';
    }

    /**
     * Function checks whether the action performer is matches with listener triggered_by
     * or not
     * @param  TicketListener  $listener  listener to fetch get trigger_by value
     * @return boolean                    true if matches otherwise false
     */
    private function checkActionIsPerformedByPerformerOfEvent(TicketListener $listener)
    {
        //listener will not be applied if action is performed by system
        if ($this->performer == 'system') return false;
        //listener will always be applied if listener has triggered by agent_requester
        if ($listener->triggered_by == 'agent_requester') return true;

        return $listener->triggered_by == $this->performer;
    }

    /**
     * Function fetches and returns ticket data for listeners by appending
     * cutom fields and title/description of tickets
     *
     * @param   Tickets  ticket of which data to be formatted in an array
     * @return  Array    formatted ticket data includes custom field data and thread data
     */
    private function getTicketDataForListeners(Tickets $ticket): array
    {
        $customs = $ticket->formattedCustomFieldValues();
        $ticketValuesArray = array_merge($customs, $ticket->toArray());
        if($ticket->firstThread) {
            $ticketValuesArray['subject'] = $ticket->firstThread->title;
           $ticketValuesArray['body'] = $ticket->firstThread->body;
        }

        return $ticketValuesArray;
    }

    /**
     * Function which actually updates the ticket data after listener enforcement
     *
     * @param   Array    $ticketValuesArray  Array containing ticket data key and values
     * @param   Array    $original           Array containing original values of ticket
     * @param   Array    $changed            Array containing changed values in ticket
     * @param   User     $performedBy        User who has performed the action default null
     * @return  Array                        Array containing ticket data after listener enforcements
     */
    public function processListeners(Tickets $ticket, array $original, array $changed, User $performedBy = null): array
    {
        $oldStatus    = $ticket->status;

        $ticketValuesArray = $this->getTicketDataForListeners($ticket);

        if(!$this->doPreConditionsForListenerPass($ticket, $ticketValuesArray)) return $ticketValuesArray;

        $listeners = TicketListener::where('status', 1)->orderBy('order')->get();

        if (!$listeners) return $ticketValuesArray;

        // converting ticket array into new key, so that workflow should be able to process that
        $this->formatTicketsArrayFromOldToNewKey($ticketValuesArray);

        $this->performer = $this->getActionPerformer($this->requester['id'], $performedBy);

        $this->original = $original;
        $this->formatTicketsArrayFromOldToNewKey($this->original);

        $this->changed = $changed;
        $this->formatTicketsArrayFromOldToNewKey($this->changed);

        $ticketValuesArray = $this->enforceAfterCheckingRules($listeners, $ticketValuesArray, 'listener');

        // converting ticket array back into old keys so that rest of the code should be able to handle that
        $this->formatTicketsArrayFromNewToOldKey($ticketValuesArray);

        //apply approval workflow if required
        $this->applyApprovalWorkflowFromListeners($ticket, $ticketValuesArray);

        // removing user keys from ticket array
        $this->removeUserCustomField($ticketValuesArray);

        return $ticketValuesArray;
    }

    /**
     * Removes user for user custom fields
     * @param $ticketValuesArray
     */
    private function removeUserCustomField(&$ticketValuesArray)
    {
        if (isset($ticketValuesArray['user_id'])) {
            foreach ($this->getUserCustomFields($ticketValuesArray['user_id']) as $key => $value) {
                unset($ticketValuesArray[$key]);
            }
        }
    }

    /**
     * Function to check listener for events and action performer. As the function is called from
     * single function which enfocorces listeners and workflow, it checks the object passed from the
     * enforceAfterCheckingRules is an instance of TicketListener then only it checks for
     * performer and events. In-case of workflow it always returns true
     *
     * @param   Object  $listener  object of Listener or Workflow model
     * @param   Array   $original  Array containing original values of ticket
     * @param   Array   $changed   Array containing changed values in ticket
     * @return  bool               true if $listener is an instance of workflow otherwise true/false
     *                             based on matching of listener action performer and events
     *
     */
    private function ifListenerShouldItApply($listener, array $original = null, array $changed = null): bool
    {
        if($listener instanceof TicketListener &&
        (!$this->checkActionIsPerformedByPerformerOfEvent($listener) ||
        !$this->checkListenerForEvents($listener, $original, $changed))) {
            return false;
        }

        return true;
    }

    /**
     * Function checks and returns true if any one of the events matches with the action performed
     * on the ticket otherwise if none of the event matches with the performed action it returns false
     *
     * @param   TicketListener  $listener  lister data to fetch events
     * @param   Array           $original  Array containing original values of ticket
     * @param   Array           $changed   Array containing changed values in ticket
     * @return  bool                       true/false
     */
    private function checkListenerForEvents(TicketListener $listener, array $original, array $changed): bool
    {
        $events = $listener->events()->get()->toArray();

        foreach ($events as $event) {
            if($this->checkEvent($event, $original, $changed)){
                //condition matched so listener will be enforced
                return true;
            }
        }
        return false;
    }

    /**
     * Function to check listener event and actions on the ticket
     *
     * @param  Array  $event    Array containing event data to be checked
     * @param  Array  $original Array containing original values of ticket
     * @param  Array  $changed  Array containing changed values in ticket
     * @return bool             true if performed action matches with the event
     *                          false otherwise
     */
    private function checkEvent(array $event, array $original,array $changed): bool
    {
        if(!in_array($event['field'], ['note', 'reply', 'duedate'])) {
            if(!checkArray($event['field'], $changed)){
                return false;
            }
            return $this->checkDependentEvents($event, $original, $changed);
        }

        return $this->checkIndependentEvents($event['field'], $changed);
    }

    /**
     * Function compares ticket changed array for the events which depends on the initial and final
     * values of ticket data such events are changing status or department of the tickets etc.
     *
     * @param  Array $event    Array containing event data to be checked
     * @param  Array $original Array containing original values of ticket
     * @param  Array $changed  Array containing changed values in ticket
     * @return                 true if event matches with the action performed false otherwise
     */
    private function checkDependentEvents(array $event,array $original,array $changed): bool
    {
        $oldData =  (int)checkArray($event['field'], $original);
        $newData =  (int)checkArray($event['field'], $changed);
        $test    =  $event['from'] - $event['to'];
        if($test == 0) {
            //from and to is any in the event so no need to verify old and new value
            return true;
        } elseif ($event['from'] == $test) {
            // to is any but from is not any so check old value of ticket
            return $oldData == $event['from'];
        } elseif ($event['to'] == abs($test)) {
            // from is any but to is not any so check new value of ticket
            return $newData == $event['to'];
        }
        //from and to both are not any so need to check both new and old values
        return abs($event['from'] - $oldData) + abs($event['to'] - $newData) == 0;
    }

    /**
     * Function compares ticket changed array for the events which do not depend on
     * the initial and final values of field in events such events are when reply or
     * note is added or duedate is updated.
     *
     * @param  String $event    Name of field in event
     * @param  Array  $changed  Array containing changed values in ticket
     * @return Bool             True if $changed contains $eventField otherwise false
     */
    private function checkIndependentEvents(string $eventField, array $changed): bool
    {
        if(checkArray('is_internal', $changed)) {
            /**
             * NOTE: System also adds internal notes in the system but in events we should only
             * check the notes which are being added by agents which contains thread_type as note
             * and internal_note value as 1. So this method checks if changed contains internal_notes
             * then $eventField  and thread_type must be equal and 'note'.
             * we do not check for value as "note" because the only condition
             * in which below statement will be true when value of $eventField is note
             * and thread_type is note
             **/
            return $eventField == checkArray('thread_type', $changed);
        }
        $key = $eventField == 'duedate' ? $eventField : 'description';
        return (bool)checkArray($key, $changed);
    }

    /**
     * Function to apply approval workflow on ticket if avaiable in workflow/listener actions
     * it updates the value of status in ticketValuesArray to resultant status updated after
     * applying approval
     *
     * @param  Tickets  $ticket  ticket on which approval workflow need to be aplied
     * @param  Array    $values  array containing ticket values updated after enforcing actions
     * @return void
     */
    private function applyApprovalWorkflowFromListeners(Tickets $ticket, array &$ticketValuesArray):void
    {
        if(checkArray('approval_workflow_id', $ticketValuesArray)) {
            if((new \App\Model\helpdesk\Workflow\ApprovalWorkflow)->find($ticketValuesArray['approval_workflow_id'])) {
                $request = new Request;
                $request->merge(['ticket_id' => $ticket->id, 'workflow_id' => $ticketValuesArray['approval_workflow_id']]);
                (new \App\Http\Controllers\Agent\helpdesk\TicketsWrite\ApiApproverController)
                ->applyApprovalWorkflow($request, $ticketValuesArray['approval_workflow_enforcer']." listener");
                $ticketValuesArray['status'] = Tickets::whereId($ticket->id)->value('status');
            }
        }
    }

    /**
     * Function checks pre conditions for listeners which are not related to listener events or rules
     * but must satisfy before processing listeners
     *
     * @param  Tickets  $ticket
     * @param  Array    $ticketValuesArray  array containing ticket values before processing listeners
     * @return bool                         true if conditions satisfy else false
     */
    private function doPreConditionsForListenerPass(Tickets $ticket, array $ticketValuesArray):bool
    {
        return !empty($ticketValuesArray) && !(bool) $ticket->approvalStatus()->where('status', 'PENDING')->count();
    }
}
