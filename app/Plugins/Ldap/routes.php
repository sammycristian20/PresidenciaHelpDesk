<?php


Route::group(['middleware' => ['web']], function() {

    Route::get('ldap/settings/create', ['as'=>'ldap.settings.create','uses'=>'App\Plugins\Ldap\Controllers\ApiLdapController@create']);

    Route::get('ldap/settings/{id}/edit', ['as'=>'ldap.settings.edit','uses'=>'App\Plugins\Ldap\Controllers\ApiLdapController@edit']);

    Route::get('ldap/settings', ['as'=>'ldap.settings.index','uses'=>'App\Plugins\Ldap\Controllers\ApiLdapController@index']);

    Route::post('api/ldap/settings', 'App\Plugins\Ldap\Controllers\ApiLdapController@postLdapSettings');

    Route::get('api/ldap/settings/{ldapId}', 'App\Plugins\Ldap\Controllers\ApiLdapController@getLdapSettings');

    Route::post('api/ldap/search-bases/{ldapId}', 'App\Plugins\Ldap\Controllers\ApiLdapController@postSearchBases');

    Route::delete('api/ldap/search-base/{id}', 'App\Plugins\Ldap\Controllers\ApiLdapController@deleteSearchBase');

    Route::get('api/ldap/search-base/ping/{ldapId}', 'App\Plugins\Ldap\Controllers\ApiLdapController@pingLdapWithSearchQuery');

    Route::get('api/ldap/advanced-settings/{ldapId}', 'App\Plugins\Ldap\Controllers\ApiLdapController@getAdvancedSettings');

    Route::post('api/ldap/advanced-settings/{ldapId}', 'App\Plugins\Ldap\Controllers\ApiLdapController@postAdvancedSettings');

    Route::get('api/dependency/ldap-directory-attributes/{ldapId}', 'App\Plugins\Ldap\Controllers\ApiLdapController@getDirectoryAttribute');

    Route::post('api/ldap/ldap-directory-attribute/{ldapId}', 'App\Plugins\Ldap\Controllers\ApiLdapController@postDirectoryAttribute');

    Route::delete('api/ldap/ldap-directory-attribute/{id}', 'App\Plugins\Ldap\Controllers\ApiLdapController@deleteDirectoryAttribute');

    Route::get('api/ldap/settings', 'App\Plugins\Ldap\Controllers\ApiLdapController@getLdapSettingsList');

    Route::post('api/ldap/hide-default-login', 'App\Plugins\Ldap\Controllers\ApiLdapController@hideDefaultLogin');

    Route::delete('api/ldap/settings/{ldapId}', 'App\Plugins\Ldap\Controllers\ApiLdapController@deleteLdapSettings');
});


Event::listen('login-data-submitting', function($request,&$validLdap2faCredential) {
    $ldap_controller = new \App\Plugins\Ldap\Controllers\ApiLdapController(new \App\Plugins\Ldap\Controllers\LdapConnector);
    $ldap_controller->authLdap($request,$validLdap2faCredential);
});


Event::listen('admin-panel-navigation-data-dispatch', function(&$navigationContainer) {
    (new App\Plugins\Ldap\Controllers\LdapAdminNavigationController)
        ->injectLdapAdminNavigation($navigationContainer);
});


Event::listen('client-panel-scripts-dispatch', function() {
    // injecting script at login page
    echo "<script src=".bundleLink('js/ldap.js')."></script>";
});

Event::listen('verify-user-credential', function(&$password,&$passwordVerified) {
    $ldap_controller = new \App\Plugins\Ldap\Controllers\ApiLdapController(new \App\Plugins\Ldap\Controllers\LdapConnector);
    $ldap_controller->verifyCredentials($password,$passwordVerified);
});

\Event::listen('delete-extra-entries', function($formFieldId) {
    $formFieldIdentifier = "custom_$formFieldId";
    \App\Plugins\Ldap\Model\LdapFaveoAttribute::where('name', $formFieldIdentifier)->delete();
});

Event::listen('social-login-provider-dispatch', function (&$data){
    $ldap_controller = new \App\Plugins\Ldap\Controllers\ApiLdapController(new \App\Plugins\Ldap\Controllers\LdapConnector);
    $ldap_controller->appendMetaSettings($data);
});