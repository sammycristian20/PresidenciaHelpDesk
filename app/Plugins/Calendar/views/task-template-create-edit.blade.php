@extends('themes.default1.admin.layout.admin')
@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
    <h1>{{ isset($template) ? trans('Calendar::lang.task-plugin-task-template-edit') : trans('Calendar::lang.task-plugin-task-template-create') }}</h1>
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

        @if(isset($template))
            <task-template-create-edit template-details="{{  json_encode($template) }}"></task-template-create-edit>
        @else
            <task-template-create-edit></task-template-create-edit>
        @endif

    </div>
    <script src="{{ bundleLink('js/calendar.js') }}" type="text/javascript"></script>
@stop
