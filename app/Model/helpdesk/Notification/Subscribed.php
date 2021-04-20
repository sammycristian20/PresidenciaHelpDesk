<?php

namespace App\Model\helpdesk\Notification;

use Illuminate\Database\Eloquent\Model;

class Subscribed extends Model {

   protected $table = 'subscribed_users';
   protected $fillable = [
     'user_id', 'browser_name', 'version', 'user_agent', 'platform','created_at', 'updated_at',
   ];
}


?>