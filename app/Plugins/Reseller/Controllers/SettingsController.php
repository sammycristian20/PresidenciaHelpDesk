<?php

namespace App\Plugins\Reseller\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\Reseller\Model\Reseller;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use App\Plugins\Reseller\Controllers\ResellerEventController;
use App\Plugins\Reseller\Model\ResellerDepartment;
use App\Plugins\Reseller\Model\ResellerCustomField;

class SettingsController extends Controller {

    public function index() {
        $resellers = new Reseller;
        $reseller = $resellers->where('id', '1')->first();

        if (!$reseller) {
            $resellers->create(['id' => '1']);
        }
        return view('reseller::settings', compact('reseller'));
    }

    public function update(Request $request) {
        
        try {
            $userid = $request->input('userid');
            $apikey = $request->input('apikey');
            $mode = $request->input('mode');
            $url = $this->url($mode);

            $test = $this->testConnection($userid, $apikey, $url);

            if (!is_array($test)) {
                throw new \Exception($test);
            }
            if (is_array($test)) {
                if (key_exists('status', $test)) {
                    if ($test['status'] == 'ERROR') {
                        throw new \Exception($test['message']);
                    }
                }
            }
            $reseller = new Reseller;
            $reseller = $reseller->where('id', '1')->first();
            $reseller->fill($request->input())->save();
            return $this->successResponse('Updated Successfully');
        } catch (\Exception $ex) {

            return $this->failsResponse($ex->getMessage());
        }
    }

    public function testConnection($userid, $apikey, $url) {
        try {
            
            $reseller_Controller = new ResellerController();
            $data = $reseller_Controller->test($userid, $apikey, $url, $domain = "facebook.com");
            return $data;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function Populate($url) {
        $reseller = new Reseller;
        $reseller = $reseller->where('id', '1')->first();
        $data = '';
        $getapi = new ResellerEventController;
        $result = $getapi->GetApi($data, $url);
        return json_decode($result);
    }

    public function GetDepartments() {
        $url = 'http://faveo.support-tools.com/getdepartment';
        $departments = $this->Populate($url);
        //dd($departments);
        if (!Schema::hasTable('reseller_department')) {
            Schema::create('reseller_department', function($table) {
                $table->increments('id');
                $table->integer('middledpt_id');
                $table->string('rcdpt_name');
                $table->timestamps();
            });
        }
        $depart = new ResellerDepartment;
        if ($depart->count() > 0) {
            $departs = $depart->get();
            foreach ($departs as $dep) {
                $dep->delete();
            }
        }
        foreach ($departments as $department) {
            $depart->create(['middledpt_id' => $department->department_id, 'rcdpt_name' => $department->name]);
        }

        $this->GetCustomFields();

        return redirect()->back()->with('success', 'Department populated');
    }

    public function GetCustomFields() {

        $url = 'http://faveo.support-tools.com/getcustom';
        $customfields = $this->Populate($url);

        if (!Schema::hasTable('reseller_custom_fields')) {
            //dd($customfields);
            Schema::create('reseller_custom_fields', function($table) {
                $table->increments('id');
                $table->integer('custom_id');
                $table->string('field_name');
                $table->integer('isrequired');
                $table->integer('fieldtype')->nullable();
                $table->integer('department_id')->nullable();
                $table->string('title');
                $table->timestamps();
            });
        }
        $custom = new ResellerCustomField;
        if ($custom->count() > 0) {
            $customs = $custom->get();
            foreach ($customs as $dep) {
                $dep->delete();
            }
        }
        foreach ($customfields as $customfield) {
            //echo $customfield->custom_id;
            //exit;
            if ($customfield) {
                $custom->create(['custom_id' => $customfield->custom_id, 'field_name' => $customfield->fieldname, 'isrequired' => $customfield->isrequired, 'fieldtype' => $customfield->fieldtype, 'department_id' => $customfield->department_id, 'title' => $customfield->title]);
            }
        }
        $this->GetFieldValues();
//        return redirect()->back()->with('success','Custom Field populated'); 
    }

    public function GetFieldValues() {
        if (!Schema::hasTable('reseller_custom_values')) {

            Schema::create('reseller_custom_values', function($table) {
                $table->increments('id');
                $table->integer('customfieldid');
                $table->string('optionvalue');
                $table->integer('isselected');
                $table->integer('parentcustomfieldoptionid');
                $table->timestamps();
            });
        }
        \DB::table('reseller_custom_values')->delete();
        $url = 'http://faveo.support-tools.com/get-field-values';

        $values = $this->Populate($url);
        foreach ($values as $value) {

            \DB::table('reseller_custom_values')->insert(['customfieldid' => $value->customfieldid, 'optionvalue' => $value->optionvalue, 'isselected' => $value->isselected, 'parentcustomfieldoptionid' => $value->parentcustomfieldoptionid]);
        }
    }

    /**
     * Send for white list
     * @param type $userid
     * @param type $ip
     */
    public function WhiteList($userid, $ip) {
        $url = 'http://faveo.support-tools.com/whitelists';
        $data = [
            'userid' => $userid,
            'ip' => $ip
        ];
        $new = new ResellerEventController();
        $new->PostApi($data, $url);
    }

    public function successResponse($message) {
        return "<div class='alert alert-success alert-dismissable'>
        <b>Success!</b>
        <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
        $message
    </div>";
    }

    public function failsResponse($message) {
        return "<div class='alert alert-danger alert-dismissable'>
        <b>Fails!</b>
        <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
        $message
    </div>";
    }

    public static function url($mode) {
        try {
            switch ($mode) {
                case 0: return "https://test.httpapi.com/";
                case 1: return "https://httpapi.com/";
                default : return "https://test.httpapi.com/";
            }
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    public function activate(){
        if (!Schema::hasTable('reseller')) {
            Schema::create('reseller', function($table) {
                $table->increments('id');
                $table->string('userid');
                $table->string('apikey');
                $table->integer('mode');
                $table->integer('enforce');
                $table->timestamps();
            });
        }
    }

}
