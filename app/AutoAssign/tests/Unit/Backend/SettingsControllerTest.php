<?php

namespace App\AutoAssign\Tests\Unit\Backend;

use Tests\DBTestCase;
use App\Model\helpdesk\Settings\CommonSettings;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsControllerTest extends DBTestCase
{
    /** @group getAutoAssignSettingsDetails */
    public function test_getAutoAssignSettingsDetails_returnsAutoAssignSettingsDetails()
    {
        $this->getLoggedInUserForWeb('admin');
        $autoAssigns = [
                       "threshold" => "1",
                       "status" => "1",
                       "only_login" => "1",
                       "assign_not_accept" => "1",
                       "assign_with_type" => "1",
                       "is_location" => "1",
                       "assign_department_option" => "all"
                      ];
        foreach ($autoAssigns as $autoAssign => $value) {
            factory(CommonSettings::class)->create([
                'option_name' => 'auto_assign',
                'optional_field' => $autoAssign,
                'option_value' => $value
            ]);
        }
        $response = $this->call('GET',url("api/get-auto-assign"));
        $response->assertStatus(200);
        $autoAssignInResponse = json_decode($response->content())->data->autoAssign;
        foreach ($autoAssigns as $autoAssign => $value) {
            $this->assertEquals($autoAssigns[$autoAssign], $value);
        }   
    }

    /** @group postSettings */
    public function test_postSettings_withAllAutoAssignFields_returnsAutoAssignUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $autoAssigns = [
                       "threshold" => "1",
                       "status" => "1",
                       "only_login" => "1",
                       "assign_not_accept" => "1",
                       "assign_with_type" => "1",
                       "is_location" => "1",
                       "assign_department_option" => "all"
                      ];
        $response = $this->call('POST',url("api/auto-assign"),$autoAssigns);
        $response->assertStatus(200);
        foreach ($autoAssigns as $autoAssign => $value) {
            $this->assertDatabaseHas('common_settings', [
                'option_name' => 'auto_assign',
                'optional_field' => $autoAssign,
                'option_value' => $value
            ]);
        }
    }

     /** @group postSettings */
    public function test_postSettings_withAllAutoAssignFieldsExceptDepartmentListWhenAssignDepartmentOptionEqualsSpecific_returnsDepartmentListRequiredException()
    {
        $this->getLoggedInUserForWeb('admin');
        $autoAssigns = [
                       "threshold" => "1",
                       "status" => "1",
                       "only_login" => "1",
                       "assign_not_accept" => "1",
                       "assign_with_type" => "1",
                       "is_location" => "1",
                       "assign_department_option" => "specific"
                      ];
        $response = $this->call('POST',url("api/auto-assign"),$autoAssigns);
        $response->assertStatus(412);
    }
}
