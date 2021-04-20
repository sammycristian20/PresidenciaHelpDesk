@extends('themes.default1.agent.layout.agent')
{{-- 
    Need to implment this page in Vue.js to remove angular code
--}}

<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.canned_response_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.canned_response_edit-page-description') !!}">

@stop
@section('custom-css')
<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>
@stop
@section('Tools')
class="active"
@stop



@section('tools-bar')
active
@stop

@section('tools')
class="active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.canned_response')}}</h1>
@stop



<!-- content -->
@section('content')
    <div id="app-agent">
        <canned-response></canned-response>
    </div>
    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop
