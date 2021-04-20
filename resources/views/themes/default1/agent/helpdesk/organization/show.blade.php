@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.organizations_show-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.organizations_show-page-description') !!}">

@stop

@section('Users')
class="active"
@stop

@section('user-bar')
active
@stop

@section('organizations')
class="active"
@stop

@section('HeadInclude')
@stop

<!-- header -->
@section('PageHeader')

<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css" media="none" onload="this.media='all';"/>
<!-- iCheck -->
<link href="{{assetLink('css','blue')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>


<style type="text/css">
    .ui-tooltip{
        padding:0px
    }
    .tooltip1 {
        position: relative;
        /*display: inline-block;*/
        /*border-bottom: 1px dotted black;*/
    }

    .tooltip1 .tooltiptext {
        visibility: hidden;
        width: 100%;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }

    .tooltip1:hover .tooltiptext {
        visibility: visible;
    }
    ul.tagit {
        width: 450%!important;
    }

</style>

<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>

<div class="box-header" style="margin-top:-5px;margin-bottom:-15px;"><h3 class="box-title">{!! Lang::get('lang.organization_profile') !!}</h3></div>
@stop
<!-- /header -->

<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop

<!-- content -->
@section('content')

    <div id="app-agent">
        
        <org-view></org-view>
    </div>

   <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop