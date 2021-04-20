<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Ticket\TicketStatusType;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Workflow\WorkflowClose;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TicketStatusType::truncate();
        Ticket_status::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
         /* Ticket Status Type */
        TicketStatusType::create(['id' => '1', 'name' => 'open']);
        TicketStatusType::create(['id' => '2', 'name' => 'closed']);
        TicketStatusType::create(['id' => '3', 'name' => 'archieved']);
        TicketStatusType::create(['id' => '4', 'name' => 'deleted']);
        TicketStatusType::create(['id' => '5', 'name' => 'approval']);
        TicketStatusType::create(['id' => '6', 'name' => 'spam']);
        TicketStatusType::create(['id' => '7', 'name' => 'unapproved']);
        /* Ticket status */
        Ticket_status::create(['name' => 'Open', 'default' => '1', 'visibility_for_client' => '1', 'message' => 'Ticket has been Reopened by {!!$user!!}', 'allow_client' => '1', 'visibility_for_agent' => '1', 'purpose_of_status' => '1', 'secondary_status' => null,
            'send_email' => json_encode(['client'=>'1','admin'=>'0','assigned_agent_team'=>'1']),
            'order' => '1', 'icon' => 'fa fa-clock-o', 'icon_color' => '#32c777']);

        Ticket_status::create(['name' => 'Resolved', 'default' => null, 'visibility_for_client' => '1', 'message' => 'Ticket has been Resolved by {!!$user!!}', 'allow_client' => '1', 'visibility_for_agent' => '1', 'purpose_of_status' => '2', 'secondary_status' => null,
            'send_email' => json_encode(['client'=>'1','admin'=>'0','assigned_agent_team'=>'1']),
            'order' => '2', 'icon' => 'fa fa-check-circle-o', 'icon_color' => '#5cb85c', 'halt_sla' => 1]);

        Ticket_status::create(['name' => 'Closed', 'default' => '1', 'visibility_for_client' => '1', 'message' => 'Ticket has been Closed by {!!$user!!}', 'allow_client' => '1', 'visibility_for_agent' => '1', 'purpose_of_status' => '2', 'secondary_status' => null,
            'send_email' => json_encode(['client'=>'1','admin'=>'0','assigned_agent_team'=>'1']),
            'order' => '3', 'icon' => 'fa fa-minus-circle', 'icon_color' => '#5cb85c', 'halt_sla' => 1]);

        // Ticket_status::create(['name' => 'Archived', 'default' => null, 'visibility_for_client' => '1', 'message' => 'Ticket has been Archived by {!!$user!!}', 'allow_client' => '1', 'visibility_for_agent' => '1', 'purpose_of_status' => '3', 'secondary_status' => null,
        //     'send_email' => json_encode(['client'=>'0','admin'=>'0','assigned_agent_team'=>'0']),
        //     'order' => '4', 'icon' => 'fa fa-trash', 'icon_color' => '#ff0000']);

        Ticket_status::create(['name' => 'Deleted', 'default' => '1', 'visibility_for_client' => '1', 'message' => 'Ticket has been Deleted by {!!$user!!}', 'allow_client' => '1', 'visibility_for_agent' => '1', 'purpose_of_status' => '4', 'secondary_status' => null,
            'send_email' => json_encode(['client'=>'0','admin'=>'0','assigned_agent_team'=>'0']),
            'order' => '4', 'icon' => 'fa fa-trash', 'icon_color' => '#f20630', 'halt_sla' => 1]);

        // Ticket_status::create(['name' => 'Unverified Status', 'default' => '0', 'visibility_for_client' => '1', 'message' => 'Approval requested by {!!$user!!}', 'allow_client' => '1', 'visibility_for_agent' => '1', 'purpose_of_status' => '1', 'secondary_status' => null,
        //     'send_email' => json_encode(['client'=>'0','admin'=>'0','assigned_agent_team'=>'0']),
        //     'order' => '6', 'icon' => 'fa fa-bell', 'icon_color' => '#f1ac0b']);


        Ticket_status::create(['name' => 'Request for close', 'default' => '1', 'visibility_for_client' => '1', 'message' => 'Approval requested by {!!$user!!}', 'allow_client' => '1', 'visibility_for_agent' => '1', 'purpose_of_status' => '5', 'secondary_status' => null,
            'send_email' => json_encode(['client'=>'0','admin'=>'0','assigned_agent_team'=>'0']),
            'order' => '5', 'icon' => 'fa fa-bell', 'icon_color' => '#0665f2', 'halt_sla' => 1]);

        Ticket_status::create(['name' => 'Spam', 'default' => '1', 'visibility_for_client' => '0', 'message' => 'Ticket has been marked as Spam by {!!$user!!}', 'allow_client' => '0', 'visibility_for_agent' => '1', 'purpose_of_status' => '6', 'secondary_status' => null,
            'send_email' => json_encode(['client'=>'0','admin'=>'0','assigned_agent_team'=>'0']),
            'order' => '6', 'icon' => 'glyphicon glyphicon-alert', 'icon_color' => '#f0ad4e', 'halt_sla' => 1]);

        Ticket_status::create(['name' => 'Unapproved', 'default' => '1', 'visibility_for_client' => '0', 'message' => 'Ticket has been marked as Unapproved by {!!$user!!}', 'allow_client' => '0', 'visibility_for_agent' => '1', 'purpose_of_status' => '7', 'secondary_status' => null,
            'send_email' => json_encode(['client'=>'0','admin'=>'1','assigned_agent_team'=>'0']),
            'order' => '7', 'icon' => 'fa fa-clock-o', 'icon_color' => '#f20630', 'halt_sla' => 1]);

        WorkflowClose::create(['id' => 1, 'days' => '2', 'condition' => 1, 'send_email' => 1, 'status' => 3]);

        Ticket_status::where('name','Open')->update(['auto_close' => 1]);
    }
}
