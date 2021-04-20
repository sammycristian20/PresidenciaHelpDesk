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
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css"  media="none" onload="this.media='all';"/>
@stop

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('report::lang.time-sheet-summary') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('content')
<div class="box box-primary">
    <div class="box-header">
        <button class="btn btn-primary" onclick="showhidefilter();">{!! Lang::get('report::lang.filter') !!}</button>
        @include('report::indepth.filter')
       
        <div class="btn-group pull-right">
        @include('report::performance.popup')
        </div>
        <div class="btn-group pull-right">
        @include('report::popup.mail')
         </div>
        
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
                {!! Lang::get('report::lang.hours-tracked') !!} <h2><span id="total-hours"></span></h2>
            </div>
            <div class="col-md-5">
                <div class="col-md-6">
                {!! Lang::get('report::lang.billable-hours') !!} <h2><span id="billable-hours"></span></h2>
                </div>
                <div class="col-md-6">
                {!! Lang::get('report::lang.billable-amount') !!} <h2><span id="billable-amount"></span></h2>
                </div>
            </div>
            <div class="col-md-4">
                {!! Lang::get('report::lang.non-billable-hours') !!} <h2><span id="nonbillable-hours"></span></h2>
            </div>
        </div><br>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered" cellspacing="0" width="100%" id="users-table">
            <thead>
                <tr>
                    <th>{!! Lang::get('report::lang.notes') !!}</th>
                    <th>{!! Lang::get('report::lang.customer') !!}</th>
                   
                    <th>{!! Lang::get('report::lang.date') !!}</th>
                    <th>{!! Lang::get('report::lang.department') !!}</th>
                    <th>{!! Lang::get('report::lang.hours') !!}</th>
                    <th>{!! Lang::get('report::lang.amount') !!}</th>
                </tr>
            </thead>
        </table>
            </div>

        </div>
    </div>
</div>
@stop
@section('FooterInclude')

<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript" ></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}" type="text/javascript" async></script>
<script>
billDetails();
$("#submit-filter").click(function () {
    var data = $("#filter").serialize();
    billDetails(data);
});
function billDetails(data=""){
    $.ajax({
        url:"{{url('report/timesheet/data')}}",
        data:data,
        dataType:'json',
        success:function(json){
            $("#total-hours").html(json.total_hours);
            $("#billable-hours").html(json.bill_hour);
            $("#billable-amount").html(json.bill_amount);
            $("#nonbillable-hours").html(json.nonbill_hour);
        }
    });
}
</script>
<script type="text/javascript">
    $(function () {
        var oTable = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            oLanguage: {
                "sLengthMenu": "_MENU_ Show  entries",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%; left:50%; width: 50px; height:50 px; display: block; position: fixed;" src="{!! assetLink("image","gifloader3") !!}">'
            },
            fnDrawCallback: function( oSettings ) {
                $('.loader').hide();
            },
            fnPreDrawCallback: function(oSettings, json) {
                $('.loader').show();
                $('#blur-bg').css({"opacity": "0.7", "z-index": "99999"});
            },
            ajax: {
                url: "{{ url('report/timesheet/datatable') }}",
                data: function (d) {
                    d.start_date = $('input[name=start_date]').val();
                    d.end_date = $('input[name=end_date]').val();
                    d.agents = $('#agents').val();
                    d.departments = $('#departments').val();
                    d.sources = $('#sources').val();
                    d.priorities = $('#priorities').val();
                    d.clients = $('#clients').val();
                    d.types = $('#types').val();
                    d.status = $('#status').val();
                    d.helptopic = $('#helptopic').val();
                    d.team = $('#team').val();
                }
            },
            columns: [
                {data: 'note', name: 'note'},
                {data: 'client', name: 'client'},
                {data: 'bill_created', name: 'bill_created'},
                {data: 'department', name: 'department'},
                {data: 'hours', name: 'hours'},
                {data: 'amount', name: 'amount'},
            ]
        });

        $('#submit-filter').on('click', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
<script type="text/javascript">
     $("#send").on('click', function () {
        var data = $("#filter").serialize();
        var send = $("#mail-form").serialize();
        $.ajax({
            url: "{{url('report/timesheet/mail')}}",
            data: data+'&'+send,
            type: "post",
            dataType: "json",
            beforeSend: function () {
                $("#mail-form").hide();
                $("#loading-mail").show();
            },
            success: function(json) {
                $("#loading-mail").hide();
                    $("#mail-form").show();
                    $("#mail-response").html("<div class='alert alert-success'><ul>" + json[0] + "</ul></div>");
            },
            error: function(json) {
                $("#loading-mail").hide();
                $("#mail-form").show();
                var res = "";
                $.each(json.responseJSON, function (idx, topic) {
                res += "<li>" + topic + "</li>";
                });
                $("#mail-response").html("<div class='alert alert-danger'><strong>Whoops!</strong> There were some problems with your input.<br><br><ul>" + res + "</ul></div>");
            }
        });
    });
</script>
@stop

