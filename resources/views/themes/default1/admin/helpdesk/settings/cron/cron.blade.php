@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.cron_settings-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.cron_settings-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('cron')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.settings') }}</h1>
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
@if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <!-- check whether success or not -->
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
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!!Session::get('fails')!!}
        </div>
        @endif
        {{-- alert block --}}
        <div class="alert alert-success cron-success alert-dismissable" style="display: none;">
            <i class="fas fa-check-circle"></i>
            <span class="alert-success-message"></span>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
        <div class="alert alert-danger cron-danger alert-dismissable" style="display: none;">
            <i class="fas fa-ban"></i>
            <span class="alert-danger-message"></span>
            <button type="button" class="close" onclick="hideAlert()">&times;</button>
        </div>
        {{-- alert block end --}}
<div class="row">
    <div class="col-md-12">
        @include('themes.default1.admin.helpdesk.settings.cron.cron-new')
    </div>
    <!-- /.col -->
</div>

<script type="text/javascript">
    
    function hideAlert() {
        $(".cron-danger").css('display', 'none');
    }
</script>
@stop
