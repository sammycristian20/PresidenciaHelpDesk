<?php

namespace App\Model\helpdesk\Ticket;

use App\BaseModel;

class StatusOverride extends BaseModel
{
    protected $table = 'ticket_status_override';
    protected $fillable = [
        'id', 'current_status', 'target_status', 'created_at', 'updated_at'
    ];
    
    public function fromStatus(){
        return $this->belongsTo('App\Model\helpdesk\Ticket\Ticket_Status', 'current_status');
    }
    
    public function toStatus(){
        return $this->belongsTo('App\Model\helpdesk\Ticket\Ticket_Status', 'target_status');
    }
}
