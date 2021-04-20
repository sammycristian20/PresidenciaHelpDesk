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
    <h1>{{ $reportTitle }}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('content')

<div id="faveo-report">
    <report-entry-page></report-entry-page>
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>

@stop
