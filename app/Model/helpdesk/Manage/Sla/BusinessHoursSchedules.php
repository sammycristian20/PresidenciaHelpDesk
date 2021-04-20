<?php

namespace App\Model\helpdesk\Manage\Sla;

use Illuminate\Database\Eloquent\Model;

class BusinessHoursSchedules extends Model
{
   	protected $table = 'business_schedule';
    /*
      this is a custom Forms created by user himself

     */
         // public $timestamps = false;
    protected $fillable = ['business_hours_id','days','status'];
    
    public function custom(){
        $related = 'App\Model\helpdesk\Manage\Sla\BusinessHoursSchedulesCustomTime';
        $foreignKey = 'business_schedule_id';
        return $this->hasOne($related, $foreignKey);
    }
        
}