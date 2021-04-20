<?php

namespace App\Plugins\Ldap\Traits;

use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\User;
use DB;
use App\Model\helpdesk\Agent_panel\User_org as UserOrganizationPivot;
use App\Model\helpdesk\Email\Emails as Email;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Agent\UserPermission;

/**
 * Contains base methods for handling faveo user dependencies
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
trait UserDependencyHandler
{

  /**
  * Assigns department to the given agent
  * @param  User $user
  * @param  array $departmentIds
  * @return boolean
  */
  private function assignDepartment(User $user, array $departmentIds)
  {
      if ($user->role == 'user') {
          return false;
      }

      if(!$departmentIds){
        // if deparment is empty assign to default department
        $defaultDepartmentId = DB::table('settings_system')->select('department')->first()->department;
        $departmentIds = (array)$defaultDepartmentId;
      }

      // check if department is changing. It should remove department managers
      // if not, then should not
      // if current department ids is different
      // NOTE: we don't need 2 tables to store department member and
      // department manager. If it was single table, this line won't
      // be required
      DepartmentAssignManager::where('manager_id', $user->id)
        ->whereNotIn('department_id', $departmentIds)
        ->delete();

      return (boolean) $user->departments()->sync($departmentIds);
  }

  /**
   * Makes a user department manager of the departmentIds passed
   * @param  User   $user
   * @param  array  $departmentIds
   * @return boolean
   */
  private function makeDepartmentManager(User $user, array $departmentIds, bool $isManager)
  {

    // see if recieved department is something that user already belongs to, if yes,
    // then make him department manager else ignore
    // if department Ids is empty and is_department_manager overwrite is true, it should
    // remove that agent from department manager
    if(!count($departmentIds) || !$isManager){
      // removing all departments, since departmentIds as empty array, it will delete all entries
      return (boolean) $user->managerOfDepartments()->sync([]);
    }

    $doesUserBelongToDepartment = DepartmentAssignAgents::where('agent_id', $user->id)
      ->whereIn('department_id', $departmentIds)->exists();

    // if agent doesn't belong to the department, department manager syncing should not happen
    if($doesUserBelongToDepartment){
      return (boolean) $user->managerOfDepartments()->sync($departmentIds);
    }

    return false;
  }

  /**
  * Assigns organization to the passed user
  * @param  User   $user
  * @param  array  $organizationIds
  * @return boolean
  */
  private function assignOrganization(User $user, array $organizationIds)
  {
      if ($user->role != 'user') {
          return false;
      }

      // NOTE: a workaround for disabling before deleting foriegn keys
      DB::statement('SET FOREIGN_KEY_CHECKS=0');

      // adding members to each organizationIds
      // NOTE: its the pivot table where we are storing the information that user is member or manager,
      // so pivot won't be using its model so can't set role as attribute in model. The only workaround is
      // to update it directly in the query
      $formattedOrgIds = [];
      foreach ($organizationIds as $index => $id) {
        $formattedOrgIds[$id] = ['role' => 'members'];
      }
      $user->organizations()->sync($formattedOrgIds);
  }

  /**
   * Makes a user organization manager of the organizationIds passed
   * @param  User   $user
   * @param  array  $departmentIds
   * @return boolean
   */
  private function makeOrganizationManager(User $user, array $organizationIds, bool $isManager)
  {
    // see if recieved organization is something that user already belongs to, if yes,
    // then make him organization manager else ignore
    // if organization Ids is empty and is_organization_manager overwrite is true, it should
    // remove that agent from organization manager
    if(!count($organizationIds)){
      // removing all organizations, since organizationIds as empty array, it will delete all entries
      // make user a member if he is a manager
      return (bool) $user->organizations()->sync([]);
    }

    if(!$isManager){
      // if not organization manager in LDAP but organization manager in faveo, make him member
      return (bool) $user->organizations()->sync($this->getFormattedOrganizationIdsByRole($organizationIds, 'members'));
    }

    // first check if user belongs to that organization or not. If not, abort
    $doesUserBelongToOrganization = DB::table('user_assign_organization')->where('user_id', $user->id)
      ->whereIn('org_id', $organizationIds)->exists();

    // if agent doesn't belong to the organization, organization manager syncing should not happen
    if($doesUserBelongToOrganization){
      return (bool) $user->organizations()->sync($this->getFormattedOrganizationIdsByRole($organizationIds, 'manager'));
    }

    return false;
  }

  /**
   * Gets formatted organization ids by role
   * for eg. when we have to update user to make him member, we need extra arguments
   * [id => 'role'=>'manager']
   * @param  array $organizationIds
   * @param  string $role 'members' or 'manager'
   * @return array
   */
  private function getFormattedOrganizationIdsByRole(array $organizationIds ,string $role)
  {
    $formattedOrgIds = [];
    foreach ($organizationIds as $id) {
      $formattedOrgIds[$id] = ['role' => $role];
    }
    return $formattedOrgIds;
  }

  /**
   * Assigns current user to given organization department
   * @param  User   $user
   * @param  int $orgDeptId
   * @param int $organizationId
   * @return boolean
   */
  private function assignOrgDept(User $user, int $orgDeptId, int $organizationId)
  {
      if($user->role != 'user'){
        return false;
      }

      //NOTE: it is a workaround for current system to work, as organization_id ideally
      //      should not be stored in connecting table but just organization_dept table
      //      this could be achieved
      return UserOrganizationPivot::updateOrCreate(['user_id' => $user->id],
        ['user_id' => $user->id, 'org_id' => $organizationId, 'org_department' => $orgDeptId ] );
  }

  /**
  * assigns default permission to the given agent
  * @param  User $user
  * @return null
  */
  private function assignDefaultPermissions(User $user)
  {
      if ($user->role != 'agent') {
          return false;
      }

      $defaultPermissionIds = UserPermission::whereIn('key', ['create_ticket', 'edit_ticket', 'close_ticket', 'transfer_ticket', 'delete_ticket', 'assign_ticket', 'access_kb', 'ban_email', 'organisation_document_upload', 'account_activate', 'report', 'agent_account_activate'])->pluck('id')->toArray();

      //update permission table
      return $user->permissions()->sync($defaultPermissionIds);
  }

  /**
  * Checks if the passed user_name and email is valid for creating a new user
  * NOTE: any condition can be added here to filter out the users who are invalid, so even if
  * this method has very less logic, don't remove it
  * @return Boolean
  */
  private function isAllowedToCreatedUser(string $username = null, string $email = null, $uniqueKey = null ) : bool
  {
      if (!$username || !$uniqueKey) {
          return false;
      }

      //check if email is already there as system configured mail
      if($email && Email::where('email_address', $email)->first()){
        return false;
      }

      return true;
  }


    /**
     * Creates or update organization
     * @param  string $organizationName
     * @return int    id of the created/updated organization
     */
    private function createOrUpdateOrganization($organizationName)
    {
        if ($organizationName) {
            return Organization::updateOrCreate(['name' => $organizationName],['name' => $organizationName])->id;
        }
    }

    /**
     * Creates or update organization department
     * @param  int $organizationId  id if the parent organization
     * @param  string $orgDeptName
     * @return int    id of the created/updated organization department
     */
    private function createOrUpdateOrgDept($organizationId, $orgDeptName)
    {
        if ($orgDeptName) {
            return OrganizationDepartment::updateOrCreate(
              ['org_id'=> $organizationId, 'org_deptname'=> $orgDeptName],
              ['org_id'=> $organizationId, 'org_deptname'=> $orgDeptName]
            )->id;
        }
    }

    /**
     * Creates department based on passed name and returns the same
     * @param  string $departmentName
     * @return int    id of the created department
     */
    private function createOrUpdateDepartment($departmentName)
    {
        if($departmentName){
          return Department::updateOrCreate(['name' => $departmentName], ['name' => $departmentName, 'type'=>1])->id;
        }
    }

    /**
     * Gets user from DB if exists else null
     * @param  string $username  username mapped to Active directory
     * @param string $email      email mapped to Active directory
     * @param  string $identifier the unique one time key that cannot be changed once created
     * @return User|null
     */
    protected function getUserFromDB($username, $email, string $identifier = null)
    {
        if($username){
          return User::where('user_name', $username)
            ->orWhere(function($q) use($identifier){
              $q->where('import_identifier',$identifier)->where('import_identifier','!=', null);
            })->orWhere(function($q) use($email){
              $q->where('email', $email)->where('email','!=', null);
            })->first();
        }
    }

    /**
     * Perform all operations that are needed when user role is changed
     * For eg, if role is getting changed from agent -> user,
     * all thing should be cleaned related to department,
     * When role is changed from agent->user, default permission and deparment should be given
     * @param  User   $user user object
     * @return null
     */
      protected function handleUserRoleUpdate(User $user)
      {
        $initialRole = isset($user->getOriginal()['role']) ? $user->getOriginal()['role'] : $user->role;

        $finalRole = $user->role;

        if($this->isChangingFromAgentToUser($initialRole, $finalRole)){
          // remove all permissions
          $user->permissions()->detach();

          // detach all departments
          $user->departments()->detach();

          // detaching all department managers
          $user->managerOfDepartments()->detach();
        }

        if($this->isChangingFromUserToAgent($initialRole, $finalRole)){
          // detach all organizations
          $user->organizations()->detach();

          // give default permissions
          $this->assignDefaultPermissions($user);

          // give default department
          // giving empty value causes it to assign default department
          $this->assignDepartment($user, []);
        }
    }

    /**
     * Checks if role is changing from agent/admin to user
     * @param  string  $initialRole   role before changing
     * @param  string  $finalRole     role after changing
     * @return boolean
     */
    private function isChangingFromAgentToUser(string $initialRole, string $finalRole) : bool
    {
      return (in_array($initialRole, ['admin','agent']) && $finalRole == 'user');
    }

    /**
     * Checks if role is changing from user to  agent/admin
     * @param  string  $initialRole   role before changing
     * @param  string  $finalRole     role after changing
     * @return boolean
     */
    private function isChangingFromUserToAgent(string $initialRole, string $finalRole) : bool
    {
      return ($initialRole == 'user' && in_array($finalRole, ['admin','agent']));
    }
}
