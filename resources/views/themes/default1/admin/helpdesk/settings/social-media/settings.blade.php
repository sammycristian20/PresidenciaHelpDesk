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
<meta name="title" content="{!! Lang::get('lang.social-login-settings-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.social-login-settings-page-description') !!}">

@stop
@section('custom-css')
<link href="{{assetLink('css','faveo-css')}}" rel="stylesheet" type="text/css" />
@stop
@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('social-login')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h3>{!! Lang::get('lang.social-login') !!}</h3>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">

</ol>
@stop
@section('content')
{!! Form::open(['url' => 'social/media/'.$provider, 'method' => 'POST','id'=>'Form']) !!}
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <!-- check whether success or not -->
        @if(Session::has('warn'))
        <div class="alert alert-warning alert-dismissable">
            <i class="fas fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!!Session::get('warn')!!}
        </div>
        @endif
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fas fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!!Session::get('success')!!}
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!!Session::get('fails')!!}
        </div>
        @endif

<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{ucfirst($provider)}} {{Lang::get('lang.settings')}}</h3>
    </div>

    <div class="card-body">
    
        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
                    {!! Form::label('client_id',Lang::get('lang.client_id')) !!}
                    {!! $errors->first('client_id', '<p class="text-red">:message</p>') !!}
                    {!! Form::text('client_id',$social->getvalueByKey($provider,'client_id'),['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('client_secret') ? 'has-error' : '' }}">
                    {!! Form::label('client_secret',Lang::get('lang.client_secret')) !!}
                    {!! $errors->first('client_secret', '<p class="text-red">:message</p>') !!}
                    {!! Form::text('client_secret',$social->getvalueByKey($provider,'client_secret'),['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('redirect') ? 'has-error' : '' }}">
                    {!! Form::label('redirect',Lang::get('lang.redirect')) !!}
                    {!! $errors->first('redirect', '<p class="text-red">:message</p>') !!}
                    {!! Form::text('redirect',$social->getvalueByKey($provider,'redirect'),['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::label('status',Lang::get('lang.status')) !!}
                            {!! $errors->first('status', '<p class="text-red">:message</p>') !!}
                            
                        </div>
                        <div class="col-md-6">
                            <p>{!! Form::radio('status',1,$social->checkActive($provider))!!} Active</p>
                        </div>
                        <div class="col-md-6">
                            <p>{!! Form::radio('status',0,$social->checkInactive($provider)) !!} Inactive</p>
                        </div>
                        <div class="col-md-12">
                            <i>Activate login via {{ucfirst($provider)}}</i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <div class="card-footer">
       <!-- {!! Form::submit(Lang::get('lang.save'),['class'=>'btn btn-primary'])!!} -->
      <!--  {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
 -->
        <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fa fa-circle-notch fa-spin'>&nbsp;</i>{!! Lang::get('lang.saving') !!}"><i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
    
      </div>
    </div>
</div>
{!! Form::close() !!}
@stop
