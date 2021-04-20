<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::group(['middleware' => ['redirect','install', 'limit.exceeded','dbNotUpdated']], function () {
    Route::get('/', ['as' => '/', 'uses' => 'Client\helpdesk\WelcomepageController@index']);
    Route::get('/js/lang', 'Admin\helpdesk\LanguageController@getLanguageFile')->name('assets.lang');
    Auth::routes();
    Route::post('login', ['uses' => 'Auth\AuthController@postLogin', 'as' => 'post.login']);
    Route::post('auth/logout', ['uses' => 'Auth\AuthController@postLogout', 'as' => 'get.logout']);
    Route::post('auth/register/{api?}', ['uses' => 'Auth\AuthController@postRegister', 'as' => 'post.register']);

    Route::get('social/login/redirect/{provider}/{redirect?}', ['uses' => 'Auth\AuthController@redirectToProvider', 'as' => 'social.login']);
    Route::get('api/social/login/{provider}', ['as' => 'social.login.callback', 'uses' => 'Auth\AuthController@handleProviderCallback']);
    Route::get('social-sync', ['as' => 'social.sync', 'uses' => 'Client\helpdesk\GuestController@sync']);

    Route::get('date/get', 'Admin\helpdesk\SettingsController@getDateByFormat');

    Route::get('login', 'Auth\AuthController@loginView');

    Route::get('account/activate/{token}', ['as' => 'account.activate', 'uses' => 'Auth\AuthController@accountActivate']);

    Route::get('verify-otp', ['as' => 'otp-verification', 'uses' => 'Auth\AuthController@getVerifyOTP']);
    Route::post('verify-otp', ['as' => 'otp-verification', 'uses' => 'Auth\AuthController@verifyOTP']);
    Route::post('resend/otp', ['as' => 'resend-otp', 'uses' => 'Auth\AuthController@resendOTP']);

    //posts update email by email and sends activation link
    Route::post('api/update-email-verification', 'Auth\VerifyAccountController@postUpdateEmailVerification');
    
    //post resends account activation link
    Route::post('api/send-email-verification-link', 'Auth\VerifyAccountController@sendEmailVeirifcation');

    //posts account activate by email-verify token
    Route::post('api/account/activate/{token}', 'Auth\VerifyAccountController@postAccountActivate');

    /*
      |-------------------------------------------------------------------------------
      | Admin Routes
      |-------------------------------------------------------------------------------
      | Here is defining entire routes for the Admin Panel
      |
     */
    Route::group(['middleware'=>'password.confirm'], function() {
    Route::get('show/verify-password', 'Admin\helpdesk\Google2FAController@showVerifyPasswordPopup');
    Route::post('2fa/enable', 'Admin\helpdesk\Google2FAController@enableTwoFactor');
    Route::get('2fa/validate', 'Auth\AuthController@getValidateToken');
    Route::post('2fa/setupValidate', 'Auth\AuthController@postSetupValidateToken');
    Route::post('2fa/disable/{userId?}', 'Admin\helpdesk\Google2FAController@disableTwoFactor');
    });
    Route::post('2fa/loginValidate', 'Auth\AuthController@postLoginValidateToken');
    Route::post('verify/password', 'Admin\helpdesk\Google2FAController@verifyPassword');

    
   

    Route::group(['middleware' => 'role.agent'], function () {
        Route::get('update-order-details', ['as' => 'update-order-details',
            'uses' => 'Update\AutoUpdateController@getOrderDetails'
        ]);

        Route::post('update-order-details', ['as' => 'update-order-details',
            'uses' => 'Update\AutoUpdateController@updateOrderDetails'
        ]);

        Route::get('check-updates', ['as' => 'check-updates',
            'uses' => 'Update\AutoUpdateController@viewUpdates'
        ])->middleware(['roles']);

        Route::get('api/check-updates', ['as' => 'check-updates',
            'uses' => 'Update\AutoUpdateController@checkForUpdates'
        ])->middleware(['roles']);

        Route::get('download-update', ['as' => 'download-updates',
            'uses' => 'Update\AutoUpdateController@downloadUpdate'
        ]);
        Route::post('auto-update-application', ['as' => 'auto-update-application',
            'uses' => 'Update\AutoUpdateController@update'
        ]);
        Route::post('create-backup', ['as' => 'create-application-backup',
            'uses' => 'Update\AutoUpdateController@createBackUp'
        ]);
        Route::get('get-latest-release', ['as' => 'create-application-backup',
            'uses' => 'Update\AutoUpdateController@getLatestRelease'
        ]);

        Route::get('get-latest-release', ['as' => 'create-application-backup',
            'uses' => 'Update\AutoUpdateController@getLatestRelease'
        ]);

        Route::post('auto-update-database', ['as' => 'auto-update-database',
            'uses' => 'Update\AutoUpdateController@updateDatabase'
        ]);

        
    });

    //Notification marking
    Route::post('mark-read/{id}', 'Common\NotificationController@markRead');
    Route::post('mark-all-read/{id}', 'Common\NotificationController@markAllRead');

    /**
     * @deprecated
     *============================================================================
     *  Routes under this block as mixed live and deprecated some of them are
     *  duplicate. Need to thoroughly test each route and remove unused route
     *  accordingly.
     *============================================================================
     */

    Route::get('ticket/recur', [
        'as'   => 'ticket.recure.get',
        'uses' => 'Admin\helpdesk\TicketRecurController@create'
    ])->middleware(['roles']);

    Route::post('/api/ticket/recur', 'Admin\helpdesk\TicketRecurController@createOrUpdateRecur');

    Route::get('/api/ticket/recur/{recurId}', 'Admin\helpdesk\TicketRecurController@getRecurTicket');

    Route::get('recur/{id}/edit', [
        'as'   => 'ticket.recure.edit',
        'uses' => 'Admin\helpdesk\TicketRecurController@edit'
    ])->middleware(['roles']);

    Route::get('recur/list', [
        'as'   => 'ticket.recure.list',
        'uses' => 'Admin\helpdesk\TicketRecurController@index'
    ])->middleware(['roles']);

    Route::delete('api/recur-delete/{id}', [
        'as'   => 'ticket.recure.delete',
        'uses' => 'Admin\helpdesk\TicketRecurController@delete'
    ])->middleware(['roles']);

    Route::get('agent/recur/list', [
        'as' => 'tools.recure.list',
        'uses' => 'Admin\helpdesk\TicketRecurController@index'
    ])->middleware(['role.agent']);

    Route::get('api/recur-list', 'Admin\helpdesk\TicketRecurController@getIndex')->middleware(['roles']);
    /*
    This is new api for showing datatable after converson to vue
     */
    Route::get('api/agent/recur-list', 'Admin\helpdesk\TicketRecurController@getIndex')->middleware(['role.agent']);

    Route::get('agent/ticket/recur', [
        'as' => 'tools.recure.get',
        'uses' => 'Admin\helpdesk\TicketRecurController@create'
    ])->middleware(['role.agent']);

    Route::get('agent/recur/{id}/edit', [
        'as' => 'tools.recure.edit',
        'uses' => 'Admin\helpdesk\TicketRecurController@edit'
    ])->middleware(['role.agent']);

    /**
     * Notification api
     */
    Route::get('notification/api/{userid?}', [
        'as'   => 'notification.api',
        'uses' => 'Agent\helpdesk\Notifications\Notification@appNotification'
    ])->middleware(['role.agent'])->where('userid', '[0-9]+');

    Route::get('notification/api/seen/{userid?}', [
        'as'   => 'notification.api.seen',
        'uses' => 'Agent\helpdesk\Notifications\Notification@notificationSeen'
    ])->middleware(['role.agent'])->where('userid', '[0-9]+');

    Route::get('notification/api/unseen/count/{userid?}', [
        'as'   => 'notification.api.unseen.count',
        'uses' => 'Agent\helpdesk\Notifications\Notification@notificationUnSeenCount'
    ])->middleware(['role.agent'])->where('userid', '[0-9]+');

    Route::get('notifications-list', ['as' => 'notification.list', 'uses' => 'Common\NotificationController@show']);
    Route::post('notification-delete/{id}', ['as' => 'notification.delete', 'uses' => 'Common\NotificationController@delete']);
    Route::get('notifications-list/delete', ['as' => 'notification.delete.all', 'uses' => 'Common\NotificationController@deleteAll']);

    Route::get('settings-notification', ['as' => 'notification.settings', 'uses' => 'Admin\helpdesk\SettingsController@notificationSettings']);
    Route::get('delete-read-notification', 'Admin\helpdesk\SettingsController@deleteReadNoti');
    Route::post('delete-notification-log', 'Admin\helpdesk\SettingsController@deleteNotificationLog');
   
    Route::resource('departments', 'Admin\helpdesk\DepartmentController', ['except' => ['show']]);
    // for departments module, for CRUD
    Route::get('departments-table-data', ['as' => 'get-department-table-data', 'uses' => 'Admin\helpdesk\DepartmentController@getDepartmentTable']);
    Route::get('/department/{id}', ['as' => 'department.profile.show', 'uses' => 'Admin\helpdesk\DepartmentController@DepartmentShow']);

    Route::get('/department/search/manager', ['as' => 'department.search.manager', 'uses' => 'Admin\helpdesk\DepartmentController@ManagerSearch']);

    Route::get('/department/userprofile/{id}', ['as' => 'department.userprofile.show', 'uses' => 'Admin\helpdesk\DepartmentController@DepartmentUserprofile']);

    Route::get('dept-chart/{id}', 'Admin\helpdesk\DepartmentController@deptChartData');
    Route::post('dept-chart-range/{id}/{date1}/{date2}', ['as' => 'post.org.chart', 'uses' => 'Admin\helpdesk\DepartmentController@deptChartData']);


    //Route for listing users based on it'd field type value(role, active, ban), department and team
    Route::get('api/admin/get-users-list', 'Admin\helpdesk\UserListController@getUserList');

    //Route to create and update agent/admin
    Route::post('api/admin/agent', 'Admin\helpdesk\AgentController@createUpdateAgent');

    Route::get('recaptcha', ['as' => 'recaptcha', 'uses' => 'Admin\helpdesk\SettingsController@recaptcha']);

    Route::get('api/admin/recaptcha', 'Admin\helpdesk\SettingsController@getRecaptcha');
            

    Route::post('store/recaptcha','Admin\helpdesk\SettingsController@postRecaptcha');


    //Route to get agent/admin existing data for edit based on agentId
    Route::get('api/admin/agent/{agentId}', 'Admin\helpdesk\AgentController@editAgent');

    //Route to get personal agent/admin existing data only
    Route::get('api/agent-info', 'Admin\helpdesk\AgentController@getOwnDetails');

    //Route to get specific agent/admin existing data to display it in view
    Route::get('api/admin/get-agent/{agentId}', 'Admin\helpdesk\AgentController@getAgents');

    //Route to change agent/admin role, password, active field value
    Route::get('api/admin/change-agent/{agentId}', 'Admin\helpdesk\AgentController@changeAgent');

    Route::resource('teams', 'Admin\helpdesk\TeamController', ['except' => 'show']); // in teams module, for CRUD
    Route::get('team-table-data', ['as' => 'get-team-table-data', 'uses' => 'Admin\helpdesk\TeamController@getTeamTable']);

    Route::get('/assign-teams/{id}', ['as' => 'teams.profile.show', 'uses' => 'Admin\helpdesk\TeamController@teamShow']);

    Route::get('getshow/{id}', ['as' => 'teams.getshow.list', 'uses' => 'Admin\helpdesk\TeamController@getshow']);
    Route::resource('agents', 'Admin\helpdesk\AgentController', ['except' => ['show']]); // in agents module, for CRUD
    /*
     * Templates
     */
    Route::resource('templates', 'Common\TemplateController', ['except'=>['index',]]);
    Route::get('get-templates', 'Common\TemplateController@GetTemplates');
    Route::get('templates-delete', 'Common\TemplateController@destroy');

    Route::resource('template-sets', 'Common\TemplateSetController', ['except'=>['edit']]); // in template module, for CRUD
    Route::delete('delete-sets/{id}', ['as' => 'sets.delete', 'uses' => 'Common\TemplateSetController@deleteSet']);
    Route::get('get-faveo-template-table-data/{id}', ['as' => 'template-table-data', 'uses' => 'Common\TemplateController@getTemplateTableData']);
    Route::get('show-template/{id}', ['as' => 'show.templates', 'uses' => 'Common\TemplateController@showTemplate']);
    Route::get('activate-templateset/{name}', ['as' => 'active.template-set', 'uses' => 'Common\TemplateSetController@activateSet']);
    Route::resource('template', 'Admin\helpdesk\TemplateController'); // in template module, for CRUD
    Route::get('list-directories', 'Admin\helpdesk\TemplateController@listdirectories');
    Route::get('activate-set/{dir}', ['as' => 'active.set', 'uses' => 'Admin\helpdesk\TemplateController@activateset']);
    Route::get('list-templates/{template}/{directory}', ['as' => 'template.list', 'uses' => 'Admin\helpdesk\TemplateController@listtemplates']);
    Route::get('read-templates/{template}/{directory}', ['as' => 'template.read', 'uses' => 'Admin\helpdesk\TemplateController@readtemplate']);
    Route::patch('write-templates/{contents}/{directory}', ['as' => 'template.write', 'uses' => 'Admin\helpdesk\TemplateController@writetemplate']);
    Route::post('create-templates', ['as' => 'template.createnew', 'uses' => 'Admin\helpdesk\TemplateController@createtemplate']);
    Route::get('delete-template/{template}/{path}', ['as' => 'templates.delete', 'uses' => 'Admin\helpdesk\TemplateController@deletetemplate']);
    Route::get('getdiagno', ['as' => 'getdiagno', 'uses' => 'Admin\helpdesk\TemplateController@formDiagno']); // for getting form for diagnostic

    Route::post('api/postdiagno', ['as' => 'postdiagno', 'uses' => 'Admin\helpdesk\TemplateController@postDiagno']); // for getting form for diagnostic
    Route::resource('helptopic', 'Admin\helpdesk\HelptopicController', ['except'=> ['show']]); // in helptopics module, for CRUD

    Route::resource('sla', 'SLA\SlaController', ['except'=> ['show', 'store', 'destroy']]); // in SLA Plan module, for CRUD


    Route::get('form/user', [
        'as'   => 'form.user',
        'uses' => 'Admin\helpdesk\FormController@user'
    ]);

    Route::get('forms/create', [
        'as'   => 'forms.create',
        'uses' => 'Admin\helpdesk\FormController@create'
    ]);

    Route::get('job-scheduler', ['as' => 'get.job.scheder', 'uses' => 'Admin\helpdesk\CronSettingsController@getSchedular']); //to get ob scheduler form page

    Route::patch('post-scheduler', ['as' => 'post.job.scheduler', 'uses' => 'Admin\helpdesk\CronSettingsController@postSchedular']); //to update job scheduler


    Route::get('getcompany', ['as' => 'getcompany', 'uses' => 'Admin\helpdesk\SettingsController@getCompanyPage'])->middleware(['role.admin']); // direct to company setting page
    Route::get('getCompanyDetails', ['as' => 'getCompanyDetails', 'uses' => 'Admin\helpdesk\SettingsController@getCompanyDetails'])->middleware(['role.admin']);


    Route::post('postcompany', 'Admin\helpdesk\SettingsController@postCompany')->middleware(['role.admin']);


    Route::post('cdn/settings', ['as' => 'cdn.settings', 'uses' => 'Admin\helpdesk\SettingsController@cdnSettings']); // direct to system setting page

    // Route for system setting update page
    Route::get('getsystem', ['as' => 'getsystem', 'uses' => 'Admin\helpdesk\Settings\SystemSettingController@getSystemSettingUpdatePage']);

    // Route for system setting update API
    Route::post('api/admin/update-system-setting', 'Admin\helpdesk\Settings\SystemSettingController@updateSystemSetting');

    // Route for system setting, to get existing data
    Route::get('api/admin/get-system-setting', 'Admin\helpdesk\Settings\SystemSettingController@getSystemSetting');

    Route::get('getticket', ['as' => 'getticket', 'uses' => 'Admin\helpdesk\SettingsController@getticket']); // direct to ticket setting page

    Route::get('system-backup', ['as' => 'system-backup', 'uses' => 'Admin\helpdesk\BackupController@index'])->middleware(['role.admin']); // direct to ticket setting page

    Route::get('api/backup/list', ['as' => 'backup.apilist', 'uses' => 'Admin\helpdesk\BackupController@getIndex']);

    Route::post('api/backup/take-system-backup', ['as' => 'take-system-backup', 'uses' => 'Admin\helpdesk\BackupController@takeSystemBackup'])->middleware(['role.admin']);

    Route::get('backup-complete', ['as' => 'backup-complete', 'uses' => 'Admin\helpdesk\BackupController@completeBackup']);

    Route::get('api/backup-path', ['as' => 'backup-path', 'uses' => 'Admin\helpdesk\BackupController@backupPath']);

    Route::get('api/get-backup-path', ['as' => 'get-backup-path', 'uses' => 'Admin\helpdesk\BackupController@getBackupPath']);

    Route::get('api/download-backup/{id}/{type}', ['as' => 'download-backup', 'uses' => 'Admin\helpdesk\BackupController@downloadBackup'])->middleware(['role.admin']);

    Route::delete('api/delete-backup/{id}', ['as' => 'download-backup', 'uses' => 'Admin\helpdesk\BackupController@deleteBackup']);

    Route::patch('api/postticket', 'Admin\helpdesk\SettingsController@postticket'); // Updating the Ticket table with requests
    // Route to get ticket settings Data
    Route::get('api/getTicketSetting', 'Admin\helpdesk\SettingsController@getTicketSettingsDetails');
    Route::get('getemail', ['as' => 'getemail', 'uses' => 'Admin\helpdesk\SettingsController@getemail']); // direct to email setting page

    Route::get('ticket/tooltip', ['as' => 'ticket.tooltip', 'uses' => 'Agent\helpdesk\TicketController@getTooltip']);

    Route::patch('postemail/{id}', 'Admin\helpdesk\SettingsController@postemail'); // Updating the Email table with requests
    Route::get('alert', ['as' => 'getalert', 'uses' => 'Admin\helpdesk\SettingsController@getalert']); // direct to alert setting page

    Route::patch('alert', 'Admin\helpdesk\SettingsController@postalert'); // Updating the Alert table with requests
    // Templates

    Route::get('security', ['as' => 'security.index', 'uses' => 'Admin\helpdesk\SecurityController@index']); // direct to security setting page
    Route::resource('close-workflow', 'Admin\helpdesk\CloseWrokflowController', ['except' =>['show', 'edit', 'create']]); // direct to security setting page
    
    //Route to update close-workflow settings based on Id
    Route::patch('api/close-workflow-update', 'Admin\helpdesk\CloseWrokflowController@update');
    //Route to get close-workflow settings data based on Id
    Route::get('api/close-workflow', 'Admin\helpdesk\CloseWrokflowController@getworkflowCloseSetingsDetails');

    Route::patch('security/{id}', ['as' => 'securitys.update', 'uses' => 'Admin\helpdesk\SecurityController@update']); // direct to security setting page

    Route::get('setting-status', ['as' => 'statuss.index', 'uses' => 'Admin\helpdesk\StatusController@getStatuses']); // direct to status setting page


    Route::get('get-status-table', ['as' => 'get-status-table', 'uses' => 'Admin\helpdesk\StatusController@getStatusesTable']);



    Route::patch('status-update/{id}', ['as' => 'statuss.update', 'uses' => 'Admin\helpdesk\StatusController@editStatuses']);

    Route::get('status-create', ['as' => 'statuss.create', 'uses' => 'Admin\helpdesk\StatusController@createStatuses']);


    Route::get('status/edit/{id}', ['as' => 'status.edit', 'uses' => 'Admin\helpdesk\StatusController@getEditStatuses']);
    Route::post('status-create', ['as' => 'statuss.store', 'uses' => 'Admin\helpdesk\StatusController@storeStatuses']);
    Route::delete('status-delete/{id}', ['as' => 'statuss.delete', 'uses' => 'Admin\helpdesk\StatusController@deleteStatuses']);
    Route::get('getratings', ['as' => 'ratings.index', 'uses' => 'Admin\helpdesk\SettingsController@RatingSettings']);

    Route::get('deleter/{rating}', ['as' => 'ratings.delete', 'uses' => 'Admin\helpdesk\SettingsController@RatingDelete']);

    Route::get('create-ratings', ['as' => 'rating.create', 'uses' => 'Admin\helpdesk\SettingsController@createRating']);
    Route::post('store-ratings', ['as' => 'rating.store', 'uses' => 'Admin\helpdesk\SettingsController@storeRating']);

    Route::get('editratings/{slug}', ['as' => 'rating.edit', 'uses' => 'Admin\helpdesk\SettingsController@editRatingSettings']);
    Route::patch('postratings/{slug}', ['as' => 'settings.rating', 'uses' => 'Admin\helpdesk\SettingsController@PostRatingSettings']);
    Route::get('remove-user-org/{id}/{org_id}', ['as' => 'removeuser.org', 'uses' => 'Agent\helpdesk\UserController@removeUserOrg']);

    Route::get('form-groups', ['as' => 'form-groups', 'uses' => 'Utility\FormController@getFormGroupIndex']);

    Route::get('form-group/create', ['as' => 'form-group/create', 'uses' => 'Utility\FormController@getFormGroupCreate']);

    Route::get('form-group/edit/{groupId}', ['as' => 'form-group/edit', 'uses' => 'Utility\FormController@getFormGroupEdit']);

    Route::get('admin', ['as' => 'setting', 'uses' => 'Admin\helpdesk\SettingsController@settings']);


    Route::get('plugins', ['as' => 'plugins', 'uses' => 'Common\SettingsController@plugins']);
    Route::get('getplugin', ['as' => 'get.plugin', 'uses' => 'Common\SettingsController@getPlugin']);


    Route::get('modules', ['as' => 'modules', 'uses' => 'Common\SettingsController@modules']);
    Route::get('getmodules/index', ['as' => 'get.modules.index', 'uses' => 'Common\SettingsController@Getmodules']);

    Route::post('change/modules/status', ['as' => 'change.module.status', 'uses' => 'Common\SettingsController@ChangemoduleStatus']);

    Route::get('getconfig', ['as' => 'get.config', 'uses' => 'Common\SettingsController@fetchConfig']);

    Route::post('plugin/status/{slug}', ['as' => 'status.plugin', 'uses' => 'Common\SettingsController@StatusPlugin']);
    //Routes for showing language table and switching language
    Route::get('languages', ['as' => 'LanguageController', 'uses' => 'Admin\helpdesk\LanguageController@index']);

    Route::get('get-languages', ['as' => 'getAllLanguages', 'uses' => 'Admin\helpdesk\LanguageController@getLanguages']);
    Route::get('change-language/{lang}', ['as' => 'lang.switch', 'uses' => 'Admin\helpdesk\LanguageController@switchLanguage']);
    //Route for download language template package
    Route::get('/download-template', ['as' => 'download', 'uses' => 'Admin\helpdesk\LanguageController@download']);

    Route::get('generate-api-key', 'Admin\helpdesk\SettingsController@GenerateApiKey'); // route to generate api key


    Route::get('workflow', ['as' => 'workflow', 'uses' => 'Admin\helpdesk\WorkflowController@index']);

    Route::get('workflow/create', ['as' => 'workflow.create', 'uses' => 'Admin\helpdesk\WorkflowController@create']);

    Route::get('workflow/edit/{id}', ['as' => 'workflow.edit', 'uses' => 'Admin\helpdesk\WorkflowController@edit']);

    /*
     * Api Settings
     */
    Route::get('api', ['as' => 'api.settings.get', 'uses' => 'Common\ApiSettingController@apiSettingUpdatePage']);

    Route::post('api/admin/update-api-setting', ['as' => 'api.settings.post', 'uses' => 'Common\ApiSettingController@updateApiSetting']);

    Route::get('api/admin/get-api-setting', 'Common\ApiSettingController@getApiSetting');

    /*
     * Error and debugging
     */
    //route for showing error and debugging setting form page
    Route::get('error-and-debugging-options', ['as' => 'err.debug.settings', 'uses' => 'Admin\helpdesk\ErrorAndDebuggingController@showSettings']);

    //route for submit error and debugging setting form page
    Route::post('post-settings', ['as'   => 'post.error.debug.settings',
        'uses' => 'Admin\helpdesk\ErrorAndDebuggingController@postSettings',]);
    //route to error logs table page
    Route::get('show-error-logs', [
        'as'   => 'error.logs',
        'uses' => 'Admin\helpdesk\ErrorAndDebuggingController@showErrorLogs',
    ]);

    Route::post('user/create/api', [
        'as'   => 'user.create.api',
        'uses' => 'Agent\helpdesk\UserController@createUserApi',
    ]);

    Route::get('user/edit/api/{id}', [
        'as'   => 'user.edit.api',
        'uses' => 'Agent\helpdesk\UserController@editUserApi',
    ]);

    Route::post('user/update/api/{id}', [
        'as'   => 'user.update.api',
        'uses' => 'Agent\helpdesk\UserController@updateUserApi',
    ]);

    /**
     * Organisation
     */
    Route::post('organisation/create/api', [
        'as'   => 'org.create.api',
        'uses' => 'Agent\helpdesk\OrganizationController@createOrgApi',
    ]);

    Route::get('organisation/edit/api/{id}', [
        'as'   => 'org.edit.api',
        'uses' => 'Agent\helpdesk\OrganizationController@editOrgApi',
    ]);

    Route::post('organisation/update/api/{id}', [
        'as'   => 'org.update.api',
        'uses' => 'Agent\helpdesk\OrganizationController@updateOrgApi',
    ]);

    /**
     * Labels
     */
    Route::resource('labels', 'Admin\helpdesk\Label\LabelController', ['except' => ['show']]);

    Route::get('labels-ajax', ['as' => 'labels.ajax', 'uses' => 'Admin\helpdesk\Label\LabelController@ajaxTable']);
    Route::get('labels/delete/{id}', ['as' => 'labels.destroy', 'uses' => 'Admin\helpdesk\Label\LabelController@destroy']);

    Route::get('/sla/business-hours/index', ['as' => 'sla.business.hours.index', 'uses' => 'SLA\BusinessHoursController@index']);
    Route::get('/sla/business-hours/getindex', ['as' => 'sla.business.hours.getindex', 'uses' => 'SLA\BusinessHoursController@getindex']);

    Route::get('/sla/business-hours/create', ['as' => 'sla.business.hours.create', 'uses' => 'SLA\BusinessHoursController@create']);

    Route::get('/api/business-hours/create', ['as' => 'api.business.hours.create', 'uses' => 'SLA\BusinessHoursController@createApi']);

    Route::post('/sla/business-hours/store', ['as' => 'sla.business.hours.store', 'uses' => 'SLA\BusinessHoursController@store']);

    Route::get('/sla/business-hours/edit/{id}', ['as' => 'sla.business.hours.edit', 'uses' => 'SLA\BusinessHoursController@edit']);
    Route::get('/api/business-hours/edit/{id}', ['as' => 'api.business.hours.edit', 'uses' => 'SLA\BusinessHoursController@editApi']);
    Route::post('/sla/business-hours/update/{id}', ['as' => 'sla.business.hours.update', 'uses' => 'SLA\BusinessHoursController@update']);

    Route::delete('/sla/business-hours/delete/{id}', ['as' => 'sla.business.hours.delete', 'uses' => 'SLA\BusinessHoursController@businessHoursDelete']);

    /*
     * Ticket_Type Settings
     */

    Route::get('ticket-types', ['as' => 'ticket.type.index', 'uses' => 'Type\TicketTypeController@typeIndex']);
    Route::get('ticket-types/get_index', ['as' => 'ticket.type.index1', 'uses' => 'Type\TicketTypeController@typeIndex1']);

    Route::get('ticket-types/create', ['as' => 'ticket.type.create', 'uses' => 'Type\TicketTypeController@typeCreate']);

    Route::post('ticket-types/create1', ['as' => 'ticket.type.create1', 'uses' => 'Type\TicketTypeController@typeCreate1']);

    Route::post('ticket-types/{id}/edit1', ['as' => 'ticket.type.edit1', 'uses' => 'Type\TicketTypeController@typeEdit1']);

    Route::get('ticket-types/{id}/edit', ['as' => 'ticket.type.edit', 'uses' => 'Type\TicketTypeController@typeEdit']);

    Route::delete('ticket-types/{id}/destroy', ['as' => 'ticket.type.destroy', 'uses' => 'Type\TicketTypeController@destroy']);

    Route::get('get/sla/{id}', ['as' => 'get.sla', 'uses' => 'SLA\ApplySla@minSpent']);

    Route::get('import', ['as' => 'import.data', 'uses' => 'Admin\helpdesk\ImportController@getImport']);
    Route::post('import/import', ['as' => 'import', 'uses' => 'Admin\helpdesk\ImportController@import']);

    //Route to get list of agents
    Route::get('get-agents-list', ['as' => 'agent.list', 'uses' => 'Admin\helpdesk\AgentController@getAgentList']);
    Route::get('clean-dummy-data', ['as' => 'clean-database', 'uses' => 'Admin\helpdesk\SettingsController@getCleanUpView']);
    Route::post('post-clean-dummy-data', ['as' => 'post-clean-database', 'uses' => 'Admin\helpdesk\SettingsController@postCleanDummyData']);

    Route::get('agent/dept/search', ['as' => 'agent.dept.search', 'uses' => 'Admin\helpdesk\AgentController@find']);

    // Route for user options update blade page
    Route::get('user-options', ['as' => 'users-options', 'uses' => 'Admin\helpdesk\Settings\UserOptionsController@userOptionsUpdatePage']);

    // Route for user options update API
    Route::post('api/admin/user-options', ['as' => 'users-options', 'uses' => 'Admin\helpdesk\Settings\UserOptionsController@updateUserOptions']);
    // Route to get user options existing data 
    Route::get('api/admin/get-user-options', 'Admin\helpdesk\Settings\UserOptionsController@getUserOptions');

    /*
      Browser Notification
     */
    Route::get('browser-notification', [ 'as' => 'browser-notification', 'uses' => 'Admin\helpdesk\SettingsController@browserNotification']);

    Route::post('browser-notification/edit', [ 'as' => 'browser-notification-edit', 'uses' => 'Admin\helpdesk\SettingsController@browserNotificationsetting']);

    /**
     * Dashboard Statistics settings is not used in agent panel dashboard
     * So, need to be removed, currently commenting it
     */
    // Route::get('dashboard-settings', ['as' => 'dashboard-statistics', 'uses' => 'Admin\helpdesk\SettingsController@statisticsSettings']);
    // Route::post('dashboard-settings', ['as' => 'dashboard-statistics', 'uses' => 'Admin\helpdesk\SettingsController@postStatisticsSettings']);
    Route::post('verify-php-path', ['as' => 'verify-cron', 'uses' => 'Admin\helpdesk\PHPController@checkPHPExecutablePath']);

    Route::get('widgets/{type?}',['as'=>  'widgets', 'uses' => 'Admin\helpdesk\WidgetController@getWidgetView']);
    Route::get('api/list-widgets/{type?}', ['as'=>  'widget.list', 'uses' => 'Admin\helpdesk\WidgetController@getWidgetList']);
    Route::post('api/update-widget/{widget}', ['as'=>  'widget.list', 'uses' => 'Admin\helpdesk\WidgetController@updateWidget']);

    Route::get('websockets/settings', ['as' => 'websockets.view', 'uses' => 'Admin\helpdesk\WebSocketSettingsController@showWebSockets']);
    Route::get('api/get-websockets-config', ['as' => 'websockets.config', 'uses' => 'Admin\helpdesk\WebSocketSettingsController@getWebSocketSettings']);
    Route::post('api/update-websockets-config', ['as' => 'websockets.config.post', 'uses' => 'Admin\helpdesk\WebSocketSettingsController@updateWebSocketSettings']);

    Route::resource('user', 'Agent\helpdesk\UserController'); /* User router is used to control the CRUD of user */

    Route::get('agent/{id}', ['as' => 'agent.show', 'uses' => 'Agent\helpdesk\UserController@show']);

    Route::get('user-export', ['as' => 'user.export', 'uses' => 'Agent\helpdesk\UserController@getExportUser']); /* User router is used to control the CRUD of user */

    Route::get('user-export-download', 'Agent\helpdesk\UserController@exportUser');

    /* User router is used to control the CRUD of user */

    Route::get('org/department/search', ['as' => 'org.department.search', 'uses' => 'Agent\helpdesk\UserController@findOrgDepartment']);

    Route::get('user-list', ['as' => 'user.list', 'uses' => 'Agent\helpdesk\UserController@userList']);

    ////////////////////////user apis/////////////////////////////
    Route::post('role-change-admin/{id}', 'Agent\helpdesk\UserController@changeRoleAdmin');
    Route::post('role-change-agent/{id}','Agent\helpdesk\UserController@changeRoleAgent');
    Route::post('role-change-user/{id}','Agent\helpdesk\UserController@changeRoleUser');
    Route::get('get/random/password','Agent\helpdesk\UserController@randomPassword');
    Route::post('changepassword/{id}','Agent\helpdesk\UserController@randomPostPassword');

    Route::get('api/get-user/view/{id}','Agent\helpdesk\UserController@getUserInfo');

    Route::delete('api/remove-user-org/{id}/{org_id}', ['as' => 'removeuser.org', 'uses' => 'Agent\helpdesk\UserController@removeUserOrg']);

    Route::post('api/user-org-assign/{id}', ['as' => 'user.assigns.org', 'uses' => 'Agent\helpdesk\UserController@userAssignOrg']);

    Route::get('user-chat-report', 'Agent\helpdesk\UserController@userChartData');

    ///////////////////////////////////////////////////////////////////////////////////////
    Route::resource('organizations', 'Agent\helpdesk\OrganizationController'); /* organization router used to deal CRUD function of organization */

    Route::delete('org/delete/{id}', 'Agent\helpdesk\OrganizationController@destroy');


    Route::get('org-list-data', 'Agent\helpdesk\OrganizationController@orgListData');


    Route::get('api/organization/view/{id}', 'Agent\helpdesk\OrganizationController@organizationViewApi');

    Route::post('add/organization/manager', 'Agent\helpdesk\OrganizationController@postOrgManager');

    Route::get('search/organization/manager/{id}','Agent\helpdesk\OrganizationController@getOrgManager');

    Route::post('create-update-org-dept','Agent\helpdesk\OrganizationController@postOrgDepartment');

    Route::get('api/org-department-list/{id}','Agent\helpdesk\OrganizationController@getOrgDepartmentInfo');

    Route::delete('delete-org-dept/{id}','Agent\helpdesk\OrganizationController@deleteOrgDept');

    Route::get('org-user-list/{org_id}', 'Agent\helpdesk\OrganizationController@getOrgUserList');

    Route::get('org-dept-list/{org_id}', 'Agent\helpdesk\OrganizationController@getOrgDeptList');

    Route::get('org-dept-user-list/{org_id}/{deptid}', 'Agent\helpdesk\OrganizationController@getOrgDeptUserList');


    Route::get('org-chart/{id}', 'Agent\helpdesk\OrganizationController@orgChartData');
    
    Route::post('org/edit/dept/{id}', ['as' => 'org.post.changeorgdept', 'uses' => 'Agent\helpdesk\OrganizationController@EditOrgDept']);

    Route::post('fork/ticket/{id}', [
        'as'   => 'fork.ticket',
        'uses' => 'Agent\helpdesk\TicketsWrite\TicketActionController@createFork',
    ]);

    // For helpsection for admin

    Route::post('api/thread/reply/{ticket}', ['as' => 'ticket.reply', 'uses' => 'Common\TicketsWrite\TicketReplyController@postReply']); /*  Patch Thread Reply */



    Route::get('profile', ['as' => 'profile', 'uses' => 'Agent\helpdesk\UserController@getProfile']); /*  User profile get  */

    Route::get('api/profile/info', 'Agent\helpdesk\UserController@getProfileInfo'); /*  User profile get  */

    Route::get('profile-edit', ['as' => 'agent-profile-edit', 'uses' => 'Agent\helpdesk\UserController@getProfileedit']); /*  User profile edit get  */

    Route::post('verify-number', ['as' => 'agent-verify-number', 'uses' => 'Agent\helpdesk\UserController@resendOTP']);

    Route::patch('agent-profile', ['as' => 'agent-profile', 'uses' => 'Agent\helpdesk\UserController@profileEdit']); /* User Profile Post */

    Route::patch('agent-profile-password/{id}', 'Agent\helpdesk\UserController@postProfilePassword'); /*  Profile
    Password Post */

    Route::get('canned/list', ['as' => 'canned.list', 'uses' => 'Agent\helpdesk\CannedController@index']); /* Canned list */
    Route::get('api/canned/list', ['as' => 'canned.apilist', 'uses' => 'Agent\helpdesk\CannedController@getIndex']); /* Canned list */
    Route::post('api/agent/canned-response', ['as' => 'canned.createupdate', 'uses' => 'Agent\helpdesk\CannedController@createUpdateCannedResponse']); /* Create/Update Canned Response */

    Route::delete('api/canned/delete/{id}', ['as' => 'canned.delete', 'uses' => 'Agent\helpdesk\CannedController@destroy']); /* delete canned response */

    Route::get('api/canned/edit/{id}', ['as' => 'canned.edit', 'uses' => 'Agent\helpdesk\CannedController@editApi']); /* Canned edit */
    Route::post('/newticket/post', ['as' => 'post.newticket', 'uses' => 'Agent\helpdesk\TicketController@post_newticket'])->middleware('role.agent'); /*  Post Create New Ticket */
    Route::post('/newbatchticket/post', ['as' => 'post.newbatchticket', 'uses' => 'Agent\helpdesk\TicketController@post_newbatchticket'])->middleware('role.agent'); /*  Post Create New Ticket */

    Event::listen('batch.ticket.notify', function ($notify) {
        App\Jobs\Notifications::dispatch('in-app-notification', $notify, 'batch-ticket');
    });

    Event::listen('batch.ticket.email.notify', function ($email_data) {
        App\Jobs\Notifications::dispatch('email-notification', $email_data, null, 'batch-ticket');
    });

    Route::get('/thread/{ticketId}', ['as' => 'ticket.thread', 'uses' => 'Agent\helpdesk\TicketController@thread']); /*  Get Thread by ID */
    Route::patch('/internal/note/{id}', ['as' => 'Internal.note', 'uses' => 'Agent\helpdesk\TicketController@InternalNote']); /*  Patch Internal Note */
    Route::post('/thread/reply', ['as' => 'ticket.reply', 'uses' => 'Agent\helpdesk\TicketController@reply']); /*  Patch Thread Reply */
    Route::patch('/ticket/assign/{id?}', ['as' => 'assign.ticket', 'uses' => 'Agent\helpdesk\TicketController@assign']); /*  Patch Ticket assigned to whom */
    Route::post('/ticket/update/api/{id}', ['as' => 'ticket.post.edit', 'uses' => 'Agent\helpdesk\TicketsWrite\TicketActionController@editTicket']);

    // this API has been pointed to a new controller which has clean code

    Route::get('/ticket/print/{id}', ['as' => 'ticket.print', 'uses' => 'Agent\helpdesk\TicketController@ticket_print']); /*  Get Print Ticket */

    Route::get('/ticket/surrender/{id}', ['as' => 'ticket.surrender', 'uses' => 'Agent\helpdesk\TicketController@surrender']); /*  Get Ticket Surrender */
    Route::post('/ticket/change-status/{id}/{id2}', ['as' => 'change.status', 'uses' => 'Agent\helpdesk\TicketController@changeStatus']);

    Route::get('agen', 'Agent\helpdesk\DashboardController@ChartData');
    /* get image */

    Route::get('get-canned/{id}', ['as' => 'get_canned', 'uses' => 'Agent\helpdesk\CannedController@getCanned']);
    Route::post('lock', ['as' => 'lock', 'uses' => 'Agent\helpdesk\TicketController@lock']);
    Route::post('user-org-assign/{id}', ['as' => 'user.assign.org', 'uses' => 'Agent\helpdesk\UserController@UserAssignOrg']);
    Route::patch('/user-org/{id}', 'Agent\helpdesk\UserController@userCreateOrganization');
    Route::post('/head-org', 'Agent\helpdesk\OrganizationController@createOrgManager');
    Route::post('/edit/head-org', 'Agent\helpdesk\OrganizationController@editOrgManager');
    Route::get('canned/create', ['as' => 'canned.create', 'uses' => 'Agent\helpdesk\CannedController@create']); /* Canned create */

    Route::get('canned/{id}/edit', ['as' => 'canned.edit', 'uses' => 'Agent\helpdesk\CannedController@edit']); /* Canned edit */


    Route::get('/newticket/{parameter?}/{value?}', ['as' => 'newticket', 'uses' => 'Agent\helpdesk\TicketController@newticket'])
        ->middleware('role.agent'); /*  Get Create New Ticket */


    Route::get('/thread/{id}/{parameter?}/{value?}', ['as' => 'ticket.thread', 'uses' => 'Agent\helpdesk\TicketController@thread']); /*  Get Thread by ID */


    Route::patch('user-org-assign/{id}', ['as' => 'user.assign.org', 'uses' => 'Agent\helpdesk\UserController@UserAssignOrg']);

    Route::get('/{dept}/assigned', ['as' => 'dept.inprogress.ticket', 'uses' => 'Agent\helpdesk\TicketController@deptinprogress']); // Inprogress

    Route::get('/{dept}/closed', ['as' => 'dept.closed.ticket', 'uses' => 'Agent\helpdesk\TicketController@deptclose']); // Closed

    Route::post('rating2/{id}', ['as' => 'ticket.rating2', 'uses' => 'Agent\helpdesk\TicketController@ratingReply']); /* Get reply Ratings */
    // To check and lock tickets
    Route::get('check/lock/{id}', ['as' => 'lock', 'uses' => 'Agent\helpdesk\TicketController@checkLock']);
    Route::patch('/change-owner/{id}', ['as' => 'change.owner.ticket', 'uses' => 'Agent\helpdesk\TicketController@changeOwner']); /* change owner */
    //To merge tickets
    Route::get('/get-merge-tickets/{id}', ['as' => 'get.merge.tickets', 'uses' => 'Agent\helpdesk\TicketController@getMergeTickets']);
    Route::get('/check-merge-ticket/{id}', ['as' => 'check.merge.tickets', 'uses' => 'Agent\helpdesk\TicketController@checkMergeTickets']);
    Route::patch('/merge-tickets/{id}', ['as' => 'merge.tickets', 'uses' => 'Agent\helpdesk\TicketController@mergeTickets']);

    // route for graphical reporting
    Route::get('report', ['as' => 'report.index', 'uses' => 'Agent\helpdesk\ReportController@index']); /* To show dashboard pages */

    // Route to get details of agents
    Route::post('/get-agents/', ['as' => 'get-agents', 'uses' => 'Agent\helpdesk\UserController@getAgentDetails']);

    /**
     * Label
     */
    Route::post('labels-ticket', ['as' => 'labels.ticket', 'uses' => 'Admin\helpdesk\Label\LabelController@attachTicket']);
    Route::get('json-labels', ['as' => 'labels.json', 'uses' => 'Admin\helpdesk\Label\LabelController@getLabel']);
    Route::get('filter', ['as' => 'filter', 'uses' => 'Agent\helpdesk\Filter\FilterController@getFilter']);
    Route::get('filter-tickets', ['as' => 'filter-tickets', 'uses' => 'Agent\helpdesk\Filter\RelationFilterController@getFilter']);


    // Client Layout
    Route::get('api/client/layout', 'Client\helpdesk\ClientLayoutController@getLayoutData');
    // ===================================================================================================================
    //          NEWLY ADDED ROUTES FOR TICKETS BY AVINASH

    //knowledge base
    Route::get('page/show', 'Common\KnowledgeBaseController@pageDetails');

    Route::get('api/search', 'Common\KnowledgeBaseController@search');

    Route::get('api/category-list-with-article-count', 'Common\KnowledgeBaseController@getCategoryListWithArticleCount');

    Route::get('api/category-list-with-articles', 'Common\KnowledgeBaseController@getCategoryListWithArticles');

    Route::get('api/category-list-with-article', 'Common\KnowledgeBaseController@getCategoryListWithArticles1');

    Route::get('api/article-list-with-categories', 'Common\KnowledgeBaseController@getArticleListWithCategories');
    Route::get('api/article-list-with-category', 'Common\KnowledgeBaseController@getArticleListWithCategories1');

    Route::get('api/category-list/{categoryId}', 'Common\KnowledgeBaseController@getArticlesForCategory');

    Route::get('api/article/{articleSlug}', 'Common\KnowledgeBaseController@getArticleBySlug');

    Route::get('api/tag-list', 'Common\KnowledgeBaseController@getTagList');


    //tickets
    Route::get('api/mytickets/{org_id?}', 'Client\helpdesk\ClientTicketController@myTickets');


    Route::get('api/myticketscount/{org_id?}', 'Client\helpdesk\ClientTicketController@myTicketsCounts');



    //has to change to standard
    Route::post('api/ticket/change-status/{id}/{id2}', 'Agent\helpdesk\TicketController@changeStatus');

    Route::get('api/status-list', 'Client\helpdesk\ClientTicketController@getStatusList');

    Route::get('api/agent/ticket-conversation/{ticketId}', 'Agent\helpdesk\TicketsView\TicketTimelineController@ticketConversationForAgent')->name('agent.ticket-conversation');

    Route::get('api/check-ticket/{id}', 'Client\helpdesk\ClientTicketController@checkTicket');
    //have a ticket inclient panel
    Route::get('api/have-a-ticket/{id}', 'Client\helpdesk\ClientTicketController@haveATicket');

    Route::get('api/client/ticket-list', 'Client\helpdesk\ClientTicketListController@getTicketsList');

    //agent routes
    Route::get('api/agent/ticket-list', 'Agent\helpdesk\TicketsView\TicketListController@getTicketsList');

    Route::get('api/agent/ticket-count', 'Agent\helpdesk\TicketsView\TicketListController@getTicketCountForCategories');

    Route::get('api/agent/tickets/get-merge-tickets', 'Agent\helpdesk\TicketsView\TicketsActionOptionsController@getSubjectOfTicketsToBeMerged')->name('agent.get-merge-tickets');

    Route::get('/api/agent/action-list', 'Agent\helpdesk\TicketsView\TicketsActionOptionsController@getActionList')->name('agent.action-list');

    Route::get('api/agent/ticket-details/{ticketId}', 'Agent\helpdesk\TicketsView\TicketTimelineController@ticketDetails')->name('agent.ticket-details')->middleware(['role.agent']);

    Route::get('api/agent/edit-mode-ticket-details/{ticketId}', 'Agent\helpdesk\TicketsView\TicketTimelineController@ticketEditFormWithValue')->name('agent.edit-mode-ticket-details');

    Route::get('api/agent/ticket-threads/{ticketId}', 'Agent\helpdesk\TicketsView\TicketTimelineController@ticketThreads')->name('agent.ticket-threads');


    Route::get('api/dependency/{type}', 'Common\Dependency\DependencyController@handle')->name('common.dependency');

    // TODO: this API is getting used in asset and problem. It has been moved to dependency api and has to be changed at frontend
    Route::get('api/agent/ticket-suggestions', 'Agent\helpdesk\TicketsView\TicketListController@getSuggestionsByTicketSubjectOrNumber')->name('agent.ticket-suggestion');


    //test-cases are pending
    Route::post('/api/ticket/change-department', ['as' => 'ticket.post.change.department', 'uses' => 'Agent\helpdesk\TicketController@changeDepartment']);

    //test cases are pending
    Route::post('/api/ticket/change-duedate', ['as' => 'ticket.changeduedate', 'uses' => 'Agent\helpdesk\TicketController@changeDuedate']);

    //a temporary route that has to be removed after login functionality is implemented in vue
    Route::get('api/get-auth-info', 'Auth\AuthController@getLoggedInUserInfo');

    Route::get('api/get-form-menu/{category}', 'Utility\FormController@getFormBuilderFieldMenu');

    Route::get('api/get-form-data', 'Utility\FormController@getFormFieldsByCategory');

    Route::get('api/form/render', 'Utility\FormController@getFormForRender');

    Route::get('api/form/automator', 'Utility\FormController@getFormForAutomator');

    Route::post('api/post-form-data', 'Utility\FormController@postFormFieldsByCategory');

    // form groups
    Route::post('api/post-form-group', 'Utility\FormController@postGroupFormFields');

    Route::get('api/get-form-group/{groupId}', 'Utility\FormController@getGroupFormFields');

    Route::get('api/form-group-list', 'Utility\FormController@getFormGroupList');

    Route::delete('api/form-group/{groupId}', 'Utility\FormController@deleteFormGroup');

    Route::delete('api/delete-form-data/{type}/{formFieldId}', 'Utility\FormController@deleteFormFieldData');

    // deleting for formGroup will really only be unlinking it from form field
    Route::delete('api/unlink-form-group/{referenceType}/{referenceId}', 'Utility\FormController@unlinkFormGroup');

    Route::get('api/ticket-custom-fields-flattened', 'Utility\FormController@getTicketCustomFieldsFlattened');

    Route::post('api/ticket/delete-forever', 'Agent\helpdesk\TicketsView\TicketsActionOptionsController@deleteTicketsAndRelatedDataFromDB')->name('agent.delete-forever');

    //gets ticket settings data
    Route::get('api/ticket/settings', 'Agent\helpdesk\TicketsView\TicketsActionOptionsController@ticketSettings');

    //common
    Route::patch('api/profile/edit', 'Common\ProfileController@postProfile');

    //updates user language in DB
    Route::post('api/user-language', 'Common\ProfileController@postUserLanguage');

    //gets password change
    Route::post('api/password/change', 'Auth\PasswordController@changePassword');

    Route::post('/api/admin/email-settings', 'Common\Mail\ApiMailController@saveEmailSettings');

    //gets email settings for given id
    Route::get('/api/email-settings/{id}', 'Common\Mail\ApiMailController@getGivenMailSettings');


    //adds collaborators to a ticket
    //NOTE: only the person who can access current ticket should be able to edit that
    Route::post('api/ticket/collaborators', 'Agent\helpdesk\TicketController@addCollaborators');

    //creates a user and adds him/her as collaborator to the ticket
    //NOTE: only the person who can access current ticket should be able to edit that
    Route::post('api/ticket/create-add-collaborator', 'Agent\helpdesk\TicketController@createNewUserAndMakeCollaborator');
    // Tickets Filter get list
    Route::get('api/agent/ticket-filters', [
        'as' => 'ticket.filters.index',
        'uses' => 'Agent\helpdesk\TicketsView\TicketFilterController@index',
    ]);

    // Tickets Filter get details
    Route::get('api/agent/ticket-filter/{ticketFilter?}', [
        'as' => 'ticket.filters.show',
        'uses' => 'Agent\helpdesk\TicketsView\TicketFilterController@show',
    ]);

    // Tickets Filter get details
    Route::get('api/agent/filter-dependencies', 'Agent\helpdesk\TicketsView\TicketFilterController@getFilterDependencies');

    // Ticket filter store / update
    Route::post('api/agent/ticket-filter', [
        'as' => 'ticket.filter.store',
        'uses' => 'Agent\helpdesk\TicketsView\TicketFilterController@store',
    ]);

    // Ticket filter delete
    Route::delete('api/agent/ticket-filter/{ticketFilter}', [
        'as' => 'ticket.filter.destroy',
        'uses' => 'Agent\helpdesk\TicketsView\TicketFilterController@destroy',
    ]);

    // Tickets Filter view
    Route::get('tickets/filter/{filterid}', [
        'as' => 'filter',
        'uses' => 'Agent\helpdesk\TicketsView\TicketFilterController@getFilterView',
    ]);

    // Approval workflow list view
    Route::get('approval-workflow', [
        'as' => 'approval.workflow.show.index',
        'uses' => 'Admin\helpdesk\ApiApprovalWorkflowController@showIndex',
    ]);

    Route::get('approval-workflow/create', [
        'as' => 'approval.workflow.create',
        'uses' => 'Admin\helpdesk\ApiApprovalWorkflowController@create',
    ]);

    Route::get('approval-workflow/{id}/edit', [
        'as' => 'approval.workflow.edit',
        'uses' => 'Admin\helpdesk\ApiApprovalWorkflowController@edit',
    ]);

    // create or update approval workflow
    Route::post('api/admin/approval-workflow', [
        'as' => 'approval.workflow.store',
        'uses' => 'Admin\helpdesk\ApiApprovalWorkflowController@createUpdateApprovalWorkflow',
    ]);

    // get approval workflow based on approvalWorkflowId
    Route::get('api/admin/approval-workflow/{approvalWorkflowId}', [
        'as' => 'approval.workflow.show',
        'uses' => 'Admin\helpdesk\ApiApprovalWorkflowController@getApprovalWorkflow',
    ]);

    // get approval workflow list
    Route::get('api/admin/approval-workflow', [
        'as' => 'approval.workflow.index',
        'uses' => 'Admin\helpdesk\ApiApprovalWorkflowController@getApprovalWorkflowList',
    ]);

    // delete approval workflow based on approvalWorkflowOrLevelId and type (type could be level or workflow)
    Route::delete('api/admin/approval-workflow/{approvalWorkflowOrLevelId}/{type}', [
        'as' => 'approval.workflow.destroy',
        'uses' => 'Admin\helpdesk\ApiApprovalWorkflowController@deleteApprovalWorkflow',
    ]);

    // Approval workflow remove
    Route::post('api/apply-approval-workflow', 'Agent\helpdesk\TicketsWrite\ApiApproverController@applyApprovalWorkflow');

    // Remove approval workflow applied on a ticket based on workflowId
    Route::delete('api/remove-approval-workflow/{workflowId}', 'Agent\helpdesk\TicketsWrite\ApiApproverController@removeApprovalWorkflow');

    // takes approval action on a ticket
    Route::post('api/approval-action/{hash}', 'Agent\helpdesk\TicketsWrite\ApiApproverController@approvalActionByHash');

    Route::post('api/approval-action-without-hash/{ticketId}', 'Agent\helpdesk\TicketsWrite\ApiApproverController@approvalActionByTicketId');

    Route::get('api/ticket-conversation/{hash}', 'Agent\helpdesk\TicketsWrite\ApiApproverController@getConversationByHash');

    Route::get('api/ticket-conversation-guest/{hash}', 'Agent\helpdesk\TicketsView\TicketTimelineController@getTicketConversationByHash');

    Route::get('api/ticket-approval-status/{ticketId}', 'Agent\helpdesk\TicketsWrite\ApiApproverController@getTicketAppovalStatus');

    //creates and updates enforcer(worflow or listener)
    Route::post('api/post-enforcer', 'Common\TicketsWrite\ApiEnforcerController@postEnforcer');

    //gets enforcer  by type and id
    Route::get('api/get-enforcer/{type}/{enforcerId}', 'Common\TicketsWrite\ApiEnforcerController@getEnforcer');

    //gets enforcer by type. Most of the attributes.
    Route::get('api/get-enforcer-list', 'Common\TicketsWrite\ApiEnforcerController@getEnforcerList');

    //gets enforcer by type and reorders them.
    Route::post('api/reorder-enforcer-list', 'Common\TicketsWrite\ApiEnforcerController@reorderEnforcer');

    //gets enforcer  by type and id
    Route::delete('api/delete-enforcer/{type}/{enforcerId}/{enforcerMetaType?}', 'Common\TicketsWrite\ApiEnforcerController@deleteEnforcer');

    // gets event list
    Route::get('api/enforcer-events', 'Common\TicketsWrite\ApiEnforcerController@getEventList');

    Route::get('api/enforcer-events/dependency/{type}', 'Common\TicketsWrite\EnforcerEventController@handle');

    Route::get('api/get-storage-directories', 'Utility\UploadController@listDirectories');

    Route::get('api/admin/navigation', 'Common\Navigation\AdminNavigationController@getAdminNavigation');

    Route::get('api/agent/navigation', 'Common\Navigation\AgentNavigationController@getAgentNavigation')->middleware(['auth']);

    Route::get('api/system-settings', 'Common\SettingsController@getSystemSettings');

    Route::post('api/forward-ticket', 'Agent\helpdesk\TicketsWrite\TicketActionController@forwardTicket');

    Route::post('api/login', 'Auth\AuthController@postLoginAndGetToken');
    //using postLogout so the API can be used in web app also
    Route::post('api/logout', 'Auth\AuthController@postLogout')->middleware(['auth']);

    Route::post('api/change-owner/{ticketId}', 'Agent\helpdesk\TicketsWrite\TicketActionController@changeOwner');
    Route::get('api/agent/ticket-activity-log/{ticketId}', 'Common\TicketActivityLogController@getTicketActivity');

// ===================================================================================================================

    //emails routes moved to APIMailController from EmailsController
    Route::resource('emails', 'Common\Mail\ApiMailController', ['except' =>['show']]);
    Route::delete('api/email-delete/{email}', 'Common\Mail\ApiMailController@delete');
    Route::get('api/emails-list', 'Common\Mail\ApiMailController@getEmailsList');





    /**
     * Tags
     */
    Route::get('tag', [
        'as'   => 'tag',
        'uses' => 'Agent\helpdesk\Filter\TagController@tag'
    ]);
    Route::get('tag/create', [
        'as'   => 'tag.create',
        'uses' => 'Agent\helpdesk\Filter\TagController@create'
    ]);
    Route::get('tag/{id}/edit', [
        'as'   => 'tag.edit',
        'uses' => 'Agent\helpdesk\Filter\TagController@edit'
    ]);
    Route::patch('tag/{id}/update', [
        'as'   => 'tag.update',
        'uses' => 'Agent\helpdesk\Filter\TagController@update'
    ]);
    Route::delete('tag/{id}/delete', [
        'as'   => 'tag.delete',
        'uses' => 'Agent\helpdesk\Filter\TagController@destroy'
    ]);
    Route::get('tag-ajax', [
        'as'   => 'tag.ajax.api',
        'uses' => 'Agent\helpdesk\Filter\TagController@ajaxDatatable'
    ]);
    Route::post('tag', [
        'as'   => 'tag.post',
        'uses' => 'Agent\helpdesk\Filter\TagController@postCreate'
    ]);



    Route::post('add-tag', ['as' => 'tag.add', 'uses' => 'Agent\helpdesk\Filter\TagController@addToFilter']);

    Route::get('get-tag', ['as' => 'tag.get', 'uses' => 'Agent\helpdesk\Filter\TagController@getTag']);

    Route::get('get-type', ['as' => 'type.get', 'uses' => 'Agent\helpdesk\Filter\TagController@getType']);

    Route::get('dashboard/getDepartment', ['as' => 'dashboard-getdepartment', 'uses' => 'Agent\helpdesk\DashboardController@departments']);

    Route::get('get-canned-response-shared-department/{id}', ['as' => 'get-canned-shared-departments', 'uses' => 'Agent\helpdesk\CannedController@getCannedDepartments']);

    Route::get('get-canned-response-message/{id}/{edit?}', ['as' => 'get-canned-message', 'uses' => 'Agent\helpdesk\CannedController@getCannedMessage']);



    // source

    Route::get('source', [
        'as'   => 'source',
        'uses' => 'Admin\helpdesk\Source\SourceController@source'
    ]);
    Route::get('source/create', [
        'as'   => 'source.create',
        'uses' => 'Admin\helpdesk\Source\SourceController@create'
    ]);
    Route::get('source/{id}/edit', [
        'as'   => 'source.edit',
        'uses' => 'Admin\helpdesk\Source\SourceController@edit'
    ]);
    Route::patch('source/{id}/update', [
        'as'   => 'source.update',
        'uses' => 'Admin\helpdesk\Source\SourceController@update'
    ]);
    Route::get('source/{id}/delete', [
        'as'   => 'source.delete',
        'uses' => 'Admin\helpdesk\Source\SourceController@destroy'
    ]);

    Route::get('source-ajax', [
        'as'   => 'source.ajax.api',
        'uses' => 'Admin\helpdesk\Source\SourceController@ajaxDatatable'
    ]);

    Route::post('source', [
        'as'   => 'source.post',
        'uses' => 'Admin\helpdesk\Source\SourceController@postCreate'
    ]);


    Route::group(['middleware' => ['role.agent']], function () {
        Route::get('tickets/{version?}', ['as' => 'tickets-view', 'uses' => 'Agent\helpdesk\TicketController@getTicketsView']);
    });

    Route::post('manual-verify', ['as' => 'manual-verify-details', 'uses' => 'Agent\helpdesk\UserController@postManualVerify']);
    ;
    /*
      |------------------------------------------------------------------
      |Agent Routes
      |--------------------------------------------------------------------
      | Here defining entire Agent Panel routers
      |
      |
     */

    Route::post('create-org-dept/{org_id}', ['as' => 'create.org.dept', 'uses' => 'Agent\helpdesk\OrganizationController@createNewOrgDept']);

    Route::post('postedform/{domain_id?}', ['as' => 'client.form.post', 'uses' => 'Client\helpdesk\FormController@postedForm']); /* post the form to store the value */

    Route::get('mytickets/{id}', ['as' => 'ticketinfo', 'uses' => 'Client\helpdesk\GuestController@singleThread']); //detail ticket information
    Route::post('checkmyticket', 'Client\helpdesk\UnAuthController@PostCheckTicket'); //ticket ckeck

    Route::get('check_ticket/{id}', ['as' => 'check_ticket', 'uses' => 'Client\helpdesk\GuestController@getTicketEmail']); //detail ticket information
//===================================================================================
    Route::group(['middleware' => 'auth'], function () {
        // In app notification
        Route::get('subscribe-user', 'Common\PushNotificationController@subscribeUser');

        Route::get('get-push-notifications', 'Common\PushNotificationController@getSubscribedNotification');

        Route::get('notification-delivered/{id}', 'Common\PushNotificationController@notificationDelivered');

        Route::get('/get/assigntickets', ['as' => 'get.assigntickets', 'uses' => 'Agent\helpdesk\TicketController@assignTicketList']);

        Route::get('/newticket', ['as' => 'newticket', 'uses' => 'Agent\helpdesk\TicketController@newticket'])
            ->middleware('role.agent'); /*  Get Create New Ticket */

        Route::get('myticket/{id}', ['as' => 'ticket', 'uses' => 'Client\helpdesk\GuestController@thread']); /* Get my tickets */

        Route::get('organizations/data/{org}', ['as' => 'organizations.data', 'uses' => 'Client\helpdesk\GuestController@clientOrgManagerData']);

        Route::get('organization/userdata/{org_id}', ['as' => 'organizations.data', 'uses' => 'Client\helpdesk\GuestController@getOrgUserList']);

        Route::get('client/organizations/edituser/{user_id}', ['as' => 'client.organizations.edituser', 'uses' => 'Client\helpdesk\GuestController@orgEditUser']);
        Route::post('/org/edit/user/{user}', 'Client\helpdesk\GuestController@updateOrgUser');

        Route::get('/org/delete/user/{id}', ['as' => 'client.org.deleteuser', 'uses' => 'Client\helpdesk\GuestController@orgDeleteUser']);

        Route::post('client/user/create', 'Client\helpdesk\GuestController@storeUser');

        //attachment




        Route::post('/delete/user/{id}', ['as' => 'client.deleteuser', 'uses' => 'Client\helpdesk\GuestController@orgDeleteUser']);

        Route::post('post/reply/{id}', ['as' => 'client.reply', 'uses' => 'Client\helpdesk\ClientTicketController@reply']);
    });

    //====================================================================================
    Route::get('checkticket', 'Client\helpdesk\ClientTicketController@getCheckTicket'); /* Check your Ticket */

    /*
      |=============================================================
      |  Error Routes
      |=============================================================
     */
    Route::get('error-in-database-connection', ['as' => 'errordb', 'uses'=> 'Client\helpdesk\UnAuthController@errorInDatabaseConnectionView']);



    Route::get('unauthorized', ['as' => 'unauth', 'uses'=> 'Client\helpdesk\UnAuthController@unauthorizedView']);


    Route::get('board-offline', ['as' => 'board.offline', 'uses'=> 'Client\helpdesk\UnAuthController@boardOfflineView']);

    /* for kb routes */
    Route::group(['middleware' => ['kbaccess']], function () {
        /*  For the crud of category  */
        Route::resource('category', 'Agent\kb\CategoryController', ['except' => ['show']]);

        Route::get('api/get-category-data',  'Agent\kb\CategoryController@getData');

        Route::post('api/category/create', 'Agent\kb\CategoryController@store');

        Route::get('api/edit/category/{id}','Agent\kb\CategoryController@editApi');

        Route::delete('category/delete/{id}', 'Agent\kb\CategoryController@destroy');

        Route::get('category/delete/{id}', 'Agent\kb\CategoryController@destroy');


        /*  For the crud of article  */

        Route::resource('article', 'Agent\kb\ArticleController', ['except' => ['show']]);

        Route::get('api/edit/article/{id}','Agent\kb\ArticleController@editArticle');

        Route::delete('article/delete/{id}', 'Agent\kb\ArticleController@destroy');

        Route::get('get-articles', ['as' => 'api.article', 'uses' => 'Agent\kb\ArticleController@getData']);

        /*  For the crud of article template  */
        Route::get('article/alltemplate/list',['as' => 'article.alltemplate.list' ,'uses' =>'Agent\kb\ArticleController@articleTemplateIndex']);
        Route::get('articletemplate/index', 'Agent\kb\ArticleController@getArticleTemplateData');

        Route::get('article/create/template', ['as'=>'article.create.template','uses'=>'Agent\kb\ArticleController@createTemplate']);
        Route::post('article/post/template',  'Agent\kb\ArticleController@postTemplate');


        Route::get('articletemplate/{id}/edit',['as'=>'article.edit.template','uses'=> 'Agent\kb\ArticleController@editTemplate']);
        Route::get('api/articletemplate/edit/{id}','Agent\kb\ArticleController@editApiTemplate');

        Route::delete('article/deletetemplate/{id}','Agent\kb\ArticleController@postTemplateDelete');

        Route::get('article/template/{id}', 'Agent\kb\ArticleController@getTemplateDetails');


        /*  For the crud of kb pages  */
        Route::resource('page', 'Agent\kb\PageController', ['except' => ['show']]);
        Route::get('api/get-pages-data',  'Agent\kb\PageController@getData');

        Route::post('api/page/create', 'Agent\kb\PageController@store');

        Route::get('api/edit/page/{id}','Agent\kb\PageController@editApi');
        Route::delete('page/delete/{id}', 'Agent\kb\PageController@destroy');




        /* get settings */
        Route::get('kb/settings', ['as' => 'settings', 'uses' => 'Agent\kb\SettingsController@settings']);
        Route::get('kb/settings/getvalue', ['as' => 'settings.getvalue', 'uses' => 'Agent\kb\SettingsController@settingsGetValue']);

        /* post settings */
        Route::post('postsettings', 'Agent\kb\SettingsController@postSettings');


        //Route for administrater to access the comment
        Route::get('comment', ['as' => 'comment', 'uses' => 'Agent\kb\SettingsController@comment']);

        /* Route to define the comment should Published */
        Route::get('published/{id}', ['as' => 'published', 'uses' => 'Agent\kb\SettingsController@publish']);
        /* Route to define the comment should unapprove */
        Route::get('unapprove/{id}', ['as' => 'unapprove', 'uses' => 'Agent\kb\SettingsController@unapprove']);
        /* Route for deleting comments */
        Route::delete('deleted/{id}', ['as' => 'deleted', 'uses' => 'Agent\kb\SettingsController@delete']);

        Route::delete('comment/delete/{id}', ['as' => 'commentdelete', 'uses' => 'Agent\kb\SettingsController@delete']);

    });

    Route::get('get-comment', ['as' => 'api.comment', 'uses' => 'Agent\kb\SettingsController@getData']);

    Route::post('image', 'Agent\kb\SettingsController@image');

    /* post the comment from show page */
    Route::post('postcomment/{slug}', ['as' => 'postcomment', 'uses' => 'Client\kb\UserController@postComment']);

    /* get the cantact page to user */
    Route::get('contact', ['as' => 'contact', 'uses' => 'Client\kb\UserController@contact']);

    /* post the cantact page to controller */

    /*
     * Update module
     */
    Route::get('database-update', ['as' => 'database.update', 'uses' => 'Update\UpgradeController@databaseUpdate']);
    Route::get('file-upgrade', ['as' => 'file.upgrade', 'uses' => 'Update\UpgradeController@fileUpgrading']);

    /**
     * Queue
     */
    Route::get('queue', ['as' => 'queue', 'uses' => 'Job\QueueController@index']);
    Route::get('form/queue', ['as' => 'queue.form', 'uses' => 'Job\QueueController@getForm']);

    Route::get('queue/{id}', ['as' => 'queue.edit', 'uses' => 'Job\QueueController@edit']);
    Route::post('queue/{id}', ['as' => 'queue.update', 'uses' => 'Job\QueueController@update']);
    Route::get('queue/{queue}/activate', ['as' => 'queue.activate', 'uses' => 'Job\QueueController@activate']);
    
    /**
     * Social media settings
     */
    Route::get('social/media', ['as' => 'social', 'uses' => 'Admin\helpdesk\SocialMedia\SocialMediaController@index']);
    Route::get('social/media/{provider}', ['as' => 'social.media', 'uses' => 'Admin\helpdesk\SocialMedia\SocialMediaController@settings']);
    Route::post('social/media/{provider}', ['as' => 'social.media.post', 'uses' => 'Admin\helpdesk\SocialMedia\SocialMediaController@postSettings']);
    Route::get('social/external-login', ['as' => 'social.media.external.get', 'uses' => 'Admin\helpdesk\SocialMedia\SocialMediaController@getExternalSettings']);
    Route::post('social/external-login', ['as' => 'social.media.external.post', 'uses' => 'Admin\helpdesk\SocialMedia\SocialMediaController@postExternalSettings']);
    /*
     * Ticket_Priority Settings
     */
    Route::get('ticket/priority', ['as' => 'priority.index', 'uses' => 'Admin\helpdesk\PriorityController@priorityIndex']);
    Route::post('user/ticket/priority', ['as' => 'user.priority.index', 'uses' => 'Admin\helpdesk\PriorityController@userPriorityIndex']);

    Route::get('get_index', ['as' => 'priority.index1', 'uses' => 'Admin\helpdesk\PriorityController@priorityIndex1']);
    Route::get('ticket/priority/create', ['as' => 'priority.create', 'uses' => 'Admin\helpdesk\PriorityController@priorityCreate']);
    Route::post('ticket/priority/create1', ['as' => 'priority.create1', 'uses' => 'Admin\helpdesk\PriorityController@priorityCreate1']);
    Route::post('ticket/priority/edit1/{priority_id}', ['as' => 'priority.edit1', 'uses' => 'Admin\helpdesk\PriorityController@priorityEdit1']);
    Route::get('ticket/priority/{ticket_priority}/edit', ['as' => 'priority.edit', 'uses' => 'Admin\helpdesk\PriorityController@priorityEdit']);
    Route::get('ticket/priority/{ticket_priority}/destroy', ['as' => 'priority.destroy', 'uses' => 'Admin\helpdesk\PriorityController@destroy']);
    Route::delete('delete/departmentpopup/{id}', ['as' => 'helper.department.delete', 'uses' => 'Admin\helpdesk\DepartmentController@destroy']);
    Route::get('delete/teampopup/{id}', ['as' => 'helper.team.delete', 'uses' => 'Admin\helpdesk\TeamController@destroy']);

    Route::get('delete/helptopicpopup/{id}', ['as' => 'helper.helptopic.delete', 'uses' => 'Admin\helpdesk\HelptopicController@destroy']);

    Route::post('api/account/restore/{user}', ['as' => 'user.restore', 'uses' => 'Agent\helpdesk\HandleAccountController@restoreUser'])->middleware(['account.access']);
    Route::post('api/account/deactivate/{user}', ['as' => 'user.deactivate', 'uses' => 'Agent\helpdesk\HandleAccountController@handleAccountDeactiveRequest'])->middleware(['account.access']);
    Route::delete('api/account/delete/{user}', ['as' => 'user.delete', 'uses' => 'Agent\helpdesk\DeleteAccountController@handleAccountDeleteRequest'])->middleware(['account.access']);


    /* -------------------------------------------------------------
      | User language change
      |-------------------------------------------------------------
     */
    Route::get('swtich-language/{id}', ['as' => 'switch-user-lang', 'uses' => 'Client\helpdesk\UnAuthController@changeUserLanguage']);

    Route::post('chunk/upload', ['as' => 'chunk.upload', 'uses' => 'Utility\UploadController@upload']);

    Route::get('media/files', ['as' => 'media.files', 'uses' => 'Utility\UploadController@files']);

    Route::get('media/files/search', ['as' => 'media.files', 'uses' => 'Utility\UploadController@filesSearch']);

    Route::post('chunk/upload/public', ['as' => 'chunk.upload.public', 'uses' => 'Utility\UploadController@uploadPublic']);

    Route::get('media/files/public', ['as' => 'media.files.public', 'uses' => 'Utility\UploadController@filesPublic']);


    /* -------------------------------------------------------------
      | User language change
      |-------------------------------------------------------------
     */


    Route::post('media/files/delete', ['as' => 'media.files.delete', 'uses' => 'Utility\UploadController@deletefile']);

    Route::get('ticket/form/dependancy', [
        'as'   => 'api.dependancy',
        'uses' => 'Utility\FormController@dependancy'
    ]);

    Route::get('ticket/form/requester', [
        'as'   => 'api.requester',
        'uses' => 'Utility\FormController@requester'
    ]);
    ;

    Route::get('sitemap.xml', 'Client\helpdesk\UnAuthController@siteMap')->name('sitemap');
});

