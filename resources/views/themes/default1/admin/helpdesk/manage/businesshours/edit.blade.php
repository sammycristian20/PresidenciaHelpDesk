@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }

        ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.business_hours_edit-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.business_hours_edit-page-description') !!}">


@stop

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('business_hours')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.business_hours') !!}</h1>
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
<div id="app-admin">
    <business-hours></business-hours>
</div>
@stop
