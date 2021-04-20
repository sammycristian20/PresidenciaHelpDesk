<?php

namespace App\Plugins\Whatsapp\database\seeds\v_1_0_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\MailJob\Condition;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    $checkValue =  Condition::where('job','whatsapp')->value('value');

    if(!$checkValue) {
        Condition::create([
            "job"=>"whatsapp", "value"=>"everyFiveMinutes",
            "icon" => "fab fa-whatsapp", "command" => "whatsapp:fetch",
            "job_info" => "whatsapp-info", "plugin_job" => 1,
            "plugin_name" => "Whatsapp",
            'active'=>1,
        ]);
    }
  }
}
