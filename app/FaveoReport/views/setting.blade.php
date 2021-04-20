@extends('themes.default1.agent.layout.agent')

@section('Settings')
active
@endsection

@section('tickets-bar')
active
@endsection

@section('report')
class="active"
@endsection

@section('HeadInclude')
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
@endsection
<!-- header -->
@section('PageHeader')
<h1>
    {!! trans('report::lang.report-settings') !!}
</h1>
@endsection
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@endsection
<!-- /breadcrumbs -->

@section('content')
<div id="faveo-report">
    <report-settings></report-settings>
</div>
<script src="{{bundleLink('js/faveoReport.js')}}" type="text/javascript"></script>
@stop
