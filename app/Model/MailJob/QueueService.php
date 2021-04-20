<?php

namespace App\Model\MailJob;

use Illuminate\Database\Eloquent\Model;
use Lang;

class QueueService extends Model
{
    protected $table = "queue_services";
    protected $fillable = ["name","short_name","status"];
    
    public function extraFieldRelation(){
        $related = "App\Model\MailJob\FaveoQueue";
        return $this->hasMany($related,'service_id');
    }
    
    public function getExtraField($key){
        $value = "";
        $setting = $this->extraFieldRelation()->where('key',$key)->first();
        if($setting){
            $value = $setting->value;
        }
        return $value;
    }
    
    public function getName(){
        $name  = $this->attributes['name'];
        $id = $this->attributes['id'];
        if($name == 'Sync' or $name == 'Database')
            $html = $name;
        else
            $html = "<a href=".url('queue/'.$id).">".$name."</a>";
        return $html;
    }
    
    public function getStatus(){
        $status = $this->attributes['status'];
        $html = "<span class='btn btn-xs btn-default' style='color:red;pointer-events:none'>".Lang::get('lang.inactive')."</span>";
        if($status==1){
            $html = "<span class='btn btn-xs btn-default' style='color:green;pointer-events:none'>".Lang::get('lang.active')."</span>";
        }
        return $html;
    }
    
    public function getAction(){
        $id = $this->attributes['id'];
        $status = $this->attributes['status'];
        $html = "<a href=".url('queue/'.$id.'/activate')." class='btn btn-primary btn-xs'><i class='fas fa-check-circle'>&nbsp;</i>".Lang::get('lang.activate')."</a>";
        if($status==1){
            $html = "<button class='btn btn-primary btn-xs' disabled><i class='fas fa-check-circle'>&nbsp;</i>".Lang::get('lang.activate')."</button>";
        }
        return $html;
    }
    
    public function isActivate(){
        $check = true;
        $settings = $this->extraFieldRelation()->get();
        if($settings->count()==0){
            $check = false;
        }
        return $check;
    }
}
