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


<meta name="title" content="{!! Lang::get('lang.status_create-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.status_create-page-description') !!}">


@stop

@section('Tickets')
active
@stop

@section('status')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.status') !!}</h1>
@stop
@section('custom-css')
<style type="text/css">
    .select2-container--default .select2-selection--single {
  
    border-radius: 0.25rem !important;
    height: 36px !important;
    border: 1px solid #ced4da !important;
}

.select2-selection--single .select2-selection__arrow{ top: 5px !important; }

#target_status { height: auto !important; }
.select2-container .select2-selection--single .select2-selection__rendered {
   
    padding-top: 9px;
}

#status-icon-class .select2-container {
    width: 100px !important;
}

#status-icon-class .select2-container .select2-selection--single .select2-selection__rendered {
    padding-top: 2px !important;
}
</style>
@stop
@section('content')

<link href="{{assetLink('css','bootstrap-colorpicker')}}" rel="stylesheet">
<!--select 2-->
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
{!! Form::open(['route'=>['statuss.store'],'method'=>'POST','files' => true,'id'=>'Form']) !!}
 @if(Session::has('errors'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
            @foreach ($errors->all() as $error)
            <li class="error-message-padding">{{ $error }}</li>
            @endforeach 
        </div>
        @endif
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fas fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! Session::get('success') !!}
        </div>
        @endif
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!} !</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>{!! Session::get('fails') !!}</p>
        </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.create_new_status') !!}</h3>
    </div><!-- /.box-header -->
    <div class="card-body">
       
        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.name') !!}: <span class="text-red"> *</span></label><br>
                    {!! Form::text('name',null,['class'=>'form-control', 'required'])!!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('sort') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.display_order') !!}: <span class="text-red"> *</span></label><br>
                    <input type="number" min="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" name="sort"  class="form-control" value="" required>
                </div>
            </div>
            <div id="status-icon-class" class="col-md-2">
                <div class="form-group {{ $errors->has('icon_class') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.icon') !!}: <span class="text-red"> * </span></label><br>
                    <select class="form-control icons"  name="icon_class" style="font-family: FontAwesome, sans-serif;" required>
                        <option value="fas fa-edit">&#xf044</option>
                        <option value="fas fa-folder-open">&#xf07c</option>
                        <option value="fas fa-minus-circle">&#xf056</option>
                        <option value="fas fa-exclamation-triangle">&#xf071</option>
                        <option value="fas fa-bars">&#xf0c9</option>
                        <option value="fas fa-bell">&#xf0f3</option>
                        <option value="fas fa-bookmark">&#xf02e</option>
                        <option value="fas fa-bug">&#xf188</option>
                        <option value="fas fa-bullhorn">&#xf0a1</option>
                        <option value="fas fa-calendar">&#xf133</option>
                        <option value="fas fa-cart-plus">&#xf217</option>
                        <option value="fas fa-check">&#xf00c</option>
                        <option value="far fa-check-circle">&#xf058</option>
                        <option value="fas fa-check-circle">&#xf058</option>
                        <option value="far fa-check-square">&#xf14a</option>
                        <option value="fas fa-check-square">&#xf14a</option>
                        <option value="fas fa-circle-notch">&#xf1ce</option>
                        <option value="fas fa-clock">&#xf017</option>
                        <option value="fas fa-times">&#xf00d</option>
                        <option value="fas fa-code">&#xf121</option>
                        <option value="fas fa-cog">&#xf013</option>
                        <option value="fas fa-cogs">&#xf085</option>
                        <option value="far fa-comment">&#xf075</option>
                        <option value="fas fa-comment">&#xf075</option>
                        <option value="far fa-comment-dots">&#xf4ad</option>
                        <option value="fas fa-comment-dots">&#xf4ad</option>
                        <option value="far fa-comments">&#xf086</option>
                        <option value="fas fa-comments">&#xf086</option>
                        <option value="fas fa-edit">&#xf044</option>
                        <option value="far fa-envelope">&#xf0e0</option>
                        <option value="fas fa-exchange-alt">&#xf362</option>
                        <option value="fas fa-exclamation">&#xf12a</option>
                        <option value="fas fa-exclamation-triangle">&#xf071</option>
                        <option value="far fa-hand-paper">&#xf256</option>
                        <option value="fas fa-hourglass-half">&#xf252</option>
                        <option value="fas fa-external-link-alt">&#xf35d</option>
                        <option value="fas fa-eye">&#xf06e</option>
                        <option value="fas fa-rss">&#xf09e</option>
                        <option value="far fa-flag">&#xf024</option>
                        <option value="fas fa-bolt">&#xf0e7</option>
                        <option value="far fa-folder">&#xf07b</option>
                        <option value="far fa-folder-open">&#xf07c</option>
                        <option value="fas fa-users">&#xf0c0</option>
                        <option value="fas fa-info">&#xf129</option>
                        <option value="fas fa-life-ring">&#xf1cd</option>
                        <option value="fas fa-chart-line">&#xf201</option>
                        <option value="fas fa-location-arrow">&#xf124</option>
                        <option value="fas fa-lock">&#xf023</option>
                        <option value="fas fa-share">&#xf064</option>
                        <option value="fas fa-reply">&#xf3e5</option>
                        <option value="fas fa-reply-all">&#xf122</option>
                        <option value="fas fa-times">&#xf00d</option>
                        <option value="fas fa-trash">&#xf1f8</option>
                        <option value="fas fa-user">&#xf007</option>
                        <option value="fas fa-user-plus">&#xf234</option>
                        <option value="fas fa-user-secret">&#xf21b</option>
                        <option value="fas fa-user-times">&#xf235</option>
                        <option value="fas fa-users">&#xf0c0</option>
                        <option value="fas fa-wrench">&#xf0ad</option>
                        <option value="fas fa-circle-notch">&#xf1ce</option>
                        <option value="fas fa-sync">&#xf021</option>
                        <option value="fas fa-spinner">&#xf110</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('icon_color') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.icon_color') !!}: <span class="text-red"> *</span></label><br>
                    <input type="text" name="icon_color" class="form-control  my-colorpicker1" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 form-group">
                {!! Form::label('visibility',Lang::get('lang.visibility_to_client')) !!}  <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::radio('visibility_for_client','yes',true,['id' => 'visibility_for_client', 'onclick' => 'handleClick(this);']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::radio('visibility_for_client','no',false,['id' => 'visibility_for_client', 'onclick' => 'handleClick(this);']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="callout callout-default bg-light" style="font-style: oblique; position: relative; top: 10px;">{!! Lang::get('lang.if_yes_status_name_will_be_displayed') !!}</div>
            </div>
            <div class="col-md-2 form-group">
                {!! Form::label('allow_client',Lang::get('lang.allow_client')) !!}  <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::radio('allow_client','yes',true,['id' => 'allow_client']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::radio('allow_client','no',false,['id' => 'allow_client']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.if_yes_then_clients_can_choose_this_status') !!}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
                {!! Form::label('purpose_of_status',Lang::get('lang.purpose_of_status')) !!}  <span class="text-red"> *</span>
                <select name="purpose_of_status" class="form-control" id="purpose_of_status" onchange="changeStatusType()" required>
                    @foreach($status_types as $status_type)
                        <option value="{!! $status_type->id !!}">{!! ucfirst($status_type->name) !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-8">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.purpose_of_status_will_perform_the_action_to_be_applied_on_the_status_selection') !!}</div>
            </div>
        </div>
        <div class="row" id="secondary" style="display:none;">
            <div class="col-md-4 form-group">
                {!! Form::label('status_to_display',Lang::get('lang.status_to_display')) !!}  <span class="text-red"> *</span>
                <select name="secondary_status" class="form-control">
                    @foreach($statusWithVisibility as $ticketStatus)
                        <option value="{!! $ticketStatus->id !!}">{!! ucfirst($ticketStatus->name) !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-8">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.this_status_will_be_displayed_to_client_if_visibility_of_client_chosen_no') !!}</div>
            </div>
        </div>
        <div class="row"  id="sending_email" >
            <div class="col-md-5 form-group">
                {!! Form::label('send_email',Lang::get('lang.send_email')) !!}
                <div class="row">
                    <div class="col-sm-3">
                        {!! Form::checkbox("send[client]") !!} {{Lang::get('lang.client')}}
                    </div>
                    <div class="col-sm-3">
                        {!! Form::checkbox("send[assigned_agent_team]") !!} {{Lang::get('lang.assigner')}}
                    </div>
                    <div class="col-sm-3">
                        {!! Form::checkbox("send[admin]") !!} {{Lang::get('lang.admin')}}
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.tick_who_all_to_send_notification') !!}</div>
            </div>
        </div>
        <?php \Event::dispatch('status_sms_option', [[]]); ?>
        <div class="row">
            <div class="col-md-8 form-group">
                {!! Form::label('message',Lang::get('lang.message')) !!}
                <textarea name="message" class="form-control" id="ckeditor" style="height:100px;"></textarea>
            </div>
            <div class="col-sm-4">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.this_message_will_be_displayed_in_the_thread_as_internal_note') !!}</div>

                <div class="col-md-12 form-group mt-5">
                {!! Form::label('halt_sla',Lang::get('lang.halt_sla')) !!}  <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::radio('halt_sla','1',true,['id' => 'halt_sla']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::radio('halt_sla','0',false,['id' => 'halt_sla']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.if_required_comment_is_mandatory_while_changing_the_ticket_status') !!}</div>
            </div>
            <div class="col-md-12 form-group">
                {!! Form::label('comment',Lang::get('lang.comment')) !!}  <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::radio('comment',1,true,['id' => 'comment']) !!} {{Lang::get('lang.required')}}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::radio('comment',0,false,['id' => 'comment']) !!} {{Lang::get('lang.optional')}}
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                {!! Form::label('allowed_override', Lang::get('lang.allowed_override_status_to')) !!}
                {!! Form::radio('allowed_override', 'all', true, ['class' => 'status-override', 'onclick' => 'handleOverride(this)']) !!}&nbsp;{!! Lang::get('lang.all_statuses') !!}
                {!! Form::radio('allowed_override', 'specific', false, ['class' => 'status-override', 'onclick' => 'handleOverride(this)']) !!}&nbsp;{!! Lang::get('lang.specific_statuses') !!}
            </div>
        </div>

        <div class="row">
             <div class="col-md-6 select-status">
                {!! Form::label('target_status[]', Lang::get('lang.select_status')) !!}
                {!! Form::select('target_status[]', $all_status, null, ['id' => 'target_status', 'class' => 'form-control', 'multiple' => true, 'required' => true]) !!}
            </div>
        </div>
        <div class="form-group mt-2">
            <input type="checkbox" name="default"> {{ Lang::get('lang.make_system_default_for_selected_purpose') }}
        </div>
    </div>
    <div class="card-footer">
<!--        {!! Form::submit(Lang::get('lang.create'),['class'=>'btn btn-primary'])!!}-->
<!--        {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}-->
            <button type="submit" class="btn btn-primary" id="submit"><i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
    
    
    </div>
    {!! Form::close() !!}
</div> 
<!-- bootstrap color picker -->
<script src="{{assetLink('js','bootstrap-colorpicker')}}"></script>
<script src="{{assetLink('js','select2')}}"></script>

<script>
function format(option){
    var icon = $(option.element).attr('value');
    return '<i class="'+icon+'" ></i> ';
}
$('.icons').select2({
        templateResult: format,
        templateSelection: format,
        escapeMarkup: function (m) {
                                    return m;
                                    }                               
})
var currentValue = 0;
function handleClick(myRadio) {
    currentValue = myRadio.value;
    if(currentValue == 'yes') {
        $("#secondary").hide();
    } else if (currentValue == 'no') {
        $("#secondary").show();
    }
}
function handleOverride(myRadio){
    if (myRadio.value == 'all') {
        handleSelectStatusField('disabled', 'none');
    } else {
        handleSelectStatusField(false, 'block');
    }
}


handleSelectStatusField('disabled', 'none');

function handleSelectStatusField($prop, $display) {
    $('#target_status').prop('disabled', $prop);
    $('.select-status').css('display', $display);
}

//Colorpicker
$(".my-colorpicker1").colorpicker({format:'hex'});

$(document).ready(function(){
   $('.select2-selection__rendered').removeAttr("title");
   $(".icons").change(function(){
        $('.select2-selection__rendered').removeAttr("title");
    });
});
</script>
@stop