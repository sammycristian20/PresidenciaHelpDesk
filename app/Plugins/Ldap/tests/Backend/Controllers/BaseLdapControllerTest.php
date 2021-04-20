<?php

namespace App\Plugins\Ldap\tests\Backend\Controllers;

use DB;
use App\User;
use Tests\AddOnTestCase;
use App\Plugins\Ldap\Model\Ldap;
use App\Location\Models\Location;
use Illuminate\Support\Collection;
use App\Model\helpdesk\Agent\Department;
use App\Plugins\Ldap\Model\LdapSearchBase;
use App\Plugins\Ldap\Model\LdapAdAttribute;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\Ldap\Controllers\BaseLdapController;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Agent_panel\User_org as UserOrganizationPivot;

class BaseLdapControllerTest extends AddOnTestCase
{
    private $ldap;

    private $ldapConfig;

    public function setUp():void
    {
        parent::setUp();

        $this->ldap = new BaseLdapController;

        $this->ldapConfig = Ldap::updateOrCreate(['domain'=>'test domain']);

        $this->setPrivateProperty($this->ldap, 'ldapConfig', $this->ldapConfig);
    }

  /**
   * Mocks LdapConnector class
   * @param  array $methodNamesWithValues  associative array of methodNames and values []
   * @return null
   */
    private function mockLdapConnector(array $methodNamesWithValues)
    {
        return $this->mockDependency('App\Plugins\Ldap\Controllers\LdapConnector', $methodNamesWithValues);
    }

    /** @group getThirdPartyAttributeByFaveoAttribute */
    public function test_getThirdPartyAttributeByFaveoAttribute_forExistingFaveoAttribute()
    {
            $ldapAdAttribute = LdapAdAttribute::create(['name'=>'ad_test_name', 'ldap_id'=> $this->ldapConfig->id]);

            $ldapFaveoAttribute = LdapFaveoAttribute::create(['name'=>'faveo_test_name','mapped_to'=>$ldapAdAttribute->id, 'ldap_id'=> $this->ldapConfig->id]);

            $response = $this->getPrivateMethod($this->ldap, 'getThirdPartyAttributeByFaveoAttribute', ['faveo_test_name']);

            $this->assertEquals($response, 'ad_test_name');
    }

    /** @group getThirdPartyAttributeByFaveoAttribute */
    public function test_getThirdPartyAttributeByFaveoAttribute_forNonExistingFaveoAttribute()
    {
            $response = $this->getPrivateMethod($this->ldap, 'getThirdPartyAttributeByFaveoAttribute', ['wrong_name']);

            $this->assertEquals($response, '');
    }

    /** @group isOverwriteAllowed */
    public function test_isOverwriteAllowed_forNonAllowedAttribute()
    {
        LdapFaveoAttribute::create(['overwrite'=>false, 'name'=>'test_name', 'ldap_id'=>$this->ldapConfig->id]);

        $response = $this->getPrivateMethod($this->ldap, 'isOverwriteAllowed', ['test_name']);

        $this->assertFalse($response);
    }

    /** @group isOverwriteAllowed */
    public function test_isOverwriteAllowed_forAllowedAttribute()
    {
        LdapFaveoAttribute::create(['overwrite'=>true, 'name'=>'test_name', 'ldap_id'=>$this->ldapConfig->id]);

        $response = $this->getPrivateMethod($this->ldap, 'isOverwriteAllowed', ['test_name']);

        $this->assertTrue($response);
    }

    /** @group isOverwriteAllowed */
    public function test_isOverwriteAllowed_forNonExistingAttribute()
    {
        $response = $this->getPrivateMethod($this->ldap, 'isOverwriteAllowed', ['test_name']);

        $this->assertFalse($response);
    }

