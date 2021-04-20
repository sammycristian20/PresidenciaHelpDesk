@extends('themes.default1.agent.layout.agent')
@section('custom-css')
    <style>

        .content-wrapper { min-height: inherit !important; }
    </style>
@stop
@section('Tickets')
active
class="active"
@stop

@section('ticket-bar')
active
@stop

<?php
$inputs     = \Input::get('show');
$activepage = $inputs[0];
if (\Input::has('assigned'))
{
    $activepage = \Input::get('assigned')[0];
}
$relativeUrl = str_replace(\URL::to('/'), '', \Request::url());
(strpos($relativeUrl, 'filter') !== false) ? $activepage = 'filter' : '';
?>


@section('PageHeader')
<h1>@if($activepage == 'trash')
            {{Lang::get('lang.trash')}}
            @elseif ($activepage == 'mytickets')
            {{Lang::get('lang.my_tickets')}}
            @elseif ($activepage == 'followup')
            {{Lang::get('lang.followup')}}
            @elseif($activepage == 'inbox')
            {{Lang::get('lang.inbox')}}
            @elseif($activepage == 'overdue')
            {{Lang::get('lang.overdue')}}
            @elseif($activepage == 'closed')
            {{Lang::get('lang.closed')}}
            @elseif($activepage == 'approval')
            {{Lang::get('lang.approval')}}
             @elseif($activepage == 'unapproved')
            {{Lang::get('lang.unapproved')}}
            @elseif($activepage == 'waiting-for-approval')
           {{Lang::get('lang.waiting-for-approval')}}
            @elseif($activepage == 'due_today')
            {{Lang::get('lang.due_today')}}
            @elseif($activepage == 'spam')
            {{Lang::get('lang.spam')}}
            @elseif($activepage == 'filter')
            {{Lang::get('lang.filter')}}
            @elseif($activepage == 0)
            {{Lang::get('lang.unassigned')}}
            @else
            {{Lang::get('lang.inbox')}}
            @endif
            @if(count(Input::all()) >= 2)
            / {{Lang::get('lang.filtered-results')}}
            @endif</h1>


@stop



@section('content')

    @include('themes.default1.common.recaptcha')

    <div id="app-agent">

        <inbox-layout :ticket-page="{{ json_encode($activepage) }}">
            
        </inbox-layout>
    </div>
    <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop
