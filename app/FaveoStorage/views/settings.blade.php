@extends('themes.default1.admin.layout.admin')

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('storage')
class="active"
@stop

@section('PageHeader')
<h1>{{ Lang::get('storage::lang.storage')}}</h1>
@stop

@section('HeadInclude')
@stop
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

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- fail message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif

{!! Form::open(['url'=>'storage','method'=>'post','id'=>'Form']) !!}
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title"> {{Lang::get('storage::lang.storage')}} </h3>
      
        
    </div><!-- /.box-header -->
    <!-- /.box-header -->
    <div class="card-body">
        <div class="row">
                <div class="col-md-6 {{ $errors->has('private-root') ? 'has-error' : '' }}">
                    {!! Form::label('storage-path',Lang::get('storage::lang.storage-path')) !!}
                    {!! Form::text('private-root',$private_folder,['class'=>'form-control']) !!}             
                </div>
        </div>
        

</div>
<!-- /.box-body -->
<div class="card-footer">

    <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fas fa-circle-notch fa-spin'>&nbsp;</i> Saving..."><i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
 
{!! Form::close() !!}
</div>
<!-- /.box -->
</div>
@stop
@section('FooterInclude')

@stop