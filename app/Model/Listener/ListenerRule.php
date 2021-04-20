<?php

namespace App\Model\Listener;

use Illuminate\Database\Eloquent\Model;

class ListenerRule extends Model
{
    protected $table = 'listener_rules';
    protected $fillable = ['listener_id','key','condition','value','custom_rule'];
    
    public function setCustomRuleAttribute($value){
        if($value && $value!='null'){
            $value = json_encode($value);
        }else{
            $value = null;
        }
        $this->attributes['custom_rule'] = $value;
    }
}
