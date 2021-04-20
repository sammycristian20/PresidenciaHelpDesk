<?php
namespace App\Plugins\Telephony\Tests\Backend\Providers;

use App\Plugins\Telephony\Tests\Backend\Providers\BaseProviderTest;
use App\Plugins\Telephony\Controllers\CallHookHandler;
use Illuminate\Http\Request;
use App\Plugins\Telephony\Model\TelephonyProvider;

/**
 * Test class to test webhook hanlder functionality of MyOperator and methods specific
 * to MyOperator avaialble in CallHookHandler.
 *
 * @package App\Plugins\Telephony\Tests\Backend\Providers
 * @author Manish Verma
 * @since v3.0.0
 */
class MyOperatorTest extends BaseProviderTest
{
	/**
	 * @var CallHookHandler
	 */
	protected $callHookHandler;

	public function setUp():void
	{
		$this->method = 'GET';
		$this->baseURL = 'telephone/myoperator/pass';
		$this->provider = 'myoperator';
		$this->params = $this->myOperatorRequestArray();
		parent::setUp();
		$this->callHookHandler = new CallHookHandler(new Request);
	}

	protected function myOperatorRequestArray()
	{
		return [
            "caller_number" => "+918764341282",
            "caller_id"     => "das213dasd4d",
            "start_time"    => "1596500100",
            "end_time"      => "1596510100",
            "recieved_by"   => "+918233077144",
            "recording"     => "32437246487226847367426478326478236qd3.mp3",
		];
	}

	/**
	 * @test
	 * @group getMyOperatorRecording
	 */
	public function check_getMyOperatorRecording_will_set_call_recording_to_resource_url_of_MyOperator_with_access_token_as_MyOperator_allows_resource_access_only_with_authentication()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', $this->myOperatorRequestArray());
		$provider = TelephonyProvider::updateOrCreate(['short' => 'myoperator'],[
			'token' => 'SomeRandomApiToken'
		]);
		$this->setPrivateProperty($this->callHookHandler, 'providerDetail', $provider);
		$this->getPrivateMethod($this->callHookHandler, 'getMyOperatorRecording');
		$recording =  $this->getPrivateProperty($this->callHookHandler, 'request')['recording'];
		$this->assertStringContainsStringIgnoringCase('SomeRandomApiToken', $recording);
	}

	/**
	 * @test
	 * @group getMyOperatorRecording
	 */
	public function check_getMyOperatorRecording_will_set_call_recording_to_an_empty_string_if_recording_is_not_persetn_in_the_request_array()
	{
		$requestArray = $this->myOperatorRequestArray();
		$provider = TelephonyProvider::updateOrCreate(['short' => 'myoperator'],[
			'token' => 'SomeRandomApiToken'
		]);
		$this->setPrivateProperty($this->callHookHandler, 'providerDetail', $provider);
		
		//request Array missing recording
		unset($requestArray['recording']);
		$this->setPrivateProperty($this->callHookHandler, 'request', $requestArray);
		$this->getPrivateMethod($this->callHookHandler, 'getMyOperatorRecording');
		$recording =  $this->getPrivateProperty($this->callHookHandler, 'request')['recording'];
		$this->assertEquals('', $recording);

		//recording is an empty string
		$requestArray['recording'] = '';
		$this->setPrivateProperty($this->callHookHandler, 'request', $requestArray);
		$this->getPrivateMethod($this->callHookHandler, 'getMyOperatorRecording');
		$recording =  $this->getPrivateProperty($this->callHookHandler, 'request')['recording'];
		$this->assertEquals('', $recording);

		//recording is null
		$requestArray['recording'] = null;
		$this->setPrivateProperty($this->callHookHandler, 'request', $requestArray);
		$this->getPrivateMethod($this->callHookHandler, 'getMyOperatorRecording');
		$recording =  $this->getPrivateProperty($this->callHookHandler, 'request')['recording'];
		$this->assertEquals('', $recording);
	}
}
