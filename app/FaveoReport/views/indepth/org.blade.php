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
<h1>{!! Lang::get('report::lang.top-customer-analysis') !!}</h1>
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
        panel-title="top_organisation_ticket_summary"
        report-panel-title="top_organisation_ticket_summary"
        category-chart-data-api="api/agent/top-customer-analysis"
        :categories="[3, 5, 10]"
        :default-category="5"
        category-prefix="top_n"
        panel-row-class="col-md-6">
    </chart-report-layout>
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>
@stop

