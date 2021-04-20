<?php

namespace App\Plugins\Facebook\Handlers;

use App\Plugins\Facebook\FB\PersistentData\PersistentDataInterface;


class FBLaravelPersistantDataHandler implements PersistentDataInterface
{
  /**
   * @var string Prefix to use for session variables.
   */
  protected $sessionPrefix = 'FBRLH_';

 
  public function get($key)
  {
    return \Session::get($this->sessionPrefix . $key);
  }

 
  public function set($key, $value)
  {
    \Session::put($this->sessionPrefix . $key, $value);
  }
}