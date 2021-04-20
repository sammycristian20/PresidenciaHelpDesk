@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.social-widget-settings-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.social-widget-settings-page-description') !!}">


@stop


@section('theme-bar')
active
@stop

@section('socail')
class="active"
@stop
@section('PageHeader')
<h1>{!! Lang::get('lang.widgets') !!}</h1>
@stop
<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>
@section('content')
<div id="app-admin">
    <social-widget-list></social-widget-list>
</div>
@stop