<?php

    \Event::listen('settings.helptopic.link.types', function() {

        $set = new App\HelptopicType\Controllers\SettingsController();
        echo $set->settingsLink();
    });
   
  Route::group(['middleware' => ['web', 'auth']], function() {
  Route::get('helpdesk/helptopic-type/settings/status', ['as' => 'helpdesk.helptopic-type.settings', 'uses' => 'App\HelptopicType\Controllers\HelptopicType\HelptopicTypeController@index']);
  Route::post('helpdesk/helptopic-type', ['as' => 'helpdesk.helptopic-type', 'uses' => 'App\HelptopicType\Controllers\HelptopicType\HelptopicTypeController@settings']);
  

    });

     

