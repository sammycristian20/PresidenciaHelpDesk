<?php

namespace App\Bill\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Bill\Models\Invoice;
use App\Model\helpdesk\Settings\Company;
use Crypt;
use Lang;
use PDF;
use App\Bill\Controllers\SendMailsController;
use App\Bill\Jobs\SendMailWithInvoiceJob;
use Logger;

/**
 * 
 */
class InvoiceController extends SendMailsController
{	
	/**
	 * Returns Invoice list view for client panel
	 */
	public function viewUserInvoice()
    {
        return view('Bill::userinvoice');
    }

    /**
     * Returns Invoice list view for agents
     */
    public function showInvoiceList()
    {
        return view('Bill::invoicelist');
    }

    /**
     * converts timezone from logged in user timezone into UTC
     * @return string
     */
    private function getTimeInUTC($time)
    {
        if(!$time){
            return null;
        }

        //covert given timestamp from agent timezone to UTC
        $agentTimeZone = agentTimeZone();

       //end time in UTC
       return changeTimezoneForDatetime($time, $agentTimeZone, 'UTC');
    }

    /**
     * Function to apply filters in invoices list data. Function takes base query as parameter 
     * and update the base query for applying filters requested in API call
     * @param Request $request
     * @param QueryBuilder $basequery
     * @return null
     */
    private function applyFilters(Request $request, &$baseQuery)
    {
    	$this->appendFilterQueryWithAndLogic($baseQuery, 'payable_amount', $request->input('payable_amount', null));
    	$this->appendFilterQueryWithAndLogic($baseQuery, 'amount_paid', $request->input('amount_paid', null));
    	$this->appendFilterQueryWithAndLogic($baseQuery, 'invoices.created_at', $this->getTimeInUTC($request->input('created_at_start', null)), '>=');
    	$this->appendFilterQueryWithAndLogic($baseQuery, 'invoices.created_at', $this->getTimeInUTC($request->input('created_at_end', null)), '<=');
    	$this->appendFilterQueryWithAndLogic($baseQuery, 'due_by', $this->getTimeInUTC($request->input('due_by_start', null)), '>=');
    	$this->appendFilterQueryWithAndLogic($baseQuery, 'due_by', $this->getTimeInUTC($request->input('due_by_end', null)), '<=');
    	$this->appendFilterQueryForArray($baseQuery, 'payment_mode', $request->input('payment_mode', []));
    	$this->filterByOrder($request->input('users', null), $request->input('status', null), $request->input('all-users', null), $baseQuery);
    }

    /**
   	 * Appends query to parent query
     * @param  QueryBuilder $parentQuery
     * @param  string $comparisionOperator '=','>=','<=','!=' etc
     * @param  string $fieldName           name of the field to be queried with
     * @param  string|null|int $fieldValue          value of the field
     * @return null
     */
    private function appendFilterQueryWithAndLogic(&$parentQuery, $fieldName, $fieldValue, $comparisionOperator = '=')
    {
        if($fieldValue !== null){
            $parentQuery = $parentQuery->where($fieldName, $comparisionOperator, $fieldValue);
        }
    }

  	/**
     * Appends query to parent query
     * @param  QueryBuilder $parentQuery
     * @param  string $fieldName           name of the field to be queried with
     * @param  string|null|int $fieldValue          value of the field
     * @return null
     */
    private function appendFilterQueryForArray(&$parentQuery, $fieldName, $fieldValue)
    {
        if(!empty($fieldValue)){
           $parentQuery = $parentQuery->whereIn($fieldName, $fieldValue);
        }
    }

    /**
     * Add order relation and filter result for order table columns user_id and status
     * @param  Array        $users     Array of user ids or null
     * @param  bool         $status    0 and 1 for unpaid and paid respectively
     * @param  QueryBuilder $basequery
     * @return null
     */
    private function filterByOrder(Array $users = null, bool $status = null, $all = null, &$baseQuery)
    {
    	$users = \Auth::user()->role == 'user' ? [\Auth::user()->id] : $users;
        if(!$all && !$users) {
            $users = [\Auth::user()->id];
        }
    	$baseQuery->whereHas('order', function($q) use ($users, $status){
    		$q->when($users, function($q) use($users){
    			$q->whereIn('user_id', $users);
    		})->when($status !== null, function($q) use($status){
    			$q->where('status', $status);
    		});
    	});
    }

