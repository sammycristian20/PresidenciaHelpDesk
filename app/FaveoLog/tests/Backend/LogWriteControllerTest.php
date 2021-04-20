<?php

namespace App\FaveoLog\Tests\Backend;

use App\Exceptions\NonLoggableException;
use App\FaveoLog\Model\CronLog;
use App\FaveoLog\Model\ExceptionLog;
use App\FaveoLog\Model\LogCategory;
use App\FaveoLog\Model\MailLog;
use App\Model\helpdesk\Ticket\Tickets;
use App\Structure\Mail;
use App\User;
use Carbon\Carbon;
use Config;
use Exception;
use Logger;
use Tests\DBTestCase;

class LogWriteControllerTest extends DBTestCase
{

    /** @group cronLog */
    public function test_cronLog_forSuccess()
    {
        $time = date('Y-m-d H:i:s');
        $category = LogCategory::create(['name' => 'test_category']);
        Logger::cron("ticket:fetch", "test description");
        $cronLog = CronLog::first();
        $this->assertEquals('ticket:fetch', $cronLog->command);
        $this->assertEquals('test description', $cronLog->description);
        $this->assertEquals(0, Carbon::parse($cronLog->start_time)->diffInMinutes($time));
        $this->assertEquals("running", $cronLog->status);
    }

    /** @group exceptionLog */
    public function test_exceptionLog_whenPassedExceptionIsAnInstanceNonLoggableException_shouldNotLogThatException()
    {
        $exception = new NonLoggableException('test_exception');
        $category = LogCategory::create(['name' => 'test_category']);
        Logger::exception($exception, $category->name);
        $this->assertNull($category->exception()->first());
    }

    /** @group exceptionLog */
    public function test_exceptionLog_whenPassedExceptionIsNotAnInstanceNonLoggableException_shouldLogThatException()
    {
        $exception = new Exception('test_exception');
        $category = LogCategory::create(['name' => 'test_category']);
        Logger::exception($exception, $category->name);
        $exceptionLog = $category->exception()->first();
        $this->assertEquals('test_exception', $exceptionLog->message);
    }

    /** @group fetchedMailLog */
    public function test_fetchedMailLog_forSuccess()
    {
        $category = LogCategory::where('name', 'mail-fetch')->first();
        $mail = new Mail();
        $mail->from = ["test@sender.com" =>"test_sender"];
        $mail->to = "test_reciever";
        $mail->subject = "subject";
        $mail->rawBody = "body";
        $mail->messageId = "message_id";
        Logger::fetchedMail($mail, 'test_email');
        $fetchedMailLog = $category->mail()->first();
        $this->assertEquals("test@sender.com", $fetchedMailLog->sender_mail);
        $this->assertEquals('test_email', $fetchedMailLog->reciever_mail);
        $this->assertEquals('subject', $fetchedMailLog->subject);
        $this->assertEquals('body', $fetchedMailLog->body);
        $this->assertEquals('mail', $fetchedMailLog->referee_type);
        $this->assertEquals('mail', $fetchedMailLog->source);
        $this->assertEquals('message_id', $fetchedMailLog->referee_id);
        $this->assertEquals("pending", $fetchedMailLog->status);
    }

    /** @group fetchedMailAccepted */
    public function test_fetchedMailAccepted_whenCalledWithValidLogId_shouldUpdateStatusOfTheMailTo()
    {
        $category = LogCategory::where('name', 'mail-fetch')->first();
        $mail = new Mail();
        $mail->from = ["test@sender.com" =>"test_sender"];
        $mail->to = "test_reciever";
        $mail->subject = "subject";
        $mail->rawBody = "body";
        $mail->messageId = "message_id";
        $logId = Logger::fetchedMail($mail, 'test_email')->id;
        Logger::fetchedMailAccepted($logId);
        $fetchedMailLog = $category->mail()->first();
        $this->assertEquals("accepted", $fetchedMailLog->status);
        $this->assertNull($fetchedMailLog->exception_log_id);
    }

