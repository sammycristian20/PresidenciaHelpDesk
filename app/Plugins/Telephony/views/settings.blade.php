@extends('themes.default1.admin.layout.admin')

@section('Plugins')
class="active"
@stop

@section('plugin-bar')
active
@stop

@section('Telephony')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>Telephony</h1>
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
<div id="telephony-settings">
    <telephony-settings></telephony-settings>
</div>
<script src="{{ bundleLink('js/telephony.js') }}" type="text/javascript"></script>

@stop
