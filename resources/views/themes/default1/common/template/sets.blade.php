@extends('themes.default1.admin.layout.admin')

@section('Emails')
active
@stop

@section('emails-bar')
active
@stop

@section('template')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.template_set') !!}</h1>
@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

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
            <i class="fas fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <b>{!! Lang::get('lang.alert') !!} !</b> <br>
            <li>{{Session::get('fails')}}</li>
        </div>
        @endif

        @if(Session::has('failed'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <b>{!! Lang::get('lang.alert') !!} !</b> <br>
            <li>{{Session::get('failed')}}</li>
        </div>
        @endif


        
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{!! Lang::get('lang.list_of_templates_sets') !!}</h3>
        <div class="card-tools">
            <button class="btn btn-tool" data-toggle="modal" data-target="#create" title="Create" id="2create"
                title="{{Lang::get('lang.create_template')}}" data-toggle="tooltip">
                <span class="glyphicon glyphicon-plus"></span>
            </button>
        </div>
    </div><!-- /.box-header -->
    <div class="card-body">

        <table id="example2" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{!! Lang::get('lang.name') !!}</th>
                    <th>{!! Lang::get('lang.status') !!}</th>
                    <th>{!! Lang::get('lang.template_language') !!}</th>
                    <th>{!! Lang::get('lang.department') !!}</th>
                    <th>{!! Lang::get('lang.action') !!}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sets as $set)
                <tr>
                    <td>{!! $set->name !!}</td>
                    <td>
                        @if($set->active == 1)
                        <a style='color:green;pointer-events:none;' class="btn btn-xs btn-default">{!! Lang::get('lang.active') !!}</a>
                         @else()
                          <a style='color:red;pointer-events:none;' class="btn btn-xs btn-default">{!! Lang::get('lang.inactive') !!}</a>
                          @endif
                    </td>
                    <td style="width: 180px;">
                        <?php $lang = 'languages.'.$set->template_language; ?>
                        {!! Config::get($lang)[0] !!}&nbsp; 
                        @if(Lang::getLocale() == "ar") 
                        &rlm;
                        @endif 
                        ({!! Config::get($lang)[1] !!})
                    </td>
                    @if($set->department()->first())
                        <td>{!! $set->department()->first()->name !!}</td>
                    @else
                        <td>{!! $set->department()->first() !!}</td>
                    @endif
                    <td style="width: 235px">

                        @if($set->active == 1 && $set->id == 1)
                           <button class="btn btn-primary btn-xs disabled" data-toggle="modal" data-target="">
                            <i class="far fa-check-circle"> </i> {!! Lang::get('lang.activate') !!}</button>&nbsp;
                        @elseif ($set->active == 1 && $set->id != 1)
                            <div class="btn-group">
                                <a href="{{url('activate-templateset/'.$set->name)}}" class="btn btn-primary btn-xs">
                                    <i class="far fa-times-circle"> </i> {{Lang::get('lang.deactivate')}}</a>&nbsp;
                            </div>
                        @else
                            <div class="btn-group">
                                <a href="{{url('activate-templateset/'.$set->name)}}" class="btn btn-primary btn-xs">
                                    <i class="far fa-check-circle"> </i> {{Lang::get('lang.activate')}}</a>&nbsp;
                            </div>                   

                        @endif
                        <!--{!! link_to_route('show.templates',Lang::get('lang.show'),[$set->id],['class'=>'btn btn-primary btn-xs']) !!}&nbsp;-->
                   
                        <a href="{{url('show-template/'.$set->id)}}" class="btn btn-primary btn-xs"><i class="fas fa-eye">&nbsp;</i>{{Lang::get('lang.view')}}</a>&nbsp;
                        <?php
                        if ($set->active) {
                            $dis = "disabled";
                        } else {
                            $dis = "";
                        }
                        ?>
                        <button class="btn btn-primary btn-xs {!! $dis !!}" data-toggle="modal" data-target="#deleteset-{{$set->id}}{!! $dis !!}">
                            <i class="fas fa-trash" style="color:white;"> </i> {!! Lang::get('lang.delete') !!}
                        </button>
                        <div class="modal fade" id="deleteset-{{$set->id}}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{!! Lang::get('lang.delete').' '.$set->name !!}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{!!Lang::get('lang.confirm-to-proceed')!!}</p>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <?php
                                        $url=url('delete-sets/'.$set->id);
                                        ?>
                                        
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-times" aria-hidden="true"> </i> {!! Lang::get('lang.close') !!}</button>

                                            <a href="{{$url}}" class="btn btn-danger" onclick="clickAndDisable(this);">
                                                <i class="fas fa-trash" aria-hidden="true"> </i> {!! Lang::get('lang.delete') !!}</a>
                                    </div>
                                </div> 
                            </div>
                            <script>
                                function clickAndDisable(link) {
                                    event.preventDefault();
                                    // disable subsequent clicks
                                    link.onclick = function(event) {
                                        event.preventDefault();
                                    }
                                    $.ajax({
                                        type: 'delete',
                                        url: link,
                                        data: {
                                            "_token": "{{csrf_token()}}",
                                        },
                                        success: function() {
                                            window.location.reload();
                                        },
                                        error: function() {
                                            window.location.reload();
                                        }
                                    });
                                }
                            </script>
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
                <h4 class="modal-title">{!! Lang::get('lang.create') !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissable">
                    <i class="fas fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <b>{!! Lang::get('lang.alert') !!} !</b><br>
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
                    <label for="title">{!! Lang::get('lang.name') !!}:<span style="color:red;">*</span></label><br>
                    {!! Form::text('name',null,['class'=>'form-control'])!!}
                </div>
                <?php
                $languages = [];
                foreach ($langs as $key => $value) {
                    if (Lang::getLocale() == "ar") {
                        $languages[$key] = $value[0].' &nbsp;&rlm;('.$value[1].')';
                    } else {
                        $languages[$key] = $value[0].' &nbsp;('.$value[1].')';
                    }
                }

                ?>
                <div class="form-group {{ $errors->has('template_language') ? 'has-error' : '' }}">
                    <label for="title">{!! Lang::get('lang.select_template_language') !!}:<span style="color:red;">*</span></label><br>
                    {!! Form::select('template_language',$languages, 'en' ,['class'=>'form-control'])!!}
                </div>
                <div class="form-group {{ $errors->has('department_id') ? 'has-error' : '' }}">
                    <label for="title">{!! Lang::get('lang.select_department_name') !!}:</label><br>
                    {!! Form::select('department_id' ,[''=>Lang::get('lang.select'), Lang::get('lang.departments') => $departments], null, ['class'=>'form-control'])!!}
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                
<!--  {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.save'),['type' => 'submit', 'class' =>'btn btn-primary','name'=>'dusk1'])!!}-->
                    <button type="button" class="btn btn-default" data-dismiss="modal">{!! Lang::get('lang.close') !!}</button>

                     {!!Form::button('<i class="fas fa-save" aria-hidden="true"> &nbsp;</i>'.Lang::get('lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
                </div>
            {!! Form::close() !!}
        </div> 
    </div>
</div>  
<!-- set script -->
@stop
@push('scripts')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        @if($errors->all())
        $("#create").modal('show');
        @endif
        $("#example1").dataTable();
        $('#example2').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
            },
            "bFilter": false,
            "bSort": true,
            "bInfo": true,
            "bAutoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": [4]},
                { "searchable": false, "targets": [4] },
            ]
        });
    });
</script>
@endpush