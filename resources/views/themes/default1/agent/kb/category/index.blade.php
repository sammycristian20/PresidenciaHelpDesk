@extends('themes.default1.agent.layout.agent')

@extends('themes.default1.agent.layout.sidebar')    
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.category-list-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.category_lists-page-description') !!}">

@stop 


@section('custom-css')
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css"/>
@stop

@section('category')
active
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.category')}}</h1>
@stop

@section('all-category')
class="active"
@stop
@section('custom-css')
<style type="text/css">

.header {
  width: 500px;
  background: aqua;
  margin: 0 auto;
  padding: 10px;
  display: table;
}

table { table-layout:fixed; word-break:break-all; word-wrap:break-word; }

</style>
@stop
@section('content')
<div id="app-agent">
        <category-index></category-index>
    </div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop