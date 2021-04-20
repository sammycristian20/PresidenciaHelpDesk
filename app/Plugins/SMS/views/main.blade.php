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

<style type="text/css">
     .settingiconblue {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

     .settingdivblue {
        width: 80px;
        height: 76px;
        margin: 0 auto;
        text-align: center;
        border: 5px solid #C4D8E4;
        border-radius: 100%;
        padding-top: 5px;
    }

    .settingiconblue p{
        text-align: center;
        font-size: 16px;
        word-wrap: break-word;
        font-variant: small-caps;
        font-weight: 500;
        line-height: 30px;
    }
</style>
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('SMS::lang.SMS')}}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body">

        <div class="row">

            <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('providers-settings') }}">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-cog fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm">{{Lang::get('SMS::lang.provider-settings')}}</div>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('sms-template-sets') }}">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-file-code fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm">{{Lang::get('SMS::lang.sms-templates')}}</div>
                    </div>
                </div>
            </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>
<!-- /.box -->

@stop

@section('FooterInclude')

@stop

<!-- /content -->
