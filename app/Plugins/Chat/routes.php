<?php
/**
 * Auth route
 */
Route::group(['middleware' => ['web', 'auth', 'roles'], 'prefix' => 'chat'], function() {
    Route::get('/settings','App\Plugins\Chat\Controllers\Core\ChatController@chatSettingsPage')->name('chat.settings');
    Route::post('/activate/{app}',['as'=>'chat.settings.post','uses'=>'App\Plugins\Chat\Controllers\Core\SettingsController@activateIntegration']);
    // Route::get('/ajax-url',['as'=>'chat.settings','uses'=>'App\Plugins\Chat\Controllers\Core\SettingsController@ajax']);
    Route::get('/edit/{id}', 'App\Plugins\Chat\Controllers\Core\ChatController@edit')->name('chat.edit');

});


Route::group(['middleware' => ['web', 'auth', 'roles'], 'prefix' => 'chat/api'], function() {
    Route::get('/chats', 'App\Plugins\Chat\Controllers\Core\ChatController@getChats');
    Route::get('/status/{id}', 'App\Plugins\Chat\Controllers\Core\ChatController@statusChange');
    Route::put('/update/{id}','App\Plugins\Chat\Controllers\Core\ChatController@persistChatService');
});

/**
 * Non auth routes
 */
Route::group(['prefix' => 'chat'], function() {
    Route::post('/{app}/{dept}/{helptopic}','App\Plugins\Chat\Controllers\Core\ChatController@chat');
    // Route::post('data/{app?}/{modelid?}/{model?}',['as'=>'chat.post','uses'=>'App\Plugins\Chat\Controllers\Core\ChatController@chat']);
    Route::get('test',['as'=>'chat.test','uses'=>'App\Plugins\Chat\Controllers\Core\SettingsController@sendTest']);
    
});

\Event::listen('admin-panel-navigation-data-dispatch', function(&$navigationContainer) {
    (new App\Plugins\Chat\Controllers\ChatAdminNavigationController)
      ->injectChatAdminNavigation($navigationContainer);
  });

\Event::listen('client-panel-scripts-dispatch', function () {
    $allChatScripts = App\Plugins\Chat\Model\Chat::where('status', 1)->get(['script'])->toArray();
    $scripts = array_filter(array_column($allChatScripts, 'script'));
    echo implode('', $scripts);

});



