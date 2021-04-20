<?php

namespace App\Plugins\Ldap\tests\Backend\Traits;

use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Agent_panel\Organization;
use Tests\AddOnTestCase;
use App\Plugins\Ldap\Traits\UserDependencyHandler;
use App\User;
use App\Model\helpdesk\Email\Emails as Email;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use DB;

class UserDependencyHandlerTest extends AddOnTestCase
{
    use UserDependencyHandler;

    /** @group createDepartment */
    public function test_createDepartment_forNewDepartment()
    {
      $initialDeptCount = Department::count();

      $response = $this->createOrUpdateDepartment('dept_test_name');

      $createdDeptId = Department::orderBy('id','desc')->first()->id;

      $this->assertEquals($response, $createdDeptId);

      $finalDeptCount = Department::count();

      $this->assertEquals($finalDeptCount - $initialDeptCount, 1);
    }

    /** @group createDepartment */
    public function test_createDepartment_forExistingDepartment()
    {
      $department = Department::create(['name'=>'dept_test_name']);;

      $initialDeptCount = Department::count();

      $response = $this->createOrUpdateDepartment('dept_test_name');

      $createdDeptId = Department::orderBy('id','desc')->first()->id;

      $this->assertEquals($response, $createdDeptId);

      $finalDeptCount = Department::count();

      $this->assertEquals($finalDeptCount, $initialDeptCount);
    }

    /** @group createOrganization */
    public function test_createOrganization_forNewOrganization()
    {
      $initialOrgCount = Organization::count();

      $response = $this->createOrUpdateOrganization('org_test_name');

      $createdOrgId = Organization::orderBy('id','desc')->first()->id;

      $this->assertEquals($response, $createdOrgId);

      $finalOrgCount = Organization::count();

      $this->assertEquals($finalOrgCount - $initialOrgCount, 1);
    }

    /** @group createOrganization */
    public function test_createOrganization_forExistingDepartment()
    {
      Organization::create(['name'=>'org_test_name']);;

      $initialOrgCount = Organization::count();

      $response = $this->createOrUpdateOrganization('org_test_name');

      $createdOrgId = Organization::orderBy('id','desc')->first()->id;

      $this->assertEquals($response, $createdOrgId);

      $finalOrgCount = Organization::count();

      $this->assertEquals($finalOrgCount, $initialOrgCount);
    }

    /** @group createOrganizationDepartment */
    public function test_createOrganizationDepartment_forNewOrgDepartment()
    {
      $orgId = Organization::create(['name'=>'org_test_name'])->id;;

      $initialOrgDeptCount = OrganizationDepartment::count();

      $response = $this->createOrUpdateOrgDept($orgId,'org_dept_test_name');

      $orgId = OrganizationDepartment::orderBy('id','desc')->first()->id;

      $this->assertEquals($orgId, $response);

      $finalOrgDeptCount = OrganizationDepartment::count();

      $this->assertEquals($finalOrgDeptCount - $initialOrgDeptCount, 1);
    }

    /** @group createOrganizationDepartment */
    public function test_createOrganizationDepartment_forExistingOrgDepartment()
    {
      $orgId = Organization::create(['name'=>'org_test_name'])->id;;

      $orgDeptId = OrganizationDepartment::create(['org_deptname'=>'org_dept_test_name','org_id'=>$orgId])->id;

      $initialOrgDeptCount = OrganizationDepartment::count();

      $response = $this->createOrUpdateOrgDept($orgId,'org_dept_test_name');

      $this->assertEquals($orgDeptId, $response);

      $finalOrgDeptCount = OrganizationDepartment::count();

      $this->assertEquals($finalOrgDeptCount, $initialOrgDeptCount);
    }

    /** @group  assignDepartment */
  	public function test_assignDepartment_forAgents()
  	{
  		$user = factory(User::class)->create(['role' => 'agent']);

  		$userId = $user->id;

  		$this->assertEquals(User::find($userId)->departments->count(),0);

      $response = $this->assignDepartment($user,[1,2]);

  		$this->assertEquals(User::find($userId)->departments->count(),2);
  	}

