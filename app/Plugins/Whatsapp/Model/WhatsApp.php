<?php
namespace App\Plugins\Whatsapp\Model;

use App\BaseModel;

class WhatsApp extends BaseModel
{
    protected $table = "whatsapp";
    protected $fillable = [
        'sid',
        'token',
        'business_phone',
        'is_image_inline',
        'webhook_url',
        'name',
        'new_ticket_interval',
        'template'
    ];
}