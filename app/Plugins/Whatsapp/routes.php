<?php


    Route::group(['prefix' => 'whatsapp'], function() {

        Route::post('/','App\Plugins\Whatsapp\Controllers\WhatsappController@webhook');
        /* Following get Route is for testing purpose only and can be safely removed */
        Route::get('/','App\Plugins\Whatsapp\Controllers\WhatsappController@webhook'); 
        
    });

    Route::group(['middleware' => ['web', 'auth', 'roles'], 'prefix' => 'whatsapp/api'], function() {

        

        Route::post('/create','App\Plugins\Whatsapp\Controllers\WhatsappSettingsController@store');
        Route::put('/update','App\Plugins\Whatsapp\Controllers\WhatsappSettingsController@update');
        Route::delete('/delete','App\Plugins\Whatsapp\Controllers\WhatsappSettingsController@destroy');
        Route::get('/accounts','App\Plugins\Whatsapp\Controllers\WhatsappSettingsController@returnAll');
        
    });

    Route::group(['middleware' => ['web', 'auth', 'roles'], 'prefix' => 'whatsapp'],function(){

        Route::get('/settings', 'App\Plugins\Whatsapp\Controllers\WhatsappSettingsController@settingsView')->name('whatsapp.settings');
        Route::get('/edit/{id}', 'App\Plugins\Whatsapp\Controllers\WhatsappSettingsController@editView')->name('whatsapp.edit');

    });
    
    

    \Event::listen('admin-panel-navigation-data-dispatch', function(&$navigationContainer) {
        (new App\Plugins\Whatsapp\Controllers\WhatsappAdminNavigationController)
        ->injectWhatsappAdminNavigation($navigationContainer);
    });


    \Event::listen('Reply-Ticket',function($event){
        
        $controller = new \App\Plugins\Whatsapp\Controllers\WhatsappReplyController(
            new \App\Plugins\Whatsapp\Model\WhatsApp()
        );
        $controller->sendReply($event);
        
    });

