@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.widgets-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.widgets-page-description') !!}">

@stop

@section('Manage')
active
@stop

@section('theme-bar')
active
@stop

@section('widget')
class="active"
@stop
@section('PageHeader')
<h1>{!! Lang::get('lang.widgets') !!}</h1>
@stop
@section('content')
<div id="app-admin">
    <widget-list></widget-list>
</div>
@stop