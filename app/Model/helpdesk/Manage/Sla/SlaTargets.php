<?php

namespace App\Model\helpdesk\Manage\Sla;

use Illuminate\Database\Eloquent\Model;

class SlaTargets extends Model
{
   	protected $table = 'sla_targets';
   
    protected $fillable = ['id', 'name', 'sla_id', 'priority_id', 'respond_within', 'resolve_within', 'business_hour_id', 'send_email', 'send_sms', 'created_at', 'updated_at'];
    
    public function businessHour(){
        $related = 'App\Model\helpdesk\Manage\Sla\BusinessHours';
        $foreignKey = 'business_hour_id';
        return $this->belongsTo($related, $foreignKey);
    }
    
    public function priority(){
        $related = "App\Model\helpdesk\Ticket\Ticket_Priority";
        return $this->belongsTo($related,'priority_id');
    }
}
