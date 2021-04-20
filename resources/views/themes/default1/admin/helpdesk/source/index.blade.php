@extends('themes.default1.admin.layout.admin')

@section('Tickets')
active
@stop

@section('manage-bar')
active
@stop

@section('source')
class="active"
@stop

@section('custom-css')
   <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop
<!-- header -->
@section('PageHeader')
<h1>{{trans('lang.source')}}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('custom-css')
<style type="text/css">

.avatar img {
  width: 60px;
}
.avatar,
.info {
  display: table-cell;
  vertical-align: middle;
   padding-left:0.5cm;
}
table { table-layout:fixed; word-break:break-all; word-wrap:break-word; }
</style>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')

@if(Session::has('success'))
<div class="alert alert-success alert-dismissable">
    <i class="fas fa-check-circle"></i>
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
        <h3 class="card-title">{!! Lang::get('lang.list_of_source') !!}</h3>
        <div class="card-tools">
             <a href="{{url('source/create')}}" class="btn btn-tool" title="{{Lang::get('lang.create_source')}}" data-toggle="tooltip">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

        </div><!-- /.box-header -->
    </div>

  
    <div class="card-body">
        <table class="table table-bordered" id="source-table">
            <thead>
                <tr>
                <th>{!! Lang::get('lang.name') !!}</th>
                <th>{!! Lang::get('lang.displayname') !!}</th>
                <th>{!! Lang::get('lang.description') !!}</th>
                <th>{!! Lang::get('lang.action') !!}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop
@push('scripts')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script type="text/javascript">
    $('#source-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! url('source-ajax') !!}",
        "oLanguage": {
            "sLengthMenu": "_MENU_ Records per page",
            "sSearch"    : "Search: ",
            "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
        },
        columns:[
           {data:'name', name:'name'},
           {data:'value',name:'value'},
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
            { "orderable": false, "targets": [3]},
            { "searchable": false, "targets": [1,2,3] },
        ]
    });
</script>
<!-- url('source-ajax') -->
@endpush