<?php
\Event::listen('sms_option', function ($mode) {
        $controllers = new App\Plugins\SMS\Controllers\Msg91SettingsController();
        return $controllers->renderSMSModeCheckbox($mode);
});

\Event::listen('status_sms_option', function ($mode) {
        $controllers = new App\Plugins\SMS\Controllers\Msg91SettingsController();
        return $controllers->renderSTatusSMSModeCheckbox($mode);
});

\Event::listen('sla_sms_label', function ($mode) {
        $controllers = new App\Plugins\SMS\Controllers\Msg91SettingsController();
        return $controllers->renderSlaSmsLabel($mode);
});

\Event::listen('sla_sms_option', function ($mode) {
        $controllers = new App\Plugins\SMS\Controllers\Msg91SettingsController();
        return $controllers->renderSlaSmsOption($mode);
});

\Event::listen('mobile_account_verification_show', function ($mode) {
        $controllers = new App\Plugins\SMS\Controllers\Msg91SettingsController();
        return $controllers->renderMobileAccountVerificationOption($mode);
});

Route::group(['middleware' => 'web'], function () {

    Route::get('sms/settings', [
        'as' =>'sms-settings',
        'uses' => 'App\Plugins\SMS\Controllers\Msg91SettingsController@showMain'
    ]);

    Route::get('sms/providers/settings', [
        'as' =>'providers-settings',
        'uses' => 'App\Plugins\SMS\Controllers\Msg91SettingsController@getForm'
    ]);
    
    Route::post('providers/postsettings', [
        'as' => 'post-sms',
        'uses' =>'App\Plugins\SMS\Controllers\Msg91SettingsController@PostForm'
    ]);

    
    Route::get('sms/template-sets', [
        'as' => 'sms-template-sets',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@showSMSTemplates'
    ]);
    
    Route::get('sms/get-template-sets', [
        'as' => 'get-sms-template',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@getTemplates'
    ]);
    
    Route::get('sms/activate-template-set/{id}', [
        'as' => 'activate-set',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@activateTemplates'
    ]);
    
    Route::get('sms/show-template-set/{id}', [
        'as' => 'show-set',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@showTemplatesType'
    ]);
    
    Route::get('sms/get-template/{id}', [
        'as' => 'get-sms-template-type',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@getTemplatesType'
    ]);
    
    Route::get('sms/edit-template/{id}', [
        'as' => 'edit-template',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@editTemplatesType'
    ]);

    Route::post('sms/create-new-template-set', [
        'as' => 'template-set-createnew',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@createTemplateSet'
    ]);
    
    Route::post('sms/delete-template-set', [
        'as' => 'template-delete',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@deleteTemplateSet'
    ]);
    
    Route::post('sms/post-edit-template', [
        'as' => 'post-edit-template',
        'uses' => 'App\Plugins\SMS\Controllers\SMSTemplateController@postTemplateEdit'
    ]);
});