    /** @group getAttributeValue */
    public function test_getAttributeValue_whenFaveoAttributeIsDepartmentAndAdAttributeIsFaveoDefault()
    {
            //should create department and return its id as array
            //create a faveo attribute which has an ad_attribute of `FAVEO DEFAULT`
            $this->createLdapAttribute('department', true);

            $searchBase = LdapSearchBase::create(['department_ids'=>[1,2], 'ldap_id'=>$this->ldapConfig->id]);

            $this->setPrivateProperty($this->ldap, 'searchBase', $searchBase);

            $response = $this->getPrivateMethod($this->ldap, 'getAttributeValue', ['department','']);

            $this->assertEquals($response, [1,2]);
    }

    /** @group getAttributeValue */
    public function test_getAttributeValue_whenFaveoAttributeIsDepartmentAndAdAttributeIsNonFaveoDefault()
    {
            $this->createLdapAttribute('department', false);

            $ldapConnectorMock = $this->mockLdapConnector(['getAttribute'=>'test_department']);

            $this->setPrivateProperty($this->ldap, 'ldapConnector', $ldapConnectorMock);

            $response = $this->getPrivateMethod($this->ldap, 'getAttributeValue', ['department','']);

            $testDepartment = Department::where('name', 'test_department')->get();

            $this->assertEquals($testDepartment->count(), 1);

            $this->assertEquals($response, [$testDepartment[0]->id]);
    }

    /** @group getAttributeValue */
    public function test_getAttributeValue_whenFaveoAttributeIsOrganizationAndAdAttributeIsFaveoDefault()
    {
            //should create organization and return its id as array
            //create a faveo attribute which has an ad_attribute of `FAVEO DEFAULT`
            $this->createLdapAttribute('organization', true);

            $searchBase = LdapSearchBase::create(['organization_ids'=>[1,2], 'ldap_id'=>$this->ldapConfig->id]);

            $this->setPrivateProperty($this->ldap, 'searchBase', $searchBase);

            $response = $this->getPrivateMethod($this->ldap, 'getAttributeValue', ['organization','']);

            $this->assertEquals($response, [1,2]);
    }

    /** @group getAttributeValue */
    public function test_getAttributeValue_whenFaveoAttributeIsOrganizationAndAdAttributeIsNonFaveoDefault()
    {

            $this->createLdapAttribute('organization', false);

            $ldapConnectorMock = $this->mockLdapConnector(['getAttribute'=>'test_organization']);

            $this->setPrivateProperty($this->ldap, 'ldapConnector', $ldapConnectorMock);

            $response = $this->getPrivateMethod($this->ldap, 'getAttributeValue', ['organization','']);

            $testDepartment = Organization::where('name', 'test_organization')->get();

            $this->assertEquals($testDepartment->count(), 1);

            $this->assertEquals($response, [$testDepartment[0]->id]);
    }

    /** @group getAttributeValue */
    public function test_getAttributeValue_whenFaveoAttributeIsRoleAndAdAttributeIsFaveoDefault()
    {
        $this->createLdapAttribute('role', true);

        $searchBase = LdapSearchBase::create(['user_type'=>'admin', 'ldap_id'=>$this->ldapConfig->id]);

        $this->setPrivateProperty($this->ldap, 'searchBase', $searchBase);

        $response = $this->getPrivateMethod($this->ldap, 'getAttributeValue', ['role','']);

        $this->assertEquals($response, 'admin');
    }

    /** @group getAttributeValue */
    public function test_getAttributeValue_whenFaveoAttributeIsRoleAndAdAttributeIsGivesNotFaveoDefaultAndValueIsAnInvalidUserType()
    {
        $this->createLdapAttribute('role');

        $ldapConnectorMock = $this->mockLdapConnector(['getAttribute'=>'invalid_role']);

        $this->setPrivateProperty($this->ldap, 'ldapConnector', $ldapConnectorMock);

        $searchBase = LdapSearchBase::create(['user_type'=>'admin', 'ldap_id'=>$this->ldapConfig->id]);

        $this->setPrivateProperty($this->ldap, 'searchBase', $searchBase);

        $response = $this->getPrivateMethod($this->ldap, 'getAttributeValue', ['role','']);

        $this->assertEquals($response, 'user');
    }

