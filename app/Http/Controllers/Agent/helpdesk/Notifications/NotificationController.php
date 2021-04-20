<?php

namespace App\Http\Controllers\Agent\helpdesk\Notifications;

//models
use App\Http\Controllers\Agent\helpdesk\Notifications\Notification;
use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Settings\CommonSettings;
//classes
use DB;
use Schema;
use Lang;
use App\User;
use App\Jobs\Notifications as NotifyQueue;
use App\Model\helpdesk\Notification\Subscribed;
use App\Model\helpdesk\Notification\PushNotification;

class NotificationController extends Notification
{

    public $ticketid             = null;
    public $key                  = null;
    public $userid               = null;
    public $message              = null;
    public $variable             = null;
    public $from                 = null;
    public $send_mail            = true;
    public $send_sms             = true;
    public $change               = [];
    public $model                = null;
    public $content_saved_thread = null;
    public $authUserid           = null;
    public $save_in_thread       = true;
    public $auto_respond         = true;
    public $thread               = null;
    public $cc                   = false;

    public function saved($array)
    {
        $change = checkArray('changes', $array);
        if (checkArray('note', $change)) {
            $this->auto_respond   = false;
            $this->save_in_thread = false;
        }

        $model           = checkArray('model', $array);
        $system          = checkArray('system', $array);
        $send            = checkArray('send_mail', $array);
        $this->send_mail = $send;
        if ($change && $model) {
            $this->authUserid = $model->user_id;
            $this->saveInThread($change, $model, $system);
        }
    }
    public function saveInThread($change, $model, $system = false)
    {
        $this->setParameter('change', $change);
        $this->setParameter('model', $model);
        $ticket_id = $this->getTicketId($model);
        $this->setParameter('ticketid', $ticket_id);
        $key       = $this->getInternalKey($change);
        $this->key = $key;
        $body      = $this->getBody($change, $model);
        $tickets   = $this->ticket($ticket_id);
        $type      = $this->type($tickets);
        if (!$system) {
            $userid = $this->by($change, true);
        }
        else {
            $userid = null;
        }
        $user           = $this->authUser();
        $agentsign      = (\Auth::user()) ? \Auth::user()->agent_sign : '';
        $poster         = $this->poster($userid, true);
        $internal       = 1;
        $client_name    = '';
        $client_email   = '';
        $client_contact = '';
        $agent_email    = '';
        $agent_name     = '';
        $agent_contact  = '';
        $client         = $tickets->user;
        $assign_agent   = $tickets->assigned;
        if ($client) {
            $client_name    = ($client->first_name != '' || $client->last_name != null)
                        ? $client->first_name . ' ' . $client->last_name : $client->user_name;
            $client_email   = $client->email;
            $client_contact = $client->mobile;
        }

        if ($assign_agent) {
            $agent_email   = $assign_agent->email;
            $agent_name    = ($assign_agent->first_name != '' || $assign_agent->last_name
                    != null) ? $assign_agent->first_name . ' ' . $assign_agent->last_name
                        : $assign_agent->user_name;
            $agent_contact = $assign_agent->mobile;
        }

        $notification = [];
        $ticket_subject = $tickets->thread()->whereNotnull('title')->where('title', '!=', '')->select('title')->first();

        /**
         * isko hataane se pehle apne liye kafan ka kapda aur dafan ki zameen khareed lena
         */
        if ($body && $this->save_in_thread) {
            $thread                        = \App\Model\helpdesk\Ticket\Ticket_Thread::create([
                'thread_type' => $type,
                'body'        => $body,
                'ticket_id'   => $ticket_id,
                'is_internal' => $internal,
                'user_id'     => $userid,
                'poster'      => $poster,
            ]);
            $this->content_saved_thread_id = $thread;
        }

        $this->setFrom($tickets);
        if ($ticket_subject && $tickets && (count($change) > 1 || !checkArray('duedate', $change))
                && !checkArray('body', $change) && !checkArray('lock_at', $change)) {
            $ticket_due_date     = "";
            $ticket_created_date = "";
            if ($tickets->duedate) {
                $ticket_due_date = $tickets->duedate->tz(timezone());
            }

            if ($tickets->created_at) {
                $ticket_created_date = $tickets->created_at->tz(timezone());
            }
            $notification[] = [
                $key => [
                    'from'     => $this->from,
                    'message'  => ['subject'  => $ticket_subject->title . '[#' . $tickets->ticket_number . ']',
                        'scenario' => 'internal_change',
                    ],
                    'variable' => [
                        'message_content'      => $body,
                        'activity_by'          => $user,
                        'client_name'          => $client_name,
                        'client_email'         => $client_email,
                        'client_contact'       => $client_contact,
                        'agent_email'          => $agent_email,
                        'agent_name'           => $agent_name,
                        'agent_contact'        => $agent_contact,
                        'ticket_subject'       => title($tickets->id),
                        'ticket_number'        => $tickets->ticket_number,
                        'ticket_link'          => url('thread', $tickets->id),
                        'ticket_due_date'      => $ticket_due_date,
                        'ticket_created_at'    => $ticket_created_date,
                        'agent_sign'           => $agentsign,
                        'department_signature' => $this->getDepartmentSign($tickets->dept_id)
                    ],
                    'ticketid' => $ticket_id,
                ],
            ];
        }

        $exclude_internal_changes = ['team_id', 'assigned_to', 'is_deleted',
            'is_resolution_sla', 'updated_at', 'isanswered', 'lock_at', 'lock_by',
            'is_response_sla', 'approval', 'follow_up', 'ratingreply', 'rating','closed',
            'reopened', 'reopened_at', 'closed_at', 'resolution_time', 'body', 'note'
        ];
        // dump($change);
        // dump(count(array_intersect(array_keys($change), $exclude_internal_changes)));

        if (is_array($change) && (count(array_intersect(array_keys($change), $exclude_internal_changes)) > 0)) {
            $this->setParameter('send_mail', false);
            $this->setParameter('send_sms', false);
        }

        if ($notification) {
            $this->setDetails($notification);
        }
    }
    public function getInternalKey($change)
    {
        $key = 'internal_activity_alert';
        if (is_array($change) && key_exists('dept_id', $change)) {
            $key = 'ticket_transfer_alert';
        }

        if (is_array($change) && key_exists('assigned_to', $change)) {
            $key = 'ticket_assign_alert';
        }

        $this->key = $key;
        return $key;
    }
    public function authUserid($key)
    {
        $id = null;
        if ($key == 'duedate') {
            return $id;
        }

        if (\Auth::user()) {
            $id = \Auth::user()->id;
        }
        elseif ($this->userid) {
            $id = $this->userid;
        }
        return $id;
    }
    public function authUser()
    {
        $name = 'System';
        if (\Auth::user()) {
            $name = \Auth::user()->name();
        }
        return $name;
    }
    public function poster($id, $force_support = false)
    {
        $poster = 'support';
        if ($id && $force_support == false) {
            $poster = 'client';
        }
        return $poster;
    }
    public function saveInNotification()
    {
        if ($this->isMode('system') && $this->model) {
            $message  = $this->getBody($this->change, $this->model, true);
            //dd($this->change, $this->model, true,$message);
            $to_array = $this->getField('id', false);
//            echo "$this->key<br>";
            $to       = '';
            if ($to_array->count() > 0) {
                $to = $to_array->implode(',');
            }

            $by = $this->by();
            if ($message) {
                $this->createNotification($message, $to, $by, $to_array);
            }
        }
    }
    public function createNotification($message, $to, $by, $to_array)
    {
        $noti = \App\Model\helpdesk\Notification\Notification::create([
                    'message' => $message,
                    'to'      => $to,
                    'by'      => $by,
                    'table'   => $this->table($this->model),
                    'row_id'  => $this->rowId($this->model),
                    'url'     => $this->getUrl($this->model),
        ]);
        $this->mobilePush($noti->id, $to_array);
        $this->createPushNotification($noti);
        if ($this->isBrowserMode()) {
            $this->webPush($noti);
        }
    }

