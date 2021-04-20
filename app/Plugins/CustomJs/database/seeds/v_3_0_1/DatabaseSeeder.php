<?php

namespace App\Plugins\CustomJs\database\seeds\v_3_0_1;

use App\Plugins\CustomJs\Model\CustomJs;
use database\seeds\DatabaseSeeder as Seeder;

/**
 * Database seeder class to udpate existing records in custom_js table
 * for route update of ticket thread to ensure the existing scripts
 * which are running on thread(ticket timeline) page run without changing
 * or updating database manually using SQL 
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
    	CustomJs::where('parameter', 'thread/{id}')->update([
    		'parameter' => 'thread/{ticketId}'
    	]);
    }
}
