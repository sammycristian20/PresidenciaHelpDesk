@extends('themes.default1.agent.layout.agent')
@section('custom-css')
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="{{assetLink('css','bootstrap-select-min')}}">
<link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css">
<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" />
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
@section('Users')
class="active"
@stop

@section('user-bar')
active
@stop

@section('user')
class="active"
@stop

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.edit_user') !!}</h1>
@stop
<!-- /breadcrumbs -->
<!-- content -->



@section('content')

<div id="app-admin">

<create-update-agent></create-update-agent>
</div>

<script src="{{ bundleLink('js/admin.js') }}"></script>
@stop