    public function mobilePush($notification_id, $to)
    {
        // NOTE FROM AVINASH: the old code of this method has been moved to App\Jobs\Notifications@mobilePushNotifications
        // @since v3.3.2
        (new PhpMailController())->setQueue();
        dispatch(new \App\Jobs\Notifications("mobile-app-notifications", ['notification_id'=> $notification_id, 'to'=> $to]));
    }

    public function by($change = "", $null = false)
    {
        $by = $this->authUserid;

        if (!$by) {
            $by = $this->userid;
        }

        if (!$by && \Auth::user()) {
            $by = \Auth::user()->id;
        }

        if (!$by && $null == false) {
            $by = null;
        }

        if (!$by && $null != false) {
            $by = null;
        }

        return $by;
    }
    public function getUrl($model)
    {
        $table = $model->getTable();
        $id    = $model->id;
        if ($table == 'ticket_thread') {
            $id  = $model->ticket_id;
            $url = faveoUrl("thread/$id");
        }

        if ($table == 'tickets') {
            $url = faveoUrl("thread/$id");
        }

        if ($table == 'users') {
            $url = faveoUrl("user/$id");
        }
        else {
            //to get servicedesk specific module view url
            \Event::dispatch('fetch-sd-url', [&$url, $model]);
        }
        return $url;
    }
    public function table($model)
    {
        $table = $model->getTable();
        if ($table == 'ticket_thread') {
            $table = 'tickets';
        }

        return $table;
    }
    public function rowId($model)
    {
        $table = $model->getTable();
        $id    = $model->id;
        if ($table == 'ticket_thread') {
            $id = $model->ticket_id;
        }

        return $id;
    }
    public function send(array $mailRecievers = [])
    {
        //echo "is active => ".$this->isActive($this->key)."<br>";
        //
        if ($this->isActive($this->key)) {
            $this->sendEmail($mailRecievers);
            $this->sendSms();
            $this->saveInNotification();
        }
    }

