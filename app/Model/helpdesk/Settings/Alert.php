<?php

namespace App\Model\helpdesk\Settings;

use App\BaseModel;

class Alert extends BaseModel
{
    /* Using alert_notice table  */

    protected $table = 'settings_alert_notice';
    /* Set fillable fields in table */
    protected $fillable = ['key','value'];
    
    public function getValue($key){
        $value = "";
        $row = $this->where('key',$key)->first();
        if($row){
            $value = $row->value;
        }
        return $value;
    }
    
    public function isValueExists($key,$value){
        $check = NULL;
        $row = $this->where('key',$key)->whereRaw("find_in_set('$value',value)")->first();
        if($row){
            $check = true;
        }
        return $check;
    }
    
}