    /** @group getAttributeValue */
    public function test_getAttributeValue_whenFaveoAttributeIsRoleAndAdAttributeIsGivesNotFaveoDefaultAndValueIsValidUserType()
    {
        $this->createLdapAttribute('role');

        $ldapConnectorMock = $this->mockLdapConnector(['getAttribute'=>'agent']);

        $this->setPrivateProperty($this->ldap, 'ldapConnector', $ldapConnectorMock);

        $searchBase = LdapSearchBase::create(['user_type'=>'admin', 'ldap_id'=>$this->ldapConfig->id]);

        $this->setPrivateProperty($this->ldap, 'searchBase', $searchBase);

        $response = $this->getPrivateMethod($this->ldap, 'getAttributeValue', ['role','']);

        $this->assertEquals($response, 'agent');
    }

    private function createLdapAttribute($faveoAttributeName, $isDefault = false)
    {
        //should create department and return its id as array
        //create a faveo attribute which has an ad_attribute of `FAVEO DEFAULT`
        $name = $isDefault ? 'FAVEO DEFAULT' : 'test_name';
        $adId = LdapAdAttribute::updateOrCreate(['name'=> $name], ['name'=> $name, 'ldap_id'=>$this->ldapConfig->id])->id;
        return LdapFaveoAttribute::updateOrCreate(
            ['name'=> $faveoAttributeName],
            ['name'=>$faveoAttributeName,'mapped_to'=>$adId, 'ldap_id'=>$this->ldapConfig->id]
        );
    }

    /** @group importBySearchBasis */
    public function test_importBySearchBasis_forReturningUsersWithoutBreaking()
    {
            $ldapSearchBase = LdapSearchBase::create(['ldap_id'=>Ldap::first()->id,'search_base'=>'']);

            $count = 0;

            //returns zero users
            $mock = $this->mockLdapConnector(['getLdapUsers'=> new Collection]);

            $this->setPrivateProperty($this->ldap, 'ldapConnector', $mock);

            $response = $this->getPrivateMethod($this->ldap, 'importBySearchBasis', [$ldapSearchBase, &$count]);

            $this->assertCount(0, $response);
    }


