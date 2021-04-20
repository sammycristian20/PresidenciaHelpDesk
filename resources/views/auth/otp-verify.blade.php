@extends('themes.default1.client.layout.logclient')

<link href="{{asset("lb-faveo/js/intlTelInput.min.css")}}" rel="stylesheet" type="text/css">
@section('home')
    class = "active"
@stop

@section('HeadInclude')
<link href="{{asset("lb-faveo/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}" rel="stylesheet" type="text/css" />
           <link href="{{asset("lb-faveo/css/widgetbox.min.css")}}" rel="stylesheet" type="text/css" />
        <link href="{{asset("lb-faveo/plugins/iCheck/flat/blue.min.css")}}" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        {{-- <link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css"> --}}
        <link href="{{asset("lb-faveo/css/jquerysctipttop.min.css")}}" rel="stylesheet" type="text/css">
        <link href="{{asset("lb-faveo/js/intlTelInput.min.css")}}" rel="stylesheet" type="text/css">
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
@stop

@section('breadcrumb')
    <div class="site-hero clearfix">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="text">{!! Lang::get('lang.you_are_here') !!}: </li>
            <li><a href="{!! URL::route('/') !!}">{!! Lang::get('lang.home') !!}</a></li>
        </ol>
    </div>
@stop
@section('content')
@if(Session::has('status'))
<div class="alert alert-success alert-dismissable">
    <i class="fa  fa-check-circle"> </i> <b> {!! Lang::get('lang.success') !!} </b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('status')}}
</div>
@endif

@if (count($errors) > 0 || Session::has('code_error'))
<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"></i>
    <b>{!! Lang::get('lang.alert') !!} !</b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
    @if(Session::has('code_error'))
        {{ Session::get('code_error') }}
    @endif
</div>
@endif
<div class="alert alert-success alert-dismissable" style="display: none;">
    <i class="fa  fa-check-circle"> </i> <b> {!! Lang::get('lang.success') !!} </b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span class="success_message"></span>
</div>
<div class="alert alert-danger alert-dismissable" style="display: none;">
    <i class="fa  fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!} </b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span class="error_message"></span>
</div>
<div id="content" class="site-content col-md-12">
    <div id="corewidgetbox">
        <div class="widgetrow text-center">
        <?php $system = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();            
        ?>
        @if($system != null) 
            @if($system->status) 
                @if($system->status == 1)
                    <span onclick="javascript: window.location.href='{!! URL::route('form') !!}';">
                        <a href="{!! URL::route('form') !!}" class="widgetrowitem defaultwidget" style="background-image:url({{ URL::asset('lb-faveo/media/images/submitticket.png') }})">
                            <span class="widgetitemtitle">{!! Lang::get('lang.submit_a_ticket') !!}</span>
                        </a>
                    </span>
                @endif
            @endif
        @endif
            <span onclick="javascript: window.location.href='{{url('mytickets')}}';">
                <a href="{{url('mytickets')}}" class="widgetrowitem defaultwidget" style="background-image:url({{ URL::asset('lb-faveo/media/images/news.png') }})">
                    <span class="widgetitemtitle">{!! Lang::get('lang.my_tickets') !!}</span>
                </a>
            </span>
            <span onclick="javascript: window.location.href='{{url('/knowledgebase')}}';">
                <a href="{{url('/knowledgebase')}}" class="widgetrowitem defaultwidget" style="background-image:url({{ URL::asset('lb-faveo/media/images/knowledgebase.png') }})">
                    <span class="widgetitemtitle">{!! Lang::get('lang.knowledge_base') !!}</span>
                </a>
            </span>
        </div>
    </div>
    <div class="login-box" style=" width: 500px; display: none;"  valign="center" id="otp">
        <div class="form-border">
            <div align="center">
            </div>
            <div>
                <h4 class="box-title" align="center">
                    <p>Hello
                    @if (Session::has('name'))
                        {{ Session::get('name')}}
                    @endif!
                    </p><br/><span style='font-size: .8em' id="otp_message">
                    </span>
                </h4>
            </div>   
            <!-- form open -->
            <!-- form open -->
{!!  Form::open(['route'=> 'otp-verification', 'method'=>'post']) !!}
<!-- Email -->
<div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
    {!! Form::hidden('email',null,['placeholder'=> Lang::get("lang.email") ,'class' => 'form-control']) !!}
    <!-- {!! $errors->first('email', '<spam class="help-block">:message</spam>') !!} -->
