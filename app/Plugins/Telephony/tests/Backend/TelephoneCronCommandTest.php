<?php
namespace App\Plugins\Telephony\Tests\Backend;

use Tests\AddOnTestCase;
use App\Plugins\Telephony\Model\TelephonyLog;
use App\Plugins\Telephony\Model\TelephonyProvider;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use App\Plugins\Telephony\Jobs\TicketConversionJob;

/**
 * Test class to check cron command functionality to convert logged calls
 * to tickets. Agents can miss call alerts and convert the tickets for calls
 * - When they picked up the calls but they are not in front of their system
 * - When call frequency is high and agents are not able to convert calls into
 *   tickets within the time before popup alert disappears
 * - When miss call logging is allowed and agents are not avaiable for picking
 *   calls
 * In all above cases call logs must be reflected as tickets in the system so to
 * ensure correct syncing between IVR provider and Faveo reports. It also helps
 * agents to listen back the conversation of the calls which they picked during
 * the time when they could not access the system
 *
 * So we run this cron which will convert the calls into tickets only after
 * call conversion waiting time has passed and there is no activity by agents for 
 * such calls. Running this command will dispatch jobs on queue for ticket conversion.
 *
 * Since there was not any action taken on the alert popup of the call so there must
 * not be any notes on the log so running the cron command will not add any note on
 * tickets.
 *
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @package App\Plugins\Telephony\Tests\Backend
 */
class TelephoneCronCommandTest extends AddOnTestCase
{
	public function setUp():void
	{
		parent::setUp();
		//setting conversion_waiting_time for five minutes
		TelephonyProvider::where('id',1)->update(['conversion_waiting_time'=>5]);
	}
	/**
	 *====================================================================
	 *                      helpers
	 *====================================================================
	 */
	private function assertTicketCount(int $count)
	{
		$this->assertModelCount(Tickets::class, $count);
	}

	private function assertThreadCount(int $count)
	{
		$this->assertModelCount(Ticket_Thread::class, $count);
	}

	private function assertModelCount(string $model, int $count)
	{
		$this->assertEquals($count, $model::count());
	}

	private function createTickets($count = 1)
	{
		factory(Tickets::class, $count)->create();
	}

	private function createCallLogs($count = 1, $modifyData=[])
	{
		factory(TelephonyLog::class, $count)->create($modifyData);
	}

	/**
	 *====================================================================
	 *                      tests
	 *====================================================================
	 */
	/**
	 * @test
	 * @group telephonycron
	 */
	public function test_running_cron_on_empty_logs_will_not_have_any_side_effect_on_tickets()
	{
		$this->createTickets(5);
		Artisan::call('telephony:convert');
		$this->assertTicketCount(5);
	}

	/**
	 * @test
	 * @group telephonycron
	 */
	public function test_when_cron_will_run_it_will_not_create_tickets_for_logs_which_were_created_with_conversion_waiting_time()
	{
		$this->createCallLogs(2,['created_at' => Carbon::now()->subMinutes(2)]);
		Artisan::call('telephony:convert');
		$this->assertTicketCount(0);
	}

	/**
	 * @test
	 * @group telephonycron
	 */
	public function test_when_cron_will_run_it_will_create_tickets_for_logs_which_were_created_with_conversion_waiting_time_and_do_not_have_any_linked_ticket()
	{
		$this->createCallLogs(2,['created_at' => Carbon::now()->subMinutes(2)]);
		$this->createCallLogs(2,['created_at' => Carbon::now()->subMinutes(10)]);
		Artisan::call('telephony:convert');
		$this->assertTicketCount(2);
		$this->assertThreadCount(2);
		//running the same again will not have any side effect
		Artisan::call('telephony:convert');
		$this->assertTicketCount(2);
		$this->assertThreadCount(2);
	}

	/**
	 * @test
	 * @group telephonycron
	 */
	public function test_when_cron_will_run_it_will_create_tickets_for_logs_which_have_been_processed_for_job_creation()
	{
		$this->createCallLogs(1,['created_at' => Carbon::now()->subMinutes(10)]);
		Artisan::call('telephony:convert');
		Artisan::call('telephony:convert');
		$this->assertTicketCount(1);
		$this->assertThreadCount(1);
	}

	/**
	 * @test
	 * @group telephonycron
	 */
	public function test_when_cron_runs_it_pushes_ticketConversionJob_on_queue()
	{
		Queue::fake();
		$this->createCallLogs(5,['created_at' => Carbon::now()->subMinutes(10)]);
		Artisan::call('telephony:convert');
		Queue::assertPushed(TicketConversionJob::class, 5);
		//running the command again will not push new jobs
		Artisan::call('telephony:convert');
		Queue::assertPushed(TicketConversionJob::class, 5);
	}
}
