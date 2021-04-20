@extends('themes.default1.agent.layout.agent')

@section('Staffs')
active
@stop

@section('staffs-bar')
active
@stop

@section('Report')
class="active"
@stop


@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!!Lang::get('lang.helpdesk_reports')!!}</h1>
@stop
@section('content')

<div id="faveo-report">
    <report-home-page></report-home-page>
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>

<?php \Event::dispatch('report',[]);  ?>
@stop