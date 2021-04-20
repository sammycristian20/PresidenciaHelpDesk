@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.listeners') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.listeners') !!}">


@stop
@section('custom-css')
<link href="{{assetLink('css','angular-ui-tree')}}" rel="stylesheet" type="text/css" >
<link href="{{assetLink('css','app-form')}}" rel="stylesheet" type="text/css" >
<style>
    .angular-ui-tree-handle {
        background: transparent;
        border: none;
        color: transparent;
        padding: 0px 13px;
    }
    .angular-ui-tree-empty{
    	border: none !important;
    	background-image: none !important;
    	background: transparent;
    }
</style>
@stop
@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('listener')
class="active"
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.listeners')}} </h1>
@stop

@section('content')
<div id="app-admin">
    <listener-index></listener-index>
</div>
@stop
