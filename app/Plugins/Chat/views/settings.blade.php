@extends('themes.default1.admin.layout.admin')
@section('Plugins')
class="active"
@stop

@section('plugin-bar')
active
@stop

@section('Social')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('chat::lang.chat-integrations')}}</h1>
@stop
<!-- /header -->

<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@endsection
<!-- /breadcrumbs -->
<!-- content -->
@section('content')

@if(Session::has('success'))

    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{Session::get('success')}}
    </div>

@endif 


@if(Session::has('error'))

    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{Session::get('error')}}
    </div>

@endif 
       
<div id="chat-settings">
    <chat-settings />
</div>
<script src="{{ bundleLink('js/chat.js') }}" type="text/javascript"></script>

@stop
