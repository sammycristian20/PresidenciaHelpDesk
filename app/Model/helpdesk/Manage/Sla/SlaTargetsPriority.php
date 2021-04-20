<?php

namespace App\Model\helpdesk\Manage\Sla;

use Illuminate\Database\Eloquent\Model;

class SlaTargetsPriority extends Model
{
   	protected $table = 'sla_targets';
   
    protected $fillable = ['id', 'sla_id', 'sla_target_priority_id', 'respond_within', 'resolve_within', 'business_hour_id', 'send_email','send_sms', 'created_at', 'updated_at'];
}
