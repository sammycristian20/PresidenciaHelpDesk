@extends('themes.default1.admin.layout.admin')

@section('Plugins')
active
@stop

@section('settings-bar')
active
@stop

@section('plugin')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.plugins') !!}</h1>
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
@if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fas fa-check-circle"> </i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!}! </b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
        @if(Session::has('errors'))
            <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!}! </b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('CustomJs::lang.custom-js')}}</h3>
        
    </div>
    <!-- /.box-header -->
    <div class="card-body ">
        
        {!! Form::model($customjs, ['url' => route('customjs.edit', $customjs->id), 'method' => 'PATCH']) !!}
        <div class="row">
                <div class="col-sm-4 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    {!! Form::label('name',Lang::get('lang.name')) !!} <span class="text-red"> *</span>
                    {!! Form::text('name',null,['class' => 'form-control', 'required' => true]) !!}
                    {!! $errors->first('name', '<spam class="help-block">:message</spam>') !!}
               </div>
               
               <div class="col-sm-4 form-group {{ $errors->has('fired_at') ? 'has-error' : '' }}">
                    {!! Form::label('fired_at',Lang::get('CustomJs::lang.fired_at')) !!} <span class="text-red"> *</span>
                    {!! Form::select('fired_at', ['adminlayout' => Lang::get('CustomJs::lang.adminlayout'), 'agentlayout' => Lang::get('CustomJs::lang.agentlayout'), 'clientlayout' => Lang::get('CustomJs::lang.clientlayout')],$customjs->fired_at,['class'=>"form-control" , 'style'=>'width: 100%;', 'required' => true,  'id' => "fired"]) !!}
                    {!! $errors->first('fired_at', '<spam class="help-block">:message</spam>') !!}
               </div>
                <div class="col-sm-4 form-group {{ $errors->has('parameter') ? 'has-error' : '' }}">
                    {!! Form::label('parameter',Lang::get('CustomJs::lang.parameter')) !!}<a href="#" data-toggle="tooltip" title="{!! Lang::get('CustomJs::lang.select_url_description') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    {!! Form::select('parameter',[null => Lang::get('CustomJs::lang.select-url')], $customjs->parameter, ['class' => 'form-control', 'id' => 'url']) !!}
                    {!! $errors->first('parameter', '<spam class="help-block">:message</spam>') !!}
               </div>
            <div class="col-md-12 form-group {{ $errors->has('fire') ? 'has-error' : '' }}">
                <div class="col-sm-6">
                    {!! Form::label('fire',Lang::get('CustomJs::lang.fire-javascript')) !!}&nbsp;
                    {!! Form::radio('fire',1,true) !!} {{Lang::get('lang.active')}}
                    {!! Form::radio('fire',0) !!} {{Lang::get('lang.inactive')}}
                    {!! $errors->first('fire', '<spam class="help-block">:message</spam>') !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-sm-12 form-group {{ $errors->has('script') ? 'has-error' : '' }}">
                    {!! Form::label('script', Lang::get('CustomJs::lang.script')) !!}
                    {!! $errors->first('script', '<spam class="help-block">:message</spam>') !!}
                    {!! Form::textarea('script',null,['class' => 'form-control','size' => '30x5','id'=>'ckeditor-custom', 'required' => true]) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sync"></i> Update
        </button>
        {!! Form::close() !!}
    </div>
</div>
@include('CustomJs::custonjs-crud-js')
@stop
