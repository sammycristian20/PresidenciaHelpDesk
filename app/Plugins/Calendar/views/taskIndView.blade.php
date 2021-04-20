@extends('themes.default1.agent.layout.agent')
@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('Calendar::lang.task_ind_view') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<div id="calendar-view">
  
    <task-individual-view></task-individual-view>
    
</div>  
<script src="{{ bundleLink('js/calendar.js') }}" type="text/javascript"></script>
@stop
