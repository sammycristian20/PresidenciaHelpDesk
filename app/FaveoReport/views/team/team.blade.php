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

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.teams') !!} {!! Lang::get('lang.report') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('content')
<link rel="stylesheet" type="text/css" href="{{assetLink('css','daterangepicker')}}" media="none" onload="this.media='all';"/>
<div class="box box-primary">
    <div class="box-header">
        <div class="btn-group">
            <input type="text" name="daterange" class="form-control" value="{{Carbon\Carbon::now()->subMonth()->format('m/d/Y')}}-{{Carbon\Carbon::now()->format('m/d/Y')}}" />
        </div>
        <div class="btn-group">
            {!! Form::select('team',$teams,null,['class'=>'form-control','id'=>'team']) !!}
        </div>
        <div class="btn-group pull-right">
            @include('report::team.popup')
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
                            <canvas id="team-source"></canvas>
                        </div>

                    </li>
                    <li>

                        <div>
                            <div id="loading-priority">
                                <img src="{{assetLink('image','gifloader')}}">
                            </div>
                            <canvas id="team-priority"></canvas>
                        </div>

                    </li>
                    <li>

                        <div>
                            <div id="loading-sla">
                                <img src="{{assetLink('image','gifloader')}}">
                            </div>
                            <canvas id="team-sla"></canvas>
                        </div>

                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
<div class="box box-primary">
    <div class="box-header">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">

                    <div>

                        <div>
                            <table class="table table-bordered" cellspacing="0" width="100%" id="chumper">
                                <thead>
                                    <tr>
                                        <th>{!! Lang::get('lang.teams') !!}</th>
                                        @forelse($table as $head)
                                        <th>{!!$head!!}</th>
                                        @empty 

                                        @endforelse

                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('FooterInclude')
<script src="{{assetLink('js','jquery-ui')}}" type="text/javascript"></script>

@include('report::asset.team-datatable')
<script src="{{assetLink('js','chart')}}"></script>
@include('report::asset.team-js')
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
        var team = $("#team").val();
        all(period, 'line',team, start, end);
        source(chart, period,team, start, end);
        priority(chart, period,team, start, end);
        sla(chart, period,team, start, end);
    });
    $('input[name="date"]').daterangepicker();
    $('input[name="date"]').on('apply.daterangepicker', function (ev, picker) {
        var start = picker.startDate.format('YYYY-MM-DD');
        var end = picker.endDate.format('YYYY-MM-DD');
        $.ajax({
            cache: false,
            responseType: 'ArrayBuffer', //not sure if needed
            type: "post",
            //dataType: "html",
            url: "{{url('report/team/export')}}",
            data: {'start':start,'end':end},
            beforeSend: function () {
                $('.loader1').css('display','block');
                $("#loading-form").show();
            },
            complete: function () {
                $('.loader1').css('display','none');
                $("#loading-form").hide();
            },
            success: function (data, textStatus, request) {

                //you could need to decode json here, my app do it automaticly, use a try catch cause csv are not jsoned

                //already json decoded? custom return from controller so format is xls
                if (jQuery.isPlainObject(data)) {
                    data = data.data; //because my return data have a 'data' parameter with the content
                }

                //V1 - http://stackoverflow.com/questions/35378081/laravel-excel-using-with-ajax-is-not-working-properly
                //+V3 - http://stackoverflow.com/questions/27701981/phpexcel-download-using-ajax-call
                var filename = "";
                var disposition = request.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1])
                        filename = matches[1].replace(/['"]/g, '');
                }
                if (!jQuery.isPlainObject(data)) { //is CSV - we use blob
                    var type = request.getResponseHeader('Content-Type');
                    var blob = new Blob([data], {type: type, endings: 'native'});
                    var URL = window.URL || window.webkitURL;
                    var downloadUrl = URL.createObjectURL(blob);
                }
                var a = document.createElement("a");
                a.href = jQuery.isPlainObject(data) ? data.file : downloadUrl;
                a.download = jQuery.isPlainObject(data) ? data.filename : filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
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

