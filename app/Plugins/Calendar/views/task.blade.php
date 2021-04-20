@extends('themes.default1.agent.layout.agent')
@section('content')
<?php
	$data = [
		"0" => "select user",
		"1" => "sadasd",
		"2" => "dfgsfgsf"
	];
?>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
	{!! Form::open(['url' => 'task/', 'method' => 'post']) !!}

	{{Form::label('task_name', 'Task Name')}} 
	{{ Form::text('task_name') }}<br><br>
	{{Form::label('task_description', 'Task Description')}} 
	{{ Form::textarea ('task_description', null) }}<br><br>
	{{Form::label('task_start_date', 'Start At')}} 
	{{ Form::text('task_start_date') }}<br><br>
	{{Form::label('task_name', 'Ends At')}} 
	{{ Form::text('task_end_date') }}<br><br>
	{{Form::label('repeat alert', 'Task Alert')}} 
	{{ Form::select('alert_repeat', array('never' => 'NEVER', 'daily' => 'DAILY','weekly' => 'WEEKLY', 'monthly' => 'MONTHLY')) }}<br><br>
	{{Form::label('assignee', 'Assign to')}}  
	@if(isset($users))
		{{ Form::select('assignee', $users) }}<br><br>
	@endif
	{{ Form::submit('Create Task', ["class" => 'btn btn-primary btn-xs' ]) }}




	{!! Form::close() !!}
@stop