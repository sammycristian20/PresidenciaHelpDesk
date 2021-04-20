<?php

namespace App\Bill\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use App\Bill\Models\Package;
use App\Bill\Models\Order;
use Lang;
use Auth;
use App\User;
/**
 * 
 */
class TicketEventHandler extends Controller
{
	protected $user;

    /**
     * @deprecated
     */
	public function renderForm($event, $formType)
	{
        $array = json_decode($event, true);
        $package_order = collect($array)->where('unique', '=', 'package_order')->all();
		if(!$this->isAllowedWithoutPackage() && !$package_order && $formType == 'ticket') {
			$prchase_code_form = [
                'title'                      => 'Select package',
                'type'                       => 'select',
                'api'						 => 'user-packages',
                'agentRequiredFormSubmit'    => false,
                'customerDisplay'            => true,
                'agentDisplay'               => true,
                'customerRequiredFormSubmit' => true,
                'default'                    => 'yes',
                'unique'                     => 'package_order',
                'agentlabel'                 => [['language'=> 'en','label'=>'Select Package','flag'=>asset('lb-faveo/flags/en.png')]],
                'clientlabel'                => [['language'=> 'en','label'=>'Select Package','flag'=>asset('lb-faveo/flags/en.png')]],
            ];
            $new_array         = array_merge($array, [$prchase_code_form]);

           	return $new_array;
		}
	}

    public function renderPackageList($data)
    {
        $packages = [];
        if(!Auth::check() || Auth::user()->role != 'user') {
            $data = Package::where('status', 1)->pluck('name', 'name')->toArray();
            foreach ($data as $key => $value) {
                array_push($packages, ['id' => 'jxkvkxj'.$key, 'name' => $value]);
            }
            return $packages;
        }
        $order = Order::with(['package' => function ($q){
            $q->select('id','name');
        }]);
        $order = $this->getActiveOrders($order, Auth::user()->id)->select('id', 'credit', 'package_id')->get()->toArray();
        foreach ($order as $key => $value) {
           array_push($packages, ['id' => $value['id'], 'name' => $value['package']['name']." (".$value['credit']." credits)"]);
        }

        return $packages;
    }

    public function handleTicketCreateEvent($formdata, $userId, $source, $department)
    {
        if(!$this->isAllowedWithoutPackage()) {
            $user = User::find($userId);
            if(!$user) throw new Exception(trans('Bill::lang.purchase_support_package'), 1);
            if (!in_array($source, [1,3]) && $user->role=="user") {
                //intimate user about ticket creation not allowed from
                loging('info', Lang::get('lang.reject_ticket_exception'));
                throw new \Exception("Error Processing Request", 1);
            }
            if($user->role=="user") {
                $v = \Validator::make($formdata, ['package_order' => 'required']);
                if ($v->fails()) {
                    throw new \Exception(trans('Bill::lang.package_is_required'));
                }

                $orderBuilder = $this->getOrderBuilder($formdata['package_order']);
                $order = $this->getActiveOrders($orderBuilder, $user->id)->first();
                if(!$order) {
                    throw new Exception(trans('Bill::lang.order_not_active'), 1);   
                }
                $packageDepartments = $order->package->packageDepartment()->get()->pluck('department_id')->toArray();
                if(!empty($packageDepartments) && !in_array($department, $packageDepartments)) {
                    throw new Exception(trans('Bill::lang.package_department_not_matched_error'), 1);
                }
            }
        }
    }

    private function getActiveOrders($order, $userId = null)
    {
        return  $order->where([
                    ['status' , '=', 1], ['credit', '>', 0]
                ])->where(function($query){
                    $query->where('expiry_date', '>', now())->orWhere('expiry_date', '=', null);
                })->when($userId, function($query) use($userId){
                    $query->where('user_id', $userId);
                });
    }

    public function handleTicketAfterCreateEvent($data)
    {
        if(!array_key_exists('package_order', $data['form_data'])) return true;
        $order = $this->getOrderBuilder($data['form_data']['package_order']);
        $order = $this->getActiveOrders($order, $data['ticket']->user_id)->first();
        if(!$order) return true;
        $order->credit = $order->credit-1;
        $order->save();
        $order->orderTickets()->create([
            'ticket_id' => $data['ticket']->id,
            'user_id'   => $data['ticket']->user_id
        ]);
        \App\Model\helpdesk\Ticket\Ticket_Form_Data::where('key', 'package_order')->where('ticket_id', $data['ticket']->id)->delete();
    }

    private function getOrderBuilder($argument)
    {
        $order = $argument;
        $package = null;
        if(strpos($order, 'jxkvkxj') !== false) {
            $package = Package::where('name', str_replace('jxkvkxj', '', $order))->first()->id;
            $order = null;
        }
        return  Order::when($order,function($query) use($order){
                    $query->where('id', $order);
                })->when($package,function($query) use($package){
                    $query->where('package_id', $package);
                });
    }

    /**
     * Function checks system settings for ticket creation without
     * activepackage
     *
     * @return bool  true if allowed, false otherwise
     */
    private function isAllowedWithoutPackage():bool
    {
        //when "allowWithoutPackage" is not seeded in database then we consider allowWithoutPackage
        // as true
        return (commonSettings('bill', 'allowWithoutPackage') === null)? true : (bool)commonSettings('bill', 'allowWithoutPackage');
    }
}