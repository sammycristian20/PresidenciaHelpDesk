@extends('themes.default1.admin.layout.admin')


@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<style>
	.tooltip1 {
		position: relative;
		/*display: inline-block;*/
		/*border-bottom: 1px dotted black;*/
	}
	.tooltip1 .tooltiptext {
		visibility: hidden;
		width: 100%;
		background-color: black;
		color: #fff;
		text-align: center;
		border-radius: 6px;
		padding: 5px 0;
		/* Position the tooltip */
		position: absolute;
		z-index: 1;
	}
	.tooltip1:hover .tooltiptext {
		visibility: visible;
	}
</style>

<link type="text/css" href="{{assetLink('css','bootstrap-datetimepicker4')}}" rel="stylesheet">
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
<!-- iCheck -->
<link href="{{assetLink('css','blue')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>
<!-- iCheck -->
<h1>{!! Lang::get('lang.department_profile') !!} </h1>

@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')

@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<!-- success message -->
<div id="alert-success" class="alert alert-success alert-dismissable" style="display:none;">
	<i class="fas fa-check-circle"> </i> <span id="get-success"></span>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
</div>
<!-- INfo message -->
<div id="alert-danger" class="alert alert-danger alert-dismissable" style="display:none;">
	<i class="fas fa-check-circle"> </i> <span id="get-danger"></span>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
</div>
@if(Session::has('success1'))
<div id="success-alert" class="alert alert-success alert-dismissable">
	<i class="fas  fa-check-circle"> </i>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{{Session::get('success1')}}
</div>
@endif
<!-- failure message -->
@if(Session::has('fails1'))
<div class="alert alert-danger alert-dismissable">
	<i class="fas fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!} ! </b>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{{Session::get('fails1')}}
</div>
@endif
<div class="row">
	<div class="col-md-4">
		<div class="card card-light card-outline" >
			
			<div class="card-body box-profile">
				<div class="text-center">
					<img src="{{assetLink('image','department')}} " class="profile-user-img img-fluid img-circle" alt="User Image">  
				</div>

				<h3 class="profile-username text-center" title="{{$departments->name}}" data-toggle="tooltip">
					{{str_limit($departments->name,20)}}</h3>

				<ul class="list-group list-group-unbordered">
						
					<li class="list-group-item">
							 
						<label>{{Lang::get('lang.department_size')}}</label> 

						<a class="badge badge-primary text-white float-right">{{$department_members->count()}}</a>
					</li>

					 @if($departments->businessHour()->first())
					 <li class="list-group-item">
							 
						<label>{{Lang::get('lang.business-hour')}}</label> 

						<a class="float-right" title="{{$departments->businessHour()->first()->name}}" data-toggle="tooltip">{{str_limit($departments->businessHour()->first()->name,10)}}</a>
					</li>
					@endif
				</ul>
			</div>
		</div>
	  <br/>


   
			@if($deptManagerDetails != null)
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php foreach ($deptManagerDetails AS $agentDetail): ?>
						   <?php
						   $manager_name    = ($agentDetail->first_name != '' || $agentDetail->first_name
							!= null) ? $agentDetail->first_name . ' ' . $agentDetail->last_name
								: $agentDetail->user_name;

								$onerrorImage = assetLink('image','contacthead');
							?>


	<div class="card card-widget widget-user-2">
		
		<div class="widget-user-header bg-warning pr-2 pt-2 pl-2 pb-0">
			
			<div class="widget-user-image">
				
				<img class="img-circle elevation-2"  src="{{ Gravatar::src( $agentDetail->email) }}" onerror="this.src='{{$onerrorImage}}'" alt="User Avatar">
			</div>
			
			<h3 class="widget-user-username" title="{{$manager_name}}" data-toggle="tooltip">
			{{ucfirst(str_limit($manager_name,15))}}</h3>
			
			<p class="widget-user-desc">{!! Lang::get('lang.department_manager') !!}</p>
		</div>
		
		<div class="card-footer p-0">
			
			<ul class="nav flex-column">
				
				<li class="nav-item">
					
					<a href="javascript:;" class="nav-link text-dark"> {!! Lang::get('lang.e-mail') !!}

						<span class="float-right"  title="{{$agentDetail->email}}" data-toggle="tooltip">
							{!! str_limit($agentDetail->email,15) !!}</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
