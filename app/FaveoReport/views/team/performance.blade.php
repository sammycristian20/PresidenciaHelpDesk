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
<h1>{!! Lang::get('report::lang.performance-report') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('content')
<div id="faveo-report">
  <tabular-report-layout
    panel-title="team-performance"
    data-url="api/agent/team-performance-report"
    column-url="api/agent/report-columns/team_performance_report"
    save-column-url="api/agent/report-columns"
    export-url="api/agent/report-export/team_performance_report"
    add-custom-column-url="api/add-custom-column/team_performance_report"
    delete-custom-column-url="api/delete-custom-column"
    short-code-url="api/report-shortcodes/team_performance_report"
    />
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>
@stop