        /** @group createValidUsers */
    public function test_createValidUsers_DontCreateUserIfUserNameIsNotPresentInLdapServer()
    {
            $ldapSearchBase = LdapSearchBase::create();

            $users = ['user'];

            $usersBeforeMethodCall = User::count();

            $mock = $this->mockLdapConnector(['getUserName'=> null, 'getEmail'=>'test_email', 'getGuid'=>'guid']);

            $this->setPrivateProperty($this->ldap, 'ldapConnector', $mock);

            $this->getPrivateMethod($this->ldap, 'createValidUsers', [$ldapSearchBase, $users]);

            $usersAfterMethodCall = User::count();

            $this->assertEquals(0, $usersAfterMethodCall - $usersBeforeMethodCall);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_createUserWhenUserNameIsNotNull()
    {
            $usersBeforeMethodCall = User::count();

            $this->defaultSetupAndCallForCreateValidUser();

            $usersAfterMethodCall = User::count();

            $this->assertEquals(1, $usersAfterMethodCall - $usersBeforeMethodCall);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_forDefaultFaveoAttributesAndUserRoleAsUser()
    {
        // it should have array of $ldapUserInstance. Now it should check in the database
        // it should find default attributes and create user using those attributes
        // Involvement of department is not involved yet
        $this->defaultSetupAndCallForCreateValidUser();

        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->user_name, 'username');
        $this->assertEquals($user->email, 'test@email.com');
        $this->assertEquals($user->first_name, 'First_name');
        $this->assertEquals($user->last_name, 'Last_name');
        $this->assertEquals($user->phone_number, '90000000');
        $this->assertEquals($user->import_identifier, 'import_identifier');
        $this->assertEquals($user->role, 'user');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_forDefaultFaveoAttributesAndUserRoleAsUserAndOrganizationInSearchBase()
    {
        //create an organization
        $orgId = Organization::create(['name'=>'test_organization'])->id;

        $this->defaultSetupAndCallForCreateValidUser('user', [$orgId]);

        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->organizations->count(), 1);
        $this->assertEquals($user->organizations[0]->name, 'test_organization');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_forDefaultFaveoAttributesAndUserRoleAsAgentAndNoDepartment()
    {
        $this->defaultSetupAndCallForCreateValidUser('agent');

        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->user_name, 'username');
        $this->assertEquals($user->email, 'test@email.com');
        $this->assertEquals($user->first_name, 'First_name');
        $this->assertEquals($user->last_name, 'Last_name');
        $this->assertEquals($user->phone_number, '90000000');
        $this->assertEquals($user->import_identifier, 'import_identifier');
        $this->assertEquals($user->role, 'agent');

        $defaultDepartmentId = DB::table('settings_system')->first()->department;

        $this->assertEquals($user->departments[0]->id, $defaultDepartmentId);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_forDefaultFaveoAttributesAndUserRoleAsAgentAndHaveDepartment()
    {
        $deptId = Department::create(['name'=>'test_department'])->id;

        $this->defaultSetupAndCallForCreateValidUser('agent', [], [$deptId]);

        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->departments[0]->name, 'test_department');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_forNonDefaultLdapAttributeForUsernameButDefaultFaveoOrganization()
    {
        //change username default to something else
        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;

        LdapFaveoAttribute::where('name', 'user_name')->first()->update(['mapped_to'=> $adAttributeId]);

        $this->defaultSetupAndCallForCreateValidUser('user');

        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->user_name, 'test_attribute');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_forNonDefaultLdapAttributeForUsernameButDefaultFaveoDepartment()
    {
        //change username default to something else
        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;

        LdapFaveoAttribute::where('name', 'user_name')->first()->update(['mapped_to'=> $adAttributeId]);

        $this->defaultSetupAndCallForCreateValidUser('agent');

        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->user_name, 'test_attribute');
        $defaultDepartmentId = DB::table('settings_system')->first()->department;

        $this->assertEquals($user->departments[0]->id, $defaultDepartmentId);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenOverwriteIsFalseForUsername()
    {

        $this->defaultSetupAndCallForCreateValidUser('agent');
        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->user_name, 'username');

        //change username default to something else
        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;

        LdapFaveoAttribute::where('name', 'user_name')->first()->update(['mapped_to'=> $adAttributeId, 'overwrite'=> false]);

        $this->defaultSetupAndCallForCreateValidUser('agent');

        $this->assertEquals($user->user_name, 'username');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenOverwriteIsFalseForDepartment()
    {
        $deptId = Department::create(['name'=>'test_department'])->id;

        //if overwrite is false for department, it will not touch department
        $this->defaultSetupAndCallForCreateValidUser('agent', [], [$deptId]);
        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->departments[0]->name, 'test_department');

        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;

        LdapFaveoAttribute::where('name', 'department')->first()->update(['mapped_to'=> $adAttributeId, 'overwrite'=> false]);

        $this->defaultSetupAndCallForCreateValidUser('agent');

        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->departments[0]->name, 'test_department');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenOverwriteIsTrueForDepartment()
    {
        $deptId = Department::create(['name'=>'test_department'])->id;

        //if overwrite is false for department, it will not touch department
        $this->defaultSetupAndCallForCreateValidUser('agent', [], [$deptId]);
        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->departments[0]->name, 'test_department');

        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;

        LdapFaveoAttribute::where('name', 'department')->first()->update(['mapped_to'=> $adAttributeId, 'overwrite'=> true]);

        $this->defaultSetupAndCallForCreateValidUser('agent');

        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->departments[0]->name, 'test_attribute');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenOverwriteIsFalseForOrganization()
    {
        $orgId = Organization::create(['name'=>'test_organization'])->id;

        //if overwrite is false for department, it will not touch department
        $this->defaultSetupAndCallForCreateValidUser('user', [$orgId], []);

        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->organizations[0]->name, 'test_organization');

        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;

