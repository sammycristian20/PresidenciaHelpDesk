<?php


Route::group(['middleware' => ['web', 'auth', 'roles']], function() {
    Route::get('system-logs', ['as' => 'system.logs', 'uses' => 'App\FaveoLog\controllers\LogViewerController@systemLogs']);
    Route::get('logs/api/{date?}', ['as' => 'logs.api', 'uses' => 'App\FaveoLog\controllers\LogViewerController@logApi']);

//====================================================================================================================================
    Route::get('/api/logs/{type}', 'App\FaveoLog\controllers\LogViewController@getLogs');
    Route::get('/api/log-category-list', 'App\FaveoLog\controllers\LogViewController@getLogCategoryList');
    Route::get('/api/get-log-mail-body/{logId}', 'App\FaveoLog\controllers\LogViewController@getMailBody');
    Route::get('/api/get-user-by-email', 'App\FaveoLog\controllers\LogViewController@getUserByEmail');
    Route::delete('/api/delete-logs', 'App\FaveoLog\controllers\LogViewController@deleteLogs');
//====================================================================================================================================

});
