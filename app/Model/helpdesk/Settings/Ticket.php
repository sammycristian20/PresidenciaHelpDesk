<?php

namespace App\Model\helpdesk\Settings;

use App\BaseModel;

class Ticket extends BaseModel
{
    /* Using Ticket table  */

    protected $table = 'settings_ticket';
    /* Set fillable fields in table */
    protected $fillable = [
        'id', 'num_format', 'num_sequence', 'priority', 'sla', 'help_topic', 'max_open_ticket', 'collision_avoid', 'lock_ticket_frequency',
        'captcha', 'status', 'claim_response', 'assigned_ticket', 'answered_ticket', 'agent_mask', 'html', 'client_update', 'max_file_size','count_internal','show_status_date','show_org_details'
        ,'custom_field_name','waiting_time', 'ticket_number_prefix', 'show_user_location'
    ];

    /**
     * converts custom field name into array
     */
    public function getCustomFieldNameAttribute($value)
    {
    	if($value){
	    	return explode(',',$value);
    	}
    	return [];
    }

    public function getTicketNumberPrefixAttribute($value)
    {
        if(!$value){
            return 'HDSK';
        }
        return $value;
    }

    // relation with status
    public function status()
    {
        return $this->hasOne(\App\Model\helpdesk\Ticket\Ticket_Status::class, 'id', 'status');
    }
}
