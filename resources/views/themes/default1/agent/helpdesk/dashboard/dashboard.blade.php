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
        {!! Lang::get('lang.dashboard_reports') !!}
    </h1>
@stop

@section('dashboard')
    class="active"
@stop

@section('content')
    <div class="alert alert-warning">
        {{-- NOTE: we are removing old dashboard in upcoming releases so we are not adding it to language file    --}}
        <i class="fa fa-exclamation-triangle">
        </i>
        {!! trans("lang.old_dashboard_warning_message", ["mail"=> '<a href="mailto:support@ladybirdweb.com?Subject=Suggestions%20regarding%20faveo%20dashboard">support@ladybirdweb.com</a>']) !!}
    </div>
    <!-- check whether success or not -->
    {{-- Success message --}}
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa fa-check-circle">
            </i>
            <button aria-hidden="true" class="close" data-dismiss="alert" type="button">
                ×
            </button>
            {{Session::get('success')}}
        </div>
    @endif
    {{-- failure message --}}
    @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban">
            </i>
            <b>
                {!! Lang::get('lang.alert') !!}!
            </b>
            <button aria-hidden="true" class="close" data-dismiss="alert" type="button">
                ×
            </button>
            {{Session::get('fails')}}
        </div>
    @endif
    <link href="{{assetLink('css','bootstrap-datetimepicker4')}}" media="none" onload="this.media='all';" rel="stylesheet" type="text/css">
    <div class="row">
        <div class="col-md-2 fifer">
            <a href="{!! url('/tickets?show%5B%5D=inbox') !!}">
                <div class="info-box center">
                    <span class="info-box-icon bg-aqua">
                        <i class="fa fa-envelope-o">
                        </i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            {!! Lang::get('lang.inbox') !!}
                        </span>
                        <span class="info-box-number">
                            <?php echo $tickets->
                            count() ?>
                            <small>
                                {!! Lang::get('lang.tickets') !!}
                            </small>
                        </span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 fifer">
            <?php
            $version = (\Cache::has('inbox-layout')) ? \Cache::get('inbox-layout') : 'new';
            $url = ($version == 'new') ? url("/tickets?show%5B%5D=unassigned"): url("/tickets?assigned[]=0");
            ?>
            <a href="{!! $url !!}">
                <div class="info-box">
                    <span class="info-box-icon bg-orange">
                        <i class="fa fa-user-times">
                        </i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            {!! Lang::get('lang.unassigned') !!}
                        </span>
                        <span class="info-box-number">
                            {{$unassigned->count() }}
                            <small>
                                {!! Lang::get('lang.tickets') !!}
                            </small>
                        </span>
                    </div>
                </div>
            </a>
        </div>
        <div class="clearfix visible-sm-block">
        </div>
        <div class="col-md-2 fifer">
            <a href="{!! url('/tickets?show%5B%5D=overdue') !!}">
                <div class="info-box">
                    <span class="info-box-icon bg-red">
                        <i class="fa fa-calendar-times-o">
                        </i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            {!! Lang::get('lang.overdue') !!}
                        </span>
                        <span class="info-box-number">
                            {{ $overdues->count() }}
                            <small>
                                {!! Lang::get('lang.tickets') !!}
                            </small>
                        </span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 fifer">
            <a href="{!! url('/tickets?show%5B%5D=mytickets') !!}">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow">
                        <i class="fa fa-user">
                        </i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            {!! Lang::get('lang.my_tickets') !!}
                        </span>
                        <span class="info-box-number">
                            {{$myticket->count() }}
                            <small>
                                {!! Lang::get('lang.tickets') !!}
                            </small>
                        </span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 fifer">
            <a href="{!! url('/tickets?show%5B%5D=due_today') !!}">
                <div class="info-box">
                    <span class="info-box-icon bg-red">
                        <i class="glyphicon glyphicon-eye-open">
                        </i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            {!! Lang::get('lang.duetoday') !!}
                        </span>
                        <span class="info-box-number">
                            {{ $due_today->count() }}
                            <small>
                                {!! Lang::get('lang.tickets') !!}
                            </small>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                {!! Lang::get('lang.report') !!}
            </h3>
        </div>
        <div class="box-body">
            <form id="filter">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-2">
                            {!! Form::label('date',Lang::get('lang.start_date'),['class' => 'lead']) !!}
                            {!! Form::text('start_date',null,['class'=>'form-control','id'=>'datepicker4', 'placeholder' => 'yyyy/mm/dd'])!!}
                        </div>
                        <?php
                        $start_date = App\Model\helpdesk\Ticket\Tickets::where('id', '=', '1')->
                        first();
                        if ($start_date != null) {
                            $created_date = $start_date->created_at;
                            $created_date = explode(' ', $created_date);
                            $created_date = $created_date[0];
                            $start_date = date("m/d/Y", strtotime($created_date . ' -1 months'));
                        } else {
                            $start_date = date("m/d/Y", strtotime(date("m/d/Y") . ' -1 months'));
                        }
                        ?>
                        <div class="col-sm-2">
                            {!! Form::label('start_time', Lang::get('lang.end_date') ,['class' => 'lead']) !!}
                            {!! Form::text('end_date',null,['class'=>'form-control','id'=>'datetimepicker3', 'placeholder' => 'yyyy/mm/dd'])!!}
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#datepicker4, #datetimepicker3').on('focus', function(){
                                    $('.col-sm-2').removeClass('has-error');
                                });
                                $('#datepicker4, #datetimepicker3').keypress(function (e) {
                                    var regex = new RegExp("^[0-9-/]+$");
                                    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                                    if (regex.test(str)) {
                                        return true;
                                    }

                                    e.preventDefault();
                                    return false;
                                });

                                var timestring1 = "{!! $start_date !!}";
                                var timestring2 = "{!! date('Y-m-d') !!}";
                                $('#datepicker4').datetimepicker({
                                    format: 'YYYY/MM/DD',
                                    maxDate: moment(timestring2).startOf('day'),

                                });
                                $('#datetimepicker3').datetimepicker({
                                    format: 'YYYY/MM/DD',
                                    useCurrent: false, //Important! See issue #1075
                                    maxDate: moment(timestring2).startOf('day')
                                });

                                $("#datepicker4").on("dp.change", function (e) {
                                    $('#datetimepicker3').data("DateTimePicker").minDate(e.date).maxDate(moment(timestring2).startOf('day'));
                                });
                                $("#datetimepicker3").on("dp.change", function (e) {
                                    $('#datepicker4').data("DateTimePicker").maxDate(e.date);
                                });
                            });
                        </script>
                        <div class="col-sm-1" style="margin-top:30px">
                            {!! Form::label('filter', Lang::get('lang.filter'),['class' => 'lead hide']) !!}
                            <br>
                        {!! Form::submit(Lang::get('lang.submit'),['class'=>'form-group btn btn-primary','id'=>'filter-form'])!!}
                        <!-- <button class="btn btn-primary" id="filter-form">Lang::get('lang.submit')</button> -->
                            </br>
                        </div>
                    </div>
                    <div class="row">
                        <style>
                            #legend-holder { border: 1px solid #ccc; float: left; width: 25px; height: 25px; margin: 1px; }
                        </style>
                        <div class="col-md-3">
                            <span id="legend-holder" style="background-color: #6C96DF;">
                            </span>
                            <span class="lead">
                                <span id="total-created-tickets">
                                </span>
                                {!! Lang::get('lang.tickets') !!} {!! Lang::get('lang.created') !!}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <span id="legend-holder" style="background-color: #6DC5B2;">
                            </span>
                            <span class="lead">
                                <span class="lead" id="total-reopen-tickets">
                                </span>
                                {!! Lang::get('lang.tickets') !!} {!! Lang::get('lang.reopen') !!}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <span id="legend-holder" style="background-color: #E3B870;">
                            </span>
                            <span class="lead">
                                <span class="lead" id="total-closed-tickets">
                                </span>
                                {!! Lang::get('lang.tickets') !!} {!! Lang::get('lang.closed') !!}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <span id="legend-holder" style="background-color: #A3B952;">
                            </span>
                            <span class="lead">
                                <span class="lead" id="total-due-tickets">
                                </span>
                                {!! Lang::get('lang.tickets') !!} {!! Lang::get('lang.overdue') !!}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row" id="no-data-msg" style="display: none">
                    <center>
                        <h4>
                            <span style="color: red">
                                {{Lang::get('lang.invalid-date-range')}}
                            </span>
                        </h4>
                    </center>
                </div>
            </form>
            <div class="chart">
                <canvas class="chart-data" height="250" id="tickets-graph" width="1000">
                </canvas>
            </div>
            <div class="overlay" id="show-report-loader">
                <i class="fa ion ion-load-d fa-spin">
                </i>
                <center>
                    <p>
                        {{Lang::get('lang.please-wait-while-we-are-crunching-your-data')}}
                    </p>
                </center>
            </div>
        </div>
    </div>
    <div class="box box-info" ng-controller="dashboardFilterCtrl">
        <div class="box-header with-border ">
            <h1 class="box-title">
                {!! Lang::get('lang.statistics') !!}
            </h1>
            <div class="pull-right dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void()" ng-click="getCustomFields($event)">
                    <i class="fa fa-plus">
                        {!! Lang::get('lang.add_custom_field') !!}
                    </i>
                </a>
                <ul class="dropdown-menu" style="background: #fff;border: 1px solid rgba(100, 100, 100, .4);border-radius: 0 0 2px 2px;box-shadow: 0 3px 8px rgba(0, 0, 0, .25);color: #1d2129;overflow: visible;position: absolute !important;width: 250% !important;padding: 0px">
                    <li class="header">
                        <div class="cool" style="padding: 8px 10px;line-height: 16px;background: #f6f8fa;border-bottom: 1px solid #e1e4e8;">
                            <b>
                                {!! Lang::get('lang.filter_custom_field') !!}
                            </b>
                        </div>
                    </li>
                    <li class="search">
                        <div style="padding: 10px 10px 0;background-color: #f6f8fa;padding-bottom: 2px;">
                            <input class="form-control" ng-keydown="getfieldName(filedValue,$event)" ng-model="filedValue" placeholder="{!! Lang::get('lang.filter_here') !!}" type="text"/>
                        </div>
                    </li>
                    <li>
                        <div custom-field-scrolled="loadField()" style="height: 110px;overflow-y: auto;width:100%">
                            <a class="fileds" href="javascript:void(0)" ng-click="showFields(field)" ng-repeat="field in customFields.data" role="menuitem" style="display: block;padding: 8px 8px 8px 20px;overflow: hidden;color: grey;cursor: pointer;border-bottom: 1px solid #eaecef;position: relative;" tabindex="-1">
                                <span ng-if="field.selected=='1'">
                                    <svg aria-hidden="true" class="octicon octicon-check select-menu-item-icon" height="16" version="1.1" viewbox="0 0 12 16" width="12">
                                        <path d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z" fill-rule="evenodd" style="fill:cornflowerblue">
                                        </path>
                                    </svg>
                                </span>&nbsp&nbsp
                                <span style="position: absolute;margin-top: -1px">
                                    @{{field.label}}
                                </span>
                            </a>
                            <div ng-hide="showloader" style="padding :35px;">
                                <div class="spinner1">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="search">
                        <a href="javascript:void(0)" ng-click="loadField()">
                            Show more fileds
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="box-body" id="department-statics">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#tab_1" id="departments" onclick="pushData('departments')">
                        {!! Lang::get('lang.departments') !!}
                    </a>
                </li>
                <li>
                    <a data-toggle="agents" href="#tab_2" id="agents" onclick="pushData('agents')">
                        {!! Lang::get('lang.agents') !!}
                    </a>
                </li>
                <li>
                    <a data-toggle="teams" href="#tab_3" id="teams" onclick="pushData('teams')">
                        {!! Lang::get('lang.teams') !!}
                    </a>
                </li>
                @for ($i = 3; $i < count($dashboard_statistics); $i++)
                    <li>
                        <a data-toggle="{!! $dashboard_statistics [$i]!!}" href="#tab_{!! $i !!}" id="{!! $dashboard_statistics [$i]!!}" onclick="pushData('{!! $dashboard_statistics [$i]!!}')">
                            {!! Lang::get('lang.'.$dashboard_statistics [$i]   ) !!}
                        </a>
                    </li>
                @endfor
                <li class="customFilter" style="display: none">
                    <a href="#tab_11" id="type" ng-click="pushData(customFilterValue.field)">
                        @{{customFilterValue.label}}
                    </a>
                </li>
            </ul>
            <br/>
            <div id="table-display">
                <table class="table table-hover table-bordered" id="myTable">
                </table>
            </div>
            <center>
                <div id="no-data-table" style="display: none">
                    {{Lang::get('lang.no-data-to-show')}}
                </div>
            </center>
        </div>
        <div class="overlay" id="show-static-loader">
            <i class="fa ion ion-load-d fa-spin">
            </i>
            <br/>
            <center>
                <p>
                    {{Lang::get('lang.please-wait-while-we-are-crunching-your-data')}}
                </p>
            </center>
        </div>
    </div>
