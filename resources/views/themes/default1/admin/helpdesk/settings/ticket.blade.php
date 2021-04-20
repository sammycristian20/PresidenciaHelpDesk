@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title :"SUPPORT CENTER";
    ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.ticket-setting-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.ticket-setting-page-description') !!}">


@stop

@section('Tickets')
active
@stop

@section('tickets-bar')
active
@stop

@section('tickets')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.ticket') }}</h1>
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
        
        <ticket-settings></ticket-settings>
    </div>
@stop
