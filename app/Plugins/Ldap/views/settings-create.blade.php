@extends('themes.default1.admin.layout.admin')

@section('Plugins')
    class="active"
@stop

@section('plugin-bar')
    active
@stop

@section('Ldap')
    class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
    <h1>LDAP</h1>
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
    <div id="ldap-settings">
        <ldap-settings></ldap-settings>
    </div>
    <script src="{{ bundleLink('js/ldap.js') }}" type="text/javascript"></script>

@stop
