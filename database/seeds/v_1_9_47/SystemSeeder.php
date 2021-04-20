<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Utility\Timezones;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         System::create(array(
             'id' => '1',
             'status' => '1',
             'department' => '1',
             'date_time_format' =>'F j, Y, g:i a',
             'time_zone_id' => Timezones::where('name', 'Europe/London')->value('id'),
           'content' => 'en',
             'version' => \Config::get('app.tags')
         ));
    }
}
