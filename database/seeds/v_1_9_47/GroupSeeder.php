<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Agent\Permission;

class GroupSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $groups = [
            [
                'name'=>'Full Access Agents','group_status'=>'1','can_edit_ticket'=>'1','can_post_ticket'=>'1',
                'can_close_ticket'=>'1','can_assign_ticket'=>'1','can_transfer_ticket'=>'1','can_create_ticket'=>'1',
                'can_delete_ticket'=>'1','can_ban_email'=>'1','can_manage_canned'=>'1','can_view_agent_stats'=>'1',
                'department_access'=>'1'
            ],
            [
                'name'=>'Edit Ticket Agents','group_status'=>'1','can_edit_ticket'=>'1','can_post_ticket'=>'1',
                'can_close_ticket'=>'1','can_assign_ticket'=>'1','can_create_ticket'=>'1','can_transfer_ticket'=>'1',
                'can_delete_ticket'=>'0','can_ban_email'=>'0','can_manage_canned'=>'1','can_view_agent_stats'=>'1',
                'department_access'=>'1'
            ],
            [
                'name'=>'Delete Ticket Agents','group_status'=>'1','can_edit_ticket'=>'0','can_post_ticket'=>'1',
                'can_close_ticket'=>'1','can_assign_ticket'=>'1','can_create_ticket'=>'1','can_transfer_ticket'=>'1',
                'can_delete_ticket'=>'1','can_ban_email'=>'0','can_manage_canned'=>'1','can_view_agent_stats'=>'1',
                'department_access'=>'1'
            ],
            [
                'name'=>'Ban User Agents','group_status'=>'1','can_edit_ticket'=>'0','can_post_ticket'=>'1',
                'can_close_ticket'=>'1','can_assign_ticket'=>'1','can_create_ticket'=>'1','can_transfer_ticket'=>'1',
                'can_delete_ticket'=>'0','can_ban_email'=>'1','can_manage_canned'=>'1','can_view_agent_stats'=>'1',
                'department_access'=>'1'
            ],
        ];
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        foreach($groups as $group){
            Permission::create($group);
        }

    }

}
