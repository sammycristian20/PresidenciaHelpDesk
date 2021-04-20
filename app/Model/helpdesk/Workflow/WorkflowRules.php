<?php

namespace App\Model\helpdesk\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowRules extends Model
{
    public $timestamps = false;
    protected $table = 'workflow_rules';
    protected $fillable = ['workflow_id', 'matching_criteria', 'matching_scenario', 'matching_relation', 'matching_value','custom_rule'];
    
    public function workflow(){
        return $this->belongsTo('App\Model\helpdesk\Workflow\WorkflowName','workflow_id');
    }
    
     public function setCustomRuleAttribute($value){
        if($value){
            $value = json_encode($value);
        }
        $this->attributes['custom_rule'] = $value;
    }
}
