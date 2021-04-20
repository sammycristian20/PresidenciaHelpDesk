@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.ratings_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.ratings_lists-page-description') !!}">


@stop

@section('custom-css')
 <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

@section('Tickets')
active
@stop

@section('ratings')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.ratings') !!}</h1>
@stop

@section('header')
@stop

@section('content')
@if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <span>{{Session::get('success')}}</span>                
        </div>
@endif

@if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <span>{{Session::get('fails')}}</span>                
        </div>
@endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.list_of_current_ratings') !!}</h3>
        <div class="card-tools">
             <a href="{{route('rating.create')}}" class="btn btn-tool" title="{{Lang::get('lang.create_rating')}}" data-toggle="tooltip"><span class="glyphicon glyphicon-plus"></span>

             </a>

        </div><!-- /.box-header -->
    </div>
    <div class="card-body">
       
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{!! Lang::get('lang.name') !!}</th>
                    <th>{!! Lang::get('lang.display_order') !!}</th>
                    <th>{!! Lang::get('lang.rating_area') !!}</th>
                    <th>{!! Lang::get('lang.action') !!}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ratings as $rating)
                <tr>
                    <td>{!! $rating->name !!}</td>                                                    
                    <td>{!! $rating->display_order !!}</td>
                    <td>{!! $rating->rating_area !!}</td>
                    <td>
                        <a href="{{url('editratings/'.$rating->id)}}" class="btn btn-primary btn-xs "><i class="fas fa-edit" style="color:white;">&nbsp;</i>Edit</a>&nbsp;
                        
                        <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#delete-{{$rating->id}}"><i class="fas fa-trash" style="color:white;"></i>&nbsp;Delete</button>
                        <div class="modal fade" id="delete-{{$rating->id}}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        
                                        <h4 class="modal-title">{!! Lang::get('lang.delete') !!}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <span>{!! Lang::get('lang.are_you_sure') !!} </span>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-times" aria-hidden="true">&nbsp;</i>{!! Lang::get('lang.close') !!}</button>
                                             <?php
                                                $url=url('deleter/'.$rating->id);
                                              ?>
                                             <a href="{!! $url !!}" class="btn btn-danger"><i class="fas fa-trash" aria-hidden="true">&nbsp;</i>{!! Lang::get('lang.delete') !!}</a>
                                    </div>
                                </div> 
                            </div>
                        </div> 
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div>
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

<!-- page script -->
<script type="text/javascript">
$(function() {
    $("#example1").dataTable();
    // $('#example2').dataTable({
    //     "bPaginate": true,
    //     "bLengthChange": false,
    //     "bFilter": false,
    //     "bSort": true,
    //     "bInfo": true,
    //     "bAutoWidth": false
    // });
});

</script>

<script type="text/javascript">
<?php if (count($errors) > 0) { ?>
        $('#create').modal('show');
<?php } ?>
</script>  
@stop
@section('footer')


@stop