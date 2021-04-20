<?php

namespace App\Model\helpdesk\Workflow;

use App\BaseModel;

class WorkflowClose extends BaseModel
{
    protected $table = 'workflow_close';
    protected $fillable = ['id', 'days', 'condition', 'send_email', 'status', 'updated_at', 'created_at'];

    public function ticketStatus()
    {
    	return $this->hasMany('App\Model\helpdesk\Ticket\Ticket_Status', 'auto_close');
    }

    public function tickets()
    {
    	return $this->hasManyThrough(
    		'App\Model\helpdesk\Ticket\Tickets',
    		'App\Model\helpdesk\Ticket\Ticket_Status',
    		'auto_close',
    		'status',
    		'id',
    		'id'
    	);
    }

    public function closeStatus()
    {
        return $this->belongsTo('App\Model\helpdesk\Ticket\Ticket_Status', 'status');
    }
}
