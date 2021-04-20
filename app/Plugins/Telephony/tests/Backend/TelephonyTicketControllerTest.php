<?php
namespace App\Plugins\Telephony\Tests\Backend;

use Tests\AddOnTestCase;
use App\Plugins\Telephony\Controllers\TelephonyTicketController;
use App\User;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Plugins\Telephony\Model\TelephonyLog;
use App\Plugins\Telephony\Model\TelephonyProvider;
use Illuminate\Support\Facades\Event;
use App\Plugins\Telephony\Events\IncomingCallEvent;
use Illuminate\Http\Request;
/**
 * Test class which tests each unit of TelephonyTicketController class.
 * TelephonyTicketController class is a generic class which handles the ticket conversion
 * and call log handling process in a generic way. Its methods and properties can be used
 * by extending it to child class for implementation based on different IVR systems. All IVR
 * specific logic should be handled in child class and method of TelephonyTicketController
 * can be used to let Faveo handle call logs and ticket creation process along with event
 * broadcasting to send alerts to front end.
 *
 *
 * @since v3.0.0
 * @author Manish Verma<manish.verma@ladybirdweb.com>
 * @package App\Plugins\Telephony\Tests\Backend
 */
class TelephonyTicketControllerTest extends AddOnTestCase
{
	/**
	 * @var TelephonyTicketController
	 */
	protected $ticketController;

	/**
	 * @var array
	 */
	private $requestKeys;

	/**
	 * @var array
	 */
	private $requestArray;

	public function setUp():void
	{
		parent::setUp();
		$this->ticketController = new TelephonyTicketController();
	}

	/**
	 * @test
	 * @group formatNumber
	 */
	public function check_formatNumber_parses_given_number_and_returns_array_containing_country_code_and_national_number_based_on_region()
	{
		//Default region India

		// Numbers passed with country  
		$result =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077144']);
		$result2 =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['8233077144']);
		//method trims whitespaces too
		$result3 =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077144 ']);
		$this->assertEquals(91, $result[0]);
		$this->assertEquals("8233077144", $result[1]);
		$this->assertEquals(91, $result2[0]);
		$this->assertEquals("8233077144", $result2[1]);
		$this->assertEquals(91, $result3[0]);
		$this->assertEquals("8233077144", $result3[1]);
		
