<?php

namespace App\Model\helpdesk\Manage\Sla;

use Illuminate\Database\Eloquent\Model;

class SlaViolatedEscalate extends Model {

    protected $table = 'sla_violated_escalate';
    protected $fillable = ['id', 'sla_plan', 'escalate_time', 'escalate_type', 'escalate_person', 'created_at', 'updated_at'];

    public function getEscalatePersonAttribute($value)
    {
        return explode(',', $value);

    }

    public function setEscalatePersonAttribute($value)
    {
        if(is_array($value)){
            $this->attributes['escalate_person'] = implode(',', $value);
        }
    }

}
