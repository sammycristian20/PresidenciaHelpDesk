<?php

namespace App\Plugins\Chat\Model;

use App\BaseModel;

class Chat extends BaseModel {
    protected $table = "chat";
    protected $fillable = ['name','short','status','url','department','helptopic','secret_key','script'];

    
    public function status($app){
        $check = false;
        $chat = $this->where('short',$app)->first();
        if($chat){
            if($chat->status=='true'){
                $check = true;
            }
        }
        return $check;
    }

    public function department() {
        return $this->belongsTo('App\Model\helpdesk\Agent\Department','department');
    }
    
    public function helptopic()
    {
        return $this->belongsTo('App\Model\helpdesk\Manage\Help_topic','helptopic');
    }

}