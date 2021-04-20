<?php

namespace App\Model\helpdesk\Notification;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model {

   protected $table = 'in_app_push_notifications';
   protected $fillable = [
     'subscribed_user_id', 'message', 'url', 'status', 'created_at', 'updated_at',
   ];
}