<?php

namespace database\seeds\v_3_3_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Manage\Sla\BusinessHours;


class DatabaseSeeder extends Seeder
{
    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        
        $this->businessHourDefaultTimeZoneSeeder();
    }


 private function businessHourDefaultTimeZoneSeeder()
    {
        //system timezone
        $tzName = System::first()->systemTimeZone()->value('name');
        //default businesshours timezone
        $businessHoursTimeZone = BusinessHours::first()->value('timezone');

        if(!$businessHoursTimeZone){

            BusinessHours::first()->update(['timezone'=>$tzName]);
        }

    }
}
