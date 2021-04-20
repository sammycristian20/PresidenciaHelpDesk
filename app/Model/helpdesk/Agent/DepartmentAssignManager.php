<?php
namespace App\Model\helpdesk\Agent;

use App\BaseModel;

class DepartmentAssignManager extends BaseModel
{
    protected $table = 'department_assign_manager';
    protected $fillable = [
        'id', 'department_id', 'manager_id', 'created_at', 'updated_at',
        
    ];
    
    
}