    /**
     * 
     * @param Request $request
     * @return type json
     */
    public function getUserInvoice(Request $request)
    {
        try {
            $meta = $request->meta ? $request->meta : false;
            $sort = $request->input('sort-field', 'created_at');
            
            $search = $request->input('search-query');
            $baseQuery = Invoice::with('order:id,status')->join('orders', 'orders.id', '=', 'invoices.order_id')->select('invoices.id', 'invoices.id as name', 'total_amount', 'due_by', 'invoices.created_at', 'order_id', 'due_by', 'order_id','payment_mode', 'payable_amount', 'amount_paid');
            $this->applyFilters($request, $baseQuery);
            if ($meta) {
                $baseQuery->join('users', 'users.id', '=', 'orders.user_id')->with(['order:id,package_id,user_id,credit_type,credit,status,expiry_date', 'order.user:id,first_name,last_name,email','order.package:id,name'])->addSelect('tax', 'discount', 'payable_amount', 'total_amount');
            }
            if($sort == 'user') {
                $sort = 'users.first_name';
            } elseif($sort == 'status') {
                $sort = 'orders.status';
            }

            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('total_amount', 'LIKE', '%' . $search . '%')
                        ->orWhere('tax', 'LIKE', '%' . $search . '%')
                        ->orWhere('discount', 'LIKE', '%' . $search . '%')
                        ->orWhere('payable_amount', 'LIKE', '%' . $search . '%')
                        ->orWhere('due_by', 'LIKE', '%' . $search . '%')
                        ->orWhere('payment_mode', 'LIKE', '%' . $search . '%');
                    })->orderBy($sort, $request->input('sort-order', 'desc'))->paginate($request->input('limit', 10));

