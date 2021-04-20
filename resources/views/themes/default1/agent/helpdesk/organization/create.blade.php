@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.organizations_create-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.organizations_create-page-description') !!}">

@stop

@section('Users')
class="active"
@stop

@section('user-bar')
active
@stop

@section('organizations')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.organisation') !!}</h1>
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

@include('themes.default1.common.recaptcha')


<div id="app-agent">
    <faveo-form category="organisation" panel="agent"></faveo-form>
</div>
<script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
@section('FooterInclude')
<script src="{{assetLink('js','select2')}}" type="text/javascript"></script>
@stop
