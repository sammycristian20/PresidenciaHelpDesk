<?php

namespace App\Plugins\Twitter\Controllers\Core;

use App\Http\Controllers\Controller;
use Exception;
use Artisan;
use Schema;

class SettingsController extends Controller {


    /**
     * Migrates the Database When Twitter Plugin is activated.
     * @param void
     * @return void
     */
    public function migrate() {
        if (env('DB_INSTALL') == 1 && !Schema::hasTable('twitter_app')) {
            $path = "app" . DIRECTORY_SEPARATOR . "Plugins" . DIRECTORY_SEPARATOR . "Twitter" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
        }
    }

     /**
     * sorts the array
     * @param array $arr
     * @return array $array
     */
    public function arraySort($arr) {
        $array = array_values(array_sort($arr, function ($value) {
                    return $value['posted_at'];
                }));
        return $array;
    }

    /**
     * Extracts the User Information from Twitter related array
     * @param array $social_fields
     * @return array $user
     */
    public function getUser($social_fields) {
        $user = [];
        $channel = $this->checkArray('channel', $social_fields);
        $name = $this->checkArray('username', $social_fields);
        $user_id = $this->checkArray('user_id', $social_fields);
        $avatar = $this->checkArray('avatar', $social_fields);
        $email = $this->checkArray('email', $social_fields);
        if ($email == "") {
            $email = null;
        }
        $user['username'] = $name;
        $user['user_id'] = $user_id;
        $user['avatar'] = $avatar;
        $user['email'] = $email;
        return $user;
    }

     /**
     * Check if key => value pair exists in array and returns the value
     * @param array $array
     * @param string $key
     * @param mixed $value
     */
    public function checkArray($key, $array) {
        $value = "";
        if (is_array($array)) {
            if (key_exists($key, $array)) {
                $value = $array[$key];
            }
        }
        return $value;
    }

    /**
     * Gets the default system default help topic
     * @param void
     * @return mixed $help_topicid
     */
    public function getSystemDefaultHelpTopic() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->find(1);
        $help_topicid = $ticket_setting->help_topic;
        return $help_topicid;
    }
    
    /**
     * Gets the default sla
     * @param void
     * @return mixed $sla
     */
    public function getSystemDefaultSla() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->find(1);
        $sla = $ticket_setting->sla;
        return $sla;
    }
    
    /**
     * Gets the default Priority
     * @param void
     * @return mixed $priority
     */
    public function getSystemDefaultPriority() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->find(1);
        $priority = $ticket_setting->priority;
        return $priority;
    }

    /**
     * Gets the default Department
     * @param void
     * @return mixed $department
     */
    public function getSystemDefaultDepartment() {
        $systems = new \App\Model\helpdesk\Settings\System();
        $system = $systems->find(1);
        $department = $system->department;
        return $department;
    }

    

}
