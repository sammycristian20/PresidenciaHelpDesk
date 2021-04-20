@extends('themes.default1.client.layout.client')


<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>
<link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css" />


@section('title')
{!! Lang::get('lang.submit_a_ticket') !!} -
@stop

<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (isset($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }

        ?>

@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.submit_a_ticket-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.submit_a_ticket-page-description') !!}">


@stop

<style>
   .btn-bs-file{
    position:relative;
}
.align {
    margin-bottom: 6px;
}
.select2-container .select2-selection--multiple{
                  height: 34px;
                  border-radius: 0px !important;
                  border: 1px solid #d2d6de !important;
            }
.btn-bs-file input[type="file"]{
    position: absolute;
    top: -9999999;
    filter: alpha(opacity=0);
    opacity: 0;
    width:0;
    height:0;
    outline: none;
    cursor: inherit;
}
</style>
@section('submit')
class = "active"
@stop
<!-- breadcrumbs -->
@section('breadcrumb')
<div class="site-hero clearfix">
    <ol class="breadcrumb breadcrumb-custom">
        <li class="text">{!! Lang::get('lang.you_are_here') !!}: </li>
        <li><a href="{!! URL::route('form') !!}">{!! Lang::get('lang.submit_a_ticket') !!}</a></li>
    </ol>
</div>
@stop
<!-- /breadcrumbs -->
@section('check')

<?php
$portal = App\Model\helpdesk\Theme\Portal::where('id', '=', 1)->first();
?>

@if($portal)

<div class="banner-wrapper  clearfix"    style = "border-color : <?php echo $portal->client_header_color; ?>">
    <h3 class="banner-title text-center text-info h4">{!! Lang::get('lang.have_a_ticket') !!}?</h3>
    @if(Session::has('check'))
    @if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissable">
        <i class="fa fa-ban"></i>
        <b>{!! Lang::get('lang.alert') !!} !</b>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        @foreach ($errors->all() as $error)
        <li>{{ $error}}</li>
        @endforeach
    </div>
    @endif
    @endif
    <div class="banner-content text-center">
        {!! Form::open(['url' => 'checkmyticket' , 'method' => 'POST'] )!!}
        <div class="align">
        {!! Form::label('email',Lang::get('lang.email')) !!}<span class="text-red"> *</span>
        {!! Form::text('email_address',null,['class' => 'form-control']) !!}
    </div>
         <div class="align1">
        {!! Form::label('ticket_number',Lang::get('lang.ticket_number')) !!}<span class="text-red"> *</span>
        {!! Form::text('ticket_number',null,['class' => 'form-control']) !!}
    </div>
        <br/><input type="submit" value="{!! Lang::get('lang.check_ticket_status') !!}" class="btn btn-custom btn-info" style = "white-space: normal;background-color : <?php echo $portal->client_header_color; ?>;border-color : <?php echo $portal->client_header_color; ?>" >
        {!! Form::close() !!}
    </div>
</div>

@else
<div class="banner-wrapper  clearfix" >
    <h3 class="banner-title text-center text-info h4">{!! Lang::get('lang.have_a_ticket') !!}?</h3>
    @if(Session::has('check'))
    @if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissable">
        <i class="fa fa-ban"></i>
        <b>{!! Lang::get('lang.alert') !!} !</b>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        @foreach ($errors->all() as $error)
        <li>{{ $error}}</li>
        @endforeach
    </div>
    @endif
    @endif
    <div class="banner-content text-center">
        {!! Form::open(['url' => 'checkmyticket' , 'method' => 'POST'] )!!}
        {!! Form::label('email',Lang::get('lang.email')) !!}<span class="text-red"> *</span>
        {!! Form::text('email_address',null,['class' => 'form-control']) !!}
        {!! Form::label('ticket_number',Lang::get('lang.ticket_number')) !!}<span class="text-red"> *</span>
        {!! Form::text('ticket_number',null,['class' => 'form-control']) !!}
        <br/><input type="submit" value="{!! Lang::get('lang.check_ticket_status') !!}" class="btn btn-custom btn-info">
        {!! Form::close() !!}
    </div>
</div>

@endif
@stop
<!-- content -->
@section('content')
<div id="content" class="site-content col-md-9" style="border: 1px solid gainsboro;">
            <div class="row">
                <div  style="border-bottom:1px solid gainsboro;background-color: white;padding:5px;" class="col-sm-12">
                        <h4>{!!Lang::get('lang.create_new_ticket')!!}</h4>
                </div>
            </div>
            <div class="row" style="margin-right:0px">
                <div class="box-body" id="app-vue">
                    <create-form :person="'user'" :category="'ticket'"></create-form>
                </div>
            </div>
            <script src="{{asset('js/lang')}}" type="text/javascript"></script>
            <script src="{{asset('js/common.js')}}" type="text/javascript"></script>
            <script src="{{asset('js/app.js')}}" type="text/javascript"></script>

        <script src="{{asset('vendor/unisharp/laravel-ckeditor/ckeditor.js')}}"></script>

</div>
@stop

@push('scripts')
<script src="{{asset("lb-faveo/js/intlTelInput.min.js")}}" async></script>
@endpush
