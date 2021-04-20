<?php
namespace App\Plugins\Telephony\Tests\Backend;

use Tests\AddOnTestCase;
use App\Plugins\Telephony\Model\TelephonyProvider;

/**
 * Test class for Telephony Settings
 * @package App\Plugins\Telephony\Tests\Backend
 * @author Manish Verma
 * @since v3.0.0
 */
class TelephonyTelephonySettingsControllerTest extends AddOnTestCase
{
	public function setUp():void
	{
		parent::setUp();
	}

	/**
	 * @test
	 * @group TelephonySettings
	 */
	public function get_provider_details_not_accessible_by_agents_or_admin()
	{
		$this->getLoggedInUserForWeb('user');
		$response = $this->call('GET', 'telephony/api/get-provider-details/exotel');
		$response->assertStatus(302);

		$this->getLoggedInUserForWeb('agent');
		$response = $this->call('GET', 'telephony/api/get-provider-details/exotel');
		$response->assertStatus(302);
	}

	/**
	 * @test
	 * @group TelephonySettings
	 */
	public function get_provider_details_return_detail_of_requested_provider_short_name_if_requested_by_admin()
	{
		$this->getLoggedInUserForWeb('admin');
		$response = $this->call('GET', 'telephony/api/get-provider-details/somthing');
		$response->assertStatus(404);

		$response = $this->call('GET', 'telephony/api/get-provider-details/exotel');
		$response->assertStatus(200);
		$providerData = json_decode($response->getContent())->data;
		$this->assertEquals(10, count($providerData));
		$this->assertEquals("Exotel", $providerData[1]->value);
	}

	/**
	 * @test
	 * @group TelephonySettings
	 */
	public function admin_can_updated_provide_details_with_correct_values()
	{
		$this->getLoggedInUserForWeb('admin');
		$param = [
			'app_id' => "",
			'conversion_waiting_time' => 5,
			'iso' => "IN",
			'log_miss_call' => 0,
			'token' => ""
		];
		$this->assertDatabaseMissing('telephony_provider_settings', $param);
		$response = $this->call('POST', 'telephony/api/update-provider-details/exotel', $param);
		$this->assertDatabaseHas('telephony_provider_settings', $param);
	}

	/**
	 * @test
	 * @group TelephonySettings
	 */
	public function admin_can_updated_provide_details_except_name_and_short()
	{
		$this->getLoggedInUserForWeb('admin');
		$param = [
			'app_id' => "",
			'conversion_waiting_time' => 5,
			'iso' => "IN",
			'log_miss_call' => 0,
			'token' => "",
			'name' => 'manish',
			'short' => 'verma'
		];
		$this->assertDatabaseMissing('telephony_provider_settings', $param);
		$response = $this->call('POST', 'telephony/api/update-provider-details/exotel', $param);
		$this->assertDatabaseMissing('telephony_provider_settings', $param);
	}
}