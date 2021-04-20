<?php

namespace App\Plugins\Facebook\Model;

use App\BaseModel;

class FacebookPages extends BaseModel
{
    protected $table = 'facebook_pages';
    protected $fillable =['id','active','app_id','page_id','access_token','page_name','logo'];

    public function app()
    {
        return $this->belongsTo(FacebookApp::class);
    }
}