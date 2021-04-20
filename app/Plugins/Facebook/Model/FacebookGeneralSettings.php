<?php


namespace App\Plugins\Facebook\Model;


use Illuminate\Database\Eloquent\Model;

class FacebookGeneralSettings extends Model
{
    protected $table = 'facebook_general_details';

    protected $fillable = ['fb_secret','hub_verify_token'];
}
