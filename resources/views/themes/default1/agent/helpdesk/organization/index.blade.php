@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.organizations_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.organizations_lists-page-description') !!}">

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

@section('organizations')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.organizations') !!}</h1>
@stop
<!-- content -->
@section('content')
<div id="app-agent">
        
        <org-directory></org-directory>
    </div>
    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop