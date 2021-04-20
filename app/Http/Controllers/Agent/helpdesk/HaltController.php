<?php

namespace App\Http\Controllers\Agent\helpdesk;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ticket\Halt;

class HaltController extends Controller {

    // NOTE FROM AVINASH: this class has been depreciated. It will be removed after testing
    protected $ticket_id;
    /**
     *Tickek model object in case of calling from handlechangestatus method
     *@var  Tickets
     */
    protected $ticketData;

    public function __construct($ticketid = "", $ticketData = null) {
        $this->ticket_id = $ticketid;
        $this->ticketData = $ticketData;
    }

//    public function setTicket($id) {
//        $this->ticket_id = $id;
//    }
//
//    protected function applySla() {
//        $apply_sla = new \App\Http\Controllers\SLA\ApplySla();
//        return $apply_sla;
//    }

//    public function timeUsed($type="response") {
//        $sla = $this->applySla();
//        $min = $sla->minSpent($this->ticket_id,$type);
//        return $min;
//    }

//    protected function createHalt($calledFromModelEvent = false) {
//        $halt = new Halt();
//        $ticket = $this->ticket();
//        $old_due = $ticket->duedate;
//        //was halted before
//        $wasHalted = $halt->where('ticket_id', $ticket->id)->orderBy('id','desc')->first();
//        if ($wasHalted && $ticket->duedate == null) {
//            $this->updateLastHalted($wasHalted);
//        }
//
//        if($ticket->isanswered=='1'){
//            //resolve
//            $time_used = $this->timeUsed('resolve');
//        }else{
//            //response
//            $time_used = $this->timeUsed();
//        }
//        $halt->ticket_id = $this->ticket_id;
//        $halt->time_used = $time_used;
//        $halt->halted_at = \Carbon\Carbon::now();
//        $halt->save();
//
//        if (!$calledFromModelEvent) {
//            $ticket->last_estd_duedate = ($ticket->duedate) ? $ticket->duedate : $ticket->last_estd_duedate;
//            $ticket->duedate = null;
//            $ticket->save();
//            return $old_due;
//        }
//        return [$old_due, null];
//    }

    protected function ticket() {
        if($this->ticketData){
            return $this->ticketData;
        }
        $tickets = new \App\Model\helpdesk\Ticket\Tickets();
        $ticket = $tickets->find($this->ticket_id);
        return $ticket;
    }

//    protected function changeTicket($due = null) {
//        $ticket = $this->ticket();
//        if ($ticket) {
//            $ticket->duedate = $due->tz('UTC');
//            $ticket->save();
//        }
//    }

//    public function changeStatus($statusid, $calledFromModelEvent = false) {
//        $status = new \App\Http\Controllers\Common\TicketStatus($statusid);
//        if ($status->isHalt()) {
//            return $this->createHalt($calledFromModelEvent);
//        } elseif ($this->wasHalt()) {
//            return $this->recalculateDue($calledFromModelEvent);
//        }
//        return $this->ticket()->duedate;
//    }

//    public function wasHalt() {
//        $ticket = $this->ticket();
//        if ($ticket) {
//            $status = new \App\Http\Controllers\Common\TicketStatus($ticket->status);
//            $check = false;
//            if ($status->isHalt()) {
//                $check = true;
//            }
//            return $check;
//        }
//    }

//    public function recalculateDue($calledFromModelEvent) {
//        $ticket = $this->ticket();
//        $halts = new Halt();
//        $halt = $halts->where('ticket_id', $ticket->id)->orderBy('id','desc')->first();
//        if ($halt) {
//            $this->updateLastHalted($halt);
//        }
//        $total = $ticket->halt()->sum('halted_time');
//        $ticket_created = $ticket->created_at;
//        $slaid = $ticket->sla;
//
//        $apply = $this->applySla();
//        $apply->requester_id = $ticket->user_id;
//        $apply->dept = $ticket->dept_id;
//        $sla = $apply->sla($slaid);
//        if ($ticket->firstResponseIsDone()) {
//            $time = $sla->resolveTime();
//        } else {
//            $time = $sla->respondTime();
//        }
//            $resolve = $time + $total;
//            $due = $apply->slaResolveDue($slaid, $ticket_created,$resolve,$ticket);
//            if(!$calledFromModelEvent) {
//                $this->changeTicket($due);
//            }
//        return $due->tz('UTC');
//    }
    
//     public function haltedTime($halted_at){
//        $start = $halted_at;//\Carbon\Carbon::now()->subDay()->tz(timezone());
//        //echo "Halted at : ".$start."<br>";
//        $end = \Carbon\Carbon::now();//\Carbon\Carbon::now()->tz(timezone());
//        //echo "Unhalted at : ".$end."<br>";
//
//        $apply = new \App\Http\Controllers\SLA\ApplySla();
//        $apply->requester_id = $this->ticket()->user_id;
//        $apply->dept = $this->ticket()->dept_id;
//        $time  = $apply->businessTime($start, $end, $this->ticket()->sla);
//        return  $time;
//    }


//    public function handleIfTicketISHalted($isAnswered =1, $slaid = 0)
//    {
//        $ticket = $this->ticket();
//        $total = $ticket->halt()->sum('halted_time');
//        $slaid = ($slaid != 0) ? $slaid :$ticket->sla;
//        $apply = $this->applySla();
//        $apply->requester_id = $this->ticket()->user_id;
//        $apply->dept = $this->ticket()->dept_id;
//        $sla = $apply->sla($slaid);
//        if ($isAnswered == 1) {
//            $time = $sla->resolveTime();
//        } else {
//            $time = $sla->respondTime();
//        }
//        $resolve = $time + $total;
//        return $resolve;
//    }

//    /**
//     * fucntion to update halted time in last record of halt
//     *
//     *
//     */
//    protected function updateLastHalted($halt)
//    {
//        dump('getting called haltcontroller updateLastHalted');
//
//        $haltedTime = $this->haltedTime($halt->halted_at);
//        $halt->halted_time = $haltedTime;
//        $halt->halted_at = \Carbon\Carbon::now();
//        $halt->save();
//        return true;
//    }
}
