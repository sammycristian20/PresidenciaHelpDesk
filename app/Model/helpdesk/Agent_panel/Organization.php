<?php

namespace App\Model\helpdesk\Agent_panel;

use App\BaseModel;

class Organization extends BaseModel
{
    /* define the table name */

    protected $table = 'organization';

    /* Define the fillable fields */
    protected $fillable = ['id', 'name', 'phone', 'website', 'address', 'head', 'internal_notes','domain','department','logo','logo_driver'];
    
    
    public function userRelation(){
        $related = "App\Model\helpdesk\Agent_panel\User_org";
        return $this->hasMany($related,'org_id');
    }
    
    public function getUserIds(){
        $user_relations = $this->userRelation()->pluck('user_id')->toArray();
        return $user_relations;
    }

    /**
     * Relationship to fetch active users linked to the organisation
     * Note: Created a seperate relationship to fetch only those users who are active
     * in the system which should be queried by default.
     */
    public function activeUsers() {
        return $this->users()->where([['active','=',1],['is_delete', '=',0]]);
    }

    /**
     * Relationship to fetch users linked to the organisation
     * Note:  but just to keep options open for future having a distinct relation
     * for all and only active users would help developer to query intended result.
     */
    public function users() {
        return $this->belongsToMany('App\User','user_assign_organization', 'org_id', 'user_id');
    }

    public function extraField(){
        return $this->hasMany('App\Model\helpdesk\Agent_panel\ExtraOrg','org_id');
    }
    
    public function orgDept(){
        return $this->hasMany('App\Model\helpdesk\Agent_panel\OrganizationDepartment','org_id');
    }
    
    public function delete() {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->userRelation()->delete();
        parent::delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return true;
    }

    public function customFieldValues()
    {
        return $this->morphMany('App\Model\helpdesk\Form\CustomFormValue', 'custom');
    }
    
    /**
     * This relationship is for users who belongs to any organization as a manager
     *
     */
    public function managers()
    {
        return $this->belongsToMany('App\User', 'user_assign_organization', 'org_id', 'user_id')->where([
            ['user_assign_organization.role', 'manager'],
            ['is_delete', 0],
            ['active', 1]
        ]);
    }

    /**
     * This relationship is for users who belongs to an organization as a member
     *
     */
    public function members()
    {
        return $this->belongsToMany('App\User', 'user_assign_organization', 'org_id', 'user_id')->where([
            ['user_assign_organization.role', 'members'],
            ['is_delete', 0],
            ['active', 1]
        ]);
    }
}
