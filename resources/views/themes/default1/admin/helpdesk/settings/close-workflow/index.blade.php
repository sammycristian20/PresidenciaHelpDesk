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


<meta name="title" content="{!! Lang::get('lang.close_ticket_workflow-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.close_ticket_workflow-page-description') !!}">


@stop
@section('custom-css')
        <link href="{{assetLink('css', 'select2')}}" rel="stylesheet" type="text/css"/>
@stop
@section('Tickets')
active
@stop

@section('close-workflow')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.close_ticket_workflow') !!}</h1>
@stop

@section('header')
@stop

@section('content')
    
    <div id="app-admin">
        
        <close-ticket-workflow></close-ticket-workflow>
    </div>
@stop
