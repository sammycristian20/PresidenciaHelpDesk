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

<!-- content -->
@section('content')
<div class="box-body" id="app-admin">
    <faveo-automator category="workflow"></faveo-automator>
</div>
@stop

