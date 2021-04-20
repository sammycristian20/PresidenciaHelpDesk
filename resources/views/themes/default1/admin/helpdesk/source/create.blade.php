@extends('themes.default1.admin.layout.admin')

@section('Tickets')
active
@stop

@section('manage-bar')
active
@stop

@section('source')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{trans('lang.source')}}</h1>
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
 {!! Form::open(['url'=>'source','method'=>'post', 'id' => 'label-form']) !!}
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
    <strong>Alert !</strong><br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('lang.create_new_source')}}</h3>
    </div>

    <div class="card-body">
      
     <div class="row">
       
             <div class="col-md-6">
            <div class="form-group {{ $errors->has('source') ? 'has-error' : '' }}">
                {!! Form::label('name',trans('lang.source')) !!}<span class="text-red"> *</span>
               
                    <div class="form-group {{ $errors->has('source') ? 'has-error' : '' }}">
                        {!! Form::text('source',null,['class'=>'form-control']) !!}
                    </div>
                
            </div>
            </div>

            <div class="col-md-6">
            <div class="form-group {{ $errors->has('display_as') ? 'has-error' : '' }}">
                {!! Form::label('value',trans('lang.display_as')) !!}<span class="text-red"> *</span>
                 <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.source_usage_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
               
                    <div class="form-group {{ $errors->has('display_as') ? 'has-error' : '' }}">
                        {!! Form::text('display_as',null,['class'=>'form-control']) !!}
                    </div>
                
            </div>
            </div>
               <div class="col-md-5 hide">
            <div class="form-group {{ $errors->has('location') ? 'has-error' : '' }}">
                {!! Form::label('location',trans('lang.location')) !!}
               
                    <div class="form-group {{ $errors->has('location') ? 'has-error' : '' }}">
                      {!! Form::select('location[]', [Lang::get('lang.location')=>$location->pluck('title','id')->toArray()],null,['multiple'=>true,'class'=>"form-control" ,'id'=>"location",'style'=>"width:100%"]) !!}
                    </div>
                
            </div>
            </div>


             <div class="col-md-2 hide">
                <div class="form-group {{ $errors->has('icon_class') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.icon') !!}: <span class="text-red"> *</span></label><br>
                    <select class="form-control"  name="icon_class" style="font-family: FontAwesome, sans-serif;" required>
                     
                        <option value="fa fa-envelope"> <span class="icon icon-search"></span>&#xf0c9</option>
                        <option value="fa fa-bell-o">&#xf0a2</option>
                        <option value="fa fa-bookmark-o">&#xf097</option>
                        <option value="fa fa-bug">&#xf188</option>
                        <option value="fa fa-bullhorn">&#xf0a1</option>
                        <option value="fa fa-calendar">&#xf073</option>
                        <option value="fa fa-cart-plus">&#xf217</option>
                        <option value="fa fa-check">&#xf00c</option>
                        <option value="fa fa-check-circle">&#xf058</option>
                        <option value="fa fa-check-circle-o">&#xf05d</option>
                        <option value="fa fa-check-square">&#xf14a</option>
                        <option value="fa fa-check-square-o">&#xf046</option>
                        <option value="fa fa-circle-o-notch">&#xf1ce</option>
                        <option value="fa fa-clock-o">&#xf017</option>
                        <option value="fa fa-close">&#xf00d</option>
                        <option value="fa fa-code">&#xf121</option>
                        <option value="fa fa-cog">&#xf013</option>
                        <option value="fa fa-cogs">&#xf085</option>
                        <option value="fa fa-comment">&#xf075</option>
                        <option value="fa fa-comment-o">&#xf0e5</option>
                        <option value="fa fa-commenting">&#xf27a</option>
                        <option value="fa fa-commenting-o">&#xf27b</option>
                        <option value="fa fa-comments">&#xf086</option>
                        <option value="fa fa-comments-o">&#xf0e6</option>
                        <option value="fa fa-edit">&#xf044</option>
                        <option value="fa fa-envelope-o">&#xf003</option>
                        <option value="fa fa-exchange">&#xf0ec</option>
                        <option value="fa fa-exclamation">&#xf12a</option>
                        <option value="fa fa-exclamation-triangle">&#xf071</option>
                        <option value="fa fa-external-link">&#xf08e</option>
                        <option value="fa fa-eye">&#xf06e</option>
                        <option value="fa fa-feed">&#xf09e</option>
                        <option value="fa fa-flag-o">&#xf11d</option>
                        <option value="fa fa-flash">&#xf0e7</option>
                        <option value="fa fa-folder-o">&#xf114</option>
                        <option value="fa fa-folder-open-o">&#xf115</option>
                        <option value="fa fa-group">&#xf0c0</option>
                        <option value="fa fa-info">&#xf129</option>
                        <option value="fa fa-life-ring">&#xf1cd</option>
                        <option value="fa fa-line-chart">&#xf201</option>
                        <option value="fa fa-location-arrow">&#xf124</option>
                        <option value="fa fa-lock">&#xf023</option>
                        <option value="fa fa-mail-forward">&#xf064</option>
                        <option value="fa fa-mail-reply">&#xf112</option>
                        <option value="fa fa-mail-reply-all">&#xf122</option>
                        <option value="fa fa-times">&#xf00d</option>
                        <option value="fa fa-trash-o">&#xf014</option>
                        <option value="fa fa-user">&#xf007</option>
                        <option value="fa fa-user-plus">&#xf234</option>
                        <option value="fa fa-user-secret">&#xf21b</option>
                        <option value="fa fa-user-times">&#xf235</option>
                        <option value="fa fa-users">&#xf0c0</option>
                        <option value="fa fa-wrench">&#xf0ad</option>
                        <option value="fa fa-circle-o-notch">&#xf1ce</option>
                        <option value="fa fa-refresh">&#xf021</option>
                        <option value="fa fa-spinner">&#xf110</option>
                    </select>
                </div>
            </div>
            </div>
            
                {!! Form::label('description',trans('lang.description')) !!}
               
                    <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                        {!! Form::textarea('description', null,['class'=>'form-control','id'=>'ckeditor']) !!}
                    </div>
               
            
             
             </div>


<div class="card-footer">

<button type="submit" id="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-circle-notch fa-spin'>&nbsp;</i> {!! Lang::get('lang.saving') !!}"><i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}</button>


    {!! Form::close() !!}
</div>
</div>


<!-- for submit button loader-->
<script>
    $(function(){
    $('#submit').attr('disabled','disabled');
    $('#label-form').on('input',function(){
        $('#submit').removeAttr('disabled');
    });
    $('#label-form').on('change',':input',function(){
        $('#submit').removeAttr('disabled');
    });
    });
    $('#label-form').submit(function () {
        var $this = $('#submit');
        $this.button('loading');
       $('#Edit').modal('show');
       
    });
   
</script>
<script src="{{assetLink('js','intlTelInput.js')}}"></script>

@stop