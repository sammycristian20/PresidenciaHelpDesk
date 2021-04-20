@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.departments_list-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.departments_list-page-description') !!}">


@stop

@section('custom-css')
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

@section('Staffs')
active
@stop

@section('staffs-bar')
active
@stop

@section('departments')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.departments')}}</h1>
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

 <!-- check whether success or not -->
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
            <b>{!! Lang::get('lang.fails') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! Session::get('fails') !!}
        </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.list_of_departments') !!}</h3>
        <div class="card-tools">
            
            <a href="{{route('departments.create')}}" class="btn btn-tool" data-toggle="tooltip" 
                title="{{Lang::get('lang.create_a_department')}}">
                <span class="glyphicon glyphicon-plus"></span>
            </a>        
        </div>
    </div>
    <div class="card-body">
       
        <!-- table -->
           <table id="chumper" class="table table-bordered dataTable no-footer">
                <thead>
                <tr>
                    <th>{!! trans('lang.name') !!}</th>
                    <th>{!! trans('lang.type') !!}</th>
                    <th>{!! trans('lang.department_manager') !!}</th>
                    <th>{!! trans('lang.action') !!}</th>
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

$(document).ready(function() {
  $(".alert-success").delay(15000);
                  
});
    jQuery(document).ready(function () {
        oTable = myFunction();
    });
    function myFunction()
    {
        return jQuery('#chumper').dataTable({
            "sPaginationType": "full_numbers",
            "bProcessing": true,
            "bServerSide": true,
            "oLanguage": {
                    "sLengthMenu": "_MENU_ Records per page",
                    "sSearch"    : "Search: ",
                    "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink("image","gifloader3") !!}">'
                },
                columns:[
                    {data:'name', name:'name', orderable: true},
                    {data:'type', name:'type', orderable: true},
                    {data:'user_name', name:'user_name', orderable: true},
                    {data:'action', name:'action'},
                ],
                "fnDrawCallback": function( oSettings ) {
                    $('.loader').css('display', 'none');
                    $(".card-body").css({"opacity": "1"});
                    $('#blur-bg').css({"opacity": "1", "z-index": "99999"});
                },
                "fnPreDrawCallback": function(oSettings, json) {
                    $('.loader').css('display', 'block');
                    $(".card-body").css({"opacity":"0.3"});
                },
            "ajax": {
                url: "{{route('get-department-table-data')}}",
            },
            "columnDefs": [
                { "orderable": false, "targets": [3]},
                { "searchable": false, "targets": [3] },
            ]
        });
    }
</script>
@endpush