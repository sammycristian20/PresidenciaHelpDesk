<?php

namespace database\seeds\v_1_9_47;

use App\Model\helpdesk\Manage\UserType;
use database\seeds\DatabaseSeeder as Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate table before seed
        $this->truncateTable();

        // Get user types
        $userTypes = $this->getUserTypes();

        // creating user types
        foreach ($userTypes as $userType) {
            $user = UserType::create(['name' => $userType]);
        }

    }

    /**
     * Truncate user_types table
     *
     * @return void
     */
    public function truncateTable()
    {
        DB::table('user_types')->truncate();
    }

    /**
     * User types
     *
     * @return array Array of user types
     */
    public function getUserTypes()
    {
        return array(
            'user',
            'agent',
            'admin',
            'department_manager',
            'team_lead',
        );
    }
}
