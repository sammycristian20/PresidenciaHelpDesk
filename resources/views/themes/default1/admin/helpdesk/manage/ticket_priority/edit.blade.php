@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.priority_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.priority_edit-page-description') !!}">


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
                @if($errors->first('ispublic'))
                <li class="error-message-padding">{!! $errors->first('ispublic', ':message') !!}</li>
                @endif
            </div>
            @endif
            
<form action="{!!URL::route('priority.edit1',$tk_priority->priority_id)!!}" method="post" role="form" id="Form">
    {{ csrf_field() }}
    <input type="hidden" name="priority_id" value="{{$tk_priority->priority_id}}">
    <div class="card card-light">
        <div class="card-header">
            <h3 class="card-title">{{Lang::get('lang.edit_priority')}}</h3>
        </div>
        <div class="card-body">
          
            <!-- Name text form Required -->
            <div>
            <!-- <table class="table table-hover" style="overflow:hidden;"> -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('priority') ? 'has-error' : '' }}">
                            {!! Form::label('priority',Lang::get('lang.priority')) !!}<span class="text-red"> *</span>
                            <input type="text" class="form-control" name="priority" value="{{ ($tk_priority->priority) }}" >
                        </div>
                    </div>
                    <!-- Grace Period text form Required -->
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('priority_desc') ? 'has-error' : '' }}">
                            {!! Form::label('priority_desc',Lang::get('lang.priority_desc')) !!} <span class="text-red"> *</span>
                            <input type="text" class="form-control" name="priority_desc" value="{{ ($tk_priority->priority_desc) }}">
                        </div>
                    </div></div>
                <!-- Priority Color -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('priority_color') ? 'has-error' : '' }}">
                            {!! Form::label('priority_color',Lang::get('lang.priority_color')) !!}<span class="text-red"> *</span>
                            <input class="form-control my-colorpicker1 colorpicker-element" id="colorpicker" value="{{ ($tk_priority->priority_color) }}" type="text" name="priority_color">
                        </div>
                    </div>
                    <!-- status radio: required: Active|Dissable -->
                    <div class="col-md-3">

                        @if($tk_priority->is_default!=1)
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            {!! Form::label('status',Lang::get('lang.status')) !!}<span class="text-red"> *</span>
                            <input type="radio"  name="status" value="1" {{$tk_priority->status == '1' ? 'checked' : ''}}>&nbsp;&nbsp;{{Lang::get('lang.active')}}&nbsp;&nbsp;
                            <input type="radio"  name="status"  value="0" {{$tk_priority->status == '0' ? 'checked' : ''}}>&nbsp;&nbsp;{{Lang::get('lang.inactive')}}
                        </div>
                        @else
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            {!! Form::label('status',Lang::get('lang.status')) !!}<span class="text-red"> *</span>
                            <input type="radio"  name="status" value="1" {{$tk_priority->status == '1' ? 'checked' : ''}}>&nbsp;&nbsp;{{Lang::get('lang.active')}}

                        </div>


                        @endif



                    </div>
                    <!-- Show radio: required: public|private -->
                    <div class="col-md-3">
                        @if($tk_priority->is_default!=1)

                        <div class="form-group {{ $errors->has('ispublic') ? 'has-error' : '' }}">
                            {!! Form::label('visibility',Lang::get('lang.visibility')) !!}&nbsp;<span class="text-red"> *</span>
                            <input type="radio"  name="ispublic" value="1" {{$tk_priority->ispublic == '1' ? 'checked' : ''}} >&nbsp;&nbsp;{{Lang::get('lang.public')}}&nbsp;&nbsp;
                            <input type="radio"  name="ispublic"  value="0" {{$tk_priority->ispublic == '0' ? 'checked' : ''}}>&nbsp;&nbsp;{{Lang::get('lang.private')}}
                        </div>     
                        @else

                        <div class="form-group {{ $errors->has('ispublic') ? 'has-error' : '' }}">
                            {!! Form::label('visibility',Lang::get('lang.visibility')) !!}&nbsp;<span class="text-red"> *</span>
                            <input type="radio"  name="ispublic" value="1" {{$tk_priority->ispublic == '1' ? 'checked' : ''}} >&nbsp;&nbsp;{{Lang::get('lang.public')}}&nbsp;&nbsp;

                        </div>     
                        @endif

                    </div>
                </div>  
                <!-- Admin Note : Textarea : -->
                <div class="row hide">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('admin_note',Lang::get('lang.admin_notes')) !!}
                            {!! Form::textarea('admin_note',null,['class' => 'form-control','size' => '30x5','id'=>'ckeditor']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                <input type="checkbox" name="default_priority" @if($tk_priority->is_default == 1) checked disabled @endif> {{ Lang::get('lang.make-default-priority')}}
            </div>
            </div>
        </div>
        <div class="card-footer">
            
         <button type="submit" id="submit" class="btn btn-primary"><i class="fas fa-sync">&nbsp;</i>{!!Lang::get('lang.update')!!}</button>    

        </div>
    </div>
    <!-- close form -->
    {!! Form::close() !!}
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

    @stop
