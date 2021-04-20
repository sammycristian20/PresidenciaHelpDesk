@extends('themes.default1.admin.layout.admin')
<style type="text/css">
    .list-inline li:before{content:'\2022'; margin:0 10px;}
</style>
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
 @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-ban"></i>  
                    <strong>{!! Lang::get('lang.alert') !!} !</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissable">
                    <i class="fas fa-check-circle"></i>  
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('success')}}
                </div>
                @endif
                <!-- fail lang -->
                @if(Session::has('fails'))
                <div class="alert alert-danger alert-dismissable">
                    <i class="fas fa-ban"></i>
                    <b>{{Lang::get('lang.alert')}}!</b>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('fails')}}
                </div>
                @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('SMS::lang.edit')}}&nbsp;{{$type}}&nbsp;{{Lang::get('SMS::lang.template')}}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">
        {!!Form::open(['route' => 'post-edit-template', 'method' => 'POST'])!!}
            <div class="col-md-12">
               
                <div class="row">
                    <div class="col-md-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <!-- first name -->
                        <p class="lead">{!! Lang::get('lang.'.$description) !!}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group {{ $errors->has('message') ? 'has-error' : '' }}">
                        <div class="card card-light">
                            <div class="card-header">
                                <h3 class="card-title">{{Lang::get('lang.list-of-available-shot-codes')}}</h3>
                                <div class="card-tools">
                                    <button class="btn btn-tool" data-widget="collapse" data-toggle="tooltip" title="{{Lang::get('lang.collapse-list')}}"><i class="fa fa-minus"></i></button>
                                </div><!-- /.box-tools -->
                            </div><!-- /.box-header -->
                            <div class="card-body">
                                <div class="alert alert-info alert-dismissable">
                                    <i class="fas  fa-info-circle"></i>
                                    <span>{{Lang::get('lang.template-short-code-tips')}}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                </div>
                               <div>
                                    <ul class="list-inline">
                                        @foreach ($var as $key => $value)
                                        <li  style="width: 24%"><span data-toggle="tooltip" title="{!! $value !!}" data-placement="right">{{$key}}</span></li>
                                        @endforeach   
                                    </ul>
                                </div>
                                <div class="col-md-12">
                                    {!! Lang::get('lang.template_shortcode_note_message') !!}
                                </div>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div>
                </div>
                <div class="row" id='editor'>
                    <div class="col-md-12 form-group {{ $errors->has('message') ? 'has-error' : '' }}">
                        {!! Form::label('message',Lang::get('SMS::lang.content'),['class'=>'required']) !!}<span style="color:red;">*</span>
                        {!! Form::textarea('message', $body,['class'=>'form-control','id'=>'ckeditor', 'placeholder' => Lang::get('SMS::lang.content-placeholder')]) !!}
                        {!! Form::hidden('event_type', $event_type)!!}
                        {!! Form::hidden('id', $id)!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        {!! Form::submit(Lang::get('SMS::lang.update'),['class'=>'form-group btn btn-primary'])!!}
    </div>
</div>
{!! Form::close() !!}
<script>
$( document ).ready(function(){
}); 
</script>
@stop

<!-- /content -->
@section('no-toolbar')
<script type="text/javascript">

$('.btn-box-tool').on('click', function(){
    if ($(this).children().attr('class') == 'fas fa-minus') {
        $(this).tooltip('hide').attr('data-original-title', "{{Lang::get('lang.expand-list')}}").tooltip('fixTitle');
    } else {
        $(this).tooltip('hide').attr('data-original-title', "{{Lang::get('lang.collapse-list')}}").tooltip('fixTitle');
    }
});
</script>
@stop