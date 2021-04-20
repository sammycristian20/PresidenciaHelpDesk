<?php

namespace App\Plugins\Ldap\database\seeds\v_1_0_0;

use App\Plugins\Ldap\Model\Ldap;
use database\seeds\DatabaseSeeder as Seeder;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    // To avoid creating multiple records for the clients who are using version 2.0.0 because
    // updater is built in 2.2.0
//    if(!Ldap::count()){
////      Ldap::create();
//    }
  }
}
