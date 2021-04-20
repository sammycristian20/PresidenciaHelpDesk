<?php

\Event::listen('App\Events\ClientTicketForm',function($event){
    $controller = new App\Plugins\Envato\Controllers\TicketFormController;
    return $controller->ClientForm($event->event);
            
});

\Event::listen('App\Events\TimeLineFormEvent',function($event){
    $controller = new App\Plugins\Envato\Controllers\TicketFormController;
    $controller->AgetTimeLineForm($event->para1);
            
});

\Event::listen('App\Events\ClientTicketFormPost',function($event){
    $controller = new App\Plugins\Envato\Controllers\TicketFormController;
    $controller->postPurchase($event->para1,$event->para2,$event->para3);
});
Route::group(['middleware' => 'web'], function(){
    Route::get('envato/settings', ['as' => 'envanto-settings', 'uses' => 'App\Plugins\Envato\Controllers\EnvatoSettingsController@SettingsForm'])
            ->middleware('roles');
    Route::get('envato/details/{id}', ['as' => 'envanto-details', 'uses' => 'App\Plugins\Envato\Controllers\TicketTimeLineController@getDetails']);
    Route::patch('postsettings',  'App\Plugins\Envato\Controllers\EnvatoSettingsController@PostSettings')
            ->middleware('roles');
    Route::any('envato/redirect',['as'=>'envato.login','uses'=>'App\Plugins\Envato\Controllers\EnvatoAuthController@envatoCallback'])
        ->middleware('roles');
});

\Event::listen('App\Events\TicketDetailTable',function($event){
    $controller = new App\Plugins\Envato\Controllers\TicketTimeLineController;
    $controller->showPurcaseCode($event->para1);
});

\Event::listen('App\Events\TopNavEvent',function($event){
    $controller = new App\Plugins\Envato\Controllers\EnvatoSettingsController;
    $controller->TopNav();
});


//Event::listen('auth.login.service', function()
//{
//    $controller = new App\Plugins\Envato\Controllers\EnvatoAuthController();
//    echo $controller->loginService();
//});



\Event::listen('plugin.status.change',function($event){
    $controller = new App\Plugins\Envato\Controllers\EnvatoSettingsController;
    $controller->statusChange($event);
});

//\Event::listen('client.create.ticket.form',function(){
//    $controller = new App\Plugins\Envato\Controllers\EnvatoAuthController();
//    echo $controller->isSubmitTicket();
//});

//\Event::listen('user.login',function(){
//    $controller = new App\Plugins\Envato\Controllers\EnvatoAuthController();
//    echo $controller->afterLogin();
//});

\Event::listen('after.ticket.created',function($event){
    $controller = new App\Plugins\Envato\Controllers\TicketFormController();
    $controller->updatePurchase($event);
});
