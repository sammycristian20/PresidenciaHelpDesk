<?php

namespace App\Bill\Controllers;

use App\Facades\Attach;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Bill\Models\Package;
use App\Bill\Models\Order;
use App\Bill\Models\Invoice;
use App\Bill\Requests\PackageRequest;
use App\Model\helpdesk\Settings\Company;
use Crypt;
use Illuminate\Http\UploadedFile;
use Lang;
use Input;

/**
 * Setting for the bill Package
 * 
 * @abstract Controller
 * @author Ladybird Web Solution <arindam.jana@ladybirdweb.com>
 * @name PackageController
 * 
 */
class PackageController extends Controller {

    public function __construct()
    {
        $this->middleware('auth')->except('getActivePackage', 'getPackageInfo');
        // $this->middleware('roles.admin');
    }

    /**
     * 
     * @return type
     */
    public function inbox()
    {
        return view('Bill::package.index');
    }

    /**
     * 
     * @param Request $request
     * @return type json
     */
    public function getData(Request $request)
    {
        try {
            $pagination = ($request->input('limit')) ? $request->input('limit') : 10;
            $sortBy = ($request->input('sort-field')) ? $request->input('sort-field') : 'id';
            $search = $request->input('search-query');
            $orderBy = ($request->input('sort-order')) ? $request->input('sort-order') : 'asc';
            $baseQuery = Package::select('id', 'name', 'status', 'price', 'validity', 'allowed_tickets')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')->orWhere('status', 'LIKE', '%' . $search . '%')->orWhere('price', 'LIKE', '%' . $search . '%')->orWhere('validity', 'LIKE', '%' . $search . '%')
                        ->orWhere('allowed_tickets', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);
            return successResponse($searchQuery);
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * 
     * @return type view
     */
    public function create()
    {
        return view('Bill::package.create');
    }

    /**
     * 
     * @return type view
     */
    public function edit()
    {
        return view('Bill::package.edit');
    }

    /**
     * this methode store package
     * @param BusinessRequest $request
     * @return type string
     */
    public function store(PackageRequest $request)
    {

        try {
            $packageId = ($request->id) ? $request->id : null;

            if ($packageId) {
                return $this->update($packageId, $request);
            }
            $validity = ($request->validity == 'one_time') ? null : $request->validity;
            Package::create(['name' => $request->name, 'status' => $request->status, 'price' => $request->price, 'validity' => $validity, 'allowed_tickets' => $request->allowed_tickets, 'display_order' => $request->display_order, 'description' => $request->description]);
            $packageId = Package::orderBy('id', 'desc')->value('id');

            if (Input::file('package_pic')) {

                Package::where('id', $packageId)->update(['package_pic' => $this->addPackagePicture(Input::file('package_pic'))]);
            }
            if ($request->kb_link) {
                Package::where('id', $packageId)->update(['kb_link' => $request->kb_link]);
            }
            $this->updatePackageDepartment(Package::find($packageId), array_filter(explode(',', $request->input('departments', ''))));
            /* redirect to Index page with Success Message */
            return successResponse(Lang::get('Bill::lang.package_saved_successfully'));
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * 
     * @param string $packageId  Package
     * @return type json
     */
    public function editApi($packageId)
    {
        try {
            $packageInfo = Package::where('id', $packageId)->first();
            $department = [];
            foreach($packageInfo->packageDepartment()->get() as $packageDept){
                array_push($department, ['id' =>$packageDept->department->id, 'name' =>$packageDept->department->name]);
            }
            $packageInfo = $packageInfo->toArray();
            //Faveo ke purvajon Bhagwaan iskliye tumhe kabhi maaf nhi krega
            $packageInfo['package_pic'] = ($packageInfo['package_pic'])?['name' => $packageInfo['package_pic']]:'';
            $packageInfo['departments'] = $department;
            $packageInfo['validity'] = ($packageInfo['validity'] == "") ? 'one_time' : $packageInfo['validity'];
            return successResponse('', $packageInfo);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }


    /**
     * Adds package pics
     * @param UploadedFile $file
     * @return mixed
     */
    private function addPackagePicture(UploadedFile $file)
    {
        $path = Attach::put('package_attachments', $file, null, null, true, 'public');

        return Attach::getUrlForPath($path, null, 'public');
    }

    /**
     * This method Update Package
     *
     * @param type int       $packageId
     * @param type SlaUpdate $request
     * @return type Response string
     */
    public function update($packageId, Request $request)
    {
        try {
            $validity = ($request->validity == 'one_time') ? null : $request->validity;
            Package::where('id', $packageId)->update(['name' => $request->name, 'status' => $request->status, 'price' => $request->price, 'validity' => $validity, 'allowed_tickets' => $request->allowed_tickets, 'display_order' => $request->display_order, 'description' => $request->description]);

            if (Input::file('package_pic')) {
                $packagePic = $this->addPackagePicture(Input::file('package_pic'));
                Package::where('id', $packageId)->update(['package_pic' => $this->addPackagePicture(Input::file('package_pic'))]);
            } elseif(!$request->package_pic) {
                //Faveo ke purvajon Bhagwaan iskliye tumhe kabhi maaf nhi krega
                Package::where('id', $packageId)->update(['package_pic' => null]);
            }

            if ($request->kb_link) {
                Package::where('id', $packageId)->update(['kb_link' => $request->kb_link]);
            }
            $this->updatePackageDepartment(Package::find($packageId), array_filter(explode(',', $request->input('departments', ''))));
            /* redirect to Index page with Success Message */
            return successResponse(Lang::get('Bill::lang.package_edit_successfully'));
        } catch (Exception $ex) {
            /* redirect to Index page with Fails Message */
            return errorResponse($ex->getMessage());
        }
    }

    private function updatePackageDepartment(Package $package, Array $departments)
    {
        if(empty($departments)) {
            $package->packageDepartment()->delete();
            return true;
        }
        $package->packageDepartment()->whereNotIn('department_id', $departments)->delete();
        foreach ($departments as $value) {
            $package->packageDepartment()->updateOrCreate(['department_id' => $value],['department_id' => $value]);
        }
    }

    /**
     * This method deleting Package  
     * @param type $packageId of  business_hours
     * @return type  json
     */
    public function delete(Request $request)
    {
        try {
            $checkOrder = Order::whereIn('package_id', explode(",", $request->package_ids))->count();
            if ($checkOrder) {
                return errorResponse(Lang::get('Bill::lang.this-package-already-purchased-by-some-user'));
            }
            Package::whereIn('id', explode(",", $request->package_ids))->delete();
            return successResponse(Lang::get('Bill::lang.package_delete_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * 
     * @param Request $request
     * @return type json
     */
    public function getActivePackage(Request $request)
    {
        try {

            $pagination = ($request->input('limit')) ? $request->input('limit') : 10;
            $sortBy = ($request->input('sort-field')) ? $request->input('sort-field') : 'id';
            $search = $request->input('search-query');
            $orderBy = $request->input('sort-order', 'desc');

            $baseQuery = Package::where('status', 1)->select('id', 'name', 'status', 'price', 'validity', 'allowed_tickets', 'package_pic', 'display_order')->orderBy($sortBy, $orderBy);

            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')->orWhere('status', 'LIKE', '%' . $search . '%')->orWhere('price', 'LIKE', '%' . $search . '%')->orWhere('validity', 'LIKE', '%' . $search . '%')->orWhere('allowed_tickets', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);
            $customPath = collect(['imageurl' => url('/uploads/packages')]);


            $data = $customPath->merge($searchQuery);
            return successResponse('', $data);
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * this method return user with billing info count
     * @param type $userId
     * @return type array
     */
    public function getCountInfo($userId)
    {
        try {
            //active package
            $activePackage = Package::where('status', 1)->count();
            //user packages
            $packageId = Order::where('user_id', $userId)->where('status', 1)->count();
            //user order invoice
            $userInvoice = Invoice::whereHas('order', function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })->count();

            $arrayCount = (['activepackage' => $activePackage, 'userpackage' => $packageId, 'invoice' => $userInvoice]);

            return successResponse('', $arrayCount);
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * 
     * @param Request $request
     * @return type json
     */
    public function getUserPackage(Request $request)
    {
        try {
            $userId = \Auth::user()->id;
            if (\Auth::user()->role != 'user' && \Auth::user()->id != $request->user_id && $request->user_id) {
                $userId = $request->user_id;
            }
            $pagination = $request->input('limit') ? $request->input('limit') : 10;
            $sortBy = $request->input('sort-field', 'id');
            $search = $request->input('search-query');
            $orderBy = $request->input('sort-order', 'desc');
            $baseQuery = Order::where('user_id', $userId)->with([
                        'package' => function ($q) {
                            return $q->select('id', 'name', 'validity');
                        },
                        'user' => function ($q) {
                            return $q->select('id', 'email', 'first_name', 'last_name');
                        },
                        'invoice' => function ($q) {
                            return $q->select('id', 'id as name', 'total_amount', 'tax', 'discount', 'payable_amount', 'due_by', 'order_id', 'amount_paid', 'paid_date');
                        },
                    ])->where('status', 1)->select('id', 'id as orderId', 'package_id', 'user_id', 'credit_type', 'credit', 'status', 'expiry_date')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('package_id', 'LIKE', '%' . $search . '%')->orWhere('user_id', 'LIKE', '%' . $search . '%')->orWhere('credit_type', 'LIKE', '%' . $search . '%')->orWhere('status', 'LIKE', '%' . $search . '%')->orWhere('expiry_date', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);
            return successResponse('', $searchQuery);
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * this method return package information
     * @param type $packageId
     * @return type json
     */
    public function getPackageInfo($packageId)
    {
        try {
            $packageIdCheck = Package::where([['id', '=', $packageId],['status', '=', 1]])->count();
            if (!$packageIdCheck) {
                return errorResponse(Lang::get('Bill::lang.package_not_found'));
            }

            $packageInfo = Package::where('id', $packageId)->select('id', 'name', 'description', 'status', 'price', 'validity', 'allowed_tickets', 'package_pic', 'kb_link','updated_at')->first()->toArray();
            $packageInfo['package_pic'] = (['name' => basename($packageInfo['package_pic']), 'path' => $packageInfo['package_pic']]);


            return successResponse('', $packageInfo);
        } catch (Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * this method return order information
     * @param type $orderId
     * @return type json
     */
    public function getOrderInfo($orderId, Request $request)
    {
        try {

            if (\Auth::user()->role == 'user') {
                $userId = \Auth::user()->id;
                $ordercheck = Order::where('id', $orderId)->where('user_id', $userId)->count();
                if (!$ordercheck) {
                    return errorResponse(Lang::get('Bill::lang.order_not_found'));
                }
            }

            $orderIdCheck = Order::where('id', $orderId)->count();

            if (!$orderIdCheck) {
                return errorResponse(Lang::get('Bill::lang.order_not_found'));
            }
            $orderInfo = Order::where('id', $orderId)
                            ->with([
                                'package' => function ($q) {
                                    return $q->select('id', 'name', 'validity');
                                }, 'orderTickets' => function ($q) {
                                    return $q->with(['ticket' => function($q) {
                                                    return $q->with(['firstThread' => function($thread) {
                                                                    return $thread->select('id', 'title', 'ticket_id');
                                                                }])->select('id', 'ticket_number', 'updated_at',  'created_at');
                                                }])->select('id', 'order_id', 'ticket_id');
                                }])->select('id', 'package_id', 'user_id', 'credit_type', 'credit', 'status', 'expiry_date')->first()->toArray();
            return successResponse('', $orderInfo);
        } catch (Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    public function checkOut(Request $request)
    {

        try {

             $meta = $request->meta ? $request->meta : false;

            $dueBy = (commonSettings('bill', 'invoice-due')) ? commonSettings('bill', 'invoice-due') : 2;
            $startDate = time();
            $InvoiceDueDate = date('Y-m-d H:i:s', strtotime('+' . $dueBy . ' day', $startDate));

            //get package info
            $packageInfo = Package::where('id', $request->package_id)->select('id', 'name', 'description', 'status', 'price', 'validity', 'allowed_tickets')->first();
            //get order info
            $orderInfo = Order::create(['package_id' => $request->package_id, 'user_id' => $request->user_id, 'credit_type' => 'tickets', 'credit' => $packageInfo->allowed_tickets]);

            //get invoice info
            $invoiceInfo = Invoice::create(['order_id' => $orderInfo->id, 'total_amount' => $packageInfo->price, 'tax' => 0, 'discount' => 0, 'payable_amount' => $packageInfo->price, 'due_by' => $InvoiceDueDate]);

            if ($packageInfo->price == 0) {
                (new \App\Bill\Controllers\TransactionController())->updateOrder($invoiceInfo);
            }


            if($meta){
                (new \App\Bill\Controllers\InvoiceController())->sendInvoice($invoiceInfo->id);
                 return successResponse(Lang::get('Bill::lang.invoice_create_successfully'));
 
            }

           $infoInvoice = $invoiceInfo->where('id', $invoiceInfo->id)->select('id', 'total_amount', 'tax', 'discount', 'payable_amount', 'due_by', 'order_id', 'created_at','amount_paid')->first()->toArray();

            $orderInfo = $orderInfo->where('id', $orderInfo->id)->select('id', 'package_id', 'user_id', 'credit_type', 'credit', 'status', 'expiry_date')->first()->toArray();
            $orderInfo['package'] = $packageInfo->where('id', $request->package_id)->select('id', 'name', 'description','validity','price')->first()->toArray();
            $infoInvoice['order'] = $orderInfo;

            $infoInvoice['transaction_id'] = Crypt::encrypt($infoInvoice['id']);

            $companyInfo = Company::first();
            $infoInvoice['company_name'] = $companyInfo->company_name;
            $infoInvoice['from'] = (['name' => $companyInfo->company_name, 'website' => $companyInfo->website, 'phone' => $companyInfo->phone, 'address' => $companyInfo->address]);
            return successResponse('', $infoInvoice);
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    public function viewOrderInfo($orderId)
    {
        return view('Bill::orders.order');
    }
}
