<?php

namespace App\Model\helpdesk\Agent_panel;

use App\BaseModel;

class DepartmentCannedResponse extends BaseModel
{
    /* define the table name */

    protected $table = 'department_canned_resposne';

    /* Define the fillable fields */
    protected $fillable = ['id', 'dept_id', 'canned_id', 'created_at', 'updated_at'];
}
