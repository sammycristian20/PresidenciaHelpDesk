@extends('themes.default1.admin.layout.admin')
@section('custom-css')
<link href="{{assetLink('css','bootstrap-switch-min')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
<style>
   input[type="radio"]:focus, input[type="checkbox"]:focus {
       outline: none !important;
     }
.tooltip-inner:not(.different){
  white-space:nowrap;
  max-width:none;
}
.dropdown-menu{
   z-index: 99;
}

.modal-open {
    overflow: auto!important;
}
</style>
@stop
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
<h1>{!! Lang::get('lang.forms') !!}</h1>
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
    <form-builder-main></form-builder-main>
  </div>    
@stop

