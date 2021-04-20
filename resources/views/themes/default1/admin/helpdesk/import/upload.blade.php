@extends('themes.default1.admin.layout.admin')

@section('PageHeader')
    <h1>{{ trans('lang.importer_import') }}</h1>
@stop

@section('content')

    <div id="app-admin">
        <import-upload></import-upload>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
@stop
