<?php

\Event::listen('lime-survey.plugin.activated', function(){
   $obj = new App\Plugins\LimeSurvey\Controllers\LimeSurveySetting();
   $obj->seedTemplates();
});




Route::group(['middleware' => 'web'], function () {
   Route::group(['middleware' => 'auth'], function(){
      Route::get('lime-survey/settings', [ 'as' => 'lime-survey.settings', 'uses' => 'App\Plugins\LimeSurvey\Controllers\LimeSurveySetting@getSettings']);
      Route::post('lime-survey/settings', [ 'as' => 'lime-survey.settings', 'uses' => 'App\Plugins\LimeSurvey\Controllers\LimeSurveySetting@setSettings']);
      Route::patch('lime-survey/settings', [ 'as' => 'lime-survey.settings', 'uses' => 'App\Plugins\LimeSurvey\Controllers\LimeSurveySetting@setSettings']);
   });
});


?>