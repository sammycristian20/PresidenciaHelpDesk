@extends('themes.default1.agent.layout.agent')
@extends('themes.default1.agent.layout.sidebar')  
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.category_create-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.category_create-page-description') !!}">

@stop   

@section('category')
active
@stop

@section('add-category')
class="active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.category')}}</h1>
@stop

@section('content')
  <div id="app-agent">
        <category></category>
    </div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
