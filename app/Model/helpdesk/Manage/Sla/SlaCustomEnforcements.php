<?php

namespace App\Model\helpdesk\Manage\Sla;

use Illuminate\Database\Eloquent\Model;

class SlaCustomEnforcements extends Model
{
    protected $table = 'sla_custom_enforcements';
   
    protected $fillable = ['id', 'f_name', 'f_type', 'f_value', 'f_label', 'sla_id', 'created_at', 'updated_at'];
}
