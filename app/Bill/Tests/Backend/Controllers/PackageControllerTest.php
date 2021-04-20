<?php

namespace App\Bill\Tests\Backend\Controllers;

// use TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DBTestCase;
use App\User;
use Illuminate\Http\Request;
use App\Bill\Models\Package;
use App\Bill\Models\Order;
use App\Bill\Models\Invoice;
use App\Bill\Requests\PackageRequest;
use App\Model\helpdesk\Settings\Company;
use Auth;

class PackageControllerTest extends DBTestCase {

    use DatabaseTransactions;

    private function getParams()
    {


        $data = [
            "name" => 'Demo',
            "status" => 1,
            "description" => 'Test Package',
            "display_order" => 1,
            "price" => 0,
            "validity" => 'quarterly',
            "departments" => '3,1',
            "allowed_tickets" => 100
        ];

        return $data;
    }

//for run migration 
    private function activeBilling()
    {
        $this->call("GET", url("bill"), ['status' => 1]);


        $tables = [
            'packages',
            'orders',
            'invoices',
        ];
        foreach ($tables as $table) {
            \DB::statement('ALTER TABLE ' . $table . ' ENGINE = InnoDB');
        }
        return true;
    }

    /** @group package */
    public function test_openInboxByAdmin()
    {

        $this->getLoggedInUserForWeb('admin');
        $response = $this->call("GET", url("bill/package/inbox"), []);
        $response->assertStatus(200);
    }

