<?php

namespace App\Model\helpdesk\Agent_panel;

use App\BaseModel;

class OrganizationDepartment extends BaseModel
{
    /* define the table name */

    protected $table = 'organization_dept';

    /* Define the fillable fields */
    protected $fillable = ['id', 'org_id', 'org_deptname', 'business_hours_id','org_dept_manager','created_at', 'updated_at'];

    public function businessHour(){
        $related = 'App\Model\helpdesk\Manage\Sla\BusinessHours';
        return $this->belongsTo($related,'business_hours_id');
    }

    /**
     * This relationship is for users who belongs to any organization department as a manager
     *
     */
    public function manager()
    {
        return $this->belongsTo('App\User', 'org_dept_manager')->where([
            ['is_delete', 0],
            ['active', 1]
        ]);
    }
}