    /** @group fetchedMailRejected */
    public function test_fetchedMailRejected_whenCalledWithValidLogId_shouldUpdateStatusOfTheMailTo()
    {
        $category = LogCategory::where('name', 'mail-fetch')->first();
        $mail = new Mail();
        $mail->from = ["test@sender.com" =>"test_sender"];
        $mail->to = "test_reciever";
        $mail->subject = "subject";
        $mail->rawBody = "body";
        $mail->messageId = "message_id";
        $logId = Logger::fetchedMail($mail, 'test_email')->id;
        Logger::fetchedMailRejected($logId, new Exception("test exception"));
        $fetchedMailLog = $category->mail()->first();
        $this->assertEquals("rejected", $fetchedMailLog->status);
        $this->assertNotNull($fetchedMailLog->exception_log_id);
        $this->assertEquals("test exception", ExceptionLog::find($fetchedMailLog->exception_log_id)->message);
    }

    /** @group mailLog */
    public function test_mailLog_forSuccess()
    {
        $userId = factory(User::class)->create()->id;
        Logger::sentMail('test_sender', 'test_email', [], 'subject', 'body', $userId, 'user', '', '', 'mail');
        $category = LogCategory::where('name', 'default')->first();
        $sentMailLog = $category->mail()->first();
        $this->assertEquals('test_sender', $sentMailLog->sender_mail);
        $this->assertEquals('test_email', $sentMailLog->reciever_mail);
        $this->assertEquals('subject', $sentMailLog->subject);
        $this->assertEquals('body', $sentMailLog->body);
        $this->assertEquals('user', $sentMailLog->referee_type);
        $this->assertEquals('mail', $sentMailLog->source);
        $this->assertEquals($userId, $sentMailLog->referee_id);
    }

    /** @group outgoingMailSent */
    public function test_outgoingMailSent_whenCalledWithValidLogId_marksOutgoingMailAsSent()
    {
        $userId = factory(User::class)->create()->id;
        $logId = Logger::sentMail('test_sender', 'test_email', [], 'subject', 'body', $userId, 'user', '', '', 'mail')->id;
        Logger::outgoingMailSent($logId);
        $this->assertEquals("sent", MailLog::find($logId)->status);
        $this->assertNull(MailLog::find($logId)->exception_log_id);
    }

    /** @group outgoingMailFailed */
    public function test_outgoingMailFailed_whenCalledWithValidLogId_marksOutgoingMailAsSent()
    {
        $userId = factory(User::class)->create()->id;
        $logId = Logger::sentMail('test_sender', 'test_email', [], 'subject', 'body', $userId, 'user', '', '', 'mail')->id;
        Logger::outgoingMailFailed($logId, new Exception("test exception"));
        $log = MailLog::find($logId);
        $this->assertEquals("failed", $log->status);
        $this->assertNotNull($log->exception_log_id);
        $this->assertEquals("test exception", ExceptionLog::find($log->exception_log_id)->message);
    }

    /** @group outgoingMailFailed */
    public function test_outgoingMailFailed_whenCalledWithValidLogIdAndExceptionExistsAlready_shouldDeleteThatExceptionAndLogTheNewOne()
    {
        $userId = factory(User::class)->create()->id;
        $logId = Logger::sentMail('test_sender', 'test_email', [], 'subject', 'body', $userId, 'user', '', '', 'mail')->id;
        Logger::outgoingMailFailed($logId, new Exception("test exception one"));
        Logger::outgoingMailFailed($logId, new Exception("test exception two"));
        $log = MailLog::find($logId);
        $this->assertEquals("failed", $log->status);
        $this->assertNotNull($log->exception_log_id);
        $this->assertEquals("test exception two", ExceptionLog::find($log->exception_log_id)->message);
    }

    /** @group logMailByCategory */
    public function test_logMailByCategory_forEmptyTemplate()
    {
        Logger::logMailByCategory('test@from.com', 'test@to.com', [], 'test_subject', 'test_body', []);
        $this->assertEquals(1, MailLog::count());
        $this->assertEquals('diagnostics', MailLog::first()->referee_type);
    }

