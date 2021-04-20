<a href="#url"  data-toggle="modal" class="btn btn-primary btn-xs" data-target="#urlpopup{{$app}}"><i class="fa fa-link" aria-hidden="true">&nbsp;&nbsp;</i>URL</a>
<div class="modal fade" id="urlpopup{{$app}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$app}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 form-group {{ $errors->has('departments') ? 'has-error' : '' }}">

                    {!! Form::label('department','Departments') !!}
                    {!! Form::select('department',$departments,null,['class' => 'form-control']) !!}

                </div>
                <div class="col-md-6 form-group {{ $errors->has('help') ? 'has-error' : '' }}">

                    {!! Form::label('help','Helptopic') !!}
                    {!! Form::select('help',$topics,null,['class' => 'form-control']) !!}

                </div>
                <div class="col-md-12">
                    <div id="url"></div>
                </div>
            </div>
            
        </div>
            </div>
            <!-- /Form -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
