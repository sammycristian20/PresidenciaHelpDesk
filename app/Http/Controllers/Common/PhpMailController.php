<?php

namespace App\Http\Controllers\Common;


use App\Exceptions\NonLoggableException;
use App\Facades\Attach;
use App\Http\Controllers\Common\Mail\ExchangeWebServices;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\TemplateVariablesController;
use App\Model\Common\TemplateType;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Settings\Email;
use App\Model\helpdesk\Ticket\EmailThread;
use App\User;
use Auth;
use Mail;
use Exception;
use Lang;
use Logger;
use App\Model\helpdesk\Ticket\Tickets;
use Config;
use Cache;
use App\Http\Controllers\Common\Mail\BaseMailController;
use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;

class PhpMailController extends Controller {

    /**
     *@var variable to instantiate common mailer class
     */
    protected $commonMailer, $queueManager;

    /**
     * Doing what we do best "JUGAAD"
     *@var string
     */
    protected $ticketDepartment = null;


    public function __construct()
    {
        $this->commonMailer = new CommonMailer;
        $this->queueManager = app('queue');
    }

    /**
     * get the configured email
     * @param string $id
     * @return Emails
     */
    public function fetch_smtp_details($id = "")
    {
        return persistentCache('smtp_details', function() use ($id){

            if ($id) {
                return Emails::where('id', '=', $id)->first();
            }
            return Emails::first();

        }, 10, [$id]);
    }

    /**
     * Function to choose from address.
     *
     * @param type Reg        $reg
     * @param type Department $dept_id
     *
     * @return type integer
     */
    public function mailfrom($reg, $dept_id)
    {
        $email    = Email::find(1);
        $email_id = $email->sys_email;

        $dept_email = Department::select('outgoing_email')->where('id', '=', $dept_id)->first();
        if ($dept_email) {
            if ($dept_email->outgoing_email) {
                $email_id = $dept_email->outgoing_email;
            }
        }
        return $email_id;
    }
    /**
     * send mail to job
     * @param integer $from
     * @param array $to
     * @param array $message
     * @param array $template_variables
     * @param mixed $thread
     * @param string $auto_respond
     */
    public function sendmail($from, $to, $message, $template_variables, $thread = "", $auto_respond
    = "")
    {
        //putting a check here because is called at multiple places and hence not possible to change everywhere
        // caching it per request, since might be in iteration but needs only once
        if(quickCache("sendmail", function() use($from) {
            return Emails::where('sending_status',1)->whereId($from)->count();
        }, [$from])){
            $this->sendEmail($from, $to, $message, $template_variables, $thread, $auto_respond);
        }
    }

    /**
     * set up sending mail configuration
     * @param integer $from
     * @param array $to
     * @param array $message
     * @param array $template_variables
     * @param mixed $thread
     * @param string $auto_respond
     * @return mixed
     * @throws Exception
     */
    public function sendEmail($from, $to, $message, $template_variables, $thread
    = "", $auto_respond = "")
    {
        $email_reciever_name = "";
        $reciever            = User::where('email', '=', $to['email'])->select('user_name', 'email', 'first_name', 'last_name', 'mobile')->first();
        if ($reciever) {
            $email_reciever_name = $reciever->name();
        }
        $template_variables['receiver_name'] = $email_reciever_name;
        $from_address                        = $this->fetch_smtp_details($from);

        $recipants = $this->checkElement('email', $to);

        // if recipient is null, it should not be processed further
        if(!$recipants){
            return;
        }

        $recipantname  = $this->checkElement('name', $to);
        $recipant_role = $this->checkElement('role', $to);
        $recipant_lang = $this->checkElement('preferred_language', $to);
        $isManager     = $this->isManager($recipants, $template_variables, $recipant_role);
        $cc            = $this->checkElement('cc', $message);
        $bc            = $this->checkElement('bc', $message);
        $subject       = $this->checkElement('subject', $message);
        $template_type = $this->checkElement('scenario', $message);
        $attachment    = $this->checkElement('attachments', $message);
        if ($recipants == $this->checkElement('agent_email', $template_variables)) {
            $assigned_agent = true;
        }
        else {
            $assigned_agent = false;
        }
        $content_array = $this->mailTemplate($template_type, $template_variables, $message, $subject, $assigned_agent, $recipant_role, $recipant_lang, $isManager, $cc);
        $content       = $this->checkElement('body', $message);
        if ($content_array) {
            $content = checkArray('content', $content_array);
            $subject = checkArray('subject', $content_array);
        }
        try {
            $logReference = Logger::logMailByCategory($from_address->email_address, $recipants, $cc, $subject, $content, $template_variables, $template_type);

            // dispatch the job from here
            $this->setQueue();
            $job = new \App\Jobs\SendEmail($recipants, $recipantname, $subject, $content, $from_address,$cc, $attachment, $thread, $auto_respond, $logReference->id);
            dispatch($job);

        } catch (\Exception $e) {
            Logger::exception($e);
        }
    }

