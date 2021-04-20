<?php

namespace App\Model\Listener;

use Illuminate\Database\Eloquent\Model;

class Listener extends Model
{
    protected $table = 'listeners';
    protected $fillable = ['name','status','description','performed_by','order','rule_match'];
    
    public function events(){
        return $this->hasMany('App\Model\Listener\ListenerEvent','listener_id');
    }
    
    public function rules(){
        return $this->hasMany('App\Model\Listener\ListenerRule','listener_id');
    }
    
    public function actions(){
        return $this->hasMany('App\Model\Listener\ListenerAction','listener_id');
    }
}
