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
        <h1>{!! Lang::get('Calendar::lang.calendar-settings') !!}</h1>
    @stop
    <!-- /header -->
    <!-- breadcrumbs -->
    @section('breadcrumbs')
    @stop
    <!-- /breadcrumbs -->
    <!-- content -->

    @section('content')

    {!! Form::open(['url' => 'alert', 'method' => 'PATCH','id'=>'Form']) !!}
    <div class="hidden">
        <input type="hidden" name="request_from" value="calendar-settings">
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
            <?php \Event::dispatch('sms_option', [['new_ticket_alert_mode', $alerts]]); ?>
            
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
        <!-- Organization Account Manager -->

        <div class="form-group">
            {!! Form::checkbox('new_ticket_alert_persons[]','organization_manager',$alerts->isValueExists('new_ticket_alert_persons','organization_manager')) !!}
            {!! Form::label('organization_account_manager',Lang::get('lang.organization_account_manager')) !!}
        </div>
    </div>
    <div class="hidden">
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
            <?php \Event::dispatch('sms_option', [['ticket_assign_alert_mode', $alerts]]); ?>
            
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
            {!! Form::label('ticket_assign_alert_agent',Lang::get('lang.agent')) !!}
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
    </div>
    <div class="hidden">
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
        
        <div class="form-group">
            {!! Form::checkbox('notification_alert_persons[]','team_lead',$alerts->isValueExists('notification_alert_persons','team_lead')) !!}
            {!! Form::label('organization_account_manager',Lang::get('lang.team_lead')) !!}
        </div>
    </div>
     <div class="hidden">
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
            <?php \Event::dispatch('sms_option', [['new_ticket_confirmation_alert_mode', $alerts]]); ?>
            
            {!! Form::checkbox('new_ticket_confirmation_alert_mode[]','system',$alerts->isValueExists('new_ticket_confirmation_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
        </div>
        <div class="form-group">

            {!! Form::checkbox('new_ticket_confirmation_alert_persons[]','client',$alerts->isValueExists('new_ticket_confirmation_alert_persons','client')) !!}
            {!! Form::label('new_ticket_confirmation_alert_persons',Lang::get('lang.client')) !!}
        </div>
    </div>
    <div class="hidden">
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
            <?php \Event::dispatch('sms_option', [['internal_activity_alert_mode', $alerts]]); ?>
            
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
    </div>
    <div class="hidden">
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
            <?php \Event::dispatch('sms_option', [['ticket_transfer_alert_mode', $alerts]]); ?>
            
            
            {!! Form::checkbox('ticket_transfer_alert_mode[]','system',$alerts->isValueExists('ticket_transfer_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
        </div>

        <!-- admin-->
        <div class="form-group">
            {!! Form::checkbox('ticket_transfer_alert_persons[]','admin',$alerts->isValueExists('ticket_transfer_alert_persons','admin')) !!}
            {!! Form::label('assigned_agent_team',Lang::get('lang.admin')) !!}
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
        <!-- Organization Account Manager -->

        <div class="form-group">
            {!! Form::checkbox('ticket_transfer_alert_persons[]','organization_manager',$alerts->isValueExists('ticket_transfer_alert_persons','organization_manager')) !!}
            {!! Form::label('organization_account_manager',Lang::get('lang.organization_account_manager')) !!}
        </div>
    </div>
    <div class="hidden">
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
            <?php \Event::dispatch('sms_option', [['registration_alert_mode', $alerts]]); ?>
            
            {!! Form::checkbox('registration_alert_mode[]','system',$alerts->isValueExists('registration_alert_mode','system')) !!}  {!! Lang::get('lang.in_app_system') !!}
        </div>

        <!-- new user -->
        <div class="form-group">
            {!! Form::checkbox('registration_alert_persons[]','new_user',$alerts->isValueExists('registration_alert_persons','new_user')) !!}
            {!! Form::label('registration_alert_persons',Lang::get('lang.new_registered_user')) !!}
        </div>
    </div>
    <div class="hidden">
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
            <?php \Event::dispatch('sms_option', [['new_user_alert_mode', $alerts]]); ?>
            
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
        <div class="form-group">
            {!! Form::checkbox('new_user_alert_persons[]','organization_manager',$alerts->isValueExists('new_user_alert_persons','organization_manager')) !!}
            {!! Form::label('new_user_alert_persons',Lang::get('lang.organization_account_manager')) !!}
        </div>
    </div>
    <div class="hidden">
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
            <?php \Event::dispatch('sms_option', [['reply_alert_mode', $alerts]]); ?>
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
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.assigned_agent_team')) !!}
        </div>
        <!-- Organization Account Manager -->
        <!-- Department Manager -->
        <div class="form-group">
            {!! Form::checkbox('reply_alert_persons[]','all_department_manager',$alerts->isValueExists('reply_alert_persons','all_department_manager')) !!}
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.department_manager')) !!}
        </div>
        <div class="form-group">
            {!! Form::checkbox('reply_alert_persons[]','department_members',$alerts->isValueExists('reply_alert_persons','department_members')) !!}
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.department_members')) !!}
        </div>
        <div class="form-group">
            {!! Form::checkbox('reply_alert_persons[]','all_team_lead',$alerts->isValueExists('reply_alert_persons','all_team_lead')) !!}
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.team_lead')) !!}
        </div>
        <div class="form-group">
            {!! Form::checkbox('reply_alert_persons[]','organization_manager',$alerts->isValueExists('reply_alert_persons','organization_manager')) !!}
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.organization_account_manager')) !!}
        </div>
    </div>
    <div class="hidden">
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
            <?php \Event::dispatch('sms_option', [['reply_notification_alert_mode', $alerts]]); ?>
            
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
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.assigned_agent_team')) !!}
        </div>
        <!-- Organization Account Manager -->
        <!-- Department Manager -->
        <div class="form-group">
            {!! Form::checkbox('reply_notification_alert_persons[]','all_department_manager',$alerts->isValueExists('reply_notification_alert_persons','all_department_manager')) !!}
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.department_manager')) !!}
        </div>
        <div class="form-group">
            {!! Form::checkbox('reply_notification_alert_persons[]','department_members',$alerts->isValueExists('reply_notification_alert_persons','department_members')) !!}
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.department_members')) !!}
        </div>
        <div class="form-group">
            {!! Form::checkbox('reply_notification_alert_persons[]','all_team_lead',$alerts->isValueExists('reply_notification_alert_persons','all_team_lead')) !!}
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.team_lead')) !!}
        </div>
        <div class="form-group">
            {!! Form::checkbox('reply_notification_alert_persons[]','organization_manager',$alerts->isValueExists('reply_notification_alert_persons','organization_manager')) !!}
            {!! Form::label('reply_notification_alert_persons',Lang::get('lang.organization_account_manager')) !!}
        </div>
    </div>
    <div class="hidden">
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
        <!-- <div class="form-inline"> -->
            <!-- Agent -->
            <!-- <div class="form-group"> -->
                <!-- {!! Form::checkbox('browser_notification_alert_persons[]','agent',$alerts->isValueExists('browser_notification_alert_persons','agent')) !!}&nbsp; -->
                <!-- {!! Form::label('browser_notification_alert_persons',Lang::get('lang.agent')) !!} -->
            <!-- </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->

            <!-- Admin -->
            <!-- <div class="form-group"> -->
                <!-- {!! Form::checkbox('browser_notification_alert_persons[]','admin',$alerts->isValueExists('browser_notification_alert_persons','admin')) !!}&nbsp; -->
                <!-- {!! Form::label('browser_notification_alert_persons',Lang::get('lang.admin')) !!} -->
            <!-- </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->

            <!-- Client -->
            <!-- <div class="form-group"> -->
                <!-- {!! Form::checkbox('browser_notification_alert_persons[]','client',$alerts->isValueExists('browser_notification_alert_persons','client')) !!}&nbsp; -->
                <!-- {!! Form::label('browser_notification_alert_persons',Lang::get('lang.client')) !!} -->
            <!-- </div> -->
        <!-- </div> -->

    </div>


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
                    <div class="box-header">
                        <h4 class="box-title">
                            {{Lang::get('Calendar::lang.alert_notices_setitngs')}}</h4> <button type="submit" class="btn btn-primary pull-right" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> Saving..."><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
                    </div>
                </div>
    	    </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{Lang::get('lang.new-task-alert')}}</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
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
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{Lang::get('lang.task-reminder')}}</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
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
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{Lang::get('lang.task-update-alert')}}</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
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
        </div>
        <div class="col-md-6">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{Lang::get('lang.task-status-alert')}}</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
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
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{Lang::get('lang.task-assign-alert')}}</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
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
        </div>
    {!! Form::close() !!}
    @stop