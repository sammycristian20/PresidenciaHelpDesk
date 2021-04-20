<?php
namespace App\Plugins\Telephony\Tests\Backend;

use Tests\AddOnTestCase;
use App\Plugins\Telephony\Traits\TicketHandler;
use App\User;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Plugins\Telephony\Model\TelephonyLog;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Manage\Help_topic;

/** 
 * Test class to test functionality of TicketHandler trait.
 * TicketHandler provides helper methods and actually handles ticket 
 * creation and updation logic for the plugin. TicketHandler uses call log
 * entry for preparing data for ticket and ticket thread
 *
 * @author Manish Verma
 * @since v3.0.0
 * @package App\Plugins\Telephony\Tests\Backend
 */
class TicketHandlerTest extends AddOnTestCase
{
	use TicketHandler;

	public function setUp():void
	{
		parent::setup();
		System::where('id', 1)->update(['time_zone' => 'Asia/Kolkata']);
	}

	public function setCallLogArray($modifyData = [])
	{
		$this->callLog = factory(TelephonyLog::class)->create($modifyData);
	}

	public function setCallerArray(User $user)
	{
		$this->caller = $user->toArray();
	}

	/**
	 * @test
	 * @group ticketBody
	 */
	public function check_ticketBody_returns_missed_call_log_thread_if_recording_is_not_avaialble_in_the_log()
	{
		$this->setCallLogArray(['recording'=>null]);
		$htmlString = $this->ticketBody();
		$this->assertEquals(trans('Telephony::lang.you_missed_call'), $htmlString);
	}

	/**
	 * @test
	 * @group ticketBody
	 */
	public function check_ticketBody_returns_actual_call_log_thread_if_recording_is_avaialble_in_the_log()
	{
		$this->setCallLogArray();
		$htmlString = $this->ticketBody();
		$this->assertNotEquals(trans('Telephony::lang.you_missed_call'), $htmlString);
	}

	/**
	 * @test
	 * @group ticketBody
	 */
	public function check_ticketBody_returns_actual_call_log_thread_if_recording_is_avaialble_in_the_log_will_contain_created_at_time_according_to_system_timezone()
	{
		$inUTC = now();
		$inTz  = faveodate($inUTC);
		$this->setCallLogArray(['call_start_date'=> $inUTC]);
		$htmlString = $this->ticketBody();
		$this->assertNotEquals(trans('Telephony::lang.you_missed_call'), $htmlString);
		$this->assertStringContainsString($inTz, $htmlString);
	}

	/**
	 * @test
	 * @group linkCallToTicket
	 */
	public function check_linkCallToTicket_will_add_new_call_thread_to_given_ticket()
	{
		$user = factory(User::class)->create(['mobile' => 8233077144, 'country_code'=>91]);
		$this->setCallerArray($user);
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$this->assertEquals(0, Ticket_Thread::count());
		$ticket = $this->linkCallToTicket($ticket->id);
		$this->assertEquals(1, Ticket_Thread::count());
	}

	/**
	 * @test
	 * @group addCallNotes
	 */
	public function check_addCallNotes_will_not_add_notes_when_notes_are_available()
	{
		$user = factory(User::class)->create(['mobile' => 8233077144, 'country_code'=>91]);
		$this->setCallerArray($user);
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$this->assertEquals(0, Ticket_Thread::count());
		$ticket = $this->addCallNotes($ticket->id);
		$this->assertEquals(0, Ticket_Thread::count());
	}

	/**
	 * @test
	 * @group addCallNotes
	 */
	public function check_addCallNotes_will_add_notes_only_when_notes_are_available()
	{
		$user = factory(User::class)->create(['role'=>'admin', 'mobile' => 8233077144, 'country_code'=>91]);
		$this->setCallLogArray(['notes' => 'testing']);
		$this->agent = $user->id;
		$ticket = factory(Tickets::class)->create(['user_id' => $user->id, 'status' => 1]);
		$this->assertEquals(0, Ticket_Thread::count());
		$ticket = $this->addCallNotes($ticket->id);
		$this->assertEquals(1, Ticket_Thread::count());
	}

	/**
	 * @test
	 * @group ticketConversion
	 */
	public function check_ticketConversion_will_create_ticket_with_default_department_if_intended_department_does_not_exist_any_longer_in_the_system()
	{
		$this->setCallLogArray(['intended_department_id'=> 3]);
		Help_topic::where('id', 3)->delete();
		Department::where('id', 3)->delete();
		$ticket = $this->ticketConversion();
		$this->assertEquals(1,$ticket->dept_id);
	}

	/**
	 * @test
	 * @group ticketConversion
	 */
	public function check_ticketConversion_will_create_ticket_with_default_intended_department()
	{
		$this->setCallLogArray(['intended_department_id'=> 3]);
		$ticket = $this->ticketConversion();
		$this->assertEquals(3, $ticket->dept_id);
	}

	/**
	 * @test
	 * @group ticketConversion
	 */
	public function check_ticketConversion_will_create_ticket_with_default_helptopic_if_intended_helptopic_does_not_exist_any_longer_in_the_system()
	{
		$this->setCallLogArray(['intended_helptopic_id'=> 3]);
		Help_topic::where('id', 3)->delete();
		Department::where('id', 3)->delete();
		$ticket = $this->ticketConversion();
		$this->assertEquals(1,$ticket->help_topic_id);
	}

	/**
	 * @test
	 * @group ticketConversion
	 */
	public function check_ticketConversion_will_create_ticket_with_default_intended_helptopic()
	{
		$this->setCallLogArray(['intended_department_id'=> 3]);
		$ticket = $this->ticketConversion();
		$this->assertEquals(3, $ticket->dept_id);
	}
}
