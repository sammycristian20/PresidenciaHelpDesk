@extends('themes.default1.admin.layout.admin')

@section('Plugins')
active
@stop

@section('settings-bar')
active
@stop

@section('modules')
active
@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.modules') !!}</h1>
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
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
 @if (count($errors) > 0)
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b><br/>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!!Session::get('success')!!}
        </div>
        @endif
        <div class="alert alert-success alert-dismissable" style="display: none;">
    <i class="fa  fa-check-circle"></i>
    <span class="success-msg"></span>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

</div>
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!!Session::get('fails')!!}
        </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.list_of_modules') !!}</h3>
       <!--  -->
    </div>
    <div class="card-body">
       

        <table class="table table-bordered" id="module-table">
            <thead>
                <tr>
                   <th>{!! Lang::get('lang.name') !!}</th>
                   <th>{!! Lang::get('lang.version') !!}</th>
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
$('body').tooltip({selector: '[data-toggle="tooltip"]'});
    $('#module-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('get.modules.index') !!}",
        "oLanguage": {
            "sLengthMenu": "_MENU_ Records per page",
            "sSearch"    : "Search: ",
            "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
        },
        columns:[
           {data:'name', name:'name'},
           {data:'version',name:'Version'},
           {data:'action',name:'action'},
        ],
        "fnDrawCallback": function( oSettings ) {
            $("#tag-table").css({"opacity": "1"});
            $('.loader').css('display', 'none');
            bindChangeStatusEvent();
        },
        "fnPreDrawCallback": function(oSettings, json) {
            $('.loader').css('display', 'block');
            $("#tag-table").css({"opacity":"0.3"});
            $('#blur-bg').css({"opacity": "0.7", "z-index": "99999"});
        },
        "columnDefs": [
            { "orderable": false, "targets": [1,2]},
            { "searchable": false, "targets": [1,2] },
        ]
    });

    function bindChangeStatusEvent() {
        $('.toggle_event_editing').click(function(){
            var current_module_name = $(this).children('.module_name');
            var current_module_status = $(this).children('.modules_settings_value');
            $.ajax({
                type: 'POST',
                url: '{{route("change.module.status")}}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    current_module_name: current_module_name.val(),
                    current_module_status: current_module_status.val()
                }
            }).done(function(result) {
                current_module_status.val( current_module_status.val() == 1 ? 0 : 1);
                $('.success-msg').html(result);
                $('.alert-success').css('display', 'block');
                setInterval(function() {
                    $('.alert-success').slideUp(3000);
                }, 500);
            });
            setInterval(function(){ location.reload(); }, 5000);
        });
    }
</script>
@endpush


