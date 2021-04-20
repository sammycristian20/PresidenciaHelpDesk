<?php

namespace App\Bill\Controllers;

use App\Http\Controllers\Controller;
use App\Bill\Controllers\ActivateController;
use Exception;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Bill\Requests\BillRequest;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Model\helpdesk\Form\FormField;
use Lang;
use Form;
/**
 * Setting for the bill module
 *
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name SettingsController
 *
 */
class SettingsController extends ActivateController
{
    public function __construct()
    {
        (new ActivateController)->activate();
    }

    /**
     * 
     * get the setting view for bill
     * 
     * @return view
     */
    public function setting()
    {
        try {
            $bill_types = collect([]);
            $status     = new Ticket_Status();
            $statuses   = $status->pluck('name', 'id')->toArray();
            $set        = new CommonSettings();
            $level      = $set->getOptionValue('bill', 'level');
            $currency   = $set->getOptionValue('bill', 'currency');
            if ($level && $level->option_value == 'type') {
                $bill_types = \App\Bill\Models\BillType::
                        with(['types'])
                        ->select('id', 'type', 'price')->get()
                        ->transform(function($v) {
                    if ($v) {
                        return [
                            'id'          => ($v->types) ? $v->types->id: 'd',
                            'optionvalue' => ($v->types) ? $v->types->name : '',
                            'price'       => $v->price,
                        ];
                    }
                    return [
                        'id'          => '',
                        'optionvalue' => '',
                        'price'       => '',
                    ];
                });
            }
            return view('Bill::settings.setting', compact('statuses', 'level', 'currency', 'bill_types'));
        } catch (Exception $ex) {

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * Billing post settings request
     * 
     * @param Request $request
     * @return string
     */
    public function postSetting(Request $request)
    {

        $this->validate($request, [
            'currency' => 'max:15',
        ]);
        try {
            $set         = new CommonSettings();
            $option_name = "bill";
            $requests    = $request->except('_token', 'trigger_on');
            $req         = $request->except('_token', 'trigger_on', 'status', 'level', 'currency');
            if (count($requests) > 0) {
                foreach ($requests as $key => $value) {
                    if ($key == 'status' && !is_array($value)) {
                        $create         = $set->firstOrCreate(['option_name' => $option_name]);
                        $create->status = $value;
                        $create->save();
                    } elseif (!is_array($value)) {
                        $create               = $set->firstOrCreate(['option_name' => $option_name, 'optional_field' => $key]);
                        $create->option_value = $value;
                        $create->save();

                        if($key == "allowWithoutPackage" && !$value) {
                          // deactivating the field
                          FormField::where('title','Billing')->update(['is_active'=>1]);
                        }

                        if($key == "allowWithoutPackage" && $value){
                          // deactivating the field
                          FormField::where('title','Billing')->update(['is_active'=> 0]);
                        }
                    }
                }
            }
            $this->billType($req);
            return response()->json(['success' => Lang::get('lang.bill_settings_saved_successfully')]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function billType($request = [])
    {
        if ($request) {
            $bill_type = new \App\Bill\Models\BillType();
            $bill_type->truncate();
            foreach ($request as $req) {
                if (is_array($req)) {
                    $bill_type->updateOrCreate(['type' => $req['type']], ['price' => $req['price']]);
                }
            }
        }
    }

    public function renderSettingsBlock()
    {
        echo '<div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">'.trans('lang.bill').'</h3>
                </div>
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="row">
                            <div class="col-md-2 col-sm-6">
                                <div class="settingiconblue">
                                    <div class="settingdivblue">
                                        <a href="' . url('bill') . '" onclick="sidebaropen(this)">
                                            <span class="fa-stack fa-2x">
                                                <i class="fas fa-wrench fa-stack-1x"></i>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="text-center text-sm">' . Lang::get('Bill::lang.options') . '</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="settingiconblue">
                                    <div class="settingdivblue">
                                        <a href="' . url('bill/package/inbox') . '" onclick="sidebaropen(this)">
                                            <span class="fa-stack fa-2x">
                                                <i class="fas fa-suitcase fa-stack-1x"></i>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="text-center text-sm">' . Lang::get('Bill::lang.packages') . '</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="settingiconblue">
                                    <div class="settingdivblue">
                                        <a href="' . url('bill/payment-gateways') . '" onclick="sidebaropen(this)">
                                            <span class="fa-stack fa-2x">
                                                <i class="fab fa-cc-paypal fa-stack-1x"></i>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="text-center text-sm">' . Lang::get('Bill::lang.payment_gateway') . '</div>
                                </div>
                            </div>
                        </div>
                    <!-- /.row -->
                    </div>
                    <!-- ./box-body -->
            </div>';
    }

    public function renderAlertAndNoticeSettings($alerts, $settings, $persons = ['admin', 'agent', 'client'])
    {
        $alertPersons = '';
        $sms = \Event::dispatch('sms_option', [[$settings.'_mode', $alerts]]);
        $sms = empty($sms) ? null : $sms[0]; 
        foreach ($persons as $person) {
            $alertPersons .= '<div class="form-group">'.
            Form::checkbox($settings.'_persons[]', $person, $alerts->isValueExists($settings.'_persons', $person)).' '.
            Form::label($settings.'_persons',Lang::get("lang.{$person}")) .'
            </div>';
        }
        echo '<div class="col-md-12">
                <div class="card card-light">
                    <div class="card-header">
                        <h3 class="card-title">'.trans('Bill::lang.'.$settings).'</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                        '.Form::label($settings.'', Lang::get('lang.status').":").'&nbsp;&nbsp;
                        '.Form::radio($settings.'',1,$alerts->isValueExists($settings.'',1)).' '.Lang::get('lang.enable').'&nbsp;&nbsp; 
                        '.Form::radio($settings.'',0,$alerts->isValueExists($settings.'',0)).' '. Lang::get('lang.disable')
                        .'</div>
                        <div class="form-group">
                            '.Form::label($settings.'_mode',Lang::get('lang.mode').":").'&nbsp;&nbsp;
                        '.Form::checkbox($settings.'_mode[]','email',$alerts->isValueExists($settings.'_mode','email')).' '.Lang::get('lang.email')  .'&nbsp;&nbsp; 
                        './**Form::checkbox($settings.'_mode[]','system',$alerts->isValueExists($settings.'_mode','system')).' '.Lang::get('lang.in_app_system')**/
                        $sms.'
                        </div>
                        '.$alertPersons.'
                    </div>
                </div>
            </div>';
    }

    /**
     * Fucntion to handle update client layout data event. It upates the layout
     * data which is used to render options in client layout.
     * @param Array $data referrence to data array
     */
    public function updateClientLayoutData(&$data)
    {
        $data['billing_settings'] = ['active'=> (bool)commonSettings('bill', 'show_packages'), 'currency' => commonSettings('bill', 'currency')];
    }

    /**
     * Function listens the event to update template variable query builder
     * and appends shortcodes of Billing plugin in template variables
     * @param QueryBuilder  $data
     * @return void as event must not be used to return value
     */
    public function updateTemplateVariables(QueryBuilder $data) :void
    {
        if(commonSettings('bill', 'show_packages')) {
            $data->orWhere('plugin_name', 'Bill');
        }        
    }

    /**
     * Function listens the event to update template list query builder
     * and appends templates of Billing plugin in template list
     * @param QueryBuilder  $data
     * @return void as event must not be used to return value
     */
    public function updateTemplateList(QueryBuilder $data) :void
    {
        if(commonSettings('bill', 'show_packages')) {
            $data->orWhere('ty.plugin_name', 'Bill');
        }        
    }

    /**
     * Function listens update top navbar event and adds invoice icon with link
     * @return void as event must not be used to return value
     */
    public function showInvoiceTabOnTopBar() :void
    {
        if(commonSettings('bill', 'show_packages')) {
            $count = \App\Bill\Models\Invoice::whereHas('order', function($q){
                $q->where('status', 0);
            })->count();
            $url = url("billing/invoices");
            echo '<li @yield("Invoice") class="nav-item d-zone d-sm-inline-block" data-toggle="tooltip" data-placement="bottom" title="'.trans('Bill::lang.pending_invoices').'"><a href="'.url('billing/invoices').'?status=0" style="direction:ltr;unicode-bidi: embed;" class="nav-link" ><i class="far fa-file-pdf"></i><span class="badge badge-danger navbar-badge" id="count">'.$count.'</span></a></li>';
        }
    }
}
