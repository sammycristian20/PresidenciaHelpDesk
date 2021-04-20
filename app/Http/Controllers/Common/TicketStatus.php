<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;

use App\Model\helpdesk\Ticket\Ticket_Status;

class TicketStatus extends Controller
{
    protected $name;
    protected $status;
    
    public function __construct($id) {
        $ticketstatus = new Ticket_Status();
        $this->status = $ticketstatus->find($id);
        $this->name = $this->name();
    }
    
    public function name(){
        if($this->status){
            return $this->status->name;
        }
    }
    
    public function isVisibleForUser(){
        $check = false;
        if($this->status && $this->status->visibility_for_client==1){
            $check = true;
        }
        return $check;
    }
    
    public function isVisibleForAgent(){
        $check = false;
        if($this->status && $this->status->visibility_for_agent==1){
            $check = true;
        }
        return $check;
    }
    
    public function purpose(){
        if($this->status){
            return $this->status->purpose;
        }
    }
    
    public function isHalt(){
        $check = false;
        if($this->status && $this->status->halt_sla==1){
            $check = true;
        }
        return $check;
    }
    
    public function isDefault(){
        $check = false;
        if($this->status && $this->status->default==1){
            $check = true;
        }
        return $check;
    }
}
