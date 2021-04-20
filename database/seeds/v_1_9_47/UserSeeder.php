<?php

namespace database\seeds\v_1_9_47;

use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\User;
use database\seeds\DatabaseSeeder as Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // creating an user
        $user = User::create([
            'first_name'  => 'Demo',
            'last_name'   => 'Admin',
            'email'       => null,
            'user_name'   => 'demo_admin',
            'password'    => \Hash::make("demopass"),
            //'assign_group' => 1,
            'primary_dpt' => 1,
            'active'      => 1,
            'role'        => 'admin',
            'agent_tzone' => 81,
            'email_verify' => 1,
            'mobile_verify'=> 0
        ]);

        // admin does not require any permission
        // $permisions = '{"create_ticket":"1","edit_ticket":"1","close_ticket":"1","transfer_ticket":"1","delete_ticket":"1","assign_ticket":"1","access_kb":"1","ban_email":"1","organisation_document_upload":"1","email_verification":"1","mobile_verification":"1","account_activate":"1","report":"1","agent_account_activate":"1"}';

        // $user->permision()->create([
        //     'permision' => $permisions,
        // ]);

        // checking if the user have been created
        if ($user) {
            $dept_assign_agent = DepartmentAssignAgents::create([
                'department_id' => 1,
                'agent_id'      => 1,
            ]);
        }
    }
}
