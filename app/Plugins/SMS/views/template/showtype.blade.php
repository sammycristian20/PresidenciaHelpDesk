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

@section('custom-css')

    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
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
 @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fas fa-check-circle"> </i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!}! </b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('SMS::lang.sms-templates')}}</h3>
    </div>
    <!-- /.box-header -->
    <div class="card-body ">
       
        <!--<div class="mailbox-controls">-->
            <!-- Check all button -->
            <!-- <a class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i></a> -->
            
<!--             <input type="submit" class="submit btn btn-default text-orange btn-sm" id="delete" name="submit" value="{!! Lang::get('lang.delete') !!}">
 -->        <!--</div>-->
        <div class="mailbox-messages" id="refresh">
            <table class="table table-bordered" id="chumper">
                <thead>
                    <tr>
                        <td>{!! Lang::get('lang.name') !!}</td>
                        <td>{!! Lang::get('lang.description') !!}</td>
                        <td>{!! Lang::get('lang.action') !!}</td>
                        <td>{!! Lang::get('lang.category') !!}</td>
                    </tr>
                </thead>
            </table>
        </div><!-- /.mail-box-messages -->
    </div><!-- /.box-body -->
</div>
<!-- /.box -->
@stop

@section('FooterInclude')
@include('SMS::template.faveo-template-javascript')
@stop

<!-- /content -->
