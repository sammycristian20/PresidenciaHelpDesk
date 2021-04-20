@extends('themes.default1.admin.layout.admin')

@section('Tickets')
active
@stop

@section('tickets-bar')
active
@stop

@section('location')
class="active"
@stop

@section('custom-css')
     <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.location') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop

<!-- content -->
@section('content')


@if (Session::has('message'))
<div class="alert alert-success">
    
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span> <i class="fas fa-check-circle"></i> {{ Session::get('message') }}</span>
</div>
@endif
@if (Session::has('fails'))
<div class="alert alert-danger">
    <i class="fas fa-ban"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span>{{ Session::get('fails') }}</span>
</div>
@endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.list_of_location') !!}</h3>
        <div class="card-tools">
        <a href="{{route('helpdesk.location.create')}}" class="btn btn-tool" title="{{Lang::get('lang.create_location')}}" data-toggle="tooltip">
            <span class="glyphicon glyphicon-plus"></span></a>
        </div>
    </div>
    <div class="card-body">




            <table class="table table-bordered dataTable" id="myTable">
                <thead>
                    <tr>
                        <th width="100px">{{Lang::get('lang.name')}}</th>
                        <!-- <th width="100px">{{Lang::get('lang.email')}}</th> -->
                        <th width="100px">{{Lang::get('lang.phone')}}</th>
                        <th width="100px">{{Lang::get('lang.address')}}</th>
                        <th width="100px">{{Lang::get('lang.action')}}</th>
                    </tr>
                </thead>

                <!-- Foreach @var$topics as @var topic -->
                <tbody>
                    @foreach($locations as $location)
                    @if($location->is_default == 1)

                    <?php
                    $disable = 'disabled';
                    ?>
                    @else
                    <?php
                    $disable = '';
                    ?>
                    @endif

                    <tr style="padding-bottom:-30px">
                         @if($location->is_default == 1)
                        <td title="{{$location->title}}">{{str_limit($location->title, 15)}} (Default)</td>
                        @else
                        <td title="{{$location->title}}">{{str_limit($location->title, 15)}}</td>
                        @endif
                        <td>{{$location->phone}}</td>
                        <td title="{{$location->address}}">{{str_limit($location->address, 15)}}</td>
                        <td>

                            {!! Form::open(['route'=>['helpdesk.location.delete', $location->id],'method'=>'post']) !!}
                            <?php
                            $url = url('helpdesk/location/deletepopup/' . $location->id);
                            $confirmation = deletePopUp($location->id, $url, "Delete", 'btn btn-primary btn-xs text-white ' . $disable);
                            ?>

                            <a href="{{route('helpdesk.location.edit',$location->id)}}" class="btn btn-primary btn-xs text-white"><i class="fas fa-edit" style="color:white;"></i>&nbsp;{!! Lang::get('lang.edit') !!}</a>&nbsp;{!! $confirmation!!}
                            <!-- To pop up a confirm Message -->
                            {!! Form::close() !!}
                        </td>

                        @endforeach
                    </tr>
                </tbody>
                <!-- Set a link to Create Page -->

            </table>


<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

        <script type="text/javascript">
$(function() {
    $("#myTable").dataTable({
        "columnDefs": [
            { "orderable": false, "targets": [3]},
            { "searchable": false, "targets": [3] },
        ]
    });

});

</script>
</div>
    <!-- /.box -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->
</div>
</section>
<!-- /.content -->
</div>

@stop