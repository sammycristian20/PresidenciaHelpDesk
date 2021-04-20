<?php

namespace App\Model\helpdesk\Agent_panel;

use App\BaseModel;

class User_org extends BaseModel {
    /* define table name  */

    protected $table = 'user_assign_organization';

    /* define fillable fields */

    protected $fillable = ['id', 'org_id', 'user_id', 'role','org_department'];


    public function setOrgIdAttribute($value) {
        if ($value == "") {
            $this->attributes['org_id'] = null;
        } else {
            $this->attributes['org_id'] = $value;
        }
    }

    public function user() {
        $related = "App\User";
        return $this->belongsTo($related, 'user_id');
    }

    public function organisation() {
        $related = "App\Model\helpdesk\Agent_panel\Organization";
        return $this->hasOne($related, 'id','org_id');
    }

    public function tickets() {
        $related = "App\Model\helpdesk\Ticket\Tickets";
        return $this->hasMany($related, 'user_id', 'user_id');
    }

     public function orgDepartment() {
        $related = "App\Model\helpdesk\Agent_panel\OrganizationDepartment";
        return $this->hasMany($related, 'id','org_department');
    }

}
