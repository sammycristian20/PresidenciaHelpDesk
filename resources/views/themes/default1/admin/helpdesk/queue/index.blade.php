@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.queue_inbox-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.queue_inbox-page-description') !!}">


@stop

@section('Emails')
active
@stop

@section('emails-bar')
active
@stop

@section('queue')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.queue')}}</h1>
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

@if(Session::has('success'))
<div class="alert alert-success alert-dismissable">
    <i class="fas fa-check-circle"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('success')}}
</div>
@endif
@if(Session::has('fails'))
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('fails')}}
</div>
@endif
@if(Session::has('warn'))
<div class="alert alert-warning alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('warn')}}
</div>
@endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">
            {!! Lang::get('lang.queues') !!}
        </h3>
    </div>
    <div class="card-body">
        {{-- alert block --}}
        <div class="alert alert-success cron-success alert-dismissable" style="display: none;">
            <i class="fas  fa-check-circle"></i>
            <span class="alert-success-message"></span>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
        <div class="alert alert-danger cron-danger alert-dismissable" style="display: none;">
            <i class="fas fa-ban"></i>
            <span class="alert-danger-message"></span>
            <button type="button" class="close" onclick="hideAlert()">&times;</button>
        </div>
        {{-- alert block end --}}
        <table id="example2" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{!! Lang::get('lang.name') !!}</th>
                    <th>{!! Lang::get('lang.status') !!}</th>
                    <th>{!! Lang::get('lang.action') !!}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($queues as $queue)
                <tr>
                    <?php
                    $cron_path = base_path('artisan');
                    $command   = false;
                    ?>
                    <td>
                        {!! $queue->getName() !!}
                        @if($queue->name === 'Database')
                            <a data-toggle="tooltip" title="{{ trans('lang.queue_page_instructions', ['name' => 'Database']) }}" target="_blank" href='https://support.faveohelpdesk.com/show/how-to-configure-different-queues-list-to-send-out-emails-from-faveo' ><i class='fas fa-info-circle'>&nbsp;&nbsp;</i></a>
                        @elseif($queue->name === 'Redis')
                            <a data-toggle="tooltip" title="{{ trans('lang.queue_page_instructions', ['name' => 'Redis']) }}" target="_blank" href='https://support.faveohelpdesk.com/show/install-and-configure-redis-supervisor-and-worker-for-faveo-on-centos-7' ><i class='fas fa-info-circle'>&nbsp;&nbsp;</i></a>
                        @endif
                    </td>
                    <td>{!! $queue->getStatus() !!}</td>
                    <td>
                        {!! $queue->getAction() !!}
                    </td>
                </tr>
            @if($queue->name=='Database' && $queue->status=='1')
            <div class="alert  alert-dismissable noselect" style="background: #F8F8F8">
            <div class="row">
                <div class="col-md-2 copy-command1">
                    <span style="font-size: 30px">*&nbsp;&nbsp;*&nbsp;&nbsp;*&nbsp;&nbsp;*&nbsp;&nbsp;*</span>
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="phpExecutableList" onchange="checksome()">
                        <option value="0">{{trans('lang.specify-php-executable')}}</option>
                        @foreach($paths as $path)
                            <option>{{$path}}</option>
                        @endforeach
                        <option value="Other">Other</option>
                    </select>
                    <div class="has-feedback" id='phpExecutableTextArea' style="display: none;">
                        <div class="has-feedback d-flex">
                            <input type="text" class="form-control input-sm" style=" padding:5px;height:34px" name="phpExecutableText" id="phpExecutableText" placeholder="{{trans('lang.specify-php-executable')}}">
                            <span class="fas fa-times form-control-feedback" style="pointer-events: initial; cursor: pointer; color: #74777a;margin-left: -20px;margin-top: 3px;" onclick="checksome(false)"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 copy-command2">
                   <span style="font-size: 18px">-q {{$cronPath}} queue:work database >> storage/logs/cron.log</span>
                </div>
                <div class="col-md-1">
                    <span style="font-size: 20px; pointer-events: initial; cursor: pointer;" id="copyBtn" title="{{trans('lang.verify-and-copy-command')}}" onclick="verifyPHPExecutableAndCopyCommand()">
                        <i class="far fa-clipboard"></i></span>
                    <span style="font-size: 20px; display:none;" id="loader"><i class="fas fa-spinner fa-spin"></i></span>
                </div>
            </div>
        </div>
            @endif
            @empty 
            <tr><td>No Records</td></tr>
            @endforelse
            </tbody>
        </table> 

    </div>
</div>
@stop
@push('scripts')
<script type="text/javascript">
    
    function hideAlert() {
        $(".cron-danger").css('display', 'none');
    }
</script>
<script type="text/javascript">
    function checksome(showtext = true)
    {
        if (!showtext) {
            $("#phpExecutableList").css('display', "block");
            $("#phpExecutableList").val(0)
            $("#phpExecutableTextArea").css('display', "none");
        } else if($("#phpExecutableList").val() == 'Other') {
            $("#phpExecutableList").css('display', "none");
            $("#phpExecutableTextArea").css('display', "block");
        }
    }
    
    function verifyPHPExecutableAndCopyCommand()
    {
        copy = false;
        var path = ($("#phpExecutableList").val()=="Other")? $("#phpExecutableText").val(): $("#phpExecutableList").val();
        var text = "* * * * * "+path.trim()+" "+$(".copy-command2").text().trim();
        copyToClipboard(text);

        $.ajax({
            'method': 'POST',
            'url': "{{route('verify-cron')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "path": path
            },
            beforeSend: function() {
                $("#loader").css("display", "block");
                $(".alert-danger, .alert-success, #copyBtn").css('display', 'none');
            },
            success: function (result,status,xhr) {
                $(".alert-success-message").html("{{trans('lang.cron-command-copied')}} "+result.message);
                $(".cron-success, #copyBtn").css('display', 'block');
                $("#loader").css("display", "none");
                copy = true
            },
            error: function(xhr,status,error) {
                $('#clearClipBoard').click();
                $(".cron-danger, #copyBtn").css('display', 'block');
                $("#loader").css("display", "none");
                $(".alert-danger-message").html("{{trans('lang.cron-command-not-copied')}} "+xhr.responseJSON.message);
            },
        });
    }

    function copyToClipboard(text = " ")
    {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
        } catch (err) {
        }
        console.log(msg);
        document.body.removeChild(textArea);
    }
</script>
@endpush