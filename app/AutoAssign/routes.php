<?php

\Event::listen('ticket.assign', function($event) {
    $dept_id = $event['department'];
    $type_id = $event['type'];
    $locationId = $event['location'];
    $assign = new \App\AutoAssign\Controllers\AutoAssignController();
    return $assign->getAssigneeId($dept_id,$type_id,$locationId);
});
\Event::listen('user.logout', function() {
    $assign = new \App\AutoAssign\Controllers\AutoAssignController();
    $assign->handleLogout();
});

\Event::listen('user.login', function() {
    $assign = new \App\AutoAssign\Controllers\AutoAssignController();
    $assign->handleLogin();
});

\Event::listen('settings.ticket.view', function(&$settingHtmlString) {
    (new App\AutoAssign\Controllers\SettingsController())->settingsView($settingHtmlString);
});

Route::group(['middleware' => ['web','auth','roles']], function() {
    Route::get('auto-assign', [
        'as' => 'auto.assign',
        'uses' => 'App\AutoAssign\Controllers\SettingsController@getSettings'
    ]);

    Route::post('api/auto-assign', [
        'as' => 'post.auto.assign',
        'uses' => 'App\AutoAssign\Controllers\SettingsController@postSettings'
    ]);

    // Route to get auto assign settings details
    Route::get('api/get-auto-assign', 'App\AutoAssign\Controllers\SettingsController@getAutoAssignSettingsDetails');
});