            return successResponse('', $searchQuery);
        } catch (\Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * this method return invoice information
     * @param type $invoiceId
     * @return type json
     */
    public function getInvoiceInfo($invoiceId)
    {
        try {
            $invoiceIdCheck = Invoice::where('id', $invoiceId)->count();
            if (!$invoiceIdCheck) {
                return errorResponse(Lang::get('Bill::lang.invoice_not_found'));
            }

            $companyInfo = Company::first();
            $invoiceInfo = Invoice::where('id', $invoiceId)->with(['order' => function ($q) {
                            $q->with([
                                'package' => function ($q) {
                                    return $q->select('id', 'name', 'description', 'price', 'validity');
                                },
                                'user' => function ($q) {
                                    $q->select('id', 'email', 'first_name', 'last_name', 'profile_pic');
                                },])->select('id', 'id as order_id', 'package_id', 'user_id', 'credit_type', 'credit', 'status', 'expiry_date');
                        }, 'transactions' => function ($q) {
                            return $q->with(['paidBy' => function ($q) {
                                           $q->select('id', 'email', 'first_name', 'last_name', 'profile_pic');
                                },])->select('transactionId', 'invoice_id', 'payment_method', 'amount_paid', 'status', 'created_at','user_id');
                        }
                    ])->select('id', 'id as invoice_id', 'total_amount', 'tax', 'discount', 'payable_amount', 'due_by', 'created_at', 'order_id', 'amount_paid', 'paid_date','payment_mode')->first()->toArray();
            $invoiceInfo['company_name'] = $companyInfo->company_name;

            $invoiceInfo['from'] = (['name' => $companyInfo->company_name, 'phone' => $companyInfo->phone, 'website' => $companyInfo->website, 'address' => $companyInfo->address]);
            $invoiceInfo['transaction_id'] = Crypt::encrypt($invoiceInfo['id']);
            $invoiceInfo['currency'] = commonSettings('bill', 'currency');
            return successResponse('', $invoiceInfo);
        } catch (Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Generates and render PDF of invoice for download
     * @param int  $invoiceId id of Invoice to generate PDF
     * @param bool $render    true if PDF needs to be download
     * @return downloads PDF stream if render is true esle returns PDF stream for further manipulation
     */
    public function downloadPdf($invoiceId, $render = true)
    {
        try{
            $invoiceInfo = json_decode(json_encode($this->getInvoiceInfo($invoiceId)), TRUE)['original']['data'];
            $html = view('Bill::invoicepdf', compact('invoiceInfo'))->render();
            $defaultOptions = PDF::getOptions();
            $defaultOptions->setDpi('120');
            if(!$render) return PDF::setOptions($defaultOptions)->load($html)->output();

            return PDF::setOptions($defaultOptions)->load($html)->filename('invoice')->show();
        } catch(\Exception $e) {

            return errorResponse($e->getMessage());
        }
    }

    /**
     * Function to send Invoice in email to users
     * @param  int $invoiceId   id of the invoice to be sent
     * @return Response
     */
    public function sendInvoice(int $invoiceId)
    {
        try {
            $client = Invoice::find($invoiceId)->order->user;
            if($client && $client->email) {
                $to['email'] = $client->email;
                $to['name']  = $client->full_name;
                $short = (new \App\Model\MailJob\QueueService())->where('status', 1)->first()->short_name;
                app('queue')->setDefaultDriver($short);
                SendMailWithInvoiceJob::dispatch('he',$to, 'send-invoice', $invoiceId, 'Invoice created', [], true);
                return successResponse(trans('Bill::lang.invoice_sent'));
            }

            return errorResponse(trans('lang.not_found'));
        } catch (\Exception $e) {
            Logger::exception($e);

            return errorResponse($e->getMessage());
        }
    }

    /**
     * Function to format Invoice PDF as attachments for emails
     * @param  int $invoiceId  Id of the invoice
     * @return Array
     */
    public function getInvoiceAsAttachment(int $invoiceId) : array
    {
        $pdfString = $this->downloadPdf($invoiceId, false);
        return [
            'mode'      => 'data',
            'file_name' => 'invoice.pdf',
            'mime'      => 'application/pdf',
            'poster'    => 'system',
            'data'      => base64_encode($pdfString),
            'file_path' => ''
        ];
    }

    public function getInvoiceMailVariables(int $invoiceId)
    {
        $invoiceInfo = json_decode(json_encode($this->getInvoiceInfo($invoiceId)), TRUE)['original']['data'];
        $transactionsCount = count($invoiceInfo['transactions']);
        $variables = [
            'invoice_id'              => $invoiceInfo['invoice_id'],
            'invoice_created_at'      => $invoiceInfo['created_at'],
            'invoice_total_amount'    => $invoiceInfo['total_amount'],
            'invioce_payable_amount'  => $invoiceInfo['payable_amount'],
            'invoice_payment_mode'    => trans('Bill::lang.'.$invoiceInfo['payment_mode']),
            'invoice_due_by'          => $invoiceInfo['due_by'],
            'invioce_amount_paid'     => $invoiceInfo['amount_paid'],
            'invoice_paid_date'       => $invoiceInfo['paid_date'],
            'invoice_discount'        => $invoiceInfo['discount'],
            'invoice_tax'             => $invoiceInfo['tax'],
            'package_credit_type'     => $invoiceInfo['order']['credit_type'],
            'package_name'            => $invoiceInfo['order']['package']['name'],
            'package_description'     => $invoiceInfo['order']['package']['description'],
            'package_price'           => $invoiceInfo['order']['package']['price'],
            'package_validity'        => $invoiceInfo['order']['package']['validity'] ? trans('Bill::lang.'.$invoiceInfo['order']['package']['validity']) : null,
            'order_expriy_date'       => $invoiceInfo['order']['expiry_date'],
            'invoice_user_name'       => $invoiceInfo['order']['user']['full_name'],
            'invoice_user_email'      => $invoiceInfo['order']['user']['email'],
            'invoice_link_for_client' => url('/invoice/').$invoiceInfo['invoice_id'],
            'invoice_link_for_agent'  => url("bill/package/").$invoiceInfo['invoice_id']."/user-invoice",
            'order_link_for_client'   => url('billing-user-packages'),
            'order_id'                => $invoiceInfo['order']['id']
        ];
        if($transactionsCount>0) {
            $variables['last_transaction_id']       = $invoiceInfo['transactions'][0]['transactionId'];
            $variables['last_transaction_amount']   = $invoiceInfo['transactions'][0]['amount_paid'];
            $variables['last_transaction_status']   = $invoiceInfo['transactions'][0]['status'] ? trans('lang.successful') : trans('failed');
            $variables['last_transaction_method']   = $invoiceInfo['transactions'][0]['payment_method'];
            $variables['last_transaction_by_name']  = $invoiceInfo['transactions'][0]['paid_by']['full_name'];
            $variables['last_transaction_by_email'] = $invoiceInfo['transactions'][0]['paid_by']['email'];
        }
        return $variables;
    }
}