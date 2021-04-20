<?php

namespace App\Model\helpdesk\Manage\Sla;

use Illuminate\Database\Eloquent\Model;

class BusinessHoursSchedulesCustomTime extends Model
{
   protected $table = 'business_open_custom_time';
    /*
      this is a custom Forms created by user himself

     */
    protected $fillable = ['id','business_schedule_id','open_time','close_time','created_at', 'updated_at'];
}