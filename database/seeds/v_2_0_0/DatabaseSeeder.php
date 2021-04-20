<?php

namespace database\seeds\v_2_0_0;

use database\seeds\DatabaseSeeder as Seeder;
use DB;
use App\Model\helpdesk\Settings\CommonSettings;

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
        $this->formSeeder();
        $this->migrateOldFormToNew();
        $this->switchAllowRegistrationValue();
    }
    /**
     * Ticket settings seeder
     * @return null
     */
    private function formSeeder()
    {
      $this->call(FormSeeder::class);
    }

    /**
     * Migrate old form to new
     * @return null
     */
    private function migrateOldFormToNew()
    {
      $this->call(FormMigration::class);
    }

    /**
     * Makes user_registration in common settings table from 0->1 and 1->0.
     * REASON : user_registration values were getting stored in opposite way in the database,
     * this is for the correction
     * @return null
     */
    private function switchAllowRegistrationValue()
    {
        $userRegisterationCurrentValue = CommonSettings::where('option_name', 'user_registration')->value('status');
        CommonSettings::updateOrCreate(['option_name'=>'user_registration'], ['status'=> (int)!$userRegisterationCurrentValue]);

        $createTicketCurrentValue = CommonSettings::where('option_name', 'allow_users_to_create_ticket')->value('status');
        CommonSettings::updateOrCreate(['option_name'=>'allow_users_to_create_ticket'], ['status'=> (int)!$createTicketCurrentValue]);
    }
}
