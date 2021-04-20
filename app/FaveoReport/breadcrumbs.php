<?php
Breadcrumbs::register('report.get', function($breadcrumbs)
{
    $breadcrumbs->push(Lang::get('lang.helpdesk_reports'), route('report.get'));
});

Breadcrumbs::register('report.details', function($breadcrumbs, $reportType, $reportId)
{
    $breadcrumbs->parent('report.get');

    $report = \App\FaveoReport\Models\Report::where("id", $reportId)->first();

    $reportName = $report ? $report->name : \Lang::get("report::lang.report_details");

    $breadcrumbs->push($reportName, route('report.details', [$reportType, $reportId]));
});

Breadcrumbs::register('report.agent', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push('Agent Report', route('report.agent'));
});

Breadcrumbs::register('report.team.get', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push('Team Report', route('report.team.get'));
});

Breadcrumbs::register('report.department', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push('Deparment Report', route('report.department'));
});

/**
 * new
 */

Breadcrumbs::register('report.indepth', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.helpdesk-in-depth'), route('report.indepth'));
});

Breadcrumbs::register('report.trends', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.ticket-volume-trends'), route('report.trends'));
});

Breadcrumbs::register('report.agent.performance', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.agent-performance'), route('report.agent.performance'));
});

Breadcrumbs::register('report.department.performance', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.department-performance'), route('report.department.performance'));
});

Breadcrumbs::register('report.team.performance', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.team-performance'), route('report.team.performance'));
});

Breadcrumbs::register('report.performance', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.performance-distribution'), route('report.performance'));
});

Breadcrumbs::register('report.timesheet', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.time-sheet-summary'), route('report.timesheet'));
});

Breadcrumbs::register('report.management.performance', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.management-performance'),route('report.management.performance'));
});

Breadcrumbs::register('report.management.performance.export.download', function($breadcrumbs)
{
    $breadcrumbs->parent('report.management.performance');
    $breadcrumbs->push(Lang::get('report::lang.management-performance'),route('report.management.performance'));
});


Breadcrumbs::register('report.organization', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.top-customer-analysis'), route('report.organization'));
});

Breadcrumbs::register('report.rating', function($breadcrumbs)
{
    $breadcrumbs->parent('report.get');
    $breadcrumbs->push(Lang::get('report::lang.satisfaction-survey'), route('report.rating'));
});

Breadcrumbs::register('report.activity.download', function($breadcrumbs)
{
    $breadcrumbs->push(Lang::get('report::lang.activity-download'),route('report.activity.download'));
});

Breadcrumbs::register('report.settings', function($breadcrumbs)
{
    $breadcrumbs->push(Lang::get('report::lang.report-settings'), route('report.settings'));
});

Breadcrumbs::register('dashboard-old', function($breadcrumbs)
{
    $breadcrumbs->push(Lang::get('lang.old_dashboard'), route('dashboard-old'));
});
