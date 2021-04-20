<?php
/**
 * Auth URLs and APIs
 *
 */
Route::group(['middleware' => ['web', 'auth']], function() {
    /**
     * Admin URLs and APIs
     *
     */
    Route::group(['middleware' => ['roles']], function() {
            
        Route::get('bill', ['as' => 'bill.setting', 'uses' => 'App\Bill\Controllers\SettingsController@setting']);

        Route::post('bill', ['as' => 'bill.settings.post', 'uses' => 'App\Bill\Controllers\SettingsController@postSetting']);

        Route::get('bill/payment-gateways', ['as' => 'gatewaylit', 'uses' => 'App\Bill\Controllers\PaymentController@showGateWays']);

        Route::get('bill/gateway/{name}/{gatewayName?}', ['as' => 'gateway.settings', 'uses' => 'App\Bill\Controllers\PaymentController@gatewayDetails']);

        Route::post('bill/update/gateway', ['as' => 'gateway.update', 'uses' => 'App\Bill\Controllers\PaymentController@updateGateway']);



        Route::get('bill/package/inbox',['as'=>'bill.inbox','uses'=>'App\Bill\Controllers\PackageController@inbox']);
    
        Route::get('bill/package/get-inbox-data',['as'=>'bill.inbox.data','uses'=>'App\Bill\Controllers\PackageController@getData']);

        Route::get('bill/package/create',['as'=>'bill.create','uses'=>'App\Bill\Controllers\PackageController@create']);
    
        Route::get('bill/package/{id}/edit',['as'=>'bill.edit','uses'=>'App\Bill\Controllers\PackageController@edit']);
    
        Route::get('api/bill/package/edit/{id}',['as'=>'bill.edit.data','uses'=>'App\Bill\Controllers\PackageController@editApi']);
        
        Route::post('bill/package/store-update',['as'=>'bill.store','uses'=>'App\Bill\Controllers\PackageController@store']);

        Route::delete('bill/package/delete',['as'=>'bill.delete','uses'=>'App\Bill\Controllers\PackageController@delete']);

    });
    
    /**
     * Agent and Admin URLs and APIs
     *
     */
    Route::group(['middleware' => ['role.agent']], function() {
            
        Route::get('bill/{id}/delete',['as'=>'bill.delete','uses'=>'App\Bill\Controllers\BillController@delete']);
        
        Route::patch('bill/{id}',['as'=>'bill.update','uses'=>'App\Bill\Controllers\BillController@update']);
        
        Route::post('new-bill',['as'=>'new.bill','uses'=>'App\Bill\Controllers\BillController@store']);
        
        Route::get('ticket/{ticket_id}/invoice',['as'=>'ticket.invoice','uses'=>'App\Bill\Controllers\BillController@sendInvoice']);

        ////delete api order and invoice///////////

        Route::get('bill/package/get-all-count/{userid}',['as'=>'user.package.count','uses'=>'App\Bill\Controllers\PackageController@getCountInfo']);

      
        Route::get('bill/package/{id}/user-invoice',['as'=>'user.invoic.page','uses'=>'App\Bill\Controllers\InvoiceController@viewUserInvoice']);

        Route::post('bill/package/add-payment', ['as' => 'manual.payment', 'uses' => 'App\Bill\Controllers\TransactionController@addPayment']);

        Route::get('bill/order/{orderId}', ['as' => 'order.info', 'uses' => 'App\Bill\Controllers\PackageController@viewOrderInfo']);

        Route::get('invoice/send/{invoiceId}', ['as' => 'send.invoice', 'uses' => 'App\Bill\Controllers\InvoiceController@sendInvoice'])->where('invoiceId', '[0-9]+');

        Route::put('invoice/update', ['as' => 'unpaid.invoice', 'uses' => 'App\Bill\Controllers\TransactionController@updateInvoice']);

        Route::get('billing/invoices', ['as' => 'list.invoice', 'uses' => 'App\Bill\Controllers\InvoiceController@showInvoiceList']);
    });

    /**
     * Client URLs and APIs
     *
     */
    Route::get('bill/package/get-user-packages',['as'=>'user.packages','uses'=>'App\Bill\Controllers\PackageController@getUserPackage']);
         
    Route::get('bill/package/get-user-invoice',['as'=>'user.invoice','uses'=>'App\Bill\Controllers\InvoiceController@getUserInvoice']);

    Route::get('bill/package/user-invoice-info/{invoiceid}',['as'=>'user.invoiceinfo','uses'=>'App\Bill\Controllers\InvoiceController@getInvoiceInfo']);
        
    /**
     * Checkout
     */
    Route::get('bill/package/user-checkout',['as'=>'user.invoice','uses'=>'App\Bill\Controllers\PackageController@checkOut']);

    Route::get('checkout/{gateway}/{invoice}', ['as' => 'checkout', 'uses' => 'App\Bill\Controllers\PaymentController@checkout']);

    Route::get('cancelled/payment/{gateway}/{invoice}', ['as' => 'checkout.cancelled', 'uses' => 'App\Bill\Controllers\PaymentController@cancelled']);

    Route::get('completed/payment/{gateway}/{invoice}', ['as' => 'checkout.completed', 'uses' => 'App\Bill\Controllers\PaymentController@completed']);

    Route::get('bill/get-gateways-list', ['as' => 'gatewaylit', 'uses' => 'App\Bill\Controllers\PaymentController@getGateWaysList']);
    
    Route::get('bill/package/user-order-info/{orderid}',['as'=>'user.orderinfo','uses'=>'App\Bill\Controllers\PackageController@getOrderInfo']);

     Route::get('/invoice/pdf/{invoiceid}', ['as' => 'invoice.pdf', 'uses' => 'App\Bill\Controllers\InvoiceController@downloadPdf']);

});
/**
 * UnAuth URLs and APIs
 *
 */
