
@if($bills)

<div class="tab-pane" id="bill">
    <div>
        <table class="table table-responsive">
            <thead>
                <tr>
                    <th>{!! Lang::get('lang.note') !!}</th>
                    <th>{!! Lang::get('lang.billable') !!}</th>
                    <th>{!! Lang::get('lang.hours') !!}</th>
                    <th>{!! Lang::get('lang.amount-per-hour') !!}</th>
                    <th>{!! Lang::get('lang.amount') !!}</th>
                    <th>{!! Lang::get('lang.agent') !!}</th>
                    <th>{!! Lang::get('lang.action') !!}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr>
                    <td>{!! str_limit($bill->note,20) !!}</td>
                    <td>{!! $bill->billable() !!}</td>
                    <td>{!! $bill->hours() !!}</td>
                    <td>
                        @if($bill->amountPerHour()!=="--")
                        {!! \App\Bill\Controllers\BillController::currency() !!} 
                        @endif
                        {!! $bill->amountPerHour() !!}
                    </td>
                    <td>
                        @if($bill->amountPerHour()!=="--")
                        {!! \App\Bill\Controllers\BillController::currency() !!} 
                        @endif
                        {!! $bill->amount() !!}
                    </td>
                    <td>{!! $bill->getAgent() !!}</td>
                    <td>{!! deletePopUp($bill->id,url('bill/'.$bill->id.'/delete'),$title = "Delete", $class = "btn btn-xs btn-primary") !!} &nbsp;
                    {!! \App\Bill\Controllers\BillController::edit($bill) !!}</td>
                </tr>
                @empty 
                <tr><td>{!! Lang::get('lang.no-billing-records') !!}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-3">
            <p>{!! Lang::get('lang.total-billable-time') !!} : {!! $billable['time'] !!} {!! Lang::get('lang.hours') !!}</p>
        </div>
        
        <div class="col-md-3">
            <p>{!! Lang::get('lang.total-non-billable-time') !!} : {!! $nonbillable['time'] !!} {!! Lang::get('lang.hours') !!}</p>
        </div>
        
        <div class="col-md-3">
            <p>{!! Lang::get('lang.total-amount') !!} : {!! \App\Bill\Controllers\BillController::currency() !!} {!! $billable['amount'] !!}</p>
        </div>
        <div class="col-sm-3 pull-right">
            <a href="{{url('ticket/'.$ticket->id.'/invoice')}}" class="btn btn-primary">{{Lang::get('lang.send-invoice')}}</a>
        </div>
    </div>
</div>
@else
<div class="tab-pane" id="bill">
    <div>
        <table class="table table-responsive">
            <thead>
                <tr>
                    <th>{!! Lang::get('lang.hours') !!}</th>
                    <th>{!! Lang::get('lang.amount-per-hour') !!}</th>
                    <th>{!! Lang::get('lang.amount') !!}</th>
                    <th>{!! Lang::get('lang.last-billing-time') !!}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{!! $billable !!}</td>
                    <td>{!! \App\Bill\Controllers\BillController::currency() !!} {!! $amount_hourly !!}</td>
                    <td>{!! \App\Bill\Controllers\BillController::currency() !!} {!! $cost !!}</td>
                    <td>{!! \Carbon\Carbon::now()->tz(timezone()) !!}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-sm-2 pull-right">
            <a href="{{url('ticket/'.$ticket->id.'/invoice')}}" class="btn btn-primary">{{Lang::get('lang.send-invoice')}}</a>
        </div>
    </div>
</div>
@endif
