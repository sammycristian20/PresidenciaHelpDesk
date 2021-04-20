<?php

Route::group(['middleware' => ['web', 'auth', 'role.agent','dbNotUpdated'], 'prefix' => 'report'], function () {
    Route::post('update-database', ['as' => 'update-database',
            'uses' => 'Update\AutoUpdateController@updateDatabaseFromMiddleware'
        ]);

    // NOTE FROM AVINASH: commented all unused routes. Will be removed in future
//    Route::get('get-all', [
//        'as'   => 'report.all.post',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@all',
//    ]);
//
//    Route::get('all', [
//        'as'   => 'report.all',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@allView',
//    ]);
//
//    Route::get('priority', [
//        'as'   => 'report.priority',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@priority',
//    ]);
//
//    Route::get('sla', [
//        'as'   => 'report.sla',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@sla',
//    ]);

//    Route::get('helptopic', [
//        'as'   => 'report.helptopic',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@helptopic',
//    ]);
//
//    Route::get('status', [
//        'as'   => 'report.status',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@status',
//    ]);
//
//    Route::get('purpose', [
//        'as'   => 'report.purpose',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@statusType',
//    ]);
//
//    Route::get('source', [
//        'as'   => 'report.source',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@source',
//    ]);

//    /**
//     * Departments
//     */
//    Route::get('department/all', [
//        'as'   => 'report.department.all',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentAll',
//    ]);
//
//    Route::get('department/priority', [
//        'as'   => 'report.department.priority',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentPriority',
//    ]);
//
//    Route::get('department/sla', [
//        'as'   => 'report.department.sla',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentSla',
//    ]);
//
//    Route::get('department/helptopic', [
//        'as'   => 'report.department.helptopic',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentHelptopic',
//    ]);
//
//    Route::get('department/status', [
//        'as'   => 'report.department.status',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentChart',
//    ]);
//
//    Route::get('department/purpose', [
//        'as'   => 'report.department.purpose',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentStatusType',
//    ]);
//
//    Route::get('department/source', [
//        'as'   => 'report.department.source',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentSource',
//    ]);
//    Route::get('department', [
//        'as'   => 'report.department',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@department',
//    ]);
//    Route::get('department/datatable', [
//        'as'   => 'report.department.datatable',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@datatable',
//    ]);
//    Route::post('department/export', [
//        'as'   => 'report.department.export',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentExport',
//    ]);
//    Route::post('department/mail', [
//        'as'   => 'report.department.mail',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@mail',
//    ]);

    /**
     * Teams
     */

//    Route::get('team/all', [
//        'as'   => 'report.team.all',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamAll',
//    ]);
//
//    Route::get('team/priority', [
//        'as'   => 'report.team.priority',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamPriority',
//    ]);
//
//    Route::get('team/sla', [
//        'as'   => 'report.team.all',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamSla',
//    ]);
//
//    Route::get('team/helptopic', [
//        'as'   => 'report.team.helptopic',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamHelptopic',
//    ]);
//
//    Route::get('team/status', [
//        'as'   => 'report.team.status',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamChart',
//    ]);
//
//    Route::get('team/purpose', [
//        'as'   => 'report.team.purpose',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamStatusType',
//    ]);
//
//    Route::get('team/source', [
//        'as'   => 'report.team.source',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamSource',
//    ]);
//
//    Route::get('team', [
//        'as'   => 'report.team.get',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@getTeam',
//    ]);
//    Route::get('team/datatable', [
//        'as'   => 'report.team.datatable',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@datatable',
//    ]);
//
//    Route::post('team/export', [
//        'as'   => 'report.team.export',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamExport',
//    ]);
//    Route::post('team/mail', [
//        'as'   => 'report.team.mail',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@mail',
//    ]);

    /**
     * Agents
     */

//    Route::get('agent', [
//        'as'   => 'report.agent',
//        'uses' => 'App\FaveoReport\Controllers\AgentReport@getAgent',
//    ]);
//
//    Route::get('get/agent', [
//        'as'   => 'report.agent.get',
//        'uses' => 'App\FaveoReport\Controllers\AgentReport@agent',
//    ]);
//
//    Route::get('agent/status', [
//        'as'   => 'report.agent.status',
//        'uses' => 'App\FaveoReport\Controllers\AgentReport@agentChart',
//    ]);
//    Route::get('agent/status/datatable', [
//        'as'   => 'report.agent.status.datatable',
//        'uses' => 'App\FaveoReport\Controllers\AgentReport@getAgentStatusTable',
//    ]);
//
//    Route::post('agent/export', [
//        'as'   => 'report.agent.export',
//        'uses' => 'App\FaveoReport\Controllers\AgentReport@AgentExport',
//    ]);
//    Route::post('agent/mail', [
//        'as'   => 'report.agent.mail',
//        'uses' => 'App\FaveoReport\Controllers\AgentReport@mail',
//    ]);

    /**
     * Export
     */

    Route::post('export', [
        'as'   => 'report.export',
        'uses' => 'App\FaveoReport\Controllers\ReportController@export',
    ]);

//    Route::post('all/mail', [
//        'as'   => 'report.mail',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@mail',
//    ]);
//

//    Route::get('mail/to', [
//        'as'   => 'report.to',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@MailTo',
//    ]);

//    Route::get('download/csv', [
//        'as'   => 'report.download',
//        'uses' => 'App\FaveoReport\Controllers\ReportController@download',
//    ]);


//    Route::get('indepth/get', [
//        'as'   => 'report.indepth.api',
//        'uses' => 'App\FaveoReport\Controllers\ReportIndepth@getTickets',
//    ]);
//
//    Route::get('trends/get', [
//        'as'   => 'report.indepth.api',
//        'uses' => 'App\FaveoReport\Controllers\ReportIndepth@getTrends',
//    ]);
//
//    Route::get('trends/analysis/get', [
//        'as'   => 'report.trends.analysis',
//        'uses' => 'App\FaveoReport\Controllers\ReportIndepth@getTrendsLoad',
//    ]);
//
//    Route::get('trends/analysis/day', [
//        'as'   => 'report.trends.day',
//        'uses' => 'App\FaveoReport\Controllers\ReportIndepth@dayTrends',
//    ]);
//
//    Route::get('trends/analysis/hour', [
//        'as'   => 'report.trends.hour',
//        'uses' => 'App\FaveoReport\Controllers\ReportIndepth@hour',
//    ]);

//    Route::get('api/get/agents', [
//        'as'   => 'report.agent.get',
//        'uses' => 'App\FaveoReport\Controllers\Utility@agentApi',
//    ]);
//
//    Route::get('api/get/departments', [
//        'as'   => 'report.department.get',
//        'uses' => 'App\FaveoReport\Controllers\Utility@departmentApi',
//    ]);
//
//    Route::get('api/get/priorities', [
//        'as'   => 'report.priority.get',
//        'uses' => 'App\FaveoReport\Controllers\Utility@priorityApi',
//    ]);
//
//    Route::get('api/get/sources', [
//        'as'   => 'report.source.get',
//        'uses' => 'App\FaveoReport\Controllers\Utility@sourceApi',
//    ]);
//
//    Route::get('api/get/clients', [
//        'as'   => 'report.client.get',
//        'uses' => 'App\FaveoReport\Controllers\Utility@clientApi',
//    ]);
//    Route::get('api/get/types', [
//        'as'   => 'report.type.get',
//        'uses' => 'App\FaveoReport\Controllers\Utility@typeApi',
//    ]);
//    Route::get('api/get/status', [
//        'as'   => 'report.status.get',
//        'uses' => 'App\FaveoReport\Controllers\Utility@statusApi',
//    ]);
//    Route::get('api/get/helptopic', [
//        'as'   => 'report.helptopic.get',
//        'uses' => 'App\FaveoReport\Controllers\Utility@helptopicApi',
//    ]);
//    Route::get('api/get/team', [
//        'as'   => 'report.team.api',
//        'uses' => 'App\FaveoReport\Controllers\Utility@teamApi',
//    ]);
//
//    Route::get('api/get/creator', [
//        'as'   => 'report.creator.api',
//        'uses' => 'App\FaveoReport\Controllers\Utility@creatorApi',
//    ]);

//    Route::get('agent/performance/api', [
//        'as'   => 'report.agent.performance.api',
//        'uses' => 'App\FaveoReport\Controllers\AgentReport@agentPerformance',
//    ]);
//    Route::get('agent/performance/api/datatable', [
//        'as'   => 'report.agent.performance.api.datatable',
//        'uses' => 'App\FaveoReport\Controllers\AgentReport@agentDatatable',
//    ]);
//
//
//    Route::get('department/performance/api', [
//        'as'   => 'report.department.performance.api',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentPerformance',
//    ]);
//    Route::get('department/performance/api/datatable', [
//        'as'   => 'report.department.performance.api.datatable',
//        'uses' => 'App\FaveoReport\Controllers\DepartmentReport@departmentDatatable',
//    ]);
//
//    Route::get('team/performance/api', [
//        'as'   => 'report.team.performance.api',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamPerformance',
//    ]);
//    Route::get('team/performance/api/datatable', [
//        'as'   => 'report.team.performance.api.datatable',
//        'uses' => 'App\FaveoReport\Controllers\TeamReport@teamDatatable',
//    ]);


//    Route::get('performance/first_response', [
//        'as'   => 'report.performance.first_response',
//        'uses' => 'App\FaveoReport\Controllers\Performance@firstResponsePerformance',
//    ]);
//
//    Route::get('performance/avg_response', [
//        'as'   => 'report.performance.avg_response',
//        'uses' => 'App\FaveoReport\Controllers\Performance@avgResponsePerformance',
//    ]);
//
//    Route::get('performance/avg_resolution', [
//        'as'   => 'report.performance.avg_resolution',
//        'uses' => 'App\FaveoReport\Controllers\Performance@resolutionPerformance',
//    ]);
//
//    Route::get('performance/first_response_response_trend', [
//        'as'   => 'report.performance.first_response_response_trend',
//        'uses' => 'App\FaveoReport\Controllers\Performance@firstAndResponseTrend',
//    ]);

    /**
     * Timesheet
     */
    Route::get('timesheet', [
        'as'   => 'report.timesheet',
        'uses' => 'App\FaveoReport\Controllers\Performance@timesheetView',
    ]);

    Route::get('timesheet/datatable', [
        'as'   => 'report.timesheet.datatable',
        'uses' => 'App\FaveoReport\Controllers\Performance@timesheetView',
    ]);

    Route::get('timesheet/data', [
        'as'   => 'report.timesheet.data',
        'uses' => 'App\FaveoReport\Controllers\Performance@getBillDetails',
    ]);

    Route::get('timesheet/datatable', [
        'as'   => 'report.timesheet.datatable',
        'uses' => 'App\FaveoReport\Controllers\Performance@timesheetDatatable',
    ]);

    Route::post('timesheet/export', [
        'as'   => 'report.timesheet.export',
        'uses' => 'App\FaveoReport\Controllers\Performance@exportTimesheet',
    ]);

    Route::post('timesheet/mail', [
        'as'   => 'report.timesheet.mail',
        'uses' => 'App\FaveoReport\Controllers\Performance@mail',
    ]);

    Route::get('organization/get', [
        'as'   => 'report.organization.get',
        'uses' => 'App\FaveoReport\Controllers\ReportIndepth@getOrg',
    ]);

    Route::get('rating/get', [
        'as'   => 'report.rating.get',
        'uses' => 'App\FaveoReport\Controllers\ReportIndepth@getSatisfaction',
    ]);
    Route::get('rating', [
        'as'   => 'report.rating',
        'uses' => 'App\FaveoReport\Controllers\ReportIndepth@satisfactionView',
    ]);

//    //Routes for management
//    Route::get('management/performance', [
//        'as'   => 'report.management.performance',
//        'uses' => 'App\FaveoReport\Controllers\ManagementReport@getView',
//    ]);
//    Route::post('management/report', [
//        'as'   => 'management.report',
//        'uses' => 'App\FaveoReport\Controllers\ManagementReport@ManagementQuery',
//    ]);

    // Exports
    Route::get('activity-download', [
        'as'   => 'report.activity.download',
        'uses' => 'App\FaveoReport\Controllers\ReportExportController@index',
    ]);

    Route::get('api/agent/exports', [
        'as'   => 'report.exports.list',
        'uses' => 'App\FaveoReport\Controllers\ReportExportController@indexList',
    ]);

    Route::get('api/agent/export/download/{hash}', [
        'as'   => 'report.exports.download',
        'uses' =>  'App\FaveoReport\Controllers\BaseReportController@downloadReport',
    ]);

    Route::delete('api/agent/export/delete/{id}', [
        'as'   => 'report.exports.delete',
        'uses' => 'App\FaveoReport\Controllers\ReportExportController@delete',
    ]);
});

