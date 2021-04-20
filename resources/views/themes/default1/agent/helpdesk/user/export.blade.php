@extends('themes.default1.agent.layout.agent')

@section('Users')
class="active"
@stop

@section('user-bar')
active
@stop

@section('user')
class="active"
@stop

@section('user-export')
class="active"
@stop


<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.export_user')}}</h1>
@stop
<!-- /header -->
<!-- content -->
@section('content')
<div id="app-agent">
        
        <user-export></user-export>
    </div>
    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop