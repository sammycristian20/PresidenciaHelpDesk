<?php

namespace App\Plugins\Ldap\tests\Backend\Controllers;

use App\Model\helpdesk\Settings\CommonSettings;
use App\Plugins\Ldap\Controllers\ApiLdapController;
use App\Plugins\Ldap\Controllers\LdapConnector;
use App\Plugins\Ldap\Model\Ldap;
use App\Plugins\Ldap\Model\LdapAdAttribute;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;
use App\Plugins\Ldap\Model\LdapSearchBase;
use App\User;
use Exception;
use Illuminate\Support\Collection;
use Lang;
use Tests\AddOnTestCase;

class ApiLdapControllerTest extends AddOnTestCase
{
    private $ldap;

    public function setUp(): void
    {
        parent::setUp();

        $ldapConnector = new LdapConnector;

        $this->ldap = new ApiLdapController($ldapConnector);

        Ldap::updateOrCreate(['domain' => 'test domain', 'schema'=>'active_directory']);
    }

    /** @group getLdapSettings */
    public function test_getLdapSettings_forSuccess()
    {
        $ldapId = Ldap::value('id');

        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['isValidLdapConfig' => new Exception('test_exception_message')]);

        $response = $this->call('GET', 'api/ldap/settings/'.$ldapId);

        $response->assertStatus(200);

        $hasSuccess = json_decode($response->content())->success;

