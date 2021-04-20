@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.ticket_type_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.ticket_type_edit-page-description') !!}">


@stop

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('ticket-types')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.ticket_type') !!}</h1>
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
 <div class="alert alert-danger alert-dismissable">
                <i class="fas fa-ban"></i>
                <b>Alert!</b>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <br/>
                @if($errors->first('name'))
                <li class="error-message-padding">{!! $errors->first('name', ':message') !!}</li>
                @endif
                @if($errors->first('type_desc'))
                <li class="error-message-padding">{!! $errors->first('type_desc', ':message') !!}</li>
                @endif
               @if($errors->first('status'))
                <li class="error-message-padding">{!! $errors->first('status', ':message') !!}</li>
                @endif

 </div>
@endif
<form action="{{route('ticket.type.edit1', $tk_type->id)}}" method="post" role="form" id="Form">
{{ csrf_field() }}
    <input type="hidden" name="tk_type_id" value="{{$tk_type->id}}">
    <div class="card card-light">
        <div class="card-header">
            <h3 class="card-title">{{Lang::get('lang.edit_ticket_type')}}</h3>
        </div>
        <div class="card-body">
           
            <!-- Name text form Required -->
            <div>
            <!-- <table class="table table-hover" style="overflow:hidden;"> -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            {!! Form::label('name',Lang::get('lang.name')) !!}<span class="text-red"> *</span>
                            <input type="text" class="form-control" name="name" value="{{ ($tk_type->name) }}" >
                        </div>
                    </div>
                    <!-- Grace Period text form Required -->
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('type_desc') ? 'has-error' : '' }}">
                            {!! Form::label('type_desc',Lang::get('lang.type_desc')) !!} <span class="text-red"> *</span>
                            <input type="text" class="form-control" name="type_desc" value="{{ ($tk_type->type_desc) }}">
                        </div>
                    </div></div>
                <!-- Priority Color -->
                <div class="row">
                 
                    <!-- status radio: required: Active|Dissable -->

                      @if($tk_type->is_default == $tk_type->id)
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            {!! Form::label('status',Lang::get('lang.status')) !!}<span class="text-red"> *</span>&nbsp;&nbsp;
                            <input type="radio"  name="status" value="1" {{$tk_type->status == '1' ? 'checked' : ''}}>&nbsp;&nbsp;{{Lang::get('lang.active')}}&nbsp;&nbsp;
                      </div>
                    </div>
                    @else
                     <div class="col-md-3">
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            {!! Form::label('status',Lang::get('lang.status')) !!}<span class="text-red"> *</span>&nbsp;&nbsp;
                            <input type="radio"  name="status" value="1" {{$tk_type->status == '1' ? 'checked' : ''}}>&nbsp;&nbsp;{{Lang::get('lang.active')}}&nbsp;&nbsp;
                            <input type="radio"  name="status"  value="0" {{$tk_type->status == '0' ? 'checked' : ''}}>&nbsp;&nbsp;{{Lang::get('lang.inactive')}}
                        </div>
                    </div>
                    @endif
                    <!-- Show radio: required: public|private -->
                    <div class="col-md-3">
                    @if($tk_type->is_default == $tk_type->id)
                    <div class="form-group {{ $errors->has('ispublic') ? 'has-error' : '' }}">
                            {!! Form::label('visibility',Lang::get('lang.visibility')) !!}&nbsp;<span class="text-red"> *</span>&nbsp;&nbsp;&nbsp;
                            <input type="radio"  name="ispublic" value="1" {{$tk_type->ispublic == '1' ? 'checked' : ''}} >&nbsp;&nbsp;{{Lang::get('lang.public')}}&nbsp;&nbsp;
                            
                        </div>     
                     @else
                     <div class="form-group {{ $errors->has('ispublic') ? 'has-error' : '' }}">
                            {!! Form::label('visibility',Lang::get('lang.visibility')) !!}&nbsp;<span class="text-red"> *</span>&nbsp;&nbsp;&nbsp;
                            <input type="radio"  name="ispublic" value="1" {{$tk_type->ispublic == '1' ? 'checked' : ''}} >&nbsp;&nbsp;{{Lang::get('lang.public')}}&nbsp;&nbsp;
                            <input type="radio"  name="ispublic"  value="0" {{$tk_type->ispublic == '0' ? 'checked' : ''}}>&nbsp;&nbsp;{{Lang::get('lang.private')}}
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
                <input type="checkbox" name="default_ticket_type" @if($tk_type->is_default == $tk_type->id) checked disabled @endif> {{ Lang::get('lang.make-default-type')}}
            </div>
            </div>
        </div>
        <div class="card-footer">
            
            
            <button type="submit" id="submit" class="btn btn-primary"><i class="fas fa-sync">&nbsp;</i>{!!Lang::get('lang.update')!!}</button>    
        </div>
    </div>
    <!-- close form -->
    {!! Form::close() !!}
   

    @stop