</div>

<!-- Password -->
<div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
    {!! Form::hidden('password',['placeholder'=>Lang::get("lang.password"),'class' => 'form-control']) !!}
    {!! Form::hidden('mobile', null) !!}
    {!! Form::hidden('country_code', null) !!}
    <!-- {!! $errors->first('password', '<spam class="help-block">:message</spam>') !!} -->
</div>
<div class="form-group has-feedback {{ $errors->has('otp') ? 'has-error' : '' }}">
    {!! Form::input('text','otp',null,['placeholder'=> Lang::get("lang.enter-otp") ,'class' => 'form-control' , 'required' => true, 'pattern' => "[0-9]{6}", "title" => Lang::get('lang.otp-input-title')]) !!}
    <!-- {!! $errors->first('email', '<spam class="help-block">:message</spam>') !!} -->
</div>
@if (Session::has('referer'))
    <input type='hidden' name="referer" value="{!! Session::get('referer') !!}">
@elseif(Session::has('errors'))
    <input type='hidden' name="referer" value="form">
@endif
<div class="row">
    <div class="col-xs-8">
        {!! Lang::get('lang.did-not-recive-code') !!}<br/>
        <a id="resend" onclick="resendOTP();" title="{!!Lang::get('lang.resend-otp-title') !!}" style="pointer-events: initial; cursor: pointer;">{!! Lang::get("lang.resend_otp") !!}</a> /
        <a id="edit_number" onclick="updateMobile();" title="{!!Lang::get('lang.enter_different_number') !!}" style="pointer-events: initial; cursor: pointer;">{!! Lang::get("lang.edit_number") !!}</a><br>
    </div><!-- /.col -->
    <div class="col-xs-4">
        <button type="submit" class="btn btn-primary btn-block btn-flat">{!! Lang::get("lang.verify") !!}</button>
    </div><!-- /.col -->
</div>
            <div>
                <div class="checkbox icheck" align="center">
                    
                </div>
            </div><!-- /.col --> 
        </div>
    </div>
    <div class="login-box" style=" width: 500px;"  valign = "center" id="number">
        <div class="form-border">
            <div align="center">
            </div>
            <div>
                <h4 class="box-title" align="center"><p>Hello
@if (Session::has('name'))
    {{ Session::get('name')}}
@endif!</p><br/>
<span style='font-size: .8em'>
{!!Lang::get('lang.verify-screen-msg')!!}
    <?php
    $value = Session::get('values');
    if (array_key_exists('referer', $value)) {
        $referer = $value['referer'];
    } else {
        $referer = '/';
    }
    $email = $value['email'];
    $password = $value['password'];
    $number = '';
    $code = Session::get('code');
    if (is_numeric(Session::get('number'))) {
        $number =  Session::get('number');
    }
    ?>
    </span></h4>
            </div>   
            <!-- form open -->
            <!-- fullname -->
            <div class="form-group has-feedback {{ $errors->has('full_name') ? 'has-error' : '' }}">
                {!! Form::text('mobile',$number,['placeholder'=>Lang::get('lang.mobile'),'class' => 'form-control  numberOnly', 'id' => 'mobile', "required" => true]) !!}
                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div>
            <div >
                <button type="submit" class="btn btn-primary btn-block btn-flat" id="send_otp" onclick="resendOTP()">{!! Lang::get('lang.send_otp') !!}</button>
            </div>
            <div>
                <div class="checkbox icheck" align="center">
                    
                </div>
            </div><!-- /.col --> 
        </div>
    </div>