    /**
     * Santizes laravelMail paramters to make it valid
     * NOTE: some inputs are coming in an invalid format which is causing extra validations after this.
     * This method converts them into valid values
     */
    private function sanitizeParameters(&$toname, &$ccEmails, &$attach, &$thread)
    {
        $ccEmails = $ccEmails ?: [];
        $attach = $attach ?: [];

        if($thread && $thread instanceof Thread){
          // if thread is there we should be picking attachments from thread instead
          $attach = $thread->attach()->get();
        }

        // sometimes it is coming as null
        $toname = (string)$toname;
    }

    /**
     * sending email via swiftmailer
     * @param string $to
     * @param string $toname
     * @param string $subject
     * @param string $data
     * @param array $cc
     * @param mixed $attachments
     * @param mixed $thread
     * @param string $auto_respond
     * @param int $logIdentifier  idetifies which mail it is sending, so that status of the log can be updated
     * @return int
     */
    public function laravelMail($to, $toname, $subject, $data, $from_address, $cc = [], $attachments
    = "", $thread = "", $auto_respond = "", $logIdentifier) {
      // cc and attach should be an array, but this method is recieving empty string
      // NOTE: some inputs are coming in an invalid format which is causing extra validations after this.
      // This method converts them into valid values
      $this->sanitizeParameters($toname, $cc, $attachments, $thread);

      $reference = $this->getReference($thread);

      try{

        if($from_address->sending_protocol == 'ews'){
            $attachmentsArray = is_array($attachments) ? $attachments : $attachments->all();
            (new ExchangeWebServices($from_address))->sendMailByEWS($to, $toname, $subject, $data, $cc, $attachmentsArray, $reference);
            return Logger::outgoingMailSent($logIdentifier);
        }

        $this->setMailConfig($from_address);

        // seperating attachments from inline
        $inlines = [];
        if(is_array($attachments)){
          foreach ($attachments as $key => $file) {
            if(strtolower($file['poster']) == 'inline'){
              $inlines[] = $file;
              unset($attachments[$key]);
            }
          }

          // reindexing attach array since after unsetting, its index might not start from 0, 1
          $attachments = array_values($attachments);
        }

        $mail = Mail::send('emails.mail', ['data' => $data, 'thread' => $thread, 'inlineImages'=> $inlines], function ($m) use ($to, $subject, $toname, $from_address, $cc, $attachments, $thread, $auto_respond, $reference) {
                    $m->to($to, $toname)->subject($subject);
                    $m->from($from_address->email_address, $from_address->email_name);
                    $swiftMessage = $m->getSwiftMessage();
                    $headers      = $swiftMessage->getHeaders();
                    if ($auto_respond) {
                        $headers->addTextHeader('X-Autoreply', 'true');
                        $headers->addTextHeader('Auto-Submitted', 'auto-replied');
                    }
                    if ($reference) {
                        $headers->addTextHeader('References', $reference);
                        $headers->addTextHeader('In-Reply-To', $reference);
                    }

                    $m->cc($cc);

                    foreach($attachments as $attachment) {
                        $this->attachFile($m, $attachment);
                    }

                    $this->saveEmailThread($swiftMessage->getId(), $thread);
        });

        Logger::outgoingMailSent($logIdentifier);

        return $mail;

      } catch(Exception $e){
          Logger::outgoingMailFailed($logIdentifier, $e);

          // throwing the exception so that job can be marked as fail and can be retried
          throw new NonLoggableException($e->getMessage());
      }
    }


