<?php

namespace App\Plugins\Chat\Model;

use App\BaseModel;

class TawkChat extends BaseModel
{
    protected $table = 'tawk_chats';
    protected $fillable = ['chat_id','ticket_id','from','body'];
}