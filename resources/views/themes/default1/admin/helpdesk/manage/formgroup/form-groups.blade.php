@extends('themes.default1.admin.layout.admin')

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('forms-ticket')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.form-groups') !!}</h1>
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
    <form-group-list></form-group-list>
  </div>

@stop

