@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.listeners') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.listeners') !!}">

@stop

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('listener')
class="active"
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.listeners')}} </h1>
@stop

@section('content')

<div id="app-admin">
    <faveo-automator category="listener"></faveo-automator>
</div>

@stop