</div>
<style>
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $(".numberOnly").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                 // Allow: Ctrl/cmd+A
                (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl/cmd+C
                (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl/cmd+X
                (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });
    function resendOTP() {
        var mobile = document.getElementById('mobile').value;
        var code = $('.selected-dial-code').text();
        if(mobile == '') {
            $('.alert-danger').css('display', 'block');
            $('.error_message').html("{!! Lang::get('lang.required-error') !!}");
            return 0;
        }
        $.ajax({                    
            url: '{{URL::route("resend-otp")}}',     
            type: 'post', // performing a POST request
            dataType: "html",
            data : {
                "mobile" : mobile,
                "user_id" : "{!! Session::get('user_id') !!}",
                "code" : code,
                "_token": "{{ csrf_token() }}",
            },
            beforeSend: function() {
    $('.loader1').css('display','block');
                $('#resend').text("{!! Lang::get('lang.sending') !!}");
                $('#otp_message').html("{!! Lang::get('lang.otp_sending_message') !!}")
                $('#send_otp').text("{!! Lang::get('lang.sending') !!}");
                $('#send_otp').prop("disabled", true);
            },
            success: function(response) {
                $('#resend').text("{!! Lang::get('lang.resend_otp') !!}");
                $('#send_otp').text("{!! Lang::get('lang.send_otp') !!}");
                $('#send_otp').prop("disabled", false);
                var response = JSON.parse(response);
                if (response == 1) {
                    $('#number').css('display', 'none');
                    $('#otp_message').html("{!! Lang::get('lang.enter-otp-message-to-users') !!}");
                    if (document.getElementById('otp').style.display != 'block') {
                        $('#otp').css('display', 'block');
                    } else {
                        $('.alert-success').css('display', 'block');
                        $('.success_message').html("{!! Lang::get('lang.otp-sent-successfully') !!}");
                    }
                } else {
                    if (!'error' in response) {
                        $('#number').css('display', 'none');
                        $('#otp_message').html("{!! Lang::get('lang.enter-otp-message-to-users') !!}");
                        if (document.getElementById('otp').style.display != 'block') {
                            $('#otp').css('display', 'block');
                        } else {
                            $('.alert-success').css('display', 'block');
                            $('.success_message').html("{!! Lang::get('lang.otp-sent-successfully') !!}");
                        }
                    } else {
                        $('.alert-danger').css('display', 'block');
                        $('.error_message').html(response.error);
                    }
                }
            },
            error: function(response) {
                $('#resend').text("{!! Lang::get('lang.resend_otp') !!}");
                $('#send_otp').text("{!! Lang::get('lang.send_otp') !!}");
                $('#send_otp').prop("disabled", false);
                var response = JSON.parse(response.responseText);
                $('.alert-danger').css('display', 'block');
                $('.error_message').html(response.mobile[0]);
            },
            complete: function( jqXHR, textStatus) {
                
            }
        });
    }

    function updateMobile() {
        $('#number').css('display', 'block');
        $('#otp').css('display', 'none');
    }
</script>
<script src="{{asset('lb-faveo/js/intlTelInput.min.js')}}"></script>
<script type="text/javascript">
    var telInput = $('#mobile');
    var code = "{!! $code !!}"
    telInput.intlTelInput({
        geoIpLookup: function(callback) {
            $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        initialCountry: "{!! $code !!}",
        separateDialCode: true,
        utilsScript: "{{asset('lb-faveo/js/utils.min.js')}}",
        formatOnDisplay : false,
    });
    $('.intl-tel-input').css('width', '100%');

    telInput.on('blur', function() {
        if ($.trim(telInput.val())) {
            if (!telInput.intlTelInput("isValidNumber")) {
                telInput.parent().addClass('has-error');
            }
        }
    });
    $('form').on('submit', function(e){
        if ($('#mobile').parent().hasClass('has-error')) {
            var alert_msg = "{{Lang::get('lang.please-check-mobile-number')}}";
alert(alert_msg);
            e.preventDefault();
        }
        $('input[name=country_code]').attr('value', $('.selected-dial-code').text());
        $('input[name=mobile]').attr('value', document.getElementById('mobile').value);
    });
</script>
@stop