//cba custom edit
Route::post('rating/{id}', ['as' => 'ticket.rating', 'uses' => 'Agent\helpdesk\TicketController@rating']); /* Get*/
Route::get('api/rating/{ticketId}/{rate}', ['as' => 'rating.feedback', 'uses' => 'Admin\helpdesk\RatingsController@saveRatingFeedBack']);
Route::post('api/request/rating/{ticket}', ['as' => 'rating.feedback.mail', 'uses' => 'Agent\helpdesk\TicketController@sendRatingRequestMail']);

Route::post('keep-page-alive', 'Client\helpdesk\UnAuthController@keepPageAlive');
Route::get('database-not-updated', 'Update\AutoUpdateController@viewDbNotUpdated')->name('database-not-updated');
Route::post('update-database', ['as' => 'update-database',
            'uses' => 'Update\AutoUpdateController@updateDatabaseFromMiddleware'
        ]);


/*
 * Added For Import
 * @author Phaniraj K
 */
Route::group(['middleware' => ['auth','role.admin']], function () {

    Route::group(['prefix' => 'importer'], function () {
        Route::get('/', 'Admin\helpdesk\FaveoImportController@importForm')
            ->name('importer.form');
    });

    Route::group(['prefix' => 'importer/api'], function () {
        Route::post('/upload', 'Admin\helpdesk\FaveoImportController@processImportFile')
            ->name('importer.upload');
        Route::get('/processing', 'Admin\helpdesk\FaveoImportController@getDetailsForProcessing')
            ->name('importer.api.processing');
        Route::post('/processing', 'Admin\helpdesk\FaveoImportController@postProcessingAttributes')
            ->name('importer.api.processing.post');
    });

    Route::get('file-system-settings', 'Admin\helpdesk\SettingsController@fileSystemSettingsPage')
        ->name('settings.filesystems');

    Route::get('file-system-settings-values', 'Admin\helpdesk\SettingsController@getFileSystemSettingsValues')
        ->name('settings.filesystems.values');

    Route::put('file-system-settings', 'Admin\helpdesk\SettingsController@updateFileSystemSettings')
        ->name('settings.filesystems.put');

});

