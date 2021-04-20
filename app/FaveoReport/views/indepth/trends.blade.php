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
<h1>{!! Lang::get('report::lang.ticket-volume-trends') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('content')
<div id="faveo-report">
  <ticket-volumn-trend-report/>
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>
@stop
