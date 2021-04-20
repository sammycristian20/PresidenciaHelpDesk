<?php

namespace App\Upgrade\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Utility\Timezones;
use App\Model\helpdesk\Agent\Permission;
use App\Upgrade\database\comm_v1_9_6\seeds\CommToProSeeder;
use Exception;
use Artisan;
use App\User;
use Lang;
use Session;
use App\Http\Controllers\Utility\FormController;
/**
 * MicroOrganization activation controller
 * 
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name ActivateController
 * 
 */

class UpgradeController extends Controller {
    
    /**
     * Running migration for MicroOrganization
     */
    public function migrate($path) {
        try {
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
            return true;
        } catch (Exception $ex) {
            dd($ex);
            return false;
            //catch the exceptions
        }
    }
    /**
     * 
     * Run seeding for bill
     * 
     * @return int
     */
    public function seed() {
        try {
            $controller = new CommToProSeeder();
            $controller->run();
            return 1;
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     *
     *
     *
     *
     */
    public function upgrade()
    {
        try {
            $sys_version = System::pluck('version')->toArray();
            switch (\Request::segment(1)) {
                case 'comm-v1.9.6-to-pro-v1.9.19':
                    if ($this->checkVersionForUpdate($sys_version, '1.9.19')) {
                        $migrate_path = "app" . DIRECTORY_SEPARATOR . "Upgrade" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "comm_v1_9_6". DIRECTORY_SEPARATOR . "migrations";
                        if(!\Schema::hastable('permision')) {
                            if($this->migrate($migrate_path)){
                                $this->seed();
                                $old_system_tz = System::where('id', '=', 1)->pluck('time_zone')->toArray()[0];
                                $new_tz = Timezones::where('id', '=', $old_system_tz)->pluck('name')->toArray();
                                \DB::table('settings_system')->update([
                                    'time_zone' => $new_tz[0],
                                    'date_time_format' => 'd/m/Y H:i:s',
                                    'version' => '1.9.19'
                                ]);

                                $user_ids = User::where('role', '!=', 'user')->pluck('id')->toArray();
                                foreach ($user_ids as $id) {
                                    Permission::create([
                                        'user_id' => $id,
                                        'permision' => '{"create_ticket":"1","edit_ticket":"1","transfer_ticket":"1","delete_ticket":"1","assign_ticket":"1","access_kb":"1","ban_email":"1"}'
                                    ]);
                                }
                                return redirect()->to('/')->with('success', 'Voila, Faveo has been upgraded to pro.');
                            }
                        }
                    } else {
                        return redirect()->to('/')->with('success', Lang::get('lang.system-updated-message'));
                    }
                    break;
                case 'pro-v1.9.19-to-pro-v1.9.20':
                    if ($this->checkVersionForUpdate($sys_version, '1.9.20')) {
                        $this->updateFromData('addKeysInFields', ['ticket', 'user', 'organisation'], [['agentDisplay' => true]]);
                        
                        $this->updateFromData('removeKeysInFields', ['ticket', 'user', 'organisation'], ['agentShow', 'captcha'], false, ['Captcha']);

                        $add_field = [[
                                        'title' =>  'Micro Organisation',
                                        'agentlabel' => [
                                            ['language' => 'en',
                                            'label' => 'Micro Organisation',
                                            'flag' => faveoUrl('lb-faveo/flags/en.png')
                                            ]
                                        ],
                                        'clientlabel' => [
                                            ['language' => 'en',
                                             'label' => 'Micro Organisation',
                                             'flag' => faveoUrl('lb-faveo/flags/en.png')
                                            ]
                                        ],
                                        'type' => 'select',
                                        'agentRequiredFormSubmit' => false,
                                        'agentDisplay' => true,
                                        'customerDisplay' => false,
                                        'customerRequiredFormSubmit' => false,
                                        'value' => '',
                                        'api' => 'org_dept',
                                        'options' => [],
                                        'default' => 'yes',
                                        'unique' => 'org_dept'
                                    ]];

                        $this->updateFromData('addFieldsInForm', ['ticket'], $add_field);
                        $this->updateSystemVersion($sys_version, '1.9.20');
                    } else {
                        dd('Already updated');
                    }
                    break;

                case 'pro-v1.9.22-to-pro-v1.9.23' :
                    $ticket = \App\Model\helpdesk\Ticket\Tickets::all();
                    dd(count($ticket));
                    break;
                default:
                    # code...
                    break;
            }
        } catch(\Exception $e) {
            //catch the exception here
        }
    }

    /**
     *
     *
     *
     */
    private function checkVersionForUpdate($version, $update_to)
    {
        $can_update = false;
        foreach ($version as $v_string) {
            if($v_string != '') {
                if(strstr($v_string, "v")){
                    $v_string = explode("v", $v_string)[1];
                }
                
                if (version_compare($v_string, $update_to, '<')) {
                    $can_update = true;
                } else {
                    $can_update = false;
                }
            }
        }
        return $can_update;
    }

    /**
     *
     *
     *
     */
    private function updateFromData($to_do, $form_types, $values = [], $in_all_fields = true, $specific_fields = []) {
        switch ($to_do) {
            case 'addKeysInFields':
                $this->addKeysInFields($form_types, $values, $in_all_fields, $specific_fields);
                break;
            
            case 'addFieldsInForm':
                $this->addFieldsInForm($form_types, $values);
                break;
            
            case 'updateKeysOrValuesInFormField':
                # code...
                break;

            case 'removeKeysInFields':
                $this->removeKeysInFields($form_types, $values, $in_all_fields, $specific_fields);
                break;

            case 'removeFieldsInForm':
                # code...
                break;

            default:
                # code...
                break;
        }
        return true;
    }

    private function addKeysInFields($form_types, $values, $in_all_fields, $specific_fields)
    {
        foreach ($form_types as $form) {
            $new_form_array = [];
            $form_array = $this->getFormJsonDecodedArray($form);
            if($form_array != null) {
                if ($in_all_fields) {
                    foreach ($form_array[0] as $form_field) {
                        foreach ($values as $key_value_array) {
                            $form_field = $this->addKeyAndFieldArray($form_field, $key_value_array);
                        }
                        array_push($new_form_array, $form_field);
                    }
                    \DB::table('forms')->where('form', '=', $form)->update([
                        'json' => json_encode($new_form_array)
                    ]);
                    dump(json_encode($new_form_array));
                } else {
                    // write logic to enter key in specific fields
                }
            }
        }
        return true;
    }

    public function removeKeysInFields($form_types, $values, $in_all_fields, $specific_fields)
    {
        foreach ($form_types as $form) {
            $new_form_array = [];
            $form_array = $this->getFormJsonDecodedArray($form);
            if($form_array != null) {
                if ($in_all_fields) {
                    //remove key from all fields
                } else {
                    for($i =0 ; $i< count($form_array[0]); $i++) {
                        foreach ($specific_fields as $sf) {
                            if ($form_array[0][$i]['title'] == $sf || (array_key_exists('unique', $form_array[0][$i]) && $form_array[0][$i]['unique'] == $sf)) {
                                foreach ($values as $value) {
                                    if(array_key_exists($value, $form_array[0][$i])) {
                                       unset($form_array[0][$i][$value]);
                                    } else {
                                        echo 'hakkuna mattata<br>';
                                    }
                                }
                            }
                        }                        
                    }
                    \DB::table('forms')->where('form', '=', $form)->update([
                        'json' => json_encode($form_array[0])
                    ]);
                    dump(json_encode($form_array[0]));
                }
            }
        }
        return true;
    }

    private function getFormJsonDecodedArray($form)
    {
        $controller = new FormController();
        $json_string = $controller->getTicketFormJson($form);
        $form_array = json_decode($json_string, true);
        return $form_array;
    }
    public function addFieldsInForm($form_types, $values)
    {
        foreach ($form_types as $form) {
            $form_array = $this->getFormJsonDecodedArray($form);
            foreach ($values as $value) {
                if(strpos(json_encode($form_array[0]), '"unique":"'.$value['unique'].'"') == false) {
                    array_push($form_array[0], $value);
                } else {
                    echo $value['unique']." already exists";
                }
            }
            \DB::table('forms')->where('form', '=', $form)->update([
                'json' => json_encode($form_array[0])
            ]);
        }
        return true;
    }

    private function updateSystemVersion($old, $new)
    {
        \DB::table('settings_system')->where('version', '=', $old[0])
        ->update([
            'version' => $new
        ]);
    }

    private function addKeyAndFieldArray($form_field, $key_value_array)
    {
        if(!array_key_exists(key($key_value_array), $form_field)) {
            $form_field = $form_field+$key_value_array;
        }
        if (array_key_exists('options', $form_field)) {
            if(count($form_field['options']) > 0) {
                foreach ($form_field['options'] as $key => $field) {
                    $form_field['options'][$key] = $this->updateOptionNode($field, $key_value_array);
                }
            }
        }
        return $form_field;
    }

    private function updateOptionNode($field, $key_value_array)
    {
        if (array_key_exists('nodes', $field)) {
            if(count($field['nodes']) > 0) {
                foreach ($field['nodes'] as $key => $node) {
                    $field['nodes'][$key] = $this->addKeyAndFieldArray($node, $key_value_array);
                }
            }
        }
        return $field;
    }
}
