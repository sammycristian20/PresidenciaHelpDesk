<?php

namespace database\seeds\v_2_2_1;

use database\seeds\DatabaseSeeder as Seeder;
use App\FaveoReport\Models\ReportColumn;
use App\Model\helpdesk\Theme\Portal;

class DatabaseSeeder extends Seeder
{

  /**
   * method to execute database seeds
   * @return void
   */
  public function run()
  {
  	ReportColumn::where('type', '')->update(['type' => 'management_report']);

    $this->colourSettings();
  }

  private function seedReportColumnsOrder()
  {
    // updating order by its id
    $reportColumns = ReportColumn::get();
    foreach ($reportColumns as $reportColumn){
      $reportColumn->order = $reportColumn->id;
    }
  }

  /**
    * method to update system color settings
    * @return void
  */ 
  public function colourSettings(){

    /* portal */
    $potalInfo = Portal::where('id', 1)->first();
    $clientHeaderColor = $potalInfo->client_header_color ? : '#009aba';
    $clientButtonColor = $potalInfo->client_button_color ? : '#009aba';
    $clientButtonBorderColor = $potalInfo->client_button_border_color ? : '#00c0ef';
    $clientInputFieldColor = $potalInfo->client_input_field_color ? : '#d2d6de';

    Portal::where('id', 1)->update(['client_header_color'=>$clientHeaderColor,'client_button_color' => $clientButtonColor,'client_button_border_color' => $clientButtonBorderColor,'client_input_field_color' => $clientInputFieldColor]);
  }
}
