<button class="btn btn-primary" data-toggle="modal" data-target="#export">{!!Lang::get('lang.export')!!}</button>
<div class="modal fade" id="export">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{!!Lang::get('lang.export')!!}</h4>
                <div id="export-response"></div>
            </div>
            <div class="modal-body">
                <div id="loading-form" style="display: none";>
                    <img src="{{assetLink('image','gifloader')}}">
                </div>
                <form id="export-form">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','tickets.ticket_number') !!} Ticket Number
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','ticket_thread.title') !!} Ticket Title
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','ticket_source.name as source') !!} Ticket Source
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','department.name as department') !!} Ticket Department
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','tickets.duedate') !!} Ticket due date
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','tickets.created_at as created') !!} Ticket created Date
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','client.user_name as client_user_name') !!} Client User name
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','client.email as client_email') !!} Client email
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','client.first_name as client_first_name') !!} Client First Name
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','client.last_name as as client_last_name') !!} Client Last Name
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','agent.user_name as agent_user_name') !!} Agent User name
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','agent.email as agent_email') !!} Agent Email
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','agent.first_name as agent_first_name') !!} Agent First Name
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','agent.last_name as agent_last_name') !!} Agent Last Name
                        </div>
                        <div class="col-md-4">
                            {!! Form::checkbox('fields[]','ticket_priority.priority') !!} Ticket Priority
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="date">{!!Lang::get('lang.date-range')!!}</label>
                            <input type="text" name="date" class="form-control" value="{{Carbon\Carbon::now()->subMonth()->format('m/d/Y')}}-{{Carbon\Carbon::now()->format('m/d/Y')}}" />
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

