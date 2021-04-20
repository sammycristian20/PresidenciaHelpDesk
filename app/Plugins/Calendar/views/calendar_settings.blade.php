@extends('themes.default1.admin.layout.admin')

    @section('Plugins')
        active
    @stop

    @section('settings-bar')
        active
    @stop

    @section('plugin')
        class="active"
    @stop

    @section('HeadInclude')
    @stop
    <!-- header -->
    @section('PageHeader')
        <h1>{!! Lang::get('lang.plugins') !!}</h1>
    @stop
    <!-- /header -->
    <!-- breadcrumbs -->
    @section('breadcrumbs')
    <ol class="breadcrumb">
    </ol>
    @stop
    <!-- /breadcrumbs -->
    <!-- content -->

    @section('content')
    {{-- Success message --}}
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa  fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        {{-- failure message --}}
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
        <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
    		    <div class="box-header with-border">
        		    <h3 class="box-title">{!! Lang::get('Calendar::lang.general_settings') !!} </h3>
    		    </div><!-- /.box-header -->
    		    <div class="box-body ">
        		    {!!Form::open(['route'=>'post-cal-settings','method'=>'post'])!!}
       			    <div class="row">
        			    <div class="col-md-6 form-group" data-toggle="tooltip" title="{{Lang::get('Calendar::lang.create_due_event_tooltip')}}">
        			    {!! Form::label('create_due_event',Lang::get('Calendar::lang.create_due_event')) !!}
        			        <div class="row">
                        	    <div class="col-xs-6">
                            	    {!! Form::radio('create_due_event', 1, $cal_settings->create_due_event) !!} {{Lang::get('lang.yes')}}
                        	    </div>
                        	    <div class="col-xs-6">
                            	    {!! Form::radio('create_due_event', 0, !$cal_settings->create_due_event) !!} {{Lang::get('lang.no')}}
	                            </div>
     	                    </div>
        			    </div>
        			    <div class="col-md-6 form-group" data-toggle="tooltip" title="{{Lang::get('Calendar::lang.send_reminder_tooltip')}}">
            				{!!Form::label('send_reminder', Lang::get('Calendar::lang.send_reminder'))!!}
            				<div class="row">
                            	<div class="col-xs-6">
                                	{!! Form::radio('send_reminder', 1, $cal_settings->send_reminder) !!} {{Lang::get('lang.yes')}}
                        	    </div>
                             	<div class="col-xs-6">
                            	   {!! Form::radio('send_reminder', 0,!$cal_settings->send_reminder) !!} {{Lang::get('lang.no')}}
	                            </div>
     	                    </div>
        			    </div>
        			    <div class="col-md-6 form-group" data-toggle="tooltip" title="{{Lang::get('Calendar::lang.email_reminder_tooltip')}}">
            			    {!!Form::label('email_reminder', Lang::get('Calendar::lang.email_reminder'))!!}
        			        <div class="row">
                        	    <div class="col-xs-6">
                            	    {!! Form::radio('email_reminder', 1, $cal_settings->email_reminder) !!} {{Lang::get('lang.yes')}}
                        	    </div>
                        	    <div class="col-xs-6">
                            	    {!! Form::radio('email_reminder', 0, !$cal_settings->email_reminder) !!} {{Lang::get('lang.no')}}
	                            </div>
     	                    </div>
        			    </div>
        			    <div class="col-md-6 form-group" data-toggle="tooltip" title="{{Lang::get('Calendar::lang.sms_reminder_tooltip')}}">
        			        {!!Form::label('sms_reminder', Lang::get('Calendar::lang.sms_reminder'))!!}
        			        <div class="row">
                        	    <div class="col-xs-6">
                            	{!! Form::radio('sms_reminder', 1, $cal_settings->sms_reminder) !!} {{Lang::get('lang.yes')}}
                        	    </div>
                        	    <div class="col-xs-6">
                            	    {!! Form::radio('sms_reminder', 0, !$cal_settings->sms_reminder) !!} {{Lang::get('lang.no')}}
	                            </div>
     	                    </div>
        			    </div>
        		    </div>
                    <div class="row col-md-12">
                        <div class="box-header">
                            <h3 class="box-title">{!! Lang::get('Calendar::lang.reminder-cron') !!} </h3>
                        </div><!-- /.box-header -->
                        <div class="col-md-6 row">
                            <div class="info-box">
                            <!-- Apply any bg-* class to to the icon to color it -->
                                <span class="info-box-icon bg-aqua"><i class="fa fa-clock-o"></i></span>
                                <div class="info-box-content">
                                    <div class="col-md-6 {{ Session::has('fails') ? 'has-error' : '' }}" id="fetching">
                                    {!! Form::select('reminder-commands',$commands,$condition->getConditionValue('remind')['condition'],['class'=>'form-control','id'=>'reminder-command', 'onchange' => 'myFunction()']) !!}
                                        <div id='fetching-daily-at'>
                                        {!! Form::text('reminder-dailyAt',$condition->getConditionValue('remind')['at'],['class'=>'form-control', 'disabled' => true, 'placeholder' => 'hrs:mins', 'id' => 'reminder-dailyAt']) !!}

                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.info-box-content -->
                        </div>
                    </div><!-- /.info-box -->
    		    </div><!-- /.box-body -->
   	 		    <div class="box-footer with-border">
   	 			    {!!Form::submit(Lang::get('lang.save'), ['class' => 'btn btn-primary'])!!}
    			    {!!Form::close()!!}
    		    </div>
		    </div><!-- /. box -->
	    </div>
        </div>
        <script type="text/javascript">
            function myFunction()
            {
                var e = document.getElementById("reminder-command");
                var strUser = e.options[e.selectedIndex].value;
                if (strUser == 'dailyAt') {
                    $('#reminder-dailyAt').attr('disabled', false);
                } else {
                    $('#reminder-dailyAt').attr('disabled', true);
                }
            }
        </script>
    @stop