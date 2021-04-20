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
<h1>{!! Lang::get('report::lang.performance-distribution') !!}</h1>
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
    panel-title="ticket_resolution_response_time_summary"
    report-panel-title="ticket_resolution_response_time_summary"
    category-chart-data-api="api/agent/performance-distribution/time-report"
    time-series-chart-data-api="api/agent/performance-distribution/trend-report"
    />
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>

@stop
