@extends('themes.default1.admin.layout.admin')

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<link type="text/css" href="{{assetLink('css','bootstrap-datetimepicker4')}}" rel="stylesheet">
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
<!-- iCheck -->
<link href="{{assetLink('css','blue')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>

<style>
    .tooltip1 {
        position: relative;
        /*display: inline-block;*/
        /*border-bottom: 1px dotted black;*/
    }

    .tooltip1 .tooltiptext {
        visibility: hidden;
        width: 100%;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }

    .tooltip1:hover .tooltiptext {
        visibility: visible;
    }
</style>
<h1>{!! Lang::get('lang.team_profile') !!} </h1>

@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')

@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<!-- success message -->
<div id="alert-success" class="alert alert-success alert-dismissable" style="display:none;">
    <i class="fa fa-check-circle"> </i> <span id="get-success"></span>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
</div>
<!-- INfo message -->
<div id="alert-danger" class="alert alert-danger alert-dismissable" style="display:none;">
    <i class="fa fa-check-circle"> </i> <span id="get-danger"></span>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
</div>
@if(Session::has('success1'))
<div id="success-alert" class="alert alert-success alert-dismissable">
    <i class="fa  fa-check-circle"> </i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('success1')}}
</div>
@endif
<!-- failure message -->
@if(Session::has('fails1'))
<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!} ! </b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('fails1')}}
</div>
@endif
<div class="row">
    <div class="col-md-3">
        <div class="card card-light card-outline" >
            
            <div class="card-body ox-profile">

                <div class="text-center">
                    <img src="{{assetLink('image','team')}} " class="profile-user-img img-fluid img-circle" alt="User Image">  
                </div>

                <h3 class="profile-username text-center" title="{{$teams->name}}" data-toggle="tooltip">
                    {{str_limit($teams->name,20)}}</h3>

                <ul class="list-group list-group-unbordered">

                    <li class="list-group-item">
                             
                        <label>{{Lang::get('lang.teamlead_name')}}</label> 

                        <a class="float-right" title="{{$team_lead_name}}" data-toggle="tooltip">{{str_limit($team_lead_name,10)}}
                        </a>
                    </li>

                    <li class="list-group-item">
                             
                        <label>{{Lang::get('lang.team_size')}}</label> 

                        <a class="badge badge-primary text-white float-right">{{$team_members}}</a>
                    </li>

                    <li class="list-group-item">
                             
                        <label>{{Lang::get('lang.status')}}</label> 

                         @if($teams->status == '1')
                        <span style="color:green;" class="float-right">{!! Lang::get('lang.active') !!}</span>
                        @else
                        <span style="color:red;" class="float-right">{!! Lang::get('lang.inactive') !!}</span>
                        @endif
                    </li>
                </ul>
            </div> 
         </div>
    </div>
    


    <div class="col-md-9">
      <div class="card card-light">
        <div class="card-header">
            <h3 class="card-title">{!! Lang::get('lang.team_members') !!}</h3>
        </div>
        <input type="hidden" name="show_id" value="{{$teams->id}}">
        <!-- /.box-header -->
        <div class="card-body">             
            {!! $html->table(['id' => 'sd','class' => 'table table-bordered ']) !!}
        </div>
        <!-- /.box-body -->
        <div class="card-footer">
        </div>
    </div>
        {{-- detals table starts --}}
        <?php
              
            $open = count(App\Model\helpdesk\Ticket\Tickets::where('team_id', '=', $teams->id)->whereIn('status', getStatusArray('open'))->get());
            $unapproved = count(App\Model\helpdesk\Ticket\Tickets::where('team_id', '=', $teams->id)->whereIn('status', getStatusArray('unapproved'))->get());
            $tickets = App\Model\helpdesk\Ticket\Tickets::where('team_id', '=', $teams->id)->where('status', '=', '3')->orderBy('id', 'DESC')->paginate(20);
            $counted = count(App\Model\helpdesk\Ticket\Tickets::where('team_id', '=', $teams->id)->whereIn('status', getStatusArray('closed'))->get());
            $deleted = count(App\Model\helpdesk\Ticket\Tickets::where('team_id', '=', $teams->id)->whereIn('status', getStatusArray('deleted'))->get());
     
        ?>

        <div class="card card-light card-outline card-outline-tabs">
            
            <div class="card-header p-0 border-bottom-0">
                
                <ul class="nav nav-tabs" role="tablist">
                
                  <li class="nav-item">
                    <a class="nav-link active" id="open_tab" data-toggle="pill" href="#tab_1" role="tab">
                        {!! Lang::get('lang.open_tickets') !!} ({{$open}})</a>
                    </a>
                  </li>  

                    <li class="nav-item">
                        <a href="#tab_2" data-toggle="pill" role="tab"  id="closed_tab" class="nav-link">
                        {!! Lang::get('lang.closed_tickets') !!} ({{$counted}})</a>
                    </li>

                    <li class="nav-item">
                        <a href="#tab_3" data-toggle="pill" role="tab"  id="deleted_tab" class="nav-link">
                        {!! Lang::get('lang.deleted_tickets') !!} ({{$deleted}})</a>
                    </li>

                    <li class="nav-item">
                        <a href="#tab_4" id="unapproved_tab" data-toggle="pill" role="tab" class="nav-link">
                        {!! Lang::get('lang.unapproved_tickets') !!} ({{$unapproved}})</a>
                    </li>
                </ul>
             </div>

             <div class="card-body">
                 
                 <div class="tab-content">
                <div class="tab-pane active" role="tabpanel">
                    @if(Session::has('success'))
                    <div id="success-alert" class="alert alert-success alert-dismissable">
                        <i class="fas  fa-check-circle"> </i>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{Session::get('success')}}
                    </div>
                    @endif
                    <!-- failure message -->
                    @if(Session::has('fails'))
                    <div class="alert alert-danger alert-dismissable">
                        <i class="fas fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!} ! </b>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{Session::get('fails')}}
                    </div>
                    @endif

                    <div class="mailbox-controls">
                    </div>
                    <div class="tab-pane active" id="tab_1">
                    </div>
                    <div class="tab-pane active" id="tab_2">
                    </div>
                    <div class="tab-pane active" id="tab_3">
                    </div>
                    <div class="mb-4">
                    <button type="button" class="btn btn-sm btn-default text-green" id="Edit_Ticket" data-toggle="modal" data-target="#MergeTickets"><i class="fas fa-code-branch"> </i> {!! Lang::get('lang.merge') !!}</button>
            <?php $inputs   = Input::all(); ?>
           <div class="btn-group">
           <?php $statuses = Finder::getCustomedStatus(); ?>
            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" id="d1">
                <i class="fas fa-exchange-alt" style="color:teal;" id="hidespin"> </i>
                <i class="fas fa-spinner fa-spin" style="color:teal; display:none;" id="spin"></i>
                {!! Lang::get('lang.change_status') !!} 
            </button>

            <div class="dropdown-menu status-list">

                @foreach($statuses as $ticket_status)    
                <a onclick="changeStatus({!! $ticket_status -> id !!}, '{!! $ticket_status->name !!}')" 
                    class="dropdown-item" href="javascript:;">
                    <i class="{!! $ticket_status->icon !!}" style="color:{!! $ticket_status->icon_color !!};"> </i> {!! $ticket_status->name !!}
                </a>
                @endforeach
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-default" id="assign_Ticket" data-toggle="modal" data-target="#AssignTickets" style="display: none;"><i class="fas fa-hand-point-right"> </i> {!! Lang::get('lang.assign') !!}</button>
    </div>
                    <div class="mailbox-messages"  id="refresh">
                        <table id="chumper" class="table table-bordered dataTable no-footer">
                <thead>
                    <tr>
                        <td><a class="checkbox-toggle"><i class="far fa-square fa-2x"></i></a></td>
                        <td>{!! Lang::get('lang.subject') !!}</td>
                        <td>{!! Lang::get('lang.ticket_id') !!}</td>
                        <td>{!! Lang::get('lang.from') !!}</td>
                        <td>{!! Lang::get('lang.assigned_to') !!}</td>
                        <td>{!! Lang::get('lang.last_activity') !!}</td>
                    </tr>
            </table>
                    </div><!-- /.mail-box-messages -->
                </div>
            </div><!-- nav-tabs-custom -->
             </div>
        </div>

        @if(Auth::user()->role == 'admin')
        <div class="row">
            <div class="col-md-12">
                
                <div class="card card-light">
                    <div class="card-header">
                   
                      
                         <h3 class="card-title">{!! Lang::get('lang.team_report') !!}</h3>
                         

                    </div>
                    <div class="card-body">
                        <form id="foo">
                            <div  class="form-group">
                                <div class="row">
                                    <div class='col-sm-4'>
                        {!! Form::label('date', Lang::get('lang.start_date'),['class' => '']) !!}
                        {!! Form::text('start_date',null,['class'=>'form-control','id'=>'datepicker4', 'placeholder' => 'YYYY/mm/dd'])!!}
                        {{ csrf_field() }}
                    </div>
                    <?php
                    $start_date = App\Model\helpdesk\Ticket\Tickets::where('id', '=', '1')->first();
                    if ($start_date != null) {
                        $created_date = $start_date->created_at;
                        $created_date = explode(' ', $created_date);
                        $created_date = $created_date[0];
                        $start_date = date("m/d/Y", strtotime($created_date . ' -1 months'));
                    } else {
                        $start_date = date("m/d/Y", strtotime(date("m/d/Y") . ' -1 months'));
                    }
                    ?>
                    
                    <div class='col-sm-4'>
                        {!! Form::label('start_time',Lang::get('lang.end_date'),['class' => '']) !!}
                        {!! Form::text('end_date',null,['class'=>'form-control','id'=>'datetimepicker3', 'placeholder' => 'YYYY/mm/dd'])!!}
                    </div>
                    <script type="text/javascript">
                        $(function () {
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
                    <div class='col-sm-3' style='margin-top:30px'>
                        <!-- {!! Form::label('filter', Lang::get('lang.filter'),['class' => 'lead hide']) !!}<br> -->
                        {!! Form::submit(Lang::get('lang.submit'),['class'=>'form-group btn btn-primary','id'=>'filter-form'])!!}  
                        <!-- <button class="btn btn-primary" id="filter-form">Submit</button> -->
                    </div>
                                    <div class="col-sm-10">
                                        <!-- <label class="lead hide">{!! Lang::get('lang.Legend') !!}:</label> -->
                                        <div class="row">
                                            <style>
                                                #legend-holder { border: 1px solid #ccc; float: left; width: 25px; height: 25px; margin: 1px; }
                                            </style>

                           
                        <div class="col-md-4"><span id="legend-holder" style="background-color: #6C96DF;"></span>&nbsp; <span class="lead"> <span id="total-created-tickets" ></span> {!! Lang::get('lang.assign_tickets') !!}  </span></div>
                         
                    

 <div class="col-md-4"><span id="legend-holder" style="background-color: #6DC5B2;"></span>&nbsp; <span class="lead"> <span id="total-reopen-tickets" class="lead"></span> {!! Lang::get('lang.tickets') !!} {!! Lang::get('lang.reopen') !!}  </span></div> 
                                            <div class="col-md-4"><span id="legend-holder" style="background-color: #E3B870;"></span>&nbsp; <span class="lead"> <span id="total-closed-tickets" class="lead"></span> {!! Lang::get('lang.tickets') !!} {!! Lang::get('lang.closed') !!}  </span></div>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="legendDiv"></div>
                        <div class="chart">
                            <canvas class="chart-data" id="tickets-graph" width="1000" height="250"></canvas>   
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </div>
    @endif
    <!-- END CUSTOM TABS -->
@include('themes.default1.agent.helpdesk.ticket.more.tickets-model')

@include('themes.default1.agent.helpdesk.ticket.more.tickets-options-script')
 <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
 <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
 <script src="{{assetLink('js','moment')}}" type="text/javascript"></script>
<script src="{{assetLink('js','bootstrap-datetimepicker4')}}" type="text/javascript"></script>
<script src="{{assetLink('js','chart-min')}}" type="text/javascript"></script>
<!--         iCheck -->
<script src="{{assetLink('js','iCheck')}}" type="text/javascript" async></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#sd').dataTable({
                "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>"+
                        "t"+
                        "<'row'<'col-sm-6'i><'col-sm-6'p>>",
                "sPaginationType": "full_numbers",
                "bProcessing": true,
                "bServerSide": true,
                "bStateSave" : false,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ Records per page",
                    "sSearch"    : "Search: ",
                    "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
                },
                    columns:[
                          {data:'user_name',name:'user_name'},
                          {data:'first_name',name:'first_name'},
                          {data:'active',name:'active'},
                          {data:'role',name:'role'},
                 ],   
                "fnDrawCallback": function( oSettings ) {
                    $('.loader1').css('display', 'none');
                    $("#sd").css({"opacity": "1"});
                    $('#blur-bg').css({"opacity": "0.7", "z-index": "99999"});
                },
                "fnPreDrawCallback": function(oSettings, json) {
                    $('.loader1').css('display', 'block');
                    $("#sd").css({"opacity":"0.3"});
                    $('#blur-bg').css({"opacity": "0.7", "z-index": "99999"});
                },
                "ajax": {
                    url: "{{route('teams.getshow.list', $teams->id)}}",
                }
            });
        });

        $(function () {
            $('#datepicker4, #datetimepicker3').on('focus', function(){
                $('.col-sm-4').removeClass('has-error');
            });
        });


        $(document).ready(function () { /// Wait till page is loaded
            $('#click').click(function () {
                $('#refresh').load('open #refresh');
                $("#show").show();
            });
        });

        </script>
    
    
    <script type="text/javascript">
        $(document).ready(function () {
             $.getJSON("../user-agen/<?php echo $teams->id; ?>/team", function (result) {
                var labels = [], open = [], closed = [], reopened = [], open_total = 0, closed_total = 0, reopened_total = 0;
                for (var i = 0; i < result.length; i++) {
                    labels.push(result[i].date);
                    open.push(result[i].open);
                    closed.push(result[i].closed);
                    reopened.push(result[i].reopened);

                    open_total += parseInt(result[i].open);
                    closed_total += parseInt(result[i].closed);
                    reopened_total += parseInt(result[i].reopened);
                }
                var buyerData = {
                    labels: labels,
                    datasets: [
                        {
                            label: "Open Tickets",
                            fillColor: "rgba(93, 189, 255, 0.05)",
                            strokeColor: "rgba(2, 69, 195, 0.9)",
                            pointColor: "rgba(2, 69, 195, 0.9)",
                            pointStrokeColor: "#c1c7d1",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            data: open
                        }
                        , {
                            label: "Closed Tickets",
                            fillColor: "rgba(255, 206, 96, 0.08)",
                            strokeColor: "rgba(221, 129, 0, 0.94)",
                            pointColor: "rgba(221, 129, 0, 0.94)",
                            pointStrokeColor: "rgba(60,141,188,1)",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(60,141,188,1)",
                            data: closed

                        }
                        , {
                            label: "Reopened Tickets",
                            fillColor: "rgba(104, 255, 220, 0.06)",
                            strokeColor: "rgba(0, 149, 115, 0.94)",
                            pointColor: "rgba(0, 149, 115, 0.94)",
                            pointStrokeColor: "rgba(60,141,188,1)",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(60,141,188,1)",
                            data: reopened
                        }
                    ]
                };
                $("#total-created-tickets").html(open_total);
                $("#total-reopen-tickets").html(reopened_total);
                $("#total-closed-tickets").html(closed_total);
                var myLineChart = new Chart(document.getElementById("tickets-graph").getContext("2d")).Line(buyerData, {
                    showScale: true,
                    //Boolean - Whether grid lines are shown across the chart
                    scaleShowGridLines: false,
                    //String - Colour of the grid lines
                    scaleGridLineColor: "rgba(0,0,0,.05)",
                    //Number - Width of the grid lines
                    scaleGridLineWidth: 1,
                    //Boolean - Whether to show horizontal lines (except X axis)
                    scaleShowHorizontalLines: true,
                    //Boolean - Whether to show vertical lines (except Y axis)
                    scaleShowVerticalLines: true,
                    //Boolean - Whether the line is curved between points
                    bezierCurve: true,
                    //Number - Tension of the bezier curve between points
                    bezierCurveTension: 0.3,
                    //Boolean - Whether to show a dot for each point
                    pointDot: true,
                    //Number - Radius of each point dot in pixels
                    pointDotRadius: 1,
                    //Number - Pixel width of point dot stroke
                    pointDotStrokeWidth: 1,
                    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                    pointHitDetectionRadius: 10,
                    //Boolean - Whether to show a stroke for datasets
                    datasetStroke: true,
                    //Number - Pixel width of dataset stroke
                    datasetStrokeWidth: 1,
                    //Boolean - Whether to fill the dataset with a color
                    datasetFill: true,
                    //String - A legend template
                    //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                    maintainAspectRatio: true,
                    //Boolean - whether to make the chart responsive to window resizing
                    responsive: true,
                });

            });
            $('#click me').click(function () {
                $('#foo').submit();
            });
            
            $('#foo').submit(function (event) {
                // get the form data
                // there are many ways to get this data using jQuery (you can use the class or id also)
                var date1 = $('#datepicker4').val();
                var date2 = $('#datetimepicker3').val();
                if(date1 == '' || date2 == '') {
                    $('.col-sm-4').addClass('has-error');
                    alert('{{Lang::get("lang.please-select-a-valid-date-range")}}');
                    return false;
                }
                var formData = date1.split("/").join('-');
                var dateData = date2.split("/").join('-');
                // process the form
                $.ajax({
                    type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                    url: '../user-chart-range/<?php echo $teams->id; ?>/team/' + dateData + '/' + formData, // the url where we want to POST
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'data': formData,
                    }, // our data object
                    dataType: 'json', // what type of data do we expect back from the server
                    beforeSend: function() {
                        $('.loader1').css('display','block');
                    },
                    success: function (result2) {
                        $('.loader1').css('display','none');
                        var labels = [], open = [], closed = [], reopened = [], open_total = 0, closed_total = 0, reopened_total = 0;

                        for (var i = 0; i < result2.length; i++) {

                            labels.push(result2[i].date);
                            open.push(result2[i].open);
                            closed.push(result2[i].closed);
                            reopened.push(result2[i].reopened);

                            open_total += parseInt(result2[i].open);
                            closed_total += parseInt(result2[i].closed);
                            reopened_total += parseInt(result2[i].reopened);
                        }
                        var buyerData = {
                            labels: labels,
                            datasets: [
                                {
                                    label: "Open Tickets",
                                    fillColor: "rgba(93, 189, 255, 0.05)",
                                    strokeColor: "rgba(2, 69, 195, 0.9)",
                                    pointColor: "rgba(2, 69, 195, 0.9)",
                                    pointStrokeColor: "#c1c7d1",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(220,220,220,1)",
                                    data: open
                                }
                                , {
                                    label: "Closed Tickets",
                                    fillColor: "rgba(255, 206, 96, 0.08)",
                                    strokeColor: "rgba(221, 129, 0, 0.94)",
                                    pointColor: "rgba(221, 129, 0, 0.94)",
                                    pointStrokeColor: "rgba(60,141,188,1)",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(60,141,188,1)",
                                    data: closed

                                }
                                , {
                                    label: "Reopened Tickets",
                                    fillColor: "rgba(104, 255, 220, 0.06)",
                                    strokeColor: "rgba(0, 149, 115, 0.94)",
                                    pointColor: "rgba(0, 149, 115, 0.94)",
                                    pointStrokeColor: "rgba(60,141,188,1)",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(60,141,188,1)",
                                    data: reopened
                                }
                            ]
                        };
                        $("#total-created-tickets").html(open_total);
                        $("#total-reopen-tickets").html(reopened_total);
                        $("#total-closed-tickets").html(closed_total);
                        var myLineChart = new Chart(document.getElementById("tickets-graph").getContext("2d")).Line(buyerData, {
                            showScale: true,
                            //Boolean - Whether grid lines are shown across the chart
                            scaleShowGridLines: false,
                            //String - Colour of the grid lines
                            scaleGridLineColor: "rgba(0,0,0,.05)",
                            //Number - Width of the grid lines
                            scaleGridLineWidth: 1,
                            //Boolean - Whether to show horizontal lines (except X axis)
                            scaleShowHorizontalLines: true,
                            //Boolean - Whether to show vertical lines (except Y axis)
                            scaleShowVerticalLines: true,
                            //Boolean - Whether the line is curved between points
                            bezierCurve: true,
                            //Number - Tension of the bezier curve between points
                            bezierCurveTension: 0.3,
                            //Boolean - Whether to show a dot for each point
                            pointDot: true,
                            //Number - Radius of each point dot in pixels
                            pointDotRadius: 1,
                            //Number - Pixel width of point dot stroke
                            pointDotStrokeWidth: 1,
                            //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                            pointHitDetectionRadius: 10,
                            //Boolean - Whether to show a stroke for datasets
                            datasetStroke: true,
                            //Number - Pixel width of dataset stroke
                            datasetStrokeWidth: 1,
                            //Boolean - Whether to fill the dataset with a color
                            datasetFill: true,
                            //String - A legend template
                            //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                            maintainAspectRatio: true,
                            //Boolean - whether to make the chart responsive to window resizing
                            responsive: true,
                        });
                        myLineChart.options.responsive = false;
                        $("#tickets-graph").remove();
                        $(".chart").html("<canvas id='tickets-graph' width='1000' height='250'></canvas>");
                        var myLineChart1 = new Chart(document.getElementById("tickets-graph").getContext("2d")).Line(buyerData, {
                            showScale: true,
                            //Boolean - Whether grid lines are shown across the chart
                            scaleShowGridLines: false,
                            //String - Colour of the grid lines
                            scaleGridLineColor: "rgba(0,0,0,.05)",
                            //Number - Width of the grid lines
                            scaleGridLineWidth: 1,
                            //Boolean - Whether to show horizontal lines (except X axis)
                            scaleShowHorizontalLines: true,
                            //Boolean - Whether to show vertical lines (except Y axis)
                            scaleShowVerticalLines: true,
                            //Boolean - Whether the line is curved between points
                            bezierCurve: true,
                            //Number - Tension of the bezier curve between points
                            bezierCurveTension: 0.3,
                            //Boolean - Whether to show a dot for each point
                            pointDot: true,
                            //Number - Radius of each point dot in pixels
                            pointDotRadius: 1,
                            //Number - Pixel width of point dot stroke
                            pointDotStrokeWidth: 1,
                            //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                            pointHitDetectionRadius: 10,
                            //Boolean - Whether to show a stroke for datasets
                            datasetStroke: true,
                            //Number - Pixel width of dataset stroke
                            datasetStrokeWidth: 1,
                            //Boolean - Whether to fill the dataset with a color
                            datasetFill: true,
                            //String - A legend template
                            //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                            maintainAspectRatio: true,
                            //Boolean - whether to make the chart responsive to window resizing
                            responsive: true,
                        });


                    }
                });
                // using the done promise callback
                // stop the form from submitting the normal way and refreshing the page
                event.preventDefault();
            });
        });

        jQuery(document).ready(function () {
            // Close a ticket
            $('#close').on('click', function (e) {
                $.ajax({
                    type: "GET",
                    url: "agen",
                    beforeSend: function () {
                        $('.loader1').css('display','block');
                    },
                    success: function (response) {
                        $('.loader1').css('display','none');
                    }
                })
                return false;
            });
        });
    </script>

    <script>
        $('#assign_Ticket').on('click', function (e) {
            window.scrollTo(0,0)
        });
    </script>
    
    @include('vendor.yajra.tuser-javascript')
    @stop
