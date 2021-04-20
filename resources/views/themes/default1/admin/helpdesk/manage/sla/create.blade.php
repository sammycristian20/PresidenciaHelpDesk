@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.sla_plans_create-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.sla_plans_create-page-description') !!}">

@stop

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

@section('PageHeader')
<h1>{!! Lang::get('lang.sla_plans') !!}</h1>
@stop
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('custom-css')

@stop

@section('content')

    <div id="app-admin">
        <sla-create-edit></sla-create-edit>
    </div>

    <script src="{{assetLink('js','select2')}}"></script>
@stop
