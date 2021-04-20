<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Theme\Portal;

class PortalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

	/* portal */
         Portal::create(['admin_header_color' => 'skin-yellow', 'agent_header_color' => 'skin-blue','client_header_color'=>NULL,'client_button_color' => NULL,'client_button_border_color' => NULL,'client_input_field_color' => NULL,'logo' => '0','icon' => '0']);
    }
}
