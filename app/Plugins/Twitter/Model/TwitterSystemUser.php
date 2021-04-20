<?php


namespace App\Plugins\Twitter\Model;


use Illuminate\Database\Eloquent\Model;

class TwitterSystemUser extends Model
{
    protected $table = 'twitter_system_user_details';

    protected $fillable = ['user_id','user_name','screen_name'];
}