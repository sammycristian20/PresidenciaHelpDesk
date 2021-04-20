@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.email_diagnostic-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.email_diagnostic-page-description') !!}">


@stop
@section('Emails')
active
@stop

@section('emails-bar')
active
@stop

@section('diagnostics')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.email_diagnostic')}}</h1>
@stop
<!-- /header -->
<!-- content -->
@section('content')
    
    <div id="app-admin">
        
        <email-diagnostics></email-diagnostics>
    </div>
@stop