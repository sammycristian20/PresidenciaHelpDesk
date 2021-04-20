<?php

namespace App\Observers;

use App\Events\Ticket\TicketThreadCreating;
use App\Events\Ticket\TicketThreadUpdating;
use App\Http\Controllers\Common\TicketsWrite\SlaEnforcer;
use App\Http\Controllers\SLA\Reminders;
use Carbon\Carbon;
use App\Observers\Listener;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Http\Controllers\Common\TicketsWrite\TicketWorkflowController;
use DB;

class ThreadObserver extends Listener
{

    /**
     * Set the thread 
     * @var Ticket_Thread 
     */
    protected $thread;
    protected $chain = true;

    /**
     * Listening to thread saving event
     * @param Ticket_Thread $thread
     * @return void
     */
    public function saving(Ticket_Thread $thread)
    {
        $ticket            = $thread->ticket;
        $ticket_array      = [];
        $this->model_name  = 'thread';
        $this->model_event = 'saving';
        $this->ticket      = $ticket;
        $this->model       = $thread;
        $this->orginal     = $thread->getOriginal();
        $this->changed     = $thread->isDirty() ? $thread->getDirty() : [];
        $this->thread      = $thread;

        if($this->thread->ticket->firstThread){
            // comes here only when a reply is made (can be internal note or an actual reply but can be handled in one event)
            // firing TicketThreadGenerating
            // in listener, generate an activty log saying a reply has been made
            $ticket_array      = (new TicketWorkflowController)->processListeners($ticket, $this->orginal, $this->changed, $thread->user);
            $ticket->fill($ticket_array);
            CustomFormValue::updateOrCreateCustomFields($ticket_array, $ticket);
        }
        $ticket->updated_at = Carbon::now();
        $ticket->save();
    }
    /**
     * Check the event happened in the system and event saved in databse are same 
     * @return boolean
     */
    public function checkEvent()
    {
        $n = 0;
        while ($n < count($this->events)) {
            $point     = checkArray('event', $this->events[$n]);
            $condition = checkArray('condition', $this->events[$n]);
            $result    = $this->eventConditions($condition, $point);
            if ($result) {
                return $result;
            }
            $n++;
        }
        return false;
    }
    /**
     * Check the events saved conditions and activity conditions are same
     * @param string $condition
     * @param string $point
     * @param string|int $condition_old
     * @param string|int $condition_new
     * @return boolean
     */
    public function eventConditions($condition, $point, $condition_old = '', $condition_new
    = '')
    {
        if ($point == 'reply') {
            switch ($condition) {
                case "support":
                    return $this->isReply('agent');
                case "requester":
                    return $this->isReply('requester');
                default:
                    return $this->isReply();
            }
        }
        if ($point == 'note') {
            return $this->isNote();
        }
        return false;
    }
    /**
     * Check is it a reply
     * @param string $performer
     * @return boolean
     */
    public function isReply($performer = '')
    {
        if ($this->thread->ticket->thread()->count() > 0) {
            if ($performer == 'requester') {
                return ($this->thread && $this->thread->poster == 'client' && $this->thread->is_internal
                        == 0) ? true : false;
            }
            if ($performer == 'agent') {
                return ($this->thread && $this->thread->poster == 'support' && $this->thread->is_internal
                        == 0) ? true : false;
            }
            return ($this->thread && $this->thread->is_internal == 0) ? true : false;
        }
        return false;
    }
    /**
     * Check is it a not entry
     * @return boolean
     */
    public function isNote()
    {
        return ($this->thread && $this->thread->thread_type == 'note') ? true : false;
    }
    
    public function deleting(Ticket_Thread $thread){
        $thread->attach()->delete();
    }


    public function created(Ticket_Thread $thread) {
        try{
            switch ($thread->poster) {
                case 'client':
                    event(new \App\Events\WebHookEvent(null,"ticket_commented", $thread));
                    break;

                case 'support':
                    if(!$thread->thread_type || $thread->thread_type == "first_reply")
                        event(new \App\Events\WebHookEvent(null,"ticket_reply", $thread));

                    elseif($thread->thread_type == "ticket_assign_alert")
                        event(new \App\Events\WebHookEvent(null,"ticket_assigned", $thread->ticket));
                    break;
                
                default:

                    break;
            }
        }
        catch(\Exception $e){
            \Log::info("Webhook Exception caught : ".$e->getMessage());
        }
    }

    public function creating(Ticket_Thread $thread)
    {
        event(new TicketThreadCreating($thread->getDirty()));
    }

    public function updating(Ticket_Thread $thread)
    {
        event(new TicketThreadUpdating($thread->getDirty()));
    }
}
