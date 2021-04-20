<?php

namespace database\seeds\v_4_0_0;

use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Settings\Alert;
use App\Model\Common\TemplateType;
use App\User;
use Schema;
use DB;
use File;
use database\seeds\DatabaseSeeder as Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->updateFormAPIField();
        $this->updateRecaptchaSettings();
        $this->ticketNumberSeeder();
        $this->correctLoginRestrictionOptions();
        $this->addEmailVerifyAlert();
        $this->changeTemplateTypeForRegistrationToEmailVerification();
        $this->handleDeletedAccount();
        $this->handleBanAccountAndDeleteBanColumn();
        $this->addBroadcasterConfigInEnv();
        $this->convertDeletedAccountsToDeactivated();
    }

    /**
     * Updates API fields in the forms
     */
    private function updateFormAPIField()
    {
        $apiFields = FormField::where('api_info', '!=', '')->where('api_info', '!=', null)->get();

        foreach ($apiFields as $apiField) {
            if (strpos($apiField->api_info, 'url:=') === false) {
                $apiField->api_info = "url:=$apiField->api_info;;key:=id;;";
                $apiField->save();
            }
        }
    }

    /**
     * method to update recaptcha settings
     * @return void
     */ 
    private function updateRecaptchaSettings()
    {

        // $googleSecretKey = commonSettings('google secret key', 'secret_key');
        $wasRecaptchaActivated = CommonSettings::where('option_name', 'google secret key')
            ->where('optional_field', 'secret_key')
            ->count();

        if($wasRecaptchaActivated){

            CommonSettings::updateOrCreate(['option_name' => 'google secret key', 'optional_field' => 'secret_key'], ['option_name' => 'google']);

            CommonSettings::updateOrCreate(['option_name' => 'google', 'optional_field' => 'recaptcha_status'], ['option_value' => '1']);

           CommonSettings::updateOrCreate(['option_name' => 'google', 'optional_field' => 'recaptcha_type'], ['option_value' => 'v2']);

           CommonSettings::updateOrCreate(['option_name' => 'google', 'optional_field' => 'recaptcha_apply_for'], ['option_value' => '']);

        }
    }

    /**
     * Extracts ticket number prefix (first 4 characters) from last ticket and store in ticket settings
     */
    public function ticketNumberSeeder()
    {
        $oldTicketNumber = Tickets::orderBy('id', 'desc')->value('ticket_number');

        if ($oldTicketNumber) {
            $oldTicketNumberArray = explode('-', $oldTicketNumber);

            if (isset($oldTicketNumberArray[0])) {
                $oldTicketNumberArray[0];
                $oldTicketNumberPrefix = substr($oldTicketNumberArray[0], 0, 4);
                $ticketSettings = Ticket::first();
                $ticketSettings->ticket_number_prefix = $oldTicketNumberPrefix;
                $ticketSettings->save();
            }
        }
    }

    /**
     * Method changes key for account activation in common_settings table to
     * from account_actvation_option to login_restrictions.
     * As there will be no option to activate account by verifying email or mobile
     * Here onwards only login restriction will be set for unverified email or mobile.
     * Means user account can still be active if email or mobile are not verified. But
     * admin can decide should such users be able to login or not.
     */
    private function correctLoginRestrictionOptions()
    {
        CommonSettings::where('option_name', 'account_actvation_option')
        ->update(['option_name' => 'login_restrictions']);
    }

    private function addEmailVerifyAlert()
    {
        foreach ($this->getEmailAlertDataArray() as $data) {
            Alert::updateOrCreate(['key'=>$data['key']],$data);
        }
    }

    private function getEmailAlertDataArray()
    {
        return [
            [
                'key'   => 'email_verify_alert',
                'value' => '1',
            ],

            [
                'key'   => 'email_verify_alert_mode',
                'value' => 'email',
            ],
            [
                'key'   => 'email_verify_alert_persons',
                'value' => 'new_user',
            ],
        ];
    }

    private function changeTemplateTypeForRegistrationToEmailVerification()
    {
        TemplateType::where('name', 'registration')->update([
            'name' => 'email_verify'
        ]);
    }

    private function handleDeletedAccount()
    {
        User::where('is_delete', 1)->update([
            'active' => 0,
            'is_delete' =>0,
        ]);
    }

    /**
     * Note:this makes system backward incompatible so decide correct version
     * tag before release
     */
    private function handleBanAccountAndDeleteBanColumn()
    {
        if(Schema::hasColumn('users','ban')) {
            DB::table('users')->where('ban',1)->update([
               'active' => 0 
            ]);
            Schema::table('users', function($table) {
                $table->dropColumn('ban');
            });
        }
    }

    /**
     * Method updates .env file to accomodate confiugration variables
     * required for websocket support
     *
     * @return void
     */
    private function addBroadcasterConfigInEnv():void
    {
        $file = base_path().DIRECTORY_SEPARATOR.'.env';
        if(file_exists($file)) {
            $datacontent = File::get($file);
            $dataToUpdate = '';
            $newEnvVariables = $this->getNewEnvVariables();
            foreach ($newEnvVariables as $key => $value) { 
                if($this->doesEnvVaribaleExist($datacontent, $key)) continue;
                $dataToUpdate .= "{$key}={$value}\n";
            }
            if($dataToUpdate) {
                $fp = fopen($file, 'a');
                fwrite($fp, $dataToUpdate);
                fclose($fp);
            }
        }
    }

    /**
     * Function returns an array containing socket's environment variables
     * and their values as key=>value
     *
     * @return array
     */
    private function getNewEnvVariables():array
    {
        return [
            'APP_NAME' => 'Faveo:'.md5(uniqid()),
            'BROADCAST_DRIVER' => 'pusher',
            'LARAVEL_WEBSOCKETS_ENABLED' => 'false',
            'LARAVEL_WEBSOCKETS_PORT' => 6001,
            'LARAVEL_WEBSOCKETS_HOST' => "127.0.0.1",
            'LARAVEL_WEBSOCKETS_SCHEME' => 'http',
            'PUSHER_APP_ID' => str_random(16),
            'PUSHER_APP_KEY' => md5(uniqid()),
            'PUSHER_APP_SECRET' => md5(uniqid()),
            'PUSHER_APP_CLUSTER' => 'mt1',
            'MIX_PUSHER_APP_KEY' => '"${PUSHER_APP_KEY}"',
            'MIX_PUSHER_APP_CLUSTER' => '"${PUSHER_APP_CLUSTER}"',
            'LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT'=> 'null',
            'LARAVEL_WEBSOCKETS_SSL_LOCAL_PK' => 'null',
            'LARAVEL_WEBSOCKETS_SSL_PASSPHRASE' => 'null',
            'SOCKET_CLIENT_SSL_ENFORCEMENT' => 'false'
        ];
    }

    /**
     * Function compares if a given sub string is present in the string
     * or not and returns boolean value
     *
     * @param   string   $haystack  main string to check
     * @param   string   $needle    substring to find
     * @return  boolean             return true if the substring exists in
     *                              the string else false
     */
    private function doesEnvVaribaleExist($haystack, $needle)
    {
        return !(strpos($haystack, $needle) === false);
    }

    /**
     * Previously we had ambiguity for banning, deleting, deactivating and verifying 
     * the user accounts. Due to which while deactivating the user we were setting is_delete
     * column as 1 to denote that the user account is deactivated. Since this messes with the
     * human brains we are simplifying the process of activation/deactivation and verifying the
     * account to make it simple and swift.
     * Now for checking account is deactivated or active we will use "active" column instead of
     * "is_delete" column which now will be used to denote user account has been deleted completely.
     * Since older version may contain "is_delete" as 1 for deactivated users we will simply change
     * its value and use "active" to store deactivated status.
     *
     * @since v4.0.0
     * @return void
     */
    private function convertDeletedAccountsToDeactivated():void
    {
        User::where('is_delete', 1)->update([
            'active' => 0, //account is deactivated 
            'is_delete' => 0 //account is not yet deleted
        ]);
    }
}
