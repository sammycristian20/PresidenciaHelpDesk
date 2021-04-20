@extends('themes.default1.agent.layout.agent')
@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css"/>

    <style>
        .bootstrap-datetimepicker-widget{
            display: block !important;
        }
        .nav-tabs > li.active{
            border-top:2px solid #3c8dbc;
        }
        .nav-tabs > li > a{
            margin-right: 0px !important;
        }
        .lead span {
            margin-left: 10px;
        }
        .info-box-icon{
            padding-top: 0px !important;
        }
        @media screen and (min-width: 1200px) {
            .fifer{
                width: 20% !important;
            }

        }
    </style>
    <link href="{{assetLink('css','select2')}}" media="none" onload="this.media='all';" rel="stylesheet" type="text/css"/>

@stop
@section('Dashboard')
    class="active"
@stop

@section('dashboard-bar')
    active
@stop

@section('PageHeader')
    <h1>
        {!! Lang::get('lang.dashboard') !!}
    </h1>
@stop

@section('dashboard')
    class="active"
@stop

@section('content')

    <div id="app-agent">
        <dashboard-page :user-roles="{{ json_encode($roles) }}" :top-widget-count="{{ json_encode($topWidgetCount) }}"></dashboard-page>
    </div>
    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop
