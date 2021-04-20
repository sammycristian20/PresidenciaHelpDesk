@extends('themes.default1.agent.layout.agent')

@extends('themes.default1.agent.layout.sidebar')    

@section('pages')
active
@stop

@section('custom-css')
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css"/>
@stop

@section('all-pages')
class="active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.pages')}}</h1>
@stop

@section('content')
 <div id="app-agent">
        <pages-index></pages-index>
    </div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop