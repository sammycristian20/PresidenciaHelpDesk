<?php

namespace App\Model\helpdesk\Settings;

use App\BaseModel;
use App\Model\helpdesk\Utility\Timezones;
use App\Model\helpdesk\Utility\Date_format as DateFormat;
use App\Model\helpdesk\Utility\Time_format as TimeFormat;

class System extends BaseModel
{
    /* Using System Table */

    protected $table    = 'settings_system';
    protected $fillable = [

        'id', 'status', 'name', 'department', 'page_size', 'log_level', 'purge_log', 'name_format',
        'time_format', 'date_format', 'date_time_format', 'day_date_time', 'time_zone_id', 'content', 'api_key', 'api_enable', 'api_key_mandatory', 'version', 'serial_key', 'order_number'
    ];

    protected $appends = ['time_zone'];

    /**
     * relationship with TimeZone
     */
    public function systemTimeZone() {
        return $this->belongsTo(Timezones::class, 'time_zone_id');
    }

    /** 
     * accessor to get timezone name
     * time_zone column had timezone name, which was stored in settings_system table
     * it's changed to time_zone_id (now instead of timezone name , timezone is stored)
     * in many places System model is used for fetching time_zone name
     * adding accessor, so that in minimum places changes need to be done, wherever code is breaking
     * @return string timezone name
     */
    public function getTimeZoneAttribute()
    {
        return $this->systemTimeZone ? $this->systemTimeZone->name : '';
    }

    /**
     * relationship for date format
     */
    public function dateFormat(){
        return $this->hasOne(DateFormat::class, 'format', 'date_format')->where('is_active', 1);
    }

    /**
     * relationship for time format
     */
    public function timeFormat(){
        return $this->hasOne(timeFormat::class, 'format', 'time_format')->where('is_active', 1);
    }
}
