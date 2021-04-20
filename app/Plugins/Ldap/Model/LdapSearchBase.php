<?php

namespace App\Plugins\Ldap\Model;

use Illuminate\Database\Eloquent\Model;
use Crypt;
use DB;

class LdapSearchBase extends Model {

    protected $table = 'ldap_search_bases';

    protected $fillable = ['ldap_id','search_base', 'filter', 'user_type', 'department_ids','organization_ids'];

    /**
     * receives values as array and convert that into comma separated string
     * @param  array $departmentId  array of departmentIds
     * @return  string 				comma seperated department ids
     */
    public function setDepartmentIdsAttribute(array $departmentIds)
    {
    	$this->attributes['department_ids'] = implode($departmentIds, ',');
    }

    /**
     * recieves value as string and returns back array of values
     * @param  string $departmentIds  comma seperated departmentIds
     * @return array 		array of department ids
     */
    public function getDepartmentIdsAttribute(string $departmentIds)
    {
        //should return default deparment in the array
        if(!$departmentIds){
            $defaultDepartmentId = DB::table('settings_system')->first()->department;
            //get default department
            return [$defaultDepartmentId];
        }

    	return explode(',',$departmentIds);
    }


    /**
     * receives values as array and convert that into comma separated string
     * @param  array $departmentId  array of departmentIds
     * @return  string              comma seperated department ids
     */
    public function setOrganizationIdsAttribute(array $organizationIds)
    {
        $this->attributes['organization_ids'] = implode($organizationIds, ',');
    }

    /**
     * recieves value as string and returns back array of values
     * @param  string $departmentIds  comma seperated departmentIds
     * @return array        array of department ids
     */
    public function getOrganizationIdsAttribute(string $organizationIds)
    {
        if(!$organizationIds){

            return [];
        }
        return explode(',',$organizationIds);
    }
}
