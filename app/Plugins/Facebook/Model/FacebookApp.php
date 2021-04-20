<?php

namespace App\Plugins\Facebook\Model;

use App\BaseModel;

class FacebookApp extends BaseModel
{
    protected $table = "facebook_app";
    protected $fillable =['id','app_id','secret','access_token','new_ticket_interval'];

    public function pages()
    {
        return $this->hasMany(FacebookPages::class,'app_id');
    }
}