<?php

namespace database\seeds\v_2_2_3;

use App\Model\helpdesk\Settings\Security;
use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Theme\Portal;

class DatabaseSeeder extends Seeder
{

  /**
   * method to execute database seeds
   * @return void
   */
  public function run()
  {

    $this->colourSettings();



    $this->call(ReportSeeder::class);

    $this->securitySeeder();


  }

  /**
    * method to update system color settings
    * @return void
  */ 
  public function colourSettings(){

    /* portal */
    $potalInfo = Portal::where('id', 1)->first();

    $clientHeaderColor = $potalInfo->client_header_color != 'null' && $potalInfo->client_header_color ?  $potalInfo->client_header_color : '#009aba';

    $clientButtonColor = $potalInfo->client_button_color != 'null' && $potalInfo->client_button_color ? $potalInfo->client_button_color : '#009aba';

    $clientButtonBorderColor = $potalInfo->client_button_border_color != 'null' && $potalInfo->client_button_border_color ? $potalInfo->client_button_border_color : '#00c0ef';

    $clientInputFieldColor = $potalInfo->client_input_field_color != 'null' && $potalInfo->client_input_field_color ? $potalInfo->client_input_field_color : '#d2d6de';

    Portal::where('id', 1)->update(['client_header_color'=>$clientHeaderColor,'client_button_color' => $clientButtonColor,'client_button_border_color' => $clientButtonBorderColor,'client_input_field_color' => $clientInputFieldColor]);
  }


  private function securitySeeder()
  {
      $securityObject = Security::first();

      if($securityObject->backlist_threshold > 5){

          // making max login attempts as 5
          $securityObject->backlist_threshold = 5;

          $securityObject->save();
      }
  }


}
