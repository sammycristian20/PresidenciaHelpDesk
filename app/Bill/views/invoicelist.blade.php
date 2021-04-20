@extends('themes.default1.agent.layout.agent')

@section('bill')
active
@stop

@section('tickets-bar')
active
@stop

@section('bill')
class="active"
@stop

@section('HeadInclude')

@stop
<!-- header -->
@section('PageHeader')
<h1>{!! trans('Bill::lang.invoices') !!}</h1>
@stop
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- /breadcrumbs -->
@section('content')
    <div id="app-billing">
        <invoices></invoices>
    </div>
    
    
    <script type="text/javascript" src="{{ bundleLink('js/faveoBilling.js') }}"></script>
@stop

@section('FooterInclude')

@stop