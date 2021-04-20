<?php
Route::group(['middleware' => ['web']], function () {
//\Event::listen('App\Events\Timeline', function($event)
// {
//    
//    $controller = new App\Plugins\Reseller\Controllers\ResellerEventController;
//   echo $controller->SendButton($event->para1,$event->para2,$event->para3);
//});

//Route::post('resellers-reply/{id}','App\Plugins\Reseller\Controllers\ResellerEventController@Reply');
//Route::post('reseller/fetch-fields','App\Plugins\Reseller\Controllers\ResellerEventController@FetchCustomFieldsOnForm');

Route::get('reseller/settings',['as'=>'reseller.settings.get','uses'=>'App\Plugins\Reseller\Controllers\SettingsController@index']);
Route::patch('reseller/postsettings',['as'=>'reseller.settings.patch','uses'=>'App\Plugins\Reseller\Controllers\SettingsController@update']);
//Route::get('reseller/populate-department','App\Plugins\Reseller\Controllers\SettingsController@GetDepartments');
//Route::get('reseller/populate-custom','App\Plugins\Reseller\Controllers\SettingsController@GetCustomFields');
/* 
 * Tichet Box header for getting order from reseller club
 */

\Event::listen('App\Events\TicketBoxHeader',function($event)
{
    //dd($event);
    $controller = new App\Plugins\Reseller\Controllers\ResellerOrderController;
    echo $controller->ResellerOrderButton($event->para1);
});
 Route::get('order-search/{id}',['as'=>'reseller.order.search','uses'=>'App\Plugins\Reseller\Controllers\ResellerOrderController@getSingleDomain']);
 
 /*
  * Ticket detail table event handling
  */
 
 \Event::listen('App\Events\TicketDetailTable',function($event){
    $controller = new App\Plugins\Reseller\Controllers\ResellerEventController;
    echo $controller->TicketDetails($event->para1,$event->para2,$event->para3);
 });
 
 /*
  * Adding Reseller Login for Faveo User
  */
 
\Event::listen('auth.login.event',function(){
    $controller = new App\Plugins\Reseller\Controllers\ResellerAuthController;
    echo $controller->PostLogin();
 }); 
 
 /*
  * Adding Reseller Register Form for Faveo User
  */
 
 \Event::listen('App\Events\FormRegisterEvent',function($event){
     //dd($event);
    $controller = new App\Plugins\Reseller\Controllers\ResellerAuthController;
    echo $controller->FormRegister($event->para1);
 }); 
 
 Route::post('auth/register',['as'=>'reseller.auth.register.post','uses'=>'App\Plugins\Reseller\Controllers\ResellerAuthController@PostRegister']);
 
 /*
  * Adding Reseller Post Register for Faveo User
  */
 
 \Event::listen('App\Events\ReadMailEvent',function($event){
     //dd($event);
    $controller = new App\Plugins\Reseller\Controllers\ResellerEventController;
    return $controller->ReadMail($event->para1,$event->para2);
 }); 
 
 /*
  * Reset password
  */
 
 \Event::listen('reset.password',function(){
     //dd($event);
    $controller = new App\Plugins\Reseller\Controllers\ResellerAuthController;
    return $controller->resetPassword();
 }); 
 
});