<?php

namespace App\Model\Listener;

use Illuminate\Database\Eloquent\Model;

class ListenerAction extends Model
{
    protected $table = 'listener_actions';
    protected $fillable = ['listener_id','key','value','meta','custom_action'];
    
    public function getMetaAttribute($value){
        if($value){
            $value = json_decode($value,true);
        }
        return $value;
    }
    public function setMetaAttribute($value){
        if($value && is_array($value)){
            $value = json_encode($value);
        }elseif($value=='null'){
            $value = null;
        }
        $this->attributes['meta'] = $value;
    }
    
    public function setCustomActionAttribute($value){
        if($value && $value!='null'){
            $value = json_encode($value);
        }else{
            $value = null;
        }
        $this->attributes['custom_action'] = $value;
    }
}
