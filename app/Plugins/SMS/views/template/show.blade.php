@extends('themes.default1.admin.layout.admin')

@section('Plugins')
active
@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
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
        <h3 class="card-title">{{Lang::get('SMS::lang.sms-templates')}}</h3>
        <div class="card-tools">
            <button class="btn btn-tool" data-toggle="modal" data-target="#createtemp" id="2create">
                <span class="glyphicon glyphicon-plus"  title="{{Lang::get('lang.create_template')}}"  data-toggle="tooltip"></span></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="card-body ">
        
        {!! Form::open(['id'=>'modalpopup', 'route'=>'template-delete','method'=>'post']) !!}
        <!--<div class="mailbox-controls">-->
            <!-- Check all button -->
        <!--</div>-->
        <p><p/>
        <div class="mailbox-messages" id="refresh">
            <!--datatable-->
            <table class="table table-bordered" id="chumper">
                <thead>
                    <tr>
                        <td>{!! Lang::get('SMS::lang.name') !!}</td>
                        <td>{!! Lang::get('SMS::lang.status') !!}</td>
                        <td>{!! Lang::get('lang.template_language') !!}</td>
                        <td>{!! Lang::get('SMS::lang.action') !!}</td>
                    </tr>
                </thead>
            </table>
            <!-- /.datatable -->
        </div><!-- /.mail-box-messages -->
        {!! Form::close() !!}
    </div><!-- /.box-body -->

</div>
<!-- /.box -->
<!--modal-->
<div class="modal fade" id="createtemp">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route'=>'template-set-createnew']) !!}
            <div class="modal-header">
               
                <h4 class="modal-title">{{Lang::get('SMS::lang.create_template')}}</h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">

                    {!! Form::label('folder_name', Lang::get('SMS::lang.template-set-name'),['style'=>'display: block']) !!}

                    {!! Form::text('folder_name',null,['class'=>'form-control', 'placeholder' => Lang::get('SMS::lang.template-set-name-placeholder')])!!}

                    {!! $errors->first('folder', '<spam class="help-block">:message</spam>') !!}
                    
                    <div class="form-group {{ $errors->has('template_language') ? 'has-error' : '' }}">
                    <label for="title">{!! Lang::get('lang.select_template_language') !!}:<span style="color:red;">*</span></label><br>
                    {!! Form::select('template_language',$languages, 'en' ,['class'=>'form-control'])!!}
                </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                       {!! Form::submit(Lang::get('SMS::lang.create'), ['class'=>'btn btn-primary'])!!}
                
            </div>
            {!! Form::close() !!}
        </div> 
    </div>
</div>
<!--.modal-->
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
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-times" aria-hidden="true">&nbsp;</i>{!! Lang::get('lang.close') !!}</button>
                <a href="#" class="btn btn-danger" onclick="deleteSet()"><i class="fas fa-trash" aria-hidden="true">&nbsp;</i>{!! Lang::get('lang.delete') !!}</a>
                </div>
            </div>
    </div>
</div>
@stop

@push('scripts')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script type="text/javascript">
    $('#chumper').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('get-sms-template') !!}",
        "oLanguage": {
            "sLengthMenu": "_MENU_ Records per page",
            "sSearch"    : "Search: ",
            "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
        },
        columns:[
           {data:'id2', name:'id2'},
           {data:'id3',name:'id3'},
           {data:'id4',name:'id4'},
           {data: 'id5', name: 'id5'},
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
            { "orderable": false, "targets": []},
            { "searchable": false, "targets": [] },
        ]
    });
</script>
<script type="text/javascript">
    var set_id = 0;
    var confirmed = false;
    function confirmDelete(id) {
        console.log(confirmed);
        if (confirmed) {
            confirmed = false;
            $('#modalpopup').append('<input type="hidden" value="'+set_id+'" name="select_all[]">');
            return true;
        } else {
            $('#myModal').modal('show');
            set_id = id;
            return false;
        }
    }

    function deleteSet() {
        confirmed = true;
        btn_id = '#delete'+set_id;
        $(btn_id).click();
    }
</script>
@endpush

<!-- /content -->