@stop
@section('FooterInclude')
    <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
    <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

    <script async="" src="{{assetLink('js','chart')}}"></script>
    <script src="{{assetLink('js','bootstrap-datetimepicker4')}}" type="text/javascript"></script>

    <script>
        var options_line = {
            maintainAspectRatio: true,
            responsive: true,
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    stacked: false,
                    scaleLabel: {
                        display: true,
                        labelString: 'Date',
                        fontStyle:'bold',
                    },
                    gridLines: {
                        display:false
                    },
                }],
                yAxes: [{
                    stacked: false,
                    barThickness: 10,
                    scaleLabel: {
                        display: true,
                        labelString: 'Ticket Count',
                        fontStyle:'bold',
                    },
                    gridLines: {
                        display:false
                    },
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        suggestedMin: 0,
                        callback: function(value, index, values) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    },
                }],
            },
        };

        var tcolumn = 'departments';
        var tstart  = '';
        var tend    = '';
        var tableObject = null;
        var dashboardChart = false;
        thisAjax();

        tableAjax(setTableDataOption(tcolumn, tstart, tend));

        function thisAjax(data) {

            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{url('agen')}}",
                data:data,
                beforeSend: function() {
                    $('.loader1').css('display','block');
                    $("#show-report-loader").css('display', 'block');
                },
                success: function (data) {
                    $('.loader1').css('display','none');
                    $("#show-report-loader").css('display', 'none');
                    var ctx = $("#tickets-graph").get(0).getContext("2d");
                    if (dashboardChart) {
                        dashboardChart.data.labels = data.data.labels;
                        dashboardChart.data.datasets = data.data.datasets;
                        dashboardChart.update();
                    } else {
                        dashboardChart = new Chart(ctx, {
                            type: 'line',
                            data: data.data,
                            options: options_line
                        });
                    }
                    var open = data.count.open;
                    var closed = data.count.closed;
                    var reopen = data.count.reopened;
                    var due = data.count.due;
                    $("#total-created-tickets").html(open);
                    $("#total-closed-tickets").html(closed);
                    $("#total-reopen-tickets").html(reopen);
                    $("#total-due-tickets").html(due);
                },
                error: function (error) {
                    $("#show-report-loader").css('display', 'none');
                    $('#no-data-msg').css('display', 'block');
                    setTimeout(function(){
                        $('#no-data-msg').css('display', 'none');
                    }, 5000);
                }
            });
        }

        $("#filter-form").on('click',function(event){
            if ($('#datetimepicker3').val() == '' || $('#datepicker4').val() == '') {
                $('.col-sm-2').addClass('has-error');
                $('#no-data-msg').css('display', 'block');
                setTimeout(function(){
                    $('#no-data-msg').css('display', 'none');
                }, 5000);
                alert('{{Lang::get("lang.please-select-a-valid-date-range")}}');
            } else {
                event.preventDefault();
                var data = $("#filter").serialize();
                thisAjax(data);
                var data2 = $("#filter").serializeArray();
                tstart = data2[0].value;
                tend = data2[1].value;
                $("#myTable").dataTable().fnDestroy();
                tableAjax(setTableDataOption(tcolumn, tstart, tend));
            }
        });
        $('.dropdown-menu>.search').click(function(event){
            event.stopPropagation();
        });
        function tableAjax(data) {

            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{url('dashboard/getDepartment')}}",
                data:data,
                beforeSend: function() {
                    $('.loader1').css('display','block');
                    $('#show-static-loader').css('display', 'block');
                },
                success: function (json) {
                    $('.loader1').css('display','none');
                    var table = "";
                    var thead = "<thead><tr><th>{{Lang::get('lang.name')}}</th>";
                    var thead_array = [];
                    var prev_name = '';
                    var trow = '';
                    if (json.length > 0) {
                        for (var i = 0; i < json.length; i++) {
                            if (prev_name == '') {
                                trow = '<tr><td>'+json[i].name+'</td><td>'+json[i].count+'</td></tr>';
                                thead += '<th>'+json[i].status+'</th>';
                                prev_name = json[i].name;
                                thead_array.push(json[i].status);
                            } else if (prev_name != '' && prev_name != json[i].name) {
                                table = table+trow;
                                trow = '';
                                prev_name = json[i].name;
                                if(!isAlreeadyInThead(thead, json[i].status)) {
                                    thead += '<th>'+json[i].status+'</th>';
                                    table = table.replaceAll_1('</tr>', '<td>0</td></tr>');
                                    trow = '<tr><td>'+json[i].name+'</td>'+addEmptyCells(thead_array.length)+'<td>'+json[i].count+'</td></tr>';
                                    thead_array.push(json[i].status);
                                } else {
                                    trow = '<tr><td>'+json[i].name+'</td>'+addEmptyCells(thead_array.indexOf(json[i].status))+'<td>'+json[i].count+'</td>'+addEmptyCells(thead_array.length - (thead_array.indexOf(json[i].status)+1))+'</tr>';
                                }
                            } else if (prev_name != '' && prev_name == json[i].name) {
                                if(!isAlreeadyInThead(thead, json[i].status)) {
                                    thead += '<th>'+json[i].status+'</th>';
                                    thead_array.push(json[i].status);
                                    table = table.replaceAll_1('</tr>', '<td>0</td></tr>');
                                    trow = trow.replace('</tr>', '<td>'+json[i].count+'</td></tr>')
                                } else {
                                    trow = editRow(trow, thead_array.length, thead_array.indexOf(json[i].status), json[i].count);
                                }
                            }
                        }
                        table = table+trow;
                        table = thead+'</thead><tbody>'+table+'</tbody>';
                        $('#myTable').html(table);
                        formatTable();
                        $('#table-display').css('display', 'block');
                        $('#no-data-table').css('display', 'none');
                        $('#show-static-loader').css('display', 'none');
                    } else {
                        $('#myTable').html('<thead><tr><th>test</th></tr></thead><tbody></tbody>');
                        formatTable();
                        $('#show-static-loader').css('display', 'none');
                        $('#table-display').css('display', 'none');
                        $('#no-data-table').css('display', 'block');
                    }
                }
            });
        }

        function isAlreeadyInThead(thead, value) {
            if (thead.search(value) == -1)  {
                return false;
            }
            return true;
        }

        function formatTable() {
            tableObject = jQuery('#myTable').dataTable({});
        }

        function addEmptyCells(times) {
            var str = '';
            for (var i =0; i < times; i++) {
                str += '<td>0</td>';
            }
            return str;
        }

        function editRow(str, length, index, value){
            res = str.split("</td>");
            res[index+1]="<td>"+value;
            str = res.join("</td>");
            return str;
        }

        String.prototype.replaceAll_1 = function(search, replacement) {
            var target = this;
            return target.replace(new RegExp(search, 'g'), replacement);
        };

        function pushData(id) {
            dates = getSetDates();
            if (tableObject != null) {
                $("#myTable").dataTable().fnDestroy();
            }
            // $('.customFilter').css('display', 'none');
            $('.active').removeClass('active');
            $('#'+id).parent('li').addClass('active');
            tableAjax(setTableDataOption(id, dates[0], dates[1]));
        }

        function setTableDataOption(column, start_date, end_date) {
            tableData = {'column': column, 'start_date': start_date, 'end_date': end_date};
            tcolumn = column;
            tstart = start_date;
            tend = end_date;
            return tableData;
        }

        function getSetDates() {
            return [tstart, tend];
        }


        app.controller('dashboardFilterCtrl',function($scope,$http,Upload,$compile){


            $scope.getCustomFields=function(event){
                $http.get("{!! url('custom-filter-fields') !!}").success(function(data){
                    $scope.customFields=data;
                    $scope.showloader=true;
                    $(event.currentTarget).attr('ng-click',null).unbind('click');
                });
            }
            $scope.getfieldName=function(x,e){
                $scope.showloader=false;
                $scope.customFields=[];
                if (e.keyCode == 8 || e.keyCode == 46){
                    setTimeout(function(){
                        if(x.length=="1"){
                            $http.get("{!! url('custom-filter-fields') !!}").success(function(data){
                                $scope.customFields=data;
                                $scope.showloader=true;
                                $(event.currentTarget).attr('ng-click',null).unbind('click');
                            });
                        }
                    },100);
                }
                else{
                    setTimeout(function(){
                        $http.get("{!! url('custom-filter-fields') !!}?term="+x).success(function (data) {
                            if(data.data.length!=0){
                                $scope.customFields=data;
                                $scope.showloader=true;
                            }
                            else{
                                $scope.customFields={"data":[{
                                        "label":"{!! Lang::get('lang.no_results_found') !!}"
                                    }]
                                };
                                $scope.showloader=true;
                            }
                        })
                    },100);
                }
            };
            $scope.customFilterValue={};
            $scope.showFields=function(x){
                if(x.label!="{!! Lang::get('lang.no_results_found') !!}"){
                    if(x.selected=='0'){
                        for(var k in $scope.customFields.data){
                            $scope.customFields.data[k].selected=0;
                        }
                        x.selected=1;
                        $scope.customFilterValue=x;
                        $('.customFilter').css('display','block');
                        $('.nav-tabs').find('li').removeClass('active');
                        pushData(x.field);
                    }

                }
            };
            $scope.pushData=function(id) {
                dates = getSetDates();
                if (tableObject != null) {
                    $("#myTable").dataTable().fnDestroy();
                }
                $('.nav-tabs').find('li').removeClass('active');
                $('.customFilter').addClass('active');
                tableAjax(setTableDataOption(id, dates[0], dates[1]));
            }
            function pushData(id) {
                dates = getSetDates();
                if (tableObject != null) {
                    $("#myTable").dataTable().fnDestroy();
                }
                $('.customFilter').addClass('active');
                tableAjax(setTableDataOption(id, dates[0], dates[1]));
            }
            $scope.loadField=function(){
                //$scope.showloader=false;
                if($scope.customFields.next_page_url==null){
                    //$scope.showloader=true;
                }
                else{
                    $http.get($scope.customFields.next_page_url).success(function(data){
                        [].push.apply($scope.customFields.data, data.data);
                        $scope.customFields.next_page_url=data.next_page_url;
                    })
                }

            }

        })
    </script>
@stop
