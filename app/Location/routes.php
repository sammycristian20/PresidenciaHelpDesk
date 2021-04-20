<?php

    \Event::listen('helpdesk.settings.ticket.location', function() {
        $set = new App\Location\Controllers\SettingsController();
        echo $set->settingsLink();
    });
   
             Route::group(['middleware' => ['web', 'auth']], function() {
                   Route::group(['middleware' => ['roles']], function() {
             Route::get('helpdesk/location-types', ['as' => 'helpdesk.location.index', 'uses' => 'App\Location\Controllers\Location\LocationController@index']);
            Route::get('helpdesk/location-types/create', ['as' => 'helpdesk.location.create', 'uses' => 'App\Location\Controllers\Location\LocationController@create']);
            Route::post('helpdesk/location-types/create1', ['as' => 'helpdesk.post.location', 'uses' => 'App\Location\Controllers\Location\LocationController@handleCreate']);
            Route::get('helpdesk/location-types/{id}/edit', ['as' => 'helpdesk.location.edit', 'uses' => 'App\Location\Controllers\Location\LocationController@edit']);
            Route::post('helpdesk/location-types/postedit1/{id}', ['as' => 'helpdesk.location.postedit', 'uses' => 'App\Location\Controllers\Location\LocationController@handleEdit']);
            Route::post('helpdesk/location/delete/{id}', ['as' => 'helpdesk.location.delete', 'uses' => 'App\Location\Controllers\Location\LocationController@handledelete']);
            //for delete popup purpose 
            Route::get('helpdesk/location/deletepopup/{id}', ['as' => 'helpdesk.location.deletepopup', 'uses' => 'App\Location\Controllers\Location\LocationController@handledelete']);
            
            Route::get('helpdesk-get-location-types', ['as' => 'helpdesk-get-location-types', 'uses' => 'App\Location\Controllers\Location\LocationController@getLocation']);



          // Route::get('helpdesk/location-types/{id}/show', ['as' => 'service-desk.location.show', 'uses' => 'App\Location\Controllers\Location\LocationController@show']);

           Route::get('location/org',['as'=>'org.location','uses'=>'App\Location\Controllers\Location\LocationController@getLocationsForForm']);


              });
    });

     

