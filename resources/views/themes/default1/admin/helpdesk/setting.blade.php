@extends('themes.default1.admin.layout.admin')
@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.admin_panel') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
@stop
<!-- /breadcrumbs -->
<!-- content -->

<style type="text/css">
    
    .content-wrapper { min-height: auto !important; }

    .settingiconblue {
    	padding-top: 0.5rem;
    	padding-bottom: 0.5rem;
    }

     .settingdivblue {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        text-align: center;
        border: 5px solid #C4D8E4;
        border-radius: 100%;
        padding-top: 5px;
    }

     .settingdivblue span { margin-top: 3px; }

     .fw_600 { font-weight: 600; }
    .settingiconblue p{
        text-align: center;
        font-size: 16px;
        word-wrap: break-word;
        font-variant: small-caps;
        font-weight: 500;
        line-height: 30px;
    }
</style>
@section('content')
<!-- failure message -->
@if(Session::has('fails'))
<div class="alert alert-danger alert-dismissable">
    <i class="fas fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!}! </b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {!! Session::get('fails')!!}
</div>
@endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.staffs') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('agents') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-user fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.agents') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('departments') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-sitemap fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.departments') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('teams') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-users fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.teams') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <?php \Event::dispatch('settings.agent.view', []); ?>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>
<!-- /.box -->

<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.email') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('emails') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-envelope fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.emails_settings') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('template-sets') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-file-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.templates') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                {{--<div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('getemail')}}" onclick="sidebaropen(this)">
                <span class="fa-stack fa-2x">
                    <i class="fas fa-at fa-stack-1x"></i>
                </span>
                </a>
            </div>
            <div class="text-center text-sm fw_600">{!! Lang::get('lang.email-settings') !!}</div>
        </div>
    </div>--}}
    <div class="col-md-2 col-sm-6">
        <div class="settingiconblue">
            <div class="settingdivblue">
                <a href="{{url('queue')}}" onclick="sidebaropen(this)">
                    <span class="fa-stack fa-2x">
                        <i class="fas fa-upload fa-stack-1x"></i>
                    </span>
                </a>
            </div>
            <div class="text-center text-sm fw_600">{!! Lang::get('lang.queues') !!}</div>
        </div>
    </div>
    <!--col-md-2-->
    <div class="col-md-2 col-sm-6">
        <div class="settingiconblue">
            <div class="settingdivblue">
                <a href="{{ url('getdiagno') }}" onclick="sidebaropen(this)">
                    <span class="fa-stack fa-2x">
                        <i class="fas fa-plus fa-stack-1x"></i>
                    </span>
                </a>
            </div>
            <div class="text-center text-sm fw_600">{!! Lang::get('lang.diagnostics') !!}</div>
        </div>
    </div>
    <!--/.col-md-2-->
</div>
<!-- /.row -->
</div>
<!-- ./box-body -->
</div>

