@extends('themes.default1.client.layout.client')
@section('title')
{!! Lang::get('lang.registration') !!}-

@stop
     <?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (isset($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }

        ?>

@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.registration-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.registration-page-description') !!}">


@stop

<style>
  .list-group-item{
    background-color: transparent !important;
  }
  .box-title{
      border-bottom: 2px solid #009aba;
      padding-bottom: 7px;
  }
  .colo{
    background-color: rgba(244, 244, 244, 0.16) !important;
  }
</style>

@section('home')
    class = "active"
@stop

@section('HeadInclude')

<link href="{{assetLink('css','widgetbox')}}" rel="stylesheet" type="text/css" />

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
@stop

@section('breadcrumb')
    <div class="site-hero clearfix">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="text">{!! Lang::get('lang.you_are_here') !!}: </li>
            <li><a href="{!! URL::route('/') !!}">{!! Lang::get('lang.home') !!}</a></li>
        </ol>
    </div>
@stop
@section('content')
<div class="box-header with-border">
        <h3 class="box-title">
            Registration
        </h3>
    </div>
    <div class="box-body" >
        <div class="row">
            <div class="box-body" id="app-vue">
                  <create-form :person="'agent'" :category="'user'"></create-form>
            </div>

            <script src="{{asset('js/lang')}}" type="text/javascript"></script>
            <script src="{{asset('js/common.js')}}" type="text/javascript"></script>
            <script src="{{asset('js/app.js')}}" type="text/javascript"></script>
        </div>
    </div>
</div>
<script src="{{assetLink('js','select2')}}"></script>
<script src="{{assetLink('js','iCheck')}}"></script>
@stop
