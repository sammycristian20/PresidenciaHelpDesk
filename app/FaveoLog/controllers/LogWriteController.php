<?php

namespace App\FaveoLog\controllers;

use App\Exceptions\NonLoggableException;
use App\FaveoLog\Model\MailLog;
use App\FaveoLog\Model\CronLog;
use App\FaveoLog\Model\LogCategory;
use App\Model\helpdesk\Ticket\Tickets;
use Carbon\Carbon;
use Config;
use App\User;
use Exception;
use App\Structure\Mail;

/**
 * Handles all write related operations while logging
 * NOTE: while passing any category, please make sure that this category exists in LogSeeder.php
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class LogWriteController
{
    /**
     * Logs cron related data.
     * NOTE: this is the latest version of CRON log. Old one can be removed later on
     * @param $signature
     * @param $description
     * @return CronLog
     */
    public function cron($signature, $description = "")
    {
        try {
            // if should catch and ignore exception. While updating client, first we update file-system and then database. In this case,
            // there will be old database structure, which will throw exceptions. To handle that, catch block isn't doing anything other than
            // logging
            return CronLog::create(['command' => $signature, "description"=> $description, "status"=>"running"]);

        } catch (Exception $e){

            $this->exception($e,"cron");
        }
    }

    /**
     * Marks a cron as failure
     * @param $logId
     * @param Exception|null $exception
     */
    public function cronFailed($logId, Exception $exception = null)
    {
        try{
            $cronLog = CronLog::whereId($logId)->select("id","created_at","command")->first();

            $exception = $this->exception($exception, "cron");

            $cronLog->update(["status"=>"failed", "exception_log_id"=> $exception->id, "duration"=> Carbon::now()->diffInSeconds($cronLog->created_at)]);
        } catch (Exception $e){
            $this->exception($e, "cron");
        }
    }

    /**
     * Marks a cron as success
     * @param $logId
     */
    public function cronCompleted($logId)
    {
        try{
            $cronLog = CronLog::whereId($logId)->select("id","created_at")->first();

            $cronLog->update(["status"=>"completed", "duration"=> Carbon::now()->diffInSeconds($cronLog->created_at)]);
        } catch(Exception $e){
            $this->exception($e, "cron");
        }
    }

    /**
     * Logs exception along with trace
     * @param Exception|object $e exception
     * @param string $category category to which it belongs
     * @return null
     */
    public function exception($e, $category = 'default')
    {
        try{
            if(!($e instanceof NonLoggableException)){

                $category = LogCategory::FirstOrCreate(['name'=>$category]);

                return $category->exception()->create([
                    'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(),
                    'trace' => nl2br($e->getTraceAsString())
                ]);
            }

        } catch(Exception $e){
            // ignore exception
            // Most probably this scenario will not arrive but just for fallback, since this might result is auto-update failure
        }
    }

    /**
     * Logs fetched mail
     * @param array $mail associative array of email with keys : 'from','subject','body','referee_id'
     * @param string $fetchedEmail emailId from which mail has been fetched
     * @return null
     */
    public function fetchedMail(Mail $mail, $fetchedEmail)
    {
        try{
            // category will be mail-fetch
            // message id from email_thread table, from where ticketId can be obtained. It will be null in case where
            // ticket is not created
            $category = LogCategory::where('name', 'mail-fetch')->first();

            // in case of fetched mail we need the email from whom it was fetched
            return $category->mail()->create([
                // need email from which it was fetched
                'sender_mail' => $mail->getReplyToAddress(), 'reciever_mail' => $fetchedEmail, 'subject' => $mail->subject,
                'body' => nl2br($mail->rawBody), 'referee_type' => 'mail', 'source' => 'mail', 'referee_id' => $mail->messageId,
                "collaborators"=>array_keys($mail->cc), "status"=>"pending"
            ]);
        } catch(\Exception $e){
            $this->exception($e, "mail-fetch");
        }
    }

    /**
     * Logs fetched mail
     * @param $logId
     * @return null
     */
    public function fetchedMailAccepted($logId)
    {
        MailLog::whereId($logId)->update(["status" => "accepted"]);
    }

    /**
     * Logs fetched mail
     * @param $logId
     * @param Exception $e
     * @return null
     */
    public function fetchedMailRejected($logId, Exception $e)
    {
        $exception = $this->exception($e, "mail-fetch");
        $exceptionId = $exception ? $exception->id : null;
        MailLog::whereId($logId)->update(["status" => "rejected", "exception_log_id"=> $exceptionId]);
    }

    /**
     * Logs fetched mail
     * @param $logId
     * @param Exception $e
     * @return null
     */
    public function fetchedMailBugEncountered($logId, Exception $e)
    {
        $exception = $this->exception($e, "mail-fetch");
        $exceptionId = $exception ? $exception->id : null;
        MailLog::whereId($logId)->update(["status" => "blocked", "exception_log_id"=> $exceptionId]);
    }

    /**
     * Logs mail related data
     * @param string $senderMail the person from whom mail is getting sent
     * @param string $receiverMail the person to whom mail is getting sent
     * @param array $cc
     * @param string $subject subject of the mail
     * @param string $body body of the mail
     * @param string $refereeId id of the referee. For eg. if a user gets created, it will be user_id
     *                              if a ticket gets created, it will be ticket_id
     * @param string $refereeType 'user' or 'ticket'
     * @param string $categoryName
     * @param string $status
     * @param string $source source can be the place from which action has been initiated.
     *                              for eg. if a user is created via mail, source will be mail,
     *                              if agent-panel then agent-panel
     *                              if client-panel then client-panel
     *                              if same goes for tickets too.
     *                              One exception is ticket recur
     * @return null
     */
    public function sentMail($senderMail, $receiverMail, $cc, $subject, $body, $refereeId, $refereeType, $categoryName = '', $status = '', $source = "default")
    {
        try{
            // sender, reciever, body, messageId,
            // referee_id/type Ticket/User
            // $categoryType can be user or ticket
            // $categoryId can be
            $categoryName = $categoryName ? $categoryName : 'default';
            $category = LogCategory::where('name', $categoryName)->first();

            return $category->mail()->create([
                'sender_mail' => $senderMail, 'reciever_mail' => $receiverMail, 'subject' => $subject,
                'body' => $body, 'referee_id' => $refereeId, 'referee_type' => $refereeType, 'status' => $status, 'source' => $source,
                "collaborators"=> $cc
            ]);
        } catch (\Exception $e){
            $this->exception($e, "mail-send");
        }
    }

    /**
     * Logs mail by category by detecting if it is a mail sent for ticket
     * @param string $from the email from which mail is getting sent
     * @param string $to the email to which mail is getting sent
     * @param array $cc
     * @param string $subject Subject of the mail
     * @param string $body body of the mail
     * @param string $templateVariables it can be used to extract information regarding the mail and update in the mail
     * @param string $templateType
     * @return null
     */
    public function logMailByCategory($from, $to, $cc, $subject, $body, $templateVariables, $templateType = '')
    {
        $refereeId = '';
        $refereeType = '';

        if (!$templateVariables) {
            $refereeType = 'diagnostics';
        } else {
            if (is_array($templateVariables)) {
                if (array_key_exists('ticket_number', $templateVariables)) {
                    $this->updateRefereeDetailsForTicket($refereeId, $refereeType, $templateVariables);
                } elseif (array_key_exists('new_user_email', $templateVariables)) {
                    $this->updateRefereeDetailsForUser($refereeId, $refereeType, $templateVariables);
                }
            }
        }

        $category = $this->getLogCategoryByTemplateType($templateType);

        return $this->sentMail($from, $to, $cc, $subject, $body, $refereeId, $refereeType, $category, 'queued');
    }

    /**
     * Marks outgoing mail as sent
     * @param $logId
     */
    public function outgoingMailSent($logId)
    {
        MailLog::whereId($logId)->update(['status' => "sent"]);
    }

    /**
     * Marks outgoing mail as failed
     * @param $logId
     * @param Exception $e
     */
    public function outgoingMailFailed($logId, Exception $e)
    {
        $mailLog = MailLog::whereId($logId)->select("id", "exception_log_id")->first();

        if($mailLog->exception_log_id){
            // if already exception exists for this, should be deleted so that latest exception can be captured
            $mailLog->exception()->delete();
        }

        $exception = $this->exception($e, "cron");
        $exceptionId = $exception ? $exception->id : null;
        $mailLog->update(['status'=> "failed", "exception_log_id"=> $exceptionId]);
    }

    /**
     * updates refereeId and refereeeType for ticket
     * @param string $refereeId
     * @param string $refereeType
     * @return null
     */
    private function updateRefereeDetailsForTicket(&$refereeId, &$refereeType, $templateVariables)
    {
        $ticketNumber = $templateVariables['ticket_number'];
        $ticketId = Tickets::where('ticket_number', $templateVariables['ticket_number'])->value('id');
        $ticketUrl = Config::get('app.url') . '/thread' . "/" . $ticketId;
        $refereeId = "<a href=" . $ticketUrl . " target=_blank>" . $ticketNumber . "</a>";
        if (!$ticketId) {
            $refereeId = 'invalid_ticket_number';
        }
        $refereeType = 'ticket';
    }

    /**
     * updates refereeId and refereeeType based for user
     * @param string $refereeId
     * @param string $refereeType
     * @return null
     */
    private function updateRefereeDetailsForUser(&$refereeId, &$refereeType, $templateVariables)
    {
        $userEmail = $templateVariables['new_user_email'];
        $userId = User::where('email', $userEmail)->value('id');

        // if user id is not null then only this should happen
        $userUrl = Config::get('app.url') . '/user' . "/" . $userId;
        $refereeId = "<a href=" . $userUrl . " target=_blank>" . $userEmail . "</a>";

        if (!$userId) {
            $refereeId = 'invalid_user';
        }
        $refereeType = 'user';
    }

    /**
     * gets ticket category by ticket scenarios
     * @param string $scenario
     * @return string
     */
    private function getLogCategoryByTemplateType($scenario): string
    {
        $categoriesForTemplates = require(app_path() . DIRECTORY_SEPARATOR . 'FaveoLog' . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR . 'templateCategory.php');

        if (array_key_exists($scenario, $categoriesForTemplates)) {
            return $categoriesForTemplates[$scenario];
        }

        return 'default';
    }
}
