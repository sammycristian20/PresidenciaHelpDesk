<?php

namespace App\Model\helpdesk\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowName extends Model
{
    protected $table = 'workflow_name';
    protected $fillable = ['id', 'name', 'status', 'order', 'target', 'internal_note', 'updated_at', 'created_at', 'rule_match'];
    
    public function rule(){
        return $this->hasMany('App\Model\helpdesk\Workflow\WorkflowRules','workflow_id');
    }
    
    public function action(){
        return $this->hasMany('App\Model\helpdesk\Workflow\WorkflowAction','workflow_id');
    }
    
    public function targets(){
        return $this->belongsTo('App\Model\helpdesk\Ticket\Ticket_source', 'target');
    }
}
