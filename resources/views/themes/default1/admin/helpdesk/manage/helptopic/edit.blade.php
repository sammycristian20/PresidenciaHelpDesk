@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.help_topics_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.help_topics_edit-page-description') !!}">


@stop


@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('help')
class="active"
@stop

@section('HeadInclude')
 <link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>

@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.help_topics') !!}</h1>
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
<!-- open a form -->
{!! Form::model($topics,['url' => 'helptopic/'.$topics->id, 'method' => 'PATCH','id'=>'Form']) !!}
@if(Session::has('errors'))
        <?php //dd($errors); ?>
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>Alert!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
            @if($errors->first('status'))
            <li class="error-message-padding">{!! $errors->first('status', ':message') !!}</li>
            @endif
            @if($errors->first('type'))
            <li class="error-message-padding">{!! $errors->first('type', ':message') !!}</li>
            @endif
            @if($errors->first('topic'))
            <li class="error-message-padding">{!! $errors->first('topic', ':message') !!}</li>
            @endif
            @if($errors->first('parent_topic'))
            <li class="error-message-padding">{!! $errors->first('parent_topic', ':message') !!}</li>
            @endif
            @if($errors->first('custom_form'))
            <li class="error-message-padding">{!! $errors->first('custom_form', ':message') !!}</li>
            @endif
            @if($errors->first('department'))
            <li class="error-message-padding">{!! $errors->first('department', ':message') !!}</li>
            @endif
            @if($errors->first('priority'))
            <li class="error-message-padding">{!! $errors->first('priority', ':message') !!}</li>
            @endif
            @if($errors->first('sla_plan'))
            <li class="error-message-padding">{!! $errors->first('sla_plan', ':message') !!}</li>
            @endif
            @if($errors->first('auto_assign'))
            <li class="error-message-padding">{!! $errors->first('auto_assign', ':message') !!}</li>
            @endif
        </div>
        @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('lang.edit_help_topic')}}</h3>
    </div>
    <div class="card-body">
        
        <!-- status radio: required: Active|Dissable -->
        <div class="row">
            <div class="col-md-6">
            @if($sys_help_topic->help_topic == $topics->id) 
            <div class="form-group {{ $errors->has('ticket_status') ? 'has-error' : '' }}">
                    {!! Form::label('ticket_status',Lang::get('lang.status')) !!}&nbsp;&nbsp;
                    {!! Form::radio('status',1,true) !!} {{Lang::get('lang.active')}}&nbsp;&nbsp;&nbsp;
                    <!-- {!! Form::radio('status','0') !!} {{Lang::get('lang.inactive')}} -->
                </div>

            @else
            <div class="form-group {{ $errors->has('ticket_status') ? 'has-error' : '' }}">
                    {!! Form::label('ticket_status',Lang::get('lang.status')) !!}&nbsp;&nbsp;
                    {!! Form::radio('status',1,true) !!} {{Lang::get('lang.active')}}&nbsp;&nbsp;&nbsp;
                    {!! Form::radio('status',0) !!} {{Lang::get('lang.inactive')}}
                </div>
            @endif

                

            </div>
            <!-- Type : Radio : required : Public|private -->
            <div class="col-md-6">
             @if($sys_help_topic->help_topic == $topics->id) 
             <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                    {!! Form::label('type',Lang::get('lang.type')) !!}&nbsp;&nbsp;
                    {!! Form::radio('type',1,true) !!} {{Lang::get('lang.public')}}&nbsp;&nbsp;&nbsp;
                    
                </div>
            @else
            <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                    {!! Form::label('type',Lang::get('lang.type')) !!}&nbsp;&nbsp;
                    {!! Form::radio('type',1,true) !!} {{Lang::get('lang.public')}}&nbsp;&nbsp;&nbsp;
                    {!! Form::radio('type',0) !!} {{Lang::get('lang.private')}}
                </div>
           @endif
                
            </div>
            <!-- Topic text form Required -->
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('topic') ? 'has-error' : '' }}">
                    {!! Form::label('topic',Lang::get('lang.topic')) !!} <span class="text-red"> *</span>
                    {!! Form::text('topic',null,['class' => 'form-control']) !!}
                </div>
            </div>
            <!-- Parent Topic: Drop down: value from helptopic table -->
            <div class="col-md-6 hide">
                <div class="form-group {{ $errors->has('parent_topic') ? 'has-error' : '' }}">
                    {!! Form::label('parent_topic',Lang::get('lang.parent_topic')) !!}
                    {!!Form::select('parent_topic', [''=>Lang::get('lang.select_a_parent_topic'),Lang::get('lang.help_topic')=>$topics->pluck('topic','id')->toArray()],null,['class' => 'form-control']) !!}
                </div>
            </div>
            <!-- Custom Form: Drop down: value from form table -->
            <!-- Department:    Drop down: value Department form table -->
                    <div class="col-md-6 form-group {{ $errors->has('linked_department') ? 'has-error' : '' }}">
                        {!! Form::label('linked_departments',Lang::get('lang.linked_department')) !!} <span class="text-red"> *</span>&nbsp;&nbsp;<a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.linked-department-tooltip-message') !!}"><i class="fas fa-question-circle" style="padding: 0px;"></i></a>
                {!! Form::select('linked_departments[]', [Lang::get('lang.departments')=>$departments->pluck('name','id')->toArray()],$dept,['multiple'=>true,'class'=>"form-control select2" ,'id'=>"primary_department", 'required', 'style'=>"width:100%"]) !!}</div>
            <!-- Department:    Drop down: value Department form table -->
            {{--<div class="col-md-6">
                <div class="form-group {{ $errors->has('department') ? 'has-error' : '' }}">
                    {!! Form::label('department',Lang::get('lang.department')) !!}<span class="text-red"> *</span>
                    {!!Form::select('department', [''=>Lang::get('lang.select_a_department'),Lang::get('lang.departments')=>$departments->pluck('name','id')->toArray()],$dept,['class' => 'form-control']) !!}
                </div>
            </div>--}}
            <div class="col-md-12 form-group {{ $errors->has('department') ? 'has-error' : '' }}" id="default-department-block" style="display: block;">
                <div class="form-group {{ $errors->has('linked_department') ?  'has-error' : '' }}">
                    {!! Form::label('department',Lang::get('lang.default_department')) !!} <span class="text-red"> *</span>&nbsp;&nbsp;<a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.default-department-tooltip-message') !!}"><i class="fas fa-question-circle" style="padding: 0px;"></i></a>
                    <div class="row" id="default-department-options">
                        <div class="col-sm-4" id="d"><input type="checkbox" class="single-check" value="0" id="check0" name="department" onclick="checkSingle(this)">&nbsp;{!! Lang::get('lang.system_default') !!}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Priority:  Drop down: value from Priority  table -->
                            <?php
    $check_linking_status = \App\Model\helpdesk\Settings\CommonSettings::where('option_name', '=', 'helptopic_link_with_type')->select('status')->first();
    ?>
      @if ($check_linking_status->status != 0)
                                <div class="row">
                                    <div class="col-sm-8 form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                                        {!! Form::label('type',Lang::get('lang.type')) !!} 


                                        {!!Form::select('ticket_type[]', [''=>Lang::get('lang.labels'), Lang::get('lang.labels')=>$tk_type->pluck('name','id')->toArray()],$assign_type,['multiple'=>true,'class'=>"form-control select2" ,'id'=>"ticket_type",'style'=>"width:100%"]) !!}


                                    </div>
                                </div>
                                @endif
        <!-- Auto-response: checkbox : Disable new ticket auto-response  -->
        <div class="row">
            <div class="col-md-12">
                <!-- intrnal Notes : Textarea :  -->
                <div class="form-group">
                    {!! Form::label('internal_notes',Lang::get('lang.internal_notes')) !!}
                    {!! Form::textarea('internal_notes',null,['class' => 'form-control','size' => '10x5','id'=>'ckeditor']) !!}
                </div>
            </div>
            <!-- Submit button -->
        </div>

        <div class="form-group">
            <input type="checkbox" name="sys_help_tpoic" @if($sys_help_topic->help_topic == $topics->id) checked disabled @endif> {{ Lang::get('lang.make-default-helptopic')}}
        </div>
    </div>
    <div class="card-footer">
     <button type="submit" class="btn btn-primary" id="submit"><i class="fas fa-sync">&nbsp;</i>{!!Lang::get('lang.update')!!}</button>      
    
    </div>
