<?php

Route::group(['middleware' => ['web', 'auth', 'roles']], function () {
    Route::group(['prefix' => 'facebook/api'], function () {
        
        Route::get('integration', 'App\Plugins\Facebook\Controllers\FacebookCredentialsController@index')
            ->name('api.facebook.integration');

        Route::delete('integration/{integrationId}', 'App\Plugins\Facebook\Controllers\FacebookCredentialsController@destroy')
            ->name('api.facebook.integration.destroy');

        Route::post('integration', 'App\Plugins\Facebook\Controllers\FacebookCredentialsController@store')
            ->name('api.facebook.integration.store');

        Route::get('integration/status/{integrationId}', 'App\Plugins\Facebook\Controllers\FacebookCredentialsController@changeStatus')
            ->name('api.facebook.integration.status');

        Route::get('integration/token', 'App\Plugins\Facebook\Controllers\FacebookCredentialsController@getVerifyToken')
            ->name('api.facebook.integration.token');

        Route::put('integration/{integrationId}', 'App\Plugins\Facebook\Controllers\FacebookCredentialsController@update')
            ->name('api.facebook.integration.update');

        Route::name('facebook.security.create')->post('/security-settings/create')
            ->uses('App\Plugins\Facebook\Controllers\FacebookGeneralSettingsController@store');

        Route::name('facebook.security.index')->get('/security-settings/index')
            ->uses('App\Plugins\Facebook\Controllers\FacebookGeneralSettingsController@index');

        Route::name('facebook.security.update')->put('/security-settings/update/{settingsId}')
            ->uses('App\Plugins\Facebook\Controllers\FacebookGeneralSettingsController@update');

        Route::name('facebook.security.delete')->delete('/security-settings/delete/{settingsId}')
            ->uses('App\Plugins\Facebook\Controllers\FacebookGeneralSettingsController@destroy');

    });
    
    Route::group(['prefix' => 'facebook','middleware' => ['web', 'auth', 'roles']], function () {

        Route::name('facebook.settings')->get('/settings')
            ->uses('App\Plugins\Facebook\Controllers\FacebookCredentialsController@settingsView');

        Route::get('integration/edit/{integrationId}', 'App\Plugins\Facebook\Controllers\FacebookCredentialsController@edit')
            ->name('facebook.integration.edit');

        Route::get('integration/create', 'App\Plugins\Facebook\Controllers\FacebookCredentialsController@create')
            ->name('facebook.integration.create');

        Route::get('/security-settings', 'App\Plugins\Facebook\Controllers\FacebookGeneralSettingsController@settingsPage')
            ->name('facebook.security.settings');

        Route::name('facebook.security.settings')->get('/security-settings')
            ->uses('App\Plugins\Facebook\Controllers\FacebookGeneralSettingsController@settingsPage');

    });

    \Event::listen('Reply-Ticket',function($event){
        $controller = new \App\Plugins\Facebook\Controllers\ReplyToFBController();
        $controller->replyPageMessage($event);
        
    });

    Event::listen('admin-panel-navigation-data-dispatch', function(&$navigationContainer) {
        (new App\Plugins\Facebook\Controllers\FacebookAdminNavigationController)
          ->injectFacebookAdminNavigation($navigationContainer);
      });
    
});

/*
* Facebook Webhook Routes
*/
Route::group(['prefix' => 'facebook/webhook', 'middleware' => ['facebook']], function () {

    Route::get('/','App\Plugins\Facebook\Controllers\FacebookMessageController@message');

    Route::post('/','App\Plugins\Facebook\Controllers\FacebookMessageController@message');

});
