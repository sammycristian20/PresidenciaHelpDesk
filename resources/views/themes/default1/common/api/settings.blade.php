@extends('themes.default1.admin.layout.admin')

<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (isset($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.api_settings-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.api_settings-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('api-set')
class=active
@stop

@section('settings-bar')
active
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.api')}}</h1>
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
        
        <api-settings></api-settings>
    </div>
@stop
