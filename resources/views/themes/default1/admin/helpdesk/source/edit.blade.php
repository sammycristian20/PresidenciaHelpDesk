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
  {!! Form::model($source,['route'=>['source.update',$source->id],'method'=>'patch','id'=>'Form']) !!}

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
        <h3 class="card-title">{{Lang::get('lang.edit_source')}}</h3>
    </div>

    <div class="card-body">

     <div class="row">
       
             <div class="col-md-6">
  
            <div class="form-group {{ $errors->has('source') ? 'has-error' : '' }}">
               {!! Form::label('name',trans('lang.source')) !!}<span class="text-red"> *</span>

                    @if($source->is_default == 1)
                    <div class="form-group {{ $errors->has('source') ? 'has-error' : '' }}">
                        {!! Form::text('source',$source->name,['class'=>'form-control','readonly']) !!}
                    </div>
                    @else
                       <div class="form-group {{ $errors->has('source') ? 'has-error' : '' }}">
                        {!! Form::text('source',$source->name,['class'=>'form-control']) !!}
                    </div>
                    @endif

            </div>

            </div>

                <div class="col-md-6">
            <div class="form-group {{ $errors->has('display_as') ? 'has-error' : '' }}">
                {!! Form::label('value',trans('lang.display_as')) !!}<span class="text-red"> *</span>
                      <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.source_usage_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
               
                    <div class="form-group {{ $errors->has('display_as') ? 'has-error' : '' }}">
                        {!! Form::text('display_as',$source->value,['class'=>'form-control']) !!}
                    </div>
                
            </div>
            </div>
              <div class="col-md-5 hide">
            <div class="form-group {{ $errors->has('location') ? 'has-error' : '' }}">
                {!! Form::label('location',trans('lang.location')) !!}
                    <div class="form-group {{ $errors->has('location') ? 'has-error' : '' }}">
                        {!! Form::select('location[]', [Lang::get('lang.location')=>$location->pluck('title','id')->toArray()],$source_location,['multiple'=>true,'class'=>"form-control" ,'id'=>"location",'style'=>"width:100%"]) !!}
                    </div>
                
            </div>
            </div>
                     <div class="col-md-2 hide">
                <div class="form-group {{ $errors->has('icon_class') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.icon_class') !!}: <span class="text-red"> *</span></label><br>
                    <select class="form-control"  name="icon_class" style="font-family: FontAwesome, sans-serif;" required>
                        <option <?php if ($source->css_class == "fa fa fa-edit") echo 'selected="selected"' ?> value="fa fa-edit">&#xf044</option>
                        <option <?php if ($source->css_class == "fa fa fa-bars") echo 'selected="selected"' ?> value="fa fa-bars">&#xf0c9</option>
                        <option <?php if ($source->css_class == "fa fa-bell-o") echo 'selected="selected"' ?> value="fa fa-bell-o">&#xf0a2</option>
                        <option <?php if ($source->css_class == "fa fa-bookmark-o") echo 'selected="selected"' ?> value="fa fa-bookmark-o">&#xf097</option>
                        <option <?php if ($source->css_class == "fa fa-bug") echo 'selected="selected"' ?> value="fa fa-bug">&#xf188</option>
                        <option <?php if ($source->css_class == "fa fa-bullhorn") echo 'selected="selected"' ?> value="fa fa-bullhorn">&#xf0a1</option>
                        <option <?php if ($source->css_class == "fa fa-calendar") echo 'selected="selected"' ?> value="fa fa-calendar">&#xf073</option>
                        <option <?php if ($source->css_class == "fa fa-cart-plus") echo 'selected="selected"' ?> value="fa fa-cart-plus">&#xf217</option>
                        <option <?php if ($source->css_class == "fa fa-check") echo 'selected="selected"' ?> value="fa fa-check">&#xf00c</option>
                        <option <?php if ($source->css_class == "fa fa-check-circle") echo 'selected="selected"' ?> value="fa fa-check-circle">&#xf058</option>
                        <option <?php if ($source->css_class == "fa fa-check-circle-o") echo 'selected="selected"' ?> value="fa fa-check-circle-o">&#xf05d</option>
                        <option <?php if ($source->css_class == "fa fa-check-square") echo 'selected="selected"' ?> value="fa fa-check-square">&#xf14a</option>
                        <option <?php if ($source->css_class == "fa fa-check-square-o") echo 'selected="selected"' ?> value="fa fa-check-square-o">&#xf046</option>
                        <option <?php if ($source->css_class == "fa fa-circle-o-notch") echo 'selected="selected"' ?> value="fa fa-circle-o-notch">&#xf1ce</option>
                        <option <?php if ($source->css_class == "fa fa-clock-o") echo 'selected="selected"' ?> value="fa fa-clock-o">&#xf017</option>
                        <option <?php if ($source->css_class == "fa fa-close") echo 'selected="selected"' ?> value="fa fa-close">&#xf00d</option>
                        <option <?php if ($source->css_class == "fa fa-code") echo 'selected="selected"' ?> value="fa fa-code">&#xf121</option>
                        <option <?php if ($source->css_class == "fa fa-cog") echo 'selected="selected"' ?> value="fa fa-cog">&#xf013</option>
                        <option <?php if ($source->css_class == "fa fa-cogs") echo 'selected="selected"' ?> value="fa fa-cogs">&#xf085</option>
                        <option <?php if ($source->css_class == "fa fa-comment") echo 'selected="selected"' ?> value="fa fa-comment">&#xf075</option>
                        <option <?php if ($source->css_class == "fa fa-comment-o") echo 'selected="selected"' ?> value="fa fa-comment-o">&#xf0e5</option>
                        <option <?php if ($source->css_class == "fa fa-commenting") echo 'selected="selected"' ?> value="fa fa-commenting">&#xf27a</option>
                        <option <?php if ($source->css_class == "fa fa-commenting-o") echo 'selected="selected"' ?> value="fa fa-commenting-o">&#xf27b</option>
                        <option <?php if ($source->css_class == "fa fa-comments") echo 'selected="selected"' ?> value="fa fa-comments">&#xf086</option>
                        <option <?php if ($source->css_class == "fa fa-comments-o") echo 'selected="selected"' ?> value="fa fa-comments-o">&#xf0e6</option>
                        <option <?php if ($source->css_class == "fa fa-edit") echo 'selected="selected"' ?> value="fa fa-edit">&#xf044</option>
                        <option <?php if ($source->css_class == "fa fa-envelope-o") echo 'selected="selected"' ?> value="fa fa-envelope-o">&#xf003</option>
                        <option <?php if ($source->css_class == "fa fa-exchange") echo 'selected="selected"' ?> value="fa fa-exchange">&#xf0ec</option>
                        <option <?php if ($source->css_class == "fa fa-exclamation") echo 'selected="selected"' ?> value="fa fa-exclamation">&#xf12a</option>
                        <option <?php if ($source->css_class == "fa fa-exclamation-triangle") echo 'selected="selected"' ?> value="fa fa-exclamation-triangle">&#xf071</option>
                        <option <?php if ($source->css_class == "fa fa-external-link") echo 'selected="selected"' ?> value="fa fa-external-link">&#xf08e</option>
                        <option <?php if ($source->css_class == "fa fa-eye") echo 'selected="selected"' ?> value="fa fa-eye">&#xf06e</option>
                        <option <?php if ($source->css_class == "fa fa-feed") echo 'selected="selected"' ?> value="fa fa-feed">&#xf09e</option>
                        <option <?php if ($source->css_class == "fa fa-flag-o") echo 'selected="selected"' ?> value="fa fa-flag-o">&#xf11d</option>
                        <option <?php if ($source->css_class == "fa fa-flash") echo 'selected="selected"' ?> value="fa fa-flash">&#xf0e7</option>
                        <option <?php if ($source->css_class == "fa fa-folder-o") echo 'selected="selected"' ?> value="fa fa-folder-o">&#xf114</option>
                        <option <?php if ($source->css_class == "fa fa-folder-open-o") echo 'selected="selected"' ?> value="fa fa-folder-open-o">&#xf115</option>
                        <option <?php if ($source->css_class == "fa fa-group") echo 'selected="selected"' ?> value="fa fa-group">&#xf0c0</option>
                        <option <?php if ($source->css_class == "fa fa-info") echo 'selected="selected"' ?> value="fa fa-info">&#xf129</option>
                        <option <?php if ($source->css_class == "fa fa-life-ring") echo 'selected="selected"' ?> value="fa fa-life-ring">&#xf1cd</option>
                        <option <?php if ($source->css_class == "fa fa-line-chart") echo 'selected="selected"' ?> value="fa fa-line-chart">&#xf201</option>
                        <option <?php if ($source->css_class == "fa fa-location-arrow") echo 'selected="selected"' ?> value="fa fa-location-arrow">&#xf124</option>
                        <option <?php if ($source->css_class == "fa fa-lock") echo 'selected="selected"' ?> value="fa fa-lock">&#xf023</option>
                        <option <?php if ($source->css_class == "fa fa-mail-forward") echo 'selected="selected"' ?> value="fa fa-mail-forward">&#xf064</option>
                        <option <?php if ($source->css_class == "fa fa-mail-reply") echo 'selected="selected"' ?> value="fa fa-mail-reply">&#xf112</option>
                        <option <?php if ($source->css_class == "fa fa-mail-reply-all") echo 'selected="selected"' ?> value="fa fa-mail-reply-all">&#xf122</option>
                        <option <?php if ($source->css_class == "fa fa-times") echo 'selected="selected"' ?> value="fa fa-times">&#xf00d</option>
                        <option <?php if ($source->css_class == "fa fa-trash-o") echo 'selected="selected"' ?> value="fa fa-trash-o">&#xf014</option>
                        <option <?php if ($source->css_class == "fa fa-user") echo 'selected="selected"' ?> value="fa fa-user">&#xf007</option>
                        <option <?php if ($source->css_class == "fa fa-user-plus") echo 'selected="selected"' ?> value="fa fa-user-plus">&#xf234</option>
                        <option <?php if ($source->css_class == "fa fa-user-secret") echo 'selected="selected"' ?> value="fa fa-user-secret">&#xf21b</option>
                        <option <?php if ($source->css_class == "fa fa-user-times") echo 'selected="selected"' ?> value="fa fa-user-times">&#xf235</option>
                        <option <?php if ($source->css_class == "fa fa-users") echo 'selected="selected"' ?> value="fa fa-users">&#xf0c0</option>
                        <option <?php if ($source->css_class == "fa fa-wrench") echo 'selected="selected"' ?> value="fa fa-wrench">&#xf0ad</option>
                        <option <?php if ($source->css_class == "fa fa-circle-o-notch") echo 'selected="selected"' ?> value="fa fa-circle-o-notch">&#xf1ce</option>
                        <option <?php if ($source->css_class == "fa fa-refresh") echo 'selected="selected"' ?> value="fa fa-refresh">&#xf021</option>
                        <option <?php if ($source->css_class == "fa fa-spinner") echo 'selected="selected"' ?> value="fa fa-spinner">&#xf110</option>
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

       <button type="submit" id="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-sync fa-spin fa-1x fa-fw'>&nbsp;</i> updating..."><i class="fas fa-sync">&nbsp;&nbsp;</i>{!!Lang::get('lang.update')!!}</button>     
{!! Form::close() !!}
</div>
</div>
<script src="{{assetLink('js','intlTelInput.js')}}"></script>

@stop