Route::get('bill/package/get-active-packages',['as'=>'user.active.packages','uses'=>'App\Bill\Controllers\PackageController@getActivePackage']);

Route::get('bill/package/user-package-info/{packageid}',['as'=>'user.packageinfo','uses'=>'App\Bill\Controllers\PackageController@getPackageInfo']);

Route::get('ticket/bind-billinfo',['as'=>'bill.bind.details','uses'=>'App\Bill\Controllers\BillController@billInfo']);




/**
 *================================
 * Event Handlers
 *================================
 */
\Event::listen('App\Events\TimeLineFormEvent',function($event){
    $set = new App\Bill\Controllers\BillController();
    echo $set->threadLevelForm($event);
});
\Event::listen('App\Events\FaveoAfterReply',function($event){
    $set = new App\Bill\Controllers\BillController();
    $set->postReply($event);
});
\Event::listen('reply.request', function($event) {
    $set = new App\Bill\Controllers\BillController();
    echo $set->requestRule($event);
});
\Event::listen('timeline.tab.content', function($event) {
    $set = new App\Bill\Controllers\BillController();
    echo $set->billingTabContent($event);
});
\Event::listen('timeline.tab.list', function($event) {
    $set = new App\Bill\Controllers\BillController();
    echo $set->billingTabList($event);
});
\Event::listen('ticket.merge', function($event) {
    $set = new App\Bill\Controllers\BillController();
    echo $set->merge($event);
});
 \Event::listen('ticket.details.more.list', function($event) {
    $set = new App\Bill\Controllers\BillController();
    echo $set->moreList($event);
});
\Event::listen('ticket.detail.modelpopup', function($event) {
    $set = new App\Bill\Controllers\BillController();
    echo $set->ticketLevelForm($event);
});

\Event::listen('App\Events\ClientTicketForm',function($event){
    $controller = new App\Bill\Controllers\TicketEventHandler;
    return $controller->renderForm($event->event, $event->formType);
});

\Event::listen('App\Events\TicketFormDependency',function($event){
    $controller = new App\Bill\Controllers\TicketEventHandler;
    return $controller->renderPackageList($event);
});

\Event::listen('before_ticket_creation_event',function($event){
    $controller = new App\Bill\Controllers\TicketEventHandler;
    return $controller->handleTicketCreateEvent($event[0],$event[1],$event[2], $event[3]);
});

\Event::listen('after.ticket.created',function($event){
    $controller = new App\Bill\Controllers\TicketEventHandler;
    return $controller->handleTicketAfterCreateEvent($event);
});

\Event::listen('show.admin.settings', function() {
    (new App\Bill\Controllers\SettingsController)->renderSettingsBlock();
});

\Event::listen('alert-settings-block-1', function($alerts) {
    (new App\Bill\Controllers\SettingsController)->renderAlertAndNoticeSettings($alerts, 'purchase-confirmation-alert');
});

\Event::listen('alert-settings-block-2', function($alerts) {
    (new App\Bill\Controllers\SettingsController)->renderAlertAndNoticeSettings($alerts, 'payment-approval-alert', ['admin', 'agent']);
});

\Event::listen('update_client_layout_data', function(&$data) {
    (new App\Bill\Controllers\SettingsController)->updateClientLayoutData($data);
});

\Event::listen('update_template_variable_shortcode_query_builder', function($data) {
    (new App\Bill\Controllers\SettingsController)->updateTemplateVariables($data);
});

\Event::listen('update_template_list_query_builder', function($data) {
    (new App\Bill\Controllers\SettingsController)->updateTemplateList($data);
});

//This is a temp event must be removed with dynamic navigation
\Event::listen('update_topbar_right', function() {
    (new App\Bill\Controllers\SettingsController)->showInvoiceTabOnTopBar();
});

Event::listen('admin-panel-navigation-array-data-dispatch', function(&$navigationContainer) {
    (new App\Bill\Controllers\BillAdminNavigationController)
    ->injectBillAdminNavigation($navigationContainer);
});

\Event::listen('agent-panel-scripts-dispatch', function() {
    echo "<script src=".bundleLink('js/faveoBilling.js')."></script>";
});

