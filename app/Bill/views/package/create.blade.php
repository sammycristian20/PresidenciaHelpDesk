@extends('themes.default1.admin.layout.admin')

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

<link href="{{assetLink('css','tw-currency-select')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
<link href="{{assetLink('css','bootstrap-select')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
<style>
    .currency-select .currency-select__flag-currency-code .currency-select__currency-code{
             font-size: 14px !important;
        }
    .bootstrap-select>.dropdown-toggle{
             background-color: #FFF;
             border-color: #A8A8A8;
        }
    .bootstrap-select.btn-group .dropdown-toggle .caret{
             margin-top: 0px;
        }
</style>
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! trans('Bill::lang.create-package') !!}</h1>
@stop
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- /breadcrumbs -->
@section('content')
    <div id="app-billing">

        <package></package>
    </div>
    
    <script type="text/javascript" src="{{ bundleLink('js/faveoBilling.js') }}"></script>
@stop


@section('FooterInclude')

@stop