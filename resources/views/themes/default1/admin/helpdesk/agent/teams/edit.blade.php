@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.teams_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.teams_edit-page-description') !!}">
@stop

@section('custom-css')
<!--select 2-->
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>

@stop

@section('Staffs')
active
@stop

@section('staffs-bar')
active
@stop

@section('teams')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.teams')}}</h1>
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
<!-- open a form -->
{!!Form::model($teams, ['url'=>'teams/'.$teams->id , 'method'=> 'PATCH','id'=>'Form'])!!}
 @if(Session::has('errors'))
            <div class="alert alert-danger alert-dismissable">
                <i class="fa fa-ban"></i>
                <b>Alert!</b>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <br/>
                @if($errors->first('name'))
                <li class="error-message-padding">{!! $errors->first('name', ':message') !!}</li>
                @endif
                @if($errors->first('team_lead'))
                <li class="error-message-padding">{!! $errors->first('team_lead', ':message') !!}</li>
                @endif
                @if($errors->first('status'))
                <li class="error-message-padding">{!! $errors->first('status', ':message') !!}</li>
                @endif
            </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.edit_a_team') !!}</h3>   
       <div class="card-tools">
        <a href="{{route('teams.profile.show', $teams->id)}}" class="btn btn-tool" title="{{Lang::get('lang.view')}}"
            data-toggle="tooltip">
                        <i class="fas fa-eye"> </i>
                    </a>
                </div>
    </div>
    <div class="card-body">
       
        <div class="row">
            <!-- name -->
            <div class="col-sm-6 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                {!! Form::label('name',Lang::get('lang.name')) !!} <span class="text-red"> *</span>
                {!! Form::text('name',null,['class' => 'form-control']) !!}
            </div>
            <!-- team lead -->
            <div class="col-sm-6 form-group {{ $errors->has('team_lead') ? 'has-error' : '' }}">
                {!! Form::label('team_lead',Lang::get('lang.team_lead')) !!}
                <a href="#" data-toggle="tooltip" title="{!! Lang::get('tooltip.team-lead') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                {!!Form::select('team_lead',[Lang::get('lang.teamlead')=>$user->pluck('full_name_with_email_username','id')->toArray()],$teams->team_lead,['class' => 'form-control select2','id'=>'assign-teamlead','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}
            </div>
        </div>
        <div class="row">
      
           
        <!-- status -->
        <div class="col-sm-6 form-group {{ $errors->has('status') ? 'has-error' : '' }}">
            {!! Form::label('status',Lang::get('lang.status')) !!}
            <a href="#" data-toggle="tooltip" title="{!! Lang::get('tooltip.team-status') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::radio('status',1,true) !!} {{Lang::get('lang.active')}}
                </div>
                <div class="col-sm-3">
                    {!! Form::radio('status',0,null) !!} {{Lang::get('lang.inactive')}}
                </div>
            </div>
        </div>
    </div>
        <!-- admin notes -->
        <div class="form-group">
            {!! Form::label('admin_notes',Lang::get('lang.admin_notes')) !!}
            <a href="#" data-toggle="tooltip" title="{!! Lang::get('tooltip.team-admin-notes') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
            {!! Form::textarea('admin_notes',null,['class' => 'form-control','size' => '30x5','id'=>'ckeditor']) !!}
        </div>
    </div>
    <div class="card-footer">
          <button type="submit" id="submit" class="btn btn-primary"><i class="fas fa-sync"> </i> {!!Lang::get('lang.update')!!}
          </button>    
    </div>
    {!!Form::close()!!}
</div>
<script src="{{assetLink('js','select2')}}"></script>
<script src="{{assetLink('js','intlTelInput')}}"></script>
        <script type="text/javascript">
            
    $('#primary_department').select2({
        placeholder: "{{Lang::get('lang.Choose_departments...')}}",
        minimumInputLength: 1,
        ajax: {
            url: '{{route("agent.dept.search")}}',
            dataType: 'json',
            beforeSend: function(){
                $('.loader').css('display', 'block');
            },
            complete: function() {
                $('.loader').css('display', 'none');
            },
            data: function (params) {
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });
        </script>


<!-- For auto search team lead  -->
<script>
    $('#assign-teamlead').select2({
       
        maximumSelectionLength: 1,
        minimumInputLength: 1,
        ajax: {

            url: '{{url("ticket/form/requester/")}}',
            dataType: 'json',
            data: function(params) {
                //console.log(params);
                return {
                    term: $.trim(params.term),
                    team:'{{$teams->id}}'

                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        image:value.profile_pic,
                        text:value.first_name+" "+value.last_name,
                        id:value.id,
                        email:value.email,
                    }
                })
               }
            },
            cache: true
        },
         templateResult: formatState,
    });
   function formatState (state) { 
       

       var onerrorImage = "'<?=assetLink('image','contacthead') ?>'";


       var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' onerror="this.src='+onerrorImage+'" width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
  }
</script>
@stop