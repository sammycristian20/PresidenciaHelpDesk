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


<meta name="title" content="{!! Lang::get('lang.status_edit-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.status_edit-page-description') !!}">


@stop

@section('Tickets')
active
@stop

@section('status')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.status_settings') !!}</h1>
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
#ticket-status-icon-container .select2.select2-container.select2-container--default{
  width: 100px !important;
}
#ticket-status-icon-container .select2-container--default .select2-selection--single .select2-selection__rendered {
  padding-top: 2px !important;
}
</style>
@stop

@section('content')


<link href="{{assetLink('css','bootstrap-colorpicker')}}" rel="stylesheet">
<!--select 2-->
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
{!! Form::model($status,['route'=>['statuss.update', $status->id],'method'=>'PATCH','files' => true,'id'=>'Form']) !!}
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
        <h3 class="card-title">{!! Lang::get('lang.edit_status') !!}</h3>
    </div><!-- /.box-header -->
    <div class="card-body">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.name') !!}: <span class="text-red"> *</span></label><br>
                    {!! Form::text('name',null,['class'=>'form-control'])!!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('sort') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.display_order') !!}: <span class="text-red"> *</span></label><br>
                    <input type="number" name="sort" min="1" min="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control" value="{!! $status->order !!}" required>
                </div>
            </div>
            <div class="col-md-2"  id="ticket-status-icon-container">
                <div class="f'orm-group {{ $errors->has('icon_class') ? 'has-error' : '' }}">
                    <i class=></i>
                    <label>{!! Lang::get('lang.icon_class') !!}: <span class="text-red"> *</span></label><br>
                    <select class="form-control icons"  name="icon_class" style="font-family: FontAwesome, sans-serif;" required>

                        <option <?php if ($status->icon == "fas fa-edit") echo 'selected="selected"' ?> value="fas fa-edit">&#xf044</option>
                        <option <?php if ($status->icon == "fas fa-folder-open") echo 'selected="selected"' ?> value="fas fa-folder-open">&#xf07c</option>
                        <option <?php if ($status->icon == "fas fa-minus-circle") echo 'selected="selected"' ?> value="fas fa-minus-circle">&#xf056</option>
                        <option <?php if ($status->icon == "fas fa-exclamation-triangle") echo 'selected="selected"' ?> value="fas fa-exclamation-triangle">&#xf071</option>
                        <option <?php if ($status->icon == "fas fa-bars") echo 'selected="selected"' ?> value="fas fa-bars">&#xf0c9</option>
                        <option <?php if ($status->icon == "fas fa-bell") echo 'selected="selected"' ?> value="fas fa-bell">&#xf0f3</option>
                        <option <?php if ($status->icon == "fas fa-bookmark") echo 'selected="selected"' ?> value="fas fa-bookmark">&#xf02e</option>
                        <option <?php if ($status->icon == "fas fa-bug") echo 'selected="selected"' ?> value="fas fa-bug">&#xf188</option>
                        <option <?php if ($status->icon == "fas fa-bullhorn") echo 'selected="selected"' ?> value="fas fa-bullhorn">&#xf0a1</option>
                        <option <?php if ($status->icon == "fas fa-calendar") echo 'selected="selected"' ?> value="fas fa-calendar">&#xf133</option>
                        <option <?php if ($status->icon == "fas fa-cart-plus") echo 'selected="selected"' ?> value="fas fa-cart-plus">&#xf217</option>
                        <option <?php if ($status->icon == "fas fa-check") echo 'selected="selected"' ?> value="fas fa-check">&#xf00c</option>
                        <option <?php if ($status->icon == "far fa-check-circle") echo 'selected="selected"' ?> value="far fa-check-circle">&#xf058</option>
                        <option <?php if ($status->icon == "fas fa-check-circle") echo 'selected="selected"' ?> value="fas fa-check-circle">&#xf058</option>
                        <option <?php if ($status->icon == "far fa-check-square") echo 'selected="selected"' ?> value="far fa-check-square">&#xf14a</option>
                        <option <?php if ($status->icon == "fas fa-check-square") echo 'selected="selected"' ?> value="fas fa-check-square">&#xf14a</option>
                        <option <?php if ($status->icon == "fas fa-circle-notch") echo 'selected="selected"' ?> value="fas fa-circle-notch">&#xf1ce</option>
                        <option <?php if ($status->icon == "fas fa-clock") echo 'selected="selected"' ?> value="fas fa-clock">&#xf017</option>
                        <option <?php if ($status->icon == "fas fa-times") echo 'selected="selected"' ?> value="fas fa-times">&#xf00d</option>
                        <option <?php if ($status->icon == "fas fa-code") echo 'selected="selected"' ?> value="fas fa-code">&#xf121</option>
                        <option <?php if ($status->icon == "far fa-hand-paper") echo 'selected="selected"' ?> value="far fa-hand-paper">&#xf256</option>
                        <option <?php if ($status->icon == "fas fa-hourglass-half") echo 'selected="selected"' ?> value="fas fa-hourglass-half">&#xf252</option>
                        <option <?php if ($status->icon == "fas fa-cog") echo 'selected="selected"' ?> value="fas fa-cog">&#xf013</option>
                        <option <?php if ($status->icon == "fas fa-cogs") echo 'selected="selected"' ?> value="fas fa-cogs">&#xf085</option>
                        <option <?php if ($status->icon == "far fa-comment") echo 'selected="selected"' ?> value="far fa-comment">&#xf075</option>
                        <option <?php if ($status->icon == "fas fa-comment") echo 'selected="selected"' ?> value="fas fa-comment">&#xf075</option>
                        <option <?php if ($status->icon == "far fa-comment-dots") echo 'selected="selected"' ?> value="far fa-comment-dots">&#xf4ad</option>
                        <option <?php if ($status->icon == "fas fa-comment-dots") echo 'selected="selected"' ?> value="fas fa-comment-dots">&#xf4ad</option>
                        <option <?php if ($status->icon == "far fa-comments") echo 'selected="selected"' ?> value="far fa-comments">&#xf086</option>
                        <option <?php if ($status->icon == "fas fa-comments") echo 'selected="selected"' ?> value="fas fa-comments">&#xf086</option>
                        <option <?php if ($status->icon == "fas fa-edit") echo 'selected="selected"' ?> value="fas fa-edit">&#xf044</option>
                        <option <?php if ($status->icon == "far fa-envelope") echo 'selected="selected"' ?> value="far fa-envelope">&#xf0e0</option>
                        <option <?php if ($status->icon == "fas fa-exchange-alt") echo 'selected="selected"' ?> value="fas fa-exchange-alt">&#xf362</option>
                        <option <?php if ($status->icon == "fas fa-exclamation") echo 'selected="selected"' ?> value="fas fa-exclamation">&#xf12a</option>
                        <option <?php if ($status->icon == "fas fa-exclamation-triangle") echo 'selected="selected"' ?> value="fas fa-exclamation-triangle">&#xf071</option>
                        <option <?php if ($status->icon == "fas fa-external-link-alt") echo 'selected="selected"' ?> value="fas fa-external-link-alt">&#xf35d</option>
                        <option <?php if ($status->icon == "fas fa-eye") echo 'selected="selected"' ?> value="fas fa-eye">&#xf06e</option>
                        <option <?php if ($status->icon == "fas fa-rss") echo 'selected="selected"' ?> value="fas fa-rss">&#xf09e</option>
                        <option <?php if ($status->icon == "far fa-flag") echo 'selected="selected"' ?> value="far fa-flag">&#xf024</option>
                        <option <?php if ($status->icon == "fas fa-bolt") echo 'selected="selected"' ?> value="fas fa-bolt">&#xf0e7</option>
                        <option <?php if ($status->icon == "far fa-folder") echo 'selected="selected"' ?> value="far fa-folder">&#xf07b</option>
                        <option <?php if ($status->icon == "far fa-folder-open") echo 'selected="selected"' ?> value="far fa-folder-open">&#xf07c</option>
                        <option <?php if ($status->icon == "fas fa-users") echo 'selected="selected"' ?> value="fas fa-users">&#xf0c0</option>
                        <option <?php if ($status->icon == "fas fa-info") echo 'selected="selected"' ?> value="fas fa-info">&#xf129</option>
                        <option <?php if ($status->icon == "fas fa-life-ring") echo 'selected="selected"' ?> value="fas fa-life-ring">&#xf1cd</option>
                        <option <?php if ($status->icon == "fas fa-chart-line") echo 'selected="selected"' ?> value="fas fa-chart-line">&#xf201</option>
                        <option <?php if ($status->icon == "fas fa-location-arrow") echo 'selected="selected"' ?> value="fas fa-location-arrow">&#xf124</option>
                        <option <?php if ($status->icon == "fas fa-lock") echo 'selected="selected"' ?> value="fas fa-lock">&#xf023</option>
                        <option <?php if ($status->icon == "fas fa-share") echo 'selected="selected"' ?> value="fas fa-share">&#xf064</option>
                        <option <?php if ($status->icon == "fas fa-reply") echo 'selected="selected"' ?> value="fas fa-reply">&#xf3e5</option>
                        <option <?php if ($status->icon == "fas fa-reply-all") echo 'selected="selected"' ?> value="fas fa-reply-all">&#xf122</option>
                        <option <?php if ($status->icon == "fas fa-times") echo 'selected="selected"' ?> value="fas fa-times">&#xf00d</option>
                        <option <?php if ($status->icon == "fas fa-trash") echo 'selected="selected"' ?> value="fas fa-trash">&#xf1f8</option>
                        <option <?php if ($status->icon == "fas fa-user") echo 'selected="selected"' ?> value="fas fa-user">&#xf007</option>
                        <option <?php if ($status->icon == "fas fa-user-plus") echo 'selected="selected"' ?> value="fas fa-user-plus">&#xf234</option>
                        <option <?php if ($status->icon == "fas fa-user-secret") echo 'selected="selected"' ?> value="fas fa-user-secret">&#xf21b</option>
                        <option <?php if ($status->icon == "fas fa-user-times") echo 'selected="selected"' ?> value="fas fa-user-times">&#xf235</option>
                        <option <?php if ($status->icon == "fas fa-users") echo 'selected="selected"' ?> value="fas fa-users">&#xf0c0</option>
                        <option <?php if ($status->icon == "fas fa-wrench") echo 'selected="selected"' ?> value="fas fa-wrench">&#xf0ad</option>
                        <option <?php if ($status->icon == "fas fa-circle-notch") echo 'selected="selected"' ?> value="fas fa-circle-notch">&#xf1ce</option>
                        <option <?php if ($status->icon == "fas fa-sync") echo 'selected="selected"' ?> value="fas fa-sync">&#xf021</option>
                        <option <?php if ($status->icon == "fas fa-spinner") echo 'selected="selected"' ?> value="fas fa-spinner">&#xf110</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('icon_color') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.icon_color') !!}: <span class="text-red"> *</span></label><br>
                    <input type="text" name="icon_color" value="{!! $status->icon_color !!}" class="form-control  my-colorpicker1" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 form-group">
                {!! Form::label('visibility',Lang::get('lang.visibility_to_client')) !!}   <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::radio('visibility_for_client',1, true ,['id' => 'state1', 'onclick' => 'handleClick(this);']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::radio('visibility_for_client',0,false ,['id' => 'state1', 'onclick' => 'handleClick(this);']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.if_yes_status_name_will_be_displayed') !!}</div>
            </div>
            <div class="col-md-2 form-group">
                {!! Form::label('allow_client',Lang::get('lang.allow_client')) !!}   <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::radio('allow_client',1,true,['id' => 'allow_client']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::radio('allow_client',0,false,['id' => 'allow_client']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.if_yes_then_clients_can_choose_this_status') !!}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
                {!! Form::label('visibility',Lang::get('lang.purpose_of_status')) !!}  <span class="text-red"> *</span>
                <?php
                $status_types = App\Model\helpdesk\Ticket\TicketStatusType::where('id', '!=', 3)->get();
                ?>
                <select name="purpose_of_status" class="form-control"  id="purpose_of_status" onchange="changeStatusType()" required>
                    @foreach($status_types as $status_type)
                        <option value="{!! $status_type->id !!}"  <?php if($status->purpose_of_status == $status_type->id) { echo 'selected'; } ?> >{!! ucfirst($status_type->name) !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-8">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.purpose_of_status_will_perform_the_action_to_be_applied_on_the_status_selection') !!}</div>
            </div>
        </div>
        <div class="row" id="secondary" style="display:none;">
            <div class="col-md-4 form-group">
                {!! Form::label('visibility',Lang::get('lang.status_to_display')) !!}
                <select name="secondary_status" class="form-control">
                    @foreach($statusWithVisibility as $ticketStatus)
                        <option value="{!! $ticketStatus->id !!}"  <?php if($status->secondary_status == $ticketStatus->id) { echo 'selected'; } ?>>{!! ucfirst($ticketStatus->name) !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-8">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.this_status_will_be_displayed_to_client_if_visibility_of_client_chosen_no') !!}</div>
            </div>
        </div>
        <div class="row"  id="sending_email">
            <div class="col-md-6 form-group">
                {!! Form::label('send_email',Lang::get('lang.send_email')) !!}
                <div class="row">
                    <div class="col-sm-3">
                        {!! Form::checkbox("send[client]",'1',checkArray('client',$status->send_email)) !!} {{Lang::get('lang.client')}}
                    </div>
                    <div class="col-sm-3">
                        {!! Form::checkbox("send[assigned_agent_team]",'1',checkArray('assigned_agent_team',$status->send_email)) !!} {{Lang::get('lang.assigner')}}
                    </div>
                    <div class="col-sm-3">
                        {!! Form::checkbox("send[admin]",'1',checkArray('admin',$status->send_email)) !!} {{Lang::get('lang.admin')}}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.tick_who_all_to_send_notification') !!}</div>
            </div>
        </div>
        <?php \Event::dispatch('status_sms_option', [[$status]]); ?>
        <div class="row">
            <div class="col-md-8 form-group">
                {!! Form::label('message',Lang::get('lang.message')) !!}
                <textarea name="message" class="form-control" id="ckeditor" style="height:100px;" >{!! $status->message !!}</textarea>
            </div>
            <div class="col-sm-4">
                <div class="callout callout-default bg-light" style="font-style: oblique;position: relative; top: 10px;">{!! Lang::get('lang.this_message_will_be_displayed_in_the_thread_as_internal_note') !!}</div>

                   <div class="col-md-12 form-group mt-5">
                {!! Form::label('halt_sla',Lang::get('lang.halt_sla')) !!}  <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::radio('halt_sla',1,true,['id' => 'halt_sla']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::radio('halt_sla',0,false,['id' => 'halt_sla']) !!} {{Lang::get('lang.no')}}
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
            <div class="col-md-6">
                {!! Form::label('allowed_override', Lang::get('lang.allowed_override_status_to')) !!}
                {!! Form::radio('allowed_override', 'all', (count($target_status) == 0) ? true: false, ['class' => 'status-override', 'onclick' => 'handleOverride(this)']) !!}&nbsp;&nbsp;{!! Lang::get('lang.all_statuses') !!}&nbsp;&nbsp;&nbsp;&nbsp;
                {!! Form::radio('allowed_override', 'specific', (count($target_status) == 0) ? false: true, ['class' => 'status-override', 'onclick' => 'handleOverride(this)']) !!}&nbsp;&nbsp;{!! Lang::get('lang.specific_statuses') !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 select-status">
                {!! Form::label('target_status[]', Lang::get('lang.select_status')) !!}<span class="text-red"> *</span>
                {!! Form::select('target_status[]', $all_status, $target_status, ['id' => 'target_status', 'class' => 'form-control', 'multiple' => true,  'required' => true]) !!}
            </div>
        </div>
        <div class="form-group mt-2">
            <input type="checkbox" name="default" <?php if($status->default == 1) { echo "checked='checked' value='1'"; } ?>> {{ Lang::get('lang.make_system_default_for_selected_purpose') }}
        </div>
    </div>
    <div class="card-footer">
            <button type="submit" id="submit" class="btn btn-primary"><i class="fas fa-sync">&nbsp;</i>{!!Lang::get('lang.update')!!}</button>

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
    if (currentValue == '1') {
        $("#secondary").hide();
    } else if (currentValue == '0') {
        $("#secondary").show();
    }
}
$(function(){
    var myRadio = {!! $status->visibility_for_client !!};
    currentValue = myRadio;
    if (currentValue == '1') {
        $("#secondary").hide();
    } else if (currentValue == '0') {
        $("#secondary").show();
    }
});
function handleOverride(myRadio){
    if (myRadio.value == 'all') {
        handleSelectStatusField('disabled', 'none');
    } else {
        handleSelectStatusField(false, 'block');
    }
}
function handleSelectStatusField($prop, $display) {
    $('#target_status').prop('disabled', $prop);
    $('.select-status').css('display', $display);
}
@if(count($target_status) > 0)
handleSelectStatusField(false, 'block');
@else
handleSelectStatusField('disabled', 'none');
@endif
// $(function(){
//     var purpose_of_status = document.getElementById('purpose_of_status').value;
//     if(purpose_of_status == 2) {
//         $('#sending_email').show();
//     } else {
//         $('#sending_email').hide();
//     }
// });
// function changeStatusType() {
//     var purpose_of_status = document.getElementById('purpose_of_status').value;
//     if(purpose_of_status == 2) {
//         $('#sending_email').show();
//     } else {
//         $('#sending_email').hide();
//     }
// }
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