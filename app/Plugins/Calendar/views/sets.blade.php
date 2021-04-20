@extends('themes.default1.admin.layout.admin')

@section('PageHeader')
<h1>{!! Lang::get('Calendar::lang.template_set') !!}</h1>
@stop

@section('content')

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif

        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <b>{!! Lang::get('Calendar::lang.alert') !!} !</b> <br>
            <li>{{Session::get('fails')}}</li>
        </div>
        @endif

        @if(Session::has('failed'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <b>{!! Lang::get('Calendar::lang.alert') !!} !</b> <br>
            <li>{{Session::get('failed')}}</li>
        </div>
        @endif
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('Calendar::lang.list_of_templates_sets') !!}</h3>
        <div class="pull-right">
<!--            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create" title="Create" id="2create"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;{{Lang::get('Calendar::lang.create_template')}}</button>-->
            <a href="#" data-toggle="tooltip" data-placement="left" title="{!! Lang::get('Calendar::lang.path_templateset') !!}"><i class="fa fa-question-circle" style="padding: 0px"></i></a>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">

        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{!! Lang::get('Calendar::lang.name') !!}</th>
                    <th>{!! Lang::get('Calendar::lang.status') !!}</th>
                    <th>{!! Lang::get('Calendar::lang.template_language') !!}</th>
                    <th>{!! Lang::get('Calendar::lang.action') !!}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sets as $set)
                <tr>
                    <td>{!! $set->name !!}</td>
                    <td>
                        @if($set->active == 1)
                        <a style='color:green'>{!! Lang::get('Calendar::lang.active') !!}</a>
                         @else()
                          <a style='color:red'>{!! Lang::get('Calendar::lang.inactive') !!}</a>
                          @endif
                    </td>
                    <td>
                        <?php $lang = 'languages.'.$set->template_language; ?>
                        {!! Config::get($lang)[0] !!}&nbsp; 
                        @if(Lang::getLocale() == "ar") 
                        &rlm;
                        @endif 
                        ({!! Config::get($lang)[1] !!})
                    </td>
                    <td>

                        @if($set->active == 1 && $set->id == 1)
                           <button class="btn btn-primary btn-xs disabled" data-toggle="modal" data-target="">{!! Lang::get('Calendar::lang.activate_this_set') !!}</button>&nbsp;&nbsp;
                        @elseif ($set->active == 1 && $set->id != 1)
                            <div class="btn-group">
                                <a href="{{url('activate-templateset/'.$set->name)}}" class="btn btn-primary btn-xs">{{Lang::get('Calendar::lang.deactivate_this_set')}}</a>&nbsp;
                            </div>
                        @else
                            <div class="btn-group">
                                <a href="{{url('activate-templateset/'.$set->name)}}" class="btn btn-primary btn-xs">{{Lang::get('Calendar::lang.activate_this_set')}}</a>&nbsp;
                            </div>                   

                        @endif
                        <!--{!! link_to_route('show.templates',Lang::get('lang.show'),[$set->id],['class'=>'btn btn-primary btn-xs']) !!}&nbsp;-->
                   
                        <a href="{{url('/calendar/settings/templates')}}" class="btn btn-primary btn-xs"><i class="fa fa-eye">&nbsp;&nbsp;</i>{{Lang::get('Calendar::lang.view')}}</a>&nbsp;&nbsp;
                        <?php
                        if ($set->active) {
                            $dis = "disabled";
                        } else {
                            $dis = "";
                        }
                        ?>
                        <button class="btn btn-primary btn-xs {!! $dis !!}" data-toggle="modal" data-target="#{{$set->id}}delete"><i class="fa fa-trash" style="color:white;">&nbsp; </i> {!! Lang::get('Calendar::lang.delete') !!}</button>
                        <div class="modal fade" id="{{$set->id}}delete">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{!! Lang::get('Calendar::lang.delete') !!}</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>{!!Lang::get('Calendar::lang.confirm-to-proceed')!!}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <?php
                                        $url=url('delete-sets/'.$set->id);
                                        ?>
                                        
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('Calendar::lang.close') !!}</button>
<!--                                        {!! link_to_route('sets.delete',Lang::get('lang.delete'),[$set->id],['id'=>'delete','class'=>'btn btn-danger btn-sm']) !!}-->
                                            <a href="{!! $url !!}" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('Calendar::lang.delete') !!}</a>
                                    </div>
                                </div> 
                            </div>
                        </div> 
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div>

<div class="modal fade" id="create" class="modal fade in {{ $errors->has('name') ? 'has-error' : '' }}">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route'=>'template-sets.store']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{!! Lang::get('Calendar::lang.create') !!}</h4>
            </div>
            <div class="modal-body">
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissable">
                    <i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <b>{!! Lang::get('Calendar::lang.alert') !!} !</b><br>
                    <li style="list-style: none">{{ $error }}</li>
                </div>
                @if($error == "The name field is required.")
                <script type="text/javascript">
                    $(document).ready(function() {
                        $("#2create").click();
                    });
                </script>
                @endif
                @endforeach 
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="title">{!! Lang::get('Calendar::lang.name') !!}:<span style="color:red;">*</span></label><br>
                    {!! Form::text('name',null,['class'=>'form-control'])!!}
                </div>

            </div>
            <div class="modal-footer">
                <div class="form-group">
<!--                    {!! Form::submit(Lang::get('lang.create_se'),['class'=>'btn btn-primary'])!!}-->
                   {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('Calendar::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{!! Lang::get('Calendar::lang.close') !!}</button>
                </div></div>
            {!! Form::close() !!}
        </div> 
    </div>
</div>  
<!-- set script -->
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $('#example2').dataTable({
            "bPaginate": true,
            "bLengthChange": false,
            "bFilter": false,
            "bSort": true,
            "bInfo": true,
            "bAutoWidth": false
        });
    });
</script>
@stop