<?php

namespace App\Http\Controllers\Common\Mail;

use App\Exceptions\MailRejectionException;
use App\Exceptions\TicketRejectionException;
use App\FaveoLog\Model\CronLog;
use App\Http\Controllers\Agent\helpdesk\TicketWorkflowController;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use App\Model\helpdesk\Settings\Email as SettingsEmail;
use App\Model\helpdesk\Ticket\EmailThread;
use App\Model\helpdesk\Ticket\Ticket_source as TicketSource;
use App\Structure\Mail;
use App\User;
use DB;
use Carbon\Carbon;
use Logger;
use Exception;

/**
 * handles all the fetch related operation while creating/updating a ticket through mail
 * It is child class of BaseMailController
 *
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class FetchMailController extends BaseMailController
{

    /**
     * System email
     * There can be multiple system mails. This will be populated with only one system email
     * and will be looped over those to keep changing it
     * @var string
     */
    private $systemEmail;

    /**
     * System mails
     * @use To avoid fetching mail from system mail, we put a check that mails coming from system mails should
     * not generate ticket
     * @var array
     */
    private $systemEmails;

    /**
     * Maximum number of configured mails which will be fetched in single CRON run
     * @var int
     */
    protected $maxMailFetchCount = 5;

    public function __construct()
    {
        // Get system emails
        $this->systemEmails = Emails::select('email_address')->pluck('email_address')->toArray();

        // setting maximum number of mails that can be fetched.
        $this->maxMailFetchCount = $this->getMaxMailFetchCount(Emails::where('fetching_status', 1)->count());
    }

    /**
     * It decides how many mails should be email configuration should be handled in one batch.
     * FOR EG.
     * - if someone has configured 10 mails, it is good to process 5 mails per CRON run
     * - if someone has configured 100 mails, it is good to process 10 mails per CRON, since those
     *      10 mails will be locked and all the mail fetching will be over in 10 CRONs even if cron overlaps
     * - if someone has configured 150 mails, its good to run 15 mails per CRON
     * @param $configuredMailsCount
     * @return int
     */
    protected function getMaxMailFetchCount($configuredMailsCount)
    {
        switch(true){

            case $configuredMailsCount >= 150:
                return 15;

            case $configuredMailsCount < 150 && $configuredMailsCount >= 100:
                return 12;

            case $configuredMailsCount < 100 && $configuredMailsCount >= 50:
                return 8;

            case $configuredMailsCount < 50:
                return 5;
        }
    }

    /**
     * handles fetching of mail
     * Fetches 5 mails per batch by picking mails which were fetched before everyone else. It will be skipping mails,
     * which are already running in parallel CRON. as soon as it start fetching those mails, it will mark them as
     * 'not available for fetch', so next CRON will skip them. As soon as fetching gets completed, it will mark them
     * back as 'available for fetch'
     * @param CronLog|null $cronLog
     * @return string
     * @throws Exception
     */
    public function fetchMail(CronLog $cronLog = null)
    {
        // Get email settings
        if (SettingsEmail::first()->email_fetching) {

            // lock first five
            // as soon as it arrives here, it has to make an announcement that it is taking first 5 mails for processing

            // if for some reason a particular mail got stuck at available_for_fetch as 0 (in case of killing the process in bw),
            // it should be picking it irrespective if available_for_fetch and it was last fetched before 15 minutes
            $emailsConfig = Emails::where('fetching_status', 1)
                ->where('available_for_fetch', 1)
                ->orWhere(function ($query){
                    $query->where('available_for_fetch', 0)
                        ->where('last_fetched_at', '<', Carbon::now()->subMinutes(15));
                })
                ->orderBy('last_fetched_at', 'asc')
                ->limit($this->maxMailFetchCount)
                ->get();

            // marking those mails as not available for fetch
            $this->markAsUnavailable($emailsConfig, $cronLog);

            foreach ($emailsConfig as $emailConfig) {

                $this->emailConfig = $emailConfig;

                $this->systemEmail = $emailConfig->email_address;

                $provider = $this->emailConfig->fetching_protocol == "ews" ? new ExchangeWebServices($this->emailConfig) : new PhpImap($this->emailConfig);

                $this->fetchMailByEmailProvider($provider);
            }

            // marking those emails as available for fetch
            $this->markAsAvailable($emailsConfig);

            //throwing this outside the loop so that rest of mails from the multiple configured emails can be fetched before command terminates
            if($this->error instanceof Exception){
                throw $this->error;
            }
        }
    }

    /**
     * Marks as unavailable, so that in mail fetch, it will be skipped
     * @param $emailsConfig
     * @param $cronLog
     * @return
     */
    private function markAsUnavailable($emailsConfig, CronLog $cronLog = null)
    {
        $emailsGettingFetched = implode(', ', $emailsConfig->pluck('email_address')->toArray());

        // if there are mails which are configured and an existing CRON log has been created for that, it will update that Log
        if($emailsGettingFetched && $cronLog) {
            $cronLog->description = "Fetching emails ($emailsGettingFetched) from configured mails and creates/updates related ticket";
            $cronLog->save();
        }

        // if this comes as false, skip to the next
        return Emails::whereIn('id', $emailsConfig->pluck('id')->toArray())->update(['available_for_fetch'=> 0, 'last_fetched_at'=> Carbon::now()]);
    }

    /**
     * Marks ticket as available for fetch
     * @param $emailsConfig
     * @return
     */
    private function markAsAvailable($emailsConfig)
    {
        return Emails::whereIn('id', $emailsConfig->pluck('id')->toArray())->update(['available_for_fetch'=> 1, 'last_fetched_at'=> Carbon::now()]);
    }

    /**
     * Fetches mail by the service provider given
     * @internal we are closing the connection while connecting to the mail server. Mail server response can be slow, which causes
     *           connection to be occupied
     * @internal do not do a dd() in this method. Execution should not stop in this method, since above method
     * @param MailServiceProvider $provider
     */
    protected function fetchMailByEmailProvider(MailServiceProvider $provider)
    {
        try {
            // we don't need an active connection when mails are getting fetched from mail server
            DB::disconnect();

            $messageIds = (array)$provider->getMessageIds();

            // only picking 15 of those at a time so that if load is more, it can be distributed in consecutive CRONS
            $messageIds = array_slice($messageIds, 0, 15);

            $mails = [];

            foreach ($messageIds as $messageId){
                $provider->setMessageId($messageId);

                $mails[] = $provider->getMail();

                $provider->markAsRead();
            }


            // laravel automatically connects whenever a connection is needed
            foreach ($mails as $mail){
                // different try-catch for this
                $this->convertMailToTicket($mail);
            }

        } catch (Exception $e){
            $this->error = $e;
            // it will continue fetching the rest of the messages
        }
    }

    /**
     * Converts mail to ticket
     * @param Mail $mail
     * @param MailServiceProvider $provider
     * @throws Exception
     */
    private function convertMailToTicket(Mail $mail)
    {
        try{
            $log = Logger::fetchedMail($mail, $this->systemEmail);

            // if exception is thrown by shallCreateTicket, it should mark them as read
            // but if an error comes, it shouldn't mark them as read
            if ($this->shallCreateTicket($mail)) {
                $this->callToWorkflow($mail);
            }

            Logger::fetchedMailAccepted($log->id);
        } catch(MailRejectionException | TicketRejectionException $e){
            Logger::fetchedMailRejected($log->id, $e);
        } catch (Exception $e){
            Logger::fetchedMailBugEncountered($log->id, $e);
        }

    }

    /**
     * checks if current mail is valid for creating a ticket
     *
     * @param Mail $mail data of the mail
     * @return boolean
     * @throws MailRejectionException
     */
    private function shallCreateTicket(Mail $mail): bool
    {
        $requesterMail = $mail->getReplyToAddress();

        if ($mail->ifAutoResponded):
            throw new MailRejectionException("Mail is detected to be an auto generated mail");

        elseif ($this->wasMailFetchedBefore($mail->messageId)):
            throw new MailRejectionException("This mail has already been fetched and associated with a ticket before");

        elseif ($this->isMailFromSystemMail($requesterMail)):
            throw new MailRejectionException("Mail is from system configured mail");

        // https://github.com/ladybirdweb/faveo-helpdesk-advance/issues/3443
        elseif($this->ifBlockedByNewUserRegistration($requesterMail)):
            throw new MailRejectionException("New user registration has been blocked. Please go to 'Admin Panel >> User Options' to activate that");

        // https://github.com/ladybirdweb/faveo-helpdesk-advance/issues/3385
        elseif ($this->ifMailSentByDeActivatedUser($requesterMail)):
            throw new MailRejectionException("$requesterMail belongs to a deactivated user");

        elseif ($this->ifDuplicateEmail($requesterMail)):
            throw new MailRejectionException("Duplicate user found. $requesterMail is a already is a username of a user. Please check user directory");

        elseif ($this->hasEmptyBody($mail)):
            throw new MailRejectionException("Empty mail body found after trimming");

        endif;

        return true;
    }

    /**
     * checks if body is empty or not after removing trailing empty spaces
     * @param $mail
     * @return bool
     */
    private function hasEmptyBody($mail)
    {
        return !(bool) trim($mail->body);
    }

    /**
     * If passed email is already someone's username
     * @param $email
     * @return bool
     */
    private function ifDuplicateEmail($email)
    {
        return (bool)User::where("email", "!=", $email)->where("user_name", $email)->select("user_name")->first();
    }

    /**
     * If mail is sent from a de-activated user
     * @param $email
     * @return bool
     */
    private function ifMailSentByDeActivatedUser($email)
    {
        //If acocunt is deactivated or deleted or being processed for deletion/deactivation
        return (bool)User::where("email", $email)->where(function($query) {
            $query->where("is_delete", 1)->orWhere("active", 0)->orWhere("processing_account_disabling", 1);
        })->count();
    }

    /**
     * Checks if new user is getting created. If yes, it checks if user registeration is blocked. If yes, return true, else false
     * @param $email
     * @return bool
     */
    private function ifBlockedByNewUserRegistration($email)
    {
        return !User::where("email", $email)->count() && !\DB::table("common_settings")
                ->where("option_name", "user_registration")
                ->where("status", 1)->count();
    }

    /**
     * checks if mail is coming from system address
     *
     * @param  string  $from  email address
     * @return boolean        true if mail is coming from system mail else false
     */
    private function isMailFromSystemMail(string $from): bool
    {
        // check if from belongs to $this->emails
        return (bool)in_array(strtolower($from), array_map('strtolower', $this->systemEmails));
    }

    /**
     * checks if ticket is already created  with the given message id
     *
     * @param integer|string $messageId Email messageId
     * @return int|bool Ticket number or False
     */
    private function wasMailFetchedBefore(string $messageId)
    {
        return (bool)EmailThread::where('message_id', $messageId)
            ->where('message_id', '!=', null)->where('message_id', '!=', '')->count();
    }

    /**
     * gets collaborators
     * @param Mail $mailObject mail object with cc, bcc, to as keys
     * @return array                    array of collaborators with email as key and name as value
     *                                  ["Bhai ka naam"=> "bhai@ka.email"]
     */
    private function collaborators(Mail $mailObject): array
    {
        //remove all system mails from cc
        $collaborators = array_merge($mailObject->cc, $mailObject->bcc, $mailObject->to);

        $systemConfiguredMailInLowerCase = array_map('strtolower', $this->systemEmails);

        $filteredCollaborators = array_filter($collaborators, function ($email) use ($systemConfiguredMailInLowerCase, $mailObject){
            /**
             * Conditions to exclude
             * - if cc is system configured mail
             * - if cc is also in `from` email (https://github.com/ladybirdweb/faveo-helpdesk-advance/issues/3722)
             */
            return !(in_array(strtolower($email), $systemConfiguredMailInLowerCase) ||
                strtolower($email) == strtolower($mailObject->getReplyToAddress()));

        }, ARRAY_FILTER_USE_KEY);
        // Check if name is null, populate it with email and make key as array and array as key as a workaround
        // For create ticket(must be removed in future)
        return $this->formatCollaborators($filteredCollaborators);
    }

    /**
     * Check if name is null, populate it with email and make key as array and array as key as a workaround
     * For create ticket(must be removed in future)
     *
     * @param  array $collaborators  list of collaborators
     * @return array                  formatted list of collaborators
     */
    private function formatCollaborators(array $collaborators): array
    {
        // Check if name exists
        $formattedCollaborators = [];

        foreach ($collaborators as $key => $value) {
            // Replace name with email and flip key value
            if (!$value) {
                $value = $key;
            }

            $formattedCollaborators[$value] = $key;
        }

        return $formattedCollaborators;
    }

    /**
     * TO DO: this method has to be removed and create_user from ticket controller has to be called directly
     * calls to workflow by formatting it in arguments that it accepts
     * @param Mail $mail
     * @return "Only Rajnikant knows"
     */
    private function callToWorkflow(Mail $mail)
    {
        $collaborators = $this->collaborators($mail);
        $emailIdentity = ['message_id'=> $mail->messageId, 'uid'=> $mail->uid, 'reference_id' => $mail->referenceIds, 'fetching_email' => $this->systemEmail];
        $helptopicId     = $this->emailConfig->help_topic;
        $departmentId    = $this->emailConfig->department;
        $priorityId      = $this->emailConfig->priority;
        $sourceId        = TicketSource::where('name', 'email')->first()->id;
        $shallAutoAssign = HelpTopic::find($helptopicId)->auto_assign;
        $team_assign     = null;
        $ticket_status   = null;
        $sla             = "";

        // not sure what is this for
        $autoResponse    = $this->emailConfig->auto_response;
        $type            = null;

        $locationId = $this->getUserLocationUsingMailIfUserExistInSystem($mail->getReplyToAddress());

        return (new TicketWorkflowController)->workflow(
            $mail->getReplyToAddress(), $mail->getReplyToName(), $mail->subject, $mail->body, '', '', '',
            $helptopicId, $sla, $priorityId, $sourceId, $collaborators, $departmentId, $shallAutoAssign,
            $team_assign, $ticket_status, $form_data = [], $autoResponse, $type, $mail->attachments, $mail->inlines,
            $emailIdentity, "", "", $locationId
        );
    }

    /**
     * Gets the user location if user is present
     * @param $mailAddress
     * @return string
     */
    private function getUserLocationUsingMailIfUserExistInSystem($mailAddress)
    {
        $user = User::where('email', $mailAddress)->first(['location']);
        return ($user) ? $user->location : '';
    }
}
