<?php

namespace App\Plugins\Ldap\database\seeds\v_2_2_2;

use database\seeds\DatabaseSeeder as Seeder;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;
use App\Model\MailJob\Condition;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
//      $this->addIsManagerKeysToFaveoAttributes();
      $this->updateCronTime();
    }

//    /**
//     * Adds Manager keys to faveo attributes
//     * @return null
//     */
//    private function addIsManagerKeysToFaveoAttributes()
//    {
//      LdapFaveoAttribute::updateOrCreate(['name'=>'is_department_manager'],['name'=>'is_department_manager','editable'=> true, 'ad_attribute_id'=>1]);
//      LdapFaveoAttribute::updateOrCreate(['name'=>'is_organization_manager'],['name'=>'is_organization_manager','editable'=> true, 'ad_attribute_id'=>1]);
//    }

    /**
     * Update cron time from 10 minutes to one hour
     * @return void
     */
    private function updateCronTime()
    {
      $condition = Condition::where('job', 'ldap')->where('value', 'everyTenMinutes')->first();

      if($condition){
        $condition->value = 'hourly';
        $condition->save();
      }
    }
}