    /** @group  assignDepartment */
    public function test_assignDepartment_forAgentsForDefaultDepartment()
    {
      $user = factory(User::class)->create(['role' => 'agent']);

      $userId = $user->id;

      $this->assertEquals(User::find($userId)->departments->count(),0);

      $response = $this->assignDepartment($user,[]);

      $this->assertEquals(User::find($userId)->departments->count(), 1);

      $defaultDepartmentId = \DB::table('settings_system')->select('department')->first()->department;

      $this->assertEquals($defaultDepartmentId, User::find($userId)->departments[0]->id);
    }

  	/** @group  assignDepartment */
  	public function test_assignDepartment_forUsers()
  	{
  		$user = factory(User::class)->create(['role' => 'user']);

  		$userId = $user->id;

  		$this->assertEquals(User::find($userId)->departments->count(),0);

      $methodResponse = $this->assignDepartment($user,[1,2]);

  		$this->assertFalse($methodResponse);

  		$this->assertEquals(User::find($userId)->departments->count(),0);
  	}


  	/** @group assignDefaultPermissions */
  	public function test_assignDefaultPermissions_forUsers()
  	{
  		$user = factory(User::class)->create(['role' => 'agent']);

  		$userId = $user->id;

  		$this->assertEmpty(User::find($userId)->permissions()->get()->toArray());

      $response = $this->assignDefaultPermissions($user);

  		$this->assertNotEmpty(User::find($userId)->permissions()->get()->toArray());
  	}

    /** @group getUserFromDB */
    public function test_getUserFromDB_whenUserNameIsNull()
    {
      $this->assertEquals(null,$this->getUserFromDB(null, null, 'test_guid'));
    }

    /** @group getUserFromDB */
    public function test_getUserFromDB_whenUserNameIsFound()
    {
      $user = factory(User::class)->create();
      $userFromMethod = $this->getUserFromDB($user->user_name, null, 'test_guid');
      $this->assertNotEquals(null, $userFromMethod);
      $this->assertEquals($user->user_name, $userFromMethod->user_name);
    }

    /** @group getUserFromDB */
    public function test_getUserFromDB_whenUserNameIsNotFoundButGuidIsFound()
    {
      $user = factory(User::class)->create(['import_identifier'=>'test_guid']);
      $userFromMethod = $this->getUserFromDB('wrong_user_name', null, 'test_guid');
      $this->assertNotEquals(null, $userFromMethod);
      $this->assertEquals($user->user_name, $userFromMethod->user_name);
    }

    /** @group getUserFromDB */
    public function test_getUserFromDB_whenUserNameIsNotFoundButEmailIsFound()
    {
      $user = factory(User::class)->create(['email'=>'test@email.com']);
      $userFromMethod = $this->getUserFromDB('wrong_user_name', 'test@email.com', 'test_guid');
      $this->assertNotEquals(null, $userFromMethod);
      $this->assertEquals($user->user_name, $userFromMethod->user_name);
    }

    /** @group getUserFromDB */
    public function test_getUserFromDB_whenUserNameIsNotFoundButPassedEmailIsNullAndUserEmailIsAlsoNull()
    {
      $user = factory(User::class)->create(['email'=>null]);
      $userFromMethod = $this->getUserFromDB('wrong_user_name', null, 'test_guid');
      $this->assertEquals(null, $userFromMethod);
    }

    /** @group handleUserRoleUpdate */
    public function test_handleUserRoleUpdate_forAgentToUser()
    {
      $this->getLoggedInUserForWeb('agent');
      $this->assignAgentToDepartment($this->user);
      $this->createNewPermissionsAndAssignToLoggedInUser([['key' => 'test_permission', 'name' => 'Test Permission']]);
      $this->assertNotEquals(0, $this->user->departments()->count());
      $this->assertNotEquals(0, $this->user->permissions()->count());
      $this->user->role = 'user';
      $this->handleUserRoleUpdate($this->user);
      $this->assertEquals(0, $this->user->departments()->count());
      $this->assertEquals(0, $this->user->permissions()->count());
    }

