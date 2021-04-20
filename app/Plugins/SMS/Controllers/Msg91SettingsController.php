<?php

namespace App\Plugins\SMS\Controllers;

//controllers
use App\Http\Controllers\Controller;
use App\Plugins\SMS\Controllers\SMSTemplateController;
use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController as Notify;
//models
use App\Plugins\SMS\Model\Msg91;
use App\Plugins\SMS\Model\Provider;
//classes
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use DB;
use Lang;
use Logger;
use Schema;

class Msg91SettingsController extends Controller
{
    /**
     * @category function to activae SMS plugin and creating necessary datatables in DB
     * @param null
     * @return null
     */
    public function activate()
    {
        try {
            $this->createSMSTable();
            $this->createProvidersTable();
            $this->createSendSMSColumnInSLAAndStatus(['ticket_status', 'sla_targets']);
            $template_controller = new SMSTemplateController;
            $template_controller->createSMSTemplates();
        } catch (\Exception $e) {
            Logger::exception($e);
            //return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * @category function to create SMS table
     * @param null
     * @return boolean true/false
     */
    public function createSMSTable()
    {
        if (!Schema::hasTable('sms')) {
            Schema::create('sms', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('provider_id');
                $table->string('name');
                $table->string('value');
                $table->timestamps();
            });
            return true;
        }
        return false;
    }
    
    /**
     * @category function to create providers table
     * @param null
     * @return boolean true/false
     */
    public function createProvidersTable()
    {
        if (!Schema::hasTable('sms_service_providers')) {
            Schema::create('sms_service_providers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('status');
                $table->timestamps();
            });
            //inserting available provider for SMS service
            $provider = new Provider;
            if ($provider->count() == 0) {
                $provider->create(['name' => 'msg91.com', 'status' => 0]);
                $provider->create(['name' => 'smslive247.com', 'status' => 0]);
            }
        }
        return true;
    }

    /**
     * @category function to show main settings page for SMS
     * @param null
     * @return response/view
     */
    public function showMain()
    {
        return view('SMS::main');
    }

    public function getForm()
    {
        $sms = new Msg91;
        $providers = new Provider;
        $active_provider = $providers->where('status', '=', 1)->first();
        $providers = $providers->get();
        $path = app_path() . '/Plugins/Msg91/views';
        \View::addNamespace('plugins', $path);
        if ($active_provider != null) {
            $settings = $sms->where('provider_id', '=', $active_provider->id)->get();
            if ($active_provider->id == 1) {
                $auth_key = $sms->where('name', 'auth_key')->first()->value;
                $sender_id = $sms->where('name', 'sender_id')->first()->value;
                $route = $sms->where('name', 'route')->first()->value;
                $provider_id = $sms->where('provider_id', '1')->first()->id;
                $owner_email = '';
                $subacc = '';
                $subacc_pass = '';
                $smslive_sender_id = '';
            } else {
                $auth_key = '';
                $sender_id = '';
                $route = '';
                $provider_id = $sms->where('provider_id', '2')->first()->id;
                $owner_email = $sms->where('name', 'owner_email')->first()->value;
                $subacc = $sms->where('name', 'subacc')->first()->value;
                $subacc_pass = $sms->where('name', 'subacc_pass')->first()->value;
                $smslive_sender_id = $sms->where('name', 'smslive_sender_id')->first()->value;
            }
            if ($settings->count() > 0) {
                // $auth_key = $sms->where('name', 'auth_key')->first()->value;
                // $sender_id = $sms->where('name', 'sender_id')->first()->value;
                // $route = $sms->where('name', 'route')->first()->value;
                // $provider_id = $sms->where('provider_id', '1')->first()->id;
                return view('SMS::settings', compact('providers', 'auth_key', 'sender_id', 'route', 'provider_id', 'owner_email', 'subacc', 'subacc_pass', 'smslive_sender_id'));
            } else {
                $auth_key = '';
                $sender_id = '';
                $route = '';
                $provider_id = '';
                $owner_email = '';
                $subacc = '';
                $subacc_pass = '';
                $smslive_sender_id = '';
                return view('SMS::settings', compact('providers', 'auth_key', 'sender_id', 'route', 'provider_id', 'owner_email', 'subacc', 'subacc_pass', 'smslive_sender_id'));
            }
        } else {
                $auth_key = '';
                $sender_id = '';
                $route = '';
                $provider_id = '';
                $owner_email = '';
                $subacc = '';
                $subacc_pass = '';
                $smslive_sender_id = '';
                return view('SMS::settings', compact('providers', 'auth_key', 'sender_id', 'route', 'provider_id', 'owner_email', 'subacc', 'subacc_pass', 'smslive_sender_id'));
        }
    }

