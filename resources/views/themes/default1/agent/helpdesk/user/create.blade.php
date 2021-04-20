@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.create-user-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.create-user-description') !!}">

@stop

@section('custom-css')
<link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css">
<!-- iCheck -->
<link href="{{assetLink('css','blue')}}" rel="stylesheet" type="text/css" />
<!-- Select2 -->
<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" />
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
<h1>{!! Lang::get('lang.user') !!}</h1>
@stop
<!-- /header -->
<!-- content -->



@section('content')
@section('content')

@include('themes.default1.common.recaptcha')

<!-- open a form -->
<div id="app-agent">
    <faveo-form category="user" panel="agent"></faveo-form>
</div>
<script src="{{asset('js/agent.js')}}" type="text/javascript"></script>

<script src="{{assetLink('js','select2')}}"></script>
<script src="{{assetLink('js','icheck')}}" type="text/javascript" async></script>
@stop
