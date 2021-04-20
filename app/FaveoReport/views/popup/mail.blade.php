<button class="btn btn-primary pull-right" data-toggle="modal" data-target="#mail">{!! Lang::get('lang.mail') !!}</button>
<div class="modal fade" id="mail">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{!! Lang::get('lang.mail') !!}</h4>
                <div id="export-response"></div>
            </div>
            <div class="modal-body">
                <div id="mail-response"></div>
                <div id="loading-mail" style="display: none; text-align: center;">
                    <img src="{{assetLink('image','gifloader')}}">
                </div>
                <form id="mail-form">

                    <div class="row">
                        <div class="col-md-12">
                            <label>{!! Lang::get('lang.agents') !!}</label><br>
                            <select class="agents-ajax form-control" style="width: 100%" multiple="multiple" name="send_agents[]" id="agents">
                            </select>
                        </div>
                        <div class="col-md-12">
                            {!! Form::label('from',Lang::get('lang.from')) !!}
                            {!! Form::select('from',[''=>'Select','System Emails'=>App\Model\helpdesk\Email\Emails::pluck('email_address','id')->toArray()],null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-12">
                            {!! Form::label('subject',Lang::get('lang.subject')) !!}
                            {!! Form::text('subject',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-12">
                            {!! Form::label('message',Lang::get('lang.message')) !!}
                            {!! Form::textarea('message',null,['class'=>'form-control']) !!}
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal" id="dismis4">{!! Lang::get('lang.close') !!}</button>
                <button type="submit" class="btn btn-success pull-right" id="send">{!!Lang::get('lang.send')!!}</button>
            </div>

        </div>
    </div>
</div>

