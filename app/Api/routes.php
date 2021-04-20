<?php

// NOTE FROM AVINASH: commenting these routes for safe side. If some client asks for these routes, we can simply uncomment
//Route::group(['middleware'=>['web','force.json','redirect'],'prefix' => 'api/v1'], function () {
//    /*
//     * ================================================================================================
//     * @version v1
//     * @access public
//     * @copyright (c) 2016, Ladybird web solution
//     * @author Vijay Sebastian<vijay.sebastian@ladybirdweb.com>
//     * @name Faveo
//     */
//    //Route::post('register', 'App\Api\v1\TokenAuthController@register');
//    Route::post('authenticate', 'App\Api\v1\TokenAuthController@authenticate');
//    Route::get('logout', 'App\Api\v1\TokenAuthController@logout');
//
//    Route::get('authenticate/user', 'App\Api\v1\TokenAuthController@getAuthenticatedUser');
//    Route::get('/database-config', ['as' => 'database-config', 'uses' => 'App\Api\v1\InstallerApiController@config_database']);
//    Route::get('/system-config', ['as' => 'database-config', 'uses' => 'App\Api\v1\InstallerApiController@config_system']);
//    /*
//     * Helpdesk
//     */
//        Route::group(['prefix' => 'helpdesk'], function () {
//        Route::post('create', 'App\Api\v1\ApiController@createTicket');
//        Route::post('reply', 'App\Api\v1\ApiController@ticketReply');
//        Route::post('edit', 'App\Api\v1\ApiController@editTicket');
//        Route::post('delete', 'App\Api\v1\ApiController@deleteTicket');
//        Route::post('assign', 'App\Api\v1\ApiController@assignTicket');
//        Route::get('open', 'App\Api\v1\ApiController@openedTickets');
//        Route::get('unassigned', 'App\Api\v1\ApiController@unassignedTickets');
//        Route::get('closed', 'App\Api\v1\ApiController@closeTickets');
//        Route::get('agents', 'App\Api\v1\ApiController@getAgents');
//        Route::get('teams', 'App\Api\v1\ApiController@getTeams');
//        Route::get('customers', 'App\Api\v1\ApiController@getCustomers');
//        Route::get('user', 'App\Api\v1\ApiController@getCustomer');
//        Route::get('ticket-search', 'App\Api\v1\ApiController@searchTicket');
//        Route::get('ticket-thread', 'App\Api\v1\ApiController@ticketThreads');
       	  Route::get('api/v1/helpdesk/url', 'App\Api\v1\ApiExceptAuthController@checkUrl');
       	  Route::get('api/v1/helpdesk/check-url', 'App\Api\v1\ApiExceptAuthController@urlResult');
