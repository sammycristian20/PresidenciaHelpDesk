<?php

namespace App\Plugins\Twitter\database\seeds\v_1_0_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\MailJob\Condition;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    $checkValue =  Condition::where('job','twitter')->value('value');

    if(!$checkValue) {
        Condition::create([
            "job"=>"twitter", "value"=>"hourly",
            "icon" => "fab fa-twitter", "command" => "twitter:fetch",
            "job_info" => "twitter-info", "plugin_job" => 1,
            "plugin_name" => "Twitter",
            'active'=>1,
        ]);
    }
  }
}