// admin.php routes
Route::group(['middleware' => ['redirect', 'roles', 'auth', 'install', 'limit.exceeded']], function () {
    /**
     * Webhook
     */
    Route::get('webhook',['as'=>'webhook','uses'=>'Common\WebhookController@webhookCreateAndEditPage']);
    Route::post('api/admin/save-webhook','Common\WebhookController@createUpdateWebhook');
    Route::get('api/admin/get-webhook', 'Common\WebhookController@getWebhookData'); 

    Route::resource('listener', 'Admin\helpdesk\Listener\ListenerController', ['except' => ['show']]);
    Route::post('listener/order','Admin\helpdesk\Listener\ListenerController@reorder');
    Route::post('listener/update/{id}','Admin\helpdesk\Listener\ListenerController@update');

    /**
     * Workflow
     */

    Route::post('workflow/order','Admin\helpdesk\WorkflowController@reorder');
});


/**
 * ===================================================================================
 *  NOTE: Do not define any functional route below this block.
 * ===================================================================================
 */

/**
 * @deprecated Routes
 * Here are the routes which are removed and no longer available in the application.
 * Though these routes can be removed but to maintain backward compatibility they are
 * being listed here and will be removed eventually.
 *
 * A route can be deprecated if it passes any of the reasons mentioned below
 *      1: Test routes created by development during development.
 *      2: Routes which provides the functionality which isn't/hasn't been use(d) in production
 * so far(Routes with useless functionality).
 *      3: Route's functionality had been converted into APIs
 *      4: Routes which are not relevant after the update.
 *      5: Routes which are converted and defined in Vue router.
 *
 * Note: Before adding any route in this commented route list add "since" annotation with version
 * number of next release in which the routes will not work. So the maintainers can decide to
 * remove these commented routes in future and clean this file. Do not add any new commented route
 * under last release tag.
 */


