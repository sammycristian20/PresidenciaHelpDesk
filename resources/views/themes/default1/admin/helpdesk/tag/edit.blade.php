@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.tags_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.tags_edit-page-description') !!}">
@stop

@section('Tickets')
active
@stop

@section('manage-bar')
active
@stop

@section('tags')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{trans('lang.tags')}}</h1>
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
  {!! Form::model($tag,['route'=>['tag.update',$tag->id],'method'=>'patch','id'=>'Form']) !!}

@if(Session::has('success'))
<div class="alert alert-success alert-dismissable">
    <i class="fas fa-check-circle"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('success')}}
</div>
@endif
@if(Session::has('fails'))
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('fails')}}
</div>
@endif
@if(Session::has('warn'))
<div class="alert alert-warning alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('warn')}}
</div>
@endif
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
<div class="card card-light">

    <div class="card-header">
        <h3 class="card-title">
            {{trans('lang.edit_tag')}}
        </h3>
      
    </div>
    <div class="card-body">
        <table class="table table-borderless">
           <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                {!! Form::label('name',trans('lang.name')) !!}<span class="text-red"> *</span>
                
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        {!! Form::text('name',null,['class'=>'form-control']) !!}
                    </div>
                
            </div>
           
                {!! Form::label('description',trans('lang.description')) !!}
                
                    <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                        {!! Form::textarea('description', null,['class'=>'form-control','id'=>'ckeditor']) !!}
                    </div>
                <td class="hide">{!! Form::label('order','Order') !!}<span class="text-red"> *</span></td>
                <td class="hide">
                    <div class="form-group {{ $errors->has('order') ? 'has-error' : '' }}">
            {!! Form::input('number', 'order', null, array('class' => 'form-control')) !!}
    </div>


<tr>
    <td class="hide">{!! Form::label('status','Status') !!}</td>
    <td class="hide"><input type="checkbox" value="1" name="status" id="status" checked="true">&nbsp;&nbsp;{{Lang::get('lang.enable')}}</td>
</tr>

</table>
</div>
<div class="card-footer">
<button type="submit" id="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-sync fa-spin fa-1x fa-fw'>&nbsp;</i> updating..."><i class="fas fa-sync">&nbsp;</i>{!!Lang::get('lang.update')!!}</button>     
{!! Form::close() !!}
</div>
</div>
@stop