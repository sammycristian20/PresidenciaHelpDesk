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
    <h1>{{ trans('Facebook::lang.facebook_app_settings') }}</h1>
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

    <div id="facebook-settings">
        <facebook-general-settings></facebook-general-settings>
    </div>
    <script src="{{ bundleLink('js/facebook.js') }}" type="text/javascript"></script>

@stop
