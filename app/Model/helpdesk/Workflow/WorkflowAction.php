<?php

namespace App\Model\helpdesk\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowAction extends Model
{
    public $timestamps = false;
    protected $table = 'workflow_action';
    protected $fillable = ['workflow_id','condition', 'action','custom_action'];
    
    public function setCustomActionAttribute($value){
        if($value){
            $value = json_encode($value);
        }
        $this->attributes['custom_action'] = $value;
    }
}
