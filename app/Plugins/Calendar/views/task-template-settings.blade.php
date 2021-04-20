@extends('themes.default1.admin.layout.admin')
@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
    <h1>{{ trans('Calendar::lang.task-plugin-task-template-settings') }}</h1>
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

        <task-template-settings></task-template-settings>

    </div>
    <script src="{{ bundleLink('js/calendar.js') }}" type="text/javascript"></script>
@stop
