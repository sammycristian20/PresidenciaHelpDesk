@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.ticket_types_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.ticket_types_lists-page-description') !!}">


@stop

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('ticket-types')
class="active"
@stop

@section('HeadInclude')
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">

@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.ticket_type') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop

<!-- content -->
@section('content')

@if(Session::has('success'))
<div class="alert alert-success alert-dismissable">
    <i class="fas  fa-check-circle"></i>

    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {!! Session::get('success') !!}
</div>
@endif
<!-- failure message -->
@if(Session::has('fails'))
<div class="alert alert-danger alert-dismissable">
    <i class="fas fa-ban"></i>
    <b>Fail!</b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {!! Session::get('fails') !!}
</div>
@endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.list of ticket types') !!}</h3>
        <div class="card-tools">
        <a href="{{route('ticket.type.create')}}" class="btn btn-tool" title="{{Lang::get('lang.create_ticket_type')}}" 
        data-toggle="tootip">
            <span class="glyphicon glyphicon-plus"></span></a></div></div>
 <div class="card-body">
    <table class="table table-bordered" id="type-table">
        <thead>
            <tr>
            <th>{!! Lang::get('lang.ticket_type_name') !!}</th>
            <th>{!! Lang::get('lang.type_desc') !!}</th>
            <th>{!! Lang::get('lang.status') !!}</th>
            <th>{!! Lang::get('lang.action') !!}</th>
            </tr>
        </thead>
    </table>
    </div>
</div>

<script>
    function confirmDelete(id) {
        var r = confirm('Are you sure?');
        if (r == true) {
            // alert('{!! url("ticket_priority") !!}/' + id + '/destroy');
            window.location = '{!! url("ticket-types") !!}/' + id + '/destroy';
            //    $url('ticket_priority/' . $model->priority_id . '/destroy')
        } else {
            return false;
        }
    }
</script>
@stop
@push('scripts')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

<script type="text/javascript">
    $('#type-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('ticket.type.index1') !!}",
        "oLanguage": {
            "sLengthMenu": "_MENU_ Records per page",
            "sSearch"    : "Search: ",
            "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
        },
        columns:[
           {data:'name', name:'name'},
           {data:'type_desc',name:'type_desc'},
           {data:'status',name:'status'},
           {data:'action',name:'action'},
        ],
        "fnDrawCallback": function( oSettings ) {
            $("#tag-table").css({"opacity": "1"});
            $('.loader').css('display', 'none');
        },
        "fnPreDrawCallback": function(oSettings, json) {
            $('.loader').css('display', 'block');
            $("#tag-table").css({"opacity":"0.3"});
            $('#blur-bg').css({"opacity": "0.7", "z-index": "99999"});
        },
        "columnDefs": [
            { "orderable": false, "targets": [3]},
            { "searchable": false, "targets": [3] },
        ]
    });
</script>
@endpush