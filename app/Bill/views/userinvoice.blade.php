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
<h1>{{Lang::get('Bill::lang.user_invoice')}}</h1>
@stop
<!-- /header -->
<!-- content -->
@section('content')
    
     <div id="app-billing">

        <package-invoice></package-invoice>
    </div>
    
    
    <script type="text/javascript" src="{{ bundleLink('js/faveoBilling.js') }}"></script>
@stop