    /** @group handleUserRoleUpdate */
    public function test_handleUserRoleUpdate_forUserToAgent()
    {
      $this->getLoggedInUserForWeb('user');
      $this->createOrganization();
      $this->assignUserWithOrganization($this->organization->id, $this->user);
      $this->assertNotEquals(0, $this->user->organizations()->count());
      $this->assertEquals(0, $this->user->departments()->count());
      $this->assertEquals(0, $this->user->permissions()->count());
      $this->user->role = 'agent';
      $this->handleUserRoleUpdate($this->user);
      $this->assertNotEquals(0, $this->user->departments()->count());
      $this->assertNotEquals(0, $this->user->permissions()->count());
      $this->assertEquals(0, $this->user->organizations()->count());
    }

    /** @group isAllowedToCreatedUser */
    public function test_isAllowedToCreatedUser_whenUsernameIsNull()
    {
      $isAllowedToCreateUser = $this->isAllowedToCreatedUser(null, null, 'import_identifier');
      $this->assertFalse($isAllowedToCreateUser);
    }

    /** @group isAllowedToCreatedUser */
    public function test_isAllowedToCreatedUser_whenLdapUniqueKeyIsNull()
    {
      $isAllowedToCreateUser = $this->isAllowedToCreatedUser('username', null, null);
      $this->assertFalse($isAllowedToCreateUser);
    }

    /** @group isAllowedToCreatedUser */
    public function test_isAllowedToCreatedUser_whenEmailIsNull()
    {
      $isAllowedToCreateUser = $this->isAllowedToCreatedUser('test_username', null, 'import_identifier');
      $this->assertTrue($isAllowedToCreateUser);
    }

    /** @group isAllowedToCreatedUser */
    public function test_isAllowedToCreatedUser_whenEmailIsAlreadyTakenBySystemConfiguredMail()
    {
      Email::create(['email_address'=>'test@email.com']);
      $isAllowedToCreateUser = $this->isAllowedToCreatedUser('test_username', 'test@email.com','import_identifier');
      $this->assertFalse($isAllowedToCreateUser);
    }

    /** @group makeDepartmentManager */
    public function test_makeDepartmentManager_whenAgentIsDepartmentManagerButDepartmentIdIsPassedAsEmpty_shouldRemoveAgentFromAllDepartmentManagerPost()
    {
      $user = factory(User::class)->create(['role' => 'agent']);

      DepartmentAssignManager::create(['manager_id'=>$user->id, 'department_id'=>1]);

      $this->makeDepartmentManager($user, [], true);

      $isDepartmentManager = DepartmentAssignManager::where('manager_id', $user->id)
        ->where('department_id', 1)->exists();

      $this->assertFalse($isDepartmentManager);
    }

    /** @group makeDepartmentManager */
    public function test_makeDepartmentManager_whenAgentIsDepartmentManagerOfADepartmentAndAnotherDepartmentIsPassed_shouldSyncPassedDepartmentWithAgent()
    {
      $user = factory(User::class)->create(['role' => 'agent']);

      // making manager of department 1
      DepartmentAssignManager::create(['manager_id'=>$user->id, 'department_id'=>1]);

      // making part of department 2
      DepartmentAssignAgents::create(['agent_id'=>$user->id, 'department_id'=>2]);

      $this->makeDepartmentManager($user, [2], true);

      $isDepartmentManager = DepartmentAssignManager::where('manager_id', $user->id)
        ->where('department_id', 2)->exists();

      $this->assertTrue($isDepartmentManager);
    }

    /** @group makeDepartmentManager */
    public function test_makeDepartmentManager_whenAgentIsNotAPartOfPassedDepartment_shouldNotMakeHimDepartmentManager()
    {
      $user = factory(User::class)->create(['role' => 'agent']);

      DepartmentAssignManager::create(['manager_id'=>$user->id, 'department_id'=>1]);

      $this->makeDepartmentManager($user, [2], true);

      $isDepartmentManager = DepartmentAssignManager::where('manager_id', $user->id)
        ->where('department_id', 2)->exists();

      $this->assertFalse($isDepartmentManager);
    }

