<?php

namespace database\seeds\v_3_5_0;

use App\Model\helpdesk\Settings\System;
use database\seeds\DatabaseSeeder as Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run()
    {
        $this->dateTimeFormatSeeder();
    }

    /**
     * Seeds default date time format
     */
    private function dateTimeFormatSeeder()
    {
        System::first()->update(['date_format'=>'F j, Y', 'time_farmat'=>'g:i a']);
    }

}

