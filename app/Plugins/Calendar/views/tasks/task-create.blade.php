@extends('themes.default1.agent.layout.agent')
@section('PageHeader')
<h1>{!!Lang::get('lang.task')  !!}</h1>
@stop
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<style type="text/css" media="screen">
   .select2-container .select2-selection--multiple,.select2-container--default .select2-selection--single{
        height: 34px;
        border-radius: 0px !important;
        border: 1px solid #d2d6de !important;
    } 
    .select2-container{
       width: 100% !important;
    }
</style>
@section('HeadInclude')


  <link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>
  <link href="{{assetLink('css','bootstrap-datetimepicker4')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>
  <link rel="stylesheet" href="{{assetLink('css','daterangepicker')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>

@stop
@section('content')

<?php
    $dateTimeFormat = dateTimeFormat(true);
?>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="box box-primary">
  <div class="box-header with-border">
  	 <h3 class="box-title">{{ Lang::get('Calendar::lang.create_task') }}</h3>
  </div>
  <div class="box-body">
  	{!! Form::open(['url' => 'task/', 'method' => 'post','class' => 'row']) !!}
      @if(isset($_GET['ticket_id']))
        <input type="hidden" name="request_from" value="{{$_GET['ticket_id']}}">
      @endif
        <div class="col-md-6">
          <label>{!! Lang::get('Calendar::lang.task_name') !!}</label><span class="text-red"> *</span></label><br>
          <input type="text" name="task_name" value="{{old('task_name')}}" class="form-control">
        </div>
        <div class="col-sm-6">
          <label>{!! Lang::get('Calendar::lang.task_description') !!}</label><span class="text-red"> *</span></label><br>
             <textarea name="task_description" class="form-control" style="resize: vertical;">{{ Input::old('task_description') }}</textarea>
        </div>
        <div class="col-sm-3">
          <label>{!! Lang::get('Calendar::lang.start_at') !!}</label><span class="text-red"> *</span></label><br>
             <input type="text" name="task_start_date" value="{{old('task_start_date')}}" class="form-control" id="start">
        </div>
        <div class="col-sm-3">
          <label>{!! Lang::get('Calendar::lang.ends_at') !!}</label><span class="text-red"> *</span></label><br>
             <input type="text" name="task_end_date" value="{{old('task_end_date')}}" class="form-control" id="end">
        </div>

        <div class="col-sm-6">
          <label>{!! Lang::get('Calendar::lang.task_alert') !!}</label><span class="text-red"> *</span></label>
             <select class="form-control" name="due_alert" value="{{old('due_alert')}}" >
               <!-- <option value="" selected="selected">select</option> -->
                 <option value="event_day">On day of event</option>
                 <option value="5_minutes_before" @if (old('due_alert') == '5_minutes_before') selected="selected" @endif>5 minutes before</option>
                 <option value="15_minutes_before" @if (old('due_alert') == '15_minutes_before') selected="selected" @endif>15 minutes before</option>
                 <option value="30_minutes_before" @if (old('due_alert') == '30_minutes_before') selected="selected" @endif>30 minutes before</option>
                 <option value="1_hour_before" @if (old('due_alert') == '1_hour_before') selected="selected" @endif>1 hour before</option>
                 <option value="2_hours_before" @if (old('due_alert') == '2_hours_before') selected="selected" @endif>2 hours before</option>
                 <option value="1_day_before" @if (old('due_alert') == '1_day_before') selected="selected" @endif>1 day before</option>
                 <option value="no_reminder" @if (old('due_alert') == 'no_reminder') selected="selected" @endif>No Reminder</option>
             </select><br>
        </div>
        <div class="col-sm-6">
          <label>{!! Lang::get('Calendar::lang.alert_repeat') !!}</label>
             <select class="form-control" name="alert_repeat">
                <optgroup label="Select"></optgroup>
                <option value="never" @if (old('alert_repeat') == 'never') selected="selected" @endif>Never</option>
                <option value="daily" @if (old('alert_repeat') == 'daily') selected="selected" @endif>Daily</option>
                <option value="weekly" @if (old('alert_repeat') == 'weekly') selected="selected" @endif>Weekly</option>
                <option value="monthly" @if (old('alert_repeat') == 'monthly') selected="selected" @endif>Monthly</option>
             </select>
        </div>

        <div class="col-sm-6">
          <label>{!! Lang::get('Calendar::lang.assignee') !!}</label>
             <!-- <select class="form-control assign" multiple name="assignee[]">
                 <optgroup label="Assign task to"></optgroup>
             </select><br> -->
            
           @if(Auth::user()->role =="admin")
             
             {!!Form::select('assignee[]',[Lang::get('lang.assignee')=>''],null,['class' => 'form-control select2','id'=>'assignee-admin','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}

          @elseif(Auth::user()->role =="agent")
               
            {!!Form::select('assignee[]',[Lang::get('lang.assignee')=>''],null,['class' => 'form-control select2','id'=>'assignee-agent','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}

          @endif
      </div>

        <div class="col-sm-7">
          <label>{!! Lang::get('Calendar::lang.link-ticket') !!} </label>
          <select class="form-control link-ticket" name="associated_ticked" multiple>
              @if(isset($_GET['ticket_id']))
                <option value="{{$_GET['ticket_id']}}" selected="selected">{{$_GET['subject']}}</option>
              @endif
             </select>
        </div>
        <div class="col-sm-2"><br>
          <input type="checkbox" name="is_private" style="margin-top: 20px;">
          <label>{!! Lang::get('Calendar::lang.is_private') !!}</label>
        </div>

        <div class="col-sm-3" style="margin-top: 6px;"><br>
              {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('Calendar::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
        </div>

    {!! Form::close() !!}
  </div>
</div>
  <script src="{{assetLink('js','moment')}}" type="text/javascript"></script>
  <script src="{{assetLink('js','select2-full-min')}}" type="text/javascript"></script>
  <script src="{{assetLink('js','bootstrap-datetimepicker4')}}" type="text/javascript"></script>

<script type="text/javascript">
    function resetTicket(){
       $(".link-ticket option").remove();
       $('.link-ticket').select2('val','');
    };
    $(function () {
        console.log("{{$dateTimeFormat}}")
        var assignees=<?php echo($users);?>;
        var arr = [];
        var arr1 = [];
        $('#start').datetimepicker({
          format: '{{$dateTimeFormat}}',
          minDate:new Date(),
        });
        $('#end').datetimepicker({
            //useCurrent: false, //Important! See issue #1075
            format: '{{$dateTimeFormat}}',
            minDate:new Date(),
        });
        $("#start").on("dp.change", function (e) {
            $('#end').data("DateTimePicker").minDate(e.date);
        });
        $("#end").on("dp.change", function (e) {
            $('#start').data("DateTimePicker").maxDate(e.date);
        });
         // 
         for(var i in assignees){
            var obj = {};
            obj['id']=i;
            obj['text']=assignees[i];
            arr.push(obj);
         }
         //console.log(arr);
        $('.assign').select2({
            data:arr
        });
        $('.link-ticket').select2({
           placeholder: "{{Lang::get('Calendar::lang.search-ticket')}}",
           minimumInputLength: 1,
           maximumSelectionLength:1,
           ajax: {

        url: '{{url("api-get-ticket-number")}}',
             
             dataType: 'json',
             data: function (params) {
                arr1 = [];
                return {
                    name: $.trim(params.term),
                    showing:"inbox"
                };
            },
            processResults: function (data) {
             console.log(data);
                return {
                results: $.map(data, function (value) {
                    return {
                        details:value.details,
                        text:value.text,
                        id: value.ticket_id
                    }
                })
              }; 
            },
            cache: true
          },
         templateResult: formatState
      });
      function formatState (state) { 
         var $state = $( '<div style="width: 90%;display: inline-block;">'+state.details+'</div>');
        return $state;  
    }
    });
</script>



 <!-- For auto search assigneee  -->
<script>
  $(function(){
    $('#assignee-admin').select2({
       
        maximumSelectionLength: 1,
        minimumInputLength: 1,
        ajax: {
            url: '{{url("ticket/form/requester?type=agent")}}',
            dataType: 'json',
            data: function(params) {
                // alert(params);
                return {
                    term: $.trim(params.term)
                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        image:value.profile_pic,
                        text:value.first_name+" "+value.last_name,
                        id:value.id,
                        email:value.email,
                    }
                })
               }
            },
            cache: true
        },
         templateResult: formatState,
    });
  });
   function formatState (state) { 
       
       var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
  }
</script>


<script>
  $(function(){
    $('#assignee-agent').select2({
       
        maximumSelectionLength: 1,
        minimumInputLength: 1,
        ajax: {
            url: '{{url("ticket/form/requester?type=agent-only")}}',
            dataType: 'json',
            data: function(params) {
                // alert(params);
                return {
                    term: $.trim(params.term)
                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        image:value.profile_pic,
                        text:value.first_name+" "+value.last_name,
                        id:value.id,
                        email:value.email,
                    }
                })
               }
            },
            cache: true
        },
         templateResult: formatState,
    });
  });
   function formatState (state) { 
       
       var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
  }
</script>


@stop