    /**
     * Attaches attachments to mail
     * @param $message
     * @param $file
     * @return mixed
     */
    private function attachFile($message, $file)
    {
        // it should be considered as billing attachment
        if(isset($file['file_name'])){
            return $message->attachData(base64_decode($file['data'], true), $file['file_name'], ['mime' => $file['mime']]);
        }

        // ticket attachments
        $fileContents = \Storage::disk($file->driver)->get($file->getOriginal('name'));
        $filePath = sys_get_temp_dir() . '/' . str_random() . $file->name;
        file_put_contents($filePath, $fileContents);
        return $message->attach($filePath, ['as' => $file->name, 'mime' => mime_content_type($filePath)]);
    }

    /**
     * set the email configuration
     * @param Email $from_address
     */
    public function setMailConfig($mail) {
        switch ($mail->sending_protocol) {
            case "smtp":
                $config = [ "host" => $mail->sending_host,
                            "port" => $mail->sending_port,
                            "security" => $mail->sending_encryption,
                            'username' => $mail->user_name,
                            'password' => $mail->password
                        ];
                if (!$this->commonMailer->setSmtpDriver($config)) {
                    \Log::info("Invaid configuration :- ".$config);
                    return "invalid mail configuration";
                }

                break;

            case "send_mail":
                $config = [
                            "host" => \Config::get('mail.host'),
                            "port" => \Config::get('mail.port'),
                            "security" => \Config::get('mail.encryption'),
                            'username' => \Config::get('mail.username'),
                            'password' => \Config::get('mail.password')
                        ];
                $this->commonMailer->setSmtpDriver($config);
                break;

            default:
                setServiceConfig($mail);
                break;
        }

    }

    public function checkElement($element, $array)
    {
        $value = "";
        if (is_array($array)) {
            if (key_exists($element, $array)) {
                $value = $array[$element];
            }
        }
        return $value;
    }
    /**
     * get the reference ket from mail
     * @param mixed $thread
     * @return string
     */
    public function getReference($thread)
    {
        if(!$thread){
            return "";
        }
        return (string)EmailThread::where('thread_id', $thread->id)->value('message_id');
    }

    /**
     * save the email keys like reference in db and associate with ticket
     * @param integer $msg_id
     * @param mixed $thread
     */
    public function saveEmailThread($msg_id, $thread)
    {
        if ($thread) {
            $content           = ['message_id' => $msg_id];
            $ticket_controller = new \App\Http\Controllers\Agent\helpdesk\TicketController();
            $ticket_controller->saveEmailThread($thread, $content);
        }
    }
    /**
     * set the queue service
     */
    public function setQueue()
    {
        $this->queueManager->setDefaultDriver($this->getActiveQueue()->driver);
        // \Config::set('queue.default', $short);
        // foreach ($field as $key => $value) {
        //     \Config::set("queue.connections.$short.$key", $value);
        // }
    }

    private function getActiveQueue()
    {
        return persistentCache('queue_configuration', function(){
            $short        = 'database';
            $field        = [
                'driver' => 'database',
                'table'  => 'jobs',
                'queue'  => 'default',
                'expire' => 60,
            ];
            $queue        = new \App\Model\MailJob\QueueService();
            $active_queue = $queue->where('status', 1)->first();
            if ($active_queue) {
                $short  = $active_queue->short_name;
                $fields = new \App\Model\MailJob\FaveoQueue();
                $field  = $fields->where('service_id', $active_queue->id)->pluck('value', 'key')->toArray();
            }
            return (object)['driver'=> $short,'config'=>$field];
        });
    }