    /** @group logMailByCategory */
    public function test_logMailByCategory_forNonArrayTemplate()
    {
        Logger::logMailByCategory('test@from.com', 'test@to.com', [], 'test_subject', 'test_body', '');
        $this->assertEquals(1, MailLog::count());
        $this->assertEquals('diagnostics', MailLog::first()->referee_type);
    }

    /** @group logMailByCategory */
    public function test_logMailByCategory_forNonEmptyTemplateButNoValidEntry()
    {
        Logger::logMailByCategory('test@from.com', 'test@to.com', [], 'test_subject', 'test_body', ['test_key' => 'test_value']);
        $this->assertEquals(MailLog::count(), 1);
        $this->assertEquals(MailLog::first()->referee_type, '');
    }

    /** @group logMailByCategory */
    public function test_logMailByCategory_whenValidTicketNumberIsPresentInTheTemplate()
    {
        $ticket = factory(Tickets::class)->create();
        Logger::logMailByCategory('test@from.com', 'test@to.com', [], 'test_subject', 'test_body', ['ticket_number' => $ticket->ticket_number]);
        $this->assertEquals(MailLog::count(), 1);
        $this->assertEquals('ticket', MailLog::first()->referee_type);
        $ticketUrl = Config::get('app.url') . '/thread' . "/" . $ticket->id;
        $refereeId = "<a href=" . $ticketUrl . " target=_blank>" . $ticket->ticket_number . "</a>";
        $this->assertEquals($refereeId, MailLog::first()->referee_id);
    }

    /** @group logMailByCategory */
    public function test_logMailByCategory_whenInvalidTicketNumberIsPresentInTheTemplate()
    {
        Logger::logMailByCategory('test@from.com', 'test@to.com', [], 'test_subject', 'test_body', ['ticket_number' => 'wrong_ticket_number']);
        $this->assertEquals(1, MailLog::count());
        $this->assertEquals('ticket', MailLog::first()->referee_type);
        $this->assertEquals('invalid_ticket_number', MailLog::first()->referee_id);
    }

    /** @group logMailByCategory */
    public function test_logMailByCategory_whenValidNewUserEmailIsPresentInTheTemplate()
    {
        $user = factory(User::class)->create(['email' => 'test@user.com']);
        Logger::logMailByCategory('test@from.com', 'test@to.com', [], 'test_subject', 'test_body', ['new_user_email' => $user->email]);
        $this->assertEquals(1, MailLog::count());
        $this->assertEquals('user', MailLog::first()->referee_type);
        $userUrl = Config::get('app.url') . '/user' . "/" . $user->id;
        $refereeId = "<a href=" . $userUrl . " target=_blank>" . $user->email . "</a>";
        $this->assertEquals($refereeId, MailLog::first()->referee_id);
    }

    /** @group logMailByCategory */
    public function test_logMailByCategory_whenInValidNewUserEmailIsPresentInTheTemplate()
    {
        Logger::logMailByCategory('test@from.com', 'test@to.com', [], 'test_subject', 'test_body', ['new_user_email' => 'wrong_user_email']);
        $this->assertEquals(1, MailLog::count());
        $this->assertEquals('user', MailLog::first()->referee_type);
        $this->assertEquals('invalid_user', MailLog::first()->referee_id);
    }

    public function test_cronFailed_shouldMarkCronAsFailedAndLinkPassedExceptionWhenCalled()
    {
        $time = date('Y-m-d H:i:s');
        $category = LogCategory::create(['name' => 'test_category']);
        $cronLogId = Logger::cron($time, $time, $category->name, 'message')->id;
        Logger::cronFailed($cronLogId, new Exception("test exception"));
        $cronLog = CronLog::find($cronLogId);
        $this->assertEquals("failed", $cronLog->status);
        $this->assertEquals("test exception", $cronLog->exception->message);
    }

    public function test_cronSuccess_shouldMarkCronAsCompleted()
    {
        $time = date('Y-m-d H:i:s');
        $category = LogCategory::create(['name' => 'test_category']);
        $cronLogId = Logger::cron($time, $time, $category->name, 'message')->id;
        Logger::cronCompleted($cronLogId);
        $cronLog = CronLog::find($cronLogId);
        $this->assertEquals("completed", $cronLog->status);
    }
}
