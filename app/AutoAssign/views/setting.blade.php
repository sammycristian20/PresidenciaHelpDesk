@extends('themes.default1.admin.layout.admin')

@section('Tickets')
active
@stop

@section('tickets-bar')
active
@stop

@section('auto-assign')
class="active"
@stop

@section('HeadInclude')
@stop

@section('custom-css')
 <link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
@stop

@section('PageHeader')
<h1>{{ Lang::get('lang.auto_assign') }}</h1>
@stop

@section('content')
    
    <div id="app-auto-assign">
        
        <auto-assign></auto-assign>
    </div>

    <script type="text/javascript" src="{{ bundleLink('js/autoAssign.js') }}"></script>
@stop