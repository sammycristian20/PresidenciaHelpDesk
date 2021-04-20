@extends('themes.default1.admin.layout.admin')
@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('browser-notification')
class="active"
@stop

@section('HeadInclude')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@stop

<!-- header -->
@section('PageHeader')
<h3>{!! Lang::get('lang.browser-notification') !!}</h3>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">

</ol>
@stop
@section('content')
	<div class="container well well-lg row">
		<button class="btn btn-info pull-right" id="edit-setting">EDIT</button><br><br>
		{!! Form::open([ 'method'=>'post', 'url'=> url('browser-notification/edit'), 'id'=>'browser-notification-setting']) !!}
		<!-- <form id="browser-notification-setting" url="{{url('browser-notification/edit')}}" method="POST"> -->
			<div class="col-md-2 col-xs-6 col-sm-6">
				<label>Browser Notification</label><br><br><br>
				<div>
					<label>Website Name</label><br><br>
					<label class="label label-primary">One Signal App ID</label><br><br>
					<label>API Key</label><br><br>
				</div>
			</div>
			<div class="col-md-10 col-xs-6 col-sm-6">
				@if($data->enabled)
					<input class="notification-enabled" name="enabled" type="checkbox" checked  data-toggle="toggle"><br><br>
				@else
					<input class="notification-enabled" name="enabled" type="checkbox" data-toggle="toggle"><br><br>
				@endif
				<div>
					<input type="text" name="" class="form-control" value="{{$data->name}}" disabled>
					<input type="text" name="one_signal_app_id" value="{{$data->app_id}}" class="form-control" disabled>
					<input type="text" name="one_signal_api_key" value="{{$data->api_key}}" class="form-control" disabled>
				</div>
			</div>
			<div class="col-md-12 col-xs-12"><br>
				<button class="btn btn-primary center-block hidden" id="update-btn"> UPDATE </button>
			</div>
		</form>	
	</div>

<script type="text/javascript">
	$('body').on('click', '#edit-setting', function(){
		$("#browser-notification-setting :input").prop("disabled", false);
		$(".notification-enabled").prop("disabled", false);
		$(this).prop("disabled", true);
		$("#update-btn").removeClass('hidden')
	})

	$('body').on('click', '#update-btn', function(){
		$("#browser-notification-setting").submit()
	
	})

</script>
@stop

