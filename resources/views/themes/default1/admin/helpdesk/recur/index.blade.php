<?php
    $relativeUrl = str_replace(\URL::to('/'), '', \Request::url());
    $panel = (strpos($relativeUrl, 'agent') !== false) ? ['themes.default1.agent.layout.agent', 'agent/'] : ['themes.default1.admin.layout.admin', ''];
?>
@extends($panel[0])

@section('Tickets')
active
@stop

@section('manage-bar')
active
@stop

@section('recur')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{trans('lang.recurring')}}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('custom-css')
<style type="text/css">

    .avatar img {
        width: 60px;
    }
    .avatar,
    .info {
        display: table-cell;
        vertical-align: middle;
        padding-left:0.5cm;
    }
    table { table-layout:fixed; word-break:break-all; word-wrap:break-word; }
</style>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
    <div id="app-agent">
        <recurring-tickets-index></recurring-tickets-index>
    </div>
    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop