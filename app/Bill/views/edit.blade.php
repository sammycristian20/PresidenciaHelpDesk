<a href="#bill-edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#bill-edit{{$bill->id}}">Edit</a>
<div class="modal fade" id="bill-edit{{$bill->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{!! Lang::get('lang.edit') !!}</h4>
                {!! Form::model($bill,['url'=>'bill/'.$bill->id,'method'=>'patch']) !!}
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::label('agent',Lang::get('lang.agent')) !!}
                        {!! Form::select('Agent',[],null,['class'=>'form-control']) !!}
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('hours',Lang::get('lang.hours')) !!}
                        {!! Form::text('hours',null,['class'=>'form-control','placeholder'=>'HH:MM']) !!}
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('amount_hourly',Lang::get('lang.amount-per-hour')) !!}
                        {!! Form::text('amount_hourly',null,['class'=>'form-control']) !!}
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('note',Lang::get('lang.note')) !!}
                        {!! Form::textarea('note',null,['class'=>'form-control']) !!}
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('billable',Lang::get('lang.billable')) !!}
                        <div class="col-md-1">
                            <input type="radio" name="billable" value="1">
                        </div>
                        <div class="col-md-1">
                            <input type="radio" name="billable" value="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                {!! Form::submit(Lang::get('lang.update'),['class'=>'btn btn-default']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<td>{!! str_limit($bill->note,20) !!}</td>
                    <td>{!! $bill->billable() !!}</td>
                    <td>{!! $bill->hours() !!}</td>
                    <td>{!! \App\Bill\Controllers\BillController::currency() !!} {!! $bill->amountPerHour() !!}</td>
                    <td>{!! \App\Bill\Controllers\BillController::currency() !!} {!! $bill->amount() !!}</td>
                    <td>{!! $bill->agent() !!}</td>
                   