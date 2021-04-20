<?php

namespace database\seeds\v_2_1_4;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\Common\Template;
use App\Model\helpdesk\Settings\Alert;

class DatabaseSeeder extends Seeder
{

    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->updateTemplateSeeder();
        $this->updateTicketApprovalTemplateSeeder();
        $this->updateRatingFeedbackAlert();
        $this->updateRatingConfirmationAlert();
        $this->updateLimeSurveyAlert();
    }

    /**
     * method to update template category from "common-tmeplates" to "common-templates"
     * @return void
     */ 
    private function updateTemplateSeeder()
    {
        Template::where('template_category', 'common-tmeplates')->update(['template_category' => 'common-templates']);
    }

    /**
     * method to update ticket approval template category from "agent-templates" to "common-templates"
     * @return void
     */
    private function updateTicketApprovalTemplateSeeder()
    {
        Template::where('name', 'template-ticket-approval')->update(['template_category' => 'common-templates']);
    }

    private function updateRatingFeedbackAlert()
    {
        Alert::where('key', 'rating')->update(['key' => 'rating_feedback_alert']);
        Alert::updateOrCreate(
            ['key' => 'rating_feedback_alert_mode'],
            ['key' => 'rating_feedback_alert_mode', 'value' => 'email']);
        Alert::updateOrCreate(
            ['key' => 'rating_feedback_alert_persons'],
            ['key' => 'rating_feedback_alert_persons', 'value' => 'client']);
    }

    private function updateRatingConfirmationAlert()
    {
        $mode = Alert::where('key', 'rating_confirmation_mode')->first();
        if($mode) {
            $mode = $mode->value;
            Alert::updateOrCreate(['key' => 'rating_confirmation_mode'], ['value' => str_replace('in-app-notify', 'system', $mode)]);
        }
    }

    private function updateLimeSurveyAlert()
    {
        $isSetup = Alert::where('key', 'lime_survey_mail_statuses')->first();
        if($isSetup) {
            Alert::updateOrCreate(
                ['key' => 'lime_survey_alert'],
                ['key' => 'lime_survey_alert', 'value' => '1']
            );
            Alert::updateOrCreate(
                ['key' => 'lime_survey_alert_persons'],
                ['key' => 'lime_survey_alert_persons', 'value' => 'client']
            );
            Alert::updateOrCreate(
                ['key' => 'lime_survey_alert_mode'],
                ['key' => 'lime_survey_alert_mode', 'value' => 'email']
            );
        }
    }
}
