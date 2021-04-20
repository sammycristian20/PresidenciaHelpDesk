@extends('themes.default1.admin.layout.admin')

@section('Plugins')
class="active"
@stop

@section('plugin-bar')
active
@stop

@section('WhatsApp')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>WhatsApp Configuration  </h1>
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
<div id="whatsapp">
    <settings />
</div>
<script src="{{ bundleLink('js/whatsapp.js') }}" type="text/javascript"></script>

@stop
