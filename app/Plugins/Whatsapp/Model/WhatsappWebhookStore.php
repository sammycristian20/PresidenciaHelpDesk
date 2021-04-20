<?php

namespace App\Plugins\Whatsapp\Model;

use App\BaseModel;

class WhatsappWebhookStore extends BaseModel
{
    protected $table = "whatsapp_webhook_store";
    protected $fillable = ['MediaContentType0','SmsMessageSid','NumMedia','From','MediaUrl0','Body'];
}