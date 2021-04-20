<?php

namespace database\seeds\v_2_1_8;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\MailJob\Condition;

class DatabaseSeeder extends Seeder
{

  /**
   * method to execute database seeds
   * @return void
   */
  public function run()
  {
    Condition::where('job','followup')->delete();
    $this->call(ManagementReportSeeder::class);
  }
}
