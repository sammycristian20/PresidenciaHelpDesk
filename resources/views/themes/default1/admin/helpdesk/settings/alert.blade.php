@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.alert_notices_setitngs-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.alert_notices_setitngs-page-description') !!}">


@stop

@section('Tickets')
active
@stop

@section('tickets-bar')
active
@stop

@section('alert')
class="active"
@stop

@section('HeadInclude')
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
<style>
    #input-ticket-status{
        width:100%;
    }
    .hidden {
        display: none!important;
    }
</style>
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.settings')}}</h1>
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
{!! Form::open(['url' => 'alert', 'method' => 'PATCH','id'=>'Form','class'=>'alertTemplateForm']) !!}
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
    <i class="fas fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <b>{!! lang::get('lang.alert') !!}!</b><br/>
    {!!Session::get('fails')!!}
</div>
@endif

<div class="card card-light">
    
    <div class="card-header">
    
        <h3 class="card-title"> {{Lang::get('lang.alert_notices_setitngs')}}</h3> 

        <div class="card-tools">
            
             <button type="submit" class="btn btn-tool btn-default" id="submit" title="{!!Lang::get('lang.save')!!}" data-toggle="tooltip">
                <i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}
            </button>
        </div>
    </div>


<div class="card-body">
<div class="row">
    <div class="col-md-6">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.new_ticket_alert')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('new_ticket_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('new_ticket_alert',1,$alerts->isValueExists('new_ticket_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('new_ticket_alert',0,$alerts->isValueExists('new_ticket_alert',0)) !!}  {!! Lang::get('lang.disable') !!}
                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('new_ticket_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('new_ticket_alert_mode[]','email',$alerts->isValueExists('new_ticket_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['new_ticket_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif

                        {!! Form::checkbox('new_ticket_alert_mode[]','system',$alerts->isValueExists('new_ticket_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>
                    <div class="form-group">
                        <!--Client -->
                        {{--<div class="form-group">
                            {!! Form::checkbox('new_ticket_alert_persons[]','client',$alerts->isValueExists('new_ticket_alert_persons','client')) !!}
                            {!! Form::label('new_ticket_alert_persons',Lang::get('lang.client')) !!}
                        </div>--}}
                        <!-- Admin Email -->
                        {!! Form::checkbox('new_ticket_alert_persons[]','admin',$alerts->isValueExists('new_ticket_alert_persons','admin')) !!}
                        {!! Form::label('ticket_admin_email',Lang::get('lang.admin')) !!}
                    </div>
                    <!-- Department Members -->
                    <div class="form-group">
                        {!! Form::checkbox('new_ticket_alert_persons[]','department_members',$alerts->isValueExists('new_ticket_alert_persons','department_members')) !!}
                        {!! Form::label('ticket_department_member',Lang::get('lang.department_members')) !!}
                    </div>

                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('new_ticket_alert_persons[]','department_manager',$alerts->isValueExists('new_ticket_alert_persons','department_manager')) !!}
                        {!! Form::label('ticket_department_manager',Lang::get('lang.department_manager')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->

        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.ticket_assignment_alert')}}</h3>
                </div><!-- /.box-header -->
                <div class="card-body">
                    <!-- Status:     Enable      Disable      -->
                    <div class="form-group">
                        {!! Form::label('ticket_assign_alert',Lang::get('lang.status').":") !!}
                        {!! Form::radio('ticket_assign_alert',1,$alerts->isValueExists('ticket_assign_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('ticket_assign_alert',0,$alerts->isValueExists('ticket_assign_alert',0)) !!}  {!! Lang::get('lang.disable') !!}
                    </div>

                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('ticket_assign_alert_mode[]',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('ticket_assign_alert_mode[]','email',$alerts->isValueExists('ticket_assign_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp;
                        <?php $sms = \Event::dispatch('sms_option', [['ticket_assign_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                        
                        {!! Form::checkbox('ticket_assign_alert_mode[]','system',$alerts->isValueExists('ticket_assign_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>

                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','client',$alerts->isValueExists('ticket_assign_alert_persons','client')) !!}
                        {!! Form::label('ticket_assign_alert_agent',Lang::get('lang.client')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','admin',$alerts->isValueExists('ticket_assign_alert_persons','admin')) !!}
                        {!! Form::label('ticket_assign_alert_admin',Lang::get('lang.admin')) !!}
                    </div>

                    <!-- Assigned Agent / Team -->
                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','agent',$alerts->isValueExists('ticket_assign_alert_persons','agent')) !!}
                        {!! Form::label('ticket_assign_alert_agent',Lang::get('lang.agent'))!!}<!-- <a href="#" style="font-size:12px;" > <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-container: 'body' title="" data-original-title="Agent"></span></a> -->
 
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','assigned_agent_team',$alerts->isValueExists('ticket_assign_alert_persons','assigned_agent_team')) !!}
                        {!! Form::label('assignment_alert_persons',Lang::get('lang.assigned_agent')) !!}
                    </div>

                    <!-- Department -->
                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','department_members',$alerts->isValueExists('ticket_assign_alert_persons','department_members')) !!}
                        {!! Form::label('ticket_assign_alert_agent',Lang::get('lang.department_members')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','department_manager',$alerts->isValueExists('ticket_assign_alert_persons','department_manager')) !!}
                        {!! Form::label('assignment_alert_persons',Lang::get('lang.department_manager')) !!}
                    </div>
                    <!-- End Department -->
                    <!-- Team Members -->
                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','team_members',$alerts->isValueExists('ticket_assign_alert_persons','team_members')) !!}
                        {!! Form::label('assignment_team_member',Lang::get('lang.team_members')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','team_lead',$alerts->isValueExists('ticket_assign_alert_persons','team_lead')) !!}
                        {!! Form::label('assignment_team_member',Lang::get('lang.team_lead')) !!}
                    </div>

                    <!-- Organization Account Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('ticket_assign_alert_persons[]','organization_manager',$alerts->isValueExists('ticket_assign_alert_persons','organization_manager')) !!}
                        {!! Form::label('organization_account_manager',Lang::get('lang.organization_account_manager')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!--/.col (left) -->
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.daily_report')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('notification_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('notification_alert',1,$alerts->isValueExists('notification_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('notification_alert',0,$alerts->isValueExists('notification_alert',0)) !!}  {!! Lang::get('lang.disable') !!}

                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('notification_alert_mode[]',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('notification_alert_mode[]','email',$alerts->isValueExists('notification_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        
                        {!! Form::checkbox('notification_alert_mode[]','system',$alerts->isValueExists('notification_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>
                    <div class="form-group">
                        <!-- Last responds -->
                        {!! Form::checkbox('notification_alert_persons[]','admin',$alerts->isValueExists('notification_alert_persons','admin')) !!}
                        {!! Form::label('last_respondent',Lang::get('lang.admin')) !!}
                    </div>
                    <!-- Assigned Agent/Team -->
                    <div class="form-group">
                        {!! Form::checkbox('notification_alert_persons[]','agent',$alerts->isValueExists('notification_alert_persons','agent')) !!}
                        {!! Form::label('assigned_agent_team',Lang::get('lang.agent')) !!}
                    </div>

                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('notification_alert_persons[]','department_manager',$alerts->isValueExists('notification_alert_persons','department_manager')) !!}
                        {!! Form::label('ticket_department_manager',Lang::get('lang.department_manager')) !!}
                    </div>
                    <!-- Organization Account Manager -->
                    {{--
                <div class="form-group">
                    {!! Form::checkbox('notification_alert_persons[]','organization_manager',$alerts->isValueExists('notification_alert_persons','organization_manager')) !!}
                    {!! Form::label('organization_account_manager',Lang::get('lang.organization_account_manager')) !!}
                </div>
                --}}
                    <div class="form-group">
                        {!! Form::checkbox('notification_alert_persons[]','team_lead',$alerts->isValueExists('notification_alert_persons','team_lead')) !!}
                        {!! Form::label('organization_account_manager',Lang::get('lang.team_lead')) !!}
                    </div>



                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->


        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.new_ticket_confirmation_alert')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('new_ticket_confirmation_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('new_ticket_confirmation_alert',1,$alerts->isValueExists('new_ticket_confirmation_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('new_ticket_confirmation_alert',0,$alerts->isValueExists('new_ticket_confirmation_alert',0)) !!}  {!! Lang::get('lang.disable') !!}

                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('new_ticket_confirmation_alert_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('new_ticket_confirmation_alert_mode[]','email',$alerts->isValueExists('new_ticket_confirmation_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['new_ticket_confirmation_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                        
                        {!! Form::checkbox('new_ticket_confirmation_alert_mode[]','system',$alerts->isValueExists('new_ticket_confirmation_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>
                    <div class="form-group">

                        {!! Form::checkbox('new_ticket_confirmation_alert_persons[]','client',$alerts->isValueExists('new_ticket_confirmation_alert_persons','client')) !!}
                        {!! Form::label('new_ticket_confirmation_alert_persons',Lang::get('lang.client')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('new_ticket_confirmation_alert_persons[]','organization_manager',$alerts->isValueExists('new_ticket_confirmation_alert_persons','organization_manager')) !!}
                        {!! Form::label('new_ticket_confirmation_alert_persons',Lang::get('lang.organization_account_manager')) !!}

                    </div>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->

        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.new_internal_activity_alert')}}</h3>
                </div><!-- /.box-header -->
                <div class="card-body">
                    <!-- Status:     Enable      Disable      -->
                    <div class="form-group">
                        {!! Form::label('internal_activity_alert',Lang::get('lang.status').":") !!}
                        {!! Form::radio('internal_activity_alert',1,$alerts->isValueExists('internal_activity_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('internal_activity_alert',0,$alerts->isValueExists('internal_activity_alert',0)) !!}  {!! Lang::get('lang.disable') !!}
                    </div>

                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('internal_activity_alert_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('internal_activity_alert_mode[]','email',$alerts->isValueExists('internal_activity_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['internal_activity_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                        
                        {!! Form::checkbox('internal_activity_alert_mode[]','system',$alerts->isValueExists('internal_activity_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>

                    <!-- Assigned Agent -->
                    <div class="form-group">
                        {!! Form::checkbox('internal_activity_alert_persons[]','agent',$alerts->isValueExists('internal_activity_alert_persons','agent')) !!}
                        {!! Form::label('assignment_assigned_agent',Lang::get('lang.agent')) !!}
                    </div>
                    <!-- admin -->
                    <div class="form-group">
                        {!! Form::checkbox('internal_activity_alert_persons[]','admin',$alerts->isValueExists('internal_activity_alert_persons','admin')) !!}
                        {!! Form::label('assignment_assigned_agent',Lang::get('lang.admin')) !!}
                    </div>
                    <!-- end admin -->

                    <!-- department members -->
                    <div class="form-group">
                        {!! Form::checkbox('internal_activity_alert_persons[]','department_members',$alerts->isValueExists('internal_activity_alert_persons','department_members')) !!}
                        {!! Form::label('assignment_assigned_agent',Lang::get('lang.department_members')) !!}
                    </div>
                    <!-- end department members -->

                    <!-- department manager -->
                    <div class="form-group">
                        {!! Form::checkbox('internal_activity_alert_persons[]','department_manager',$alerts->isValueExists('internal_activity_alert_persons','department_manager')) !!}
                        {!! Form::label('assignment_assigned_agent',Lang::get('lang.department_manager')) !!}
                    </div>
                    <!-- end department manager -->

                    <div class="form-group">
                        {!! Form::checkbox('internal_activity_alert_persons[]','assigned_agent_team',$alerts->isValueExists('internal_activity_alert_persons','assigned_agent_team')) !!}
                        {!! Form::label('assignment_assigned_agent',Lang::get('lang.assigned_agent')) !!}
                    </div>

                    <!-- Team Members -->
                    <div class="form-group">
                        {!! Form::checkbox('internal_activity_alert_persons[]','team_members',$alerts->isValueExists('internal_activity_alert_persons','team_members')) !!}
                        {!! Form::label('assignment_team_member',Lang::get('lang.team_members')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('internal_activity_alert_persons[]','team_lead',$alerts->isValueExists('internal_activity_alert_persons','team_lead')) !!}
                        {!! Form::label('assignment_team_member',Lang::get('lang.team_lead')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!--/.col (left) -->
         <!-- left column -->


        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.rating-mail')}}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        {!! Form::label('rating', Lang::get('lang.status')) !!}&nbsp;&nbsp;
                        {!! Form::radio('rating_feedback_alert', 1, $alerts->isValueExists('rating_feedback_alert', 1))!!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp;
                        {!! Form::radio('rating_feedback_alert', 0, $alerts->isValueExists('rating_feedback_alert', 0))!!} {!! Lang::get('lang.disable') !!} &nbsp;&nbsp; 
                        <br><br>
                        {{ Form::hidden('rating_feedback_alert_mode', 'email') }}
                        {{ Form::hidden('rating_feedback_alert_persons', 'client') }}
                        {!! Form::label('status', Lang::get('lang.ticket-status')) !!}&nbsp;&nbsp;
                        <?php
                            $statuses = App\Model\helpdesk\Ticket\Ticket_Status::all();
                            $selected_statuses = null;
                            if($alerts->where('key', 'rating_mail_statuses')->first())
                                $selected_statuses = $alerts->where('key', 'rating_mail_statuses')->first()->value;
                        ?>
                        <select name="rating_mail_statuses[]" id="input-ticket-status" class="form-control"  multiple style="width: 100%">
                            @foreach($statuses as $status)
                                <option value="{{$status->id}}">{{$status->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.rating-confirmation-label')}}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        {!! Form::label('rating_confirmation', Lang::get('lang.status')) !!}&nbsp;&nbsp;
                        {!! Form::radio('rating_confirmation', 1, $alerts->isValueExists('rating_confirmation', 1))!!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp;
                        {!! Form::radio('rating_confirmation', 0, $alerts->isValueExists('rating_confirmation', 0))!!} {!! Lang::get('lang.disable') !!} &nbsp;&nbsp; 
                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('rating_confirmation_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('rating_confirmation_mode[]','email',$alerts->isValueExists('rating_confirmation_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        
                        
                        {{-- Form::checkbox('rating_confirmation_mode[]','system',$alerts->isValueExists('rating_confirmation_mode','system')) !!}  {!! Lang::get('lang.in_app_system') --}}
                    </div>
                    <div class="form-group">
                        <!-- Creator -->
                        <div class="form-group">
                            {!! Form::checkbox('rating_confirmation_persons[]','agent',$alerts->isValueExists('rating_confirmation_persons','agent')) !!}
                            {!! Form::label('new-task-alert-person',Lang::get('lang.agent')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::checkbox('rating_confirmation_persons[]','admin',$alerts->isValueExists('rating_confirmation_persons','admin')) !!}
                            {!! Form::label('rating_confirmation_persons',Lang::get('lang.admin')) !!}
                        </div>
                        <!-- <div class="form-group">
                            {!! Form::checkbox('rating_confirmation_persons[]','team_lead',$alerts->isValueExists('rating_confirmation_persons','team_lead')) !!}
                            {!! Form::label('rating_confirmation_persons',Lang::get('lang.team_lead')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::checkbox('rating_confirmation_persons[]','team_members',$alerts->isValueExists('rating_confirmation_persons','team_members')) !!}
                            {!! Form::label('rating_confirmation_persons',Lang::get('lang.team_members')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::checkbox('rating_confirmation_persons[]','department_manager',$alerts->isValueExists('rating_confirmation_persons','department_manager')) !!}
                            {!! Form::label('rating_confirmation_persons',Lang::get('lang.department_manager')) !!}<br>
                        </div>
                        <div class="form-group">
                            {!! Form::checkbox('rating_confirmation_persons[]','department_members',$alerts->isValueExists('rating_confirmation_persons','department_members')) !!}
                            {!! Form::label('rating_confirmation_persons',Lang::get('lang.department_members')) !!}
                        </div> -->
                        <!-- <div class="form-group">
                            {!! Form::checkbox('rating_confirmation_persons[]','assignee',$alerts->isValueExists('rating_confirmation_persons','assignee')) !!}
                            {!! Form::label('rating_confirmation_persons',Lang::get('lang.team_members')) !!}
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

         @if(isPlugin('Calendar'))        
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.new-task-alert')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('new-task-alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('new-task-alert',1,$alerts->isValueExists('new-task-alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('new-task-alert',0,$alerts->isValueExists('new-task-alert',0)) !!}  {!! Lang::get('lang.disable') !!}
                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('new-task-alert-mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('new-task-alert-mode[]','email',$alerts->isValueExists('new-task-alert-mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        
                        
                        {!! Form::checkbox('new-task-alert-mode[]','in-app-notify',$alerts->isValueExists('new-task-alert-mode','in-app-notify')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>
                    <div class="form-group">
                        <!-- Creator -->
                        <div class="form-group">
                            {!! Form::checkbox('new-task-alert-person[]','creator',$alerts->isValueExists('new-task-alert-person','creator')) !!}
                            {!! Form::label('new-task-alert-person',Lang::get('lang.task-owner')) !!}
                        </div>
                        <!-- Admin Email -->
                        {!! Form::checkbox('new-task-alert-person[]','assignee',$alerts->isValueExists('new-task-alert-person','assignee')) !!}
                        {!! Form::label('new-task-alert-person',Lang::get('lang.task-assignee')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div>

        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.task-reminder')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('task-reminder-alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('task-reminder-alert',1,$alerts->isValueExists('task-reminder-alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('task-reminder-alert',0,$alerts->isValueExists('task-reminder-alert',0)) !!}  {!! Lang::get('lang.disable') !!}
                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('task-reminder-alert-mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('task-reminder-alert-mode[]','email',$alerts->isValueExists('task-reminder-alert-mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        
                        {!! Form::checkbox('task-reminder-alert-mode[]','in-app-notify',$alerts->isValueExists('task-reminder-alert-mode','in-app-notify')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>
                    <div class="form-group">
                        <!-- Creator -->
                        <div class="form-group">
                            {!! Form::checkbox('task-reminder-alert-person[]','creator',$alerts->isValueExists('task-reminder-alert-person','creator')) !!}
                            {!! Form::label('task-reminder-alert-person',Lang::get('lang.task-owner')) !!}
                        </div>
                        <!-- Admin Email -->
                        {!! Form::checkbox('task-reminder-alert-person[]','assignee',$alerts->isValueExists('task-reminder-alert-person','assignee')) !!}
                        {!! Form::label('task-reminder-alert-person',Lang::get('lang.task-assignee')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div>

        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.task-update-alert')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('task-update-alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('task-update-alert',1,$alerts->isValueExists('task-update-alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('task-update-alert',0,$alerts->isValueExists('task-update-alert',0)) !!}  {!! Lang::get('lang.disable') !!}
                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('task-update-alert-mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('task-update-alert-mode[]','email',$alerts->isValueExists('task-update-alert-mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        
                        {!! Form::checkbox('task-update-alert-mode[]','in-app-notify',$alerts->isValueExists('task-update-alert-mode','in-app-notify')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>
                    <div class="form-group">
                        <!-- Creator -->
                        <div class="form-group">
                            {!! Form::checkbox('task-update-alert-person[]','creator',$alerts->isValueExists('task-update-alert-person','creator')) !!}
                            {!! Form::label('task-update-alert-person',Lang::get('lang.task-owner')) !!}
                        </div>
                        <!-- Admin Email -->
                        {!! Form::checkbox('task-update-alert-person[]','assignee',$alerts->isValueExists('task-update-alert-person','assignee')) !!}
                        {!! Form::label('task-update-alert-person',Lang::get('lang.task-assignee')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div>
        @endif
        <?php \Event::dispatch('alert-settings-block-1', [$alerts]) ?>
    </div>
    <!-- left column -->
    <div class="col-md-6">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.ticket_transfer_alert')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('ticket_transfer_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('ticket_transfer_alert',1,$alerts->isValueExists('ticket_transfer_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('ticket_transfer_alert',0,$alerts->isValueExists('ticket_transfer_alert',0)) !!}  {!! Lang::get('lang.disable') !!}

                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('ticket_transfer_alert_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('ticket_transfer_alert_mode[]','email',$alerts->isValueExists('ticket_transfer_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['ticket_transfer_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                        
                        
                        {!! Form::checkbox('ticket_transfer_alert_mode[]','system',$alerts->isValueExists('ticket_transfer_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>

                    <!-- admin-->
                    <div class="form-group">
                        {!! Form::checkbox('ticket_transfer_alert_persons[]','admin',$alerts->isValueExists('ticket_transfer_alert_persons','admin')) !!}
                        {!! Form::label('assFPigned_agent_team',Lang::get('lang.admin')) !!}
                    </div>

                    <!-- Assigned Agent/Team -->
                    <div class="form-group">
                        {!! Form::checkbox('ticket_transfer_alert_persons[]','assigned_agent_team',$alerts->isValueExists('ticket_transfer_alert_persons','assigned_agent_team')) !!}
                        {!! Form::label('assigned_agent_team',Lang::get('lang.assigned_agent')) !!}
                    </div>

                    <!-- Department members -->
                    <div class="form-group">
                        {!! Form::checkbox('ticket_transfer_alert_persons[]','department_members',$alerts->isValueExists('ticket_transfer_alert_persons','department_members')) !!}
                        {!! Form::label('ticket_department_manager',Lang::get('lang.department_members')) !!}
                    </div>

                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('ticket_transfer_alert_persons[]','department_manager',$alerts->isValueExists('ticket_transfer_alert_persons','department_manager')) !!}
                        {!! Form::label('ticket_department_manager',Lang::get('lang.department_manager')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->


        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.registration_confirmation_and_verification')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('registration_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('registration_alert',1,$alerts->isValueExists('registration_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('registration_alert',0,$alerts->isValueExists('registration_alert',0)) !!}  {!! Lang::get('lang.disable') !!}

                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('registration_alert_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('registration_alert_mode[]','email',$alerts->isValueExists('registration_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['registration_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                        
                        {!! Form::checkbox('registration_alert_mode[]','system',$alerts->isValueExists('registration_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>

                    <!-- new user -->
                    <div class="form-group">
                        {!! Form::checkbox('registration_alert_persons[]','new_user',$alerts->isValueExists('registration_alert_persons','new_user')) !!}
                        {!! Form::label('registration_alert_persons',Lang::get('lang.new_registered_user')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->

        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.registration_notification')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('new_user_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('new_user_alert',1,$alerts->isValueExists('new_user_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('new_user_alert',0,$alerts->isValueExists('new_user_alert',0)) !!}  {!! Lang::get('lang.disable') !!}

                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('new_user_alert_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('new_user_alert_mode[]','email',$alerts->isValueExists('new_user_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['new_user_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                        
                        {!! Form::checkbox('new_user_alert_mode[]','system',$alerts->isValueExists('new_user_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>

                    <!-- Assigned Agent/Team -->
                    <div class="form-group">
                        {!! Form::checkbox('new_user_alert_persons[]','admin',$alerts->isValueExists('new_user_alert_persons','admin')) !!}
                        {!! Form::label('new_user_alert_persons',Lang::get('lang.admin')) !!}
                    </div>

                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('new_user_alert_persons[]','agent',$alerts->isValueExists('new_user_alert_persons','agent')) !!}
                        {!! Form::label('new_user_alert_persons',Lang::get('lang.agent')) !!}
                    </div>
                    <!-- Organization Account Manager -->
                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('new_user_alert_persons[]','all_department_manager',$alerts->isValueExists('new_user_alert_persons','all_department_manager')) !!}
                        {!! Form::label('new_user_alert_persons',Lang::get('lang.all_department_manager')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('new_user_alert_persons[]','all_team_lead',$alerts->isValueExists('new_user_alert_persons','all_team_lead')) !!}
                        {!! Form::label('new_user_alert_persons',Lang::get('lang.all_team_lead')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.email_verify')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('email_verify_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('email_verify_alert',1,$alerts->isValueExists('email_verify_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('email_verify_alert',0,$alerts->isValueExists('email_verify_alert',0)) !!}  {!! Lang::get('lang.disable') !!}

                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('email_verify_alert_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('email_verify_alert_mode[]','email',$alerts->isValueExists('email_verify_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['new_user_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                    </div>

                    <!-- User -->
                    <div class="form-group">
                        {!! Form::checkbox('email_verify_alert_persons[]','new_user',$alerts->isValueExists('email_verify_alert_persons','new_user')) !!}
                        {!! Form::label('email_verify_alert_persons',Lang::get('lang.user_requesting_verification')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->

        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.agent_reply')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('reply_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('reply_alert',1,$alerts->isValueExists('reply_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('reply_alert',0,$alerts->isValueExists('reply_alert',0)) !!}  {!! Lang::get('lang.disable') !!}

                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('reply_alert_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('reply_alert_mode[]','email',$alerts->isValueExists('reply_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['reply_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                        {!! Form::checkbox('reply_alert_mode[]','system',$alerts->isValueExists('reply_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>

                    <!-- Assigned Agent/Team -->
                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','client',$alerts->isValueExists('reply_alert_persons','client')) !!}
                        {!! Form::label('reply_alert_persons',Lang::get('lang.client')) !!}
                    </div>

                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','agent',$alerts->isValueExists('reply_alert_persons','agent')) !!}
                        {!! Form::label('reply_alert_persons',Lang::get('lang.agent')) !!}
                    </div>
                    <!-- Organization Account Manager -->
                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','admin',$alerts->isValueExists('reply_alert_persons','admin')) !!}
                        {!! Form::label('reply_alert_persons',Lang::get('lang.admin')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','assigned_agent_team',$alerts->isValueExists('reply_alert_persons','assigned_agent_team')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.assigned_agent')) !!}
                    </div>
                    <!-- Organization Account Manager -->
                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','department_manager',$alerts->isValueExists('reply_alert_persons','department_manager')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.department_manager')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','department_members',$alerts->isValueExists('reply_alert_persons','department_members')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.department_members')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','team_members',$alerts->isValueExists('reply_alert_persons','team_members')) !!}
                        {!! Form::label('reply_alert_persons',Lang::get('lang.team_members')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','team_lead',$alerts->isValueExists('reply_alert_persons','team_lead')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.team_lead')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('reply_alert_persons[]','organization_manager',$alerts->isValueExists('reply_alert_persons','organization_manager')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.organization_account_manager')) !!}
                    </div>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->

        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.client_reply')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('reply_notification_alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('reply_notification_alert',1,$alerts->isValueExists('reply_notification_alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('reply_notification_alert',0,$alerts->isValueExists('reply_notification_alert',0)) !!}  {!! Lang::get('lang.disable') !!}

                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('reply_notification_alert_mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('reply_notification_alert_mode[]','email',$alerts->isValueExists('reply_notification_alert_mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        <?php $sms = \Event::dispatch('sms_option', [['reply_notification_alert_mode', $alerts]]); ?>
                        @if(!empty($sms))
                        {!! $sms[0] !!}
                        @endif
                        
                        {!! Form::checkbox('reply_notification_alert_mode[]','system',$alerts->isValueExists('reply_notification_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>

                    <!-- Assigned Agent/Team -->
                    <div class="form-group">
                        {!! Form::checkbox('reply_notification_alert_persons[]','admin',$alerts->isValueExists('reply_notification_alert_persons','admin')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.admin')) !!}
                    </div>

                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('reply_notification_alert_persons[]','agent',$alerts->isValueExists('reply_notification_alert_persons','agent')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.agent')) !!}
                    </div>
                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('reply_notification_alert_persons[]','assigned_agent_team',$alerts->isValueExists('reply_notification_alert_persons','assigned_agent_team')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.assigned_agent')) !!}
                    </div>
                    <!-- Organization Account Manager -->
                    <!-- Department Manager -->
                    <div class="form-group">
                        {!! Form::checkbox('reply_notification_alert_persons[]','department_manager',$alerts->isValueExists('reply_notification_alert_persons','department_manager')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.department_manager')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('reply_notification_alert_persons[]','department_members',$alerts->isValueExists('reply_notification_alert_persons','department_members')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.department_members')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('reply_notification_alert_persons[]','team_members',$alerts->isValueExists('reply_notification_alert_persons','team_members')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.team_members')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('reply_notification_alert_persons[]','team_lead',$alerts->isValueExists('reply_notification_alert_persons','team_lead')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.team_lead')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('reply_notification_alert_persons[]','organization_manager',$alerts->isValueExists('reply_notification_alert_persons','organization_manager')) !!}
                        {!! Form::label('reply_notification_alert_persons',Lang::get('lang.organization_account_manager')) !!}
                    </div>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->

        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.browser-notification')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                           <div class="radio">
                              <label>
                                 {!! Form::radio('active_in_app_notification',"in_app_javascript",$alerts->isValueExists('active_in_app_notification',"in_app_javascript")) !!}
                                 (in app)Javascript
                              </label>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="radio">
                              <label>
                                 {!! Form::radio('active_in_app_notification',"one_signal",$alerts->isValueExists('active_in_app_notification',"one_signal")) !!}
                                 One Signal
                              </label>
                           </div>
                        </div>
                    </div><hr>

                     <div class="" id="one_signal">
                        <div class="form-group">
                           <!-- Status:     Enable   Disable     -->
                           {!! Form::label('browser_notification_status',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                           {!! Form::radio('browser_notification_status',1,$alerts->isValueExists('browser_notification_status',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                           {!! Form::radio('browser_notification_status',0,$alerts->isValueExists('browser_notification_status',0)) !!}  {!! Lang::get('lang.disable') !!}

                        </div>
                        <div class="form-group">
                           <!-- One Signal configuration -->
                           {!! Form::label('one-signal-app_id',Lang::get('lang.app-id').":") !!}&nbsp;&nbsp;                        
                           {!! Form::text('api_id', $browser_notification_settings->app_id, ['class' => 'form-control', 'placeholder' => 'Enter One-Signal App ID']) !!}

                        </div>
                        <div class="form-group">
                           {!! Form::label('one-signal-api-key',Lang::get('lang.app-key').":") !!}&nbsp;&nbsp;                        
                           {!! Form::text('rest_api_key', $browser_notification_settings->api_key, ['class' => 'form-control', 'placeholder' => 'Enter One-Signal API key']) !!}
                        </div>
                     </div>
                     <div class="" id="in_app_javascript">
                        <div class="form-group">
                           <!-- Status:     Enable   Disable     -->
                           {!! Form::label('browser-notification-status-inbuilt',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                           {!! Form::radio('browser-notification-status-inbuilt',1,$alerts->isValueExists('browser-notification-status-inbuilt',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                           {!! Form::radio('browser-notification-status-inbuilt',0,$alerts->isValueExists('browser-notification-status-inbuilt',0)) !!}  {!! Lang::get('lang.disable') !!}
                        </div>
                     </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div><!--/.col (left) -->

        @if(isPlugin('Calendar'))
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.task-status-alert')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        {!! Form::label('task-status-alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('task-status-alert',1,$alerts->isValueExists('task-status-alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('task-status-alert',0,$alerts->isValueExists('task-status-alert',0)) !!}  {!! Lang::get('lang.disable') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('task-status-alert-mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('task-status-alert-mode[]','email',$alerts->isValueExists('task-status-alert-mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        
                        
                        {!! Form::checkbox('task-status-alert-mode[]','in-app-notify',$alerts->isValueExists('task-status-alert-mode','in-app-notify')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>
                    <div class="form-group">
                        <!-- Creator -->
                        <div class="form-group">
                            {!! Form::checkbox('task-status-alert-person[]','creator',$alerts->isValueExists('task-status-alert-person','creator')) !!}
                            {!! Form::label('task-status-alert-person',Lang::get('lang.task-owner')) !!}
                        </div>
                        <!-- Admin Email -->
                        {!! Form::checkbox('task-status-alert-person[]','assignee',$alerts->isValueExists('task-status-alert-person','assignee')) !!}
                        {!! Form::label('task-status-alert-person',Lang::get('lang.task-assignee')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div>
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">{{Lang::get('lang.task-assign-alert')}}</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <!-- Status:     Enable   Disable     -->
                        {!! Form::label('task-assign-alert',Lang::get('lang.status').":") !!}&nbsp;&nbsp;
                        {!! Form::radio('task-assign-alert',1,$alerts->isValueExists('task-assign-alert',1)) !!} {!! Lang::get('lang.enable') !!} &nbsp;&nbsp; 
                        {!! Form::radio('task-assign-alert',0,$alerts->isValueExists('task-assign-alert',0)) !!}  {!! Lang::get('lang.disable') !!}
                    </div>
                    <div class="form-group">
                        <!-- Mode :   Email    Sms -->
                        {!! Form::label('task-assign-alert-mode',Lang::get('lang.mode').":") !!}&nbsp;&nbsp;
                        {!! Form::checkbox('task-assign-alert-mode[]','email',$alerts->isValueExists('task-assign-alert-mode','email')) !!} {!! Lang::get('lang.email') !!} &nbsp;&nbsp; 
                        
                        
                        {!! Form::checkbox('task-assign-alert-mode[]','in-app-notify',$alerts->isValueExists('task-assign-alert-mode','in-app-notify')) !!}  {!! Lang::get('lang.in_app_system') !!}
                    </div>
                    <div class="form-group">
                        <!-- Creator -->
                        <div class="form-group">
                            {!! Form::checkbox('task-assign-alert-person[]','creator',$alerts->isValueExists('task-assign-alert-person','creator')) !!}
                            {!! Form::label('task-assign-alert-personn',Lang::get('lang.task-owner')) !!}
                        </div>
                        <!-- Admin Email -->
                        {!! Form::checkbox('task-assign-alert-person[]','assignee',$alerts->isValueExists('task-assign-alert-person','assignee')) !!}
                        {!! Form::label('task-assign-alert-person',Lang::get('lang.task-assignee')) !!}
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- /.box -->
        </div>
        @endif
        @if(isPlugin('LimeSurvey'))        
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card card-light">
                    <div class="card-header">
                        <h3 class="card-title">Lime Survey</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            {!! Form::label('status', Lang::get('lang.ticket-status')) !!}&nbsp;&nbsp;
                            <?php
                                $statuses = App\Model\helpdesk\Ticket\Ticket_Status::all();
                                $lime_survey_selected_statuses = null;
                                if($alerts->where('key', 'lime_survey_mail_statuses')->first())
                                    $lime_survey_selected_statuses = $alerts->where('key', 'lime_survey_mail_statuses')->first()->value;
                            ?>
                            {{ Form::hidden('lime_survey_alert', 1) }}
                            {{ Form::hidden('lime_survey_alert_mode', 'email') }}
                            {{ Form::hidden('lime_survey_alert_persons', 'client') }}
                            <select name="lime_survey_mail_statuses[]" id="input-ticket-status1" class="form-control"  multiple style="width: 100%">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <?php \Event::dispatch('alert-settings-block-2', [$alerts]) ?>
    </div>
</div></div>
</div>
<script src="{{assetLink('js','select2')}}"></script>

<script type="text/javascript">
    $(function(){
        var value="{{$selected_statuses}}";
        var select2_status=value.split(",");

         $('#input-ticket-status>option').each(function(){
            for(var i in select2_status){
                if($(this).val()==select2_status[i]){
                    $(this).attr('selected','selected');
                }
            }
         })
         $('#input-ticket-status').select2();

        @if(isPlugin('LimeSurvey'))
        var value2="{{$lime_survey_selected_statuses}}";
        var select2_status2=value2.split(",");
        $('#input-ticket-status1>option').each(function(){
            for(var i in select2_status2){
                if($(this).val()==select2_status2[i]){
                    $(this).attr('selected','selected');
                }
            }
         })
         $('#input-ticket-status1').select2();
         @endif
    })
     function validate(x){
                    $(x).prop('required',true);
                    $(x).css('border-color','red');
                    setTimeout(function(){
                        $(document).find('#submit').html('<i class="fas fa-save">&nbsp;</i> Save');
                        $(document).find('#submit').removeAttr('disabled');
                        $(document).find('#submit').removeClass('disabled');
                    },200)
   }
   $('input[name="rest_api_key"]').change(function(){
          $(this).css('border-color','#d2d6de');
   })
   $('input[name="api_id"]').change(function(){
          $(this).css('border-color','#d2d6de');
   })
   $('.alertTemplateForm').submit(function(){
         if($('input[name="active_in_app_notification"]:checked').val()=='in_app_javascript'){
                 $('input[name="browser_notification_status"]').val('0') 
         }
         else if($('input[name="active_in_app_notification"]:checked').val()=='one_signal'){
                $('input[name="browser-notification-status-inbuilt"]').val('0');
            if($('input[name="browser_notification_status"]:statuses').val()==1){
                if($('input[name="api_id"]').val()==""){
                    if($('input[name="rest_api_key"]').val()==""){
                       validate('input[name="rest_api_key"]');
                    }
                    validate('input[name="api_id"]');
                    return false;
                }
                if($('input[name="rest_api_key"]').val()==""){
                    validate('input[name="rest_api_key"]');
                    return false;
                }
            }
         }
         setTimeout(function(){
               return true;
         },400);
   })
  
   $('input:radio[name="active_in_app_notification"]').change(function(){
      var type = $(this).val();
      
         if(type == "in_app_javascript"){
            $("#one_signal").fadeOut("slow", function() {
               $(this).addClass("hidden")
            })
            $("#in_app_javascript").fadeIn("slow", function(){
               $("#in_app_javascript").removeClass("hidden")
            })
            
         }
         
         else if(type = "one_signal"){
            $("#one_signal").fadeIn("slow", function() {
               $(this).removeClass("hidden")
            })
            $("#in_app_javascript").fadeIn("slow", function(){
               $("#in_app_javascript").addClass("hidden")
            })
         }
         else{

         }
      
   })

   $(document).ready(function(){
      var radioValue = $("input[name='active_in_app_notification']:checked").val();
            if(radioValue == "in_app_javascript"){
               $("#one_signal").addClass("hidden");
               $("#in_app_javascript").removeClass("hidden");
            }

            else if(radioValue == "one_signal"){
               $("#one_signal").removeClass("hidden")
               $("#in_app_javascript").addClass("hidden")
            }
            else{
               $("#one_signal").addClass("hidden")
               $("#in_app_javascript").addClass("hidden")
            }
   });

</script>
{!! Form::close() !!}

@stop
