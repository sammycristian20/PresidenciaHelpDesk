<?php

namespace App\FaveoLog\Tests\Backend;

use App\FaveoLog\Model\CronLog;
use App\FaveoLog\Model\MailLog;
use App\FaveoLog\Model\ExceptionLog;
use App\FaveoLog\Model\LogCategory;
use App\Structure\Mail;
use Tests\DBTestCase;
use Logger;
use Exception;
use App\User;
use App\FaveoLog\controllers\LogViewController;
use App\Model\helpdesk\Ticket\EmailThread;
use App\Model\helpdesk\Ticket\Tickets;
use Carbon\Carbon;
use DB;
use App\Model\helpdesk\Email\Emails;
use Config;

class LogViewControllerTest extends DBTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->getLoggedInUserForWeb('admin');
    }

    /** @group getExceptionLogs */
    public function test_getExceptionLogs_whenNoParametersIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));
        $response = $this->call('GET', '/api/logs/exception');
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $exceptions);
        $this->assertEquals('test_exception_1', $exceptions[0]->message);
        $this->assertEquals('test_exception_2', $exceptions[1]->message);
    }

    /** @group getExceptionLogs */
    public function test_getExceptionLogs_whenSearchQueryIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));
        $response = $this->call('GET', '/api/logs/exception', ['search_query' => 'exception_1']);
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $exceptions);
        $this->assertEquals('test_exception_1', $exceptions[0]->message);
    }

    /** @group getExceptionLogs */
    public function test_getExceptionLogs_whenLimitIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));
        $response = $this->call('GET', '/api/logs/exception', ['limit' => 1]);
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $exceptions);
    }

    /** @group getExceptionLogs */
    public function test_getExceptionLogs_whenStartTimeIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));
        $response = $this->call('GET', '/api/logs/exception', ['created_at_start' => '3000-11-27 02:02:15']);
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(0, $exceptions);
    }

    /** @group getExceptionLogs */
    public function test_getExceptionLogs_whenEndTimeIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));
        $response = $this->call('GET', '/api/logs/exception', ['created_at_end' => '3000-11-27 02:02:15']);
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $exceptions);
    }

    /** @group getExceptionLogs */
    public function test_getExceptionLogs_whenSearchQueryForCategoryIsPassed()
    {
        $categoryOne = LogCategory::create(['name' => 'test_category_1']);
        $categoryTwo = LogCategory::create(['name' => 'test_category_2']);
        Logger::exception(new Exception('exception_one'), $categoryOne->name);
        Logger::exception(new Exception('exception_two'), $categoryTwo->name);
        $response = $this->call('GET', '/api/logs/exception', ['search_query' => 'test_category_1']);
        $response->assertStatus(200);
        $exceptionLog = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $exceptionLog);
        $this->assertEquals('exception_one', $exceptionLog[0]->message);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenNoParametersIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron');
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $cronLog);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenSearchQueryIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron', ['search_query' => 'fetch']);
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $cronLog);
        $this->assertEquals('fetch description', $cronLog[0]->description);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenLimitIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron', ['limit' => 1]);
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $cronLog);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenCreatedAtStartTimeIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron', ['created_at_start' => '3000-11-27 02:02:15']);
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(0, $cronLog);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenCreatedAtEndTimeIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron', ['created_at_end' => '3000-11-27 02:02:15']);
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $cronLog);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenStartTimeStartIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron', ['start_time_start' => '3000-11-27 02:02:15']);
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $cronLog);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenStartTimeEndIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron', ['start_time_end' => '3000-11-27 02:02:15']);
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $cronLog);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenEndTimeStartIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron', ['end_time_start' => '3000-11-27 02:02:15']);
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $cronLog);
    }

    /** @group getCronLogs */
    public function test_getCronLogs_whenEndTimeEndIsPassed()
    {
        Logger::cron("ticket:fetch", "fetch description");
        Logger::cron("ticket:escalate", "escalate description");
        $response = $this->call('GET', '/api/logs/cron', ['end_time_end' => '3000-11-27 02:02:15']);
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $cronLog);
    }

    /** @group getMailLogs */
    public function test_getMailLogs_whenNoParametersIsPassed()
    {
        $mail = $this->getMail();
        Logger::fetchedMail($mail, 'test_email');
        Logger::fetchedMail($mail, 'test_email');
        $response = $this->call('GET', '/api/logs/mail');
        $response->assertStatus(200);
        $cronLog = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $cronLog);
    }

    /** @group getMailLogs */
    public function test_getMailLogs_whenSearchQueryForCategoryIsPassed()
    {
        $this->getLoggedInUserForWeb('admin');
        $mail = $this->getMail();
        Logger::fetchedMail($mail, 'test_email');
        $response = $this->call('GET', '/api/logs/mail', ['search_query' => 'mail-fetch']);
        $response->assertStatus(200);
        $mailLog = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $mailLog);
        $this->assertEquals('subject', $mailLog[0]->subject);
    }

    /** @group getMailLogs */
    public function test_getMailLogs_whenLimitIsPassed()
    {
        $mail = $this->getMail();
        Logger::fetchedMail($mail, 'test_email');
        Logger::fetchedMail($mail, 'test_email');
        $response = $this->call('GET', '/api/logs/mail', ['limit' => 1]);
        $response->assertStatus(200);
        $mailLog = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $mailLog);
    }

    /** @group getMailLogs */
    public function test_getMailLogs_whenSenderMailsIsSent()
    {
        $mailOne = $this->getMail();
        $mailOne->from = ['testOne@test.com'=> "test one"];

        $mailTwo = $this->getMail();
        $mailTwo->from = ['testTwo@test.com'=> "test two"];

        Logger::fetchedMail($mailOne, 'test_email');
        Logger::fetchedMail($mailTwo, 'test_email');

        $response = $this->call('GET', '/api/logs/mail', ['sender_mails' => ['testOne@test.com']]);
        $response->assertStatus(200);
        $mailLog = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $mailLog);
        $this->assertEquals($mailLog[0]->sender_mail, 'testOne@test.com');
    }

    /** @group getMailLogs */
    public function test_getMailLogs_whenRecieverMailsIsSent()
    {
        $mailOne = $this->getMail();
        $mailTwo = $this->getMail();
        Logger::fetchedMail($mailOne, 'testOne@test.com');
        Logger::fetchedMail($mailTwo, 'testTwo@test.com');

        $response = $this->call('GET', '/api/logs/mail', ['reciever_mails' => ['testOne@test.com']]);
        $response->assertStatus(200);
        $mailLog = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $mailLog);
        $this->assertEquals($mailLog[0]->reciever_mail, 'testOne@test.com');
    }

    /** @group getLogCategoryList */
    public function test_getLogCategoryList_whenNoSearchStringIsPassed()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('GET', '/api/log-category-list');
        $response->assertStatus(200);
        $categories = json_decode($response->getContent())->data->data;
        $this->assertTrue(count($categories) > 0);
    }

    /** @group getLogCategoryList */
    public function test_getLogCategoryList_whenSearchStringIsPassed()
    {
        $this->getLoggedInUserForWeb('admin');
        LogCategory::create(['name' => 'Test Category']);
        $response = $this->call('GET', '/api/log-category-list', ['search_query' => 'Test Category']);
        $response->assertStatus(200);
        $categories = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $categories);
    }

    /** @group getMailBody */
    public function test_getMailBody_forSuccess()
    {
        $this->getLoggedInUserForWeb('admin');
        $mailLogId = MailLog::create(['body' => 'test_body', 'log_category_id' => 3, 'referee_id' => 'test'])->id;
        $response = $this->call('GET', "/api/get-log-mail-body/$mailLogId");
        $response->assertStatus(200);
        $body = json_decode($response->getContent())->data->mail_body;
        $this->assertEquals($body, 'test_body');
    }

    /** @group getUserByEmail */
    public function test_getUserByEmail_forUnregisteredUser()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('GET', "/api/get-user-by-email", ['email' => 'wrong_email']);
        $response->assertStatus(400);
    }

    /** @group getUserByEmail */
    public function test_getUserByEmail_forRegisteredUser()
    {
        $this->getLoggedInUserForWeb('admin');
        $user = factory(User::class)->create();
        $mailLogId = MailLog::create(['body' => 'test_body'])->id;
        $response = $this->call('GET', "/api/get-user-by-email", ['email' => $user->email]);
        $response->assertStatus(200);
        $userFromResponse = json_decode($response->getContent())->data->user;
        $this->assertEquals($userFromResponse->id, $user->id);
    }

    /** @group getUserByEmail */
    public function test_getUserByEmail_forSystemConfiguredMail()
    {
        $this->getLoggedInUserForWeb('admin');
        $email = Emails::create(['email_address' => 'system@mail.com', 'email_name' => 'test faveo']);
        $response = $this->call('GET', "/api/get-user-by-email", ['email' => 'system@mail.com']);
        $response->assertStatus(200);
        $userFromResponse = json_decode($response->getContent())->data->user;
        $this->assertEquals($userFromResponse->email, $email->email_address);
        $this->assertEquals($userFromResponse->name, $email->email_name);
        $this->assertEquals($userFromResponse->role, 'System Configured Mail');
        $this->assertEquals($userFromResponse->profile_link, Config::get('app.url') . '/emails/' . $email->id . '/edit');
        $this->assertEquals($userFromResponse->profile_pic, assetLink('image', 'system'));
    }

    /** @group getFetchMailResponseMessage */
    public function test_getFetchMailResponseMessage_whenNoTicketWasNotCreated()
    {
        $logViewController = new LogViewController;
        $message = $this->getPrivateMethod($logViewController, 'getFetchMailResponseMessage', ['wrong_message_id']);
        $this->assertRegexp('/No ticket found/', $message);
    }

    /** @group getFetchMailResponseMessage */
    public function test_getFetchMailResponseMessage_whenTicketIsFound()
    {
        $ticket = factory(Tickets::class)->create(['ticket_number' => 'test_ticket_number']);
        EmailThread::create(['message_id' => 'test_message_id', 'ticket_id' => $ticket->id]);
        $logViewController = new LogViewController;
        $message = $this->getPrivateMethod($logViewController, 'getFetchMailResponseMessage', ['test_message_id']);
        $this->assertRegexp("/$ticket->ticket_number/", $message);
    }

    /** @group deleteLogs */
    public function test_deleteLogs_forMailLogsAndDeleteAll()
    {
        $this->getLoggedInUserForWeb('admin');
        MailLog::create(['body' => 'test_body']);
        MailLog::create(['body' => 'test_body']);
        $this->assertEquals(MailLog::count(), 2);
        $response = $this->call('delete', '/api/delete-logs', ['categories' => ['mail'], 'delete_all' => true]);
        $response->assertStatus(200);
        $this->assertEquals(MailLog::count(), 0);
    }

    /** @group deleteLogs */
    public function test_deleteLogs_forCronLogsAndDeleteAll()
    {
        $this->getLoggedInUserForWeb('admin');
        CronLog::create();
        CronLog::create();
        $this->assertEquals(CronLog::count(), 2);
        $response = $this->call('delete', '/api/delete-logs', ['categories' => ['cron'], 'delete_all' => true]);
        $response->assertStatus(200);
        $this->assertEquals(CronLog::count(), 0);
    }

    /** @group deleteLogs */
    public function test_deleteLogs_forExceptionLogsAndDeleteAll()
    {
        $this->getLoggedInUserForWeb('admin');
        ExceptionLog::create();
        ExceptionLog::create();
        $this->assertEquals(ExceptionLog::count(), 2);
        $response = $this->call('delete', '/api/delete-logs', ['categories' => ['exception'], 'delete_all' => true]);
        $response->assertStatus(200);
        $this->assertEquals(ExceptionLog::count(), 0);
    }

    /** @group deleteLogs */
    public function test_deleteLogs_forAllLogs()
    {
        $this->getLoggedInUserForWeb('admin');
        MailLog::create();
        ExceptionLog::create();
        CronLog::create();
        $this->assertEquals(MailLog::count(), 1);
        $this->assertEquals(ExceptionLog::count(), 1);
        $this->assertEquals(CronLog::count(), 1);
        $response = $this->call('delete', '/api/delete-logs', ['categories' => ['exception', 'mail', 'cron'], 'delete_all' => true]);
        $response->assertStatus(200);
        $this->assertEquals(MailLog::count(), 0);
        $this->assertEquals(ExceptionLog::count(), 0);
        $this->assertEquals(CronLog::count(), 0);
    }

    /** @group deleteLogs */
    public function test_deleteLogs_forTimeRangeForOldRange()
    {
        $this->getLoggedInUserForWeb('admin');
        MailLog::create();
        ExceptionLog::create();
        CronLog::create();
        $this->assertEquals(MailLog::count(), 1);
        $this->assertEquals(ExceptionLog::count(), 1);
        $this->assertEquals(CronLog::count(), 1);
        $deleteBefore = Carbon::createFromTimestamp(0)->toDateTimeString();
        $response = $this->call('delete', '/api/delete-logs', ['categories' => ['exception', 'mail', 'cron'], 'delete_before' => $deleteBefore]);
        $response->assertStatus(200);
        $this->assertEquals(MailLog::count(), 1);
        $this->assertEquals(ExceptionLog::count(), 1);
        $this->assertEquals(CronLog::count(), 1);
    }

    /** @group deleteLogs */
    public function test_deleteLogs_forTimeRangeForNewRange()
    {
        $this->getLoggedInUserForWeb('admin');
        MailLog::create();
        ExceptionLog::create();
        CronLog::create();
        $this->assertEquals(MailLog::count(), 1);
        $this->assertEquals(ExceptionLog::count(), 1);
        $this->assertEquals(CronLog::count(), 1);
        $deleteBefore = Carbon::tomorrow()->toDateTimeString();
        $response = $this->call('delete', '/api/delete-logs', ['categories' => ['exception', 'mail', 'cron'], 'delete_before' => $deleteBefore]);
        $response->assertStatus(200);
        $this->assertEquals(MailLog::count(), 0);
        $this->assertEquals(ExceptionLog::count(), 0);
        $this->assertEquals(CronLog::count(), 0);
    }

    /** @group deleteLogs */
    public function test_deleteLogs_parametersDeleteBeforeAndDeleteAfter()
    {
        $this->getLoggedInUserForWeb('admin');
        DB::table('mail_logs')->insert(['created_at' => new Carbon('1993-01-01 00:00:00')]);
        DB::table('mail_logs')->insert(['created_at' => new Carbon('1995-01-01 00:00:00')]);
        $deleteBefore = new Carbon('1994-01-01 00:00:00');
        $deleteAfter = new Carbon('1992-01-01 00:00:00');

        // create logs for 1993 1995
        // and give range bw them
        $response = $this->call('delete', '/api/delete-logs', ['categories' => ['exception', 'mail', 'cron'], 'delete_before' => $deleteBefore, 'delete_after' => $deleteAfter]);
        $response->assertStatus(200);
        $this->assertEquals(MailLog::count(), 1);
        $this->assertEquals(MailLog::first()->created_at, '1995-01-01 00:00:00');
    }

    private function getMail()
    {
        $mail = new Mail();
        $mail->from = ["test@sender.com" =>"test_sender"];
        $mail->to = "test_reciever";
        $mail->subject = "subject";
        $mail->rawBody = "body";
        $mail->messageId = "message_id";
        return $mail;
    }
}