<?php endforeach; ?>
@endif



 </div>

	<div class="col-md-8">
		<div class="card card-light">

			<div class="card-header">

				<h3 class="card-title">{!! Lang::get('lang.list_of_department_members')!!}</h3>
				
			</div>   
			<div class="card-body">
				<table id="users-table" class="table table-bordered display" cellspacing="0" width="100%" styleClass="borderless">

					<thead><tr>
							<th>{!! Lang::get('lang.name') !!}</th>
							<th>{!! Lang::get('lang.email') !!}</th>
							<th>{!! Lang::get('lang.status') !!}</th>
							<th>{!! Lang::get('lang.action') !!}</th>
						</tr></thead>


				</table>
			</div>    
		</div>
		
		<?php
			$open = $tickets->where('dept_id', '=', $departments->id)->whereIn('status', getStatusArray('open'))->count();
			$unapproved = $tickets->where('dept_id', '=', $departments->id)->whereIn('status', getStatusArray('unapproved'))->count();
			$counted = $tickets->where('dept_id', '=', $departments->id)->whereIn('status', getStatusArray('closed'))->count();
			$deleted = $tickets->where('dept_id', '=', $departments->id)->whereIn('status', getStatusArray('deleted'))->count();
		?>

		<div class="card card-light card-outline card-outline-tabs">
			
			<div class="card-header p-0 border-bottom-0">
				
				<ul class="nav nav-tabs" role="tablist">
				
				  <li class="nav-item">
					<a class="nav-link active" id="open_tab" data-toggle="pill" href="#tab_1" role="tab">
						{!! Lang::get('lang.open_tickets') !!} ({{$open}})</a>
					</a>
				  </li>  

				  	<li class="nav-item">
						<a href="#tab_2" data-toggle="pill" role="tab"  id="closed_tab" class="nav-link">
						{!! Lang::get('lang.closed_tickets') !!} ({{$counted}})</a>
					</li>

					<li class="nav-item">
						<a href="#tab_3" data-toggle="pill" role="tab"  id="deleted_tab" class="nav-link">
						{!! Lang::get('lang.deleted_tickets') !!} ({{$deleted}})</a>
					</li>

					<li class="nav-item">
						<a href="#tab_4" id="unapproved_tab" data-toggle="pill" role="tab" class="nav-link">
						{!! Lang::get('lang.unapproved_tickets') !!} ({{$unapproved}})</a>
					</li>
				</ul>
			 </div>

			<div class="card-body">
				
				<div class="tab-content">
				
				  <div class="tab-pane active" role="tabpanel" >
					 @if(Session::has('success'))
					<div id="success-alert" class="alert alert-success alert-dismissable">
						<i class="fas  fa-check-circle"> </i>
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						{{Session::get('success')}}
					</div>
					@endif
					<!-- failure message -->
					@if(Session::has('fails'))
					<div class="alert alert-danger alert-dismissable">
						<i class="fas fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!} ! </b>
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						{{Session::get('fails')}}
					</div>
					@endif

					<div class="mailbox-controls">
					</div>
					<div class="tab-pane active" id="tab_1">
					</div>
					<div class="tab-pane active" id="tab_2">
					</div>
					<div class="tab-pane active" id="tab_3">
					</div>

					<div class="mb-4">

						<button type="button" class="btn btn-sm btn-default text-green" id="Edit_Ticket" data-toggle="modal" 
							data-target="#MergeTickets"><i class="fas fa-code-branch"> </i> {!! Lang::get('lang.merge') !!}
						</button>

						<?php $inputs   = Input::all(); ?>
						
						<div class="btn-group">
							<?php $statuses = Finder::getCustomedStatus(); ?>
							<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" id="d1">
								<i class="fas fa-exchange-alt" style="color:teal;" id="hidespin"> </i>
								<i class="fas fa-spinner fa-spin" style="color:teal; display:none;" id="spin"></i>
								{!! Lang::get('lang.change_status') !!}
							</button>
							
							<div class="dropdown-menu status-list">
								@foreach($statuses as $ticket_status)    
								<a onclick="changeStatus({!! $ticket_status -> id !!}, '{!! $ticket_status->name !!}')" 
									class="dropdown-item" href="javascript:;">
									<i class="{!! $ticket_status->icon !!}" style="color:{!! $ticket_status->icon_color !!};"> </i> {!! $ticket_status->name !!}
								</a>
								@endforeach
							</div>
						</div>

						<button type="button" class="btn btn-sm btn-default" id="assign_Ticket" data-toggle="modal" 
							data-target="#AssignTickets" style="display: none;">
							<i class="fas fa-hand-point-right"> </i> {!! Lang::get('lang.assign') !!}
						</button>
					</div>

					<div class="mailbox-messages"  id="refresh">
						<table id="chumper" class="table table-bordered dataTable no-footer">
							<thead>
							   <tr>
									<td><a class="checkbox-toggle"><i class="far fa-square fa-2x"></i></a></td>
									<td>{!! Lang::get('lang.subject') !!}</td>
									<td>{!! Lang::get('lang.ticket_id') !!}</td>
									<td>{!! Lang::get('lang.from') !!}</td>
									<td>{!! Lang::get('lang.assigned_to') !!}</td>
									<td>{!! Lang::get('lang.last_activity') !!}</td>
								</tr>
						</table>
					</div><!-- /.mail-box-messages -->
				  </div>
				</div>
			</div>
			  <!-- /.card -->
		</div>
		
				<div class="card card-light">
					<div class="card-header">
				   
					  
						 <h3 class="card-title">{!! Lang::get('lang.department_report') !!}</h3>
						 

					</div>
					<div class="card-body">
						<form id="foo">
							<div  class="form-group">
								<div class="row">
									<div class='col-sm-4'>
						{!! Form::label('date', Lang::get('lang.start_date'),['class' => '']) !!}
						{!! Form::text('start_date',null,['class'=>'form-control','id'=>'datepicker4', 'placeholder' => 'YYYY/mm/dd'])!!}
					</div>
					<?php
					$start_date = App\Model\helpdesk\Ticket\Tickets::where('id', '=', '1')->first();
					if ($start_date != null) {
						$created_date = $start_date->created_at;
						$created_date = explode(' ', $created_date);
						$created_date = $created_date[0];
						$start_date = date("m/d/Y", strtotime($created_date . ' -1 months'));
					} else {
						$start_date = date("m/d/Y", strtotime(date("m/d/Y") . ' -1 months'));
					}
					?>
					
					<div class='col-sm-4'>
						{!! Form::label('start_time',Lang::get('lang.end_date'),['class' => '']) !!}
						{!! Form::text('end_date',null,['class'=>'form-control','id'=>'datetimepicker3', 'placeholder' => 'YYYY/mm/dd'])!!}
					</div>
					<script type="text/javascript">
						$(function () {
							$('#datepicker4, #datetimepicker3').keypress(function (e) {
								var regex = new RegExp("^[0-9-/]+$");
								var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
								if (regex.test(str)) {
									return true;
								}
								e.preventDefault();
								return false;
							});
							var timestring1 = "{!! $start_date !!}";
							var timestring2 = "{!! date('Y-m-d') !!}";
							$('#datepicker4').datetimepicker({
								format: 'YYYY/MM/DD',
								maxDate: moment(timestring2).startOf('day'),
							});
							$('#datetimepicker3').datetimepicker({
								format: 'YYYY/MM/DD',
								useCurrent: false, //Important! See issue #1075
								maxDate: moment(timestring2).startOf('day')
							});
							$("#datepicker4").on("dp.change", function (e) {
								$('#datetimepicker3').data("DateTimePicker").minDate(e.date).maxDate(moment(timestring2).startOf('day'));
							});
							$("#datetimepicker3").on("dp.change", function (e) {
								$('#datepicker4').data("DateTimePicker").maxDate(e.date);
							});
						});
					</script>
					<div class='col-sm-3' style='margin-top:30px'>
						<!-- {!! Form::label('filter', Lang::get('lang.filter'),['class' => 'lead hide']) !!}<br> -->
						{!! Form::submit(Lang::get('lang.submit'),['class'=>'form-group btn btn-primary','id'=>'filter-form'])!!}  
						<!-- <button class="btn btn-primary" id="filter-form">Submit</button> -->
					</div>
									<div class="col-sm-10">
										<!-- <label class="lead hide">{!! Lang::get('lang.Legend') !!}:</label> -->
										<div class="row">
											<style>
												#legend-holder { border: 1px solid #ccc; float: left; width: 25px; height: 25px; margin: 1px; }
											</style>

						   
						<div class="col-md-4"><span id="legend-holder" style="background-color: #6C96DF;"></span>&nbsp; <span class="lead"> <span id="total-created-tickets" ></span> {!! Lang::get('lang.assign_tickets') !!}  </span></div>
						 
					

 <div class="col-md-4"><span id="legend-holder" style="background-color: #6DC5B2;"></span>&nbsp; <span class="lead"> <span id="total-reopen-tickets" class="lead"></span> {!! Lang::get('lang.tickets') !!} {!! Lang::get('lang.reopen') !!}  </span></div> 
											<div class="col-md-4"><span id="legend-holder" style="background-color: #E3B870;"></span>&nbsp; <span class="lead"> <span id="total-closed-tickets" class="lead"></span> {!! Lang::get('lang.tickets') !!} {!! Lang::get('lang.closed') !!}  </span></div>  
										</div>
									</div>
								</div>
							</div>
						</form>
						<div id="legendDiv"></div>
						<div class="chart">
							<canvas class="chart-data" id="tickets-graph" width="1000" height="250"></canvas>   
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
	</div>
	@include('themes.default1.agent.helpdesk.ticket.more.tickets-model')
	<!-- page script -->   
	@include('themes.default1.agent.helpdesk.ticket.more.tickets-options-script')
	@stop
	@push('scripts')
	@include('vendor.yajra.tuser-javascript')
	<!--  -->

	 <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
 <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
 <script src="{{assetLink('js','moment')}}" type="text/javascript"></script>
<script src="{{assetLink('js','bootstrap-datetimepicker4')}}" type="text/javascript"></script>
<script src="{{assetLink('js','chart-min')}}" type="text/javascript"></script>
<!--         iCheck -->
<script src="{{assetLink('js','iCheck')}}" type="text/javascript" async></script>

	<script>
		$(function () {
			$('#datepicker4, #datetimepicker3').on('focus', function(){
				$('.col-sm-4').removeClass('has-error');
			});
		});
		function confirmDelete(id) {
			var check = confirm('Are you sure?');
			if (check == true) {
				window.location = '{!! url("delete") !!}/' + id;
			} else {
				return false;
			}
		}
	</script>
	<script>
		$('#users-table').DataTable({
			processing: true,
			serverSide: true,
			ajax: '{!! route('department.userprofile.show',$departments->id) !!}',
			"oLanguage": {
				"sLengthMenu": "_MENU_ Records per page",
				"sSearch"    : "Search: ",
				"sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
			},
			columns: [
				{data: 'user_name', name: 'user_name'},
				{data: 'email', name: 'email'},
				{data: 'active', name: 'active'},
				{data: 'actions', name: 'action'}
			],
			"fnDrawCallback": function( oSettings ) {
				$('.loader').css('display', 'none');
			},
			"fnPreDrawCallback": function(oSettings, json) {
				$('.loader').css('display', 'block');
			},
		});
  
	$(document).ready(function () { /// Wait till page is loaded
			$('#click').click(function () {
				$('#refresh').load('open #refresh');
				$("#show").show();
			});
		});
		</script>
	<div id="refresh"> 
	   
	</div>
	
	<script type="text/javascript">
		$(document).ready(function () {
			 $.getJSON("../dept-chart/<?php echo $departments->id; ?>", function (result) {
				var labels = [], open = [], closed = [], reopened = [], open_total = 0, closed_total = 0, reopened_total = 0;
				for (var i = 0; i < result.length; i++) {
					labels.push(result[i].date);
					open.push(result[i].open);
					closed.push(result[i].closed);
					reopened.push(result[i].reopened);
					open_total += parseInt(result[i].open);
					closed_total += parseInt(result[i].closed);
					reopened_total += parseInt(result[i].reopened);
				}
				var buyerData = {
					labels: labels,
					datasets: [
						{
							label: "Open Tickets",
							fillColor: "rgba(93, 189, 255, 0.05)",
							strokeColor: "rgba(2, 69, 195, 0.9)",
							pointColor: "rgba(2, 69, 195, 0.9)",
							pointStrokeColor: "#c1c7d1",
							pointHighlightFill: "#fff",
							pointHighlightStroke: "rgba(220,220,220,1)",
							data: open
						}
						, {
							label: "Closed Tickets",
							fillColor: "rgba(255, 206, 96, 0.08)",
							strokeColor: "rgba(221, 129, 0, 0.94)",
							pointColor: "rgba(221, 129, 0, 0.94)",
							pointStrokeColor: "rgba(60,141,188,1)",
							pointHighlightFill: "#fff",
							pointHighlightStroke: "rgba(60,141,188,1)",
							data: closed
						}
						, {
							label: "Reopened Tickets",
							fillColor: "rgba(104, 255, 220, 0.06)",
							strokeColor: "rgba(0, 149, 115, 0.94)",
							pointColor: "rgba(0, 149, 115, 0.94)",
							pointStrokeColor: "rgba(60,141,188,1)",
							pointHighlightFill: "#fff",
							pointHighlightStroke: "rgba(60,141,188,1)",
							data: reopened
						}
					]
				};
				$("#total-created-tickets").html(open_total);
				$("#total-reopen-tickets").html(reopened_total);
				$("#total-closed-tickets").html(closed_total);
				var myLineChart = new Chart(document.getElementById("tickets-graph").getContext("2d")).Line(buyerData, {
					showScale: true,
					//Boolean - Whether grid lines are shown across the chart
					scaleShowGridLines: false,
					//String - Colour of the grid lines
					scaleGridLineColor: "rgba(0,0,0,.05)",
					//Number - Width of the grid lines
					scaleGridLineWidth: 1,
					//Boolean - Whether to show horizontal lines (except X axis)
					scaleShowHorizontalLines: true,
					//Boolean - Whether to show vertical lines (except Y axis)
					scaleShowVerticalLines: true,
					//Boolean - Whether the line is curved between points
					bezierCurve: true,
					//Number - Tension of the bezier curve between points
					bezierCurveTension: 0.3,
					//Boolean - Whether to show a dot for each point
					pointDot: true,
					//Number - Radius of each point dot in pixels
					pointDotRadius: 1,
					//Number - Pixel width of point dot stroke
					pointDotStrokeWidth: 1,
					//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
					pointHitDetectionRadius: 10,
					//Boolean - Whether to show a stroke for datasets
					datasetStroke: true,
					//Number - Pixel width of dataset stroke
					datasetStrokeWidth: 1,
					//Boolean - Whether to fill the dataset with a color
					datasetFill: true,
					//String - A legend template
					//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
					maintainAspectRatio: true,
					//Boolean - whether to make the chart responsive to window resizing
					responsive: true,
				});
			});
			$('#click me').click(function () {
				$('#foo').submit();
			});
			
			$('#foo').submit(function (event) {
				// get the form data
				// there are many ways to get this data using jQuery (you can use the class or id also)
				var date1 = $('#datepicker4').val();
				var date2 = $('#datetimepicker3').val();
				if(date1 == '' || date2 == '') {
					$('.col-sm-4').addClass('has-error');
					alert('{{Lang::get("lang.please-select-a-valid-date-range")}}');
					return false;
				}
				var formData = date1.split("/").join('-');
				var dateData = date2.split("/").join('-');
				// process the form
				$.ajax({
					type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
					url: '../dept-chart-range/<?php echo $departments->id; ?>/' + dateData + '/' + formData, // the url where we want to POST
					data: {formData, "_token": "{{ csrf_token() }}"}, // our data object
					dataType: 'json', // what type of data do we expect back from the server
					beforeSend: function() {
						$('.loader').css('display','block');
					},
					complete: function(){
						$('.loader').css('display','none');
					},
					success: function (result2) {
						var labels = [], open = [], closed = [], reopened = [], open_total = 0, closed_total = 0, reopened_total = 0;
						for (var i = 0; i < result2.length; i++) {
							labels.push(result2[i].date);
							open.push(result2[i].open);
							closed.push(result2[i].closed);
							reopened.push(result2[i].reopened);
							open_total += parseInt(result2[i].open);
							closed_total += parseInt(result2[i].closed);
							reopened_total += parseInt(result2[i].reopened);
						}
						var buyerData = {
							labels: labels,
							datasets: [
								{
									label: "Open Tickets",
									fillColor: "rgba(93, 189, 255, 0.05)",
									strokeColor: "rgba(2, 69, 195, 0.9)",
									pointColor: "rgba(2, 69, 195, 0.9)",
									pointStrokeColor: "#c1c7d1",
									pointHighlightFill: "#fff",
									pointHighlightStroke: "rgba(220,220,220,1)",
									data: open
								}
								, {
									label: "Closed Tickets",
									fillColor: "rgba(255, 206, 96, 0.08)",
									strokeColor: "rgba(221, 129, 0, 0.94)",
									pointColor: "rgba(221, 129, 0, 0.94)",
									pointStrokeColor: "rgba(60,141,188,1)",
									pointHighlightFill: "#fff",
									pointHighlightStroke: "rgba(60,141,188,1)",
									data: closed
								}
								, {
									label: "Reopened Tickets",
									fillColor: "rgba(104, 255, 220, 0.06)",
									strokeColor: "rgba(0, 149, 115, 0.94)",
									pointColor: "rgba(0, 149, 115, 0.94)",
									pointStrokeColor: "rgba(60,141,188,1)",
									pointHighlightFill: "#fff",
									pointHighlightStroke: "rgba(60,141,188,1)",
									data: reopened
								}
							]
						};
						$("#total-created-tickets").html(open_total);
						$("#total-reopen-tickets").html(reopened_total);
						$("#total-closed-tickets").html(closed_total);
						var myLineChart = new Chart(document.getElementById("tickets-graph").getContext("2d")).Line(buyerData, {
							showScale: true,
							//Boolean - Whether grid lines are shown across the chart
							scaleShowGridLines: false,
							//String - Colour of the grid lines
							scaleGridLineColor: "rgba(0,0,0,.05)",
							//Number - Width of the grid lines
							scaleGridLineWidth: 1,
							//Boolean - Whether to show horizontal lines (except X axis)
							scaleShowHorizontalLines: true,
							//Boolean - Whether to show vertical lines (except Y axis)
							scaleShowVerticalLines: true,
							//Boolean - Whether the line is curved between points
							bezierCurve: true,
							//Number - Tension of the bezier curve between points
							bezierCurveTension: 0.3,
							//Boolean - Whether to show a dot for each point
							pointDot: true,
							//Number - Radius of each point dot in pixels
							pointDotRadius: 1,
							//Number - Pixel width of point dot stroke
							pointDotStrokeWidth: 1,
							//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
							pointHitDetectionRadius: 10,
							//Boolean - Whether to show a stroke for datasets
							datasetStroke: true,
							//Number - Pixel width of dataset stroke
							datasetStrokeWidth: 1,
							//Boolean - Whether to fill the dataset with a color
							datasetFill: true,
							//String - A legend template
							//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
							maintainAspectRatio: true,
							//Boolean - whether to make the chart responsive to window resizing
							responsive: true,
						});
						myLineChart.options.responsive = false;
						$("#tickets-graph").remove();
						$(".chart").html("<canvas id='tickets-graph' width='1000' height='250'></canvas>");
						var myLineChart1 = new Chart(document.getElementById("tickets-graph").getContext("2d")).Line(buyerData, {
							showScale: true,
							//Boolean - Whether grid lines are shown across the chart
							scaleShowGridLines: false,
							//String - Colour of the grid lines
							scaleGridLineColor: "rgba(0,0,0,.05)",
							//Number - Width of the grid lines
							scaleGridLineWidth: 1,
							//Boolean - Whether to show horizontal lines (except X axis)
							scaleShowHorizontalLines: true,
							//Boolean - Whether to show vertical lines (except Y axis)
							scaleShowVerticalLines: true,
							//Boolean - Whether the line is curved between points
							bezierCurve: true,
							//Number - Tension of the bezier curve between points
							bezierCurveTension: 0.3,
							//Boolean - Whether to show a dot for each point
							pointDot: true,
							//Number - Radius of each point dot in pixels
							pointDotRadius: 1,
							//Number - Pixel width of point dot stroke
							pointDotStrokeWidth: 1,
							//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
							pointHitDetectionRadius: 10,
							//Boolean - Whether to show a stroke for datasets
							datasetStroke: true,
							//Number - Pixel width of dataset stroke
							datasetStrokeWidth: 1,
							//Boolean - Whether to fill the dataset with a color
							datasetFill: true,
							//String - A legend template
							//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
							maintainAspectRatio: true,
							//Boolean - whether to make the chart responsive to window resizing
							responsive: true,
						});
					}
				});
				// using the done promise callback
				// stop the form from submitting the normal way and refreshing the page
				event.preventDefault();
			});
		});
</script>
<script>
		$('#assign_Ticket').on('click', function (e) {
			window.scrollTo(0,0)
		});
	</script>
	

@endpush