    /** @group makeDepartmentManager */
    public function test_makeDepartmentManager_whenAgentIsADepartmentManagerButIsManagerIsPassedAsFalse_shouldRemoveAgentFromManager()
    {
      $user = factory(User::class)->create(['role' => 'agent']);

      DepartmentAssignManager::create(['manager_id'=>$user->id, 'department_id'=>1]);
      DepartmentAssignAgents::create(['agent_id'=>$user->id, 'department_id'=>1]);

      $this->makeDepartmentManager($user, [1], false);

      $isDepartmentManager = DepartmentAssignManager::where('manager_id', $user->id)
        ->where('department_id', 1)->exists();

      $this->assertFalse($isDepartmentManager);

      $isDepartmentMember = DepartmentAssignAgents::where('agent_id', $user->id)
        ->where('department_id', 1)->exists();

      $this->assertTrue($isDepartmentMember);
    }

    /** @group makeOrganizationManager */
    public function test_makeOrganizationManager_whenUserIsOrgManagerButOrganisationIdIsPassedAsEmpty_shouldRemoveUserFromAllOrganisationManagerPost()
    {
      $user = factory(User::class)->create(['role' => 'user']);

      $this->createOrganization();

      DB::table('user_assign_organization')->insert(['user_id'=>$user->id,
        'org_id'=> $this->organization->id, 'role'=>'manager']);

      $this->makeOrganizationManager($user, [], true);

      $isOrganizationManager = DB::table('user_assign_organization')->where('user_id', $user->id)
        ->where('org_id', $this->organization->id)->exists();

      $this->assertFalse($isOrganizationManager);
    }

    /** @group makeOrganizationManager */
    public function test_makeOrganizationManager_whenUserIsOrganisationManagerOfAnOrganisationAndAnotherOrganisationIsPassed_shouldSyncPassedOrganisationWithUser()
    {
      $user = factory(User::class)->create(['role' => 'user']);

      $this->createOrganization();

      DB::table('user_assign_organization')->insert(['user_id'=>$user->id,
        'org_id'=> $this->organization->id, 'role'=>'manager']);

      // creating another organization
      $this->createOrganization();

      // linking the user again
      DB::table('user_assign_organization')->insert(['user_id'=>$user->id,
        'org_id'=> $this->organization->id, 'role'=>'manager']);

      $this->makeOrganizationManager($user, [$this->organization->id], true);

      $isOrganizationManager = DB::table('user_assign_organization')->where('user_id', $user->id)
        ->where('org_id', $this->organization->id)->where('role','manager')->exists();

      $this->assertTrue($isOrganizationManager);
    }

    /** @group makeOrganizationManager */
    public function test_makeOrganizationManager_whenAgentIsNotAPartOfPassedOrganisation_shouldNotMakeHimOrganisationManager()
    {
      $user = factory(User::class)->create(['role' => 'user']);

      // just creating organization but not linking user with it
      $this->createOrganization();

      $this->makeOrganizationManager($user, [$this->organization->id], true);

      $isOrganizationManager = DB::table('user_assign_organization')->where('user_id', $user->id)
        ->where('org_id', $this->organization->id)->where('role','manager')->exists();

      $this->assertFalse($isOrganizationManager);
    }

    /** @group makeOrganizationManager */
    public function test_makeOrganizationManager_whenUserIsOrganisationManagerButIsManagerIsFalse_shouldRemoveUserFromManager()
    {
      $user = factory(User::class)->create(['role' => 'user']);

      // just creating organization but not linking user with it
      $this->createOrganization();

      // making user organization manager
      DB::table('user_assign_organization')->insert(['user_id'=>$user->id,
        'org_id'=> $this->organization->id, 'role'=>'manager']);

      $this->makeOrganizationManager($user, [$this->organization->id], false);

      $isOrganizationManager = DB::table('user_assign_organization')->where('user_id', $user->id)
        ->where('org_id', $this->organization->id)->where('role','manager')->exists();

      $this->assertFalse($isOrganizationManager);

      $isOrganisationMember = DB::table('user_assign_organization')->where('user_id', $user->id)
        ->where('org_id', $this->organization->id)->where('role','members')->exists();

      $this->assertTrue($isOrganisationMember);
    }
}
