@extends('themes.default1.admin.layout.admin')

@section('system-logs')
active
@stop

@section('error-bugs')
active
@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ __('lang.system-logs') }}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('content')
<div id="app-system-logs">
    <logs-index></logs-index>
</div>
<script src="{{bundleLink('js/faveoLog.js')}}" type="text/javascript"></script>
@stop
