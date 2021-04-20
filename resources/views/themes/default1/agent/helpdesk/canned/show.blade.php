@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.canned_response_show-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.canned_response_show-page-description') !!}">

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

<!-- content -->
@section('content')

	<!-- <section class="content"> -->
<div class="box box-primary">

	<div class="box-header">
		<h3 class="box-title">hi</h3>
	</div>

	<div class="box-body">
		<pre>hello</pre>
	</div>

@stop
<!-- /content -->