@extends('themes.default1.admin.layout.admin')
@section('PageHeader')
<h1> Lime Survey </h1>
@stop
@section('content')
@if(isset($status))
                   <div class="alert alert-success">
                     <span class="fas fa-check-circle"> </span> Success ! {{$status}}
                  </div>
               @elseif(isset($error))
                  <div class="alert alert-danger">
                     <span class="fas fa-ban"> </span> Alert ! {{$error}}
                  </div>
               @endif
<!-- Main content -->
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">Survey Settings</h3>
            </div>
            <div class="card-body">
               

               @if(isset($survey))
                  {!! Form::model($survey, ['url' => url('/lime-survey/settings'), 'method' => 'patch']) !!}
               @else
                  {{Form::open([ 'url' => url('/lime-survey/settings')])}}
               @endif
                  <input type="hidden" name="name" value="survey link">
                  <div class="row">
                     <div class="col-md-12">
                        <label class=""><b> Survey url: </b></label>
                     </div>
                     <div class="col-md-11 col-sm-9 col-xs-9">
                        @if(isset($survey->survey_link))
                           {!! Form::url('survey_link', $survey->survey_link, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'https://www.limesurvey/survey-link', 'type' => 'url']) !!}
                        @else
                           {!! Form::url('survey_link', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'https://www.limesurvey/survey-link', 'type' => 'url']) !!}
                        @endif
                     </div>
                     <div class="col-md-1 col-sm-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"> </i> Save</button>
                     </div>
                  </div>
                  {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@stop