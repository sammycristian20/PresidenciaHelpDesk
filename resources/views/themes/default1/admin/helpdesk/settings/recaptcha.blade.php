@extends('themes.default1.admin.layout.admin')


@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('system')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.recaptcha')}}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop

<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer>
            
</script>
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
	
	<div id="app-admin">
    
    <recaptcha-settings></recaptcha-settings>
  </div>
  
  <script type="text/javascript" src="{{ bundleLink('js/lang' ) }}"></script>
@stop
