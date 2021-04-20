@extends('themes.default1.admin.layout.admin')

@section('Settings')
active
@stop
@section('settings-bar')
active
@stop
@section('MicroOrganization')
class="active"
@stop
@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('HelptopicType::lang.helptopic_and_type_linking') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- content -->
@section('content')
@if (Session::has('message'))
<div class="alert alert-success fade in">
    <i class="fa  fa-check-circle"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <p>{{ Session::get('message') }}</p>
</div>
@endif
<div class="alert alert-success alert-dismissable" style="display: none;">
    <i class="fa  fa-check-circle"></i>
    <span class="success-msg"></span>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h2 class="box-title">{{Lang::get('lang.settings')}}</h2>
    </div>

    <div class="box-body">
         <a class="right" title="" data-placement="right" data-toggle="tooltip" href="#" data-original-title="{{Lang::get('lang.organization_department_add_ability_to_setup_department_for_user_organization_and_this_can_be_used_for_assigning_SLA')}}">
<span class="lead" >{!! Lang::get('lang.current') !!} {!! Lang::get('lang.status') !!} </span>
         </a>
<div class="btn-group pull-right" id="toggle_event_editing">
            <button type="button"   class="btn {{$settings == '0' ? 'btn-info' : 'btn-default'}} locked_active">{{Lang::get('lang.inactive')}}</button>
            <button type="button"  class="btn {{$settings == '1' ? 'btn-info' : 'btn-default'}} unlocked_inactive">{{Lang::get('lang.active')}}</button>
        </div>



    </div>


    <div class="box-footer">

    </div>
</div>

<script>
    $('#toggle_event_editing button').click(function() {

        var current_settings = 1;
        var current_settings = 0;
        if ($(this).hasClass('locked_active')) {
            current_settings = 0
        }
        if ($(this).hasClass('unlocked_inactive')) {
            current_settings = 1;
        }

        /* reverse locking status */
        $('#toggle_event_editing button').eq(0).toggleClass('locked_inactive locked_active btn-default btn-info');
        $('#toggle_event_editing button').eq(1).toggleClass('unlocked_inactive unlocked_active btn-info btn-default');
        $.ajax({
            type: 'post',
            url: '{{route("helpdesk.helptopic-type")}}',
            data: {
                "_token": "{{ csrf_token() }}",
                current_settings: current_settings
            },
            success: function(result) {
                $('.success-msg').html(result);
                $('.alert-success').css('display', 'block');
                setInterval(function() {
                    $('.alert-success').slideUp(3000, function() {
                    });
                }, 500);
            }
        });
    });
</script>

@stop