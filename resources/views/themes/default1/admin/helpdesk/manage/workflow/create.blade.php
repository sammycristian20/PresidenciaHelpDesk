@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.workflow_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.workflow_lists-page-description') !!}">

@stop

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('workflow')
class="active"
@stop

@section('HeadInclude')
@stop

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.workflow') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{!! URL::route('setting') !!}"><i class="fa fa-dashboard"></i> {!! Lang::get('lang.home') !!}</a></li>
	<li><a href="{!! URL::route('workflow') !!}">{!! Lang::get('lang.ticket_workflow') !!}</a></li>
	<li class="active"><a href="{!! URL::route('workflow.create') !!}">{!! Lang::get('lang.create_workflow') !!}</a></li>
</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<div id="app-admin">
	<faveo-automator category="workflow"></faveo-automator>
</div>
@stop