    /**
     * embed the mail template with conversion
     * @param array $templateVariables
     * @param string $templateType, $lang, $role, $assigned_agent, $subject,$message, $from
     * @return array
     */
    public function mailTemplate($templateType, $templateVariables, $message, $subject, $assignedAgent, $role, $lang, $isManager = false, &$cc = [])
    {
        $templateCategory = $this->getTemplateCategory($templateType, $role, $assignedAgent, $message, $isManager);
        $content       = $this->checkElement('body', $message);
        $ticketNumber = $this->checkElement('ticket_number', $templateVariables);
        $template      = TemplateType::where('name', '=', $templateType)->first();

        /**
         * Doing again what we do best, "JUGAAD"
         *
         * if category is not "client-template" and template-type is not "ticket-reply" then cc should
         * be cleared
         * REASON : cc should be only added if a reply is made from agent panel.
         * because if a reply is made from mail, it already will send cc.
         *
         * if category is "client-template" and template-type is "ticket-reply-agent" then cc should be
         * cleared
         *
         * REASON : cc should be only added if a reply is made from agent panel.
         * But now cc person recieved reply if done from client panel so $template-catgeory will have
         * client-templates with $template->name as ticket-reply-agent
         */
        if(!($templateCategory == 'client-templates' && $template->name == "ticket-reply")) {
          $cc = [];
        } elseif ($templateCategory == 'client-templates' && $template->name == "ticket-reply-agent") {
          $cc = [];
        }

        $setId           = $this->getTemplateSetIdByTicketNumber($lang, $ticketNumber);
        $temp = [];
        if ($template) {
            $temp                = $this->set($setId, $ticketNumber, $message, $template, $templateCategory);
            $contents            = $temp['content'];
            $messageBody         = $this->replaceShortcodeInTemplates($templateVariables, $contents, $content);
            $content = '<div style="display:none">---Reply above this line(to trim old messages)--- </div>' . $messageBody;
        }
        (checkArray('subject', $temp)) && ($subject = $this->replaceShortcodeInTemplates($templateVariables, checkArray('subject', $temp), $content));
        return ['content' => $content, 'subject' => $subject];
    }


    /**
     * Gets template set Id by ticketNumber
     * @param $lang
     * @param $ticketNumber
     * @return int|null
     */
    private function getTemplateSetIdByTicketNumber($lang, $ticketNumber)
    {
        /*
        NOTE: would be better if we were passing department_id too in template variables. We could have used the
        cache for all ticket of same department
        */
        return quickCache('template_set_id', function() use ($lang, $ticketNumber){
            $this->ticketDepartment = Tickets::where('ticket_number', $ticketNumber)->value('dept_id');
            return $this->getTemplateSet($lang);
        }, [$lang, $ticketNumber]);
    }

    /**
     *
     * @param string $set
     * @param string $ticket_number
     * @param string $message
     * @param string $template
     * @param string $template_category
     * @return array
     */
    public function set($set, $ticket_number, $message, $template, $template_category)
    {
        $contents = null;
        $subject  = null;
        if (isset($set)) {
            $subject       = checkArray('subject', $message);
            $template_data = \App\Model\Common\Template::where('set_id', '=', $set)->where('type', '=', $template->id)->where('template_category', '=', $template_category)->first();
            $contents      = $template_data->message;
            if ($template_data->variable == 1) {
                if ($template_data->subject) {
                    $subject = $template_data->subject;
                    if ($ticket_number != null) {
                        $subject = $subject . ' [#' . $ticket_number . ']';
                    }
                }
            }
        }
        return ['content' => $contents, 'subject' => $subject];
    }

    /**
     * function method is used to get reciever is manager or not which then passed to getTemplateCateogry()
     * @param   string   $recipants           email address of reciever
     * @param   arrray   $templateVariables   variables passed in notification array object
     * @return  boolean                       true if reciepant is manager and not owner of the ticket
     */
    protected function isManager($recipants, $templateVariables,  $role = 'agent')
    {
        if (($recipants == $this->checkElement('client_email', $templateVariables)) || ($role != 'user')) {
           return false;
        }
        return User::where('email', $recipants)->first()->isManagerOf();
    }

    /**
     * function takes 5 arguments like type of template type, reciever role etc
     * then returns a srting value for template category
     * @param   string     $templateType   type of template
     * @param   array      $message        array contianing for scenario mail
     * @param   boolean    $isManager      true if reciever is an Organization Manager
     * @param   strin      $role           role of reciepant user
     * @param   boolean    $assignedAgent  true if reciever is an agent and the ticket is assigned to him/her
     * @return  string                     template category name
     */
    protected function getTemplateCategory($templateType, $role, $assignedAgent, $message, $isManager)
    {
        $commonTemplates = ['registration-notification', 'reset-password', 'reset_new_password', 'registration', 'registration-and-verify','send-invoice','ticket-forwarding', 'ticket-approval', 'rating', 'lime-survey', 'email_verify'];
        if (in_array($templateType, $commonTemplates)) {
            return 'common-templates';
        } elseif (in_array($message['scenario'], ['check-ticket', 'invoice'])) {//strict client templates
            return 'client-templates';
        } elseif ($role == 'user' || in_array($message['scenario'], ['create-ticket', 'create-ticket-by-agent'])) {
            return ($isManager) ? 'org-mngr-templates' : 'client-templates';
        }
        return ($assignedAgent) ? 'assigend-agent-templates' : 'agent-templates';
    }

    /**
     * function to replace short codes with their actual values
     * @param   array   $templateVariables  array containing short codes values in key value pair
     * @param   string  $contents           email content in which short codes need to be replaced
     * @param   string  $content            custom message content to use in place of short code
     * @return  string
     */
    private function replaceShortcodeInTemplates($templateVariables, $contents, $content='')
    {
        $tempVarCntroller    = new TemplateVariablesController;
        $variables           = $tempVarCntroller->getVariableValues($templateVariables, $content);
        foreach ($variables as $k => $v) {
            $messageBody = str_replace($k, $v, $contents);
            $contents    = $messageBody;
        }
        return $messageBody;
    }

    /**
     * Function to return template set according to user language preference and availability of the set
     * @param String       $userLanguage
     * @param int           gives template set Id
     */
    private function getTemplateSet(String $userLanguage = null)
    {
        $systemLanguage = Cache::get('language');
        $userLanguageSet = $this->getTemplateSetIdForLanguage($userLanguage);
        if(!$userLanguageSet) {
            return $this->getTemplateSetIdForLanguage($systemLanguage, true);
        }

        return $userLanguageSet;
    }

    /**
     * Function fetches tempalate sets from database based on language and department
     * in a way that it will always fetch if template with department for given language
     * exist else default template for given lanuage else system default template set.
     *
     * @param  String      $language    user/system language
     * @param  bool        $getDefault  shall query fetch system default set
     *
     * @return int|null         First mactching template set or null
     */
    private function getTemplateSetIdForLanguage(string $language = null, bool $getDefault = false)
    {
        return \App\Model\Common\TemplateSet::where([['active', 1],
            ['template_language', $language]])->where(function($query) {
                return $query->whereNull('department_id')
                    ->when($this->ticketDepartment, function($query){
                            return $query->orWhere('department_id', $this->ticketDepartment);
                    });
        })->when($getDefault, function($query){
            return $query->orWhere('id', 1);
        })->orderBy('department_id', 'desc')->value('id');
    }
}
