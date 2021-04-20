@extends('themes.default1.agent.layout.agent')
<?php
 $itilSettings = \App\Model\helpdesk\Settings\CommonSettings::where('option_name','itil')->value('status');
 $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
 $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>

@if($itilSettings && $itilSettings != 1)
 @include ('themes.default1.agent.layout.sidebar')
@endif
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.canned_response_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.canned_response_lists-page-description') !!}">

@stop
@section('custom-css')

<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css"/ media="none" onload="this.media='all';">

@stop


@section('tools-bar')
active
@stop

@section('canned-response')
class = "active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.tools')}}</h1>
@stop

<!-- content -->
@section('content')
    <div id="app-agent">
        <canned-response-directory></canned-response-directory>
    </div>
    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop
<!-- /content -->
