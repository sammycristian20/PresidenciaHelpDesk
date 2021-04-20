<?php

namespace App\Model\helpdesk\Ticket;

use App\BaseModel;
use App\Model\helpdesk\Ticket\TicketStatusType;

class Ticket_Status extends BaseModel
{
    protected $table    = 'ticket_status';
    protected $fillable = [

        'id', 'name', 'state', 'message', 'mode', 'flag', 'sort', 'properties', 'icon_class','send_email',
        'visibility_for_client', 'allow_client', 'visibility_for_agent', 'purpose_of_status', 'assigned_agent_team', 'icon', 'order', 'icon_color', 'send_sms', 'default', 'auto_close', 'comment', 'halt_sla', 'secondary_status'
    ];

    public function type()
    {
        return $this->belongsTo('App\Model\helpdesk\Ticket\TicketStatusType', 'purpose_of_status');
    }

    public function getSendEmailAttribute($value)
    {
        if ($value) {
            $value = json_decode($value, true);
        }
        return $value;
    }

    public function allowedTargetStatuses()
    {
        return $this->hasMany('App\Model\helpdesk\Ticket\StatusOverride', 'target_status');
    }

    public function overrideStatuses()
    {
        return $this->hasMany('App\Model\helpdesk\Ticket\StatusOverride', 'current_status');
    }

    /**
     * Defines one to one relationship between Ticket_Status and WorkflowClose
     * ticket_status may have one related record in workflo_close table
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    public function workflowClose()
    {
        return $this->hasOne('App\Model\helpdesk\Workflow\WorkflowClose', 'status');
    }

    /**
     * relationship with ticket
     */
    public function tickets()
    {
        return $this->hasMany(Tickets::class, 'status');
    }

    /**
     * relation for secondary statuses linked with one status to be displayed
     * Eg: 2 statuses => Waiting, Late
     * 'Waiting' status 'visibility_for_client' attribute is set to 'No' or 0 then status to display field could be set to 'Open'
     * 'Late' status 'visibility_for_client' attribute is set to 'No' or 0 then status to display field could be set to 'Open'
     * then in client panel => 'Open' status need to include Waiting' and 'Late' statuses as alternative statuses 
     * alternative statuses value will be used for combining 'Open', 'Waiting' and 'Late' statuses tickets as 'Open' in client panel
     */
    public function alternativeStatus()
    {
        return $this->hasMany(Ticket_Status::class, 'secondary_status');
    }

    /**
     * relation for secondary status, alternative status name to be displayed
     * Eg: 'Waiting' status 'visibility_for_client' attribute is set to 'No' or 0 then status to display field could be set to 'Open'
     * then for knowing the secondary status of 'Waiting' this relation could be used, it will be used in Client Panel only, as of now
     */
    public function secondaryStatus()
    {
        return $this->belongsTo(Ticket_Status::class, 'secondary_status');
    }

}
