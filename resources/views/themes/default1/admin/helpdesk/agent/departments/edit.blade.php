@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.departments_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.departments_edit-page-description') !!}">
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

<!-- open a form -->
{!!Form::model($departments, ['url'=>'departments/'.$departments->id , 'method'=> 'PATCH','id'=>'Form'])!!}
        @if(Session::has('errors'))
        <?php //dd($errors); ?>
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>Alert!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
            @if($errors->first('name'))
            <li class="error-message-padding">{!! $errors->first('name', ':message') !!}</li>
            @endif
            @if($errors->first('account_status'))
            <li class="error-message-padding">{!! $errors->first('account_status', ':message') !!}</li>
            @endif
            @if($errors->first('sla'))
            <li class="error-message-padding">{!! $errors->first('sla', ':message') !!}</li>
            @endif
            @if($errors->first('manager'))
            <li class="error-message-padding">{!! $errors->first('manager', ':message') !!}</li>
            @endif
            @if($errors->first('outgoing_email'))
            <li class="error-message-padding">{!! $errors->first('outgoing_email', ':message') !!}</li>
            @endif
        </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.edit_department') !!}</h3>
        
        <div class="card-tools">
            
            <a href="{{ route('department.profile.show', $departments->id) }}" class="btn btn-tool"
                 title="{{Lang::get('lang.view')}}" data-toggle="tooltip">
                        <i class="fas fa-eye"> </i>
                    </a>
        </div>
    </div>
    <div class="card-body">

        <div class="row">
            <!-- name -->
            <div class="col-sm-4 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                {!! Form::label('name',Lang::get('lang.name')) !!} <span class="text-red"> *</span>
                {!! Form::text('name',null,['class' => 'form-control']) !!}
            </div>
            <!-- account status -->
            <div class="col-sm-4 form-group {{ $errors->has('account_status') ? 'has-error' : '' }}">
                {!! Form::label('type',Lang::get('lang.type')) !!}
                <a href="#" data-toggle="tooltip" title="{!! Lang::get('tooltip.department-type') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::radio('type','1',true) !!} {{Lang::get('lang.public')}}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::radio('type','0',null) !!} {{Lang::get('lang.private')}}
                    </div>
                </div>
            </div>
            <!-- sla -->
           <div class="col-sm-4 form-group {{ $errors->has('business_hour') ? 'has-error' : '' }}">
                {!! Form::label('manager',Lang::get('lang.business-hour')) !!}
                <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.business-hour-desc') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                {!!Form::select('business_hour',[null=>Lang::get('lang.select'),Lang::get('lang.business-hour')=>$business_hours],null,['class' => 'form-control select']) !!}
            </div>
            
        </div>
          <!-- manager -->
        <div class="row">
            <div class="col-sm-8 form-group {{ $errors->has('manager') ? 'has-error' : '' }}">
                {!! Form::label('manager',Lang::get('lang.manager')) !!}
                <a href="#" data-toggle="tooltip" title="{!! Lang::get('tooltip.department-manager') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                <?php
                 $managerId=App\Model\helpdesk\Agent\DepartmentAssignManager::where('department_id','=',$departments->id)->pluck('manager_id')->toArray();
                 $deptManager= (count($managerId)) ?  $managerId : null ;
                ?>
                {!!Form::select('manager[]',[Lang::get('lang.manager')=>$user->pluck('full_name_with_email_username','id')->toArray()],$deptManager,['class' => 'form-control select2','id'=>'department_manager','style'=>"width:100%",'multiple'=>'true']) !!}
            </div>
        </div>

        <!-- DepartmentStatusLink plugin -->
        @php
            \Event::dispatch('department-box-mounted', ['edit', $departments]);
        @endphp
        <!-- DepartmentStatusLink plugin -->

        <div class="card card-light">
            
                <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.outgoing_email_settings') !!}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- sla -->
            <div class="col-sm-6 form-group {{ $errors->has('outgoing_email') ? 'has-error' : '' }}">
                {!! Form::label('outgoing_email',Lang::get('lang.outgoing_email')) !!}
                <a href="#" data-toggle="tooltip" title="{!! Lang::get('tooltip.department-outgoin-mail') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                {!!Form::select('outgoing_email', ['' => Lang::get('lang.system_default'), Lang::get('lang.emails')=>$emails->pluck('email_name','id')->toArray()],null,['class' => 'form-control select']) !!}
            </div>
        </div>
        <div>
            <label>{!! Lang::get('lang.department_signature') !!}</label>
        </div>
        <div class="">
            {!! Form::textarea('department_sign',null,['class' => 'form-control','size' => '30x5','id'=>'ckeditor']) !!}
        </div>
        <div class="form-group">
            <input type="checkbox" name="sys_department" @if($sys_department->department == $departments->id) checked disabled @endif> {{ Lang::get('lang.make-default-department')}}&nbsp;<a href="#" data-toggle="tooltip" title="{!! Lang::get('tooltip.default-department') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
        </div>
    </div>
        </div>
    </div>


    <div class="card-footer">

            <button type="submit" id="submit" class="btn btn-primary" >
                <i class="fas fa-sync">&nbsp;&nbsp;</i>{!!Lang::get('lang.update')!!}</button>    
    </div>
    {!!Form::close()!!}
</div>

@stop
@push('scripts')
<script src="{{assetLink('js','select2')}}"></script>
 <script>
    $('#department_manager').select2({
        minimumInputLength: 1,
        ajax: {
            url: '{{route("department.search.manager")}}',
            dataType: 'json',
            data: function(params) {
                var dept_id=<?php echo($departments->id) ?>;
                return {
                    q: $.trim(params.term),
                    j: dept_id,
                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        image:value.profile_pic,
                        text:value.first_name+" "+value.last_name,
                        id: value.id,
                        email:value.text
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

        return $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' onerror="this.src='+onerrorImage+'" width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
    }
</script>

@endpush