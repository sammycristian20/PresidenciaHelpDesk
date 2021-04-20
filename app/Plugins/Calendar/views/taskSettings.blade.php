@extends('themes.default1.admin.layout.admin')
@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ trans('Calendar::lang.task-plugin-project-and-category-settings') }}</h1>
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
    
    <task-settings />

</div>  
<script src="{{ bundleLink('js/calendar.js') }}" type="text/javascript"></script>
@stop