    /**
     * @category function to save message service provider settings
     * @param object $request
     * @var
     * @return response view with success/fail message
     */
    public function postForm(Request $request)
    {
        if ($request->input('provider_id') == '1' || $request->input('provider_id') == 1) {
            $this->validate($request, [
                'auth_key'=>'required',
                'sender_id'=>'required',
                'route'=>'required',
                'mobile'=>'required|numeric',
            ]);
        } else {
            $this->validate($request, [
                'owner_email' => 'required|email',
                'subacc'    => 'required',
                'subacc_pass' => 'required',
                'mobile' => 'required|numeric',
                'smslive_sender_id' => 'required',
            ]);
        }
        try {
            if ($request->input('provider_id') == 1 || $request->input('provider_id') == '1') {
                $auth_key = $request->input('auth_key');
                $sender_id = $request->input('sender_id');
                $route = $request->input('route');
                $owner_email = '';
                $subacc = '';
                $subacc_pass = '';
                $smslive_sender_id = '';
            } else {
                $auth_key = '';
                $sender_id = '';
                $route = '';
                $owner_email = $request->input('owner_email');
                $subacc = $request->input('subacc');
                $subacc_pass = $request->input('subacc_pass');
                $smslive_sender_id = $request->input('smslive_sender_id');
            }
            $sms = new Msg91;
            $providers = new Provider;
            $notify = $request->input('notification');
            $ticket = $request->input('ticket');
            $provider_id = $request->input('provider_id');
            $mobileNumber = $request->input('mobile');
            $msg_Controller = new Msg91Controller();
            $response = $msg_Controller->testConnection($auth_key, $mobileNumber, $sender_id, $route, $owner_email, $subacc, $subacc_pass, $smslive_sender_id);
            if ($response) {
                if ($provider_id == 1 || $provider_id == '1') {
                    \DB::table('sms')->delete();
                    $sms->create(['provider_id' => $provider_id, 'name' => 'auth_key', 'value' => $auth_key]);
                    $sms->create(['provider_id' => $provider_id, 'name' => 'sender_id', 'value' => $sender_id]);
                    $sms->create(['provider_id' => $provider_id, 'name' => 'route', 'value' => $route]);
                    $providers->where('id', '=', $provider_id)->update(['status' => 1]);
                    $providers->where('id', '!=', $provider_id)->update(['status' => 0]);
                } else {
                    if (strstr($response, 'ERR: ')) {
                        return redirect()
                        ->back()
                        ->with('fails', $response)
                        ->withInput();
                    } else {
                        \DB::table('sms')->delete();
                        $sms->create(['provider_id' => $provider_id, 'name' => 'owner_email', 'value' => $owner_email]);
                        $sms->create(['provider_id' => $provider_id, 'name' => 'subacc', 'value' => $subacc]);
                        $sms->create(['provider_id' => $provider_id, 'name' => 'subacc_pass', 'value' => $subacc_pass]);
                        $sms->create(['provider_id' => $provider_id, 'name' => 'smslive_sender_id', 'value' => $smslive_sender_id]);
                        $providers->where('id', '=', $provider_id)->update(['status' => 1]);
                        $providers->where('id', '!=', $provider_id)->update(['status' => 0]);
                    }
                }
                return redirect()->back()->with('success', Lang::get('SMS::lang.provider-settings-saved-successfully'));
            }
            return redirect()->back()->with('fails', Lang::get('SMS::lang.can-not-connect-to-service-provider'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', Lang::get('SMS::lang.can-not-connect-to-service-provider'));
        }
    }

    /**
     * @category function to render SMS mode checkbox in alerts and notices settings
     * @param array $data
     * @return null
     */
    public function renderSMSModeCheckbox($data)
    {
        if ($this->checkPluginSetup()) {
            $field_name = $data[0].'[]';
            $form = \Form::checkbox($field_name, 'sms', $data[1]->isValueExists($data[0], 'sms'))." ".Lang::get('lang.sms')."&nbsp;&nbsp;";
            return $form;
        }
    }

    /**
     * @category function to render SMS mode checkbox in status settings
     * @param array $data
     * @return null
     */
    public function renderSTatusSMSModeCheckbox($data)
    {
        if ($this->checkPluginSetup()) {
            $value = false;
            if (count($data) > 0) {
                $value = $data[0]->send_sms;
            }
            echo '<div class="row"  id="sending_email">
                <div class="col-md-6 form-group">'
                    .\Form::label('send_email', Lang::get('lang.send_sms'))
                    .'<div class="row">
                        <div class="col-xs-3">'
                            .\Form::radio("send_sms", '1', $value).' '.Lang::get('lang.yes')
                        .'</div>
                        <div class="col-xs-3">'
                            .\Form::radio("send_sms", '0', !$value).' '.Lang::get('lang.no')
                        .'</div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="callout callout-default" style="font-style: oblique;">'.Lang::get("lang.send_status_update_via_sms").'</div>
                </div>
            </div>';
        }
    }

    /**
     * @category function to render SMS mode label in SLA settings
     * @param array $data
     * @return null
     */
    public function renderSlaSmsLabel($mode)
    {
        if ($this->checkPluginSetup()) {
            echo "<th>".Lang::get('lang.send_report_SMS')."</th>";
        }
    }

    /**
     * @category function to render SMS send option in SLA settings
     * @param array $data
     * @return null
     */
    public function renderSlaSmsOption($mode)
    {
        if ($this->checkPluginSetup()) {
            $selected = 'selected';
            $not_selected = '';
            if (count($mode) >0) {
                if ($mode[0]->send_sms == 1) {
                    $not_selected = 'selected';
                    $selected = '';
                }
            }
            echo '<td>
                    <select name="send_sms" class="form-control">
                        <option value="1" '.$not_selected.'>'.Lang::get('lang.yes').'</option>
                        <option value="0" '.$selected.'>'.Lang::get('lang.no').'</option>
                    </select> 
                </td>';
        }
    }

    /**
     * @category function to show account verification option by mobile
     * @param array $mode
     * @return html string
     */
    public function renderMobileAccountVerificationOption($mode)
    {
        if ($this->checkPluginSetup()) {
            echo \Form::checkbox('login_restrictions[]', 'mobile', in_array('mobile', $mode[0]), ["class" => "boption"]).' '.Lang::get('lang.activate-by-mobile-number');
        }
    }

    /**
     * @category function to check if SMS plugin is setup or not
     * @param null
     * @return boolean true/false
     */
    public function checkPluginSetup()
    {
        $controller = $alert = new Notify();
        if ($controller->checkPluginSetup()) {
            return true;
        }

        return false;
    }

    public function createSendSMSColumnInSLAAndStatus($table_name_array = [])
    {
        if(count($table_name_array) > 0) {
            foreach ($table_name_array as $table_name) {
                $this->checkAndCreateColumn($table_name, 'send_sms');
            }
        }
    }

    public function checkAndCreateColumn($table_name, $column_name)
    {
        if (!Schema::hasColumn($table_name, $column_name)) {
            //add column to the table
            Schema::table($table_name, function (Blueprint $table) use ($column_name)
            {
                $table->boolean($column_name)->default(0);
            });
            return true;
        }
        return false;
    }
}
