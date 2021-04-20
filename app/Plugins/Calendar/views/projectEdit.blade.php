@extends('themes.default1.admin.layout.admin')
@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('Calendar::lang.project_edit') !!}</h1>
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
    
    <project-edit />

</div>  
<script src="{{ bundleLink('js/calendar.js') }}" type="text/javascript"></script>
@stop
