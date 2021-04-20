<?php

namespace App\Plugins\Twitter\Model;

use Illuminate\Database\Eloquent\Model;

class TwitterHashtags extends Model
{
    protected $table = 'twitter_hashtags';

    protected $fillable = ['hashtag','app_id'];

    public function app()
    {
        return $this->belongsTo(TwitterApp::class);
    }
}
