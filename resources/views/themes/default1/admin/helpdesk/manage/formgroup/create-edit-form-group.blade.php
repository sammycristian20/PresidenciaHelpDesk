@extends('themes.default1.admin.layout.admin')
@section('custom-css')
<link href="{{assetLink('css','bootstrap-switch-min')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
<style>
   input[type="radio"]:focus, input[type="checkbox"]:focus {
       outline: none !important;
     }
.tooltip-inner:not(.different){
  white-space:nowrap;
  max-width:none;
}
.dropdown-menu{
   z-index: 99;
}

.modal-open {
    overflow: auto!important;
}
</style>
@stop
@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('forms-ticket')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.form-group') !!}</h1>
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

<div>
  <div id="app-admin">
    <!-- <form-builder :getmenu="'ticket'"></form-builder> -->
    <form-builder-main
      :form-name-obj="{ id: 'name' , className: 'col-md-4', label: 'form_group_name', placeholder: 'name_your_form_group', value: '', submitFormParamKey: 'name' }"
      :form-linker="[{ id: 'departments', className: 'col-md-4', apiEndpoint: '/api/dependency/departments?config=true', label: 'department', value: [], submitFormParamKey: 'department_ids' }, { id: 'help_topics', className: 'col-md-4', apiEndpoint: '/api/dependency/help-topics?config=true', label: 'helptopic', value: [], submitFormParamKey: 'help_topic_ids' }]"
      post-from-group-api="api/post-form-group"
      edit-form-group-api="api/get-form-group"
      form-category-type="group">
    </form-builder-main>
  </div>
</div>
@stop

@push('scripts')
<script src="{{assetLink('js','bootstrap-switch')}}"></script>
@endpush

