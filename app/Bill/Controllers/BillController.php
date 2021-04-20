<?php

namespace App\Bill\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use Exception;
use App\Bill\Models\Bill;
use Auth;
use Illuminate\Http\Request;

/**
 * Bill controller
 * 
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name BillController
 * 
 */
class BillController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'role.agent']);
    }
    /**
     * 
     * If thread level condition
     * 
     * @return string
     */
    public function threadLevelForm()
    {

        if ($this->isThreadLevel() == true) {
            return $this->renderThreadLevelForm();
        }
    }
    /**
     * 
     * render thread level HTML Form
     * 
     * @return string
     */
    // "pattern"=>"^([0-9]|0[0-9]|1[0-9]|2[0-9]|0[0-9][0-9]|1[0-9][0-9]|2[0-9][0-9]):[0-5][0-9]$","title"=>"Only accept HH:MM format"
    public function renderThreadLevelForm()
    {
        if ($this->isThreadLevel() == true) {
            return '<div class="form-group">
                            <div class="row">
            <div class="form-group">
                                    <div class="col-md-2">
                                        ' . \Form::label("hours", \Lang::get("lang.hours")) . '<span class="text-red"> *</span>' . "<span>&nbsp;  <i>(use ':' to seperate hours and minutes)</i></span>" . '
                                    </div>
                                    <div class="col-md-10">
                                        <div id="newtextarea">
                                           ' . \Form::text('hours', null, ["class" => "form-control", "style" => "width:25%", "placeholder" => "HH:MM"]) . '
                                        </div>
                                        
                                    </div>
                                </div>
                                </div>
                                </div>
                                <div class="form-group">
                            <div class="row">
            <div class="form-group">
                                    <div class="col-md-2">
                                        ' . \Form::label("billable", \Lang::get("lang.billable")) .'
                                    </div>
                                    
                                        <div class="col-md-1">
                                           ' . \Form::radio('billable', 1, true, ['onClick' => 'toggleBillable(1)']) . " " . \Lang::get("lang.yes") . '
                                        </div>
                                        <div class="col-md-1">
                                           ' . \Form::radio('billable', 0, false, ['onClick' => 'toggleBillable(0)']) . " " . \Lang::get("lang.no") . '
                                        </div>
                                        
                                    
                                </div>
                                </div>
                                </div>
                                <div class="form-group">
                            <div class="row amount">
            <div class="form-group">
                                    <div class="col-md-2">
                                        ' . \Form::label("amount_hourly", \Lang::get("lang.amount-per-hour")) . '<span class="text-red"> *</span>
                                    </div>
                                    <div class="col-md-10">
                                        <div id="newtextarea">
                                           ' . \Form::number('amount_hourly', null, ["class" => "form-control", "style" => "width:25%", "min" => "0"]) . '
                                        </div>
                                        
                                    </div>
                                </div>
                                </div>
                                </div>';
        }
    }
    /**
     * 
     * Validating the request for bill
     * 
     * @param string $request
     */
    public function requestRule($request)
    {
        if ($this->isThreadLevel() == true) {
            $this->validate($request, [
                'hour' => ['required', 'regex:/^[0-9]*:[0-5][0-9]$/'],
            ]);
        }
    }
    /**
     * 
     * Event listening function from TicketController class to bill
     * 
     * @param object $event
     */
    public function postReply($event)
    {
        if ($this->isThreadLevel() == true) {
            $note    = $event->para1;
            $request = $event->para4;
            $ticket  = $event->para5;
            $thread  = $event->para6;
            try {
                $level = $this->findLevel();
                if ($level) {
                    $model    = $this->getModel($level, $ticket, $thread);
                    $modelid  = $model->id;
                    $hours    = $request->input('hour');
                    $billable = $request->input('billable');
                    $agentid  = Auth::user()->id;
                    $ticketid = $request->input('ticket_ID');
                    $amount   = $request->input('amount_hourly');
                    $this->saveBill($level, $modelid, $hours, $billable, $agentid, $ticketid, $note, $amount);
                }
            } catch (Exception $ex) {
                // dd($ex);
            }
        }
    }
    /**
     * 
     * Get the billing level
     * 
     * @return string
     */
    public function findLevel()
    {
        if (isBill() == true) {
            $set    = new CommonSettings();
            $schema = $set->getOptionValue('bill', 'level');
            if ($schema) {
                return $schema->option_value;
            }
        }
    }
    /**
     * 
     * Get the model according to level
     * 
     * @param type $level
     * @param type $ticket
     * @param type $thread
     * @return type object
     */
    public function getModel($level, $ticket, $thread)
    {
        switch ($level) {
            case "ticket":
                return $ticket;
            case "thread":
                return $thread;
        }
    }
    /**
     * 
     * Saving the billing
     * 
     * @param string $level
     * @param int $modelid
     * @param number $hours
     * @param int $billable
     * @param int $agentid
     * @param int $ticketid
     * @param string $note
     * @param number $amount
     */
    public function saveBill($level, $modelid, $hours, $billable, $agentid, $ticketid, $note, $amount)
    {
        $bill = new Bill();
        $bill->create([
            'level'         => $level,
            'model_id'      => $modelid,
            'hours'         => $hours,
            'billable'      => $billable,
            'agent'         => $agentid,
            'ticket_id'     => $ticketid,
            'note'          => $note,
            'amount_hourly' => $amount,
        ]);
    }
    /**
     * 
     * Billing ticket level tab in ticket deatail page
     * 
     * @param int $ticket
     * @return string
     */
    public function billingTabList($ticket)
    {
        if (isBill() == true) {
            return '<li><a href="#bill" data-toggle="tab"><i class="fa fa-file-text"> </i> Bill</a></li>';
        }
    }
    /**
     * 
     * Billing Tab Content
     * 
     * @param class $ticket
     * @return view
     */
    public function billingTabContent($ticket)
    {
        $bills = "";
        if (isBill() == true) {
            $ticketid  = $ticket->id;
            $bil       = new Bill();
            $bill_type = \App\Bill\Models\BillType::where('type', $ticket->type)->first();
            if ($this->getLevel() === 'type' && $bill_type) {

                $price         = $this->getPrice($ticketid);
                $billable      = checkArray('time', $price);
                $amount_hourly = $bill_type->price;
                $cost          = checkArray('cost', $price);
                return view('Bill::bill-display', compact('cost', 'billable', 'amount_hourly', 'bills', 'ticket'));
            }
            $billable_hours_array = $bil->where('ticket_id', $ticketid)->where('billable', 1)->select('amount_hourly', 'hours')->get()->toArray();
            $bill_coll = collect($billable_hours_array);
            $bill_time = $bill_coll->sum('hours');
            $bill_amount = $bill_coll->transform(function($value){
                ($value['amount_hourly'] == '') && ($value['amount_hourly'] = 0);
                return $value['amount_hourly'] = $value['amount_hourly']*$value['hours'];
            })->sum();
            $billable             = ['time'=>$bill_time,'amount'=>$bill_amount];
            $nonbillable_array    = $bil->where('ticket_id', $ticketid)->where('billable', 0)->select('amount_hourly', 'hours')->get()->toArray();
            $non_bill_col = collect($nonbillable_array);
            $non_bill_time = $bill_coll->sum('hours');
            $non_bill_amount = $non_bill_col->transform(function($value){
                return $value['amount_hourly'] = $value['amount_hourly']*$value['hours'];
            })->sum();
            $nonbillable          = ['time'=>$non_bill_time,'amount'=>$non_bill_amount];
            $bills                = $bil->where('ticket_id', $ticketid)
                            ->select('id', 'agent', 'hours', 'amount_hourly', 'note', 'created_at', 'billable')->get();
            return view('Bill::bill-display', compact('bills', 'billable', 'nonbillable', 'ticket'));
        }
    }
    /**
     * 
     * deleting thebilling entry
     * 
     * @param int $id
     * @return string
     * @throws Exception
     */
    public function delete($id)
    {
        try {
            $bills = new Bill();
            $bill  = $bills->find($id);
            if (!$bill) {
                throw new Exception('Sorry! We could not find your request');
            }
            $bill->delete();
            return redirect()->back()->with('success', 'deleted');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * Get billable hours
     * 
     * @param array $array
     * @return array
     */
    public function getBillableHours($array)
    {
        $time     = 0;
        $amount   = 0;
        $out      = ['hours' => '', 'min' => ''];
        $subtotal = [];
        if ($array > 0) {
            foreach ($array as $key => $value) {
                if (str_contains($key, ":")) {
                    $change = ":";
                }
                if (str_contains($key, ".")) {
                    $change = ".";
                }
                $time         = explode($change, $key);
                $out['hours'] = (int)$out['hours'] + (int)$time[0];
                $out['min']   = (int)$out['min'] + (int)$time[1];
                $samay        = $time[0] . '.' . $time[1];
                ($value == '') && ($value = 0);
                $subtotal[]   = $samay * $value;
            }
            $hour                = (int)$out['hours'];
            $min                 = (int)$out['min'];
            $amount              = array_sum($subtotal);
            $convert_min_to_hour = floor($min / 60);
            if ($convert_min_to_hour > 0) {
                $hour = $hour + $convert_min_to_hour;
                $min  = $min % 60;
            }
            if ($hour != "" || $min != "") {
                $time = $hour . ":" . $min;
            }
        }
        return ['time' => $time, 'amount' => $amount];
    }
    /**
     * 
     * get the billing setup values
     * 
     * @param string $field
     * @return string
     */
    public static function billSettings($field)
    {
        $set    = new \App\Model\helpdesk\Settings\CommonSettings();
        $schema = $set->getOptionValue('bill', $field);
        if ($schema) {
            return $schema->option_value;
        }
    }
    /**
     * 
     * get billing currency
     * 
     * @return string
     */
    public static function currency()
    {
        $currency = self::billSettings('currency');
        if (!$currency) {
            $currency = "";
        }
        return $currency;
    }
    /**
     * 
     * editing for if bill is there
     * 
     * @param object $bill
     * @return string
     * @throws Exception
     */
    public static function edit($bill)
    {
        try {
            if (!$bill) {
                throw new Exception('Sorry! We can not find your request');
            }
            return self::editPopup($bill);
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     * Updating the billing entry
     * 
     * @param int $id
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'note' => 'required',
                //'hour' => 'required|regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/',
        ]);
        try {
            $bills = new Bill();
            $bill  = $bills->find($id);
            if (!$bill) {
                throw new Exception('Sorry! We can not find your request');
            }
            $bill->fill($request->input())->save();
            return redirect()->back()->with('success', 'Updated');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * 
     *  get bill edit form
     * 
     * @param object $bill
     * @return string
     */
    public static function editPopup($bill)
    {
        $agents = \App\User::where('role', '!=', 'user')->pluck('user_name', 'id')->toArray();
        return '<a href="#bill-edit" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#bill-edit' . $bill->id . '"><i class="fa fa-edit ">&nbsp;&nbsp;</i>Edit</a>
<div class="modal fade" id="bill-edit' . $bill->id . '">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit</h4>
                ' . \Form::model($bill, ["url" => "bill/" . $bill->id, "method" => "patch"]) . '
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                      
                <label for="agents">Agents</label> 
                    <select class="form-control assign-agent" id="bindAgent'. $bill->id . '" name="agent" style="width:100%;" multiple required>
                </select>

                    </div>
                    <div class="col-md-12">
                        ' . \Form::label("hours", "Hours") ."<span>&nbsp;  <i>(use ':' to seperate hours and minutes)</i></span>".
                \Form::text("hours", null, ["class" => "form-control", "placeholder" => "HH:MM","placeholder" => "HH:MM","required"=>"required","pattern"=>"^([0-9]|[0-9][0-9]|[0-9][0-9][0-9]).[0-5][0-9]$","title"=>"Only accept HH:MM format"]) . '
                    </div>
                    <div class="col-md-12">
                        ' . \Form::label("amount_hourly", "Amount Hourly") .
                \Form::text("amount_hourly", null, ["class" => "form-control numberOnly"]) . '
                    </div>
                    <div class="col-md-12">
                        ' . \Form::label("note", "Billing Note") .
                \Form::textarea("note", null, ["class" => "form-control","required"=>"required"]) . '
                    </div>
                    <div class="col-md-12">
                        
                        ' . \Form::label("billable", "Billable") . '</br>
                            
                       
                            ' . \Form::radio("billable", 1) . ' Yes  &nbsp; &nbsp; &nbsp;
                        
                            ' . \Form::radio("billable", 0) . ' No
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-primary pull-left" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>Close</button>
                 <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-refresh" aria-hidden="true">&nbsp;&nbsp;</i>Update</button>'.
                \Form::close() . '
            </div>
        </div>
    </div>
</div>
<script>

$(".numberOnly").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                 // Allow: Ctrl/cmd+A
                (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl/cmd+C
                (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl/cmd+X
                (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        
</script>
';
    }
    /**
     * 
     * Billing while merging the code
     * 
     * @param array $event
     */
    public function merge($event)
    {
        try {
            $parent = $event['parent'];
            $child  = $event['child'];
            Bill::where('ticket_id', '=', $child)
                    ->update(['ticket_id' => $parent]);
        } catch (Exception $ex) {
            loging('bill-merge', $ex->getMessage());
        }
    }
    /**
     * 
     * In time line more drop down event listening
     * 
     * @param object $ticket
     * @return type
     */
    public function moreList($ticket)
    {
        if ($this->isTicketLevel() == true) {
            return $this->renderMoreList($ticket);
        }
    }
    /**
     *  
     * get ticket level form if ticket level is true
     * 
     * @param object $ticket
     * @return type
     */
    public function ticketLevelForm($ticket)
    {
        if ($this->isTicketLevel() == true) {
            return $this->renderTicketLevelForm($ticket);
        }
    }
    /**
     * 
     * check is ticket level setup
     * 
     * @return boolean
     */
    public function isTicketLevel()
    {
        $check  = false;
        $set    = new CommonSettings();
        $schema = $set->getOptionValue('bill', 'level');
        if (is_object($schema) && $schema->option_value == 'ticket' && isBill() == true) {
            $check = true;
        }
        return $check;
    }
    /**
     * 
     * check if thread level setup
     * 
     * @return boolean
     */
    public function isThreadLevel()
    {
        $check  = false;
        $set    = new CommonSettings();
        $schema = $set->getOptionValue('bill', 'level');
        if (is_object($schema) && $schema->option_value == 'thread' && isBill() == true) {
            $check = true;
        }
        return $check;
    }
    /**
     * 
     * render the more drop down popup
     * 
     * @return string
     */
    public function renderMoreList()
    {
        if ($this->isTicketLevel() == true) {
            return '<li data-toggle="modal" data-target="#bill-new"><a href="#"><i class="fa fa-file-text" ></i>Add Bill</a></li>';
        }
    }
    /**
     * 
     * get the ticket level popup
     * 
     * @param object $ticket
     * @return string
     */
    public function renderTicketLevelForm($ticket)
    {
        if ($this->isTicketLevel() == true) {
            $agents = \App\User::where('role', '!=', 'user')->pluck('user_name', 'id')->toArray();
            return '<div class="modal fade" id="bill-new">
            <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Bill</h4>
                ' . \Form::open(["url" => "new-bill", "method" => "post"]) . '
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                  <label for="agents">Agents</label> 
                    <select class="form-control assign-agent"  name="agent" style="width:100%;" multiple required>
                  </select>
                         
                    </div>
                    <div class="col-md-12">
                        ' . \Form::label("hours", "Hours") . "<span>&nbsp;  <i>(use ':' to seperate hours and minutes)</i></span>" .
                    \Form::text("hours", null, ["class" => "form-control timepicker", "placeholder" => "HH:MM", "required" => "required", "pattern" => "^([0-9]|0[0-9]|1[0-9]|2[0-9]|0[0-9][0-9]|1[0-9][0-9]|2[0-9][0-9]):[0-5][0-9]$", "title" => "Only accept HH:MM format"]) . '
                    </div>
                    <div class="col-md-12">
                        ' . \Form::label("amount_hourly", "Amount Hourly") .
                    \Form::text("amount_hourly", null, ["class" => "form-control numberOnly", "required" => "required"]) .
                    \Form::hidden("ticket_id", $ticket->id) . '
                    </div>
                    <div class="col-md-12">
                        ' . \Form::label("note", "Billing Note") .
                    \Form::textarea("note", null, ["class" => "form-control", "required"]) . '
                    </div>
                    <div class="col-md-12">
                        
                        ' . \Form::label("billable", "Billable") . '</br>
                            
                       
                            <input name="billable" type="radio" class="not-apply" value="1" required> Yes  &nbsp; &nbsp; &nbsp;
                        
                            <input name="billable" type="radio" class="not-apply" value="0">No
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                ' . \Form::submit("Save", ["class" => "btn btn-default"]) .
                    \Form::close() . '
            </div>
        </div>
    </div>
</div>
<script>
$(".numberOnly").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                 // Allow: Ctrl/cmd+A
                (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl/cmd+C
                (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl/cmd+X
                (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
</script>';
        }
    }
    /**
     * 
     * Ticket level request saving
     * 
     * @param Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'note'  => 'required',
            'hours' => ['required', 'regex:/^([0-9]|0[0-9]|1[0-9]|2[0-9]|0[0-9][0-9]|1[0-9][0-9]|2[0-9][0-9]):[0-5][0-9]$/'],
        ]);
        try {
            $level = $this->findLevel();
            if ($level) {
                $ticketid = $request->input('ticket_id');
                $agentid  = $request->input('agent');
                $billable = $request->input('billable');
                $modelid  = $request->input('ticket_id');
                $note     = $request->input('note');
                $amount   = $request->input('amount_hourly');
                $hours    = $request->input('hours');
                $this->saveBill($level, $modelid, $hours, $billable, $agentid, $ticketid, $note, $amount);
            }
            return redirect()->back()->with('success', 'Saved');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    public function getPrice($ticket_id)
    {
        $ticket        = \App\Model\helpdesk\Ticket\Tickets::whereHas('types.bill')
                ->whereId($ticket_id)
                ->first()
        ;
        $minutes_spent = $this->ticketController()->ticketOpenTime($ticket_id);
        $price         = ($ticket->types && $ticket->types->bill) ? $ticket->types->bill->price
                    : 0;
        $cost          = round($minutes_spent / 60, 2) * $price;
        $minutes_hours = convertToHours($minutes_spent);
        return ['time' => $minutes_hours, 'cost' => $cost];
    }
    public function ticketController()
    {
        return new \App\Http\Controllers\Agent\helpdesk\TicketController();
    }
    public function getLevel()
    {
        $level = \DB::table('common_settings')->where('option_name', 'bill')
                ->where('optional_field', 'level')
                ->value('option_value');
        return $level;
    }
    public function sendInvoice($ticket_id)
    {
        try {
            $level     = $this->getLevel();
            $ticket    = \App\Model\helpdesk\Ticket\Tickets::find($ticket_id);
            $bill_type = \App\Bill\Models\BillType::where('type', $ticket->type)->first();
            if ($level === 'type' && $bill_type) {
                $price      = $this->getPrice($ticket_id);
                $total_time = checkArray('time', $price);
                $cost       = checkArray('cost', $price);
            }
            elseIf ($level !== 'type') {
                $bil                  = new Bill();
                $billable_hours_array = $bil->where('ticket_id', $ticket_id)->where('billable', 1)->pluck('amount_hourly', 'hours')->toArray();
                $billable             = $this->getBillableHours($billable_hours_array);
                $total_time           = $billable['time'];
                $cost                 = $billable['amount'];
            }
            else {
                throw new \Exception('no data to send');
            }
            $this->send($cost, $total_time, $ticket);
            return redirect()->back()->with('success', 'Sent invoice successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }
    public function send($cost, $total_time, $ticket)
    {
        $client       = $ticket->user()->first();
        $client_name  = $client->first_name . " " . $client->last_name;
        $client_email = $client->email;
        $currency     = self::currency();
        $billing_date = \Carbon\Carbon::now()->timezone(timezone());
        $from         = $this->mailController()->mailfrom('1', $ticket->dept_id);
        $message      = ['subject'  => 'Invoice ' . '[#' . $ticket->ticket_number . ']',
            'scenario' => 'invoice'];
        $variable     = [
            'ticket_subject' => title($ticket->id),
            'ticket_number'  => $ticket->ticket_number,
            'ticket_link'    => faveoUrl('/thread/' . $ticket->id),
            'cost'           => $cost,
            'total_time'     => $total_time,
            'client_name'    => $client_name,
            'client_email'   => $client_email,
            'bill_date'      => $billing_date,
            'currency'       => $currency,
        ];
        $to           = [
            'email' => $client_email,
            'name'  => $client_name,
        ];
        $this->mailController()->sendmail($from, $to, $message, $variable);
    }
    public function mailController()
    {
        return new \App\Http\Controllers\Common\PhpMailController();
    }

    public function billInfo()
    {
      $bill      = new Bill();
      $bill_info = $bill->select('id','agent as agent_id','ticket_id','level','hours','billable','amount_hourly','note')->get();
      return $bill_info;
    }


}
