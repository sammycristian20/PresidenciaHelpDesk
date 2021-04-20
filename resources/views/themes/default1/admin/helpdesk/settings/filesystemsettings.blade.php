@extends('themes.default1.admin.layout.admin')

@section('PageHeader')
    <h1>{{ trans('lang.settings_file_system_page_header') }}</h1>
@stop

@section('content')

    <div id="app-admin">
        @php

        @endphp
        <file-system-settings />
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
@stop
