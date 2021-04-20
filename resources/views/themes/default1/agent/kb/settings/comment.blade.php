@extends('themes.default1.agent.layout.agent')
@extends('themes.default1.agent.layout.sidebar')

@section('Tools')
active
@stop
@section('comment')
class="active"
@stop



<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.comments_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.comments_lists-page-description') !!}">

@stop
@section('PageHeader')
<h1>{!! Lang::get('lang.comments') !!}</h1>
@stop

@section('custom-css')
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css"/>
<style type="text/css">
    /*.header {
      width: 500px;
      background: aqua;
      margin: 0 auto;
      padding: 10px;
      display: table;
    }*/
    .avatar img {
        width: 60px;
    }
    .avatar,
    .info {
        display: table-cell;
        vertical-align: middle;
        padding-left:0.5cm;
    }
    .hoverview{
        min-height: 25px;
    }
    table { table-layout:fixed; word-break:break-all; word-wrap:break-word; }
</style>
@stop

@section('content')

    <div id="app-agent">
        <comments-list></comments-list>
    </div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
