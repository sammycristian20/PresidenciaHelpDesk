@extends('themes.default1.admin.layout.admin')

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop


@section('PageHeader')
<h1>{!! Lang::get('lang.templates') !!}</h1>
@stop

@section('content')

@if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fas fa-check"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        @if(Session::has('failed'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>{{Session::get('failed')}}</p>                
        </div>
        @endif
        
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.edit_templates') !!}</h3>
    </div><!-- /.box-header -->
    <div class="card-body">
        <table id="chumper" class="table table-bordered dataTable no-footer">
                <thead>
                    <tr>
                        <td>{!! Lang::get('lang.name') !!}</td>
                        <td>{!! Lang::get('lang.description') !!}</td>
                        <td>{!! Lang::get('lang.action') !!}</td>
                        <td>{!! Lang::get('lang.category') !!}</td>
                    </tr>
                </thead>
        </table>
    </div><!-- /.box-body -->
</div>
@stop
@push('scripts')
@include('vendor.yajra.faveo-template-javascript')
@endpush