</div>
<!-- close form -->
{!! Form::close() !!}
@stop
@push('scripts')
<script src="{{assetLink('js','select2')}}"></script>
<script type="text/javascript">
    function checkSingle(para)
    {
        $('.single-check').prop('checked', false);
        $(para).prop('checked', true)
    }
    $('#primary_department').select2({
        placeholder: "{{Lang::get('lang.Choose_departments...')}}",
        minimumInputLength: 2,
        ajax: {
            url: '{{url("api/dependency/departments")}}',
            dataType: 'json',
            beforeSend: function(){
                $('.loader').css('display', 'block');
            },
            complete: function() {
                $('.loader').css('display', 'none');
            },
            data: function (params) {
                return {
                    "search-query": $.trim(params.term)
                };
            },
            processResults: function (data) {
                let res  = [];
                data.data.departments.forEach(function(item) {
                    res.push({"id":item.id,"text":item.name});
                });
                return {
                    results: res
                };
            },
            cache: true
        }
    });
    $(function() {
        var data = $('#primary_department').select2('data');
        var dept = 0;
        @if(Input::old('department', false))
            dept = {!! Input::old('department', false) !!}
        @else
            dept = "{!! $topics->department !!}";
        @endif
        var set = false;
        for(var j = 0; j <data.length; j++) {
            var id = data[j].id;
            var text = data[j].text;
            var checked = '';
            if (dept == id) {
                set = true;
                checked = "checked";
            }
            $('#default-department-options').append('<div class="col-sm-4" id="div'+id+'"><input type="checkbox" class="single-check" '+checked+' value="'+id+'" name="department" id="check'+id+'" onclick="checkSingle(this)">&nbsp;'+text+'</div>');
        }
        if (!set) {
            $('#check0').prop('checked', true);
        }
    });
    $('#primary_department').on('select2:select', function (e) {
        var data = e.params.data;
        $('#default-department-options').append('<div class="col-sm-4" id="div'+data.id+'"><input type="checkbox" class="single-check" value="'+data.id+'" id="check'+data.id+'" name="department" onclick="checkSingle(this)">&nbsp;'+data.text+'</div>');
    });
    $('#primary_department').on("select2:unselect", function (e) { 
        var data = e.params.data;
        var divid = "#div"+data.id;
        var check = "#check"+data.id;
        console.log(check, $(this).select2('data'), $(this).select2('data').length);
        if ($(this).select2('data').length >=1) {
            if($(check).prop('checked')) {
                var rest_data = $(this).select2('data');
                var nextchecked = '#check'+rest_data[0].id;
                $(nextchecked).prop('checked', true);
            }
        } else {
            $('#check0').prop('checked', true);
        }
        $(divid).remove();
    });

     $('#ticket_type').select2({
        minimumInputLength: 1,
        ajax: {
            url: '{{url("api/dependency/types")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    "search-query": $.trim(params.term),
                    "paginate": true,
                    "page": params.page || 1
                };
            },
            processResults: function (data) {
                let res  = [];
                data.data.data.forEach((item) => { res.push({"id":item.id,"text":item.name})});
                return {
                    results: res,
                    pagination: {
                        more : !(data.data.next_page_url===null)
                    }
                };
            },
            cache: true
        }
    });
</script>
@endpush