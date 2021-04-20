@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.priority_create-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.priority_create-page-description') !!}">


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

@section('HeadInclude')
<link href="{{assetLink('css','bootstrap-colorpicker')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>
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
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<!-- open a form -->

 @if(Session::has('errors'))
            <?php //dd($errors); ?>
            <div class="alert alert-danger alert-dismissable">
                <i class="fas fa-ban"></i>
                <b>Alert!</b>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <br/>
                @if($errors->first('priority'))
                <li class="error-message-padding">{!! $errors->first('priority', ':message') !!}</li>
                @endif
                @if($errors->first('priority_desc'))
                <li class="error-message-padding">{!! $errors->first('priority_desc', ':message') !!}</li>
                @endif
                @if($errors->first('priority_color'))
                <li class="error-message-padding">{!! $errors->first('priority_color', ':message') !!}</li>
                @endif
                @if($errors->first('status'))
                <li class="error-message-padding">{!! $errors->first('status', ':message') !!}</li>
                @endif
            </div>
            @endif
            
<form action="{!!URL::route('priority.create1')!!}" method="post" role="form" id="Form">
    {{ csrf_field() }}
    <div class="card card-light">
        <div class="card-header">
            <h3 class="card-title">{{Lang::get('lang.create_new_ticket_priority')}}</h3>
        </div>
        <div class="card-body">
           
            <!-- <table class="table table-hover" style="overflow:hidden;"> -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('priority') ? 'has-error' : '' }}">
                        {!! Form::label('priority',Lang::get('lang.priority')) !!} <span class="text-red"> *</span>
                        {!! Form::text('priority',null,['class' => 'form-control']) !!}
                        <!--<input type="text" class="form-control" name="priority" value="" >-->
                    </div>
                </div>
                <!-- Grace Period text form Required -->
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('priority_desc') ? 'has-error' : '' }}">
                        {!! Form::label('priority_desc',Lang::get('lang.priority_desc')) !!}<span class="text-red"> *</span>
                        {!! Form::text('priority_desc',null,['class' => 'form-control']) !!}
                        <!-- <input type="text" name="priority_desc" class="form-control">-->
                    </div>
                </div> 
            </div>
            <!-- Priority Color -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('priority_color') ? 'has-error' : '' }}">
                        {!! Form::label('priority_color',Lang::get('lang.priority_color')) !!}<span class="text-red"> *</span>                      
                        {!! Form::text('priority_color',null,['class'=>'form-control my-colorpicker1 colorpicker-element','id'=>'colorpicker'])!!}
                      
                    </div>
                </div>
                <!-- status radio: required: Active|Dissable -->
                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                        {!! Form::label('status',Lang::get('lang.status')) !!}&nbsp;<span class="text-red"> *</span>

                        {!! Form::radio('status','1',true) !!}&nbsp;&nbsp;{{Lang::get('lang.active')}}&nbsp;&nbsp;
                        {!! Form::radio('status','0') !!}&nbsp;&nbsp;{{Lang::get('lang.inactive')}}

                    </div>      
                </div>

                <!-- Show radio: required: public|private -->
                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('ispublic') ? 'has-error' : '' }}">
                        {!! Form::label('ispublic',Lang::get('lang.visibility')) !!}&nbsp;<span class="text-red"> *</span>
                        {!! Form::radio('ispublic','1',true) !!}&nbsp;&nbsp; {{Lang::get('lang.public')}}&nbsp;&nbsp;
                        {!! Form::radio('ispublic','0') !!}&nbsp;&nbsp; {{Lang::get('lang.private')}}

                    </div>       
                </div>
            </div>  
            <!-- Admin Note  : Textarea :  -->
            <div class="row hide">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('admin_note',Lang::get('lang.admin_notes')) !!}
                        {!! Form::textarea('admin_note',null,['class' => 'form-control','size' => '30x5','id'=>'ckeditor']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <input type="checkbox" name="default_priority" value="1"> {{ Lang::get('lang.make-default-priority')}}
            </div>
        </div>
        <div class="card-footer">
             
            <button type="submit" id="submit" class="btn btn-primary"><i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}</button>

        </div>
    </div>
    <!-- Colorpicker -->
        <script src="{{assetLink('js','bootstrap-colorpicker')}}" ></script>
    <script>
        $(function () {

            $("#colorpicker").colorpicker({format:'hex'}).colorpicker().on('changeColor',
                    function (ev) {
                        $('#submit').removeAttr('disabled');
                    });
            ;

        });
    </script>

    <!-- close form -->
    {!! Form::close() !!}
    @stop