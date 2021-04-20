<!DOCTYPE html>

<html ng-app="fbApp">

	<head>

		<style type="text/css">
			.lockscreen-wrapper { max-width:  600px !important;}
		</style>
	
		<meta charset="UTF-8">

 		@yield('meta-tags')
		
		<title> Database Updation Required </title>

		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		
		<meta name="_token" content="{!! csrf_token() !!}"/>
		
		<meta name="csrf-token" content="{{ csrf_token() }}">
		
		<meta name="api-base-url" content="{{ url('/') }}" />
			
		<link href="{{assetLink('css','bootstrap-4')}}" rel="stylesheet" type="text/css" id="bootstrapLTR"/>
		
		<link href="{{assetLink('css','adminlte-3')}}" rel="stylesheet" type="text/css" id="adminLTR"/>
	  
		<link href="{{assetLink('css','font-awesome-5')}}" rel="stylesheet" type="text/css" />
		
		<script src="{{assetLink('js','jquery-3')}}" type="text/javascript" media="none" onload="this.media='all';"></script>
		
		<script src="{{assetLink('js','polyfill')}}"></script>
		
	</head>

	<body class="hold-transition lockscreen">
		
		<div id="header-container">
			
			<database-updates></database-updates>	
		</div>
		
		<script src="{{bundleLink('js/lang')}}" type="text/javascript"></script>
      
      <script src="{{bundleLink('js/common.js')}}" type="text/javascript"></script>
		
		<script src="{{bundleLink('js/headerMenu.js')}}" type="text/javascript"></script>

	   <script type="text/javascript" src="{{assetLink('js','popper')}}"></script>
		
		<script src="{{assetLink('js','bootstrap-4')}}" type="text/javascript"></script>
		
		<script src="{{assetLink('js','adminlte-3')}}" type="text/javascript"></script>
	</body>
</html>