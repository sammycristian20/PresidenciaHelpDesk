@extends('themes.default1.agent.layout.agent')

@section('Tickets')
class="active"
@stop

@section('custom-css')
@stop

@section('ticket-bar')
active
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.ticket-details')}}</h1>
@stop

@section('content')

@include('themes.default1.common.recaptcha')

<div id="app-agent">

    <inbox-timeline :ticket-id="{!! $ticketId !!}" :from="'timeline'"></ticket-timeline>
</div>
<script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop
