<?php

namespace App\Model\helpdesk\Utility;

use App\BaseModel;

class Timezones extends BaseModel
{
    public $timestamps = false;
    protected $table = 'timezone';
    protected $fillable = ['name', 'location'];
    protected $appends = ['timezone_name'];

    // added a new  accessor timezone_name
    public function getTimezoneNameAttribute()
    {
      $extractGMT = explode(' ', $this->location);
  	  $timezone = reset($extractGMT) . ' ' . $this->name;
      return $timezone;
    }
}
