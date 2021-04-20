@extends('themes.default1.admin.layout.admin')
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

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('Calendar::lang.calendar-settings') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('calendar/settings/alert')}}">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-bell-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.alert_notices') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('calendar/settings/template-sets') }}">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-file-text-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.templates') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>

@stop
