@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.queue_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.queue_edit-page-description') !!}">


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
<h1>{{$queue->name}}</h1>
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
        {!! Form::open(['url'=>'queue/'.$queue->id,'method'=>'post','id'=>'form']) !!}
        <div id="response" class="mb-2">

        </div>
        {!! Form::submit('Save',['class'=>'btn btn-primary', 'id'=>'submitButton', 'disabled'=>true]) !!}
        {!! Form::close() !!}
    </div>

</div>
<script>

    $(document).ready(function () {
        var queueid = '{{$queue->id}}';
        $.ajax({
            url: "{{url('form/queue')}}",
            dataType: "html",
            data: {'queueid': queueid},
            beforeSend: function() {
                $('.loader').css('display','block');
            },
            success: function (response) {
                $('.loader').css('display','none');
                $("#response").html(response);
                $("#submitButton").attr('disabled', false);

            },
            error: function ($xhr) {
                $data = JSON.parse($xhr.responseText);
                $("#response").html($data.message);
            }
        });
    });
</script>
@stop