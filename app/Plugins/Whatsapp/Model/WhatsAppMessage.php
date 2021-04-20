<?php
namespace App\Plugins\Whatsapp\Model;

use App\BaseModel;

class WhatsAppMessage extends BaseModel
{
    protected $table = "whatsapp_messages";
    protected $fillable = [
        'from',
        'ticket_id',
        'posted_at'
    ];
}