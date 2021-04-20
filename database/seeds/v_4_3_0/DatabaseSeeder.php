<?php

namespace database\seeds\v_4_3_0;

use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Settings\CommonSettings;
use App\FileManager\Helpers\StoreFileMetadataHelper;
use App\FileManager\Models\FileManagerAclRule;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Settings\System;
use App\User;
use Storage;
use database\seeds\DatabaseSeeder as Seeder;

class DatabaseSeeder extends Seeder
{


    public function run()
    {
        $this->formTypeSeeder();
        $this->recaptchaMigration();
        $this->addToolTipToOrganisationDomainName();
        $this->fileSystemSeeder();
        $this->createPrivateFolderIfNotExisting();
        $this->createEntriesForPrivateFiles();
        $this->createEntriesForPublicFiles();
        $this->storeUpdateTimeToCommonSettings();

    }


    private function formTypeSeeder()
    {
        $ticketCategory = FormCategory::where('category', 'ticket')->first();

        FormField::whereIn('title', ['Requester', 'Organisation Department'])->update(['type'=>'api']);

        FormField::where('title', 'Description')->update(['type'=>'htmltextarea']);

        FormField::whereIn('title', ['Organisation', 'CC'])->update(['type'=>'multiselect']);
        FormField::whereIn('title', ['Requester', 'CC', 'Captcha'])->update(['is_observable'=> 0]);

        // hiding description only for ticket
        $ticketCategory->formFields()->whereIn('title', ['CC', 'Description'])->update(['is_edit_visible'=> 0]);

        FormField::where('title', 'Organisation Domain Name')->update(['type'=> 'taggable']);
        FormField::where('title', 'Organisation Department')->update(['type'=> 'api', 'api_info'=>'url:=/api/dependency/organisation-departments;;']);
        FormField::where('title', 'Organisation')->update(['api_info'=>'url:=/api/dependency/organizations;;']);

        $organisationCategory = FormCategory::where('category', 'organisation')->first();
        $organisationCategory->formFields()->where('title', 'Description')->update(['type'=> 'textarea', 'is_edit_visible'=> 1]);
        $organisationCategory->formFields()->where('title', 'Organisation Department')->update(['type'=> 'taggable']);
    }

    private function addToolTipToOrganisationDomainName()
    {
        FormField::where('unique', 'organisation_domain_name')->first()->labels()->get()->map(function ($label) {
            $label->update(['description'=> 'Put domain name without "http://" or "https://. Example example.com"']);
        });
    }

    /**
     * Migrates recaptcha keys
     */
    private function recaptchaMigration()
    {
        $recaptchaKey = (string)CommonSettings::where('optional_field', 'recaptcha_apply_for')->value('option_value');

        $this->appendTicketRecaptchaKeys($recaptchaKey);

        $this->appendUserRecaptchaKeys($recaptchaKey);

        $this->appendOrgRecaptchaKeys($recaptchaKey);

        CommonSettings::where('optional_field', 'recaptcha_apply_for')->update(['option_value'=> $recaptchaKey]);

        FormField::where('title', 'Captcha')->delete();
    }

    /**
     * Appends ticket recaptcha keys if its enabled in old forms
     * @param $recaptchaKey
     */
    private function appendTicketRecaptchaKeys(&$recaptchaKey)
    {
        if ($ticketCaptchaField = FormCategory::where('category', 'ticket')->first()->formFields()->where('title', 'Captcha')->first()) {
            if ($ticketCaptchaField->display_for_agent) {
                $recaptchaKey .= ',ticket_create_agent,ticket_edit_agent,ticket_fork_agent,ticket_fork_agent,ticket_recur_admin,ticket_recur_agent';
            }
            if ($ticketCaptchaField->display_for_user) {
                $recaptchaKey .= ',ticket_create_client';
            }
        }
    }

    /**
     * Appends user recaptcha keys if its enabled in old forms
     * @param $recaptchaKey
     */
    private function appendUserRecaptchaKeys(&$recaptchaKey)
    {
        if ($userCaptchaField = FormCategory::where('category', 'user')->first()
            ->formFields()->where('title', 'Captcha')->first()) {
            if ($userCaptchaField->display_for_agent) {
                $recaptchaKey .= ',user_edit_agent,user_create_agent';
            }
            if ($userCaptchaField->display_for_user) {
                $recaptchaKey .= ',user_create_client';
            }
        }
    }

    /**
     * Appends organization recaptcha keys if its enabled in old forms
     * @param $recaptchaKey
     */
    private function appendOrgRecaptchaKeys(&$recaptchaKey)
    {
        if ($orgCaptchaField = FormCategory::where('category', 'organisation')->first()
                ->formFields()->where('title', 'Captcha')->first()) {
            if ($orgCaptchaField->display_for_agent) {
                $recaptchaKey .= ',organisation_edit_agent,organisation_create_agent';
            }
        }
    }

    private function fileSystemSeeder()
    {
        if (! FileSystemSettings::count()) {
            FileSystemSettings::create([
                'disk' => 'private',
                'allowed_files' => implode(',', ['png', 'gif', 'jpg', 'jpeg', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'csv','txt'])
            ]);
        }
    }

    private function createPrivateFolderIfNotExisting()
    {
        if (! is_dir(storage_path() . '/app/private')) {
            if (is_writable(storage_path() . '/app')) {
                mkdir(storage_path() . '/app/private', 0755);
            }
        }
    }

    private function createEntriesForPrivateFiles()
    {
        (new StoreFileMetadataHelper('private'))->scanForFilesInDirectory();
    }

    private function createEntriesForPublicFiles()
    {
        (new StoreFileMetadataHelper('public'))->scanForFilesInDirectory();
    }

    /**
     * Saving UTC timestamp of system update to v4.3.0 which is being used in
     * makeshifter to show all activity records of the old tickets of already
     * installed systems.
     * This timestamp can also be used to clean the ticket_threads table once
     * notifications are corrected to remove all activity logs stored in the 
     * table after updating to v4.3.0
     */
    private function storeUpdateTimeToCommonSettings()
    {
        CommonSettings::updateOrCreate(['option_name' => 'v_4_3_0_update'], ['option_name' => 'v_4_3_0_update', 'option_value' => now()->format('Y-m-d H:i:s')
        ]);
    }

}
