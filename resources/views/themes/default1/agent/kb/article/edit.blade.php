@extends('themes.default1.agent.layout.agent')
@extends('themes.default1.agent.layout.sidebar')  
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.article_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.article_edit-page-description') !!}">
@stop     

@section('custom-css')
<!--Daterangepicker-->
<link href="{{assetLink('css','bootstrap-datetimepicker4')}}" rel="stylesheet" type="text/css" />
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media = 'all';"/>
@stop   

@section('article')
active
@stop

@section('add-article')
class="active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.article')}}</h1>
@stop

@section('content')
     <div id="app-agent">
        <articles></articles>
    </div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
