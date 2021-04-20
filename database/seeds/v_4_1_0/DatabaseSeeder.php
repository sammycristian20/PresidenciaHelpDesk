<?php

namespace database\seeds\v_4_1_0;

use App\Model\helpdesk\Ticket\Ticket_source;
use App\Model\helpdesk\Form\FormField;
use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\MailJob\Condition;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->changeSourceIconClassSeeder();
        $this->updateFormMetaKeys();
        $this->updateFormUniqueKeysForRequester();
        $this->updateCronJobIcons();
    }

    /**
     * Seeder to update source icon class 
     */
    private function changeSourceIconClassSeeder()
    {   
        $sourceIconClasses = [
            'fa fa-globe' => 'fas fa-globe',
            'fa fa-whatsapp' => 'fab fa-whatsapp',
            'fa fa-twitter' => 'fab fa-twitter',
            'fa fa-facebook' => 'fab fa-facebook',
            'fa fa-envelope' => 'fas fa-envelope',
            'fa fa-phone' => 'fas fa-phone',
            'fa fa-user' => 'fas fa-user',
            'fa fa-comment' => 'fas fa-comment-dots',
        ];

        foreach ($sourceIconClasses as $key => $value) {
            Ticket_source::where('css_class', $key)->update(['css_class' => $value]);
        }
    }

    /**
     * Updates `is_observable` and  `is_edit_visible` ke
     */
    private function updateFormMetaKeys()
    {
        FormField::whereIn('title', ['CC', 'Requester','Captcha'])->update(['is_observable'=> 0]);

        FormField::whereIn('title', ['Description', 'Status','Assigned', 'CC'])->update(['is_edit_visible'=> 0]);
    }

    /**
     * Corrects values of unique for mobile and phone fields as per columns in database to
     * avoid confusions
     */
    private function updateFormUniqueKeysForRequester()
    {
        $formCategory = FormCategory::where('category','user')->first();
        $formCategory->formFields()->where('unique', 'mobile_phone')->update(['unique' => 'mobile']);
        $formCategory->formFields()->where('unique', 'work_phone')->update(['unique' => 'phone_number']);
    }

    private function updateCronJobIcons()
    {
        $sourceIconClasses = [
            'fa fa-globe' => 'fas fa-globe',
            'fa fa-whatsapp' => 'fab fa-whatsapp',
            'fa fa-twitter' => 'fab fa-twitter',
            'fa fa-facebook' => 'fab fa-facebook',
            'fa fa-envelope' => 'fas fa-envelope',
            'fa fa-phone' => 'fas fa-phone',
            'fa fa-user' => 'fas fa-user',
            'fa fa-comment' => 'fas fa-comment-dots',
            'fa fa-arrow-circle-o-down' => 'fas fa-mail-bulk',
            'fa fa-line-chart' => 'fas fa-chart-line',
            'fa  fa-repeat' => 'fas fa-redo',
            'fa fa-refresh' => 'fas fa-sync-alt',
            'fa fa-cloud-download' => 'fa fa-cloud-download-alt',
        ];

        foreach ($sourceIconClasses as $key => $value) {
            Condition::where('icon', $key)->update(['icon' => $value]);
        }
    }
}
