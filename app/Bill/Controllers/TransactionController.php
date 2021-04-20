<?php

namespace App\Bill\Controllers;

use App\Bill\Models\Invoice;
use App\Bill\Models\Order;
use App\Bill\Models\Package;
use App\Bill\Models\Transactions;
use App\Bill\Requests\PaymentRequest;
use Illuminate\Http\Request;
use App\Bill\Controllers\PackageController;
use App\Bill\Controllers\SendMailsController;
use App\Bill\Jobs\SendMailWithInvoiceJob;
use Logger;
/**
 * 
 */
class TransactionController extends SendMailsController
{
	public function addPayment(PaymentRequest $request)
	{
		try {
			$invoiceId     = $request->get('invoice');
			$method        = $request->get('method');
			$transactionId = $request->get('transactionId');
			$amount        = $request->get('amount');
            $comment       = $request->get('comment');
			$res = $this->updateTransaction($invoiceId, $transactionId, $method, $amount, 1, 'marked_paid_by_agent', true, $comment);
			if($res === 0) {
				return errorResponse(trans('Bill::lang.transaction_amount_is_greater_than_payable_amount'));
			}
			return successResponse(trans('Bill::lang.payment_added_successfully'));

		} catch(\Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	protected function updateTransaction($invoiceId, $transactionId, $method, $amount, $status=1, $gateway='marked_paid_by_agent', $updateTransaction=true, $comment=null)
	{
		$invoice = Invoice::where('id', $invoiceId)->first();
		if($invoice->payable_amount - $invoice->amount_paid < $amount) return 0;
		if($gateway == 'online' || in_array($invoice->payment_mode, [null,'agent_marked_unpaid'])) {
			$invoice->update(['payment_mode' => $gateway]); 
		}
		if(!$updateTransaction) {
            $short = (new \App\Model\MailJob\QueueService())->where('status', 1)->first()->short_name;
                app('queue')->setDefaultDriver($short);
            SendMailWithInvoiceJob::dispatch('payment-approval-alert', [], 'payment-approval', $invoiceId, 'Payment approval requested', []);
            return true;
        }
		Transactions::updateOrCreate(['transactionId' => $transactionId],[
			'transactionId' => $transactionId, 'payment_method'=> $method,
        	'amount_paid'   => $amount, 'status'        => $status, 'invoice_id' => $invoiceId,
        	'user_id' => \Auth::user()->id, 'comment' => $comment
		]);
		if($status) {
			$oldAmountPaidInvoice = $invoice->amount_paid;
			$invoice->update([
				'paid_date' => now(), 'amount_paid' => $amount+$invoice->amount_paid
			]);
			if($amount+$oldAmountPaidInvoice >= $invoice->payable_amount) {
				$this->updateOrder($invoice);
			}
		}
	}

	public function updateOrder(Invoice $invoice, $status = 1)
	{
		$order = $invoice->order()->first();
    	$data['status'] = $status;
    	if($order->package->validity){
    		$data['expiry_date'] = $this->calculateExprityDate($order->package->validity);
    	}
    	$order->update($data);
    	if(!$status) return true;
    	
    	$this->sendPurchaseConfirmations($order);

    	return true;
	}

	private function calculateExprityDate($validity)
	{
		$time = strtotime("now");
		switch ($validity) {
			case 'monthly':
			$time = strtotime("+1 month");
			break;

			case 'quarterly':
			$time = strtotime("+4 month");
			break;

			case 'semi_annually':
			$time = strtotime("+6 month");
			break;

			default :
			$time = strtotime("+1 year");
			break;
		}

		return date('Y-m-d H:i:s', $time);
	}

    private function sendPurchaseConfirmations($order)
    {
    	try {
	    	$client = $order->user;
    		if($client && $client->email) {
                $short = (new \App\Model\MailJob\QueueService())->where('status', 1)->first()->short_name;
                app('queue')->setDefaultDriver($short);
            	SendMailWithInvoiceJob::dispatch('purchase-confirmation-alert', [], 'purchase-confirmation', $order->invoice->id, 'Order successfull', [], false, $client->id);
    		}
    	} catch (\Exception $e) {
    		Logger::exception($e);
    	}
    }

    public function updateInvoice(Request $request)
    {
    	try {
	    	$invoice = Invoice::findOrFail($request->input('invoice_id', 0));
    		$amountPaid = $request->input('amount_paid', $invoice->amount_paid);
    		$payableAmount = $request->input('payable_amount', $invoice->payable_amount);
    		$invoice->update([
    			'amount_paid' => $amountPaid,
    			'payable_amount' => $payableAmount
    		]);
    		if($amountPaid == 0) {
    			$invoice->transactions()->delete();
    			$invoice->update(['paid_date' => null, 'payment_mode' => 'agent_marked_unpaid']);
    		}
    		$this->updateOrder($invoice, $amountPaid == 0 ? 0 : $amountPaid > $payableAmount);
    		return successResponse(trans('Bill::lang.invoice_updated_successfully'));
    	} catch(\Exception $e) {

    		return errorResponse($e->getMessage());
    	}
    }
}
