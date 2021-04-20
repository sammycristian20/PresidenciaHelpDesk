<?php

namespace database\seeds\v_1_9_54;

use App\Model\Common\TemplateShortCode;
use database\seeds\DatabaseSeeder as Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // has to be added by version-wise
        $this->seedTemplateShortCode();
    }

    private function seedTemplateShortCode()
    {     
        TemplateShortCode::updateOrCreate([
                'key_name'             => 'receiver_name'
            ],[
                'shortcode'            => '{!! $receiver_name !!}',           
                'description_lang_key' => 'lang.shortcode_receiver_name_description',
                'key_name'             => 'receiver_name'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'system_link'
            ],[
                'shortcode'            => '{!! $system_link !!}',             
                'description_lang_key' => 'lang.shortcode_system_link_description',
                'key_name'             => 'system_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'system_name'
            ],[
                'shortcode'            => '{!! $system_name !!}',             
                'description_lang_key' => 'lang.shortcode_system_from_description',
                'key_name'             => 'system_name'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'company_name'
            ],[
                'shortcode'            => '{!! $company_name !!}',            
                'description_lang_key' => 'lang.shortcode_company_name_description',
                'key_name'             => 'company_name'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'company_link'
            ],[
                'shortcode'            => '{!! $company_link !!}',            
                'description_lang_key' => 'lang.shortcode_company_link_description',
                'key_name'             => 'company_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'new_user_name'
            ],[
                'shortcode'            => '{!! $new_user_name !!}',           
                'description_lang_key' => 'lang.shortcode_new_user_name_description',
                'key_name'             => 'new_user_name'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'new_user_email'
            ],[
                'shortcode'            => '{!! $new_user_email !!}',          
                'description_lang_key' => 'lang.shortcode_new_user_email_description',
                'key_name'             => 'new_user_email'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'user_password'
            ],[
                'shortcode'            => '{!! $user_password !!}',           
                'description_lang_key' => 'lang.shortcode_user_password_description',
                'key_name'             => 'user_password'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'password_reset_link'
            ],[
                'shortcode'            => '{!! $password_reset_link !!}',     
                'description_lang_key' => 'lang.shortcode_password_reset_link_description',
                'key_name'             => 'password_reset_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'account_activation_link'
            ],[
                'shortcode'            => '{!! $account_activation_link !!}', 
                'description_lang_key' => 'lang.shortcode_account_activation_link_description',
                'key_name'             => 'account_activation_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ticket_link'
            ],[
                'shortcode'            => '{!! $ticket_link !!}',             
                'description_lang_key' => 'lang.shortcode_ticket_link_description',
                'key_name'             => 'ticket_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ticket_number'
            ],[
                'shortcode'            => '{!! $ticket_number !!}',           
                'description_lang_key' => 'lang.shortcode_ticket_number_description',
                'key_name'             => 'ticket_number'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ticket_subject'
            ],[
                'shortcode'            => '{!! $ticket_subject !!}',          
                'description_lang_key' => 'lang.shortcode_ticket_subject_description',
                'key_name'             => 'ticket_subject'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ticket_due_date'
            ],[
                'shortcode'            => '{!! $ticket_due_date !!}',         
                'description_lang_key' => 'lang.shortcode_ticket_due_date_description',
                'key_name'             => 'ticket_due_date'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ticket_created_at'
            ],[
                'shortcode'            => '{!! $ticket_created_at !!}',       
                'description_lang_key' => 'lang.shortcode_ticket_created_at_description',
                'key_name'             => 'ticket_created_at'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'department_signature'
            ],[
                'shortcode'            => '{!! $department_signature !!}',    
                'description_lang_key' => 'lang.shortcode_department_signature_description',
                'key_name'             => 'department_signature'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'agent_name'
            ],[
                'shortcode'            => '{!! $agent_name !!}',              
                'description_lang_key' => 'lang.shortcode_agent_name_description',
                'key_name'             => 'agent_name'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'agent_email'
            ],[
                'shortcode'            => '{!! $agent_email !!}',             
                'description_lang_key' => 'lang.shortcode_agent_email_description',
                'key_name'             => 'agent_email'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'agent_contact'
            ],[
                'shortcode'            => '{!! $agent_contact !!}',           
                'description_lang_key' => 'lang.shortcode_agent_contact_description',
                'key_name'             => 'agent_contact'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'agent_sign'
            ],[
                'shortcode'            => '{!! $agent_signature !!}',         
                'description_lang_key' => 'lang.shortcode_agent_signature_description',
                'key_name'             => 'agent_sign'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'client_name'
            ],[
                'shortcode'            => '{!! $client_name !!}',             
                'description_lang_key' => 'lang.shortcode_client_name_description',
                'key_name'             => 'client_name'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'client_email'
            ],[
                'shortcode'            => '{!! $client_email !!}',            
                'description_lang_key' => 'lang.shortcode_client_email_description',
                'key_name'             => 'client_email'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'client_contact'
            ],[
                'shortcode'            => '{!! $client_contact !!}',          
                'description_lang_key' => 'lang.shortcode_client_contact_description',
                'key_name'             => 'client_contact'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'user_profile_link'
            ],[
                'shortcode'            => '{!! $user_profile_link !!}',       
                'description_lang_key' => 'lang.shortcode_user_profile_link_description',
                'key_name'             => 'user_profile_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'message_content'
            ],[
                'shortcode'            => '{!! $message_content !!}',         
                'description_lang_key' => 'lang.shortcode_message_content_description',
                'key_name'             => 'message_content'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'activity_by'
            ],[
                'shortcode'            => '{!! $activity_by !!}',             
                'description_lang_key' => 'lang.shortcode_activity_by_description',
                'key_name'             => 'activity_by'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'assigned_team_name'
            ],[
                'shortcode'            => '{!! $assigned_team_name !!}',      
                'description_lang_key' => 'lang.shortcode_assigned_team_name_description',
                'key_name'             => 'assigned_team_name'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'otp_code'
            ],[
                'shortcode'            => '{!! $otp_code !!}',                
                'description_lang_key' => 'lang.shortcode_otp_code_description',
                'key_name'             => 'otp_code'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ticket_client_edit_link'
            ],[
                'shortcode'            => '{!! $ticket_edit_link !!}',        
                'description_lang_key' => 'lang.shortcode_to_send_ticket_edit_link',
                'key_name'             => 'ticket_client_edit_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'approve_url'
            ],[
                'shortcode'            => '{!! $approve_url !!}',             
                'description_lang_key' => 'lang.shortcode_approve_url_description',
                'key_name'             => 'approve_url'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'deny_url'
            ],[
                'shortcode'            => '{!! $deny_url !!}',                
                'description_lang_key' => 'lang.shortcode_deny_url_description',
                'key_name'             => 'deny_url'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'total_time'
            ],[
                'shortcode'            => '{!! $total_time !!}',              
                'description_lang_key' => 'lang.shortcode_total_time_description',
                'key_name'             => 'total_time'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'cost'
            ],[
                'shortcode'            => '{!! $cost !!}',                    
                'description_lang_key' => 'lang.shortcode_cost_description',
                'key_name'             => 'cost'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'bill_date'
            ],[
                'shortcode'            => '{!! $bill_date !!}',               
                'description_lang_key' => 'lang.shortcode_bill_date_description',
                'key_name'             => 'bill_date'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'currency'
            ],[
                'shortcode'            => '{!! $currency !!}',                
                'description_lang_key' => 'lang.shortcode_currency_description',
                'key_name'             => 'currency'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'task_name'
            ],[
                'shortcode'            => '{!! $task_name !!}',               
                'description_lang_key' => 'lang.shortcode_task_name_description',
                'key_name'             => 'task_name'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'task_end_date'
            ],[
                'shortcode'            => '{!! $task_end_date !!}',           
                'description_lang_key' => 'lang.shortcode_task_end_date_description',
                'key_name'             => 'task_end_date'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'status'
            ],[
                'shortcode'            => '{!! $status !!}',                  
                'description_lang_key' => 'lang.shortcode_status_description',
                'key_name'             => 'status'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'updated_by'
            ],[
                'shortcode'            => '{!! $updated_by !!}',              
                'description_lang_key' => 'lang.shortcode_updated_by_description',
                'key_name'             => 'updated_by'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'created_by'
            ],[
                'shortcode'            => '{!! $created_by !!}',              
                'description_lang_key' => 'lang.shortcode_created_by_description',
                'key_name'             => 'created_by'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'report_type'
            ],[
                'shortcode'            => '{!! $report_type !!}',             
                'description_lang_key' => 'lang.shortcode_report_type_description',
                'key_name'             => 'report_type'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'report_link'
            ],[
                'shortcode'            => '{!! $report_link !!}',             
                'description_lang_key' => 'lang.shortcode_report_link_description',
                'key_name'             => 'report_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'report_expiry'
            ],[
                'shortcode'            => '{!! $report_expiry !!}',           
                'description_lang_key' => 'lang.shortcode_report_expiry_description',
                'key_name'             => 'report_expiry'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'approval_link'
            ],[
                'shortcode'            => '{!! $approval_link !!}',           
                'description_lang_key' => 'lang.shortcode_approval_link_description',
                'key_name'             => 'approval_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ratings_icon_with_link'
            ],[
                'shortcode'            => '{!! $ratings_icon_with_link !!}',  
                'description_lang_key' => 'lang.shortcode_ratings_icon_with_link_description',
                'key_name'             => 'ratings_icon_with_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'error_message'
            ],[
                'shortcode'            => '{!! $error_message !!}',           
                'description_lang_key' => 'lang.shortcode_error_message_description',
                'key_name'             => 'error_message'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ex_message'
            ],[
                'shortcode'            => '{!! $ex_message !!}',              
                'description_lang_key' => 'lang.shortcode_ex_message_description',
                'key_name'             => 'ex_message'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ex_file'
            ],[
                'shortcode'            => '{!! $ex_file !!}',                 
                'description_lang_key' => 'lang.shortcode_ex_file_description',
                'key_name'             => 'ex_file'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ex_line'
            ],[
                'shortcode'            => '{!! $ex_line !!}',                 
                'description_lang_key' => 'lang.shortcode_ex_line_description',
                'key_name'             => 'ex_line'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ex_trace'
            ],[
                'shortcode'            => '{!! $ex_trace !!}',                
                'description_lang_key' => 'lang.shortcode_ex_trace_description',
                'key_name'             => 'ex_trace'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'rating_value'
            ],[
                'shortcode'            => '{!! $rating_value !!}',            
                'description_lang_key' => 'lang.shortcode_rating_value_description',
                'key_name'             => 'rating_value'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'session_id'
            ],[
                'shortcode'            => '{!! $session_id !!}',            
                'description_lang_key' => 'lang.shortcode_session_id_description',
                'key_name'             => 'session_id'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'survey_link'
            ],[
                'shortcode'            => '{!! $survey_link !!}',            
                'description_lang_key' => 'lang.shortcode_survey_link_description',
                'key_name'             => 'survey_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'reference_link'
            ],[
                'shortcode'            => '{!! $reference_link !!}',            
                'description_lang_key' => 'lang.shortcode_reference_link_description',
                'key_name'             => 'reference_link'
        ]);

        TemplateShortCode::updateOrCreate([
                'key_name'             => 'ticket_conversation'
            ],[
                'shortcode'            => '{!! $ticket_conversation !!}',            
                'description_lang_key' => 'lang.shortcode_ticket_conversation_description',
                'key_name'             => 'ticket_conversation'
        ]);
    }
}
