<?php

namespace App\FaveoReport\Tests\Backend\Controllers;

use Auth;
use Tests\DBTestCase;

class SettingsControllerTest extends DBTestCase
{
    public function setUp():void
    {
        parent::setUp();

        $this->getLoggedInUserForWeb('admin');
    }

    /** @group reports-settings */
    public function test_showSettings_getReportSettingsAsUser()
    {
        Auth::logout();

        $this->getLoggedInUserForWeb();

        $response = $this->call('GET', route('report.settings'));
        $response->assertStatus(FAVEO_TEMP_REDIRECT_CODE);
    }

    /** @group reports-settings */
    public function test_showSettings_getReportSettingsAsAgent()
    {
        Auth::logout();

        $this->getLoggedInUserForWeb('agent');

        $response = $this->call('GET', route('report.settings'));

        $response->assertStatus(FAVEO_TEMP_REDIRECT_CODE);
    }

    /** @group reports-settings */
    public function test_showSettings_getReportSettingsAsAdmin()
    {
        $response = $this->call('GET', route('report.settings'));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertSee('Report');
    }

    /** @group reports-settings */
    public function test_getReportSettings_getReportSettingsDataAsAdmin()
    {
        $response = $this->call('GET', route('api.report.settings'));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $settingsData = json_decode($response->content())->data;

        $this->assertArrayHasKeys(['max_date_range', 'records_per_file'], $settingsData);
    }

    /** @group reports-settings */
    public function test_storeSettings_storeReportSettingsDataAsAdminFailurTest()
    {
        $response = $this->call('POST', route('api.report.settings.store'), ['max_date_range' => 'string']);

        $response->assertStatus(FAVEO_VALIDATION_ERROR_CODE);
        $response->assertJson(['success' => false]);
    }

    /** @group reports-settings */
    public function test_storeSettings_storeReportSettingsDataAsAdminSuccessTest()
    {
        $response = $this->call('POST', route('api.report.settings.store'), [
            'max_date_range'   => 3,
            'records_per_file' => 500,
        ]);

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('common_settings', [
            'option_name'  => 'reports_records_per_file',
            'option_value' => 500,
        ]);
    }
}
