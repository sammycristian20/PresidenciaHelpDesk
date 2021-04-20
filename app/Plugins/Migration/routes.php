<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'migration'], function() {
    
    Route::get('get-migration',['as'=>'migration.get','uses'=>'App\Plugins\Migration\Controllers\MigrateController@getMigration']);
    
    Route::get('migrate',['as'=>'migrate','uses'=>'App\Plugins\Migration\Controllers\MigrateController@migrate']);
    
    Route::match(['get', 'post'],'upload',['as'=>'migrate.upload','uses'=>'App\Plugins\Migration\Controllers\MigrateController@upload']);
    
    
});