/**
 * @since v3.5.0
 * @internal mobile app is using this API. Make sure they are using api/agent/ticket-conversation/{ticketId} for the same purpose
 */


/**
 * Protecting only by 'auth' as the way middleware are defined in the wep.php for
 * below routes only auth works.
 */
Route::group(['middleware' => 'auth'], function () {
    Route::post('/thread/reply', ['as' => 'ticket.reply', 'uses' => 'Agent\helpdesk\TicketController@reply']); /*  Patch Thread Reply */
    Route::post('post/reply/{id}', ['as' => 'client.reply', 'uses' => 'Client\helpdesk\ClientTicketController@reply']);
});



// these are old Dashboard routes and will be removed in future
Route::post('chart-range/{date1}/{date2}', ['as' => 'post.chart', 'uses' => 'Agent\helpdesk\DashboardController@ChartData']);
Route::get('agen1', 'Agent\helpdesk\DashboardController@ChartData');
Route::post('chart-range', ['as' => 'post.chart', 'uses' => 'Agent\helpdesk\DashboardController@ChartData']);
Route::post('user-chart-range/{id}/{date1}/{date2}', ['as' => 'post.user.chart', 'uses' => 'Agent\helpdesk\DashboardController@userChartData']);
Route::get('user-agen/{id}/{team?}', 'Agent\helpdesk\DashboardController@userChartData');
Route::get('user-agen1', 'Agent\helpdesk\DashboardController@userChartData');
Route::post('user-chart-range', ['as' => 'post.user.chart', 'uses' => 'Agent\helpdesk\DashboardController@userChartData']);

