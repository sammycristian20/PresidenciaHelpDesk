@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.priority_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.priority_lists-page-description') !!}">


@stop

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('priority')
class="active"
@stop
@section('custom-css')
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">

@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.priority') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop

<!-- content -->
@section('content')
<div class="alert alert-success alert-dismissable" style="display: none;">
    <i class="fas  fa-check-circle"></i>
    <span class="success-msg"></span>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    
</div>
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
        <h3 class="card-title">{!! Lang::get('lang.list_of_ticket_priority') !!}</h3>

        <div class="card-tools">
            
            <a href="{{route('priority.create')}}" class="btn btn-tool" title="{{Lang::get('lang.create_ticket_priority')}}"
            data-toggle="tooltip">
                <span class="glyphicon glyphicon-plus"></span>
            </a>        
        </div>
    </div>
    
     




    <div class="card-body">
        <table class="table table-bordered" id="priority-table">
            <thead>
                <tr>
                <th>{!! Lang::get('lang.priority') !!}</th>
                <th>{!! Lang::get('lang.priority_desc') !!}</th>
                <th>{!! Lang::get('lang.priority_color') !!}</th>
                <th>{!! Lang::get('lang.status') !!}</th>
                <th>{!! Lang::get('lang.action') !!}</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="box-footer">
    </div>
</div>
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

<!--  -->
<script>
    function confirmDelete(priority_id) {
        var r = confirm('Are you sure?');
        if (r == true) {
            // alert('{!! url("ticket_priority") !!}/' + priority_id + '/destroy');
            window.location = '{!! url("ticket/priority") !!}/' + priority_id + '/destroy';
            //    $url('ticket_priority/' . $model->priority_id . '/destroy')
        } else {
            return false;
        }
    }
</script>
<script>
    $('#toggle_event_editing button').click(function () {

        var user_settings_priority=1;
         var user_settings_priority=0;
        if ($(this).hasClass('locked_active') ) {
         

            user_settings_priority = 0
        } if ( $(this).hasClass('unlocked_inactive')) {
          
            user_settings_priority = 1;
        }

        /* reverse locking status */
        $('#toggle_event_editing button').eq(0).toggleClass('locked_inactive locked_active btn-default btn-info');
        $('#toggle_event_editing button').eq(1).toggleClass('unlocked_inactive unlocked_active btn-info btn-default');
        $.ajax({
            type: 'post',
            url: '{{route("user.priority.index")}}',
            data: {
                "_token": "{{ csrf_token() }}",
                user_settings_priority: user_settings_priority
            },
            beforeSend: function() {
                $('.loader').css('display','block');
            },
            success: function (result) {
                $('.loader').css('display','none');
                $('.success-msg').html(result);
                $('.alert-success').css('display', 'block');
                setInterval(function(){
                    $('.alert-success').slideUp( 3000, function() {});
                }, 500);
            }
        });
    });
</script>

@stop
@push('scripts')
<script type="text/javascript">
    $('#priority-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('priority.index1') !!}",
        "oLanguage": {
            "sLengthMenu": "_MENU_ Records per page",
            "sSearch"    : "Search: ",
            "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
        },
        columns:[
           {data:'priority', name:'priority'},
           {data:'priority_desc',name:'priority_desc'},
           {data: 'priority_color', name: 'priority_color'},
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
            { "orderable": false, "targets": [2,4]},
            { "searchable": false, "targets": [3] },
        ]
    });
</script>
@endpush