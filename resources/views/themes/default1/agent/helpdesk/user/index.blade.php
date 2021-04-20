@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.user_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.user_lists-page-description') !!}">
@stop

@section('custom-css')

<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css" media="none" onload="this.media='all';"/>
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

<!-- @section('HeadInclude')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}" type="text/javascript" async></script>
@stop -->
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.user_directory')}}</h1>
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
    <div id="app-agent">
        
        <user-directory></user-directory>
    </div>
    
    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop
<!-- /content -->