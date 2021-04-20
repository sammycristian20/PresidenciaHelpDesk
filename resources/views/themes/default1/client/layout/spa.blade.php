<!DOCTYPE html>

<html>
	
	<head>
	
		<meta charset="UTF-8">
	
		<base href="{{url('/')}}">
	
		<meta name="csrf-token" content="{{ csrf_token() }}">
	
		<meta name="api-base-url" content="{{ url('/') }}" />
	
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

		<link href="{{assetLink('css','widgetbox')}}" rel="stylesheet" type="text/css" />
	
		<link href="{{assetLink('css','bootstrap-4')}}" rel="stylesheet" type="text/css" />
	
		<link href="{{assetLink('css','font-awesome-5')}}" rel="stylesheet" type="text/css" />
	
		<link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css" />
	
		<link href="{{assetLink('css','client-custom-css')}}" rel="stylesheet" type="text/css" />
	
		<link href="{{assetLink('css','jquery-rating')}}" rel="stylesheet" type="text/css" />
	
		<script src="{{assetLink('js','jquery-3')}}" type="text/javascript" media="none" onload="this.media='all';">
			
		</script>

		<!-- for recaptcha session storage we are using this file-->
		@include('themes.default1.common.recaptcha')
			
		</script>
		
		<script src="{{assetLink('js','polyfill')}}"></script>
		
		<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" />

		<link href="{{assetLink('css','ckeditor-css')}}" rel="stylesheet" type="text/css" />

	  <!-- check if lanuage is RTL, if yes, inject bootstrap-RTL -->
	  
	  <?php
		  $lang = App::getLocale();
		  if($lang == 'ar'){
			$bootstrapRTLPath = assetLink('css','bootstrap-rtl');
			echo "<link href=$bootstrapRTLPath rel='stylesheet' type='text/css'/>";
		  }
		?>

		<?php
		  try {
				$seo =  new \App\Http\Controllers\Client\helpdesk\SeoController();
				$layoutController = new \App\Http\Controllers\Client\helpdesk\ClientLayoutController();
				$authController = new App\Http\Controllers\Auth\AuthController();
				// layout data
				$layout = $layoutController->getLayoutData();
				// auth user data
				$authInfo = $authController->getLoggedInClientInfo();
				// dd($layout, $authInfo);
				$response = $seo->getUrlandAppendTitleDescription();
				$meta = json_decode($response->content())->data;
			} catch(\Exception $e) {
				// ignore exception
			}
		?>

		<meta name="title" content="{!! $meta->title !!} :: {!! strip_tags($meta->company_name) !!}">
		
		<meta name="description" content="{!! $meta->description !!}">
		
		<meta content="{!! $meta->company_logo !!}" property="og:image">

	</head>
	
	<body>

		<script type="text/javascript">
			sessionStorage.setItem('data_time_format', '{{ dateTimeFormat() }}');
            sessionStorage.setItem('date_format', '{{ dateFormat() }}');
            sessionStorage.setItem('timezone', '{{ agentTimeZone() }}');
		</script>
		@include('themes.default1.common.socket')
		
		<div id="app-client-panel" style="height:100%;">

			<client-panel-layout :layout-details="{{ json_encode($layout) }}" 
				:auth-info="{{ json_encode($authInfo) }}">
					
			</client-panel-layout>
		</div>

		<script type="text/javascript" src="{{ bundleLink('js/lang' ) }}"></script>

		<script type="text/javascript" src="{{ bundleLink('js/common.js') }}"></script>

		<script type="text/javascript" src="{{ bundleLink('js/app.js') }}"></script>

		<script src="{{assetLink('js','select2')}}"></script>

		<script type="text/javascript" src="{{assetLink('js','popper')}}"></script>

		<script type="text/javascript" src="{{assetLink('js','bootstrap-4')}}"></script>

		<script type="text/javascript" src="{{assetLink('js','superfish')}}"></script>

		<script type="text/javascript" src="{{assetLink('js','mobilemenu')}}"></script>
		
		<script type="text/javascript">
			localStorage.setItem('LANGUAGE', '{{Lang::getLocale()}}');
		</script>
		<?php \Event::dispatch('timeline-customjs', [['fired_at' => 'clientlayout','request' => Request()]]); ?>

		<?php
			// adding scripts for plugins
			// there's no point in adding the entire script, so we can use dynamic import
			// emit an event which ldap is listening to
			\Event::dispatch('client-panel-scripts-dispatch');
		?>


	</body>
</html>