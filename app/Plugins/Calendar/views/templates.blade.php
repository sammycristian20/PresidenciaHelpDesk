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
                            {{Lang::get('Calendar::lang.templates_setting')}}</h4><!--  <button type="submit" class="btn btn-primary pull-right" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> Saving..."><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button> -->
                    </div>
                </div>
    	    </div>
        </div>
        <div class="box box-header">
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
        </div>
    {!! Form::close() !!}
    @include('vendor.yajra.faveo-template-javascript')
    @stop
    