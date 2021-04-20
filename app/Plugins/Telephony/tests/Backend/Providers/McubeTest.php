<?php
namespace App\Plugins\Telephony\Tests\Backend\Providers;

use App\Plugins\Telephony\Tests\Backend\Providers\BaseProviderTest;

/**
 * Test class to test webhook hanlder functionality of MyOperator and methods specific
 * to MyOperator avaialble in CallHookHandler.
 *
 * @package App\Plugins\Telephony\Tests\Backend\Providers
 * @author Manish Verma
 * @since v3.0.0
 */
class McubeTest extends BaseProviderTest
{
	/**
	 * @var CallHookHandler
	 */
	protected $callHookHandler;

	public function setUp():void
	{
		$this->method = 'GET';
		$this->baseURL = 'telephone/mcube/pass';
		$this->provider = 'mcube';
		$this->params = $this->myMcubeRequestArray();
		$this->isParamNested = true;
		$this->nestedKey = 'data';
		parent::setUp();
	}

	private function myMcubeRequestArray()
	{
		return [
			"data" => [
           		"empnumber" => "+918233077144",
           		"extension" => "None",
           		"callid" => "ec8a4ba6-c2eb-4e71-888f-c3acbf585111",
           		"call_duration" => "7",
           		"destination" => "Welcome Sound",
           		"callfrom" => "+919966679850",
           		"end_time" => "2016-09-27 09:19:34.667036+05:30",
           		"action" => "6",
           		"timezone" => "Asia/Kolkata",
           		"RecordingUrl" => "https://google.com",
           		"hangup_cause" => "900",
           		"type" => "dtmf",
           		"start_time" => "2016-09-27 03:49:44.391601+00:00",
           		"call_type" => "incoming"
			]
		];
	}
}
