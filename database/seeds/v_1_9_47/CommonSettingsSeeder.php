<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;

use App\Model\helpdesk\Settings\CommonSettings;

class CommonSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommonSettings::updateOrCreate(['option_name' => 'user_registration'],['option_name' => 'user_registration', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'user_show_org_ticket'],['option_name' => 'user_show_org_ticket', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'user_reply_org_ticket'],['option_name' => 'user_reply_org_ticket', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'allow_users_to_create_ticket'],['option_name' => 'allow_users_to_create_ticket', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'login_restrictions'],['option_name' => 'login_restrictions', 'option_value' => 'email']);
        CommonSettings::updateOrCreate(['option_name' => 'micro_organization_status'],['option_name' => 'micro_organization_status', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'dashboard-statistics'],['option_name' => 'dashboard-statistics', 'option_value' => 'departments,agents,teams']);
        CommonSettings::updateOrCreate(['option_name' => 'helptopic_link_with_type'],['option_name' => 'helptopic_link_with_type', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'batch_tickets'],['option_name' => 'batch_tickets', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'redirect_to_timeline'],['option_name' => 'redirect_to_timeline', 'status' => 0]);

        CommonSettings::updateOrCreate(['option_name' => 'allow_external_login'],['option_name' => 'allow_external_login', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'allow_users_to_access_system_url'],['option_name' => 'allow_users_to_access_system_url', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'redirect_unauthenticated_users_to'],['option_name' => 'redirect_unauthenticated_users_to']);
        CommonSettings::updateOrCreate(['option_name' => 'validate_token_api'],['option_name' => 'validate_token_api']);
        CommonSettings::updateOrCreate(['option_name' => 'validate_api_parameter'],['option_name' => 'validate_api_parameter', 'option_value' => 'token']);
        CommonSettings::updateOrCreate(['option_name' => 'allow_organization_mngr_approve_tickets'],['option_name' => 'allow_organization_mngr_approve_tickets', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'allow_organization_dept_mngr_approve_tickets'],['option_name' => 'allow_organization_dept_mngr_approve_tickets', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'show_gravatar_image'],['option_name' => 'show_gravatar_image', 'status' => 1]);
        CommonSettings::updateOrCreate(['option_name' => 'time_track'],['option_name' => 'time_track', 'status' => 0]);
        CommonSettings::updateOrCreate(['option_name' => 'registration_mail_templates'],['option_value' => 'multiple']);
    }
}
