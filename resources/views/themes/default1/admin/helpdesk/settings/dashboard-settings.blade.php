@extends('themes.default1.admin.layout.admin')

@section('Settings')
class="active"
@stop

@section('settings-bar')
active
@stop

@section('dashboard-statistics')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.dashboard-statistics') !!}</h1>
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
	<div class="row">
		<div class="col-md-12">
			@if(Session::has('success'))
            <div class="alert alert-success alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
               {!!Session::get('success')!!}
            </div>
            @endif
			@if(Session::has('fails'))
            <div class="alert alert-danger alert-dismissable">
                <i class="fas fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b>{!! Lang::get('lang.alert') !!}!</b><br/>
                <li class="error-message-padding">{!!Session::get('fails')!!}</li>
            </div>
            @endif
            @if(Session::has('errors'))
            <?php //dd($errors); ?>
            <div class="alert alert-danger alert-dismissable">
                <i class="fas fa-ban"></i>
                <b>{!! Lang::get('lang.alert') !!}!</b>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <br/>
            </div>
            @endif
			{!! Form::open(['url' => route('dashboard-statistics'), 'method' => 'POST', 'id' => 'option-form']) !!}
			<div class="card card-light">
				<div class="card-header">
                		<h3 class="card-title">{{Lang::get('lang.select_default_tabs')}}</h3>
              	</div>
              	<div class="card-body">
              		<div class="row">
              				<div class="col-md-6 form-group">
              					{!! Form::input('checkbox', 'options[]', 'departments', ['disabled' => true, 'checked' => true]) !!}&nbsp;{!! Lang::get('lang.departments') !!}
              				</div>
              				<div class="col-md-6 form-group">
              					{!! Form::input('checkbox', 'options[]', 'agents', ['disabled' => true, 'checked' => true]) !!}&nbsp;{!! Lang::get('lang.agents') !!}
              				</div>
                  </div>
                  <div class="row">
              				<div class="col-md-6 form-group">
              					{!! Form::input('checkbox', 'options[]', 'teams', ['disabled' => true, 'checked' => true]) !!}&nbsp;{!! Lang::get('lang.teams') !!}
              				</div>
              				<div class="col-md-6 form-group">
              					<?php $show_helptopic = (in_array('helptopics', $dashboard_statistics)) ? true : false; ?>
              					{!! Form::input('checkbox', 'options[]', 'helptopics', ['checked' => $show_helptopic, 'onclick' => 'toggleCheck()']) !!}&nbsp;{!! Lang::get('lang.helptopics') !!}
              				</div>
                  </div>
                  <div class="row">
              				<div class="col-md-6 form-group">
              					<?php $show_priority = (in_array('priority', $dashboard_statistics)) ? true : false; ?>
              					{!! Form::input('checkbox', 'options[]', 'priority', ['checked' => $show_priority, 'onclick' => 'toggleCheck()']) !!}&nbsp;{!! Lang::get('lang.priority') !!}
              				</div>
              				<div class="col-md-6 form-group">
              					<?php $show_source = (in_array('source', $dashboard_statistics)) ? true : false; ?>
              					{!! Form::input('checkbox', 'options[]', 'source', ['checked' => $show_source, 'onclick' => 'toggleCheck()']) !!}&nbsp;{!! Lang::get('lang.source') !!}
              				</div>
                    </div>
                  <div class="row">
              				<div class="col-md-6 form-group">
              					<?php $show_sla = (in_array('sla-plans', $dashboard_statistics)) ? true : false; ?>
              					{!! Form::input('checkbox', 'options[]', 'sla-plans', ['checked' => $show_sla, 'onclick' => 'toggleCheck()']) !!}&nbsp;{!! Lang::get('lang.sla-plans') !!}
              				</div>
              				<div class="col-md-6 form-group">
              					<?php $show_labels = (in_array('labels', $dashboard_statistics)) ? true : false; ?>
              					{!! Form::input('checkbox', 'options[]', 'labels', ['checked' => $show_labels, 'onclick' => 'toggleCheck()']) !!}&nbsp;{!! Lang::get('lang.labels') !!}
              				</div>
                    </div>
                  <div class="row">
              				<div class="col-md-6 form-group">
              					<?php $show_tags = (in_array('tags', $dashboard_statistics)) ? true : false; ?>
              					{!! Form::input('checkbox', 'options[]', 'tags', ['checked' => $show_tags, 'onclick' => 'toggleCheck()']) !!}&nbsp;{!! Lang::get('lang.tags') !!}
              				</div>
              				<div class="col-md-6 form-group">
              					<?php $show_type = (in_array('type', $dashboard_statistics)) ? true : false; ?>
              					{!! Form::input('checkbox', 'options[]', 'type', ['checked' => $show_type, 'onclick' => 'toggleCheck()']) !!}&nbsp;{!! Lang::get('lang.type') !!}
              				</div>
                    </div>
              		</div>

                  <div class="card-footer">
                  <button type="submit" class="btn btn-primary" name="submit"  id="submit" data-loading-text="<i class='fas fa-sync  fa-spin fa-1x fa-fw'>&nbsp;</i> updating..."><i class="fas fa-sync">&nbsp;</i>{!!Lang::get('lang.update')!!}</button>
                </div>
              	</div>
              	
              	{!! Form::close() !!}
            </div>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
	var start_checked = $('#option-form input:checked').length;
	function toggleCheck() {
		if($('#option-form input:checked').length > start_checked || $('#option-form input:checked').length < start_checked) {
			$('#submit').prop('disabled', false);
		} else {
			$('#submit').prop('disabled', true);
		}
	}
</script>
@endpush