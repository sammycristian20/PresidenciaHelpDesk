@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.notification_settings-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.notification_settings-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('notification')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.settings') !!}</h1>
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
<!-- open a form -->
@if(Session::has('success'))
                <div class="alert alert-success alert-dismissable">
                    <i class="fas fa-check-circle"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {!!Session::get('success')!!}
                </div>
                @endif
                <!-- failure message -->
                @if(Session::has('fails'))
                <div class="alert alert-danger alert-dismissable">
                    <i class="fas fa-ban"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <b> {!! Lang::get('lang.alert') !!} ! </b>
                    <li class="error-message-padding">{!!Session::get('fails')!!}</li>
                </div>
                @endif
<div class="row">
    <div class="col-md-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{Lang::get('lang.notification_settings')}}</h3>
            </div>
            <!-- check whether success or not -->
            <div class="card-body">
                
                <div class="row">
                    <!-- Default System Email:	DROPDOWN value from emails table : Required -->
                    
                        <div class="col-md-3 no-padding">
                            <div class="form-group">
                                {!! Form::label('del_noti', Lang::get('lang.delete_noti')) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('delete-read-notification') }}" style="color:red;"><i class='fa fa-trash'>&nbsp;&nbsp;</i>{!! Lang::get('lang.del_all_read') !!}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 no-padding">
                            <div class="form-group">
                                {!! Form::label('del_noti', Lang::get('lang.noti_msg1')) !!}<span class="text-red"> *</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ url('delete-notification-log') }}" method="post" id="Form">
                                {{ csrf_field() }}
                                <div class="callout callout-default bg-light" style="font-style: oblique;">{!! Lang::get('lang.noti_msg2') !!}</div>
                                <input type="number" class="form-control" name='no_of_days' placeholder="{!! lang::get('lang.enter_no_of_days') !!}" id ='test' min='1'>


<!--                                <button type="submit" class="btn btn-primary">{!! Lang::get('lang.submit') !!}</button>-->
                                    <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fa fa-circle-notch fa-spin'>&nbsp;</i>Deleting..."><i class="fas fa-trash">&nbsp;</i>{!!Lang::get('lang.delete')!!}</button>
                            
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
 
$("#test").keyup(function() {
    var val = $("#test").val();
    if(parseInt(val) < 0 || isNaN(val)) {
    alert("please enter valid values");
        $("#test").val("");
        $("#test").focus();
    }
});
</script>

@stop