// deleted user list
Route::get('deleted/user', ['as' => 'user.deleted', 'uses' => 'Agent\helpdesk\UserController@deletedUser']);


////////////////////////user apis/////////////////////////////
Route::group(['middleware' => 'auth'], function () {
    Route::post('deactivate/user/{id}', ['as' => 'user.post.deactivate', 'uses' => 'Agent\helpdesk\UserController@deactivateUser']);
    Route::post('deactivate/agent/{id}', ['as' => 'agent.post.deactivate', 'uses' => 'Agent\helpdesk\UserController@deactivateAgent']);
    Route::post('deactivate/admin/{id}', ['as' => 'agent.post.deactivate', 'uses' => 'Agent\helpdesk\UserController@deactivateAgent']);
});


// admin.php routes
Route::group(['middleware' => ['web', 'redirect', 'roles', 'auth', 'install', 'limit.exceeded']], function () {
    Route::resource('listener', 'Admin\helpdesk\Listener\ListenerController', ['except' => ['show']]);
    Route::post('listener/order','Admin\helpdesk\Listener\ListenerController@reorder');
    Route::post('listener/update/{id}','Admin\helpdesk\Listener\ListenerController@update');

    /**
     * Workflow
     */

    Route::post('workflow/order','Admin\helpdesk\WorkflowController@reorder');
});

/**
 * ===================================================================================
 *  ******************** Deprecated route block ends here. ********************
 * ===================================================================================
 */

/**
 * This block snippet gets called after routes are checked in Laravel
 * and Vue router and not found so system returns 404.
 * Keeping it in the end ensures deprecated routes work fine until they are
 * removed from the system. And as instructed in the doc block above we should
 * not define any functional route here.
 */
if (strpos(url()->current(), '/api') == false) {
    Route::get('/{one}/{two?}/{three?}/{four?}/', function(){
        return view('welcome');
    })->name('error404');
}
