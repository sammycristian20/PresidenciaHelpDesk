<?php

namespace App\Model\helpdesk\Manage\Sla;

use Illuminate\Database\Eloquent\Model;

class BusinessHoliday extends Model
{
   	protected $table = 'business_holidays';
   
    protected $fillable = ['id','business_hours_id','name','date','created_at', 'updated_at'];
}