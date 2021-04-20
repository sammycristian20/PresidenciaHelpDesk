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
<meta name="title" content="{!! Lang::get('lang.security_settings-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.security_settings-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('security')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.settings') !!}</h1>
@stop

@section('header')
@stop

@section('content')

 @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fas fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! Session::get('success') !!}
        </div>
        @endif
        @if(Session::has('failed'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>{!! Lang::get('lang/alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>{{Session::get('failed')}}</p>
        </div>
        @endif
        @if(Session::has('errors'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
            @if($errors->first('lockout_message'))
            <li class="error-message-padding">{!! $errors->first('lockout_message', ':message') !!}</li>
            @endif
            @if($errors->first('backlist_threshold'))
            <li class="error-message-padding">{!! $errors->first('backlist_threshold', ':message') !!}</li>
            @endif
            @if($errors->first('lockout_period'))
            <li class="error-message-padding">{!! $errors->first('lockout_period', ':message') !!}</li>
            @endif
        </div>
        @endif
        
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.security_settings') !!}</h3>
    </div><!-- /.box-header -->
    <div class="card-body">
       
        {!! Form::model($security,['route'=>['securitys.update', $security->id],'id'=>'Form','method'=>'PATCH','files' => true]) !!}
        <div class="form-group {{ $errors->has('lockout_message') ? 'has-error' : '' }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="title">Lockout Message: <span class="text-red"> *</span></label>
                </div>
                <div  class="col-md-9">
                    <div class="callout callout-default bg-light" style="font-style: oblique;">{!! Lang::get('lang.security_msg1') !!}</div>
                    {!! Form::textarea('lockout_message',null,['class'=>'form-control'])!!}
                </div>
            </div>
        </div>
        <div class="form-group {{ $errors->has('backlist_threshold') ? 'has-error' : '' }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="title">{!! Lang::get('lang.max_attempt') !!}: <span class="text-red"> *</span></label>
                </div>
                <div class="col-md-9">
                    <div class="callout callout-default bg-light" style="font-style: oblique;">{!! Lang::get('lang.security_msg2') !!}</div>

                       <span> {!! Form::input('number', 'backlist_threshold', null, array('class' => 'form-control','id' => 'test' ,'min' => '1')) !!}</span>



                    <!-- <span>{!! Form::text('backlist_threshold',null,['class'=>'form-control'])!!} {!! Lang::get('lang.lockouts') !!}</span> -->
                </div>
            </div>
        </div>

        <div class="form-group {{ $errors->has('lockout_period') ? 'has-error' : '' }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="title">Lockout Period: <span class="text-red"> *</span></label>
                </div>
                <div class="col-md-8">
                    <div class="callout callout-default bg-light" style="font-style: oblique;">{!! Lang::get('lang.security_msg3') !!}</div>

                     <span> {!! Form::input('number', 'lockout_period', null, array('class' => 'form-control','id' => 'test' ,'min' => '1')) !!}</span>


                    <!-- <span> {!! Form::text('lockout_period',null,['class'=>'form-control'])!!} {!! Lang::get('lang.minutes') !!}</span> -->
                </div>
            </div>
        </div>
    </div><!-- /.box-body -->
    <div class="card-footer">
       <!--  <button type="submit" class="btn btn-primary">{!! lang::get('lang.save') !!}</button> -->
         <!-- {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!} -->
             <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fas fa-circle-notch fa-spin'>&nbsp;</i> {!! Lang::get('lang.saving') !!}"><i class="fas fa-save">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}
             </button>

    </div>
    {!! Form::close() !!}
</div>
<script>

$("#test").keyup(function() {
    var val = $("#test").val();
    if(parseInt(val) < 0 || isNaN(val)) {
    alert("please enter valid values");
        $("#test").val("");
        $("#test").focus();
    }
});
</script>

@stop
