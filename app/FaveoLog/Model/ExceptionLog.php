<?php

namespace App\FaveoLog\Model;

use App\BaseModel;

class ExceptionLog extends BaseModel
{
  protected $table    = 'exception_logs';

  protected $fillable = ['log_category_id', 'file', 'line', 'trace', 'message'];

  public function category()
  {
      return $this->belongsTo('App\FaveoLog\Model\LogCategory','log_category_id');
  }

}
