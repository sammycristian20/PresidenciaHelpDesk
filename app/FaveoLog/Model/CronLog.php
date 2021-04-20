<?php

namespace App\FaveoLog\Model;

use App\BaseModel;

class CronLog extends BaseModel
{
  protected $table    = 'cron_logs';

  protected $fillable = ["command", "description", "status", "exception_log_id", "duration"];

  protected $hidden = ["exception_log_id"];

  public function exception()
  {
      return $this->belongsTo('App\FaveoLog\Model\ExceptionLog','exception_log_id');
  }
}
