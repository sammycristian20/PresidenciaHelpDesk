@extends('themes.default1.agent.layout.agent')
@section('breadcrumbs')

@stop
<style>
  .fc-day-grid-event>.fc-content{
  	padding: 3px;
        
  }
  body .ui-tooltip {
      padding:2px;
  }
  button{
      outline: none !important;
  }
</style>
@section('HeadInclude')
<!-- fullCalendar 2.2.5-->

  <link href="{{assetLink('css','fullcalendar')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>

 <link rel="stylesheet" href="{{assetLink('css','daterangepicker')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>

  <style>
      .buttons-div {
          float: right;
          margin: 0 10px 10px 10px;
      }
      div.box-header {
          padding: 0 !important;
      }
  </style>

@stop
@section('PageHeader')
<h1>{{Lang::get('Calendar::lang.tasks_list')}}</h1>
@stop
@section('content')
  {{-- Success message --}}
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fas fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        {{-- failure message --}}
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<div class="row">
            <div id="calo" class="col-md-12" style="width:100%">
              <div class="card card-light">
                  <div class="card-header">
                      <div class="card-tools">
                          <button class="btn btn-tool" onclick="taskCreatePage()" title="{!! Lang::get('Calendar::lang.create_task') !!}"
                            data-toggle="tooltip">
                              <span class="glyphicon glyphicon-plus"></span>
                          </button>
                          <button class="btn btn-tool" onclick="taskListPage()" title="{!! Lang::get('Calendar::lang.list_view') !!}"
                            data-toggle="tooltip">
                              <span class="glyphicon glyphicon-th-list"></span>
                          </button>
                      </div>
                  </div>
                <div class="card-body">
                  <!-- THE CALENDAR -->
                  <div id="calendar"></div>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
            </div>
 </div> 
<script>
  function taskCreatePage(){
     window.location.href='{{url("/tasks/task/create")}}';
  }
  function taskListPage(){
     window.location.href='{{url("/tasks/task?category=$category")}}';
  }
</script>

<script type="text/javascript">
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
</script>
@stop 
 @section('FooterInclude')
  <script src="{{assetLink('js','fullcalendar')}}" type="text/javascript" ></script>
 <script>
  $(function(){

    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },

      buttonText: {
        today: 'today',
        month: 'month'
      },
      editable: false,
      eventLimit: true,
      navLinks: true,
        selectable: true,
        selectHelper: true,
        nextDayThreshold: '00:00:00',

     events: function (start,end,timeZone, callback) {
        $.ajax({
            url: '{!! url("tasks/api/get-all-tasks-for-calendar?category=$category") !!}',
            type: 'GET',
            data: {},
            success: function (response) {
               var events = [];
               $.each(response, function (key,value) {
                   switch(value.status) {
                       case 'Open':
                           background = "#e24934"
                           break;
                       case 'In-progress':
                           background = "#f1ae06"
                           break;
                       default:
                           background = "#488833"
                   }
                    events.push({
                        title: value.title,
                        start: value.task_start_date,
                        end: value.task_end_date,
                        url: value.url,
                        backgroundColor: background,
                        allDay: false,
                        displayEventEnd: true
                    });
               });
                callback(events)
            }
        })
     },
        timezone: "local",
    });

    });
 </script>     
@stop