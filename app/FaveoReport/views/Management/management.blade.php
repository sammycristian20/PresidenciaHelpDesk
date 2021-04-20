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

@section('PageHeader')
<h1>{!! Lang::get('report::lang.management_report') !!}</h1>
@stop

@section('content')
<div id="faveo-report">
  <tabular-report-layout
    panel-title="management-performance"
    data-url="api/agent/management-report"
    column-url="api/agent/report-columns/management_report"
    save-column-url="api/agent/report-columns"
    export-url="api/agent/report-export/management_report"
    add-custom-column-url="api/add-custom-column"
    delete-custom-column-url="api/delete-custom-column"
    short-code-url="api/report-shortcodes"
    />
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>
@stop

