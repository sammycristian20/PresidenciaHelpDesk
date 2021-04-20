@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.tags_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.tags_lists-page-description') !!}">
@stop

@section('Tickets')
active
@stop

@section('manage-bar')
active
@stop

@section('tags')
class="active"
@stop

@section('custom-css')
 <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

<!-- header -->
@section('PageHeader')
<h1>{{trans('lang.tags')}}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')

@if(Session::has('success'))
<div class="alert alert-success alert-dismissable">
    <i class="fa fa-check-circle"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('success')}}
</div>
@endif
@if(Session::has('fails'))
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('fails')}}
</div>
@endif
@if(Session::has('warn'))
<div class="alert alert-warning alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('warn')}}
</div>
@endif
<div class="card card-light">

    <div class="card-header">
        <h3 class="card-title">
            {{trans('lang.list_of_tags')}}
        </h3>
        <div class="card-tools">
            
            <a href="{{url('tag/create')}}" class="btn btn-tool" title="{{trans('lang.create_tag')}}" data-toggle="tooltip">
                <span class="glyphicon glyphicon-plus"></span></a>
        </div>

    </div>
    <div class="card-body">
        <table class="table table-bordered" id="tag-table">
            <thead>
                <tr>
                    <th>{!! trans('lang.name') !!}</th>
                    <th>{!! trans('lang.tickets-count') !!}</th>
                    <th>{!! trans('lang.description') !!}</th>
                    <th>{!! trans('lang.action') !!}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@push('scripts')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

<script type="text/javascript">
    $('#tag-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! url('tag-ajax') !!}",
        "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
            },
              columns:[
                       {data:'name', name:'name'},
                       {data:'count',name:'count'},
                       {data:'description',name:'description'},
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
            { "orderable": false, "targets": [2,3]},
            { "searchable": false, "targets": [,3] },
        ]
    });
</script>
@endpush
@stop