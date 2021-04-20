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
  $dateTimeFormat = App\Model\helpdesk\Settings\System::first()->date_time_format;
?>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@elseif(Session::has('success'))
  <div class="alert alert-success alert-dismissable">
      <i class="fa  fa-check-circle"></i>
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      {{Session::get('success')}}
  </div>   
@endif
<div class="box box-primary">
  <div class="box-header with-border">
     <h3 class="box-title"  >{!! Lang::get('Calendar::lang.edit_task') !!}</h3>
      @if($task->status == "active")
        <button class="btn btn-default pull-right" onclick="window.location.href='{{url('change-task').'/'.$task->id.'/'.'close'}}'"><i class="fa fa-check-circle-o">&nbsp;</i> Close Task </button>
      @elseif($task->status == "deleted")
        <button class="btn btn-danger pull-right" disabled> Task deleted </button>
      @else
        <button class="btn btn-default pull-right" onclick="window.location.href='{{url('change-task').'/'.$task->id.'/'.'open'}}'"><i class="fa fa-recycle" aria-hidden="true">&nbsp;&nbsp;</i>Re-open Task </button>
      @endif
  </div>
  <div class="box-body row">
    {!! Form::open(['url' => "task/".$task->id, "id" => "task-form"]) !!}
    <input type="hidden" name="_method" value="PATCH" id="form-type">
  	  <div class="col-sm-6">
  	  	<label>{!! Lang::get('Calendar::lang.task_name') !!}</label><span class="text-red"> *</span></label><br>
  	  	  <input type="text" name="task_name" value="{{$task->task_name}}" class="form-control">
  	  </div>
  	  <div class="col-sm-6">
  	  	<label>{!! Lang::get('Calendar::lang.task_description') !!}</label><span class="text-red"> *</span></label><br>
  	  	   <textarea name="task_description" class="form-control" style="resize: vertical;">{{$task->task_description}}</textarea>
  	  </div>
  	  <div class="col-sm-3">
  	  	<label>{!! Lang::get('Calendar::lang.start_at') !!}</label><span class="text-red"> *</span></label><br>
          @if($dateTimeFormat == 'human-read')
            <input type="text" name="task_start_date" value="{{$task->task_start_date->format('d-mY h:i a')}}" class="form-control" id="start">
          @else
  	  	    <input type="text" name="task_start_date" value="{{$task->task_start_date->format($dateTimeFormat)}}" class="form-control" id="start">
          @endif

  	  </div>
  	  <div class="col-sm-3">
  	  	<label>{!! Lang::get('Calendar::lang.ends_at') !!}</label><span class="text-red"> *</span></label><br>
        @if($dateTimeFormat == 'human-read')
  	  	   <input type="text" name="task_end_date" value="{{$task->task_end_date->format('d-m-Y h:i a')}}" class="form-control" id="end">
        @else
          <input type="text" name="task_end_date" value="{{$task->task_end_date->format($dateTimeFormat)}}" class="form-control" id="end">
        @endif
  	  </div>
      <div class="col-sm-6">
        <label>{!! Lang::get('Calendar::lang.task_alert') !!}</label><span class="text-red"> *</span></label><br>
           <select class="form-control" name="due_alert" id="task-due-alert">
             <!-- <option value="" selected="selected">select</option> -->
             <option value="event_day">On day of event</option>
             <option value="5_minutes_before">5 minutes before</option>
             <option value="15_minutes_before">15 minutes before</option>
             <option value="30_minutes_before">30 minutes before</option>
             <option value="1_hour_before">1 hour before</option>
             <option value="2_hours_before">2 hours before</option>
             <option value="1_day_before">1 day before</option>
             <option value="no_reminder">No Reminder</option>
           </select><br>
           <script type="text/javascript">
             $(document).ready(function(){
              // alert("dsd");
                $('#task-due-alert option[value="{{$due_alert}}"]').attr("selected",true)
             })
           </script>
      </div>
  	  <div class="col-sm-6">
  	  	<label>{!! Lang::get('Calendar::lang.alert_repeat') !!}</label>
          @if($task->alerts != null)
            {{ Form::select('alert_repeat', array('never' => 'Never', 'daily' => 'Daily','weekly' => 'Weekly', 'monthly' => 'Monthly'), $task->alerts->repeat_alerts, [ "class" => "form-control" ] )}}
          @else
  	  	   <select class="form-control" name="alert_repeat">
    	  	   	 <option value="" selected="selected">Select</option>
    	  	   	 <option value="never">Never</option>
    	  	   	 <option value="daily">Daily</option>
    	  	   	 <option value="weekly">Weekly</option>
    	  	   	 <option value="monthly">Monthly</option>
  	  	   </select>
           @endif
  	  </div>
  	  <div class="col-sm-6">
  	  	<label>{!! Lang::get('Calendar::lang.assignee') !!}</label>
  	       <!-- select class="form-control assign" multiple name="assignee[]">
                  <optgroup label="Assign task to"> </optgroup>
           </select><br>  -->
        @if(Auth::user()->role =="admin")

            {!!Form::select('assignee[]',[Lang::get('lang.assignee')=>''],null,['class' => 'form-control assign','id'=>'assignee-admin','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}

         @elseif(Auth::user()->role =="agent")
  	 
           {!!Form::select('assignee[]',[Lang::get('lang.assignee')=>''],null,['class' => 'form-control assign','id'=>'assignee-agent','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}

         @endif
    </div>
      <div class="col-sm-7">
        <label>{!! Lang::get('Calendar::lang.link-ticket') !!}</label>
           <select class="form-control link-ticket" name="associated_ticked">
            @if(isset($associated_ticket))
              <option value="{{$associated_ticket['id']}}">{{$associated_ticket['title']}}</option>
            @endif
           </select>
      </div>
      <div class="col-sm-2"><br>
        @if($task->is_private)
          <input type="checkbox" checked="checked" name="is_private" style="margin-top: 20px;">
        @else
          <input type="checkbox" name="is_private" style="margin-top: 20px;">
        @endif
        <label>{!! Lang::get('Calendar::lang.is_private') !!}</label>
      </div>
      <div class="col-sm-3" style="margin-top: 6px;"><br>
          <button type="submit"  class="btn btn-primary"><i class="fa fa-refresh">&nbsp;&nbsp;</i>{!! Lang::get('lang.update') !!}</button>&nbsp;
          @if($task->status == "deleted")
            <button type="button"  class="btn btn-primary delete-task" disabled><i class="fa fa-trash">&nbsp;&nbsp;</i>{!! Lang::get('lang.deleted') !!}</button>
          @else
            <button type="button"  class="btn btn-primary delete-task"><i class="fa fa-trash">&nbsp;&nbsp;</i>{!! Lang::get('lang.delete') !!}</button>
          @endif
      </div>
     </div>
    {!! Form::close() !!}
</div>
<div id="delete-task-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete </h4>
      </div>
      <div class="modal-body">
        <p>Are you sure  ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>Close</button>
        <button type="button" id="confirm-delete" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>Delete</button>
      </div>
    </div>

  </div>
</div>
<?php
    $dateTimeFormat = dateTimeFormat(true); 
?>
<script src="{{assetLink('js','moment')}}" type="text/javascript"></script>
<script src="{{assetLink('js','select2-full-min')}}" type="text/javascript"></script>
<script src="{{assetLink('js','bootstrap-datetimepicker4')}}" type="text/javascript"></script>

<script type="text/javascript">
    function resetTicket(){
       $(".link-ticket option").remove();
       $('.link-ticket').select2('val','');
    };
    $(function () {
        var assignees=<?php echo($users);?>;
        var assigned=<?php echo($assigned_users);?>;
        console.log(assigned)
         for(var j in assigned){
            $('.assign').append('<option value="'+j+'" selected="selected">'+assigned[j]+'</option>')
         }
        var arr = [];
        var arr1 = [];

        $('#start').datetimepicker({
          format: '{{$dateTimeFormat}}',
        });
        $('#end').datetimepicker({
            useCurrent: false, //Important! See issue #1075
            format: '{{$dateTimeFormat}}',
        });
        $("#start").on("dp.change", function (e) {
            $('#end').data("DateTimePicker").minDate(e.date);
        });
        $("#end").on("dp.change", function (e) {
            $('#start').data("DateTimePicker").maxDate(e.date);
        });

        for(var i in assignees){
            var obj = {};
            obj['id']=i;
            obj['text']=assignees[i];
            arr.push(obj);
         }
         // console.log(arr);
        $('.assign').select2({
            data:arr
        });
        $('.link-ticket').select2({
           placeholder: "{{Lang::get('Calendar::lang.search-ticket')}}",
           minimumInputLength: 1,
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


    $('body').on('click', '.delete-task', function(){
      $("#delete-task-modal").modal('show')
    })

    $('body').on('click', '#confirm-delete', function(){
      $("#form-type").val('DELETE')
      $("#task-form").submit()
    })

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