Route::group(['middleware' => ['web', 'auth', 'role.admin']], function() {

    Route::get('report/settings', ['as' => 'report.settings', 'uses' => 'App\FaveoReport\Controllers\Admin\SettingsController@showSettings']);

    Route::get('api/report-settings', ['as' => 'api.report.settings', 'uses' => 'App\FaveoReport\Controllers\Admin\SettingsController@getReportSettings']);

    Route::post('api/report-settings', ['as' => 'api.report.settings.store', 'uses' => 'App\FaveoReport\Controllers\Admin\SettingsController@storeSettings']);
});

/*
  NEWLY ADDED ROUTES BY AVINASH
  NOTE: Old routes must be removed once new ones are functional
 */
Route::group(['middleware' => ['web', 'auth', 'dbNotUpdated','role.agent', \App\FaveoReport\Middleware\AccessReport::class]], function() {

    Route::get('report/get', ['as'   => 'report.get', 'uses' => 'App\FaveoReport\Controllers\ReportController@groupView']);

    Route::get('api/agent/report-columns/{subReportId}', 'App\FaveoReport\Controllers\ApiReportController@getSubReportColumnsBySubReportId');

    Route::post('api/agent/report-columns/{subReportId}', 'App\FaveoReport\Controllers\ApiReportController@postSubReportColumnsBySubReportId');

    Route::get('api/agent/management-report/{reportId}', ['as' => 'report.management-report', 'uses' => 'App\FaveoReport\Controllers\ManagementReportController@getManagementReportData']);

    Route::get('api/agent/top-customer-analysis/{reportId}', ['as' => 'report.top-customer-analysis', 'uses' => 'App\FaveoReport\Controllers\TopCustomerAnalysisController@getOrganizationReport']);

    Route::get('api/agent/helpdesk-in-depth/{reportId}', ['as' => 'report.helpdesk-in-depth', 'uses' => 'App\FaveoReport\Controllers\HelpdeskInDepth@getReports']);

    Route::get('api/agent/helpdesk-in-depth-widget/{reportId}', ['as' => 'report.helpdesk-in-depth-widget', 'uses' => 'App\FaveoReport\Controllers\HelpdeskInDepth@getWidgetData']);

    Route::get('api/report-shortcodes/{type?}', 'App\FaveoReport\Controllers\ApiReportController@getReportShortCodes');

    Route::post('api/add-custom-column/{subReportId}', 'App\FaveoReport\Controllers\ApiReportController@addCustomColumn');

    Route::delete('api/delete-custom-column/{id}', 'App\FaveoReport\Controllers\ApiReportController@deleteCustomColumn');

    Route::get('/agent/export/download/{hash}', ['as'   => 'report.export.download', 'uses' => 'App\FaveoReport\Controllers\BaseReportController@downloadReport']);

    Route::get('api/agent/agent-performance-report/{reportId}', 'App\FaveoReport\Controllers\PerformanceController@getAgentPerformanceData');

    Route::get('api/agent/team-performance-report/{reportId}', 'App\FaveoReport\Controllers\PerformanceController@getTeamPerformanceData');

    Route::get('api/agent/department-performance-report/{reportId}', 'App\FaveoReport\Controllers\PerformanceController@getDepartmentPerformanceData');

    Route::get('api/agent/performance-distribution/time-report/{reportId}', 'App\FaveoReport\Controllers\PerformanceDistribution@getTimeReport');

    Route::get('api/agent/performance-distribution/trend-report/{reportId}', 'App\FaveoReport\Controllers\PerformanceDistribution@getTrendReport');

    Route::get('api/agent/ticket-volume-trend/overall-ticket-trend/{reportId}', 'App\FaveoReport\Controllers\TicketVolumeTrend@getOverallTicketTrend');

    Route::get('api/agent/ticket-volume-trend/overall-ticket-trend-widget/{reportId}', 'App\FaveoReport\Controllers\TicketVolumeTrend@getOverallTicketTrendWidget');

    Route::get('api/agent/ticket-volume-trend/day-ticket-trend/{reportId}', 'App\FaveoReport\Controllers\TicketVolumeTrend@getDayTicketTrend');

    Route::get('api/agent/ticket-volume-trend/day-ticket-trend-widget/{reportId}', 'App\FaveoReport\Controllers\TicketVolumeTrend@getDayTicketTrendWidget');

    Route::post('api/agent/report-export/{reportId}', 'App\FaveoReport\Controllers\ApiReportController@triggerReportExport');

    Route::get('api/agent/report-list', 'App\FaveoReport\Controllers\ApiReportController@getReportList');

    Route::get('api/agent/report-config/{reportId}', 'App\FaveoReport\Controllers\ApiReportController@getReportConfigByReportId');

    Route::post('api/agent/report-config/{parentReportId?}', 'App\FaveoReport\Controllers\ApiReportController@postReportConfigByReportId');

    Route::get('reports/{reportType}/{reportId}', 'App\FaveoReport\Controllers\ApiReportController@getReportView')->name("report.details");

    Route::delete('api/report/{reportId}', 'App\FaveoReport\Controllers\ApiReportController@deleteReport');

    Route::get('api/agent/daily-report', 'App\FaveoReport\Controllers\DailyReport@getView');
});

