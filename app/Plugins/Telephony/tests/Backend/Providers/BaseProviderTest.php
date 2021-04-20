<?php
namespace App\Plugins\Telephony\Tests\Backend\Providers;

use Tests\AddOnTestCase;
use App\Plugins\Telephony\Model\TelephonyProvider;
use Illuminate\Support\Facades\Event;
use App\Plugins\Telephony\Events\IncomingCallEvent;

/**
 * Base test class for testing webhook call functionality of different IVR
 * service providers. It's an abstract class which should not be run as Test
 * class as it does not have any tests of it's own but provides generic tests
 * which every provider test class must run as their own tests.
 *
 * @package App\Plugins\Telephony\Tests\Backend\Providers
 * @author Manish Verma
 * @since v3.0.0
 */
abstract class BaseProviderTest extends AddOnTestCase
{
	/**
	 * @var string
	 */
	protected $baseURL;

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var string
	 */
	protected $provider;

	/**
	 * @var array
	 */
	protected $params;

	/**
	 * @var bool
	 */
	protected $isParamNested = false;

	/**
	 * @var bool
	 */
	protected $nestedKey = '';

	public function setUp():void
	{
		parent::setUp();
		$this->resetLogs();
	}
	/**
	 *=======================================================
	 *                    Helpers
	 *=======================================================
	 */
	private function getRecordingKeyInParams()
	{
		/**
		 * Ensure the values of these keys in request param for test is always
		 * 08233077144 so we can check who is getting notified via broadcasting event
		 */

		return [
			"knowlarity" => "resource_url",
			"mcube" => "RecordingUrl",
			"myoperator" => "recording",
			"exotel" => "RecordingUrl"
		];
	}

	private function getAgentNumberInParams()
	{
		return [
			"knowlarity" => "dispnumber",
			"mcube" => "empnumber",
			"myoperator" => "recieved_by",
			"exotel" => "DialWhomNumber"
		];
	}

	protected function updateProviderSettings(array $updateData)
	{
		TelephonyProvider::where('short', $this->provider)->update($updateData);
	}

	protected function corruptParamArray(array $params, array $keyForRemoval = [])
	{
		if($this->isParamNested && (bool)checkArray($this->nestedKey,$params)) {
			$params[$this->nestedKey] = $this->corruptParamArray($params[$this->nestedKey], $keyForRemoval);
			return $params;
		}
		if(!$keyForRemoval) return array_splice($params, 2);
		
		foreach ($keyForRemoval as $key) {
			unset($params[$key]);
		}

		return $params;
	}

	protected function assertCallhookProcess($eventDispactched = false, $event = 'call-ended', $channel = '', $statusCode = 200)
	{
		$response = $this->call($this->method, $this->baseURL, $this->params);
		$response->assertStatus($statusCode);
		($eventDispactched) ? $this->assertEventIsBroadcasted($event, $channel) : $this->assertEventIsNotBroadcasted($event);
	}

	protected function assertModelCount(string $model, $count = 0)
	{
		$this->assertEquals($count, $model::count());
	}

	/**
	 *=======================================================
	 *                    Tests
	 *=======================================================
	 */

	/**
	 * @test
	 */
	public function check_calling_webhook_url_with_missing_required_parameters_for_IVR_providers_will_not_create_ticket_and_will_not_dispatch_the_event()
	{
		$this->params = $this->corruptParamArray($this->params);
		$this->assertCallhookProcess();
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets');
		$this->assertModelCount('App\Plugins\Telephony\Model\TelephonyLog');
	}

	/**
	 * @test
	 */
	public function check_calling_webhook_url_with_all_required_parameters_for_IVR_providers_will_create_ticket_and_dispatch_call_ended_event()
	{
		$this->assertCallhookProcess(true);
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets',1);
		$this->assertModelCount('App\Plugins\Telephony\Model\TelephonyLog',1);
	}

	/**
	 * @test
	 */
	public function check_calling_webhook_url_with_missing_call_recording_will_not_create_ticket_if_call_log_for_missed_call_is_not_allowed_and_will_not_dispatch_the_event()
	{
		$recordingKey = checkArray($this->provider,$this->getRecordingKeyInParams());
		$this->params = $this->corruptParamArray($this->params, [$recordingKey]);
		$this->assertCallhookProcess();
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets');
		$this->assertModelCount('App\Plugins\Telephony\Model\TelephonyLog');
	}

	/**
	 * @test
	 */
	public function check_calling_webhook_url_with_missing_call_recording_will_create_ticket_if_call_log_for_missed_call_is_t_allowed_dispatch_call_ended_event()
	{
		$recordingKey = checkArray($this->provider,$this->getRecordingKeyInParams());
		$this->params = $this->corruptParamArray($this->params, [$recordingKey]);
		$this->updateProviderSettings(['log_miss_call' => 1]);
		$this->assertCallhookProcess(true);
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets', 1);
		$this->assertModelCount('App\Plugins\Telephony\Model\TelephonyLog',1);
	}

	/**
	 * @test
	 */
	public function check_calling_webhook_url_with_correct_param_will_not_create_ticket_if_conversion_waiting_time_is_greater_than_zero_but_will_broadcast_event()
	{
		$recordingKey = checkArray($this->provider,$this->getRecordingKeyInParams());
		$this->updateProviderSettings(['conversion_waiting_time' => 5]);
		$this->assertCallhookProcess(true);
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets', 0);
		$this->assertModelCount('App\Plugins\Telephony\Model\TelephonyLog',1);
	}

	/**
	 * @test
	 */
	public function check_calling_webhook_url_will_broadcast_event_only_for_agent_to_whom_call_is_connected_on_their_mobile_or_phone_number_with()
	{
		$userId = factory('App\User')->create(['country_code' => 91, 'mobile' => '8233077144', 'role' => 'agent'])->id;
		$recordingKey = checkArray($this->provider,$this->getRecordingKeyInParams());
		$this->assertCallhookProcess(true, 'call-ended', "private-user-notifications.$userId");
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets', 1);
		$this->assertModelCount('App\Plugins\Telephony\Model\TelephonyLog',1);
	}
}