    /**
     * Sends mail after calculating to whom mail is required to be sent
     * @param array $mailRecievers  users who will get emails irrespective of alert and notification settings
     */
    public function sendEmail(array $mailRecievers = [])
    {
        //echo "is mode email and this send mail => ".$this->isMode('email') && $this->send_mail."<br>";
        if ($this->isMode('email') && $this->send_mail) {
            $emails = $this->getField("email", false, true, ['id', 'email', 'user_name', 'first_name', 'last_name', 'role', 'user_language']);

            // merge $mailRecievers with
            // ids of the user who are added in to
            $mailRecieverUsers = \App\User::whereIn('id', $mailRecievers)->select('id', 'email', 'user_name', 'first_name', 'last_name', 'role', 'user_language')
              ->get();

            $emails = $emails->merge($mailRecieverUsers);

            foreach ($emails as $email) {
                $this->postMail($email->toArray(), $email->id);
            }
        }
    }
    public function getField($field = "email", $schma = true, $collect = false, $collection_fields
    = "")
    {
        $persons    = $this->getPersons();
        $collection = collect();
        $ticket     = $this->getTicket();
        foreach ($persons as $person) {
            //echo $person."<br>";
            $collection->push($this->getAgentIdByDependency($person, $ticket));
        }

        // push those uner

        $unique = $collection->flatten()->unique()->filter(function ($item) {
            return $item != null;
        });

        if ($schma == true) {
            $unique = \App\User::whereNotnull($field)->whereIn('id', $unique)->pluck($field, 'user_name')->toArray();
        }

        if ($collect == true) {
            if (sizeof($collection_fields) > 0) {
                $unique = \App\User::whereNotnull($field)->select($collection_fields)->whereIn('id', $unique)->get();
            }
            else {
                $unique = \App\User::whereNotnull($field)->whereIn('id', $unique)->get();
            }
        }

        return $unique;
    }
    public function getAgentIdByDependency($person, $ticket)
    {
        $agents = [];
        switch ($person) {
            case "department_members": // pass department id
                if ($ticket) {
                    $modelid = $ticket->dept_id;
                    $agents  = \App\Model\helpdesk\Agent\DepartmentAssignAgents::where('department_assign_agents.department_id', $modelid)
                            ->join('users', 'department_assign_agents.agent_id', '=', 'users.id')
                            ->select('users.id as department_members')
                            ->where('users.active', '=', 1)
                            ->where('users.is_delete', '=', 0)
                            ->get()
                            ->toArray()
                    ;
                }

                return $agents;
            case "team_members": //pass team id
                if ($ticket) {
                    $modelid = $ticket->team_id;
                    $agents  = \App\Model\helpdesk\Agent\Assign_team_agent::
                            where('team_assign_agent.team_id', $modelid)
                            ->join('users', 'team_assign_agent.agent_id', '=', 'users.id')
                            ->select('users.id as team_members')
                            ->where('users.active', '=', 1)
                            ->where('users.is_delete', '=', 0)
                            ->get()
                            ->toArray()
                    ;
                }

                return $agents;
            case "agent":
                $agents = \App\User::where('role', 'agent')
                        ->where('active', 1)
                        ->where('is_delete', 0)
                        ->select('id')
                        ->get()
                        ->toArray();
                return $agents;
            case "admin":
                $agents = \App\User::where('role', 'admin')
                        ->where('active', 1)
                        ->where('is_delete', 0)
                        ->select('id as admin')
                        ->get()
                        ->toArray();
                return $agents;
            case "user": // pass ticket user id
                if ($ticket) {
                    $modelid = $ticket->user()
                            ->where('active', 1)
                            ->where('is_delete', 0)
                            ->value('id')
                    ;
                    $agents  = ['user' => $modelid];
                }
                return $agents;
            case "agent_admin":
                $agents = \App\User::where('role', '!=', 'user')
                        ->where('active', 1)
                        ->where('is_delete', 0)
                        ->select('id as agent_admin')
                        ->get()
                        ->toArray();
                return $agents;
            case "department_manager"://pass department id
                if ($ticket) {
                    $modelid = $ticket->dept_id;

                    $agents  = \App\Model\helpdesk\Agent\DepartmentAssignManager::where('department_id', $modelid)
                     ->join('users', 'department_assign_manager.manager_id', '=', 'users.id')
                            ->select('users.id as department_manager')
                            ->where('users.active', '=', 1)
                            ->where('users.is_delete', '=', 0)
                            ->get()
                            ->toArray();

                }
                return $agents;
            case "team_lead": //pass team id
                if ($ticket) {
                    $modelid = $ticket->team_id;
                    $agents  = \App\Model\helpdesk\Agent\Teams::where('teams.id', $modelid)
                            ->where('status', 1)
                            ->join('users', 'teams.team_lead', '=', 'users.id')
                            ->select('users.id as team_lead')
                            ->where('users.active', '=', 1)
                            ->where('users.is_delete', '=', 0)
                            ->get()
                            ->toArray();
                }

                return $agents;
            case "organization_manager":
            //pass user id
                if ($ticket) {
                    $modelid = $ticket->user_id;
                }
                else {
                    $modelid = $this->userid;
                }

                if ($modelid) {
                    $org = \App\Model\helpdesk\Agent_panel\User_org::where('user_assign_organization.user_id', $modelid)
                            ->join('users', 'user_assign_organization.user_id', '=', 'users.id')
                            ->where('users.active', '=', 1)
                            ->where('users.is_delete', '=', 0)
                            ->pluck('user_assign_organization.org_id')
                            ->toArray();
                    if (count($org) > 0) {
                        $agents =\App\Model\helpdesk\Agent_panel\User_org::whereIn('org_id',$org)->where('role','=','manager')->select('user_id as organization_manager')->get()->toArray();
                         }
                }
                return $agents;
            case "last_respondent":
                if ($ticket) {
                    $agents = $ticket->thread()
                            ->whereNotnull('ticket_thread.user_id')
                            ->join('users', function ($join) {
                                return $join->on('ticket_thread.user_id', '=', 'users.id')
                                        ->where('users.active', '=', 1)
                                        ->where('users.is_delete', '=', 0);
                            })
                            ->orderBy('ticket_thread.id', 'desc')
                            ->select('users.id as last_respondent')
                            ->first()
                            ->toArray();
                }
                return $agents;
            case "assigned_agent_team":
                if ($ticket) {
                    $assigned = $ticket->assigned()
                            ->where('users.active', '=', 1)
                            ->where('users.is_delete', '=', 0)
                            ->value('id')
//                            ->get()
                    ;
                    $agents   = ['assigned_agent_team' => $assigned];
                }

                return $agents;
            case "all_department_manager":
                $manager_ids=\App\Model\helpdesk\Agent\DepartmentAssignManager::where('id','!=',0)->pluck('manager_id')->toArray();
                if ($manager_ids) {
                    $manager_id=array_unique($manager_ids);

                    $agents = \App\Model\helpdesk\Agent\DepartmentAssignManager::
                              select('manager_id as all_department_manager')
                            ->join('users', function ($join) {
                                $join->on('manager_id', '=', 'users.id')
                                ->where('users.active', '=', 1)
                                ->where('users.is_delete', '=', 0);
                            })
                            ->whereIn('manager_id', $manager_id)
                            ->get()
                            ->toArray();
                } else {
                    $agents = [];
                }
              return $agents;
            case "all_team_lead":
                $agents = \App\Model\helpdesk\Agent\Teams::where('teams.status', 1)
                        ->join('users', function ($join) {
                            $join->on('teams.team_lead', '=', 'users.id')
                            ->where('users.active', '=', 1)
                            ->where('users.is_delete', '=', 0);
                        })
                        ->select('teams.team_lead as all_team_lead')
                        ->get()
                        ->toArray();
                return $agents;
            case "client":
                //dd($this);
                if ($ticket) {
                    $agents = ['client' => $ticket->user_id];
                }
                elseif ($this->userid) {
                    $agents = ['client' => $this->userid];
                }

                return $agents;
            case "new_user":
                if ($this->userid) {
                    $agents = ['' => $this->userid];
                }
                return $agents;
        }
    }
    public function ccDisable()
    {
        $this->cc            = false;
        $this->message['cc'] = [];
    }
    public function ccEnable($ticket, $u_id)
    {
        if ($ticket) {
            if ($this->key === 'reply_alert' || $this->key === 'reply_notification_alert') {
                $this->cc            = true;
                $this->message['cc'] = $this->getCc($ticket);
                //dd($this);
            }
        }
    }
    public function getCc($ticket)
    {
        $ccs    = $this->cc;
        $emails = [];
        if ($ticket && $ccs) {
            $collab = $ticket->collaborator()->get();
            if ($collab->count() > 0) {
                foreach ($collab as $col) {
                    $user = $col->userBelongs()
                            ->whereNotnull('email')
                            ->where('is_delete', 0)
                            ->select('email')
                            ->first();
                    if ($user) {
                        $emails[] = $user->email;
                    }
                }
            }
        }
        return $emails;
    }
    public function sendSms()
    {
        if ($this->isMode('sms') && $this->send_mail) {
            //put check for SMS plugin and settings
            if ($this->checkPluginSetup()) {
                $users     = $this->getField('mobile', false, true, ['user_name', 'first_name', 'last_name', 'role', 'country_code', 'mobile', 'user_language', 'email']);
                $variables = $this->variable;
                $message   = $this->message;
                $ticket    = $this->getTicket();
                $ticket    = null;
                if ($ticket) {
                    $ticket = $ticket->toArray();
                }

                foreach ($users as $user) {
                    $sms_controller = new \App\Plugins\SMS\Controllers\MsgNotificationController;
                    $sms_controller->notifyBySMS($user->toArray(), $variables, $message, $ticket);
                }
            }
            else {
                loging('alert & notification', Lang::get('lang.can-not-send-message-sms-plugin-not-active'), 'info');
            }
        }
    }
    public function getTicketId($model)
    {
        switch ($model->getTable()) {
            case "tickets":
                return $model->id;
            case "ticket_thread":
                return $model->ticket_id;
        }
    }
    public function ticket($id)
    {
        return \App\Model\helpdesk\Ticket\Tickets::where('id', $id)->first();
    }
    public function getType($change)
    {
        if ($change) {
            if (checkArray('response_due', $change)) {
                return 'response_due';
            }
            if (checkArray('resolve_due', $change)) {
                return 'resolve_due';
            }
            return 'system';
        }
    }
    public function getBody($change, $model, $inapp = false)
    {
        $auth_username = Lang::get('lang.system');
        if (\Auth::user()) {
            $auth_username = "<a href=" . faveoUrl('user/' . \Auth::user()->id) . ">" . \Auth::user()->user_name . "</a>";
        }
        return $this->getSchemas($change, $model, $auth_username, $inapp);
    }
    public function getSchemas($change, $model, $auth_username, $inapp)
    {
        //dd($change,$model);
        $content = "";
        if ($this->key == "new_ticket_alert") {
            $content = trans('lang.created.ticket', ['subject' => "<b>" . title($model->id) . "</b>", 'created_at' => faveoDate($model->created_at)]);
        }
        elseif (count($change) > 0) {
            $counter = 0;
            foreach ($change as $key => $value) {
                $get_content = $this->getContent($key, $value, $model, $auth_username, $inapp);
                if ($get_content) {
                    $this->authUserid = $this->authUserid($key);
                    $content .= ($counter == 0) ? $get_content : ", ".$get_content;
                    $counter++;
                }
            }
        }
        elseif ($model && $model->getTable() == 'users') {
            $content = trans('lang.new-user-register', ['name' => $model->name(), 'created' => faveoDate($model->created_at)]);
        }
        elseif ($model && $model->getTable() == 'ticket_thread' && ($this->key == 'reply_alert'
                || $this->key == 'reply_notification_alert')) {
            $this->authUserid = $model->user_id;
            $content          = trans('lang.reply.notification', ['title' => ticketNumber($this->ticketid), 'created' => faveoDate($model->created_at)]);
        }
        elseif($model->getTable() == 'backups') {
            $content = trans('lang.backup_completed_successfully',['version'=>$model->version]);
        }
        else {
            //Servicedesk to push in app notification
            \Event::dispatch('push-in-app-notification', [&$content, $model, $this->key]);
        }
        //dd($content);
        return $content;
    }
    public function getContent($key, $value, $model, $auth_username, $inapp)
    {
        // dd($key,$value,$model->title);
        $new        = $this->switchNewSchema($key, $value, $model, $auth_username);
        $old        = $this->switchOldSchema($key, $value, $model, $auth_username);
        $tz         = timezone();
        $created_at = \Carbon\Carbon::now()->tz($tz);
        switch ($key) {
            case 'priority_id':
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'Priority', 'created_at' => faveoDate($created_at), 'old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.update', ['model' => 'Priority', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $new]);
            case 'source':
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'Source', 'created_at' => faveoDate($created_at), 'old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.update', ['model' => 'Source', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $new]);
            case 'title':
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'Title', 'created_at' => faveoDate($created_at), 'old' => "<b>" . utfEncoding($model->title) . "</b>", 'new' => "<b>" . utfEncoding($value) . "</b>", 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.update', ['model' => 'Title', 'created_at' => $created_at->tz($tz), 'old' => "<b>" . utfEncoding($model->title) . "</b>", 'new' => "<b>" . utfEncoding($value) . "</b>"]);
            case 'help_topic_id':
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'Help topic', 'created_at' => faveoDate($created_at), 'old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.update', ['model' => 'Help topic', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $new]);
            case 'sla':
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'SLA', 'created_at' => faveoDate($created_at), 'old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.update', ['model' => 'SLA', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $new]);
            case 'status':
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'Status', 'created_at' => faveoDate($created_at), 'old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.update', ['model' => 'Status', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $new]);
            case 'team_id':
                if ($new == null || $new == '') {
                    return false;
                }

                if ($inapp == true) {
                    return trans('lang.notification.assigned.inapp', ['old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.assigned', ['old' => $old, 'new' => $new]);
            case 'assigned_to':
                if ($new == null || $new == '') {
                    return false;
                }

                if ($value == $this->authUserid) {
                    if ($inapp == true) {
                        return trans('lang.notification.assigned.myself.inapp', ['old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                    }

                    return trans('lang.notification.assigned.myself', ['old' => $old, 'new' => $new]);
                }
                else {
                    if ($inapp == true) {
                        return trans('lang.notification.assigned.inapp', ['old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                    }

                    return trans('lang.notification.assigned', ['old' => $old, 'new' => $new]);
                }
            //no break
            case 'user_id':
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'Requester', 'created_at' => faveoDate($created_at), 'old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.update', ['model' => 'Requester', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $new]);
            case 'dept_id':
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'Department', 'created_at' => faveoDate($created_at), 'old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.update', ['model' => 'Department', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $new]);
            case "duedate":
                if ($inapp == true) {
                    return trans('lang.notification.duedate.inapp', ['model' => 'Duedate', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $value, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.duedate', ['model' => 'Duedate', 'new' => carbon($value)->tz($tz)]);
            case "note":
                if ($inapp == true) {
                    return trans('lang.notification.note.inapp', ['model' => 'Internal Note', 'new' => $value, 'ticket' => ticketNumber($this->ticketid)]);
                }

                return trans('lang.notification.note', ['model' => 'Internal Note', 'new' => $value]);
            case "type":
                if($old == 'none' || $new == 'none'){
                    $activityType = ($old == 'none') ? Lang::get('lang.added') : Lang::get('lang.removed');
                    $type = ($old == 'none') ? $new : $old;
                    if ($inapp == true) {
                        return trans('lang.changed.type.inapp', ['activityType' => $activityType, 'type' => $type]);
                    }
                    return trans('lang.changed.type', ['activityType' => $activityType, 'type' => $type]);
                }
                if ($inapp == true) {
                    return trans('lang.notification.update.inapp', ['model' => 'type', 'created_at' => faveoDate($created_at), 'old' => $old, 'new' => $new, 'ticket' => ticketNumber($this->ticketid)]);
                }
                return trans('lang.notification.update', ['model' => 'type', 'created_at' => $created_at->tz($tz), 'old' => $old, 'new' => $new]);
        }
    }
    public function switchNewSchema($key, $value, $model, $auth_username)
    {
        switch ($key) {
            case 'priority_id':
                $priority = "";
                $schema   = \App\Model\helpdesk\Ticket\Ticket_Priority::where('priority_id', $value)->select('priority')->first();
                if ($schema) {
                    $priority = "<b>" . $schema->priority . "</b>";
                }

                return $priority;
            case 'source':
                $source = "";
                $schema = \App\Model\helpdesk\Ticket\Ticket_source::where('id', $value)->select('name')->first();
                if ($schema) {
                    $source = "<b>" . $schema->name . "</b>";
                }

                return $source;
            case 'title':
                return "<b>" . $value . "</b>";

            case 'help_topic_id':
                $topic  = "";
                $schema = \App\Model\helpdesk\Manage\Help_topic::where('id', $value)->select('topic')->first();
                if ($schema) {
                    $topic = "<b>" . $schema->topic . "</b>";
                }

                return $topic;

            case 'sla':
                $sla    = "";
                $schema = \App\Model\helpdesk\Manage\Sla\Sla_plan::where('id', $value)->select('name')->first();
                if ($schema) {
                    $sla = "<b>" . $schema->name . "</b>";
                }

                return $sla;

            case 'status':
                $status = "";
                $schema = \App\Model\helpdesk\Ticket\Ticket_Status::where('id', $value)->select('name')->first();
                if ($schema) {
                    $status = "<b>" . $schema->name . "</b>";
                }

                return $status;
            case 'assigned_to':
                $assigned = "";
                $schema   = \App\User::where('id', $value)->select('first_name', 'last_name', 'user_name')->first();
                if ($schema) {
                    $assigned = "<b>" . $schema->name() . "</b>";
                }

                return $assigned;
            case 'user_id':
                $user   = "";
                $schema = \App\User::where('id', $value)->select('first_name', 'last_name', 'user_name')->first();
                if ($schema) {
                    $user = "<b>" . $schema->name() . "</b>";
                }

                return $user;
            case 'dept_id':
                $department = "";
                $schema     = \App\Model\helpdesk\Agent\Department::where('id', $value)->select('name')->first();
                if ($schema) {
                    $department = "<b>" . $schema->name . "</b>";
                }

                return $department;
            case 'team_id':
                $team   = "";
                $schema = \App\Model\helpdesk\Agent\Teams::where('id', $value)->select('name')->first();
                if ($schema) {
                    $team = "<b>" . $schema->name . "</b>";
                }
                return $team;

            case 'type':
                $type = "";
                $schema = \App\Model\helpdesk\Manage\Tickettype::where('id', $value)->select('name')->first();
                if ($schema) {
                    $type = "<b>" . $schema->name. "</b>";
                }
                return ($type != '' || $type != null) ? $type : 'none';
        }
    }
    public function switchOldSchema($key, $value, $model, $auth_username)
    {
        switch ($key) {
            case 'priority_id':
                $schema = $model->priority()->select('priority', 'priority_id')->first();
                if ($schema) {
                    return "<b>" . $schema->priority . "</b>";
                }
            //no break
            case 'source':
                $source = "";
                $schema = $model->sources()->select('name', 'id')->first();
                if ($schema) {
                    $source = "<b>" . $schema->name . "</b>";
                }

                return $source;
            case 'title':
                $title  = "";
                $schema = $model->whereNotnull('title')->select('title')->first();
                if ($schema) {
                    $title = "<b>" . $schema->title . "</b>";
                }

                return $title;
            case 'help_topic_id':
                $topic  = "";
                $schema = $model->helptopic()->select('topic')->first();
                if ($schema) {
                    $topic = "<b>" . $schema->topic . "</b>";
                }

                return $topic;
            case 'sla':
                $sla    = "";
                $schema = $model->slaPlan()->select('name')->first();
                if ($schema) {
                    $sla = "<b>" . $schema->name . "</b>";
                }

                return $sla;
            case 'status':
                $status = "";
                $schema = $model->statuses()->select('name')->first();
                if ($schema) {
                    $status = "<b>" . $schema->name . "</b>";
                }

                return $status;
            case 'assigned_to':
                $assigned = "";
                $schema   = $model->assigned()->select('user_name', 'first_name', 'last_name', 'email')->first();
                if ($schema) {
                    $assigned = "<b>" . $schema->name() . "</b><";
                }

                return $assigned;
            case 'user_id':
                $user   = "";
                $schema = $model->user()->select('user_name', 'first_name', 'last_name', 'email')->first();
                if ($schema) {
                    $user = "<b>" . $schema->name() . "</b>";
                }

                return $user;
            case 'dept_id':
                $department = "";
                $schema     = $model->departments()->select('name')->first();
                if ($schema) {
                    $department = "<b>" . $schema->name . "</b>";
                }

                return $department;
            case 'team_id':
                $team   = "";
                $schema = $model->assignedTeam()->select('name')->first();
                if ($schema) {
                    $team = "<b>" . $schema->name . "</b>";
                }

                return $team;
            case 'type':
                $type = "";
                $schema = $model->types()->pluck('name')->first();
                if ($schema) {
                    $type = "<b>" . $schema . "</b>";
                }
                return ($type != '' || $type != null) ? $type : 'none';

        }
    }
    public function setParameters($array)
    {
        if (is_array($array) && count($array) > 0) {
            if (checkArray('ticketid', $array)) {
                $this->ticketid = checkArray('ticketid', $array);
            }

            $this->key      = checkArray('key', $array);
            $this->userid   = checkArray('userid', $array);
            $this->from     = checkArray('from', $array);
            $this->message  = checkArray('message', $array);
            $this->variable = checkArray('variable', $array);
            $this->thread   = checkArray('thread', $array);
            if (checkArray('send_mail', $array)) {
                $this->send_mail = checkArray('send_mail', $array);
            }

            if (checkArray('change', $array)) {
                $this->change = checkArray('change', $array);
            }

            if (checkArray('model', $array)) {
                $this->model = checkArray('model', $array);
            }
        }
    }
    public function setParameter($key, $value)
    {
        if ($key) {
            $this->$key = $value;
        }
    }
    public function getTicket()
    {
        $tickets = new \App\Model\helpdesk\Ticket\Tickets();
        $ticket  = $tickets->find($this->ticketid);
        if ($ticket) {
            return $ticket;
        }
    }
    public function postMail($email, $u_id = "")
    {
        $to_name = ($email['first_name'] == '') ? $email['user_name'] : $email['first_name'];
        $mail    = new \App\Http\Controllers\Common\PhpMailController();
        $ticket  = $this->getTicket();
        $this->ccEnable($ticket, $u_id);
        $to      = ['email' => $email['email'], 'name' => $to_name, 'preferred_language' => $email['user_language'], 'role' => $this->getRoleAfterCheckingForTicketReply($ticket, $email)];
        try {
            $mail->sendmail($this->from, $to, $this->message, $this->variable, $this->thread, $this->auto_respond);
        } catch (\Exception $ex) {
            //dd($ex);
        }

        $this->ccDisable();
        loging('alert & notification', 'Alert email has sent to ' . json_encode($to) . 'with ' . json_encode([$this->message, $this->variable]), 'info');
    }

    /**
     * Jugaad level 999999
     * When ticket reply is made by agents and the requester of the ticket is also an
     * agent then we must send the reply made to the requester and include CCs in them.
     * Since in PHPMailController mailTemplate() method removes CCs added by ccEnable method
     * if the templateCategory is not client_template and template name is ticket-reply but as
     * in the stated case template name will be "ticket-reply" we need to manipulate templateCategory
     * by making the requester agent role as "user" so templateCategory will be considered as
     * "client_template" and CCs are not removed while sending the reply.
     *
     */
    private function getRoleAfterCheckingForTicketReply($ticket, $email)
    {
        if($this->key != 'reply_alert' || !$ticket) return $email['role'];
        return $ticket->user_id == $email['id'] ? 'user' : $email['role'];
    }

    public function setDetails($array)
    {
        $collection = array_collapse($array);

        if (count($collection) > 0) {
            foreach ($collection as $key => $value) {
                if ($key == 'registration_notification_alert') {
                    $key = 'registration_alert';
                }
                if (str_contains($key, 'ticket_assign_alert')) {
                    $key = 'ticket_assign_alert';
                }

                $to      = checkArray('to', $value);
                $from      = checkArray('from', $value);
                $message   = checkArray('message', $value);
                $variables = checkArray('variable', $value);
                $ticketid  = checkArray('ticketid', $value);
                $send_mail = checkArray('send_mail', $value);
                $userid    = checkArray('userid', $value);
                $model     = checkArray('model', $value);
                $thread    = checkArray('thread', $value);
                if ($key == 'reply_alert') {
                    $this->auto_respond = false;
                }

                $this->setParameters(
                        [
                            'ticketid'  => $ticketid,
                            'key'       => $key,
                            'from'      => $from,
                            'message'   => $message,
                            'variable'  => $variables,
                            'send_mail' => $send_mail,
                            'userid'    => $userid,
                            'model'     => $model,
                            'thread'    => $thread,
                        ]
                );
                if ($key === 'new_user_alert' || $key === 'new_ticket_alert') {
                    $this->authUserid = $userid;
                    $this->userid     = $userid;
                }

                $to = $to ? $to : [];
                $this->send($to);
            }
        }
    }
    public function setFrom($ticket)
    {
        $phpmail    = new \App\Http\Controllers\Common\PhpMailController();
        $from       = $phpmail->mailfrom('1', $ticket->dept_id);
        $this->from = $from;
    }
    public function type($ticket)
    {
        $type = $this->key;
        if ($ticket && is_array($this->change) && key_exists('duedate', $this->change)) {
            $type = "response_due";
            if ($ticket->isanswered == 1) {
                $type = "resolve_due";
            }
        }

        return $type;
    }
    /**
     * @category function to check is msg91 settins has been set up or not
     * @param null
     * @return null
     *
     */
    public function checkPluginSetup()
    {
        //put check for SMS plugin and settings
        if (isPlugin('SMS')) {
            if (Schema::hasTable('sms')) {
                $sms = DB::table('sms')->get();
                if (count($sms) > 0) {
                    return true;
                }
            }
        }

        return false;
    }
    /**
     *
     *
     *
     *
     */
    public function groupCollectionbyField($users, $field, $show_field = ['email'])
    {
        if ($this->keysExistsinCollectionArray($users, $show_field) && $this->keysExistsinCollectionArray($users, $field)) {
            $tmp = [];
            foreach ($users as $user) {
                $tmp[$user[$field]][] = $this->setArrayValues($user, $show_field);
            }
        }

        return $tmp;
    }
    /**
     *
     *
     *
     *
     */
    public function keysExistsinCollectionArray($collection_array, $keys)
    {
        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (!array_key_exists($key, $collection_array->toArray()[0])) {
                    return false;
                }
            }

            return true;
        }
        else {
            return array_key_exists($keys, $collection_array->toArray()[0]);
        }
    }
    public function setArrayValues($user, $fields)
    {
        $array = [];
        foreach ($fields as $field) {
            $array[$field] = $user[$field];
        }

        return $array;
    }
    /**
     * @category function to get ticket's department signature
     * @param integer $id(ticket's id)
     * @return string $sign signature of department
     */
    public function getDepartmentSign($id)
    {
        $sign = "";
        $dept = \App\Model\helpdesk\Agent\Department::select('department_sign')->where('id', '=', $id)->first();
        if ($dept) {
            $sign = $dept->department_sign;
        }
        return $sign;
    }
    public function webPush($data)
    {
        try {
            $users = explode(',', $data['to']);
            $user1 = explode(',', $data['by']);
            $user1 = [];
            $users = array_merge($users, $user1);
            $users = array_unique($users);
            foreach ($users as $value) {
                if ($value) {
                    $webNotify = [
                        "message"       => User::where('id', $data['by'])->first()->user_name . " " . $data['message'],
                        "url"           => $data['url'],
                        "user_type"     => "admin",
                        "specific_user" => true,
                        "user_ids"      => [User::where('id', $value)->first()->hash_ids]
                    ];
                    \Log::info($webNotify);
                    $obj       = (new NotifyQueue("browser-notification", $webNotify));
                    $this->dispatch($obj);
                }
            }
        } catch (\Exception $ex) {
            loging('error', 'Error occured in webpush methond in NotificationController');
        }
    }

    public function createPushNotification($data){
        $pushNotify = new PushNotification;
        $subscribed = new Subscribed;
        $obj  = (new NotifyQueue("in-app-web-push", $data));
        $this->dispatch($obj);

    }










}
