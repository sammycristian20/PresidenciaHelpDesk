@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.agent_create-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.agent_create-page-description') !!}">
@stop
@section('custom-css')
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="{{assetLink('css','bootstrap-select')}}">
<!--select 2-->

<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
<link rel="stylesheet" href="{{assetLink('css','intlTelInput')}}">
<style type="text/css" media="screen">
     .list-group>li>a:hover,.list-group>li>a:focus{
        color: rgb(51, 122, 183) !important;
        text-decoration: none !important;
     }
     .list-group>li>a:focus{
        outline-offset: 0px;
        outline: none !important;
     }
    .permission-menu{
        width: 300px !important;
    }
    .permisson-drop:hover, .permisson-drop:active, .permisson-drop.hover {
        background-color: #e7e7e7 !important;
    }
    .permisson-drop {
        background-color: #f4f4f4;
        color: #444;
        border-color: #ddd;
        width: 90%;
    }
    .permisson-drop {
        -webkit-box-shadow: none;
        box-shadow: none;
        border: 1px solid transparent;
    }
    .permisson-drop {
        -moz-user-select: none;
        background-image: none;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-block;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.42857;
        margin-bottom: 0;
        padding: 6px 12px;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    .notific {
        height: 50px;
    }
    .faveo-choice{
        background-color: #e4e4e4;
        border: 1px solid #aaa;
        border-radius: 4px;
        padding: 3px;
        cursor: default;
        margin: 5px;
        display: table;
    }
    .label-warning{
        right: 6px !important;
    }
    /* span{
        white-space: nowrap;
    } */
</style>
@stop
@section('Staffs')
active
@stop

@section('staffs-bar')
active
@stop

@section('agents')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.agents')}}</h1>
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
<create-update-agent></create-update-agent>
</div>
@stop

