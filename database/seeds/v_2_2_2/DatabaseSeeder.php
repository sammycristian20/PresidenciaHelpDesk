<?php

namespace database\seeds\v_2_2_2;

use database\seeds\DatabaseSeeder as Seeder;

use App\User;
use App\Model\helpdesk\Manage\Sla\BusinessHoursSchedules;
use App\Model\Common\UserNotificationToken;

use File;


class DatabaseSeeder extends Seeder
{

    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->updateFCMKeys();
        $this->seedExistingTokens();
        $this->businessHourSpellingSeeder();
        $this->userSeeder();
    }

    /**
     * Function updates the FCM configuration data in .env file to make
     * mobile push notifications working in system which are being updated.
     *
     * NOTE: This method has been added in the seeder to ensure FCM keys are
     * updated while updating system from old to new.
     */
    private function updateFCMKeys()
    {
        $file = base_path().DIRECTORY_SEPARATOR.'.env';

        if(file_exists($file)){
            $datacontent = File::get($file);
            $datacontent = str_replace(
                'FCM_SERVER_KEY=AIzaSyCyx5OFnsRFUmDLTMbPV50ZMDUGSG-bLw4', //old key
                'FCM_SERVER_KEY=AIzaSyBJNRvyub-_-DnOAiIJfuNOYMnffO2sfw4', //new key
                $datacontent);
            $datacontent = str_replace(
                'FCM_SENDER_ID=661051343223', //old id
                'FCM_SENDER_ID=505298756081', //new id
                $datacontent);
            File::put($file, $datacontent);
        }
    }

    private function seedExistingTokens()
    {
        if(!\Schema::hasColumn('i_token','users') || !\Schema::hasColumn('fcm_token','users')) return;
        /**
         * Removing i_token and fcm_token column from user tables will create an issue as
         * we won't be able to retain the data which is why we are seeding existing tokens
         * in this seeder so in next release we can write migration to remove columns.
         * Still need to implement migration version wise.
         */
        $users = User::whereNotNull('i_token')->orWhereNotNull('fcm_token')->get(['id', 'fcm_token', 'i_token']);
        foreach ($users as $user) {
            if((bool)$user->i_token) {
                $user->notificationTokens()->updateOrCreate(['token' => $user->i_token], [
                    'token' => $user->i_token,
                    'type'  => 'fcm_token',  
                ]);
            }
            if((bool)$user->fcm_token) {
                $user->notificationTokens()->updateOrCreate(['token' => $user->fcm_token],[
                    'token' => $user->fcm_token,
                    'type'  => 'fcm_token',
                ]);
            }
        }
    }

    private function businessHourSpellingSeeder()
    {
        BusinessHoursSchedules::where('days', 'Wednusday')->update(['days'=>'Wednesday']);
    }

    private function userSeeder()
    {
        if(\Schema::hasColumn('ban','users')) {
            \DB::table('users')->where('ban',1)->update(['is_delete'=>1]);            
        }
    }
}
