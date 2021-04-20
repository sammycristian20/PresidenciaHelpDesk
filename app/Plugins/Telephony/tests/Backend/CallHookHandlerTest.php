<?php
namespace App\Plugins\Telephony\Tests\Backend;

use Tests\AddOnTestCase;
use App\Plugins\Telephony\Controllers\CallHookHandler;
use Illuminate\Http\Request;
use App\Plugins\Telephony\Model\TelephonyProvider;

/**
 * Test class to test Generic Units of CallHookHandler independently.
 *
 * @package App\Plugins\Telephony\Tests\Backend
 * @author Manish Verma
 * @since v3.0.0
 */
class CallHookHandlerTest extends AddOnTestCase
{
	/**
	 * @var CallHookHandler
	 */
	protected $callHookHandler;

	public function setUp():void
	{
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
	 * @group CallHookHandler
	 * @group validateRequestArray
	 */
	public function check_validateRequestArray_returns_false_if_requestArray_is_empty()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', []);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['a']]);
		$this->assertEquals(false, $result);
	}

	/**
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestArray
	 */
	public function check_validateRequestArray_returns_false_if_requestArray_does_not_contain_any_required_keys()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', ['a' => 'b', 'c' => 'd']);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['a', 'c', 'd']]);
		$this->assertEquals(false, $result);
	}

	/**
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestArray
	 */
	public function check_validateRequestArray_returns_true_if_requestArray_contains_all_required_keys()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', ['a' => 'b', 'c' => 'd', 'e' => 'f']);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['a', 'c', 'e']]);
		$this->assertEquals(true, $result);
	}

	/**
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestArray
	 */
	public function check_validateRequestArray_returns_true_if_requestArray_contains_all_required_keys_irrespective_of_case()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', ['aBc' => 'b', 'C' => 'd', 'EDe' => 'f']);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['abc', 'c', 'eDE']]);
		$this->assertEquals(true, $result);
	}

	/**
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestArray
	 */
	public function check_validateRequestArray_returns_false_if_datakey_is_not_present_in_requestArray()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', ['a' => 'b', 'c' => 'd', 'e' => 'f']);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['a', 'c', 'e'], 'data']);
		$this->assertEquals(false, $result);
	}

	/**
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestArray
	 */
	public function check_validateRequestArray_returns_false_if_datakey_is_present_in_requestArray_but_any_of_required_key_is_not_present_in_data_array_of_request_array()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', ['data' => ['a' => 'b', 'c' => 'd', 'e' => 'f']]);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['a', 'c', 'e', 'f'], 'data']);
		$this->assertEquals(false, $result);
	}

	/**
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestArray
	 */
	public function check_validateRequestArray_returns_true_if_datakey_is_present_in_requestArray_and_all_of_required_keys_are_present_in_data_array_of_request_array()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', ['data' => ['a' => 'b', 'c' => 'd', 'e' => 'f']]);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['a', 'c', 'e'], 'data']);
		$this->assertEquals(true, $result);
	}

	/**
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestArray
	 */
	public function check_validateRequestArray_returns_false_if_request_array_contains_all_the_required_keys_but_has_missing_values()
	{
		$this->setPrivateProperty($this->callHookHandler, 'request', ['a' => '', 'c' => 'd', 'e' => 'f']);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['a', 'c', 'e']]);
		$this->assertEquals(false, $result);

		$this->setPrivateProperty($this->callHookHandler, 'request', ['a' => 'b', 'c' => 'd', 'e' => null]);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestArray', [['a', 'c', 'e']]);
		$this->assertEquals(false, $result);
	}
	/**
	 * @test
	 * @group CallHookHandler
	 */
	public function check_calling_webhook_url_with_random_provider_pass_returns_500()
	{
		//Webhook listens for GET request
		$response1 = $this->call('GET', 'telephone/randomprovider/pass/');
		$response1->assertStatus(500);
		//Webhook also listens for POST request
		$response2 = $this->call('POST', 'telephone/randomprovider/pass/');
		$response2->assertStatus(500);
	}

	/**
	 *
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestAndProceedToConvert
	 */
	public function check_validateRequestAndProceedToConvert_will_allow_ticket_creation_only_if_called_with_default_parameters_and_provider_setting_allows_misscall_logging()
	{
		$requiredKeys = ['caller_number', 'recieved_by', 'recieved_by', 'start_time', 'end_time', 'caller_id', 'recording'];
		$requestArray = $this->myOperatorRequestArray();
		unset($requestArray['recording']);
		$this->setPrivateProperty($this->callHookHandler, 'request', $requestArray);

		//createLog true but provider does not allow misscall logging
		$provider = factory(TelephonyProvider::class)->create(['log_miss_call' => 0]);
		$this->setPrivateProperty($this->callHookHandler, 'providerDetail', $provider);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestAndProceedToConvert', [$requiredKeys]);
		$this->assertEquals(null, $result);

		//createLog false but provider allows misscall logging
		$provider = factory(TelephonyProvider::class)->create(['log_miss_call' => 1]);
		$this->setPrivateProperty($this->callHookHandler, 'providerDetail', $provider);
		$result2 = $this->getPrivateMethod($this->callHookHandler, 'validateRequestAndProceedToConvert', [$requiredKeys, null, false]);
		$this->assertEquals(null, $result2);

		//ticket will be created only when createLog is true and provider allows misscall logging
		$this->setPrivateProperty($this->callHookHandler, 'providerDetail', $provider);
		$result3 = $this->getPrivateMethod($this->callHookHandler, 'validateRequestAndProceedToConvert', [$requiredKeys, null, true]);
		$this->assertNotEquals(null, $result3);
	}

	/**
	 * @test
	 * @group CallHookHandler
	 * @group validateRequestAndProceedToConvert
	 */
	public function check_validateRequestAndProceedToConvert_will_not_create_ticket_for_provider_which_has_convsetion_waiting_time_greater_than_zero()
	{
		$requiredKeys = ['caller_number', 'recieved_by', 'recieved_by', 'start_time', 'end_time', 'caller_id', 'recording'];
		$this->setPrivateProperty($this->callHookHandler, 'request', $this->myOperatorRequestArray());
		// if conversion_waiting_time is zero ticket will be created immediately
		$provider = factory(TelephonyProvider::class)->create(['conversion_waiting_time' => 0]);
		$this->setPrivateProperty($this->callHookHandler, 'providerDetail', $provider);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestAndProceedToConvert', [$requiredKeys]);
		$this->assertNotEquals(null, $result);

		// if conversion_waiting_time is not zero ticket will not be created immediately
		$provider = factory(TelephonyProvider::class)->create(['conversion_waiting_time' => 5]);
		$this->setPrivateProperty($this->callHookHandler, 'providerDetail', $provider);
		$result = $this->getPrivateMethod($this->callHookHandler, 'validateRequestAndProceedToConvert', [$requiredKeys]);
		$this->assertEquals(null, $result);
	}
}