<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.manage') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('helptopic')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-file-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.help_topics') !!}</div>
                    </div>
                </div>

                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('sla')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-clock fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.sla_plans') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->


                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('sla/business-hours/index')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-calendar fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.business_hours') !!}</div>
                    </div>
                </div>

                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('forms/create')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-file-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.form-builder') !!}</div>
                    </div>
                </div>

                <!--/.col-md-2-->
                <!-- <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('form/user')}}">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-user-plus fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.requester-form') !!}</div>
                    </div>
                </div> -->
                <!--/.col-md-2-->

                
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('form-groups') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-object-group fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.form-groups') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('workflow')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-sitemap fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.workflow') !!}</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('approval-workflow')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-sitemap fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.approval-workflow') !!}</div>
                    </div>
                </div>
                <!-- priority -->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('ticket/priority')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">

                                    <i class="fas fa-asterisk fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.priority') !!}</div>
                    </div>
                </div>

                <!-- Ticket Types -->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('ticket-types')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">

                                    <i class="fas fa-list-ol fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.ticket_type') !!}</div>
                    </div>
                </div>

                <!--/.col-md-2-->

                <!-- <div class="col-md-2 col-sm-6 hide">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('url/settings')}}">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-server fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">Url</div>

                    </div>
                </div> -->

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('listener')}}">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-magic fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.listeners') !!}</div>

                    </div>
                </div>

                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('widgets') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-list-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.widgets') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.ticket') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('getticket')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-ticket-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.ticket_settings') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('alert')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-bell fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.alert_notices') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('setting-status')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-plus-square fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{{Lang::get('lang.status')}}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('labels')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fab fa-lastfm fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{{Lang::get('lang.labels')}}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('getratings')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.ratings') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('close-workflow')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-sitemap fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.close_ticket_workflow') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('tag')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-tags fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.tags') !!}</div>
                    </div>
                </div>

                <?php $extraSettingHtmlString = ''; \Event::dispatch('settings.ticket.view', [&$extraSettingHtmlString]); ?>
                {!! $extraSettingHtmlString !!}

                <div class="col-md-2 col-sm-6" >
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('source')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fab fa-gg fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.source') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6" >
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('recur/list')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-copy fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.recurring') !!}</div>
                    </div>
                </div>
                
                <?php \Event::dispatch('helpdesk.settings.ticket.location'); ?>

                @includeWhen(isTimeTrack(), 'timetrack::settings.icon')

        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.settings') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">
                <!--/.col-md-2-->
                <!-- <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{!! url('dashboard-settings') !!}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-tachometer-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.dashboard-statistics') !!}</div>
                    </div>
                </div> -->
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{!! url('getcompany') !!}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-building fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.company') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('getsystem')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-laptop fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                       <div class="text-center text-sm fw_600">{!! Lang::get('lang.system') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('user-options')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-user fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.user-options') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('social/media') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-globe fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{{Lang::get('lang.social-login')}}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('languages')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-language fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.language') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('job-scheduler')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-hourglass fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.cron') !!}</div>
                    </div>
                </div>
        </div>
        <!-- /.row -->
        <div class="row">
                <!--/.col-md-2-->
                
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6" >
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('security')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas  fa-lock fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.security') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('settings-notification')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-bell fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                       <div class="text-center text-sm fw_600">{!! Lang::get('lang.notification') !!}</div>
                    </div>
                </div>

                <?php //\Event::dispatch('settings.system', []); ?>
            <div class="col-md-2 col-sm-6">
                <div class="settingiconblue">
                    <div class="settingdivblue">
                        <a href="{{ url('file-system-settings') }}" onclick="sidebaropen(this)">
                                    <span class="fa-stack fa-2x">
                                        <i class="fas fa-folder fa-stack-1x"></i>
                                    </span>
                        </a>
                    </div>
                    <div class="text-center text-sm fw_600">{!! Lang::get('lang.settings_file_system') !!}</div>
                </div>
            </div>
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('system-backup')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-hdd fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.system-backup') !!}</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('widgets/social-icon') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas  fa-external-link-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.social-icon-links') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('api') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-cogs fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.api') !!}</div>
                    </div>
                </div>


                <!--/.col-md-2-->
        </div>
        <div class="row">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('webhook') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-server fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.webhook') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('websockets/settings') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-bolt  fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.websockets') !!}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('importer') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-download fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.importer_user_import') !!}</div>
                    </div>
                </div>

                
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('recaptcha') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-sync-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600"><span style="font-variant: normal; font-size: 13px;">re</span>{!! Lang::get('lang.captcha') !!}</div>
                    </div>
                </div>


                <!--col-md-2-->
                <!--/.col-md-2-->
                @if($dummy_installation == 1 || $dummy_installation == '1')
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{route('clean-database')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-undo fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.delete_dummy_data') !!}</div>
                    </div>
                </div>
                @endif
        </div>
    </div>
    <!-- ./box-body -->
</div>
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.add-ons') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('plugins') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-plug fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.plugin') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('modules') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                       <i class="fas fa-link fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.modules') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->

                <?php
                         $check_satellite_helpdesk = \App\Model\helpdesk\Settings\CommonSettings::where('option_name', '=', 'satellite_helpdesk')->select('status')->first();
                              ?>

                                   @if ($check_satellite_helpdesk && $check_satellite_helpdesk->status == 1)

                                    <?php \Event::dispatch('satellite-helpdesk.settings', array()); ?>

                                    @endif


                <!--/.col-md-2-->
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>

<?php \Event::dispatch('show.admin.settings', array());?>

<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.system-debug') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('err.debug.settings') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-bug fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.debug-options') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('system.logs') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-history fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.system-logs') !!}</div>
                    </div>
                </div>

                <!--/.col-md-2-->
                @if(getActiveQueue() == 'redis')
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('horizon.index') }}" target="_blank">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-desktop fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm fw_600">{!! Lang::get('lang.queue-monitor') !!}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                @endif
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>

<?php \Event::dispatch('service.desk.admin.settings', array()); ?>
@stop
