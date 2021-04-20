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

@section('content')

<section class="content-header">
    <h1>{!! Lang::get('lang.report') !!}</h1>

</section>
<link rel="stylesheet" type="text/css" href="{{assetLink('css','jquery.daterangepicker')}}" />
<div class="box box-primary">
    <div class="box-header">
        <div class="btn-group">
            <input type="text" name="daterange" class="form-control" value="{{Carbon\Carbon::now()->subMonth()->format('m/d/Y')}}-{{Carbon\Carbon::now()->format('m/d/Y')}}" />
        </div>

        <div class="btn-group pull-right">
            @include('report::popup.export')
            <button class="btn btn-default period" value="today">{!! Lang::get('lang.today') !!}</button>
            <button class="btn btn-default period" value="week">{!! Lang::get('lang.last-7-day') !!}</button>
            <button class="btn btn-default period" value="month">{!! Lang::get('lang.last-30-days') !!}</button>
            <button class="btn btn-default period" value="year">{!! Lang::get('lang.last-1-year') !!}</button>
            @include('report::popup.mail')
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-8">

                <div>
                    <div id="loading-all">
                        <img src="{{assetLink('image','gifloader')}}">
                    </div>
                    <canvas id="all"></canvas>
                </div>
            </div>
            <div class="col-md-3">
                <ul class="list-unstyled">
                    <li>

                        <div>
                            <div id="loading-source">
                                <img src="{{assetLink('image','gifloader')}}">
                            </div>
                            <canvas id="source"></canvas>
                        </div>

                    </li>
                    <li>

                        <div>
                            <div id="loading-priority">
                                <img src="{{assetLink('image','gifloader')}}">
                            </div>
                            <canvas id="priority"></canvas>
                        </div>

                    </li>
                    <li>

                        <div>
                            <div id="loading-sla">
                                <img src="{{assetLink('image','gifloader')}}">
                            </div>
                            <canvas id="sla"></canvas>
                        </div>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop
@section('FooterInclude')
<script src="{{assetLink('js','jquery-ui')}}" type="text/javascript"></script>
<script src="{{assetLink('js','chart')}}"></script>
@include('report::asset.all-js')
<script type="text/javascript" src="{{assetLink('js','moment')}}"></script>
<script type="text/javascript" src="{{assetLink('js','daterangepicker')}}"></script>
<script type="text/javascript">
$(function () {
    $('input[name="interval"]').daterangepicker();
    $('input[name="daterange"]').daterangepicker();
    $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
        var start = picker.startDate.format('YYYY-MM-DD');
        var end = picker.endDate.format('YYYY-MM-DD');
        var period = "custom";
        var chart = "doughnut";
        all(period, 'line', start, end);
        source(chart, period, start, end);
        priority(chart, period, start, end);
        sla(chart, period, start, end);
    });
    $('input[name="date"]').daterangepicker();
    $('input[name="date"]').on('apply.daterangepicker', function (ev, picker) {
        var start = picker.startDate.format('YYYY-MM-DD');
        var end = picker.endDate.format('YYYY-MM-DD');
        var data = $("#export-form").serialize();
        $.ajax({
            cache: false,
            responseType: 'ArrayBuffer', //not sure if needed
            type: "post",
            //dataType: "html",
            url: "{{url('report/export')}}",
            data: data,
            beforeSend: function () {
                $('.loader1').css('display','block');
                $("#loading-form").show();
            },
            complete: function () {
                $('.loader1').css('display','none');
                $("#loading-form").hide();
            },
            success: function (data, textStatus, request) {
                if (data==='success'){
                    window.location = "{{url('report/download')}}";
                }
            },
            error: function (ajaxContext) {
                toastr.error('Export error: ' + ajaxContext.responseText);
            }

        });
    });
});
</script>
<script src="{{assetLink('js','tagit')}}"></script>
<script>
$('#to').tagit({
    tagSource: "{{url('report/mail/to')}}",
    allowNewTags: true,
    placeholder: 'Enter email address',
    select: true,
});

</script>
@stop

