@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.create_ticket-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.create_ticket-page-description') !!}">
@stop


@section('Tickets')
active
@stop

@section('newticket')
class="active"
@stop

@section('custom-css')

<style>
   .btn-bs-file{
    position:relative;
}
.btn-bs-file input[type="file"]{
    position: absolute;
    filter: alpha(opacity=0);
    opacity: 0;
    width:0;
    height:0;
    outline: none;
    cursor: inherit;
}
.danis:hover{
    background-color: #5897fb;
}
.danis:hover>div>a{
    color:white !important;

}
</style>
@stop
@section('Tickets')
class="active"
@stop

@section('ticket-bar')
active
@stop

@section('newticket')
class="active"
@stop

@section('PageHeader')
<h1 id="header">{{Lang::get('lang.tickets')}}</h1>
@stop



@section('content')

@include('themes.default1.common.recaptcha')

<div id="app-agent">
    <faveo-form category="ticket" panel="agent"></faveo-form>
</div>

<script src="{{asset('js/agent.js')}}" type="text/javascript"></script>

@stop
@push('scripts')
<script src="{{assetLink('js','ckeditor')}}"></script>
<script src="{{assetLink('js','intlTelInput')}}" async></script>
@endpush