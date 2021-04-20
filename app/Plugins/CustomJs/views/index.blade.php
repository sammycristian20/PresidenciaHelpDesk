@extends('themes.default1.admin.layout.admin')

@section('custom-css')
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css" media="none" onload="this.media='all';"/>
@stop

@section('Plugins')
active
@stop

@section('settings-bar')
active
@stop

@section('plugin')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.plugins') !!}</h1>
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
            <i class="fas fa-check-circle"> </i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!}! </b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('CustomJs::lang.custom-js')}}</h3>
        <div class="card-tools">
            <a href="{!! URL::route('customjs.create') !!}" class="btn btn-tool" title="{{Lang::get('lang.add')}}" id="2create"
                data-toggle="tooltip">
                <span class="glyphicon glyphicon-plus"></span></a>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="card-body ">
        
        {!! Form::open(['id'=>'modalpopup','method'=>'post']) !!}
        <!--<div class="mailbox-controls">-->
            <!-- Check all button -->
        <!--</div>-->
        <p><p/>
        <div class="mailbox-messages" id="refresh">
            <table class="table table-bordered" id="chumper">
                <thead>
                    <tr>
                        <td>{!!Lang::get('lang.name') !!}</td>
                        <td>{!!Lang::get('CustomJs::lang.parameter') !!}</td>
                        <td>{!!Lang::get('CustomJs::lang.fired_at') !!}</td>
                        <td>{!!Lang::get('lang.status') !!}</td>
                        <td>{!!Lang::get('lang.action') !!}</td>
                    </tr>
                </thead>
            </table>
        </div><!-- /.mail-box-messages -->
        {!! Form::close() !!}
    </div><!-- /.box-body -->

</div>
<!-- /.box -->
<!-- Modal -->   
<div class="modal fade in" id="myModal">
    <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <h4 class="modal-title">{!! Lang::get('lang.delete') !!}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="custom-alert-body" >
                    <span>{!!Lang::get('lang.confirm-to-proceed')!!}</span>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.close') !!}</button>
                <a href="#" class="btn btn-danger" onclick="deleteSet()"><i class="fas fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.delete') !!}</a>
                </div>
            </div>
    </div>
</div>
@stop

@section('FooterInclude')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script type="text/javascript">
	jQuery('#chumper').dataTable({
            "sPaginationType": "full_numbers",
            "bProcessing": true,
            "bServerSide": true,
            "oLanguage": {
                    "sLengthMenu": "_MENU_ Records per page",
                    "sSearch"    : "Search: ",
                    "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLInk('image','gifloader3') !!}">'
                },
                columns:[
                    {data:'name', name:'name', orderable: true},
                    {data:'parameter', name:'parameter', orderable: true},
                    {data:'fired_at', name:'fired_at', orderable: true},
                    {data:'fire', name:'fire', orderable: true},
                    {data:'action', name:'action'},
                ],
                "fnDrawCallback": function( oSettings ) {
                    $('.loader').css('display', 'none');
                    $(".box-body").css({"opacity": "1"});
                    $('#blur-bg').css({"opacity": "1", "z-index": "99999"});
                },
                "fnPreDrawCallback": function(oSettings, json) {
                    $('.loader').css('display', 'block');
                    $(".box-body").css({"opacity":"0.3"});
                },
            "ajax": {
                url: "{{route('get-cusotom-js')}}",
            },
            "columnDefs": [
                { "orderable": false, "targets": [4]},
                { "searchable": false, "targets": [4] },
            ]
    });
</script>
<script type="text/javascript">
    var set_id = 0;
    var confirmed = false;
    function confirmDelete(id) {
        if (confirmed) {
            confirmed = false;
            $('#myModal').modal('hide');
            $url = "{!! url('custom-js/delete') !!}/"+id;
            window.location = $url;
        } else {
            $('#myModal').modal('show');
            set_id = id;
            return false;
        }
    }

    function deleteSet() {
        confirmed = true;
        return confirmDelete(set_id);
    }
</script>
@stop

<!-- /content -->
