<?php

namespace App\Model\kb;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Settings\CommonSettings;
use App\User;


/**
 * Define the Model of comment table.
 */
class Comment extends BaseModel
{
    protected $table = 'kb_comment';

    protected $fillable = ['article_id', 'name', 'email', 'website', 'comment', 'status','profile_pic','created_at','updated_at'];

    protected $htmlAble = ['comment'];

    public function article()
    {
        return $this->hasMany('App\Model\kb\Article','id','article_id');
    } 

    /**
     * If email exist in user table then will return user profile pic else default image will return
     */
    public function getProfilePicAttribute()
    {
        //get user info
        $userInfo = User::where('email', $this->email)->select('email', 'profile_pic')->first();
         
        if($userInfo){
          return $userInfo->profile_pic;
        }
        
        //check cdn settings
        if(isCdnActive()){
          return \Gravatar::src($this->email);
        }

        return assetLink('image', 'contacthead');
    }
}