		// set region to another country china which has country code as 86
		$this->setPrivateProperty($this->ticketController, 'region', 'CN');
		$result =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077144']);
		$result2 =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['08233077144']);
		$this->assertEquals(91, $result[0]);
		$this->assertEquals("8233077144", $result[1]);
		$this->assertEquals(86, $result2[0]);
		$this->assertEquals("8233077144", $result2[1]);
	}

	/**
	 * @test
	 * @group formatNumber
	 */
	public function check_formatNumber_will_throw_exception_when_number_passed_as_argument_is_incorrect()
	{
		$this->expectException(\Exception::class);
		//Default region India
		$this->getPrivateMethod($this->ticketController, 'formatNumber', ['+mansi']);

		$this->getPrivateMethod($this->ticketController, 'formatNumber', [' ']);

		$this->getPrivateMethod($this->ticketController, 'formatNumber', [null]);

		$this->getPrivateMethod($this->ticketController, 'formatNumber', ['83ee077!44']);
	}

	/**
	 * @test
	 * @group searchUser
	 */
	public function check_searchUser_will_return_null_if_user_with_given_number_not_found()
	{
		$number =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077144']);
		$user = $this->getPrivateMethod($this->ticketController, 'searchUser', [$number]);
		$this->assertEquals(null, $user);

		factory(User::class)->create(['mobile' => '8233077144']);
		$user2 = $this->getPrivateMethod($this->ticketController, 'searchUser', [$number]);
		$this->assertEquals(null, $user);
	}

	/**
	 * @test
	 * @group searchUser
	 */
	public function check_searchUser_will_return_user_object_if_user_with_given_number_and_country_code_is_found_in_database()
	{
		// Will return user if number found as mobile
		$user1 = factory(User::class)->create(['country_code' => '91', 'mobile' => '8233077144']);
		$number =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077144']);
		$result = $this->getPrivateMethod($this->ticketController, 'searchUser', [$number]);
		$this->assertEquals($user1->id, $result->id);

		// or will return user if number found as phone_number
		$user2 = factory(User::class)->create(['country_code' => '91', 'phone_number' => '8233077133']);
		$number2 =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077133']);
		$result2 = $this->getPrivateMethod($this->ticketController, 'searchUser', [$number2]);
		$this->assertEquals($user2->id, $result2->id);
	}

	/**
	 * @test
	 * @group searchUser
	 */
	public function check_searchUser_return_null_even_if_user_with_given_number_exist_but_role_does_not_match_in_given_array_of_roles()
	{
		$user1 = factory(User::class)->create(['country_code' => '91', 'mobile' => '8233077144']);
		$number =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077144']);
		$result = $this->getPrivateMethod($this->ticketController, 'searchUser', [$number, ['user']]);
		$this->assertEquals(null, $result);
	}

	/**
	 * @test
	 * @group searchUser
	 */
	public function check_searchUser_returns_user_object_if_user_with_given_role_and_number_is_found()
	{
		$user1 = factory(User::class)->create(['country_code' => '91', 'mobile' => '8233077144', 'role' => 'user']);
		$number =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077144']);
		$result = $this->getPrivateMethod($this->ticketController, 'searchUser', [$number, ['user']]);
		$this->assertEquals($user1->id, $result->id);
	}

	/**
	 * @test
	 * @group searchUser
	 */
	public function check_searchUser_returns_user_object_irrespective_of_role_if_user_with_given_number_is_found_and_role_is_not_passed()
	{
		$user1 = factory(User::class)->create(['country_code' => '91', 'mobile' => '8233077144', 'role' => 'user']);
		$number =  $this->getPrivateMethod($this->ticketController, 'formatNumber', ['+918233077144']);
		$result = $this->getPrivateMethod($this->ticketController, 'searchUser', [$number]);
		$this->assertEquals($user1->id, $result->id);
	}

	/**
	 * @test
	 * @group assignTo
	 */
	public function check_assignTo_return_null_if_agent_or_admin_with_given_number_does_not_exist()
	{
		$agentId = $this->getPrivateMethod($this->ticketController, 'assignTo', ['+918233077144']);
		$this->assertEquals(null, $agentId);

		//will not be found as country code will not match with given number's country code
		factory(User::class)->create(['country_code' => '86', 'mobile' => '8233077144', 'role' => 'admin']);
		$agentId = $this->getPrivateMethod($this->ticketController, 'assignTo', ['+918233077144']);
		$this->assertEquals(null, $agentId);
	}

	/**
	 * @test
	 * @group assignTo
	 */
	public function check_assignTo_return_if_of_user_if_agent_or_admin_with_given_number_exists()
	{
		//will not be found as country code will not match with given number's country code
		$admin = factory(User::class)->create(['country_code' => '91', 'mobile' => '8233077144', 'role' => 'admin']);
		$adminId = $this->getPrivateMethod($this->ticketController, 'assignTo', ['+918233077144']);
		$this->assertEquals($admin->id, $adminId);

		$agent = factory(User::class)->create(['country_code' => '91', 'mobile' => '8233077133', 'role' => 'admin']);
		$agentId = $this->getPrivateMethod($this->ticketController, 'assignTo', ['+918233077133']);
		$this->assertEquals($agent->id, $agentId);
	}

	/**
	 *
	 * @test
	 * @group getRequesterDetails
	 */
	public function check_getRequesterDetails_reutrns_details_of_existing_user_if_prop_callLog_contains_call_from_as_number_of_existing_user_with_any_role()
	{
		$user1 = factory(User::class)->create(['country_code' => '91', 'mobile' => '8233077144', 'role' => 'agent']);
		$this->setPrivateProperty($this->ticketController, 'callLog', ['call_from' => '8233077144']);
		$requester1 = $this->getPrivateMethod($this->ticketController, 'getRequesterDetails');
		$this->assertEquals($user1->id, $requester1['id']);

		$user2 = factory(User::class)->create(['country_code' => '91', 'mobile' => '8233077122', 'role' => 'user']);
		$this->setPrivateProperty($this->ticketController, 'callLog', ['call_from' => '8233077122']);
		$requester2 = $this->getPrivateMethod($this->ticketController, 'getRequesterDetails');
		$this->assertEquals($user2->id, $requester2['id']);
	}

	/**
	 *
	 * @test
	 * @group getRequesterDetails
	 */
	public function check_getRequesterDetails_reutrns_details_as_new_user_with_number_as_their_username__if_prop_callLog_contains_call_from_new_number()
	{
		$this->setPrivateProperty($this->ticketController, 'callLog', ['call_from' => '8233077144']);
		$requester = $this->getPrivateMethod($this->ticketController, 'getRequesterDetails');
		$this->assertEquals(null, $requester['id']);
		$this->assertEquals("8233077144", $requester['username']);
	}

	/**
	 * @group formattedTime
	 * @test
	 */
	public function check_formattedTime_returns_null_if_given_string_is_not_in_empty_or_null()
	{
		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', ['']);
		$this->assertEquals(null, $result);

		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', [null]);
		$this->assertEquals(null, $result);

		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', ['manish']);
		$this->assertEquals(null, $result);

		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', ['2020/02/3manish']);
		$this->assertEquals(null, $result);
	}

	/**
	 * @group formattedTime
	 * @test
	 */
	public function check_formattedTime_returns_utc_date_time_after_converting_given_timestring()
	{
		// timestring with timezone will be converted to UTC
		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', ['2020/02/3 00:00:00+5:30']);
		$this->assertEquals("2020-02-02 18:30:00", $result);

		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', ['2020/02/3 00:00:00+0:00']);
		$this->assertEquals("2020-02-03 00:00:00", $result);

		// timestring without timezone will remain the same
		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', ['2020/02/3 00:00:00']);
		$this->assertEquals("2020-02-03 00:00:00", $result);

		// different date format will be converted to YYYY-mm-dd H:i:s 
		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', ['2nd Feb 2020']);
		$this->assertEquals("2020-02-02 00:00:00", $result);

		$result = $this->getPrivateMethod($this->ticketController, 'formattedTime', ['1580601600']);
		$this->assertEquals("2020-02-02 00:00:00", $result);
	}

	/**
	 * @test
	 * @group getRecentTicketOfUser
	 */
	public function check_getRecentTicketOfUser_updates_recentTickets_array_to_contain_only_5_recently_updated_tickets_of_the_user_with_open_status()
	{
		$recentTickets = null;
		$user = factory(User::class)->create();
		$tickets = factory(Tickets::class, 10)->create(['user_id' => $user->id, 'status' => 1]);
		foreach ($tickets as $ticket) {
			factory(Ticket_Thread::class)->create(['ticket_id' => $ticket->id]);
		}
		$this->getPrivateMethod($this->ticketController, 'getRecentTicketOfUser', [$user->id, &$recentTickets]);
		$this->assertEquals(5, count($recentTickets));
	}

	/**
	 * @test
	 * @group getRecentTicketOfUser
	 */
	public function check_getRecentTicketOfUser_will_not_updaterecentTickets_array_if_user_does_not_have_any_recently_updated_ticket_with_open_status()
	{
		$recentTickets = null;
		$user = factory(User::class)->create();
		$tickets = factory(Tickets::class, 10)->create(['user_id' => $user->id, 'status' => 2]);
		foreach ($tickets as $ticket) {
			factory(Ticket_Thread::class)->create(['ticket_id' => $ticket->id]);
		}
		$this->getPrivateMethod($this->ticketController, 'getRecentTicketOfUser', [$user->id, &$recentTickets]);
		$this->assertEquals(null, $recentTickets);
	}

	/**
	 * @test
	 * @group getRecentTicketOfUser
	 */
	public function check_getRecentTicketOfUser_will_not_updaterecentTickets_array_if_user_does_not_exist()
	{
		$recentTickets = null;
		$this->getPrivateMethod($this->ticketController, 'getRecentTicketOfUser', [132, &$recentTickets]);
		$this->assertEquals(null, $recentTickets);
	}

	/**
	 * @test
	 * @group getCallerDetails
	 */
	public function check_getCallerDetails_return_array_containing_user_detail_available_in_caller_prop_linked_and_recent_tickets()
	{
		$this->setPrivateProperty($this->ticketController,'caller',['id'=>234]);
		$result = $this->getPrivateMethod($this->ticketController, 'getCallerDetails', []);
		// dd($result);
		$this->assertTrue(array_key_exists('linked_ticket', $result));
		$this->assertTrue(array_key_exists('recent_tickets', $result));
	}

	/**
	 * @test
	 * @group getCallerDetails
	 */
	public function check_array_returned_by_getCallerDetails_will_have_linked_ticket_as_null_if_ticket_is_not_passed_while_method_calling()
	{
		$this->setPrivateProperty($this->ticketController,'caller',['id'=>234]);
		$result = $this->getPrivateMethod($this->ticketController, 'getCallerDetails', []);
		$this->assertTrue($result['linked_ticket'] == null);
	}

	/**
	 * @test
	 * @group getCallerDetails
	 */
	public function check_array_returned_by_getCallerDetails_will_have_linked_ticket_as_array_if_ticket_is_passed_while_method_calling()
	{
		$user = factory(User::class)->create();
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		factory(Ticket_Thread::class)->create(['ticket_id' => $ticket->id]);
		$this->setPrivateProperty($this->ticketController,'caller',['id'=>$user->id]);
		$result = $this->getPrivateMethod($this->ticketController, 'getCallerDetails', [$ticket]);
		$this->assertTrue($result['linked_ticket'] != null);
		$this->assertTrue(is_array($result['linked_ticket']));
	}

	/**
	 * @test
	 * @group getCallerDetails
	 */
	public function check_array_returned_by_getCallerDetails_will_have_recent_tickets_as_null_if_user_does_not_have_any_open_tickets()
	{
		$user = factory(User::class)->create();
		$this->setPrivateProperty($this->ticketController,'caller',['id'=>$user->id]);
		$result = $this->getPrivateMethod($this->ticketController, 'getCallerDetails', []);
		$this->assertTrue($result['recent_tickets'] == null);
	}

	/**
	 * @test
	 * @group getCallerDetails
	 */
	public function check_array_returned_by_getCallerDetails_will_have_recent_tickets_as_array_if_user_have__open_tickets()
	{
		$user = factory(User::class)->create();
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		factory(Ticket_Thread::class)->create(['ticket_id' => $ticket->id]);
		$this->setPrivateProperty($this->ticketController,'caller',['id'=>$user->id]);
		$result = $this->getPrivateMethod($this->ticketController, 'getCallerDetails', [$ticket]);
		$this->assertTrue($result['recent_tickets'] != null);
		$this->assertTrue(is_array($result['recent_tickets']));
	}

	/**
	 * @test
	 * @group getCallerDetails
	 */
	public function check_array_returned_by_getCallerDetails_will_have_ticket_passed_as_argument_in_both_recent_tickets_and_linked_tickets_if_ticket_is_in_open_status()
	{
		$user = factory(User::class)->create();
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		factory(Ticket_Thread::class)->create(['ticket_id' => $ticket->id]);
		$this->setPrivateProperty($this->ticketController,'caller',['id'=>$user->id]);
		$result = $this->getPrivateMethod($this->ticketController, 'getCallerDetails', [$ticket]);
		$this->assertEquals($result['linked_ticket']['id'], $result['recent_tickets'][0]['id']);
	}

	/**
	 * @test
	 * @group saveCallDetails
	 */
	public function check_saveCallDetails_creates_new_entry_in_telephony_call_logs_with_request_data_for_new_call_id_and_provider()
	{
		$callId = str_random(32);
		$requestData = [
			'call_from'   => '8233077144',
            'call_to'     => '100',
            'connecting_to' => '8233077133',
            'call_start_date'   => now(),
            'call_end_date'   => now(),
            'intended_department_id' => null,
            'intended_helptopic_id' => null,
            'provider_id' => 1,
            'recording' => null
		];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->assertEquals(0, TelephonyLog::count());
		$this->getPrivateMethod($this->ticketController, 'saveCallDetails',[$callId, $requestData]);
		$this->assertEquals(1, TelephonyLog::count());
	}

	/**
	 * @test
	 * @group saveCallDetails
	 */
	public function check_saveCallDetails_updates_existing_entry_in_telephony_call_logs_with_request_data_for_same_old_provider_and_call_id()
	{
		$callId = str_random(32);
		$requestData = [
			'call_from'   => '8233077144',
            'call_to'     => '100',
            'connecting_to' => '8233077133',
            'call_start_date'   => now(),
            'call_end_date'   => now(),
            'intended_department_id' => null,
            'intended_helptopic_id' => null,
            'provider_id' => 1,
            'recording' => null
		];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->assertEquals(0, TelephonyLog::count());
		$this->getPrivateMethod($this->ticketController, 'saveCallDetails',[$callId, $requestData]);
		$this->assertEquals(1, TelephonyLog::count());
		$user = factory(User::class)->create();
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$requestData = [
			'call_ticket_id' => $ticket->id
		];
		$this->assertEquals(null, TelephonyLog::where('call_id', $callId)->first()->call_ticket_id);
		$this->getPrivateMethod($this->ticketController, 'saveCallDetails',[$callId, $requestData]);
		$this->assertEquals($ticket->id, TelephonyLog::where('call_id', $callId)->first()->call_ticket_id);
	}

	/**
	 * @test
	 * @group initiateCallLog
	 */
	public function check_initiateCallLog_does_not_create_record_in_telephony_call_logs_if_createLog_is_false()
	{
		$this->requestKeys = ['a','b','c','d','e','f','g'];
		$this->requestArray = ['a' => 1,'b' => 1,'c' => 1,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$this->getPrivateMethod($this->ticketController, 'initiateCallLog', ['sadad',$this->requestKeys,false]);
		$this->assertEquals(0, TelephonyLog::count());
	}

	/**
	 * @test
	 * @group initiateCallLog
	 */
	public function check_initiateCallLog_creates_record_in_telephony_call_logs_if_createLog_is_true()
	{
		$this->requestKeys = ['a','b','c','d','e','f','g'];
		$this->requestArray = ['a' => 1,'b' => 1,'c' => 1,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$this->getPrivateMethod($this->ticketController, 'initiateCallLog', ['sadad',$this->requestKeys,false]);
		$this->assertEquals(0, TelephonyLog::count());
	}

	/**
	 * @test
	 * @group initiateCallLog
	 */
	public function check_array_returned_by_initiateCallLog_will_have_recording_as_null_if_size_of_keys_array_is_less_than_seven()
	{
		$this->requestKeys = ['a','b','c','d','e','f'];
		$this->requestArray = ['a' => 1,'b' => 1,'c' => 1,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$result = $this->getPrivateMethod($this->ticketController, 'initiateCallLog', ['sadad',$this->requestKeys,false]);
		$this->assertEquals(null, $result['recording']);
	}

	/**
	 * @test
	 * @group initiateCallLog
	 */
	public function check_array_returned_by_initiateCallLog_will_have_recording_as_not_null_if_size_of_keys_array_is_equal_to_seven()
	{
		$this->requestKeys = ['a','b','c','d','e','f', 'g'];
		$this->requestArray = ['a' => 1,'b' => 1,'c' => 1,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$result = $this->getPrivateMethod($this->ticketController, 'initiateCallLog', ['sadad',$this->requestKeys,false]);
		$this->assertNotEquals(null, $result['recording']);
	}

	/**
	 * @test
	 * @group handleTicketConversion 
	 */
	public function check_handleTicketConversion_will_return_null_and_not_create_ticket_if_create_log_is_passed_as_false()
	{
		$this->requestKeys = ['a','b','c','d','e','f', 'g'];
		$this->requestArray = ['a' => 8233077144,'b' => 8233077133,'c' => 8233077134,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$result = $this->getPrivateMethod($this->ticketController, 'handleTicketConversion', [$this->requestKeys, 'asdasd', false]);
		$this->assertEquals(null, $result);
		$this->assertEquals(0, Tickets::count());
	}

	/**
	 * @test
	 * @group handleTicketConversion 
	 */
	public function check_handleTicketConversion_will_return_null_and_not_create_ticket_if_create_log_is_passed_as_true_but_provider_has_conversion_waiting_time_greater_than_zero()
	{
		$this->requestKeys = ['a','b','c','d','e','f', 'g'];
		$this->requestArray = ['a' => 8233077144,'b' => 8233077133,'c' => 8233077134,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		TelephonyProvider::where('id',1)->update([
			'conversion_waiting_time' => 5
		]);
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$result = $this->getPrivateMethod($this->ticketController, 'handleTicketConversion', [$this->requestKeys, 'asdasd', true]);
		$this->assertEquals(null, $result);
		$this->assertEquals(0, Tickets::count());
	}

	/**
	 * @test
	 * @group handleTicketConversion 
	 */
	public function check_handleTicketConversion_will_create_and_return_ticket_only_when_create_log_is_passed_as_true_and_provider_has_conversion_waiting_time_as_zero()
	{
		$this->requestKeys = ['a','b','c','d','e','f', 'g'];
		$this->requestArray = ['a' => 8233077144,'b' => 8233077133,'c' => 8233077134,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$result = $this->getPrivateMethod($this->ticketController, 'handleTicketConversion', [$this->requestKeys, 'asdasd', true]);
		$this->assertNotEquals(null, $result);
		$this->assertEquals(1, Tickets::count());
		$this->assertDatabaseHas('telephony_call_logs', [
			'call_ticket_id' => $result->id
		]);
	}

	/**
	 * @test
	 * @group handleTicketConversion 
	 */
	public function check_handleTicketConversion_will_dispatch_IncomingCallEvent_with_allowed_conversion_as_true_if_create_log_is_true()
	{
		Event::fake([
			IncomingCallEvent::class
		]);
		$this->requestKeys = ['a','b','c','d','e','f', 'g'];
		$this->requestArray = ['a' => 8233077144,'b' => 8233077133,'c' => 8233077134,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$result = $this->getPrivateMethod($this->ticketController, 'handleTicketConversion', [$this->requestKeys, 'asdasd', true]);
		Event::assertDispatched(IncomingCallEvent::class, function($event){
			return $event->broadcastWith()['allow_ticket_conversion'] == true;
		});
	}

	/**
	 * @test
	 * @group handleTicketConversion 
	 */
	public function check_handleTicketConversion_will_dispatch_IncomingCallEvent_with_allowed_conversion_as_false_if_create_log_is_false()
	{
		Event::fake([
			IncomingCallEvent::class
		]);
		$this->requestKeys = ['a','b','c','d','e','f', 'g'];
		$this->requestArray = ['a' => 8233077144,'b' => 8233077133,'c' => 8233077134,'d' => 1,'e' => 1,'f' => 1,'g' => 1];
		$this->setPrivateProperty($this->ticketController, 'providerDetail', TelephonyProvider::find(1));
		$this->setPrivateProperty($this->ticketController, 'request', $this->requestArray );
		$result = $this->getPrivateMethod($this->ticketController, 'handleTicketConversion', [$this->requestKeys, 'asdasd', false]);
		Event::assertDispatched(IncomingCallEvent::class, function($event){
			return $event->broadcastWith()['allow_ticket_conversion'] == false;
		});
	}
	/**
	 * @test
	 * @group handleTicket
	 */
	public function check_handleTicket_creates_new_ticket_for_call_log_if_ticket_id_is_not_passed()
	{
		$this->assertEquals(0, Tickets::count());
		$result = $this->getPrivateMethod($this->ticketController, 'handleTicket', []);
		$this->assertEquals(1, Tickets::count());
	}

	/**
	 * @test
	 * @group handleTicket
	 */
	public function check_handleTicket_will_link_existing_ticket_with_call_intstead_of_creating_new_ticket_if_ticket_id_is_passed()
	{
		$user = factory(User::class)->create();
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$this->assertEquals(1, Tickets::count());
		$this->assertEquals(0, Ticket_Thread::count());
		$this->setPrivateProperty($this->ticketController, 'caller', ['id' => $user->id]);
		$result = $this->getPrivateMethod($this->ticketController, 'handleTicket', [$ticket->id]);
		$this->assertEquals(1, Tickets::count());
		$this->assertEquals(1, Ticket_Thread::count());
		$this->assertEquals($ticket->id, $result->id);
	}

	/**
	 * @test
	 * @group convertCallLogIntoTicket
	 */
	public function check_convertCallLogIntoTicket_returns_error_when_trying_to_link_already_linked_call_to_new_or_another_ticket()
	{
		$user = factory(User::class)->create();
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$log = factory(TelephonyLog::class)->create(['call_ticket_id' => $ticket->id]);
		$response = $this->ticketController->convertCallLogIntoTicket($log, new Request);
		$this->assertEquals(400, $response->status());

		$request = new Request;
		$request->merge(['link_ticket' => '100']);
		$response = $this->ticketController->convertCallLogIntoTicket($log, $request);
		$this->assertEquals(400, $response->status());
	}

	/**
	 * @test
	 * @group convertCallLogIntoTicket
	 */
	public function check_convertCallLogIntoTicket_will_link_and_add_call_log_note_to_new_ticket_if_log_is_not_linked_with_the_ticket()
	{
		$user = factory(User::class)->create();
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$log = factory(TelephonyLog::class)->create();
		$request = new Request;
		$response = $this->ticketController->convertCallLogIntoTicket($log, $request);
		$this->assertEquals(200, $response->status());
	}

	/**
	 * @test
	 * @group convertCallLogIntoTicket
	 */
	public function check_convertCallLogIntoTicket_will_link_and_add_call_log_note_to_given_ticket_if_given_ticket_is_different_and_log_is_not_linked_with_the_ticket()
	{
		$user = factory(User::class)->create(['mobile' => 8233077144, 'country_code'=>91]);
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$log = factory(TelephonyLog::class)->create(['call_from'=>8233077144]);
		$request = new Request;
		$request->merge(['link_ticket' => $ticket->id]);
		$response = $this->ticketController->convertCallLogIntoTicket($log, $request);
		$this->assertEquals(200, $response->status());
	}

	/**
	 * @test
	 * @group convertCallLogIntoTicket
	 */
	public function check_convertCallLogIntoTicket_will_link_and_add_note_to_call_log_to_new_given_ticket_if_log_is_not_linked_with_the_ticket()
	{
		$user = factory(User::class)->create(['mobile' => 8233077144, 'country_code'=>91]);
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$log = factory(TelephonyLog::class)->create(['call_from'=>8233077144]);
		$request = new Request;
		$request->merge(['link_ticket' => $ticket->id]);
		$response = $this->ticketController->convertCallLogIntoTicket($log, $request);
		$this->assertEquals(200, $response->status());
	}

	/**
	 * @test
	 * @group convertCallLogIntoTicket
	 */
	public function check_convertCallLogIntoTicket_will_link_and_add_call_log_note_to_given_ticket_if_given_ticket_is_same_as_existing_linked_ticket_with_the_log()
	{
		$user = factory(User::class)->create(['mobile' => 8233077144, 'country_code'=>91]);
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$log = factory(TelephonyLog::class)->create(['call_from'=>8233077144, 'call_ticket_id' => $ticket->id]);
		$request = new Request;
		$request->merge(['link_ticket' => $ticket->id]);
		$response = $this->ticketController->convertCallLogIntoTicket($log, $request);
		$this->assertEquals(200, $response->status());
	}
}
