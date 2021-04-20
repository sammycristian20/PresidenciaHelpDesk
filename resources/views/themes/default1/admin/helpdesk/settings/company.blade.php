@extends('themes.default1.admin.layout.admin')

<?php
    $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
    
    $titleName = ($title) ? $title :"SUPPORT CENTER";
?>

@section('meta-tags')

    <meta name="title" content="{!! Lang::get('lang.company-page-title') !!} :: {!! strip_tags($titleName) !!} ">
    
    <meta name="description" content="{!! Lang::get('lang.company-page-description') !!}">

@stop

@section('custom-css')

    <link href="{{assetLink('css','faveo-css')}}" rel="stylesheet" type="text/css" />
    
    <link rel="stylesheet" href="{{assetLink('css','intlTelInput')}}">
    
    <link href="{{assetLink('css','bootstrap-colorpicker')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>

@stop

@section('Settings')
    
    active
@stop

@section('settings-bar')
    
    active
@stop

@section('company')
    
    class="active"
@stop

@section('HeadInclude')

@stop

@section('PageHeader')
    
    <h1>{{ Lang::get('lang.settings') }}</h1>
@stop

@section('breadcrumbs')
    
    <ol class="breadcrumb"></ol>
@stop

@section('content')
    
    <div id="app-admin">
   
        <company-settings></company-settings>
    </div>

@stop