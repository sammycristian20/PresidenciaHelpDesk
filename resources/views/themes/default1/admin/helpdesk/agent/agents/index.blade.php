@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.agents_list-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.agents_list-page-description') !!}">
@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

@section('Staffs')
active
@stop

@section('staffs-bar')
active
@stop

@section('agents')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.agents')}} </h1>
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
		@if(Session::has('success'))
            <div class="alert alert-success alert-dismissable">
                <i class="fa fa-check-circle"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!!Session::get('success')!!}
            </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <b>{!! Lang::get('lang.alert') !!}!</b><br/>
            <li class="error-message-padding">{!!Session::get('fails')!!}</li>
        </div>
        @endif
<div id="app-admin">
<agent-list></agent-list>
</div>
@stop

@push('scripts')
  <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
  <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
@endpush
