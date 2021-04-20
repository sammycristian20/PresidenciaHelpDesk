<?php

namespace App\TimeTrack\Tests\Admin;

use Tests\AddOnTestCase;

class TimeTrackTest extends AddOnTestCase
{
    /** @group timetrack */
    public function test_settings_seeTimeTrackSettingsPageAsAdmin()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('GET', url('time-track'));

        $response->assertStatus(200);
    }

    /** @group timetrack */
    public function test_getTimeTrackSettings_getTimeTrackSettingsAsAdmin()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('GET', url('time-track/get-settings'));

        $response->assertStatus(200);
        $response->assertJson(["success" => true]);

        $json = json_decode($response->content(), true);

        $this->assertArrayHasKey('additional', $json['data']);
    }

    /** @group timetrack */
    public function test_storeSetting_storeTimeTrackSettingsAsAdminSuccessTest()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->json('POST', url('time-track'), [
            'additional' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(["success" => true]);

        $this->assertDatabaseHas('common_settings', [
            'option_name'    => 'time_track_option',
            'status'         => 1,
            'optional_field' => 'additional',
        ]);
    }

    /** @group timetrack */
    public function test_storeSetting_storeTimeTrackSettingsAsAdminFailurTest()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->json('POST', url('time-track'));

        $response->assertStatus(422);
        $response->assertJson(["success" => false]);
    }
}
