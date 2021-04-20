<?php

namespace App\Observers;

use App\Events\Ticket\TicketUpdating;
use App\Http\Controllers\Common\TicketsWrite\SlaEnforcer;

use App\Model\helpdesk\Settings\Ticket;
use App\Model\Common\TicketActivityLog;
use App\Model\helpdesk\Ticket\Tickets;
use App\Observers\Listener as Observe;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Http\Controllers\Admin\helpdesk\SettingsController;
use App\Http\Controllers\Common\TicketsWrite\TicketWorkflowController;
use Auth;

class TicketObserver extends Observe
{

    /**
     * Event occurring ticket instance
     * @var Tickets
     */
    protected $ticket;
    protected $chain = true;

    /**
     * Listen to an updated event in ticket update
     * @param Tickets $ticket
     * @return void
     */
    public function updating(Tickets $ticket)
    {
        // should log all the changes done before even listener was executed
        // need to know a way by which we can know if it is the first time ticket is getting saved
        // how to know if it is first time or not
        // emit event that ticket is getting updated
        // if listener log is empty, means no listener has been enfored
        event(new TicketUpdating(array_merge($ticket->getDirty(), $ticket->formattedCustomFieldValues())));
        // get custom initial custom fields of the ticket

        if ($this->shallProcessListeners($ticket)) {
            $ticket_array = (new TicketWorkflowController)->processListeners($ticket, $ticket->getOriginal(), $ticket->getDirty(), Auth::user());
            krsort($ticket_array);
            $ticket->fill($ticket_array);
            CustomFormValue::updateOrCreateCustomFields($ticket_array, $ticket);
        }
    }

    /**
     * Decides if listeners should be processed
     * @param Tickets $ticket
     * @return bool
     */
    private function shallProcessListeners(Tickets $ticket)
    {
        $columnsWhichDoesntRequireListener = ['updated_at', "sla", "ticket_number"];

        // if there is no column other than mentioned above column, listener will not be executed
        if (count(array_diff(array_keys($ticket->getDirty()), $columnsWhichDoesntRequireListener))) {
            return true;
        }

        return false;
    }


    /**
     * Decides if SLA should be processed
     * @param Tickets $ticket
     * @return bool
     */
    private function shallProcessSLA(Tickets $ticket)
    {
        // SLA doesn't have to be recalculated when duedate is changed manually or updated_at column is updated
        $columnsWhichDoesntRequireListener = ['updated_at', "duedate", "ticket_number"];

        // if there is no column other than mentioned above column, listener will not be executed
        if (count(array_diff(array_keys($ticket->getDirty()), $columnsWhichDoesntRequireListener))) {
            return true;
        }

        return false;
    }

    public function saved(Tickets $ticket)
    {
        if($this->shallProcessSLA($ticket)){
            (new SlaEnforcer($ticket))->handleSlaRelatedUpdates();
        }
        TicketActivityLog::saveActivity($ticket->id);
    }


    public function created(Tickets $ticket)
    {
        try {

            $ticketNumber = (new SettingsController())->getTicketNumberById($ticket->id);
            // save it using DB facade to avoid initialisation of observers again

            /*
             * After ticket is created, if we assign certain values to its attributes, it is going to return the updated ticket
             * number in all the events which gets emitted after this event
             */
            $ticket->ticket_number = $ticketNumber;

            // updating the ticket without emitting any event
            \DB::table('tickets')->where('id', $ticket->id)->update(['ticket_number'=> $ticketNumber]);

            $url = apiSettings('web_hook');

            if ($url) {
                $tickets      = $ticket->toArray();
                $thread       = $ticket->thread()->select('title', 'body', 'created_at')->get()->toArray();
                $custom_field = $ticket->formdata()->select('key', 'content')->get()->toArray();
                $requester    = $ticket->user()->select('id', 'first_name', 'last_name', 'email', 'user_name')->first()->toArray();

                $parameters = [
                    'event'         => 'ticket_created',
                    'ticket'        => $tickets,
                    'thread'        => $thread,
                    'custom_fields' => $custom_field,
                    'requester'     => $requester,
                ];

                try {
                    event(new \App\Events\WebHookEvent(null, "ticket_created", $parameters));
                } catch (\Exception $e) {
                    \Log::info("Webhook Exception Caught:  ".$e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Logger::exception($e);
        }
    }
    public function updated(Tickets $ticket)
    {
        try {
            $url = apiSettings('web_hook');
            if ($url) {
                $tickets      = $ticket->toArray();
                $requester    = $ticket->user()->select('id', 'first_name', 'last_name', 'email', 'user_name')->first()->toArray();

                if ($ticket->getOriginal('user_id') != $ticket->user_id) {
                    $parameters = [
                        "event" => "ticket_owner_updated",
                        "thread" => $tickets,
                        "requester" => $requester,
                    ];
                    event(new \App\Events\WebHookEvent($parameters, "ticket_owner_updated"));
                }
            }
        } catch (\Exception $e) {
            \Log::info("exception cought : ". $e);
        }
    }

    public function deleting(Tickets $ticket)
    {
        $threads = $ticket->thread()->get();
        if (count($threads) > 0) {
            foreach ($threads as $thread) {
                $thread->delete();
            }
        }
        $ticket->collaborator()->delete();
        $ticket->allFormData()->delete();
        $ticket->filter()->delete();
        $ticket->approachingReminders()->detach();
        $ticket->violatedReminders()->detach();

        // related activity logs
        TicketActivityLog::where('ticket_id', $ticket->id)->delete();
    }
}