        $this->assertTrue($hasSuccess);
    }

    /**
     * Mocks LdapConnector class
     * @param array $methodNamesWithValues associative array of methodNames and values []
     * @return null
     */
    private function mockLdapConnector(array $methodNamesWithValues)
    {
        return $this->mockDependency('App\Plugins\Ldap\Controllers\LdapConnector', $methodNamesWithValues);
    }

    /** @group getLdapSettings */
    public function test_getLdapSettings_whenSettingsAreInvalid_shouldShowTheSameInResponseMessage()
    {
        Ldap::first()->update(['is_valid' => 0]);

        $ldapId = Ldap::first()->id;

        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['isValidLdapConfig' => new Exception('test_exception_message'), 'isLdapExtensionEnabled' => true]);

        $response = $this->call('GET', "api/ldap/settings/$ldapId");

        $response->assertStatus(200);

        $message = json_decode($response->content())->message;

        $this->assertEquals(Lang::get('Ldap::lang.please_configure_ldap'), $message);
    }

    /** @group getLdapSettings */
    public function test_getLdapSettings_whenLdapExtensionIsDisabled_shouldShowTheSameInResponseMessage()
    {
        Ldap::first()->update(['is_valid' => 1]);

        $ldapId = Ldap::first()->id;

        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['isValidLdapConfig' => new Exception('test_exception_message'), 'isLdapExtensionEnabled' => false]);

        $response = $this->call('GET', "api/ldap/settings/$ldapId");

        $response->assertStatus(200);

        $message = json_decode($response->content())->message;

        $this->assertEquals(Lang::get('Ldap::lang.ldap_extension_not_enabled'), $message);
    }

    /** @group authLdap */
    public function test_authLdap_whenCredentialsDoesntExistOnLdapServer()
    {
        $mock = $this->mockLdapConnector(['isValidCredentials' => false]);

        $validLdap2faCredential = false;

        $response = (new ApiLdapController($mock))->authLdap(['email' => 'email', 'password' => 'password', 'ldap' => false, 'ldap_id'=> Ldap::value('id')], $validLdap2faCredential);
        $this->assertFalse($response);
    }

    /** @group authLdap */
    public function test_authLdap_whenCredentialsExistsOfExistingUser()
    {
        $user = factory(User::class)->create(['ldap_id' => Ldap::value('id')]);

        $initialUserCount = User::count();

        $validLdap2faCredential = false;

        $mock = $this->mockLdapConnector(['isValidCredentials' => true]);

        $response = (new ApiLdapController($mock))->authLdap(['email' => $user->user_name, 'password' => $user->password, 'ldap' => true, 'ldap_id'=> Ldap::value('id')], $validLdap2faCredential);

        $this->assertTrue($response);

        $finalUserCount = User::count();

        $this->assertEquals(0, $finalUserCount - $initialUserCount);
    }

    /** @group authLdap */
    public function test_authLdap_whenLdapCredentialsAreWrong()
    {
        $user = factory(User::class)->create(['ldap_id' => Ldap::value('id')]);

        $mock = $this->mockLdapConnector(['isValidCredentials' => false]);

        $validLdap2faCredential = false;

        $this->expectException(Exception::class);

        (new ApiLdapController($mock))->authLdap(['email' => $user->user_name, 'password' => $user->password, 'ldap' => true, 'ldap_id'=> Ldap::value('id')], $validLdap2faCredential);
    }

    /** @group authLdap */
    public function test_authLdap_whenCredentialsExistsOfExistingUserButUserIsInactive()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Lang::get('lang.invalid_credentials'));

        $user = factory(User::class)->create(['active' => 0, 'ldap_id' => Ldap::value('id')]);

        $initialUserCount = User::count();

        $validLdap2faCredential = false;

        $mock = $this->mockLdapConnector(['isValidCredentials' => true]);

        (new ApiLdapController($mock))->authLdap(['email' => $user->user_name, 'password' => $user->password, 'ldap' => true, 'ldap_id'=> Ldap::value('id')], $validLdap2faCredential);

        $finalUserCount = User::count();

        $this->assertEquals(0, $finalUserCount - $initialUserCount);
    }

    /** @group authLdap */
    public function test_authLdap_whenUsernameIsNotAsSameAsUsernameInTheDB()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Lang::get('lang.invalid_credentials'));

        $user = factory(User::class)->create(['active' => 0, 'ldap_id' => Ldap::value('id')]);

        $initialUserCount = User::count();

        $validLdap2faCredential = false;

        $mock = $this->mockLdapConnector(['isValidCredentials' => true]);

        (new ApiLdapController($mock))->authLdap(['email' => $user->user_name, 'password' => $user->password, 'ldap' => true, 'ldap_id'=> Ldap::value('id')], $validLdap2faCredential);

        $finalUserCount = User::count();

        $this->assertEquals(0, $finalUserCount - $initialUserCount);
    }

    /** @group postLdapSettings */
    public function test_postLdapSettings_whenLdapCredentialsAreValid()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['isValidLdapConfig' => true]);

        $response = $this->call('POST', 'api/ldap/settings', [
            'domain' => 'test_domain', 'username' => 'test_username', 'password' => 'test_password']);

        $response->assertStatus(200);

        $ldap = Ldap::orderBy('id', 'desc')->first();

        $this->assertEquals($ldap->username, 'test_username');

        $this->assertEquals($ldap->domain, 'test_domain');

        $this->assertEquals($ldap->password, 'test_password');
    }

    /** @group postLdapSettings */
    public function test_postLdapSettings_whenPortIsPassedAsEmptyString()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['isValidLdapConfig' => true]);

        $response = $this->call('POST', 'api/ldap/settings', [
            'domain' => 'test_domain', 'username' => 'test_username', 'password' => 'test_password', 'port' => '']);

        $response->assertStatus(200);

        $ldap = Ldap::first();

        $this->assertEquals((bool)$ldap->port, false);
    }

    /** @group postLdapSettings */
    public function test_postLdapSettings_whenPortIsPassedAsZero()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['isValidLdapConfig' => true]);

        $response = $this->call('POST', 'api/ldap/settings', [
            'domain' => 'test_domain', 'username' => 'test_username', 'password' => 'test_password', 'port' => 0]);

        $response->assertStatus(200);

        $ldap = Ldap::first();

        $this->assertEquals((bool)$ldap->port, false);
    }

    /** @group postLdapSettings */
    public function test_postLdapSettings_whenPortIsPassedAsNonZeroNumber()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['isValidLdapConfig' => true]);

        $response = $this->call('POST', 'api/ldap/settings', [
            'domain' => 'test_domain', 'username' => 'test_username', 'password' => 'test_password', 'port' => 44]);

        $response->assertStatus(200);

        $ldap = Ldap::orderBy('id', 'desc')->first();

        $this->assertEquals($ldap->port, 44);
    }

    /** @group postLdapSettings */
    public function test_postLdapSettings_whenLdapCredentialsAreInvalid_itDoesntSaveTheChanges()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['isValidLdapConfig' => new Exception('test_exception_message')]);

        $initialCount = Ldap::count();
        $response = $this->call('POST', 'api/ldap/settings', [
            'domain' => 'test_domain', 'username' => 'test_username', 'password' => 'test_password']);

        $response->assertStatus(400);

        $finalCount = Ldap::count();

        $this->assertEquals($finalCount, $initialCount);
    }


    /** @group postSearchBases */
    public function test_postSearchBases_withoutImport()
    {
        $this->getLoggedInUserForWeb('admin');

        $searchBases = [['id' => 0,
            'search_base' => 'search_base',
            'filter' => 'test_filter',
            'department_ids' => [1, 2],
            'user_type' => 'user',
            'organization_ids' => []
        ]];

        $this->assertEquals(LdapSearchBase::count(), 0);

        $ldapId = Ldap::value('id');

        $response = $this->call('POST', 'api/ldap/search-bases/'.$ldapId, ['search_bases' => $searchBases, 'import' => false]);

        $response->assertStatus(200);

        $this->assertEquals(LdapSearchBase::count(), 1);
        $searchBase = LdapSearchBase::first();
        $this->assertEquals($searchBase->search_base, 'search_base');
        $this->assertEquals($searchBase->filter, 'test_filter');
        $this->assertEquals($searchBase->user_type, 'user');
        $this->assertEquals($searchBase->department_ids, [1, 2]);
    }

    /** @group postSearchBases */
    public function test_postSearchBases_withImportAsTrueButWrongConfiguration()
    {
        $this->getLoggedInUserForWeb('admin');
        // Ldap::create(['domain'=>'192.168.1.162','username'=>'avinash@faveo.com','password'=>'Ladybird123*']);

        $searchBases = [['id' => 0, 'search_base' => 'search', 'department_ids' => [1, 2], 'user_type' => 'user',
            'organization_ids' => []
        ]];

        $this->assertEquals(LdapSearchBase::count(), 0);

        $this->mockLdapConnector(['getLdapUsers' => new Exception('test_exception_message')]);

        $ldapId = Ldap::value('id');

        $response = $this->call('POST', 'api/ldap/search-bases/'.$ldapId, ['search_bases' => $searchBases, 'import' => true]);

        $response->assertStatus(400);

        $this->assertEquals(LdapSearchBase::count(), 0);
    }

    /** @group postSearchBases */
    public function test_postSearchBases_withImportAsTrueAndCorrectConfiguration()
    {
        $this->getLoggedInUserForWeb('admin');

        $searchBases = [['id' => 0, 'search_base' => 'search_base', 'filter' => 'test_filter', 'department_ids' => [1, 2], 'user_type' => 'user',
            'organization_ids' => []
        ]];

        $this->assertEquals(LdapSearchBase::count(), 0);

        $this->mockLdapConnector(['getLdapUsers' => new Collection]);

        $ldapId = Ldap::value('id');

        $response = $this->call('POST', 'api/ldap/search-bases/'.$ldapId, ['search_bases' => $searchBases, 'import' => true]);

        $response->assertStatus(200);

        $this->assertEquals(LdapSearchBase::count(), 1);

        $searchBase = LdapSearchBase::first();
        $this->assertEquals($searchBase->search_base, 'search_base');
        $this->assertEquals($searchBase->filter, 'test_filter');
        $this->assertEquals($searchBase->user_type, 'user');
        $this->assertEquals($searchBase->department_ids, [1, 2]);
    }

    /** @group pingLdapWithSearchQuery */
    public function test_pingLdapWithSearchQuery_withWrongConfiguration()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('GET', 'api/ldap/search-base/ping/'.Ldap::value('id'), ['search_base' => 'wrong search base']);

        $response->assertStatus(400);
    }

    /** @group pingLdapWithSearchQuery */
    public function test_pingLdapWithSearchQuery_withCorrectConfiguration()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->mockLdapConnector(['getLdapUsers' => new Collection]);

        $response = $this->call('GET', 'api/ldap/search-base/ping', ['search_base' => 'wrong search base']);

        $response->assertStatus(200);
    }

    /** @group deleteSearchBase */
    public function test_deleteSearchBase_forInvalidId()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('delete', 'api/ldap/search-base/wrong-id');

        $response->assertStatus(400);

        $hasSuccess = json_decode($response->content())->success;

        $this->assertFalse($hasSuccess);
    }

    /** @group deleteSearchBase */
    public function test_deleteSearchBase_forValidId()
    {
        $this->getLoggedInUserForWeb('admin');

        $searchBase = LdapSearchBase::create();

        $response = $this->call('delete', 'api/ldap/search-base/' . $searchBase->id);

        $response->assertStatus(200);

        $hasSuccess = json_decode($response->content())->success;

        $this->assertTrue($hasSuccess);

        $this->assertEquals(0, LdapSearchBase::count());
    }

    /** @group importByCurrentConfiguration */
    public function test_importByCurrentConfiguration_whenLdapIsConfiguredButInvalid()
    {
        //if ldap is not configured
        LdapSearchBase::create(['ldap_id' => Ldap::first()->id]);

        $mock = $this->mockLdapConnector(['getLdapUsers' => new Exception()]);

        $response = (new ApiLdapController($mock))->importByCurrentConfiguration(Ldap::first());

        $this->assertFalse($response);
    }

    /** @group importByCurrentConfiguration */
    public function test_importByCurrentConfiguration_whenLdapIsConfiguredAndValid()
    {
        //if ldap is not configured
        LdapSearchBase::create(['ldap_id' => Ldap::first()->id]);

        $mock = $this->mockLdapConnector(['getLdapUsers' => new Collection]);

        $response = (new ApiLdapController($mock))->importByCurrentConfiguration(Ldap::first());

        $this->assertTrue($response);
    }

    /** @group postAdvancedSettings */
    public function test_postAdvancedSettings_forCreatingNewRecord()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldapId = Ldap::first()->id;
        $params = ['faveo_attributes' => [
            ['id' => null,
                'name' => 'test_name',
                'overwrite' => false,
                'mapped_to' => 1]
        ]];

        $response = $this->call('POST', "/api/ldap/advanced-settings/$ldapId", $params);

        $response->assertStatus(200);

        $faveoAttribute = LdapFaveoAttribute::orderBy('id', 'desc')->where('ldap_id', $ldapId)->first();

        $this->assertEquals($faveoAttribute->name, 'test_name');
        $this->assertEquals($faveoAttribute->overwrite, 0);
        $this->assertEquals($faveoAttribute->mapped_to, 1);
    }

    /** @group getAdvancedSettings */
    public function test_getAdvancedSettings_forSuccess()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldapId = Ldap::value('id');
        $response = $this->call('GET', "/api/ldap/advanced-settings/$ldapId");
        $response->assertStatus(200);
        $data = json_decode($response->getContent())->data;
        $this->assertTrue(isset($data->faveo_attributes));
        $this->assertTrue(isset($data->third_party_attributes));
    }

    /** @group getAdvancedSettings */
    public function test_getAdvancedSettings_whenOrgDeptModuleIsDisabled()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldapId = Ldap::value('id');
        $response = $this->call('GET', "/api/ldap/advanced-settings/$ldapId");
        $response->assertStatus(200);
        $data = json_decode($response->getContent())->data;
        $this->assertTrue(isset($data->faveo_attributes));
        $this->assertNotRegexp('/org_dept/', json_encode($data->faveo_attributes));
    }

    /** @group getAdvancedSettings */
    public function test_getAdvancedSettings_whenOrgDeptModuleIsEnabled()
    {
        $this->getLoggedInUserForWeb('admin');
        $this->activateOrgDeptModule();
        $ldapId = Ldap::value('id');
        $response = $this->call('GET', "/api/ldap/advanced-settings/$ldapId");
        $response->assertStatus(200);
        $data = json_decode($response->getContent())->data;
        $this->assertTrue(isset($data->faveo_attributes));
        $this->assertRegexp('/org_dept/', json_encode($data->faveo_attributes));
    }

    /**
     * Activates organization department module
     * @return CommonSettings
     */
    private function activateOrgDeptModule()
    {
        return CommonSettings::create(['option_name' => 'micro_organization_status', 'status' => 1]);
    }

    /** @group getDirectoryAttribute */
    public function test_getDirectoryAttribute_withSearchQuery()
    {
        $this->getLoggedInUserForWeb('admin');
        $searchQuery = 'search-me';
        $ldap = Ldap::first();
        $ldap->adAttributes()->create(['name' => $searchQuery]);
        $methodResponse = $this->call('GET', "/api/dependency/ldap-directory-attributes/$ldap->id", ['search_query' => $searchQuery]);
        $methodResponse->assertStatus(200);
        $directoryAttribute = json_decode($methodResponse->getContent())->data->data;
        $this->assertCount(1, $directoryAttribute);
        $this->assertEquals($searchQuery, $directoryAttribute[0]->name);
    }

    /** @group getDirectoryAttribute */
    public function test_getDirectoryAttribute_withLimit()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldapId = Ldap::value('id');
        $methodResponse = $this->call('GET', "/api/dependency/ldap-directory-attributes/$ldapId", ['limit' => 1]);
        $methodResponse->assertStatus(200);
        $directoryAttribute = json_decode($methodResponse->getContent())->data->data;
        $this->assertCount(1, $directoryAttribute);
    }

    /** @group getDirectoryAttribute */
    public function test_getDirectoryAttribute_withValidSortFieldAndSortOrder()
    {
        $this->getLoggedInUserForWeb('admin');
        $name = 'zzz-i-am-last-in-sort';
        $ldap = Ldap::first();
        $ldap->adAttributes()->create(['name' => 'Some Name']);
        $ldap->adAttributes()->create(['name' => $name]);
        $methodResponse = $this->call('GET', "/api/dependency/ldap-directory-attributes/$ldap->id", ['sort_field' => 'name', 'sort_order' => 'desc']);
        $methodResponse->assertStatus(200);
        $directoryAttribute = json_decode($methodResponse->getContent())->data->data;
        $this->assertCount(10, $directoryAttribute);
        $this->assertEquals($name, $directoryAttribute[0]->name);
    }

    /** @group getDirectoryAttribute */
    public function test_getDirectoryAttribute_withInvalidParameter()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldapId = Ldap::value('id');
        $methodResponse = $this->call('GET', "/api/dependency/ldap-directory-attributes/$ldapId", ['sort_field' => 'wrong_field']);
        $methodResponse->assertStatus(500);
    }

    /** @group postDirectoryAttribute */
    public function test_postDirectoryAttribute_forCreatingANewRecord()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldap = Ldap::first();
        $ldapId = $ldap->id;
        $initialCount = $ldap->adAttributes()->count();
        $response = $this->call('POST', "/api/ldap/ldap-directory-attribute/$ldapId", ['id' => 0, 'name' => 'test_name']);
        $response->assertStatus(200);
        $finalCount = $ldap->adAttributes()->count();
        $this->assertEquals($finalCount - $initialCount, 1);
    }

    /** @group postDirectoryAttribute */
    public function test_postDirectoryAttribute_forUpdatingAnExistingRecord()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldap = Ldap::first();
        $ldapId = $ldap->id;
        $adAttribute = $ldap->adAttributes()->create(['is_default' => false, 'name' => 'initial_name']);
        $initialCount = $ldap->adAttributes()->count();
        $response = $this->call('POST', "/api/ldap/ldap-directory-attribute/$ldapId", ['id' => $adAttribute->id, 'name' => 'final_name']);
        $response->assertStatus(200);
        $finalCount = $ldap->adAttributes()->count();;
        $this->assertEquals($finalCount - $initialCount, 0);
        $this->assertEquals($ldap->adAttributes()->whereId($adAttribute->id)->value('name'), 'final_name');
    }

    /** @group postDirectoryAttribute */
    public function test_postDirectoryAttribute_forADefaultAttribute()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldap = Ldap::first();
        $ldapId = $ldap->id;
        $adAttribute = $ldap->adAttributes()->first();
        $initialCount = $ldap->adAttributes()->count();
        $response = $this->call('POST', "/api/ldap/ldap-directory-attribute/$ldapId", ['id' => $adAttribute->id, 'name' => 'test_name']);
        $response->assertStatus(400);
        $finalCount = $ldap->adAttributes()->count();
        $this->assertEquals($finalCount - $initialCount, 0);
    }

    /** @group deleteDirectoryAttribute */
    public function test_deleteDirectoryAttribute_forExistingId()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldapId = Ldap::value('id');
        $adAttribute = LdapAdAttribute::create(['name' => 'test_name', 'is_default' => false, 'ldap_id'=> $ldapId]);
        $initialCount = LdapAdAttribute::where('ldap_id', $ldapId)->count();
        $response = $this->call('DELETE', '/api/ldap/ldap-directory-attribute/' . $adAttribute->id);
        $response->assertStatus(200);
        $finalCount = LdapAdAttribute::where('ldap_id', $ldapId)->count();
        $this->assertEquals($finalCount - $initialCount, -1);
        $this->assertNull(LdapAdAttribute::find($adAttribute->id));
    }

    /** @group deleteDirectoryAttribute */
    public function test_deleteDirectoryAttribute_forNonExistingId()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('DELETE', '/api/ldap/ldap-directory-attribute/wrong_id');
        $response->assertStatus(400);
    }

    /** @group deleteDirectoryAttribute */
    public function test_deleteDirectoryAttribute_whenAttributeIsUsedByFaveoAttribute()
    {
        $this->getLoggedInUserForWeb('admin');
        $ldapId = Ldap::value('id');
        $adAttribute = LdapAdAttribute::create(['name' => 'test_attribute', 'is_default' => false, 'ldap_id'=> $ldapId]);
        LdapFaveoAttribute::orderBy('id', 'desc')->first()->update(['mapped_to' => $adAttribute->id]);
        $response = $this->call('DELETE', '/api/ldap/ldap-directory-attribute/' . $adAttribute->id);
        $response->assertStatus(200);
        $ldapAttributeId = LdapFaveoAttribute::where('ldap_id', $ldapId)->orderBy('id', 'desc')->first()->mapped_to;
        $defaulAttributeId = LdapAdAttribute::where('ldap_id', $ldapId)->where('name', 'FAVEO DEFAULT')->first()->id;
        $this->assertEquals($ldapAttributeId, $defaulAttributeId);
    }

    public function test_getLdapSettingsList_forSuccess()
    {
        $this->getLoggedInUserForWeb('admin');
        $methodResponse = $this->call('GET', 'api/ldap/settings');
        $methodResponse->assertStatus(200);
    }

    public function test_hideDefaultLogin_whenRecordsArePresent_shouldSaveTheConfig()
    {
        $this->getLoggedInUserForWeb('admin');
        $methodResponse = $this->call('POST', 'api/ldap/hide-default-login', ['hide_default_login'=>1]);
        $methodResponse->assertStatus(200);
        $hideDefaultLogin = (bool)CommonSettings::where('option_name', 'hide_default_login')->value('option_value');
        $this->assertTrue($hideDefaultLogin);
    }

    public function test_hideDefaultLogin_whenNoRecordsArePresentInTheDB_shouldReturn400()
    {
        $this->getLoggedInUserForWeb('admin');
        Ldap::where('id', '!=', null)->delete();
        $methodResponse = $this->call('POST', 'api/ldap/hide-default-login', ['hide_default_login'=>1]);
        $methodResponse->assertStatus(400);
    }

    public function test_deleteLdapSettings_whenWrongIdIsPassed_shouldGiveStatus404()
    {
        $this->getLoggedInUserForWeb('admin');
        $methodResponse = $this->call('DELETE', 'api/ldap/settings/909089809');
        $methodResponse->assertStatus(404);
    }

    public function test_deleteLdapSettings_whenCorrectIdIsPassed_shouldDeleteSuccessfullyAlongWithAllTheDependencies()
    {
        $ldap = Ldap::create();
        $ldap->searchBases()->create();
        $ldap->adAttributes()->create();

        $this->getLoggedInUserForWeb('admin');
        $methodResponse = $this->call('DELETE', "api/ldap/settings/$ldap->id");
        $methodResponse->assertStatus(200);
        $this->assertEquals(0, LdapAdAttribute::where('ldap_id', $ldap->id)->count());
        $this->assertEquals(0, LdapSearchBase::where('ldap_id', $ldap->id)->count());
    }
}
