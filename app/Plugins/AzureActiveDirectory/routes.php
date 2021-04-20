<?php

Route::group(['middleware' => ['web']], function () {

    Route::get('azure-active-directory/settings', ['as'=>'azure-active-directory.index','uses'=>'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@index']);
    Route::get('azure-active-directory/create', ['as'=>'azure-active-directory.create','uses'=>'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@create']);
    Route::get('azure-active-directory/{azureAdId}/edit', ['as'=>'azure-active-directory.edit','uses'=>'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@edit']);
    Route::get('azure-active-directory/authenticate/{azureAdId}', 'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@authenticate');
    Route::get('azure-active-directory/auth-token/callback', 'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@authenticationCallback');

    Route::get('api/azure-active-directory/settings/{azureAdId}', 'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@getAzureAdSettings');
    Route::post('api/azure-active-directory/settings', 'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@postAzureAdSettings');
    Route::delete('api/azure-active-directory/settings/{azureAdId}', 'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@deleteAzureAdSettings');
    Route::get('api/azure-active-directory/settings', 'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@getAzureAdList');
    Route::post('api/azure-active-directory/hide-default-login', 'App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController@hideDefaultLogin');
});

Event::listen('client-panel-scripts-dispatch', function() {
    echo "<script src=".bundleLink('js/azureActiveDirectory.js')."></script>";
});

Event::listen('admin-panel-navigation-data-dispatch', function(&$navigationContainer) {
    (new \App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController(new \App\Plugins\AzureActiveDirectory\Controllers\AzureConnector()))
        ->injectAzureAdminNavigation($navigationContainer);
});

Event::listen('social-login-provider-dispatch', function (&$data){
    (new \App\Plugins\AzureActiveDirectory\Controllers\ApiAzureAdController(new \App\Plugins\AzureActiveDirectory\Controllers\AzureConnector()))
        ->appendMetaSettings($data);
});