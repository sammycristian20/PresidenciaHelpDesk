@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.help_topics_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.help_topics_lists-page-description') !!}">


@stop

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('help')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.help_topics')}}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">

</ol>
@stop
@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
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
            <b>{!! Lang::get('lang.alert') !!} !</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! Session::get('fails') !!}
        </div>
        @endif

<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('lang.list_of_help_topics')}}</h3>
        <div class="card-tools">
            <a href="{{route('helptopic.create')}}" class="btn btn-tool" title="{{Lang::get('lang.create_help_topic')}}"
                data-toggle="tooltip">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </div>
    <div class="card-body">
    
        <table class="table table-bordered dataTable" id="myTable">
        <thead>
            <tr>
                <th width="100px">{{Lang::get('lang.topic')}}</th>
                <th width="100px">{{Lang::get('lang.status')}}</th>
                <th width="100px">{{Lang::get('lang.type')}}</th>
                <th width="100px">{{Lang::get('lang.department')}}</th>
                <th width="100px">{{Lang::get('lang.last_updated')}}</th>
                <th width="100px">{{Lang::get('lang.action')}}</th>
            </tr>
            </thead>
            <?php
            $default_helptopic = App\Model\helpdesk\Settings\Ticket::where('id', '=', '1')->first();
            $default_helptopic = $default_helptopic->help_topic;
            ?>
            <!-- Foreach @var$topics as @var topic -->
             <tbody>
            @foreach($topics as $topic)

            <tr style="padding-bottom:-30px">
                <!-- topic Name with Link to Edit page along Id -->
                <td><a href="{{route('helptopic.edit',$topic->id)}}">{!! $topic->topic !!}
                        @if($topic->id == $default_helptopic)
                        ( Default )
                        <?php
                        $disable = 'disabled';
                        ?>
                        @else
                        <?php
                        $disable = '';
                        ?>
                        @endif
                    </a></td>

                <!-- topic Status : if status==1 active -->
                <td>
                    @if($topic->status=='1')
                    <span class="btn btn-xs btn-default" style="pointer-events:none;color:green">{!! Lang::get('lang.active') !!}</span>
                    <!-- <span style="color:green">{!! Lang::get('lang.active') !!}</span> -->
                    @else
                    <span class="btn btn-xs btn-default" style="pointer-events:none;color:red">{!! Lang::get('lang.inactive') !!}</span>
                    <!-- <span style="color:red">{!! Lang::get('lang.inactive') !!}</span> -->
                    @endif
                </td>

                <!-- Type -->

                <td>
                    @if($topic->type=='1')
                    <span class="btn btn-xs btn-default" style="color:green;pointer-events:none;">{!! Lang::get('lang.public') !!}</span>
                    @else
                    <span class="btn btn-xs btn-default" style="color:red;pointer-events:none;">{!! Lang::get('lang.private') !!}</span>
                    @endif
                </td>
                <!-- Priority -->
                 <!-- Department -->
                @if($topic->department != null)
                <?php
                $dept = App\Model\helpdesk\Agent\Department::where('id', '=', $topic->department)->first();
                $dept = $dept->name;
                ?>
                @elseif($topic->department == null)
                <?php $dept = ""; ?>
                @endif
                <td> {!! $dept !!} </td>
                <!-- Last Updated -->
                <td> {!! faveoDate($topic->updated_at) !!} </td>
                <!-- Deleting Fields -->
                <td>
                    <?php
                      $sys_help_topic = \DB::table('settings_ticket')
                                ->select('help_topic')
                                ->where('id', '=', 1)->first();
                      ?>
            @if($sys_help_topic->help_topic == $topic->id)
                   <a href="{{route('helptopic.edit',$topic->id)}}" class="btn btn-primary btn-xs "><i class="fas fa-edit" style="color:white;"></i>
                         {!! Lang::get('lang.edit') !!}</a>
                   <button class='btn btn-primary btn-primary btn-xs' disabled='disabled' ><i class='fas fa-trash' style='color:white;'></i> {!! Lang::get('lang.delete') !!} </button>
                </div>
                   {!! Form::close() !!}
            @else
                   {!! Form::open(['route'=>['helptopic.destroy', $topic->id],'method'=>'DELETE']) !!}
                   <?php
                        $url = url('delete/helptopicpopup/'.$topic->id);
                        $confirmation = deletePopUp($topic->id, $url, "Delete",'btn btn-primary btn-xs');
                    ?> 
                      <a href="{{route('helptopic.edit',$topic->id)}}" class="btn btn-primary btn-xs "><i class="fas fa-edit" style="color:white;"></i> {!! Lang::get('lang.edit') !!}</a>&nbsp;{!!$confirmation!!}
                 </div>
                  {!! Form::close() !!}
            @endif
              </td>
                @endforeach
            </tr>
            </tbody>
            <!-- Set a link to Create Page -->

        </table>
    </div>
</div>
    



<!-- page script -->
<script type="text/javascript">
$(function() {
    $("#myTable").dataTable({
        "columnDefs": [
                { "orderable": false, "targets": [5]},
                { "searchable": false, "targets": [5] },
        ]
    });
   
});

</script>
@stop
@push('scripts')
 <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(function() {
  $(".alert-success").delay(15000);
                  
});
</script>
@endpush