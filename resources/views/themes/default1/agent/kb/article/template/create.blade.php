@extends('themes.default1.agent.layout.agent')
@extends('themes.default1.agent.layout.sidebar') 
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.article_template_create-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.article_template_create-page-description') !!}">
@stop     
@section('custom-css')
<link href="{{assetLink('css','blue')}}" rel="stylesheet"  type="text/css"/>
@stop    
@section('article')
active
@stop



@section('add-article-template')
class="active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.article_template')}}</h1>
@stop



@section('content')
    <div id="app-agent">
        <article-template></article-template>
    </div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
