<?php

namespace App\Model\helpdesk\Manage\Sla;

use App\BaseModel;
class Sla_plan extends BaseModel
{
    protected $table = 'sla_plan';
    protected $fillable = [
        'name',  'admin_note', 'status', 'transient', 'ticket_overdue','apply_sla_orgdepts','apply_sla_labels','apply_sla_tags', 'order','is_default','sla_target'
    ];
    
    public function approachOld(){
        $related = 'App\Model\helpdesk\Manage\Sla\SlaApproaches';
        $foreignKey = 'sla_plan';
        return $this->hasOne($related, $foreignKey);
    }
    
    public function violatedOld(){
        $related = 'App\Model\helpdesk\Manage\Sla\SlaViolated';
        $foreignKey = 'sla_plan';
        return $this->hasOne($related, $foreignKey);
    }
    
    public function approach(){
        $related = 'App\Model\helpdesk\Manage\Sla\SlaApproachEscalate';
        $foreignKey = 'sla_plan';
        return $this->hasMany($related, $foreignKey);
    }
    public function violated(){
        $related = 'App\Model\helpdesk\Manage\Sla\SlaViolatedEscalate';
        $foreignKey = 'sla_plan';
        return $this->hasMany($related, $foreignKey);
    }
    
    public function notAssign(){
        $related = 'App\Model\helpdesk\Manage\Sla\NoAssignEscalate';
        $foreignKey = 'sla_plan';
        return $this->hasOne($related, $foreignKey);
    }
    
    public function target(){
        $related = 'App\Model\helpdesk\Manage\Sla\SlaTargets';
        $foreignKey = 'sla_target';
        return $this->belongsTo($related, $foreignKey);
    }
    
    public function tickets(){
        $related = 'App\Model\helpdesk\Ticket\Tickets';
        $foreignKey = 'sla';
        return $this->hasMany($related, $foreignKey);
    }
    
    public function getApplySlaTickettypeAttribute($value){
        //dd($value);
        if($value && is_string($value)){
            return explode(',', $value);
        }
        return $value;
    }
    
    public function getApplySlaDepertmentAttribute($value){
        if($value && is_string($value)){
            return explode(',', $value);
        }
        return $value;
    }
    
    public function getApplySlaTicketsourceAttribute($value){
        //dd($value);
        if($value && is_string($value)){
            return explode(',', $value);
        }
        return $value;
    }
    
    public function getApplySlaCompanyAttribute($value){
        if($value && is_string($value)){
            return explode(',', $value);
        }
        return $value;
    }
    public function getApplySlaHelptopicAttribute($value){
        if($value && is_string($value)){
            return explode(',', $value);
        }
        return $value;
    }
    public function getApplySlaOrgdeptsAttribute($value){
        if($value && is_string($value)){
            return explode(',', $value);
        }
        return $value;
    }
    public function getApplySlaLabelsAttribute($value){
        if($value && is_string($value)){
            return explode(',', $value);
        }
        return $value;
    }
    public function getApplySlaTagsAttribute($value){
        if($value && is_string($value)){
            return explode(',', $value);
        }
        return $value;
    }

    /**
     *
     *
     *
     */
    public function customSLAEnforcements()
    {    
        return $this->hasMany('App\Model\helpdesk\Manage\Sla\SlaCustomEnforcements', 'sla_id');
    }
}
