@extends('themes.default1.admin.layout.admin')

@section('Plugins')
    class="active"
@stop

@section('plugin-bar')
    active
@stop

@section('Azure Active Directory')
    class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
    <h1>Azure Active Directory</h1>
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
    <div id="azure-active-directory-settings">
        <azure-active-directory-index></azure-active-directory-index>
    </div>
    <script src="{{ bundleLink('js/azureActiveDirectory.js') }}" type="text/javascript"></script>
@stop
