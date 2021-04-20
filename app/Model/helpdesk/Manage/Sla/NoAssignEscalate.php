<?php

namespace App\Model\helpdesk\Manage\Sla;

use Illuminate\Database\Eloquent\Model;

class NoAssignEscalate extends Model {

    protected $table = 'no_assign_escalate';
    protected $fillable = ['id', 'sla_plan', 'escalate_time', 'escalate_type', 'escalate_person', 'created_at', 'updated_at'];

    public function getEscalatePersonAttribute($value) {
        return explode(',', $value);
    }

    public function setEscalatePersonAttribute($value) {
        if ($value) {
            $this->attributes['escalate_person'] = str_replace(" ", '_', $value);
        }
    }

}
