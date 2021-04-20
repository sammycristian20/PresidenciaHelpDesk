<?php

namespace database\seeds\v_1_9_51;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Settings\System;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Job Conditon Seeder
        $this->ticketSettingsSeeder();
    }
    /**
     * Ticket settings seeder
     * @return null
     */
    private function ticketSettingsSeeder()
    {
      Ticket::where('id',1)->update(['waiting_time' => 17520]);
    }    
}
