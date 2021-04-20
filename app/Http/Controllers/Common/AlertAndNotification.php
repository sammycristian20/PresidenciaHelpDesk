<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\Alert;

class AlertAndNotification extends Controller {

   /**
   * ******************************************** 
   * Class Notification Controller
   * ********************************************
   * This controller is used to handle functionality or alerts and noticies
   * @author Ladybird <info@ladybirdweb.com>
   */

   private $alert;
   
   public function __construct(){
      $this->alert = new Alert;
   }

   public function checkAlertAndNotification($key = null){
      try{
         if($this->enabled($key)){
            $alertMode = $this->getAlertMode($key);
            $alertModePersons = $this->getAlertModePerson($key);
            $response = array("mode" => $alertMode, "persons" => $alertModePersons);
            return $response;
         }
      }
      catch(\Exception $e){
         \Log::info("Exception caught :  ".$e->getMessage());
      }
   }


   private function enabled($key){
      try{
         $status = (int)$this->alert->where("key", $key)->first()->value;
         if($status){
            return true;
         }
         return false;
      }
      catch(\Exception $e){
         \Log::error("Exception caught:    ".$e->getMessage());
         return false;
      }
      
   }


   private function getAlertMode($key){
      $mode = $key."_mode";
      $activeModes = $this->alert->where('key', $mode)->first()->value;
      $activeModes =  explode(',', $activeModes);
      return $activeModes;
   }

   private function getAlertModePerson($key){
      $mode = $key."_persons";
      $activeModePersons = $this->alert->where('key', $mode)->first()->value;
      $activeModePersons =  explode(',', $activeModePersons);
      return $activeModePersons;
   }

}

?>