<?php

    \Event::listen('settings.micro.organization', function() {
        $set = new App\MicroOrganization\Controllers\SettingsController();
        echo $set->settingsLink();
    });
   
             Route::group(['middleware' => ['web', 'auth']], function() {
           Route::get('helpdesk/micro-organizarion', ['as' => 'helpdesk.micro.organizarion', 'uses' => 'App\MicroOrganization\Controllers\MicroOrganization\MicroOrganizationController@index']);

  Route::post('helpdesk/micro/organization/settings', ['as' => 'micro.organization.settings', 'uses' => 'App\MicroOrganization\Controllers\MicroOrganization\MicroOrganizationController@settings']);

    });

     

