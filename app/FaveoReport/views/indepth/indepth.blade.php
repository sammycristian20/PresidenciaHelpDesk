@extends('themes.default1.agent.layout.agent')
@section('Staffs')
active
@stop

@section('staffs-bar')
active
@stop

@section('Report')
class="active"
@stop

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('report::lang.helpdesk-in-depth') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('content')
<div id="faveo-report">
    <chart-report-layout
        panel-title="overall_ticket_summary"
        report-panel-title="overall_ticket_summary"
        category-chart-data-api="api/agent/helpdesk-in-depth"
        widget-data-api="api/agent/helpdesk-in-depth-widget"
        :categories="['priority', 'source', 'type', 'status']"
        default-category="priority"
        category-prefix="view_by"
        panel-row-class="col-md-6">
    </chart-report-layout>
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>
@stop