//        Route::get('api_key', 'App\Api\v1\ApiController@generateApiKey');
//        Route::get('help-topic', 'App\Api\v1\ApiController@getHelpTopic');
//        Route::get('sla-plan', 'App\Api\v1\ApiController@getSlaPlan');
//        Route::get('priority', 'App\Api\v1\ApiController@getPriority');
//        Route::get('department', 'App\Api\v1\ApiController@getDepartment');
//        Route::get('tickets', 'App\Api\v1\ApiController@getTickets');
//        Route::get('ticket', 'App\Api\v1\ApiController@getTicketById');
//        Route::get('inbox', 'App\Api\v1\ApiController@inbox');
//        Route::get('trash', 'App\Api\v1\ApiController@getTrash');
//        Route::get('my-tickets-agent', 'App\Api\v1\ApiController@getMyTicketsAgent');
//        Route::post('internal-note', 'App\Api\v1\ApiController@internalNote');
//        /*
//         * Newly added
//         */
//        Route::get('customers-custom', 'App\Api\v1\ApiController@getCustomersWith');
//        Route::get('collaborator/search', 'App\Api\v1\ApiController@collaboratorSearch');
//        Route::post('collaborator/create', 'App\Api\v1\ApiController@addCollaboratorForTicket');
//        Route::post('collaborator/remove', 'App\Api\v1\ApiController@deleteCollaborator');
//        Route::post('collaborator/get-ticket', 'App\Api\v1\ApiController@getCollaboratorForTicket');
//        Route::get('my-tickets-user', 'App\Api\v1\ApiController@getMyTicketsUser');
//        Route::get('dependency', 'App\Api\v1\ApiController@dependency');
//        Route::get('notifications', 'App\Api\v1\ApiController@notification');
//        Route::post('notifications-seen', 'App\Api\v1\ApiController@notificationSeen');
//        Route::post('register', 'App\Api\v1\ApiController@createUser');
//        Route::get('organizations', 'App\Api\v1\ApiController@organizations');
//        Route::get('organizations/search', 'App\Api\v1\ApiController@organizationSearch');
//        Route::get('organizations/tickets', 'App\Api\v1\ApiController@organizationTickets');
//        Route::get('organizations/users', 'App\Api\v1\ApiController@organizationUsers');
//        Route::post('collaborator/create/user', 'App\Api\v1\ApiController@register');
//        // For helpsection for admin
//       Route::post('helpsection/mails', ['as' => 'helpsection.mails', 'uses' => 'App\Api\v1\ApiController@sentmail']);
//
//
//
//        //===================================================
//        //satellite routes
//        //=====================================================
//
//         Route::get('helpdesk', 'App\Api\v1\ApiController@gethelpdesk');
//         Route::post('register/satellite', 'App\Api\v1\ApiController@createSatelliteUser');
//         Route::post('create/satellite/ticket', 'App\Api\v1\ApiController@createSatelliteTicket');
//         Route::get('ticket/satellite', 'App\Api\v1\ApiController@getsatelliteTicketById');
//         Route::get('get-tickets/satellite/{?domain_id}', 'App\Api\v1\ApiController@getTicketsAPI');
//         Route::post('reply/withdetails', 'App\Api\v1\ApiController@ticketReplyWithDetails');
//         Route::get('user/ticket/list', 'App\Api\v1\ApiController@userTicketList');
//
//        //===================================================
//
//
//
//       });
//
//
//    /*
//     * FCM token response
//     */
//    Route::post('fcmtoken', ['as' => 'fcmtoken', 'uses' => 'App\Http\Controllers\Common\PushNotificationController@fcmToken']);
//});

//Route::group(['middleware'=>['web','force.json','redirect'],'prefix' => 'api/v2'], function () {
//    /*
//     * ================================================================================================
//     * @version v2
//     * @access public
//     * @copyright (c) 2016, Ladybird web solution
//     * @author Vijay Sebastian<vijay.sebastian@ladybirdweb.com>
//     * @name Faveo
//     */
//
//    /*
//     * Helpdesk
//     */
//    Route::group(['prefix' => 'helpdesk'], function () {
//        Route::get('get-tickets', 'App\Api\v1\ApiController@getTicketsAPI');
//        Route::post('status/change', 'App\Api\v1\ApiController@statusChange');
//        Route::post('merge', 'App\Api\v1\ApiController@merge');
//        Route::get('custom/form/{type?}', 'App\Api\v2\ApiControllerVersion@getCustomForm');
//        Route::patch('user/edit/','App\Api\v2\ApiControllerVersion@editUser');
//        Route::patch('user-edit/{id}','App\Api\v2\ApiControllerVersion@userEdit');
//        Route::post('user/change/password', ['as' => 'user.change.password', 'uses' => 'App\Api\v2\ApiControllerVersion@changePassword']);
//        Route::patch('user/status/{id}','App\Api\v2\ApiControllerVersion@userStatus');
//        Route::get('user/filter','App\Api\v2\ApiControllerVersion@userDetails');
//        Route::post('ticket/delete','App\Api\v2\ApiControllerVersion@ticketDelete');
//        Route::post('ticket/assign','App\Api\v2\ApiControllerVersion@ticketAssignment');
//       Route::get('/api/get/agentlist', ['as' => 'api.get.agentlist', 'uses' => 'App\Api\v1\ApiController@getAgentList']);
//
//    });
//
//});

/**
 * Note this are temporary changes made for make old create and edit api working
 * till mobile apps are using v3 API completely with new form structure for tickets.
 * 
 * @todo remove these routes once mobile apps are working with new form structure.
 */
Route::group(['middleware'=>['force.json', 'auth'], 'prefix' => 'api/v3/helpdesk'], function () {
    Route::post('create', 'App\Api\v1\ApiController@createTicket');
    Route::post('edit', 'App\Api\v1\ApiController@editTicket');
    //add FCM device token using legacy method
    Route::post('fcmtoken', 'App\Api\v3\FCMController@addDeviceTokenOld');
});
