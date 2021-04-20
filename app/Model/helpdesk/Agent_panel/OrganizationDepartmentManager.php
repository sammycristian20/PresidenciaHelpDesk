<?php

namespace App\Model\helpdesk\Agent_panel;

use App\BaseModel;

class OrganizationDepartmentManager extends BaseModel
{
    /* define the table name */

    protected $table = 'org_dept_maneger';

    /* Define the fillable fields */
    protected $fillable = ['id', 'org_dept_id','org_dept_manager_id'];

    /**
     * This relationship is for users who belongs to any organization as a manager
     *
     */
    public function managers()
    {
        return $this->belongsToMany('App\User', 'organization_dept_manager', 'org_dept_id', 'org_dept_manager_id')->where([
           
            ['is_delete', 0],
            ['active', 1]
        ]);
    }
}