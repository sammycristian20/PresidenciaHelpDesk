<?php

namespace App\Plugins\Facebook\database\seeds\v_1_0_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\MailJob\Condition;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    $checkValue =  Condition::where('job','facebook')->value('value');

    if(!$checkValue) {
        Condition::create([
            "job"=>"facebook", "value"=>"hourly",
            "icon" => "fab fa-facebook", "command" => "facebook:fetch",
            "job_info" => "facebook-info", "plugin_job" => 1,
            "plugin_name" => "Facebook",
            'active'=>1,
        ]);
    }
  }
}
