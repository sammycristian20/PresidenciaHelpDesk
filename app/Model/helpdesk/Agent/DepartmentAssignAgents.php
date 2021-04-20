<?php

namespace App\Model\helpdesk\Agent;

use App\BaseModel;

class DepartmentAssignAgents extends BaseModel
{
    protected $table = 'department_assign_agents';
    protected $fillable = [ 
        'id', 'department_id', 'agent_id', 'created_at', 'updated_at',
        
    ];
    
    public function agent(){
        $related = 'App\User';
        return $this->hasMany($related,'id','agent_id');
    }
    
    public function tickets(){
        $related = 'App\Model\helpdesk\Ticket\Tickets';
        return $this->hasMany($related,'dept_id','department_id');
    }
}
