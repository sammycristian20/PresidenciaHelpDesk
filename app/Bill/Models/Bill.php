<?php

namespace App\Bill\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';
    protected $fillable = ['level','model_id','hours','billable','agent','ticket_id','note','amount_hourly',];
    
    
    public function setHoursAttribute($value){
        if($value){
            if(str_contains($value, ':')){
                $hours_array = explode(':', $value);
                $hour = (int)$hours_array[0];
                $min = (int)$hours_array[1];
                $convert_min_to_hour = floor($min/60);
                if($convert_min_to_hour>0){
                    $hour = $hour+$convert_min_to_hour;
                    $min = $min%60;
                }
                $value = $hour.".".$min;
            }
        }
        $this->attributes['hours']=$value;
    }
    
   
    
    
    public function billable(){
        $value = $this->attributes['billable'];
        
        if($value==1){
            $result = '<span style="color:green;">Yes</span>';
        }else{
            $result = '<span style="color:red;">No</span>';
        }
        return $result;
    }
    
    public function hours(){
        $value = $this->attributes['hours'];
        $result = '<span>'.$value.' Hours</span>';
        return $result;
    }
    
    public function amountPerHour(){
        $result = '--';
      
        $value = $this->attributes['amount_hourly'];
        
        if(!$value || $value==""){
            $value = "";
        }
        
        if($value!==""){
            $result = '<span>'.$value.'</span>';
        }
        return $result;
    }
    
    public function amount(){
        
        $amount = $this->attributes['amount_hourly'];
        $hour = $this->getHour($this->attributes['hours']);  
        if($amount){
            return '<span>'.$hour*$amount.'</span>';
        }
        return "--";
    }
    
    
    
    public function getAgent(){
        $out = "--";
        $agent = $this->attributes['agent'];
        $users = new \App\User();
        $user = $users->where('id',$agent)->select('id','first_name','last_name','user_name')->first();
        if($user){
            if($user->first_name || $user->last_name){
                $result = $user->first_name.' '.$user->last_name;
            }else{
                $result = $user->first_name.' '.$user->last_name;
            }
            $out = '<a href="'.url("user/".$user->id).'">'.$result.'</a>';
        }
        return $out;
    }
    
    public function getHour($hour){
        return str_replace(":", ".", $hour);
    }
}