    /** @group package */
    public function test_openInboxByAgent()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call("GET", url("bill/package/inbox"), []);
        $response->assertStatus(302);
    }

    /** @group package */
    public function test_openInboxByClient()
    {
        $this->getLoggedInUserForWeb('user');
        $response = $this->call("GET", url("bill/package/inbox"), []);
        $response->assertStatus(302);
    }

    /** @group package */
    public function test_openInboxByguestUser()
    {
        $response = $this->call("GET", url("bill/package/inbox"), []);
        $response->assertStatus(302);
    }

    /** @group package */
    public function test_openInboxDataByAdmin()
    {

        $this->getLoggedInUserForWeb('admin');
        $response = $this->call("GET", url("bill/package/get-inbox-data"), []);
        $response->assertStatus(200);
    }

    /** @group package */
    public function test_openInboxDataByAgent()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call("GET", url("bill/package/get-inbox-data"), []);
        $response->assertStatus(302);
    }

    /** @group package */
    public function test_openInboxDataByClient()
    {
        $this->getLoggedInUserForWeb('user');
        $response = $this->call("GET", url("bill/package/get-inbox-data"), []);
        $response->assertStatus(302);
    }

    /** @group package */
    public function test_openInboxDataByguestUser()
    {
        $response = $this->call("GET", url("bill/package/get-inbox-data"), []);
        $response->assertStatus(302);
    }

    /** @group package */
    public function test_openCreatePageByAdmin()
    {

        $this->getLoggedInUserForWeb('admin');
        $response = $this->call("GET", url("bill/package/create"), []);
        $response->assertStatus(200);
    }

    /** @group package */
    public function test_openCreatePageByAgent()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call("GET", url("bill/package/create"), []);
        $response->assertStatus(302);
    }

    /** @group package */
    public function test_openCreatePageByClient()
    {
        $this->getLoggedInUserForWeb('user');
        $response = $this->call("GET", url("bill/package/create"), []);
        $response->assertStatus(302);
    }

    /** @group package */
    public function test_openCreatePageByguestUser()
    {
        $response = $this->call("GET", url("bill/package/create"), []);
        $response->assertStatus(302);
    }

    /** @group storePackage */
    public function test_storePackageByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call("post", url("bill/package/store-update"), $this->getParams());
        $response->assertStatus(200);
        $this->assertEquals(json_decode($response->getContent())->message, 'Package saved successfully');
    }

    /** @group storePackage */
    public function test_storePackageByAgent()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call("post", url("bill/package/store-update"), $this->getParams());
        $response->assertStatus(302);
    }

    /** @group storePackage */
    public function test_storePackageByClient()
    {
        $this->getLoggedInUserForWeb('user');
        $response = $this->call("post", url("bill/package/store-update"), $this->getParams());
        $response->assertStatus(302);
    }

    /** @group  storePackage */
    public function test_storePackageByguestUser()
    {
        $response = $this->call("post", url("bill/package/store-update"), $this->getParams());
        $response->assertStatus(302);
    }

    /** @group editPackage */
    public function test_openEditPageByAdmin()
    {

        $this->getLoggedInUserForWeb('admin');
        //for create package
        $this->call("post", url("bill/package/store-update"), $this->getParams());
        $packageId = Package::orderBy('id', 'desc')->value('id');

        $response = $this->call("GET", url("bill/package/" . $packageId . "/edit"), []);
        $response->assertStatus(200);
    }

    /** @group editPackage */
    public function test_openEditPageByAgent()
    {
        $this->getLoggedInUserForWeb('admin');
        //for create package
        $this->call("post", url("bill/package/store-update"), $this->getParams());
        $packageId = Package::orderBy('id', 'desc')->value('id');

        $this->getLoggedInUserForWeb('agent');
        $response = $this->call("GET", url("bill/package/" . $packageId . "/edit"), []);
        $response->assertStatus(302);
    }

    /** @group editPackage */
    public function test_openEditPageByClient()
    {
        $this->getLoggedInUserForWeb('admin');
        //for create package
        $this->call("post", url("bill/package/store-update"), $this->getParams());
        $packageId = Package::orderBy('id', 'desc')->value('id');

        $this->getLoggedInUserForWeb('user');

        $response = $this->call("GET", url("bill/package/" . $packageId . "/edit"), []);
        $response->assertStatus(302);
    }

    /** @group editPackage */
    public function test_openEditPageByguestUser()
    {       
        $this->getLoggedInUserForWeb('admin');
        //for create package
        $this->call("post", url("bill/package/store-update"), $this->getParams());
        $packageId = Package::orderBy('id', 'desc')->value('id');
        Auth::logout();
        $response = $this->call("GET", url("bill/package/" . $packageId . "/edit"), []);
        $response->assertStatus(302);
    }

    /** @group editPackageInfo */
    public function test_getEditInfoByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        //for create package
        $this->call("post", url("bill/package/store-update"), $this->getParams());
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $response = $this->call("GET", url("api/bill/package/edit/" . $packageId), []);
        $response->assertStatus(200);
    }

    /** @group editPackageInfo */
    public function test_getEditInfoByAgent()
    {
        $this->getLoggedInUserForWeb('admin');
//for create package
        $this->call("post", url("bill/package/store-update"), $this->getParams());
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->getLoggedInUserForWeb('agent');

        $response = $this->call("GET", route("bill.edit.data", $packageId), []);
        $response->assertStatus(302);
    }

    /** @group editPackageInfo */
    public function test_getEditInfoByClient()
    {
        $this->getLoggedInUserForWeb('admin');
        //for create package
        $this->call("post", url("bill/package/store-update"), $this->getParams());
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->getLoggedInUserForWeb('user');
        $response = $this->call("GET", route("bill.edit.data", $packageId), []);
        $response->assertStatus(302);
    }

    /** @group editPackageInfo */
    public function test_getEditInfoByguestUser()
    {
        $this->getLoggedInUserForWeb('admin');
//for create package
        $this->call("post", url("bill/package/store-update"), $this->getParams());
        $packageId = Package::orderBy('id', 'desc')->value('id');
        Auth::logout();
        $response = $this->call("GET", route("bill.edit.data", $packageId), []);
        $response->assertStatus(302);
    }

    /** @group updatePackageInfo */
    public function test_updateEditInfoByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $getInfo['id'] = $packageId;

        $response = $this->call("post", url("bill/package/store-update"), $getInfo);
        $response->assertStatus(200);
    }

    /** @group deletePackage */
    public function test_deletePackageByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $response = $this->call("delete", url("bill/package/delete"), ['package_ids' => $packageId]);
        $response->assertStatus(200);
    }

    /** @group deletePackage */
    public function test_deletePackagewhenUserPurchesedPackageByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $checkout = $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $this->user->id]);
        $info = Order::orderBy('id', 'desc')->first();
        $response = $this->call("delete", url("bill/package/delete"), ['package_ids' => $packageId]);
        $this->assertEquals(json_decode($response->getContent())->message, 'This package already purchased by some user');
    }

    /** @group getActivePackage */
    public function test_getActivePackageByAdmin()
    {

        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', url('bill/package/get-active-packages'), []);
        $this->assertEquals(json_decode($response->content())->data->total, 1);
        Package::where('id', $packageId)->update(['status' => 0]);
        $response = $this->call('get', url('bill/package/get-active-packages'), []);
        $this->assertEquals(json_decode($response->content())->data->total, 0);
    }

    /** @group getActivePackage */
    public function test_getActivePackageByAgent()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('agent');
        $packageId = Package::orderBy('id', 'desc')->value('id');

        $activeresponse = $this->call('get', route('user.active.packages'), []);
        $this->assertEquals(json_decode($activeresponse->content())->data->total, 1);
        Package::where('id', $packageId)->update(['status' => 0]);
        $response = $this->call('get', url('bill/package/get-active-packages'), []);
        $this->assertEquals(json_decode($response->content())->data->total, 0);
    }

    /** @group getActivePackage */
    public function test_getActivePackageByUser()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        $packageId = Package::orderBy('id', 'desc')->value('id');
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $response = $this->call('get', url('bill/package/get-active-packages'), []);
        $this->assertEquals(json_decode($response->content())->data->total, 1);

        Package::where('id', $packageId)->update(['status' => 0]);
        $response = $this->call('get', url('bill/package/get-active-packages'), []);
        $this->assertEquals(json_decode($response->content())->data->total, 0);
    }

    /** @group getUserPackage */
    public function test_getUserPackageByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        Auth::logout();
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('get', url('bill/package/get-user-packages'), ['package_id' => $packageId, 'meta' => true, 'user_id' => $userId]);
        $this->assertEquals(json_decode($response->content())->data->total, 1);
    }

    /** @group getUserPackage */
    public function test_getUserPackageByAgent()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        Auth::logout();
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call('get', url('bill/package/get-user-packages'), ['package_id' => $packageId, 'meta' => true, 'user_id' => $userId]);
        $this->assertEquals(json_decode($response->content())->data->total, 1);
    }

    /** @group getUserInvoice */
    public function test_getUserInvoiceByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        Auth::logout();
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('get', url('bill/package/get-user-invoice'), ['meta' => true, 'users' => [$userId]]);
        $this->assertEquals(json_decode($response->content())->data->total, 1);
    }

    /** @group getUserInvoice */
    public function test_getUserInvoiceByAgent()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;

        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        Auth::logout();
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call('get', url('bill/package/get-user-invoice'), ['meta' => true, 'users' => [$userId]]);
        $this->assertEquals(json_decode($response->content())->data->total, 1);
    }

    /** @group getUserInvoice */
    public function test_getUserInvoiceByUser()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        $response = $this->call('get', url('bill/package/get-user-invoice'), ['meta' => true]);
        $this->assertEquals(json_decode($response->content())->data->total, 1);
    }

    /** @group getInvoiceInfo */
    public function test_getInvoiceInfoByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        Auth::logout();
        $this->getLoggedInUserForWeb('admin');
        $invoiceId = Invoice::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', route('user.invoiceinfo', $invoiceId), []);
        $this->assertEquals(json_decode($response->content())->data->id, $invoiceId);
    }

    /** @group getInvoiceInfo */
    public function test_getInvoiceInfoByAgent()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();

        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        Auth::logout();
        $this->getLoggedInUserForWeb('agent');
        $invoiceId = Invoice::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', route('user.invoiceinfo', $invoiceId), []);
        $this->assertEquals(json_decode($response->content())->data->id, $invoiceId);
    }

    /** @group getInvoiceInfo */
    public function test_getInvoiceInfoByUser()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        $invoiceId = Invoice::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', route('user.invoiceinfo', $invoiceId), []);
        $this->assertEquals(json_decode($response->content())->data->id, $invoiceId);
    }

    /** @group getPackageInfo */
    public function test_getPackageInfoByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);

        $userId = $this->user->id;

        $packageId = Package::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', route('user.packageinfo', $packageId), []);
        $this->assertEquals(json_decode($response->content())->success, true);
        $this->assertEquals(json_decode($response->content())->data->id, $packageId);
    }

    /** @group getPackageInfo */
    public function test_getPackageInfoByAgent()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();

        $this->getLoggedInUserForWeb('agent');
        $packageId = Package::orderBy('id', 'desc')->value('id');

        $response = $this->call('get', route('user.packageinfo', $packageId), []);
        $this->assertEquals(json_decode($response->content())->success, true);
        $this->assertEquals(json_decode($response->content())->data->id, $packageId);
    }

    /** @group getPackageInfo */
    public function test_getPackageInfoByUser()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', route('user.packageinfo', $packageId), []);
        $this->assertEquals(json_decode($response->content())->success, true);
        $this->assertEquals(json_decode($response->content())->data->id, $packageId);
    }

    /** @group getOrderInfo */
    public function test_getOrderInfoByAdmin()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();

        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        Auth::logout();
        $this->getLoggedInUserForWeb('admin');

        $orderId = Order::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', route('user.orderinfo', $orderId), []);

        $this->assertEquals(json_decode($response->content())->success, true);
        $this->assertEquals(json_decode($response->content())->data->id, $orderId);
    }

    /** @group getOrderInfo */
    public function test_getOrderInfoByAgent()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();

        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        Auth::logout();
        $this->getLoggedInUserForWeb('agent');
        $orderId = Order::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', route('user.orderinfo', $orderId), []);

        $this->assertEquals(json_decode($response->content())->success, true);
        $this->assertEquals(json_decode($response->content())->data->id, $orderId);
    }

    /** @group getOrderInfo */
    public function test_getOrderInfoByUser()
    {
        $this->getLoggedInUserForWeb('admin');
        $getInfo = $this->getParams();
        //for create package
        $this->call("post", url("bill/package/store-update"), $getInfo);
        Auth::logout();
        $this->getLoggedInUserForWeb('user');
        $userId = $this->user->id;
        $packageId = Package::orderBy('id', 'desc')->value('id');
        $this->call('get', url('bill/package/user-checkout'), ['package_id' => $packageId, 'meta' => false, 'user_id' => $userId]);
        $orderId = Order::orderBy('id', 'desc')->value('id');
        $response = $this->call('get', route('user.orderinfo', $orderId), []);
        $this->assertEquals(json_decode($response->content())->success, true);
        $this->assertEquals(json_decode($response->content())->data->id, $orderId);
    }

}
