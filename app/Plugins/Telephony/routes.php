<?php

Route::group(['middleware' => ['web', 'auth', 'roles'], 'prefix' => 'telephony'], function() {
    Route::get('settings', ['as' => 'telephone.settings', 'uses' => 'App\Plugins\Telephony\Controllers\TelephonySettingsController@showSettings']);

    Route::get('api/get-providers-list', ['as' => 'telephone.settings', 'uses' => 'App\Plugins\Telephony\Controllers\TelephonySettingsController@getProviderList']);

    Route::get('api/get-provider-details/{provider}', ['as' => 'provider.details', 'uses' => 'App\Plugins\Telephony\Controllers\TelephonySettingsController@getProviderDetails']);

    Route::post('api/update-provider-details/{provider}', ['as' => '', 'uses' => 'App\Plugins\Telephony\Controllers\TelephonySettingsController@updateProviderDetails']);

    Route::get('api/get-regions-list', ['as' => '', 'uses' => 'App\Plugins\Telephony\Controllers\TelephonySettingsController@getCountriesWithIsoCode']);

    Route::post('api/convert-call-to-ticket/{callLog}', ['as' => '', 'uses' => 'App\Plugins\Telephony\Controllers\CallHookHandler@convertCallLogIntoTicket']);
});

Route::match(['GET', 'POST'],'telephone/{provider}/pass/{modelid?}/{model?}',['as' => 'telephone.pass', 'uses' => 'App\Plugins\Telephony\Controllers\CallHookHandler@handleHook']);

\Event::listen('agent-panel-scripts-dispatch', function(){
	(new App\Plugins\Telephony\Controllers\TelephonySettingsController)->echoTelephonyScript();
});

\Event::listen('admin-panel-scripts-dispatch', function(){
	(new App\Plugins\Telephony\Controllers\TelephonySettingsController)->echoTelephonyScript();
});

\Event::listen('client-panel-scripts-dispatch', function(){
	(new App\Plugins\Telephony\Controllers\TelephonySettingsController)->echoTelephonyScript();
});