        LdapFaveoAttribute::where('name', 'organization')->first()->update(['mapped_to'=> $adAttributeId, 'overwrite'=> false]);

        $this->defaultSetupAndCallForCreateValidUser('agent');

        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->organizations[0]->name, 'test_organization');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenOverwriteIsTrueForOrganization()
    {
        $orgId = Organization::create(['name'=>'test_organization'])->id;

        //if overwrite is false for department, it will not touch department
        $this->defaultSetupAndCallForCreateValidUser('user', [$orgId], []);

        //check if user is created with those attributes
        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->organizations[0]->name, 'test_organization');

        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;

        LdapFaveoAttribute::where('name', 'organization')->first()->update(['mapped_to'=> $adAttributeId, 'overwrite'=> true]);

        $this->defaultSetupAndCallForCreateValidUser('agent');

        $user = User::orderBy('id', 'desc')->first();

        $this->assertEquals($user->organizations[0]->name, 'test_attribute');
    }

        /** @group createValidUsers */
    public function test_createValidUsers_doesNotCreateOrgDeptWhenOrgDeptFaveoAttributeIsDefault()
    {
        $orgId = Organization::create(['name'=>'test_organization'])->id;

        $initialOrgDeptCount = OrganizationDepartment::count();
        //if overwrite is false for department, it will not touch department
        $this->defaultSetupAndCallForCreateValidUser('user', [$orgId], []);

        $finalOrgDeptCount = OrganizationDepartment::count();

        $this->assertEquals($initialOrgDeptCount, $finalOrgDeptCount);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_doesNotCreateOrgDeptIfModuleIsOff()
    {
        $orgId = Organization::create(['name'=>'test_organization'])->id;

        $initialOrgDeptCount = OrganizationDepartment::count();

        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;

        LdapFaveoAttribute::where('name', 'org_dept')->first()->update(['mapped_to'=> $adAttributeId]);

        //if overwrite is false for department, it will not touch department
        $this->defaultSetupAndCallForCreateValidUser('user', [$orgId], []);

        $finalOrgDeptCount = OrganizationDepartment::count();

        $this->assertEquals($initialOrgDeptCount, $finalOrgDeptCount);
    }


        /** @group createValidUsers */
    public function test_createValidUsers_createOrgDeptIfOrgDeptFaveoAttributeIsNonDefaultAndOrgDeptModuleIsOn()
    {
        $this->activateOrgDeptModule();

        $orgId = Organization::create(['name'=>'test_organization'])->id;
        $initialOrgDeptCount = OrganizationDepartment::count();

        $adAttributeId = LdapAdAttribute::create(['name'=>'test_ldap_attribute'])->id;
        $adAttributeIdForOrg = LdapAdAttribute::create(['name'=>'test_ldap_attribute_2'])->id;

        LdapFaveoAttribute::where('name', 'org_dept')->first()->update(['mapped_to'=> $adAttributeId]);
        LdapFaveoAttribute::where('name', 'organization')->first()->update(['mapped_to'=> $adAttributeIdForOrg]);

        //if overwrite is false for department, it will not touch department
        $this->defaultSetupAndCallForCreateValidUser('user', [$orgId], []);

        $finalOrgDeptCount = OrganizationDepartment::count();

        $this->assertEquals($finalOrgDeptCount - $initialOrgDeptCount, 1);

        $orgDept = OrganizationDepartment::orderBy('id', 'desc')->first();

        $this->assertEquals($orgDept->org_deptname, 'test_attribute');

        $user = User::orderBy('id', 'desc')->first();

        //check organization and user assigned to that organization
        $orgDeptUserPivot = UserOrganizationPivot::orderBy('id', 'desc')->first();
        $this->assertEquals($user->id, $orgDeptUserPivot->user_id);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenUserIsDeactiveInFaveoAndGettingImportedFromLdapItShouldRemainDeactive()
    {
            // user should remain deactive but its properties should update
            // `username` is the mock username with with LDAP will return user
            $user = factory(User::class)->create(['active' => 0, 'user_name'=>'username']);

            $usersBeforeMethodCall = User::count();

            $this->defaultSetupAndCallForCreateValidUser();

            $usersAfterMethodCall = User::count();

            $this->assertEquals(0, $usersAfterMethodCall - $usersBeforeMethodCall);

            $isUserActive = (bool)User::whereId($user->id)->value('active');

            $this->assertFalse($isUserActive);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenUserIsActiveInFaveoAndGettingImportedFromLdapItShouldRemainActive()
    {
            // user should be active after import if initially it is active
            // `username` is the mock username with with LDAP will return user
            $user = factory(User::class)->create(['active' => 1, 'user_name'=>'username']);

            $usersBeforeMethodCall = User::count();

            $this->defaultSetupAndCallForCreateValidUser();

            $usersAfterMethodCall = User::count();

            $this->assertEquals(0, $usersAfterMethodCall - $usersBeforeMethodCall);

            $isUserActive = (bool)User::whereId($user->id)->value('active');

            $this->assertTrue($isUserActive);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenNewUserGetsImportedItsStatusShouldBeActive()
    {
            $usersBeforeMethodCall = User::count();

            $this->defaultSetupAndCallForCreateValidUser();

            $usersAfterMethodCall = User::count();

            $this->assertEquals(1, $usersAfterMethodCall - $usersBeforeMethodCall);

            $isUserActive = (bool)User::where('user_name', 'username')->value('active');

            $this->assertTrue($isUserActive);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenIsDepartmentManagerOverwriteIsTrueAndDepartmentOverwriteIsTrueAndUserGettingUpdated_shouldUpdateDepartmentAndMakeAgentAsDepartmentManager()
    {
            // creating a user with username as `username`, so that the user can be considered as
            // a case of update instead of create
            $users = [factory(User::class)->create(['user_name'=>'username', 'role'=>'agent'])];

            // making department overwrite and is_department_manager overwite as true
            LdapFaveoAttribute::where('name', 'department')
                ->orWhere('name', 'is_department_manager')
                ->update(['overwrite'=>1]);

            $this->setUpForIsDepartmentManager([1], $users);

            $user = User::latest()->first();
            $this->assertEquals(1, $user->departments->count());
            $this->assertEquals(1, $user->managerOfDepartments->count());
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenIsDepartmentManagerOverwriteIsFalseAndDepartmentOverwriteIsTrueAndUserGettingUpdatedAndDepartmentIsSameAsUserCurrentDepartment_shouldMakeUserDepartmentManager()
    {
            // creating a user with username as `username`, so that the user can be considered as
            // a case of update instead of create
            $users = [factory(User::class)->create(['user_name'=>'username', 'role'=>'agent'])];

            $this->assignAgentToDepartment($users[0], [1]);

            // making department overwrite and is_department_manager overwite as true
            LdapFaveoAttribute::where('name', 'is_department_manager')
                ->update(['overwrite'=>1]);

            $this->setUpForIsDepartmentManager([1], $users);

            $user = User::latest()->first();
            $this->assertEquals(1, $user->departments->count());
            $this->assertEquals(1, $user->managerOfDepartments->count());
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenIsDepartmentManagerOverwriteIsFalseAndDepartmentOverwriteIsTrueAndUserGettingUpdatedAndDepartmentIsNotSameAsUserCurrentDepartment_shouldNotMakeUserDepartmentManager()
    {
            // creating a user with username as `username`, so that the user can be considered as
            // a case of update instead of create
            $users = [factory(User::class)->create(['user_name'=>'username', 'role'=>'agent'])];

            $this->assignAgentToDepartment($users[0], [1]);

            // making department overwrite and is_department_manager overwite as true
            LdapFaveoAttribute::where('name', 'is_department_manager')
                ->update(['overwrite'=>1]);

            $this->setUpForIsDepartmentManager([2], $users);

            $user = User::latest()->first();
            $this->assertEquals(1, $user->departments->count());
            $this->assertEquals(0, $user->managerOfDepartments->count());
    }


        /** @group createValidUsers */
    public function test_createValidUsers_whenIsOrganisationManagerOverwriteIsTrueAndOrganisationOverwriteIsTrueAndUserGettingUpdated_shouldUpdateOrganisationAndMakeUserOrganisationManager()
    {
            // creating a user with username as `username`, so that the user can be considered as
            // a case of update instead of create
            $users = [factory(User::class)->create(['user_name'=>'username', 'role'=>'user'])];

            // making department overwrite and is_department_manager overwite as true
            LdapFaveoAttribute::where('name', 'organization')
                ->orWhere('name', 'is_organization_manager')
                ->update(['overwrite'=>1]);

            $this->createOrganization();

            $this->setUpForIsOrganisationManager([$this->organization->id], $users);

            $user = User::latest()->first();
            $this->assertEquals(1, $user->organizations->count());
            $isOrganisationManager = DB::table('user_assign_organization')->where('user_id', $user->id)
        ->where('org_id', $this->organization->id)->where('role', 'manager')->exists();

            $this->assertTrue($isOrganisationManager);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenIsOrganisationManagerOverwriteIsFalseAndOrganisationOverwriteIsTrueAndUserGettingUpdatedAndOrganisationIsSameAsUserCurrentDepartment_shouldMakeUserOrganisationManager()
    {
            // creating a user with username as `username`, so that the user can be considered as
            // a case of update instead of create
            $users = [factory(User::class)->create(['user_name'=>'username', 'role'=>'user'])];

            $this->createOrganization();

            $this->assignUserWithOrganization($this->organization->id, $users[0]);

            // making department overwrite and is_department_manager overwite as true
            LdapFaveoAttribute::where('name', 'is_organization_manager')
                ->update(['overwrite'=>1]);

            $this->setUpForIsOrganisationManager([$this->organization->id], $users);

            $user = User::latest()->first();
            $this->assertEquals(1, $user->organizations->count());

            $isOrganisationManager = DB::table('user_assign_organization')->where('user_id', $user->id)
        ->where('org_id', $this->organization->id)->where('role', 'manager')->exists();

            $this->assertTrue($isOrganisationManager);
    }

        /** @group createValidUsers */
    public function test_createValidUsers_whenIsOrganisationManagerOverwriteIsFalseAndOrganisationOverwriteIsTrueAndUserGettingUpdatedAndOrganisationIsNotSameAsUserCurrentOrganisation_shouldNotMakeUserOrganisationManager()
    {
            // creating a user with username as `username`, so that the user can be considered as
            // a case of update instead of create
            $users = [factory(User::class)->create(['user_name'=>'username', 'role'=>'user'])];

            $this->createOrganization();

            $this->assignUserWithOrganization($this->organization->id, $users[0]);


            // making department overwrite and is_department_manager overwite as true
            LdapFaveoAttribute::where('name', 'is_organization_manager')
                ->update(['overwrite'=>1]);

            // creating another organization
            $this->createOrganization();

            $this->setUpForIsOrganisationManager([$this->organization->id], $users);

            $user = User::latest()->first();
            $this->assertEquals(1, $user->organizations->count());
            $isOrganisationManager = DB::table('user_assign_organization')->where('user_id', $user->id)
                ->where('org_id', $this->organization->id)->where('role', 'manager')->exists();

            $this->assertFalse($isOrganisationManager);
    }

        /**
         * Sets up required data for IsDepartmentManager test cases
         * @param array $departmentIds
         * @param array $users
         * @return void
         */
    private function setUpForIsDepartmentManager(array $departmentIds = [], array $users = [])
    {
        $ldapSearchBase = LdapSearchBase::create(['ldap_id'=>$this->ldapConfig->id,'user_type'=>'user','department_ids'=> $departmentIds]);

        // making is_department_manager (getAttribute method) return true
        $mock = $this->mockLdapConnector(['getUserName'=> 'username', 'getEmail'=>'test@email.com',
            'getFirstName'=>'first_name','getLastName'=>'last_name','getPhoneNumber'=>'phone_number',
            'getGuid'=>'import_identifier', 'getAttribute'=> true]);

        // making is_department_manager attribute to non-default so that it can be mocked to true
        LdapFaveoAttribute::where('name', 'is_department_manager')
            ->update(['mapped_to'=>10]);

        $this->setPrivateProperty($this->ldap, 'ldapConnector', $mock);

        $count = 0;

        $this->getPrivateMethod($this->ldap, 'createValidUsers', [$ldapSearchBase, $users]);
    }

        /**
         * Sets up required data for IsDepartmentManager test cases
         * @param array $departmentIds
         * @param array $users
         * @return void
         */
    private function setUpForIsOrganisationManager(array $organizationIds = [], array $users = [])
    {
        $ldapSearchBase = LdapSearchBase::create(['ldap_id'=>$this->ldapConfig->id,'user_type'=>'agent','organization_ids'=> $organizationIds, 'department_ids'=> []]);

        // making is_department_manager (getAttribute method) return true
        $mock = $this->mockLdapConnector(['getUserName'=> 'username', 'getEmail'=>'test@email.com',
            'getFirstName'=>'first_name','getLastName'=>'last_name','getPhoneNumber'=>'phone_number',
            'getGuid'=>'import_identifier', 'getAttribute'=> true]);

        // making is_organization_manager attribute to non-default so that it can be mocked to true
        LdapFaveoAttribute::where('name', 'is_organization_manager')
            ->update(['mapped_to'=>10]);


        $this->setPrivateProperty($this->ldap, 'ldapConnector', $mock);

        $count = 0;

        $this->getPrivateMethod($this->ldap, 'createValidUsers', [$ldapSearchBase, $users]);
    }

        /**
         * Activates organization department module
         * @return CommonSettings
         */
    private function activateOrgDeptModule()
    {
        return  CommonSettings::create(['option_name' => 'micro_organization_status','status' => 1]);
    }

        /**
         * Makes call to createValidUsers method with required parameters after mocking ldapConnector
         * @param  string $userType
         * @param  array  $orgIds
         * @param  array  $deptIds
         * @return null
         */
    private function defaultSetupAndCallForCreateValidUser($userType = 'user', $orgIds = [], $deptIds = [])
    {

        $users = ['user1'];

        $ldapSearchBase = LdapSearchBase::create(['ldap_id'=>$this->ldapConfig->id,'user_type'=>$userType,'organization_ids'=>$orgIds, 'department_ids'=>$deptIds]);

        $count = 0;

        $mock = $this->mockLdapConnector(['getUserName'=> 'username', 'getEmail'=>'test@email.com',
            'getFirstName'=>'first_name','getLastName'=>'last_name','getPhoneNumber'=>'90000000',
            'getGuid'=>'import_identifier', 'getAttribute'=>'test_attribute', 'getUserLocation' => 'London']);

        $this->setPrivateProperty($this->ldap, 'ldapConnector', $mock);

        $this->getPrivateMethod($this->ldap, 'createValidUsers', [$ldapSearchBase, $users]);
    }

    /** @group createValidUsers */
    public function test_createValidUsers_alsoMapsLocation()
    {
        $usersBeforeMethodCall = User::count();

        $this->defaultSetupAndCallForCreateValidUser();

        $usersAfterMethodCall = User::count();

        $this->assertEquals(1, $usersAfterMethodCall - $usersBeforeMethodCall);

        $userLocation = User::where('user_name', 'username')->value('location');

        $this->assertTrue((bool)$userLocation);

        //Below `London` is set from mock method
        $this->assertEquals('London', Location::where('id', $userLocation)->value('title'));
    }
}
