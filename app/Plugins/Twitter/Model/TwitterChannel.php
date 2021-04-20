<?php

namespace App\Plugins\Twitter\Model;

use App\BaseModel;

class TwitterChannel extends BaseModel {
    
    protected $table = "twitter_channel";
    protected $fillable = ['channel','via','message_id','body','user_id','ticket_id','username','posted_at','page_access_token','hashtag','system_twitter_user'];

}
