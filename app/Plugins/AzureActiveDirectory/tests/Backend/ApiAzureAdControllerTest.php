<?php


namespace App\Plugins\AzureActiveDirectory\tests\Backend;

use App\Model\helpdesk\Settings\CommonSettings;
use App\Plugins\AzureActiveDirectory\Model\AzureAd;
use Tests\AddOnTestCase;

class ApiAzureAdControllerTest extends AddOnTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->getLoggedInUserForWeb('admin');
    }

    public function test_postAzureAdSettings_whenIdIsGivenAsNull_shouldSaveAsANewRecord()
    {
        $initialCount = AzureAd::count();

        $this->mockAzureConnector(['getUsers'=> [], 'getAccessCodeInClientCredentialsMode'=>'test']);

        $methodResponse = $this->call(
            'POST',
            'api/azure-active-directory/settings',
            ['id'=> null, 'app_name'=>'test_app_name', 'tenant_id'=>'test_tenant_id', 'app_id'=> 'test_app_id',
            'app_secret'=>'test_app_secret',
            'login_button_label'=>'test_login_button_label']
        );

        $methodResponse->assertStatus(200);

        $finalCount = AzureAd::count();
        $this->assertEquals(1, $finalCount - $initialCount);
        $azureAd = AzureAd::orderBy('id', 'desc')->first();
        $this->assertEquals('test_app_name', $azureAd->app_name);
        $this->assertEquals('test_tenant_id', $azureAd->tenant_id);
        $this->assertEquals('test_app_id', $azureAd->app_id);
        $this->assertEquals('test_app_secret', $azureAd->app_secret);
        $this->assertEquals('test_login_button_label', $azureAd->login_button_label);
    }

    public function test_postAzureAdSettings_whenIdIsGivenAsExistingId_shouldSaveAsExistingRecord()
    {

        $azureAdId = AzureAd::create(['app_name'=> 'app_name', 'tenant_id'=> 'tenant_id', 'app_id'=> 'app_id', 'app_secret'=> 'app_secret'])->id;

        $initialCount = AzureAd::count();

        $this->mockAzureConnector(['getUsers'=> [], 'getAccessCodeInClientCredentialsMode'=>'test']);

        $methodResponse = $this->call(
            'POST',
            'api/azure-active-directory/settings',
            ['id'=> $azureAdId, 'app_name'=>'test_app_name', 'tenant_id'=>'test_tenant_id', 'app_id'=> 'test_app_id',
            'app_secret'=>'test_app_secret',
            'login_button_label'=>'test_login_button_label']
        );

        $methodResponse->assertStatus(200);

        $finalCount = AzureAd::count();
        $this->assertEquals(0, $finalCount - $initialCount);
        $azureAd = AzureAd::orderBy('id', 'desc')->first();
        $this->assertEquals('test_app_name', $azureAd->app_name);
        $this->assertEquals('test_tenant_id', $azureAd->tenant_id);
        $this->assertEquals('test_app_id', $azureAd->app_id);
        $this->assertEquals('test_app_secret', $azureAd->app_secret);
        $this->assertEquals('test_login_button_label', $azureAd->login_button_label);
    }

    public function test_getAzureAdSettings_whenIdIsGivenAsExistingId_shouldGiveThatSettingBack()
    {
        $azureAdId = AzureAd::create(['app_name'=> 'app_name', 'tenant_id'=> 'tenant_id', 'app_id'=> 'app_id', 'app_secret'=> 'app_secret'])->id;

        $methodResponse = $this->call('get', "api/azure-active-directory/settings/$azureAdId");

        $methodResponse->assertStatus(200);

        $azureAdObject = json_decode($methodResponse->getContent())->data;

        $this->assertEquals('app_name', $azureAdObject->app_name);
        $this->assertEquals('tenant_id', $azureAdObject->tenant_id);
        $this->assertEquals('app_id', $azureAdObject->app_id);
        $this->assertEquals('app_secret', $azureAdObject->app_secret);
    }

    public function test_getAzureAdSettings_whenIdIsGivenAsNonExisting_shouldGive404()
    {
        AzureAd::create(['app_name'=> 'app_name', 'tenant_id'=> 'tenant_id', 'app_id'=> 'app_id', 'app_secret'=> 'app_secret'])->id;

        $methodResponse = $this->call('get', "api/azure-active-directory/settings/unknownId");

        $methodResponse->assertStatus(404);
    }

    public function test_deleteAzureAdSettings_whenIdIsGivenAsExistingId_shouldDeleteThatSetting()
    {
        $azureAdId = AzureAd::create(['app_name'=> 'app_name', 'tenant_id'=> 'tenant_id', 'app_id'=> 'app_id', 'app_secret'=> 'app_secret'])->id;

        $initialCount = AzureAd::count();

        $methodResponse = $this->call('delete', "api/azure-active-directory/settings/$azureAdId");

        $methodResponse->assertStatus(200);

        $finalCount = AzureAd::count();

        $this->assertEquals(1, $initialCount - $finalCount);
    }

    public function test_deleteAzureAdSettings_whenIdIsGivenAsNonExisting_shouldGive404()
    {
        $azureAdId = AzureAd::create(['app_name'=> 'app_name', 'tenant_id'=> 'tenant_id', 'app_id'=> 'app_id', 'app_secret'=> 'app_secret'])->id;

        $initialCount = AzureAd::count();

        $methodResponse = $this->call('delete', "api/azure-active-directory/settings/unknownId");

        $methodResponse->assertStatus(404);

        $finalCount = AzureAd::count();

        $this->assertEquals(0, $initialCount - $finalCount);
    }

    /**
     * Mocks LdapConnector class
     * @param array $methodNamesWithValues associative array of methodNames and values []
     * @return null
     */
    private function mockAzureConnector(array $methodNamesWithValues)
    {
        return $this->mockDependency('App\Plugins\AzureActiveDirectory\Controllers\AzureConnector', $methodNamesWithValues);
    }

    public function test_getAzureAdList_forSuccess()
    {
        AzureAd::create(['app_name'=> 'app_name', 'tenant_id'=> 'tenant_id', 'app_id'=> 'app_id', 'app_secret'=> 'app_secret']);
        AzureAd::create(['app_name'=> 'app_name', 'tenant_id'=> 'tenant_id', 'app_id'=> 'app_id', 'app_secret'=> 'app_secret']);

        $methodResponse = $this->call('GET', 'api/azure-active-directory/settings');
        $records = json_decode($methodResponse->getContent())->data->directories->data;
        $this->assertCount(2, $records);
    }

    public function test_appendMetaSettings_whenDataIsPassedAsArray_shouldAppendAzureMetaSettingsToIt()
    {
        $data = ['test'=> 'test'];

        // this event indirectly calls `appendMetaSettings` method
        \Event::dispatch('social-login-provider-dispatch', [&$data]);

        $this->assertArrayHasKey('azure_meta_settings', $data);
    }

    public function test_hideDefaultLogin_whenRecordsArePresent_shouldSaveTheConfig()
    {
        AzureAd::create();
        $this->getLoggedInUserForWeb('admin');
        $methodResponse = $this->call('POST', 'api/azure-active-directory/hide-default-login', ['hide_default_login'=>1]);
        $methodResponse->assertStatus(200);
        $hideDefaultLogin = (bool)CommonSettings::where('option_name', 'hide_default_login')->value('option_value');
        $this->assertTrue($hideDefaultLogin);
    }

    public function test_hideDefaultLogin_whenNoRecordsArePresentInTheDB_shouldReturn400()
    {
        $this->getLoggedInUserForWeb('admin');
        AzureAd::where('id', '!=', null)->delete();
        $methodResponse = $this->call('POST', 'api/azure-active-directory/hide-default-login', ['hide_default_login'=>1]);
        $methodResponse->assertStatus(400);
    }

}
