@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.system-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.system-page-description') !!}">

@stop

<style type="text/css">
    
    .bootstrap-select>.dropdown-toggle, .bootstrap-select>.dropdown-toggle:hover { 
        border: 1px solid #ced4da !important;
        background: white !important; 
    }
</style>

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('system')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.settings')}}</h1>
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
    
      <system-settings></system-settings>
  </div>
@stop
