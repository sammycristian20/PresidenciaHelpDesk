<?php


namespace App\Plugins\Facebook\Model;


use Illuminate\Database\Eloquent\Model;

class FacebookMessage extends Model
{
    protected $table = 'facebook_messages';

    protected $fillable = ['sender_id','message_id','attachment_urls','posted_at','ticket_id','processed','page_id','message'];
}