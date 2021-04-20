<?php

namespace App\Model\helpdesk\Agent;

use App\BaseModel;

class TeamsDepartment extends BaseModel
{
    protected $table = 'team_assign_department';
    protected $fillable = [
        'id',  'team_id', 'dept_id', 'created_at','updated_at',
    ];
    
   
}
