<?php

namespace App\Plugins\Facebook\Model;

use App\BaseModel;

class FbChannel extends BaseModel 
{
    protected $table = "fb_channel";
    protected $fillable = ['channel','via','message_id','con_id','user_id','ticket_id','username','posted_at','page_access_token','page_id'];

    /**
     * Get Channel of ticket
     * @param string $channel
     * @param string $via
     * @param mixed $userid
     * @return model $social
     */
    public function getChannelVia($channel,$via,$userid)
    {
        $social = $this->where('channel',$channel)->where('via',$via)->where('user_id',$userid)->first();
        return $social;
    }

    /**
     * Get Channel of ticket
     * @param string $channel
     * @param string $via
     * @param string $con_id
     * @return model $social
     */
    public function getChannelMessageid($channel,$via,$con_id)
    {
        $social = $this->where('channel',$channel)
                ->where('via',$via)
                ->where('con_id',$con_id)
                ->last();
        return $social;
    }
    
    /**
     * sets the posted_at attribute
     * @param mixed $value
     * @return void
     */
    public function setPostedAtAttribute($value)
    {
         $test = new \DateTime($value);
         $date = date_format($test, 'Y-m-d H:i:s');
         $this->attributes['posted_at'] = $date;
    }

    /**
     * sets the con_id attribute
     * @param mixed $value
     * @return void
     */
    public function setConIdAttribute($value)
     {
         if($value==""){
             $value = NULL;
         }
         $this->attributes['con_id'] = $value;
    }
}
