@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.sla_plans_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.sla_plans_edit-page-description') !!}">


@stop

<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>

<script src="{{assetLink('js','select2')}}" type="text/javascript"></script>

@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('sla')
class="active"
@stop

@section('HeadInclude')
 
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.sla_plans') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('custom-css')
<style type="text/css">
    
    .glyphicon-minus-sign{
        font-size: 25px!important;
        color: #990000;
    }
    .glyphicon-plus{
       display: inline-block;
      width: 23px;
      height: 24px;
      color:#ffffff!important;
      border:5px solid #009853;
      background-color: #009853;
      border-radius: 50%; /* circle */
    }
</style>
@stop

<!-- /breadcrumbs -->
<!-- content -->
@section('content')
    
    <div id="app-admin">
        <sla-create-edit></sla-create-edit>
    </div>

    <script src="{{assetLink('js','select2')}}"></script>
@stop
