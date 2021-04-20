<?php

namespace App\Model\helpdesk\Ticket;

use App\BaseModel;

class TicketStatusType extends BaseModel
{
    protected $table = 'ticket_status_type';
    protected $fillable = [
        'id', 'name', 'created_at', 'updated_at'
    ];
    
    public function status(){
        return $this->hasMany('App\Model\helpdesk\Ticket\Ticket_Status','purpose_of_status');
    }

    public function allowedOverrideThroughStatus() {
    	return $this->hasManyThrough(
    		'App\Model\helpdesk\Ticket\StatusOverride',
    		'App\Model\helpdesk\Ticket\Ticket_Status',
    		'purpose_of_status',
    		'current_status',
    		'id',
    		'id'
    	);
    }
}