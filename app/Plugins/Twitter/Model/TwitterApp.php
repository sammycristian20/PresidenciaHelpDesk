<?php

namespace App\Plugins\Twitter\Model;

use App\BaseModel;
use App\Plugins\Twitter\Model\TwitterHashtags;

class TwitterApp extends BaseModel
{

    protected $table = "twitter_app";
    protected $fillable =[
        'id','consumer_api_key',
        'consumer_api_secret',
        'access_token',
        'access_token_secret',
        'reply_interval',
    ];

    public function hashtags()
    {
        return $this->hasMany(TwitterHashtags::class,'app_id');
    }

}