// These routes don't need report permission to be executed
Route::group(['middleware' => ['web', 'auth', 'role.agent','dbNotUpdated']], function() {
    // gets dashboard view
    Route::get('dashboard', 'App\FaveoReport\Controllers\DashboardController@getDashboardView')->name("dashboard");

    Route::get('dashboard-old-layout', 'App\FaveoReport\Controllers\DashboardController@getOldDashboardView')->name("dashboard-old");

    // dashboard report routes
    Route::get('api/agent/dashboard-report/top-widget', 'App\FaveoReport\Controllers\DashboardController@getDashboardTopWidget');

    Route::get('api/agent/dashboard-report/agent-performance-widget', 'App\FaveoReport\Controllers\DashboardController@getAgentPerformanceWidget');

    Route::get('api/agent/dashboard-report/require-immediate-action', 'App\FaveoReport\Controllers\DashboardController@getRequireImmediateAction');

    Route::get('api/agent/dashboard-report/system-analysis', 'App\FaveoReport\Controllers\DashboardController@getSystemAnalysis');

    Route::get('api/agent/dashboard-report/manager/{type}', 'App\FaveoReport\Controllers\DashboardController@getManagerSpecificReportAnalysis');


    // "todo" CRUD routes
    Route::get('api/agent/todo-list', 'App\FaveoReport\Controllers\DashboardController@getTodoList');

    Route::post('api/agent/create-todo', 'App\FaveoReport\Controllers\DashboardController@createTodo');

    Route::post('api/agent/update-todos', 'App\FaveoReport\Controllers\DashboardController@updateTodos');

    Route::delete('api/agent/todo/{id}', 'App\FaveoReport\Controllers\DashboardController@deleteTodo');

});

\Event::listen(STS\ZipStream\Events\ZipSizePredictionFailed::class, function () {
    \Logger::exception(new \Exception('Zip failed due to bad size prediction.'), 'report');
});

