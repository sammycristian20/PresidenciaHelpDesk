<?php

Route::group(['middleware' => ['web', 'auth', 'roles']], function () {
    Route::group(['prefix' => 'twitter'], function () {
        Route::get('api/app', 'App\Plugins\Twitter\Controllers\TwitterController@getTwitterApp');
        Route::put('api/update/{id}', 'App\Plugins\Twitter\Controllers\TwitterController@updateApp');
        Route::post('api/create', 'App\Plugins\Twitter\Controllers\TwitterController@createApp');
        Route::delete('api/delete/{id}', 'App\Plugins\Twitter\Controllers\TwitterController@deleteApp');
        Route::get('settings', 'App\Plugins\Twitter\Controllers\TwitterController@settingsView')->name('twitter.settings');
    }); //prefix twitter
    
    \Event::listen('Reply-Ticket', function ($event) {
        $controller = new \App\Plugins\Twitter\Controllers\ReplyToTwitter();
        $controller->replyTwitter($event);
    });
});

\Event::listen('admin-panel-navigation-data-dispatch', function (&$navigationContainer) {
    (new App\Plugins\Twitter\Controllers\TwitterAdminNavigationController())
      ->injectTwitterAdminNavigation($navigationContainer);
});
