@extends('themes.default1.admin.layout.admin')
@section('PageHeader')
<h1>{!! Lang::get('lang.templates') !!}</h1>
@stop

@section('content')

{!! Form::model($template,['url'=>'templates/'.$template->id,'method'=>'patch']) !!}

 @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-ban"></i>  
                    <strong>{!! Lang::get('lang.alert') !!} !</strong>
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
                <!-- fail lang -->
                @if(Session::has('fails'))
                <div class="alert alert-danger alert-dismissable">
                    <i class="fas fa-ban"></i>
                    <b>{{Lang::get('lang.alert')}}!</b>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('fails')}}
                </div>
                @endif
                
<div class="card card-light">
    <div class="card-header">

        <h3 class="card-title">{!! Lang::get('lang.edit_template',['template_name' => $type->name]) !!}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
               
                <div class="row">
                    <div class="col-md-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <!-- first name -->
                        <p class="lead">{!! $type->plugin_name ? Lang::get($type->plugin_name.'::lang.'.$template->name): Lang::get('lang.'.$template->name) !!}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-light">
                            <div class="card-header">
                                <h3 class="card-title">{{Lang::get('lang.list-of-available-shot-codes')}}</h3>
                                <div class="card-tools">
                                    <button class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="{{Lang::get('lang.collapse-list')}}"><i class="fas fa-minus"></i></button>
                                </div><!-- /.box-tools -->
                            </div><!-- /.box-header -->
                            <div class="card-body col-md-12">
                                <div class="alert alert-info alert-dismissable">
                                    <i class="fas  fa-info-circle"></i>
                                    <span>{{Lang::get('lang.template-short-code-tips')}}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                </div>
                                <div>
                                    <ul class="list-inline ">
                                        @foreach ($var as $key => $value)
                                        <li  class="list-inline-item" style="width: 23%"><span data-toggle="tooltip" title="{{trans($value)}}" data-placement="right">{{$key}}</span></li>
                                        @endforeach   
                                    </ul>
                                </div>
                                <div class="col-md-12">
                                {!! Lang::get('lang.template_shortcode_note_message') !!}
                                </div>
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group" id = "use-subject">
                        <br/>
                        {!! Form::checkbox('variable', $template->variable, null, ['id' => 'use-sub-check']) !!}
                        {!! Form::label('subject',Lang::get('lang.use_subject')) !!} <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.template-subject-usage-info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group subject {{ $errors->has('subject') ? 'has-error' : '' }}">
                        {!! Form::hidden('type', $template->type) !!} 
                        {!! Form::label('subject',Lang::get('lang.subject')) !!}
                        {!! Form::text('subject',$subject,['class' => 'form-control', 'id' =>'subject']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group {{ $errors->has('message') ? 'has-error' : '' }}">
                        {!! Form::label('message',Lang::get('lang.content'),['class'=>'required']) !!}<span style="color:red;">*</span>
                        {!! Form::textarea('message',$body,['class'=>'form-control','id'=>'ckeditor']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
<!--        {!! Form::submit(Lang::get('lang.update'),['class'=>'form-group btn btn-primary'])!!}-->
<!--        {!!Form::button('<i class="fa fa-refresh" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.update'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}-->
            <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-sync fa-spin fa-1x fa-fw'>&nbsp;</i> updating..."><i class="fas fa-sync">&nbsp;&nbsp;</i>{!!Lang::get('lang.update')!!}</button>    
    </div>
</div>
{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $("#subject").keyup(function() {
            var subject = document.getElementById('subject').value;
            if (subject) {
                $("#use-subject").show();
            } else {
                $("#use-subject").hide();
            }
        });
        $('.btn-tool').on('click', function(){
            if ($(this).children().attr('class') == 'fas fa-minus') {
                $(this).tooltip('hide').attr('data-original-title', "{{Lang::get('lang.expand-list')}}").tooltip('fixTitle');
            } else {
                $(this).tooltip('hide').attr('data-original-title', "{{Lang::get('lang.collapse-list')}}").tooltip('fixTitle');
            }
        });
    });
</script>
<script type="text/javascript">
        if ($('#use-sub-check').is(':checked')) {
            showSubjectField(true);
        } else {
            showSubjectField(false);
        }

        $('#use-sub-check').on('click', function(){
            var show = false;
            if ($(this).is(':checked')) {
                show = true;
            } else {
                show = false;
            }
            showSubjectField(show);
        });

        function showSubjectField(show) {
            var val = 'none';
            if (show) {
                val = 'block';
            }
            $('.subject').css('display', val);
        }
</script>
@stop