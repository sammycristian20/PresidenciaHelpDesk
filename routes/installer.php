<?php
Route::post('pre-license', [
    'as' => 'licAgreement',
    'uses' => 'Installer\helpdesk\InstallController@licAgreement'
]);


Route::get('/JavaScript-disabled', [
    'as' => 'js-disabled',
    'uses' => 'Installer\helpdesk\InstallController@jsDisabled'
]);
Route::get('/license-agreement', [
    'as' => 'license-agreement', 
    'uses' => 'Installer\helpdesk\InstallController@licence'
]);
Route::post('/post-lic-agreement', [
    'as' => 'postprerequisites', 
    'uses' => 'Installer\helpdesk\InstallController@prerequisitescheck'
]);
Route::get('/db-setup', [
    'as' => 'db-setup',
    'uses' => 'Installer\helpdesk\InstallController@configuration'
]);
Route::post('/config', [
    'as' => 'config',
    'uses' => 'Installer\helpdesk\InstallController@configurationcheck'
]);
Route::get('/config-check', [
    'as' => 'database',
    'uses' => 'Installer\helpdesk\InstallController@database'
]);
Route::get('/getting-started/{timezone?}', [
    'as' => 'getting-started',
    'uses' => 'Installer\helpdesk\InstallController@account'
]);
Route::post('/license-code', [
    'as' => 'license-code',
    'uses' => 'Installer\helpdesk\InstallController@accountcheck'
]);


Route::post('/final', [
    'as' => 'final',
    'uses' => 'Installer\helpdesk\InstallController@finalize'
]);
Route::post('/finalpost', [
    'as' => 'postfinal',
    'uses' => 'Installer\helpdesk\InstallController@finalcheck'
]);
Route::post('/postconnection', [
    'as' => 'postconnection',
    'uses' => 'Installer\helpdesk\InstallController@postconnection'
]);
Route::get('/change-file-permission', [
    'as' => 'change-permission',
    'uses' => 'Installer\helpdesk\InstallController@changeFilePermission'
]);


Route::get('create/env', [
    'as' => 'create.env',
    'uses' => 'Installer\helpdesk\InstallController@createEnv'
]);

Route::get('preinstall/check', [
    'as' => 'preinstall.check',
    'uses' => 'Installer\helpdesk\InstallController@checkPreInstall'
]);

Route::get('migrate', [
    'as' => 'migrate',
    'uses' => 'Installer\helpdesk\InstallController@migrate'
]);

Route::get('seed', [
    'as' => 'seed',
    'uses' => 'Installer\helpdesk\InstallController@seed'
]);

Route::get('update/install', [
    'as' => 'update.install',
    'uses' => 'Installer\helpdesk\InstallController@updateInstalEnv'
]);



