@extends('themes.default1.admin.layout.admin')

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('websocket')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.settings') }}</h1>
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

  <div id="app-admin">
    <web-socket-settings></web-socket-settings>
  </div>    
@stop

