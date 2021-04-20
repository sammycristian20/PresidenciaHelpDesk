<?php


namespace App\Plugins\Facebook\Model;


use Illuminate\Database\Eloquent\Model;

class FacebookCredential extends Model
{
    protected $table = 'facebook_credentials';

    protected $fillable = ['page_id','page_access_token','page_name','new_ticket_interval','active'];
}
