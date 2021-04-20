<?php

namespace App\Bill\Controllers;

use App\Http\Controllers\Controller;
use App\Bill\OmnipayHelper;
use App\Bill\Models\PaymentGateway;
use Illuminate\Http\Request;
use App\Bill\Models\Invoice;
use Crypt;
use App\Bill\Requests\GatewaySettingRequest;
use App\Bill\Controllers\PackageController;

/**
 * 
 */
class PaymentController extends TransactionController
{
	
	public function getGateWaysList(Request $request)
	{
		try {
            $search = $request->input('search-query');
			$baseQuery = $this->getBaseQuery()
				->when($search, function($query) use ($search) {
					$query->where('name', 'LIKE', "%$search%")->orWhere('gateway_name', 'LIKE', "%$search%");
				});
           	if($request->get('active')) {
           		$baseQuery = $baseQuery->where('status', 1);
           	}

			return successResponse('', $baseQuery->distinct()->orderBy($request->input('sort-field', 'is_default'), $request->input('sort-order', 'desc'))->paginate($request->input('limit', 10)));
		} catch (\Exception $e) {

			return errorResponse($e->getMessage());
		}
	}

	private function getBaseQuery($where =[], $select =[])
	{
		return PaymentGateway::select(array_merge(['name', 'gateway_name', 'status', 'is_default'], $select))
			->when(!empty($where), function($query) use ($where){
				$query->where($where);
			});
	}

	public function showGateWays()
	{
		return view("Bill::payment.index");
	}

	public function gatewayDetails($name, $gatewayName =null)
	{
		try {
			$data = [];
			$extra = $this->getBaseQuery([['name', '=', $name], ['gateway_name', '=', $gatewayName]], ['value', 'key'])->pluck('value', 'key')->toArray();
			if(!empty($extra)) {
				$data = $this->getBaseQuery([['name', '=', $name], ['gateway_name', '=', $gatewayName]])->first()->toArray();
				$data['extra'] = [];
				foreach ($extra as $key => $value) {
					if(!empty($key)) array_push($data['extra'], ['name' =>$key, 'value' =>$value]);
				}
			}

			return successResponse('', $data);
		} catch (\Exception $e) {

			return errorResponse($e->getMessage());
		}
	}

	public function updateGateway(GatewaySettingRequest $request)
	{
		$status      = $request->get('status');
		$default     = $request->get('is_default');
		$name        = $request->get('name');
		$gatewayName = $request->get('gateway_name');
		$extra       = $request->input('extra', []);
		$query       = $this->getBaseQuery([['name', '=', $name], ['gateway_name', '=', $gatewayName]], ['id']);
		if ($query->count() > 0) {
			$rowIds = $this->getBaseQuery([['name', '=', $name]], ['id'])->pluck('id')->toArray();
			if($status) {
				$this->toggleStatusAndDefault($rowIds, ['status' => 0], true);
			}
			$query->update(['status' => $status, 'is_default' => $default]);
			$rowIds2 = $query->pluck('id')->toArray();
			if($default) {
				$this->toggleStatusAndDefault($rowIds2, ['is_default' => 0]);
			}
			foreach($extra as $key => $value) {
				$q = clone $query;
				$q->where('key', $key)->update(['value' => $value]);
			}

			return successResponse(trans('lang.updated'));
		}

		return errorResponse(trans('lang.not_found'));
	}

	private function toggleStatusAndDefault($ids, $updateValye, $in = false)
	{
		$in ? PaymentGateway::whereIn('id',$ids)->update($updateValye) : PaymentGateway::whereNotIn('id',$ids)->update($updateValye);
	}

	public function checkout($gateway, $invoiceId)
	{
		try {
			$invoice = Invoice::findOrFail(Crypt::decrypt($invoiceId));
			if (\Auth::user()->id != $invoice->order()->first()->user_id) {
				$message = trans('lang.sorry_you_are_not_allowed_token_expired');
				return redirect('/invoice/'.$invoice->id.'?error='.$message);
			}
			$transactionId = strtoupper(str_random(5)).$invoice->id.strtoupper(str_random(10)).$invoice->order()->first()->user_id;
			if(in_array(strtolower($gateway), ['bank transfer', 'cash'])) {
				$this->updateTransaction($invoice->id, $transactionId, 'online', $invoice->payable_amount, 0, str_replace(' ', '_', strtolower($gateway)), false);
				return redirect('/billing-user-packages/'.'?success='.trans('Bill::lang.you_have_selected_offline_payment_method'));
			}
			$paypal = new OmnipayHelper;
			$response = $paypal->purchase($gateway, [
            	'amount' => $paypal->formatAmount($invoice->payable_amount),
            	'transactionId' => $transactionId,
            	'currency' => commonSettings('bill', 'currency') ? commonSettings('bill', 'currency') : 'USD',
            	'cancelUrl' => $paypal->getCancelUrl($gateway, $invoice->id),
            	'returnUrl' => $paypal->getReturnUrl($gateway, $invoice->id),
        	]);

        	if ($response->isRedirect()) {
        		$this->updateTransaction($invoice->id, $transactionId, 'online', $invoice->payable_amount, 0, 'online');
            	$response->redirect();
        	}
        	
        	$message = $response->getMessage();
        	return redirect('/invoice/'.$invoice->id.'?error='.$message);
		} catch (\Exception $e) {

			return redirect('/invoice/'.$invoice->id.'?error='.$e->getMessage());
		}
	}

	public function completed($gateway, $invoiceId)
	{
		$invoice = Invoice::findOrFail($invoiceId);
		$paypal = new OmnipayHelper;
		$transactionId = $invoice->transactions()->where('status', 0)->where('payment_method','online')->get()->last()->transactionId;
        $response = $paypal->complete($gateway, [
            'amount' => $paypal->formatAmount($invoice->payable_amount),
            'transactionId' => $transactionId,
            'currency' => commonSettings('bill', 'currency') ? commonSettings('bill', 'currency') : 'USD',
            'transactionReference' => \Input::get('paymentId'),
            'payerId' => \Input::get('PayerID'),
            'cancelUrl' => $paypal->getCancelUrl($gateway, $invoice->id),
            'returnUrl' => $paypal->getReturnUrl($gateway, $invoice->id),
            'notifyUrl' => $paypal->getNotifyUrl($invoice->id),
        ]);
        if ($response->isSuccessful()) {
        	$this->updateTransaction($invoice->id, $transactionId, 'online', $invoice->payable_amount, 1, 'online');
        	$message = 'Package has been added to your orders.';
            return redirect('/billing-user-packages/'.'?success='.$message);
        }

        return redirect()->back()->with([
            'message' => $response->getMessage(),
        ]);
	}

	/**
     * @param $order_id
     */
    public function cancelled($gateway, $invoiceId)
    {
        $message = 'You have cancelled your recent PayPal payment !';
        return redirect('/invoice/'.$invoiceId.'?error='.$message);
    }
}