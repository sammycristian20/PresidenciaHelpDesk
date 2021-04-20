<?php

namespace App\Jobs;

use App\Model\Common\Template;
use Auth;
use App\User;
use OneSignal;
use Illuminate\Bus\Queueable;
use App\Model\MailJob\QueueService;
use App\Model\helpdesk\Settings\Alert;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\Common\FaveoMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\helpdesk\Notification\Subscribed;
use App\Model\helpdesk\Notification\PushNotification;
use App\Http\Controllers\Common\NotificationController;



class Notifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $handle_type, $data, $notification_type, $template_name, $alerts, $notify, $faveoMail, $mail_cc, $to;

    public function __construct($handle_type = null, $data = null, $notification_type = null, $template_name = null, $mail_cc = null, $to=null)
    {
        // config(['queue.default'=>'database']);
        $this->setDriver();
        \Log::info(app('queue')->getDefaultDriver());
        $this->data = $data;
        $this->mail_cc = $mail_cc;
        $this->handle_type = $handle_type;
        $this->template_name = $template_name;
        $this->notification_type = $notification_type;
        if($to == null){
            if(!Auth::guest())
                $this->to = Auth::user()->email;
        }
        else{
            $this->to = $to;
        }


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FaveoMail $faveoMail, NotificationController $notify, Alert $alerts)
    {
        $this->faveoMail = $faveoMail;
        $this->notify = $notify ;
        \Log::info("Handle executing");
        $this->alerts = $alerts;
        $user = new User;

        switch ($this->handle_type) {
            case 'mobile-app-notifications':
                $this->mobilePushNotifications($this->data['notification_id'], $this->data['to']);
                break;

            case 'in-app-notification':
            \Log::info("Notify Executing");
                switch ($this->notification_type) {
                    case 'task':
                    case'task-repeat':
                            \Log::info("Creating notification");
                            \Log::info($this->data);
                            $this->notify->createNotification($this->data);
                            $this->inAppWebPush();
                    break;
                    default:
                        $this->notify->createNotification($this->data);
                            $this->inAppWebPush();
                    break;
                }
            break;

            /* Minimal Code Changes to get task alerts working author github.com/Phaniraj35*/
            case 'email-notification':
                    switch ($this->template_name) {
                        case "task-reminder-owner":
                        case "task-reminder-assignee":
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
                            $this->sendTaskEmailNotifications();
                            break;
                        /*End github.com/Phaniraj35*/

                        case "rating":
                            $mail_content = $this->faveoMail->setEmailTemplate($this->to, $this->template_name, $this->data, null);
                            \Log::info("\nsending----rating----mail\n");
                            \Log::info($mail_content);
                            $this->faveoMail->sendMail($this->to, $mail_content);
                        break;

                        case "rating-confirmation":
                            $mail_content = $this->faveoMail->setEmailTemplate($this->to, $this->template_name, $this->data, null);
                            $this->faveoMail->sendMail($this->to, $mail_content);
                        break;

                        case "lime-survey":
                            $mail_content = $this->faveoMail->setEmailTemplate($this->to, $this->template_name, $this->data, null);
                            \Log::info("\nsending----survey----mail\n");
                            $this->faveoMail->sendMail($this->to, $mail_content);
                        break;

                        default:
                           
                                $person_type = explode(',',$this->alerts->where('key', 'task_alert_persons')->value('value'));
                                $cc = $this->mail_cc;
                                foreach ($cc as $key => $value) {
                                    if(! in_array($user->where('email',$value)->value('role'), $person_type))
                                        unset($cc[$key]);
                                }
                                $this->mail_cc = $cc;

                                $mail_content = $this->faveoMail->setEmailTemplate($this->to, $this->template_name, $this->data, ["subject"=> "Task Status Update"]);
                            
                                $this->faveoMail->sendMail($this->to,$mail_content, []);
                                foreach ($this->mail_cc as $key => $value) {
                                    $this->faveoMail->sendMail($value,$mail_content, []);
                                }
                        break;
                    }
                        
            break;

            case 'browser-notification':
                \Log::info("executed web notification");
                $this->pushNotification();
            break;

            case 'in-app-web-push':
                \Log::info("executed in app web notification");
                $this->inAppWebPush();
            break;

        }
    }

    private function setDriver(){
        $queue_driver = 'sync';
        if($driver = QueueService::where('status', 1)->first())
            $queue_driver = $driver->short_name;
        app('queue')->setDefaultDriver($queue_driver);
    }

    private function pushNotification(){
        // dd($this->data);
        if($this->alerts->isValueExists('browser_notification_status','1')){
            // if($this->alerts->isValueExists('browser_notification_alert_persons', $this->data['user_type'])){
                if($this->data['specific_user']){
                    
                    foreach ($this->data['user_ids'] as $key => $value) {
                        OneSignal::sendNotificationUsingTags($this->data['message'], [ "key" => "user_id", "relation" => "=", "value" => $value ], $url = $this->data['url'], $data = null, $buttons = null, $schedule = null);
                    }
                    
                }
                else{
                    // dd($this->data);
                    OneSignal::sendNotificationUsingTags($this->data['message'], [ "key" => "user_role", "relation" => "=", "value" => $this->data['user_type'] ], $url = $this->data['url'], $data = null, $buttons = null, $schedule = null);
                }
            // }
        }
    }


    private function inAppWebPush(){
        $pushNotify = new PushNotification;  
        $subscribed = new Subscribed;
        $users = explode(',', $this->data['to']);
        $user1 = explode(',', $this->data['by']);
        $user1 = [];
        $users = array_merge($users, $user1);
        $users = array_unique($users);
        $message = str_replace('<b>', "", $this->data['message']) ;
        $message = str_replace('</b>', "", $message);
        $message = (is_numeric($this->data['by'])) ? User::where('id', $this->data['by'])->first()->user_name . " " . $message : "System ".$message;
        foreach ($users as $value) {
            if ($value) {
                if(count($subscribed_users = $subscribed->where('user_id', $value)->get())){
                    foreach ($subscribed_users as $key => $subscribed_user) {
                        $pushNotify->create([
                            'message' => $message,
                            'url' => $this->data['url'],
                            'subscribed_user_id' => $subscribed_user->id,
                            'status' => 'pending'
                        ]);
                    }
                }
            }
        }
    }


    /**
     * This was getting called while creating ticket, which was making ticket creation painfully slow
     * @since v3.3.2
     * @internal moved from App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController@mobilePush
     * @param $notification_id
     * @param $to
     */
    private function mobilePushNotifications($notification_id, $to)
    {
        $fcm  = new \App\Http\Controllers\Common\PushNotificationController();
        $noti = \App\Model\helpdesk\Notification\Notification::where('id', $notification_id)
            ->with([
                'requester' => function ($query) {
                    return $query->select(
                        'first_name', 'last_name', 'user_name', 'profile_pic', 'email', 'id'
                    );
                }])
            ->select(
                'notifications.message', 'notifications.created_at', 'notifications.table as scenario', 'by', 'notifications.id as notification_id', 'row_id as id'
            )
            ->first()
            ->toArray();

        \App\User::whereIn('id', $to)
            ->with(['notificationTokens'=> function($q){
                $q->where([['type', 'fcm_token'],['active',1]])->select('token','user_id');
            }])
            ->select('id')
            ->chunk(10, function ($agents) use ($noti, $fcm) {
                foreach ($agents as $agent) {
                    $tokens = $agent->notificationTokens->pluck('token')->toArray();
                    foreach ($tokens as $atoken) {
                        $fcm->response($atoken, $noti);
                    }
                }
            });
    }


    /**
     *Send Email Notifications required by Calendar/Task Plugin
     * @author github.com/Phaniraj35
     */
    private function sendTaskEmailNotifications()
    {
        $emailTemplate = Template::where('name', $this->template_name)->first(['message','subject']);
        if ($emailTemplate) {
            $mailContent = $this->faveoMail->setEmailTemplate(
                $this->to,
                $this->template_name,
                $this->data,
                $emailTemplate->message,
                null,
                ["subject" => $emailTemplate->subject]
            );
            $this->faveoMail->sendMail($this->to, $mailContent, $this->mail_cc);
        }
    }

}
