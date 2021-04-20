<?php

namespace App\FaveoLog\Model;

use App\BaseModel;

class LogCategory extends BaseModel
{

  protected $table    = 'log_categories';

  public $timestamps = false;

  protected $fillable = ['name'];

  public function exception()
  {
    return $this->hasMany('App\FaveoLog\Model\ExceptionLog');
  }

  public function mail()
  {
    return $this->hasMany('App\FaveoLog\Model\MailLog');
  }
}
