<?php

namespace App\Plugins\Ldap\database\seeds\v_2_0_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\MailJob\Condition;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    $checkValue =  Condition::where('job','ldap')->value('value');

    if(!$checkValue) {
        Condition::create([
            "job"=>"ldap", "value"=>"everyTenMinutes",
            "icon" => "fa fa-cloud-download", "command" => "ldap:sync",
            "job_info" => "ldap-info", "plugin_job" => 1,
            "plugin_name" => "Ldap",
            'active'=>1,
        ]);
    }
  }
}
