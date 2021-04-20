@extends('themes.default1.agent.layout.agent')
@extends('themes.default1.agent.layout.sidebar')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('PageHeader')
<h1>{{Lang::get('lang.kb-settings')}}</h1>
@stop
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.kb-settings-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.kb-settings-page-description') !!}">

@stop
@section('settings')
class="active"
@stop
<style type="text/css">
.spinner:before {
  margin-top: 3px !important;
}


</style>


@section('content')
<div id="app-agent">
    <kb-settings></kb-settings>
</div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
