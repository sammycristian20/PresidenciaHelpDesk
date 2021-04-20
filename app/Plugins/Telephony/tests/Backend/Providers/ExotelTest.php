<?php
namespace App\Plugins\Telephony\Tests\Backend\Providers;

use App\Plugins\Telephony\Tests\Backend\Providers\BaseProviderTest;

/**
 * Test class to test webhook hanlder functionality of Exotel and methods specific
 * to Exotel avaialble in CallHookHandler.
 *
 * Exotel allows POPUp hook URL which sends information of call start and end with data
 * to that URL. We currently have single webhook URL, so we handle this hooks based on
 * the data posted on the hook. So for exotel integration we can have below cases
 *
 * A. Popup hook enabled
 *    In this case exotel may call hook 3 times
 *    1. Call start alert data contains Status key
 *    2. Call finish alert data contains Status key
 *       This again may have 2 sub case
 *       # Call is completed : Call is completed then it will have RecordingAvailableBy
 *         key so we can ignore it as it will also call Post call hook.
 *       # Call is missed call : If call is missed then chances are Post call hook might
 *         not get called so we will handle missed call logging explicitly
 *    3. Post call hook(normal scenario)
 *
 * B. Popup hook not enables
 *    In this case exotel will call hook only one time Post call hook(normal scenario)
 *
 * @package App\Plugins\Telephony\Tests\Backend\Providers
 * @author Manish Verma
 * @since v3.0.0
 */
class ExotelTest extends BaseProviderTest
{
	/**
	 * @var CallHookHandler
	 */
	protected $callHookHandler;

	public function setUp():void
	{
		parent::setUp();
		$this->method = 'GET';
		$this->baseURL = 'telephone/exotel/pass';
		$this->provider = 'exotel';
		$this->params = $this->exotelArray();
	}

	private function exotelArray()
	{
		return [
           	"CallSid" => "d95feaed6383fb24d71a6756126d5350",
            "From" => "08042096073",
            "To" => "08033172870",
            "Direction" => "incoming",
           	"DialCallDuration" => "13",
            "StartTime" => "2016-08-17 19:42:43",
            "EndTime" => "0000-00-00 00:00:00",
            "CallType" => "completed",
            "RecordingUrl" => "https:\/\/s3-ap-southeast-1.amazonaws.com\/exotelrecordings\/laad5\/d95feaed6383fb24d71a6756126d5350.mp3",
	        "DialWhomNumber" => "08233077144", //Agent number who recieved the call
    	    "Created" => "Wed, 17 Aug 2016 19:42:43",
        	"RecordingAvailableBy" => "Wed, 17 Aug 2016 19:47:58",
           	"flow_id" => "107722",
            "tenant_id" => "42758",
            "CallFrom" => "08042096073",
            "CallTo" => "08033172870",
            "DialCallStatus" => "completed",
            "CurrentTime" => "2016-08-17 19:42:58"
		];
	}

	/**
	 * When calling hook with call start popup parameters will have "status"
	 * key with with "busy" as its value. It will also contain other required data
	 * as normal hook but will not have "RecordingAvailableBy".
	 */
	private function exotelPopupWebhookParameterCallStart()
	{
		$params = $this->exotelArray();
		$params['Status'] = 'busy';
		return $params ;
	}

	/**
	 * When calling hook with call start popup parameters will have "status"
	 * key with with "free" as its value. It will also contain other required data
	 * as normal hook but "RecordingAvailableBy" may or may not be present based
	 * on the nature of the call like missed call or completed call
	 */
	private function exotelPopupWebhookParameterCallEnd()
	{
		$params = $this->exotelArray();
		$params['Status'] = 'free';
		return $params;
	}

	/**
	 * @test
	 */
	public function callig_hook_with_call_start_pop_alert_will_broadcast_call_start_event_and_will_not_create_the_ticket()
	{
		$this->params = $this->exotelPopupWebhookParameterCallStart();
		$this->params = $this->corruptParamArray($this->params, ['RecordingUrl']);
		$this->assertCallhookProcess(true, 'call-started');
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets');
	}

	/**
	 * @test
	 */
	public function callig_hook_with_call_end_alert_with_completed_call_will_not_create_ticket_or_not_dispatch_event()
	{
		$this->params = $this->exotelPopupWebhookParameterCallEnd();
		$this->assertCallhookProcess();
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets');
	}

	/**
	 * @test
	 */
	public function callig_hook_with_call_end_alert_with_missed_call_will_not_create_ticket_and_will_dispatch_call_ended_event_if_missed_call_loggig_is_not_allowed()
	{
		$this->params = $this->exotelPopupWebhookParameterCallEnd();
		$this->params = $this->corruptParamArray($this->params, ['RecordingAvailableBy']);
		$this->assertCallhookProcess(true, 'call-ended');
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets');
		$this->assertModelCount('App\Plugins\Telephony\Model\TelephonyLog');
	}

	/**
	 * @test
	 */
	public function callig_hook_with_call_end_alert_with_missed_call_will_create_ticket_and_will_dispatch_call_ended_event_if_missed_call_loggig_is_allowed()
	{
		$this->params = $this->exotelPopupWebhookParameterCallEnd();
		$this->params = $this->corruptParamArray($this->params, ['RecordingAvailableBy']);
		$this->updateProviderSettings(['log_miss_call' => 1]);
		$this->assertCallhookProcess(true, 'call-ended');
		$this->assertModelCount('App\Model\helpdesk\Ticket\Tickets', 1);
		$this->assertModelCount('App\Plugins\Telephony\Model\TelephonyLog', 1);
	}
}
