<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateType;
use App\Model\Common\Template;

class TemplateSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TemplateSet::truncate();
        TemplateType::truncate();
        Template::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        TemplateSet::create(['id' => '1', 'name' => 'default', 'active' => '1']);


        TemplateType::create(['id' => '1', 'name' => 'assign-ticket']);
        TemplateType::create(['id' => '2', 'name' => 'check-ticket']);
        TemplateType::create(['id' => '3', 'name' => 'close-ticket']);
        TemplateType::create(['id' => '4', 'name' => 'create-ticket']);
        TemplateType::create(['id' => '5', 'name' => 'create-ticket-agent']);
        TemplateType::create(['id' => '6', 'name' => 'create-ticket-by-agent']);
        TemplateType::create(['id' => '7', 'name' => 'registration-notification']);
        TemplateType::create(['id' => '8', 'name' => 'reset-password']);
        TemplateType::create(['id' => '9', 'name' => 'ticket-reply']);
        TemplateType::create(['id' => '10', 'name' => 'ticket-reply-agent']);
        TemplateType::create(['id' => '11', 'name' => 'email_verify']);
        TemplateType::create(['id' => '12', 'name' => 'response_due_violate']);
        TemplateType::create(['id' => '13', 'name' => 'resolve_due_violate']);
        TemplateType::create(['id' => '14', 'name' => 'team_assign_ticket']);
        TemplateType::create(['id' => '15', 'name' => 'reset_new_password']);
        TemplateType::create(['id' => '16', 'name' => 'internal_change']);
        TemplateType::create(['id' => '17', 'name' => 'response_due_approach']);
        TemplateType::create(['id' => '18', 'name' => 'resolve_due_approach']);
        TemplateType::create(['id' => '19', 'name' => 'new-user']);
        TemplateType::create(['id' => '20', 'name' => 'no_assign_message']);
        TemplateType::create(['id' => '21', 'name' => 'status-update']);
        TemplateType::create(['id' => '22', 'name' => 'approve-ticket']);
        TemplateType::create(['id' => '23', 'name' => 'invoice']);
        TemplateType::create(['id' => '24', 'name' => 'task-reminder']);
        TemplateType::create(['id' => '25', 'name' => 'task-created']);
        TemplateType::create(['id' => '26', 'name' => 'task-update']);
        TemplateType::create(['id' => '27', 'name' => 'task-status']);
        TemplateType::create(['id' => '28', 'name' => 'task-assigned']);
        TemplateType::create(['id' => '29', 'name' => 'task-deleted']);
        TemplateType::create(['id' => '30', 'name' => 'task-assigned-owner']);
        TemplateType::create(['id' => '31', 'name' => 'rating']);
        TemplateType::create(['id' => '32', 'name' => 'rating-confirmation']);
        TemplateType::create(['id' => '33', 'name' => 'report-export']);
        TemplateType::create(['id' => '34', 'name' => 'ticket-approval']);
        TemplateType::create(['id' => '35', 'name' => 'registration-and-verify']);

        //common templates
        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'name' => 'template-register-confirmation-with-account-details',
            'type' => '7',
            'subject' => 'Registration Confirmation',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'This email is confirmation that you are now registered at our helpdesk.<br /><br />'
                    .'<strong>Registered Email:</strong> {!! $new_user_email !!}<br />'
                    .'<strong>Password:</strong> {!! $user_password !!}<br /><br />'
                    .'You can visit the helpdesk to browse articles and contact us at any time: {!! $system_link !!}<br />'
                    .'Thank You.<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'name' => 'template-reset-password-link',
            'type' => '8',
            'subject' => 'Reset your Password',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'You asked to reset your password. To do so, please click this link:<br />'
                    .'{!! $password_reset_link !!}<br /><br />'
                    .'This will let you change your password to something new. If you did not ask for this, do not worry, we will keep your password safe.<br />'
                    .'Thank You.<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'name' => 'template-new-password',
            'type' => '15',
            'subject' => 'Password changed',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'Your password is successfully changed. Your new password is : {!! $user_password !!}<br /><br />'
                    .'Thank You.<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'name' => 'template-register-confirmation-with-activation-link',
            'type' => '11',
            'subject' => 'Verify your email address',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br/><br/>'
                    .'This is a verification mail for your email registered with us.<br/><br/>'
                    .'<strong>Registered Email:</strong> {!! $new_user_email !!}<br/><br/>'
                    .'Please click on the below link to verify your email address so you can use the system.<br/>'
                    .'{!! $account_activation_link !!}<br/><br/>'
                    .'Thank You.<br/><br/>'
                    .'Kind Regards,<br/>'
                    .'{!! $system_from !!}'
        ]);






        // client tepmlates
        Template::create([
            'variable' => '1',
            'name' => 'template-ticket-checking-wihtout-login-link',
            'type' => '2',
            'subject' => 'Check your Ticket',
            'template_category' => 'client-templates',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'Click the link below to view your requested ticket<br />'
                    .'{!! $ticket_link !!}<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}</p>'
        ]);

        Template::create([
            'variable' =>'0',
            'name' => 'template-ticket-creation-acknowledgement-client-by-client',
            'type' => '4',
            'template_category' => 'client-templates',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!}<br /><br />'
                    .'Thank you for contacting us. This is an automated response confirming the receipt of your ticket. Our team will get back to you as soon as possible. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies.<br /><br />'
                    .'<strong>Ticket ID:</strong> {!! $ticket_number !!} <br /><br />'
                    .'{!! $department_signature !!}<br />'
                    .'You can check the status of or update this ticket online at: {!! $system_link !!}<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}</p>'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'client-templates',
            'name' => 'template-ticket-status-update-client',
            'type' => '21',
            'subject' => '',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                .'This email is regarding your ticket with ID: {!! $ticket_number !!}.<br />'
                .'{!! $message_content !!}<br />'
                .'If you are not satisfied please respond to the ticket here {!! $ticket_link !!}<br /><br />'
                .'Thank You.<br /><br />'
                .'Kind Regards,<br />'
                .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '0',
            'name' => 'template-ticket-creation-acknowledgement-client-by-agent',
            'type' => '6',
            'template_category' => 'client-templates',
            'set_id' => '1',
            'subject' => '',
            'message' => 'Hello {!! $receiver_name !!}<br /><br />'
                    .'Thank you for contacting us. This is to confirm that our agent has logged your request in our support system. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies.<br /><br />'
                    .'<strong>Ticket ID:</strong> {!! $ticket_number !!} <br /><br />'
                    .'<strong>Request message:</strong> {!! $message_content !!} <br /><br />'
                    .'{!! $department_signature !!}<br />'
                    .'You can check the status of or update this ticket online at: {!! $system_link !!}<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}</p>'

        ]);

        Template::create([
            'variable' => '0',
            'name' => 'template-ticket-assignment-notice-to-client',
            'type' => '1',
            'template_category' => 'client-templates',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                        .'Your ticket with ID: {!! $ticket_number !!} has been assigned to one of our agents. They will contact you soon.<br /><br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}</p>'
        ]);

        Template::create([
            'variable' => '0',
            'template_category' => 'client-templates',
            'name' => 'template-ticket-reply-to-client-by-agent',
            'type' => '9',
            'set_id' => '1',
            'message' => '{!! $message_content !!}<br /><br />'
                        .'<strong>Ticket Details</strong>'
                        .'<strong>Ticket ID:</strong> {!! $ticket_number !!}<br /><br />'
                        .'{!! $agent_signature !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'client-templates',
            'name' => 'template-ticket-assigment-notice-to-team-client',
            'type' => '14',
            'subject' => '',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                        .'Your ticket with ID: {!! $ticket_number !!} has been assigned to our {!! $assigned_team_name !!} team. They will contact you soon.<br /><br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}</p>'
        ]);


        // Assigend agent templates
        Template::create([
            'variable' => '0',
            'name' => 'template-ticket-assignment-notice-to-assigned-agent',
            'type' => '1',
            'template_category' => 'assigend-agent-templates',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br /><strong>Ticket No:</strong> {!! $ticket_number !!}<br />'
                    .'Has been assigned to you by {!! $activity_by !!}<br /><br />'
                    .'Thank You<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}</p>'
        ]);

        Template::create([
            'template_category' => 'assigend-agent-templates',
            'variable' => '0',
            'name' => 'template-ticket-reply-to-assigned-agents-by-client',
            'type' => '10',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                        .'Client has made a new reply on their ticket which is assigned to you.<br /><br />'
                        .'<strong>Ticket ID:</strong>  {!! $ticket_number !!}<br />'
                        .'<strong>Reply Message</strong><br />'
                        .'{!! $message_content !!}<br /><br />'
                        .'Please follow the link below to check and reply on ticket.<br />'
                        .'{!! $ticket_link !!}<br /><br />'
                        .'Thanks<br />'
                        .'{!! $system_from !!}<br />'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'assigend-agent-templates',
            'name' => 'template-response-voilate-escalation-to-assigned-agent',
            'type' => '12',
            'subject' => 'Response Time SLA Violate',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'There has been no response for a ticket assigned to you. The first response was due by {!! $ticket_due_date !!}.<br /><br />'
                    .'Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />'
                    .'Number: {!! $ticket_number !!}<br />'
                    .'Requester: {!! $client_name !!}<br /><br />'
                    .'This is an automatic escalation email from {!! $system_from !!}<br /><br />'
                    .'Respond to ticket here {!! $ticket_link !!}<br /><br />'
                    .'Thank You.<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);
        Template::create([
            'variable' => '1',
            'template_category' => 'assigend-agent-templates',
            'name' => 'template-resolve-voilate-escalation-to-assigned-agent',
            'type' => '13',
            'subject' => 'Resolve Time SLA Violate',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'Ticket which is assigened to you has not been resolved within the SLA time period. The ticket was due by {!! $ticket_due_date !!}.<br /><br />'
                    .'Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />'
                    .'Number: {!! $ticket_number !!}<br />'
                    .'Requester: {!! $client_name !!}<br /><br />'
                    .'This is an automatic escalation email from {!! $system_from !!}<br /><br />'
                    .'Respond to ticket here {!! $ticket_link !!}<br /><br />'
                    .'Thank You.<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'assigend-agent-templates',
            'name' => 'template-internal-change-to-assigned-agent',
            'type' => '16',
            'subject' => '',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                        .'This message is regarding a ticket assigned to you with the ticket ID {!! $ticket_number !!}.<br />'
                        .'{!! $message_content !!}.<br />'
                        .'By {!! $activity_by !!}<br /><br />'
                        .'Thank you<br />'
                        .'Kind regards,<br />'
                        .'{!! $system_from !!}<br />'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'assigend-agent-templates',
            'name' => 'template-response-time-approach-to-assigned-agents',
            'type' => '17',
            'subject' => 'Response Time SLA Approaching',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'There has been no response for a ticket assigned to you. The first response should happen on or before {!! $ticket_due_date !!}.<br /><br />'
                    .'Ticket Details:<br />'
                    .'Subject: {!! $ticket_subject !!}<br />'
                    .'Number: {!! $ticket_number !!}<br />'
                    .'Requester: {!! $client_name !!}<br /><br />'
                    .'This is an automatic escalation email from {!! $system_from !!}<br /><br />'
                    .'Respond to ticket here {!! $ticket_link !!} <br /><br />'
                    .'Thank You.<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'assigend-agent-templates',
            'name' => 'template-resolve-time-approach-to-assigned-agents',
            'type' => '18',
            'subject' => 'Resolution Time SLA Approaching',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'The ticket assigned to you has not yet resolved. The ticket has to be resolved on or before {!! $ticket_due_date !!}.<br /><br />'
                    .'Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />'
                    .'Number: {!! $ticket_number !!}<br />'
                    .'Requester: {!! $client_name !!}<br /><br />'
                    .'This is an automatic escalation email from {!! $system_from !!}<br /><br />'
                    .'Respond to ticket here {!! $ticket_link !!} <br /><br />'
                    .'Thank You.<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);


        Template::create([
            'variable' => '1',
            'template_category' => 'assigend-agent-templates',
            'name' => 'template-ticket-status-update-assign-agent',
            'type' => '21',
            'subject' => '',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
            .'This email is regarding ticket {!! $ticket_number !!} which is assigned to you.<br />'
            .'{!! $message_content !!}<br />'
            .'Thank You.<br/><br />'
            .'Kind Regards,<br />'
            .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '0',
            'template_category' => 'assigend-agent-templates',
            'name' => 'template-ticket-reply-to-assigned-agents-by-agent',
            'type' => '9',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'A reply has been made to a ticket assigned to you with ID: {!! $ticket_number !!} by {!! $activity_by !!} in our helpdesk system.<br />'
                    .'<strong>Reply content</strong><br />'
                    .'{!! $message_content !!}<br />'
                    .'<strong>Ticket link</strong><br />'
                    .'{!! $ticket_link !!}<br />'
                    .'{!! $agent_signature !!}<br />'
                    .'Thanks<br />'
                    .'{!! $system_from !!}'
        ]);


        //Other agent templates

        Template::create([
            'variable' => '0',
            'name' => 'template-new-ticket-creation-notice-agents',
            'type' => '5',
            'template_category' => 'agent-templates',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'New ticket with ID: {!! $ticket_number !!} has been created in our helpdesk.<br /><br />'
                    .'<strong>Client Details</strong><br />'
                    .'<strong>Name:</strong> {!! $client_name !!}<br />'
                    .'<strong>E-mail:</strong> {!! $client_email !!}<br /><br />'
                    .'<strong>Message</strong><br />'
                    .'{!! $message_content !!}<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}</p>'
        ]);


        Template::create([
            'variable' => '0',
            'name' => 'template-ticket-reply-to-agents-by-client',
            'template_category' => 'agent-templates',
            'type' => '10',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                        .'Client has made a new reply on their ticket in our helpdesk system.<br /><br />'
                        .'<strong>Ticket ID:</strong>  {!! $ticket_number !!}<br />'
                        .'<strong>Reply Message</strong><br />'
                        .'{!! $message_content !!}<br /><br />'
                        .'Please follow the link below to check and reply on ticket.<br />'
                        .'{!! $ticket_link !!}<br /><br />'
                        .'Thanks<br />'
                        .'{!! $system_from !!}<br />'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-response-voilate-escalation-to-agent',
            'type' => '12',
            'subject' => 'Response Time SLA Violate',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'There has been no response for a ticket. The first response was due by {!! $ticket_due_date !!}.<br /><br />'
                    .'Ticket Details:<br />'
                    .'Subject: {!! $ticket_subject !!}<br />'
                    .'Number: {!! $ticket_number !!}<br />'
                    .'Requester: {!! $client_name !!}<br /><br />'
                    .'This is an automatic escalation email from {!! $system_from !!}<br /><br />'
                    .'Respond to ticket here {!! $ticket_link !!}<br /><br />'
                    .'Thank You.<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);
        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-resolve-voilate-escalation-to-agent',
            'type' => '13',
            'subject' => 'Resolve Time SLA Violate',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'Ticket has not been resolved within the SLA time period. The ticket was due by {!! $ticket_due_date !!}.<br /><br />'
                    .'Ticket Details:<br />'
                    .'Subject: {!! $ticket_subject !!}<br />'
                    .'Number: {!! $ticket_number !!}<br />'
                    .'Requester: {!! $client_name !!}<br /><br />'
                    .'This is an automatic escalation email from {!! $system_from !!}<br /><br />'
                    .'Respond to ticket here {!! $ticket_link !!}<br /><br />'
                    .'Thank You.<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);
        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-ticket-assigment-notice-to-team',
            'type' => '14',
            'subject' => '',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br /><strong>Ticket No:</strong> {!! $ticket_number !!}<br /><br />'
                    .'Has been assigned to your team <b>{!! $assigned_team_name !!}</b> by {!! $activity_by !!}<br /><br />'
                    .'Follow the link below to check and reply on the ticket.<br />'
                    .'{!! $ticket_link !!}<br /><br />'
                    .'Thank You<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}</p>'
        ]);


        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-internal-change-to-agent',
            'type' => '16',
            'subject' => '',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                        .'This message is regarding a ticket with the ticket ID {!! $ticket_number !!}.<br />'
                        .'{!! $message_content !!}.<br />'
                        .'By {!! $activity_by !!}<br /><br />'
                        .'Thank you<br />'
                        .'Kind regards,<br />'
                        .'{!! $system_from !!}<br />'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-response-time-approach-to-agents',
            'type' => '17',
            'subject' => 'Response Time SLA Approaching',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'There has been no response for a ticket. The first response should happen on or before {!! $ticket_due_date !!}.<br/><br />'
                    .'Ticket Details:<br />'
                    .'Subject: {!! $ticket_subject !!}<br />'
                    .'Number: {!! $ticket_number !!}<br />'
                    .'Requester: {!! $client_name !!}<br /><br />'
                    .'This is an automatic escalation email from {!! $system_from !!}<br /><br />'
                    .'Respond to ticket here {!! $ticket_link !!} <br /><br />'
                    .'Thank You.<br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-resolve-time-approach-to-agents',
            'type' => '18',
            'subject' => 'Resolution Time SLA Approaching',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                .'Ticket has not been resolved within the SLA time period. The ticket has to be resolved on or before {!! $ticket_due_date !!}.<br /><br />'
                .'Ticket Details:<br />'
                .'Subject: {!! $ticket_subject !!}<br />'
                .'Number: {!! $ticket_number !!}<br />'
                .'Requester: {!! $client_name !!}<br /><br />'
                .'This is an automatic escalation email from {!! $system_from !!}<br /><br />'
                .'Respond to ticket here {!! $ticket_link !!} <br /><br />'
                .'Thank You.<br />'
                .'Kind Regards,<br />'
                .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-new-user-entry-notice',
            'type' => '19',
            'subject' => 'New user has created',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br/><br/>'
                        .'A new user has been registered in our helpdesk system.<br/><br/>'
                        .'<strong>User Details</strong><br/>'
                        .'<strong>Name: </strong>{!! $new_user_name !!}<br/>'
                        .'<strong>Email</strong><strong>:</strong> {!! $new_user_email !!}<br/><br/>'
                        .'You can check or update the user\'s complete profile by clicking the link below<br/>'
                        .'{!! $user_profile_link !!}<br/><br/>'
                        .'Thank You.<br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-non-assign-escalation-notice',
            'type' => '20',
            'subject' => 'Non Assign Ticket',
            'set_id' => '1'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'name' => 'template-ticket-status-update-agent',
            'type' => '21',
            'subject' => '',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
            .'This email is regarding ticket {!! $ticket_number !!}.<br />'
            .'{!! $message_content !!}<br />'
            .'Thank You.<br/><br />'
            .'Kind Regards,<br />'
            .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '0',
            'name' => 'template-ticket-assignment-notice-to-agent',
            'type' => '1',
            'template_category' => 'agent-templates',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                        .'<strong>Ticket No:</strong> {!! $ticket_number !!}<br />'
                        .'Has been assigned to {!! $agent_name !!} by {!! $activity_by !!}<br /><br /><br />'
                        .'Thank You<br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '0',
            'template_category' => 'agent-templates',
            'name' => 'template-ticket-reply-to-agents-by-agent',
            'type' => '9',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'An agent has replied to a ticket with ID: {!! $ticket_number !!} in our helpdesk system.<br /><br />'
                    .'<strong>Reply content</strong><br />'
                    .'{!! $message_content !!}<br />'
                    .'<strong>Ticket link</strong><br />'
                    .'{!! $ticket_link !!}<br />'
                    .'{!! $agent_signature !!}<br />'
                    .'Thanks.<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '0',
            'template_category' => 'agent-templates',
            'name' => 'template-ticket-approve',
            'type' => '22',
            'set_id' => '1',
            'message' => 'Hello {!! $agent_name !!},<br /><br />'
                    .'An agent has applied for approval to a ticket with ID: {!! $ticket_number !!} in our helpdesk system.<br /><br />'
                    .'<strong>Ticket link</strong><br />'
                    .'{!! $ticket_link !!}<br /><br />'
                    .'<strong>Approve link</strong><br />'
                    .'{!! $approve_url !!}<br />'
                    .'<strong>Deny link</strong><br />'
                    .'{!! $deny_url !!}<br /><br />'
                    .'Thanks.<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'subject' => 'Helpdesk report available for download',
            'name' => 'template-report-export',
            'type' => '33',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                        .'{!! $report_type !!} is successfully generated and ready for download.<br /><br />'
                        .'<strong>Download link</strong><br />'
                        .'{!! $report_link !!}<br /><br />'
                        .'***Please note this link will be expired in {!! $report_expiry !!}.<br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '0',
            'template_category' => 'client-templates',
            'name' => 'template-invoice-to-clients',
            'type' => '23',
            'set_id' => '1',
            'message' => 'Hello {!! $client_name !!},<br /><br />'
                    .'An invoice has generated for a ticket with ID: {!! $ticket_number !!}.<br /><br />'
                    .'<strong>Ticket link</strong><br />'
                    .'{!! $ticket_link !!}<br /><br />'
                    .'<strong>Total time spend</strong><br />'
                    .'{!! $total_time !!} Hours<br /><br />'
                    .'<strong>Cost</strong><br />'
                    .'{!! $currency !!}{!! $cost !!}<br /><br />'
                    .'<strong>Billing Date</strong><br />'
                    .'{!! $bill_date !!}<br /><br />'
                    .'Thanks.<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'subject' => 'Task Alert',
            'name' => 'task-reminder',
            'type' => '24',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                    .'Your task {!! $task_name !!} is due on {!! $task_end_date !!}. </p>'
                    .''
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'subject' => 'Task created',

            'name' => 'task-created',

            'type' => '25',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                    .'Your task {!! $task_name !!} has been created,  is due on {!! $task_end_date !!}. </p>'
                    .''
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'subject' => 'Task Update',

            'name' => 'task-update',

            'type' => '26',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                    .'Your task {!! $task_name !!} has been updated by {!! $updated_by !!}.</p>'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'subject' => 'Task status update',

            'name' => 'task-status',

            'type' => '27',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                    .'Your task {!! $task_name !!} status has been {!! $status !!}.</p>'
                    .''
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'subject' => 'Task Assigned',

            'name' => 'task-assigned',
            'type' => '28',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                        .'You have been assigned to task {!! $task_name !!} by {!! $created_by !!}'
                        .'  is due on {!! $task_end_date !!}.</p>'

                        .''
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'subject' => 'Task Deleted',
            'name' => 'task-deleted',
            'type' => '29',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                        .'Your task {!! $task_name !!} was deleted or removed by {!! $updated_by !!}.</p>'
                        .''
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'subject' => 'Task Assigned',
            'name' => 'task-assigned-owner',
            'type' => '30',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                        .'Your task {!! $task_name !!} has been assigned to {!! $agent_name !!} and'
                        .'  is due on {!! $task_end_date !!}.</p>'
                        .'<br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'subject' => 'Rating',
            'name' => 'template-rating-feedback',
            'type' => '31',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},</p>'
                        .'<p>We hope you are happy with our support for the <strong>Ticket ID:</strong>&nbsp;{!! $ticket_number !!}</p>'
                        .'<p>Please provide us with your valuable feedback for our service by clicking on a star of your choice.</p>'
                        .'<p>{!! $ratings_icon_with_link !!}</p><br/>'
                        .'Thank You.<br/>'
                        .'Kind Regards,<br/>'
                        .'{!! $system_from !!}</p>'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'subject' => 'Rating Submitted',
            'name' => 'template-rating-confirmation',
            'type' => '32',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                        .'Overall rating has been submitted for a ticket {!! $ticket_link !!}'
                        .'<br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' =>'0',
            'name' => 'template-ticket-creation-acknowledgement-org-mngr-by-client',
            'type' => '4',
            'template_category' => 'org-mngr-templates',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br/><br/>'
                        .'A member of your organization has created a new ticket in our helpdesk system.</p>'
                        .'<p><strong>Ticket ID:</strong>&nbsp; {!! $ticket_number !!}<br/><br/>'
                        .'<strong>Member&#39;s Details</strong><br/>'
                        .'<strong>Name:</strong>&nbsp; {!! $client_name !!}<br/>'
                        .'<strong>E-mail:</strong> {!! $client_email !!}<br/><br/>'
                        .'<strong>Message</strong><br/>'
                        .'{!! $message_content !!}<br/><br/>'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}</p>'
        ]);

        Template::create([
            'variable' =>'0',
            'name' => 'template-ticket-creation-acknowledgement-org-mngr-by-agent',
            'type' => '6',
            'template_category' => 'org-mngr-templates',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br/><br/>'
                        .'Our agent has created a new ticket for a request of your organization&#39;s member.</p>'
                        .'<p><strong>Ticket ID:</strong>&nbsp; {!! $ticket_number !!}<br/><br/>'
                        .'<strong>Member&#39;s Details</strong><br/>'
                        .'<strong>Name:</strong>&nbsp; {!! $client_name !!}<br/>'
                        .'<strong>E-mail:</strong> {!! $client_email !!}<br/><br/>'
                        .'<strong>Message</strong><br/>'
                        .'{!! $message_content !!}<br/><br/>'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}</p>'
        ]);


        Template::create([
            'variable' => '0',
            'template_category' => 'org-mngr-templates',
            'name' => 'template-ticket-reply-to-agents-by-client-to-org-mngr',
            'type' => '9',
            'set_id' => '1',
            'message' => '{!! $message_content !!}<br /><br />'
                        .'<strong>Ticket Details</strong>'
                        .'<strong>Ticket ID:</strong> {!! $ticket_number !!}<br /><br />'
                        .'{!! $agent_signature !!}'
        ]);

        Template::create([
            'variable' => '0',
            'name' => 'template-ticket-reply-to-client-by-agent-to-org-mngr',
            'template_category' => 'org-mngr-templates',
            'type' => '10',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                        .'Your organization member has made a new reply on their ticket in our helpdesk system.<br /><br />'
                        .'<strong>Ticket ID:</strong>  {!! $ticket_number !!}<br />'
                        .'<strong>Reply Message</strong><br />'
                        .'{!! $message_content !!}<br /><br />'
                        .'Please follow the link below to check and reply on ticket.<br />'
                        .'{!! $ticket_link !!}<br /><br />'
                        .'Thanks<br />'
                        .'{!! $system_from !!}<br />'
        ]);

        Template::create([
            'variable' => '0',
            'name' => 'template-ticket-assignment-notice-to-org-mngr',
            'type' => '1',
            'template_category' => 'org-mngr-templates',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                        .'Your organization member\'s ticket with ID: {!! $ticket_number !!} has been assigned to one of our agents. They will contact them soon.<br /><br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}</p>'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'agent-templates',
            'subject' => 'Ticket Approval Link',
            'name' => 'template-ticket-approval',
            'type' => '34',
            'set_id' => '1',
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                        .'Ticket <a href="{!! $ticket_link !!}">{!! $ticket_number !!}</a> has been waiting for your approval.<br /><br />'
                        .'Please click here to review the ticket : {!! $approval_link !!} .<br /><br />'
                        .'Have a nice day.<br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);

        Template::create([
            'variable' => '1',
            'template_category' => 'common-tmeplates',
            'name' => 'template-register-confirmation-with-account-details-and-activation',
            'type' => '35',
            'subject' => 'Registration confirmed, account verifcation required',
            'set_id' => '1',
            'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'This email is confirmation that you are now registered at our helpdesk.<br /><br />'
                    .'<strong>Registered Email:</strong> {!! $new_user_email !!}<br />'
                    .'<strong>Password:</strong> {!! $user_password !!}<br /><br />'
                    .'Please click on the below link to activate your account and Login to the system<br/>'
                    .'{!! $account_activation_link !!}<br/><br/>'
                    .'You can visit the helpdesk to browse articles and contact us at any time: {!! $system_link !!}<br />'
                    .'Thank You.<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}'
        ]);

    }
}
