<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Notification\Notification;
use App\Model\helpdesk\Notification\UserNotification;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\User;
use App\Model\helpdesk\Notification\NotificationType;

class NotificationController extends Controller {

    /**
     * ******************************************** 
     * Class Notification Controller
     * ********************************************
     * This controller is used to generate in app notification
     * under the folling occurrence 
     * 1. Ticket Creation
     * 2. Ticket Reply
     * 3. User Creation
     * 
     * @author      Ladybird <info@ladybirdweb.com>
     */
    public $user;

    /**
     * Constructor
     */
    public function __construct() {

        $user = new User();
        $this->user = $user;
        // checking authentication
        $this->middleware('auth');
        // checking if role is agent
        $this->middleware('role.agent');
    }

    /**
     * This function is used to create in app notifications.
     * @param type $model_id
     * @param type $userid_created
     * @param type $type_id
     * @param type $forwhome
     */
    public function create($model_id, $userid_created, $type_id, $forwhome = []) {
        try {
            if (empty($forwhome)) {
                $ticket = Tickets::where('id', '=', $model_id)->first();
                $thread = Ticket_Thread::where('ticket_id', '=', $ticket->id)->first();
                $client = $this->user->where('id' , '=', $ticket->user_id)->first();
                $sender_name = ($client->first_name != '' || $client->first_name != null)? $client->first_name.' '.$client->last_name : $client->user_name;
                $forwhome = $this->user->where('role', '=', 'agent')->where('primary_dpt', '=', $ticket->dept_id)->get();
                $forwhome2 = $this->user->where('role', '=', 'admin')->get();
                $forwhome = $forwhome->merge($forwhome2);
            }
            // system notification
            $notify = Notification::create(['model_id' => $model_id, 'userid_created' => $userid_created, 'type_id' => $type_id]);
            foreach ($forwhome as $agent) {
                $type_message = NotificationType::where('id', '=', $type_id)->first();
                UserNotification::create(['notification_id' => $notify->id, 'user_id' => $agent['id'], 'is_read' => 0]);
                $push = new \App\Http\Controllers\Common\PushNotificationController;
                if($type_id == 3) {
                    if ($agent->fcm_token || $agent->i_token) {
                        if ($agent->fcm_token !== null && $agent->fcm_token!=='0') {
                            $push->response($agent->fcm_token, $type_message->message, $ticket->ticket_number, $ticket->id, $sender_name, $thread->title);
                        }
                        if ($agent->i_token !== null && $agent->i_token!=='0') {
                            $push->response($agent->i_token, $type_message->message, $ticket->ticket_number, $ticket->id, $sender_name, $thread->title);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * This function is to mark all ticket to read status
     * @param type $id
     * @return int
     */
    public function markAllRead($id) {
        $markasread = UserNotification::where('user_id', '=', \Auth::user()->id)->where('is_read', '=', '0')->get();
        foreach ($markasread as $mark) {
            $mark->is_read = '1';
            $mark->save();
        }
        return 1;
    }

    /**
     * This function to mark read
     * @param type $id
     * @return int
     */
    public function markRead($id) {
        $markasread = UserNotification::where('notification_id', '=', $id)->where('user_id', '=', \Auth::user()->id)->where('is_read', '=', '0')->get();
        foreach ($markasread as $mark) {
            $mark->is_read = '1';
            $mark->save();
        }
        return 1;
    }

    /**
     * function to show all the notifications
     * @return type
     */
    public function show() {
        $notifications = $this->getNotifications();
        //dd($notifications);
        return view('notifications-all', compact('notifications'));
    }

    /**
     * function to delete notifications
     * @param type $id
     * @return int
     */
    public function delete($id) {
        $markasread = UserNotification::where('notification_id', '=', $id)->where('user_id', '=', \Auth::user()->id)->get();
        foreach ($markasread as $mark) {
            $mark->delete();
        }

        return 1;
    }

    public function deleteAll() {
        try{
        $notifications = new Notification();
        if ($notifications->count()>0) {
            foreach ($notifications->get() as $notification) {
                $notification->delete();
            }
        }
        $notifications->dummyDelete();
        return redirect()->back()->with('success', 'deleted');
        }  catch (\Exception $ex){
            return redirect()->back()->with('fails',$ex->getMessage());
        }
    }

    /**
     * get the page to list the notifications.
     * @return response
     */
    public static function getNotifications() {
        $notifications = UserNotification::with([
                    'notification.type' => function($query) {
                        $query->select('id', 'message', 'type');
                    }, 'users' => function($query) {
                        $query->select('id', 'email', 'profile_pic');
                    }, 'notification.model' => function($query) {
                        $query->select('id', 'ticket_number');
                    },
        ])->where('user_id', '=', \Auth::user()->id);
        return $notifications;
    }

    public function createNotification($data){
        return Notification::create([
                'message' => $data['message'],
                'to'      => $data['to'],
                'by'      => $data['by'],
                'table'   => $data['table'],
                'row_id'  => $data['row_id'],
                'url'     => $data['url'],
            ]);
    }

}
