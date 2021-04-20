@extends('themes.default1.admin.layout.admin')

@section('Plugins')
active
@stop

@section('settings-bar')
active
@stop

@section('plugin')
class="active"
@stop

@section('custom-css')
  <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.plugins') !!}</h1>
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
     <plugins-page />
 </div>
@stop
