@extends('themes.default1.agent.layout.agent')

@section('custom-css')

<link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
 <!-- Select2 -->
@stop

@section('Dashboard')
class="active"
@stop

@section('dashboard-bar')
active
@stop

@section('profile')
class="active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.view-profile')}}</h1>
@stop

@section('profileimg')
<img src="{{Auth::user()->profile_pic}}" class="img-circle" alt="User Image" width="100%"/>
@stop





@section('content')
    
     <div id="app-agent">
                
        <agent-profile-edit></agent-profile-edit>
    </div>

    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>

@stop