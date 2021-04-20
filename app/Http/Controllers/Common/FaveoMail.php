<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Model\Common\TemplateType;
use Illuminate\Support\Facades\Mail;
use App\Model\helpdesk\Email\Emails;
use App\Http\Controllers\Controller;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Common\CommonMailer;
use App\Model\helpdesk\Settings\Email as DefaultMail;
use App\Http\Controllers\Common\TemplateVariablesController;
use App\User;

class FaveoMail extends Controller
{
   protected $commonMailer, $system_mail, $default_template_set;

   public function __construct(){
     $this->commonMailer = new CommonMailer;
     /* initilize default system mail */
     $this->system_mail =  Emails::where('id', DefaultMail::First()->value('sys_email'))->first();
     $this->default_template_set = \App\Model\Common\TemplateSet::where('active', '=', 1)->first();
   }

   public function checkElement($element, $array) {
        $value = "";
        if (is_array($array)) {
            if (key_exists($element, $array)) {
                $value = $array[$element];
            }
        }
        return $value;
    }

    public function setEmailBody($template_set, $message, $template, $template_category, $ticket_number = null) {
        $contents = null;
        $subject  = null;
        if (isset($template_set)) {
            $subject       = checkArray('subject', $message);
            $template_data = \App\Model\Common\Template::where('set_id', '=', $template_set)->where('type', '=', $template->id)->where('template_category', '=', $template_category)->first();
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

   // Function to set templates
    public function setEmailTemplate($user, $template_type, $template_variables, $message, $from = null, $subject = null, $template_category = null){
        $email_reciever_name = "";
        $reciever            = User::where('email', '=', $user)->first();
        if ($reciever) {
            $email_reciever_name = $reciever->name();
        }
        $template_variables['receiver_name'] = $email_reciever_name;
        switch($template_type){
            case "task-created-owner":
            case "task-created-assignee":
            case "task-update-owner":
            case "task-update-assignee":
            case "task-deleted-owner":
            case "task-deleted-assignee":
            case "task-assigned-owner":
            case "task-assigned-assignee":
            case "task-status-owner":
            case "task-status-assignee":
            case "task-reminder-owner":
            case "task-reminder-assignee":        
                $content       = $this->checkElement('body', $message);
                $ticket_number = $this->checkElement('ticket_number', $template_variables);
                $template      = TemplateType::where('name', '=', $template_type)->first();
                // $set      
                if ($template) {
                    $temp = $this->setEmailBody($this->default_template_set->id, $message, $template, "common-templates");
                    $subject             = $temp['subject'];
                    $contents            = $temp['content'];
                    $temp_var_controller = new TemplateVariablesController;
                    $variables           = $temp_var_controller->getVariableValues($template_variables, $content);
                    foreach ($variables as $k => $v) {
                        $messagebody = str_replace($k, $v, $contents);
                        $contents    = $messagebody;
                    }
                    $content = $messagebody;
                }
                return ['content' => $content, 'subject' => $subject];
            break;

            default:
                if($template = TemplateType::where('name', $template_type)->first()){
                    $category = ($template_type == 'rating-confirmation')? "agent-templates": "common-tmeplates";
                    $temp = $this->setEmailBody($this->default_template_set->id, [], $template, $category);
                    $content = $temp['content'];
                    $temp_var_controller = new TemplateVariablesController;
                    $variables           = $temp_var_controller->getVariableValues($template_variables, $temp['content']);
                    foreach ($variables as $k => $v) {
                        $messagebody = str_replace($k, $v, $content);
                        $content    = $messagebody;
                    }
                    $content = $messagebody;
                    $variables2           = $temp_var_controller->getVariableValues($template_variables, $temp['subject']);
                    $subject = $temp['subject'];
                    foreach ($variables2 as $k => $v) {
                        $subBody = str_replace($k, $v, $subject);
                        $subject    = $subBody;
                    }
                    $subject = $subBody;
                    return ['content' => $content, 'subject' => $subject];
                }
            break;
      }
   }

   public function sendMail($to, $content, $cc = null, $attachment = null, $from = null){
       switch ($from) {
            case null:
                $this->setMailConfig($this->system_mail);
                $from = $this->system_mail;
                $subject = $content['subject'];
                // DOING WHAT WE DO BEST, 'JUGAAD'
                // sometimes to is coming as array and sometimes as string. PhpMailController only accepts a single to.
                // So, extracting first `to` out. It always comes as single `to` even in array
                if(is_array($to)){
                    $to = $to[0];
                }

                (new PhpMailController)->sendmail($from->id,['email'=>$to], ['body' => $content['content'], 'scenario'=>'', 'subject' => $subject], []);
                break;
            
            default:
                $this->setMailConfig($from);
                break;
        }
   }


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

            case "mailgun":
                $this->commonMailer->setMailGunDriver(null);
                break;

            default:
                return "Mail driver not supported";
                break;
        }
    }

}
