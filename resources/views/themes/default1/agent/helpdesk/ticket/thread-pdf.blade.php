<!DOCTYPE html>
<?php
$lang = \Lang::getLocale();
?>

<html>
    <head>
        <title>PDF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css">
    .ar{
        direction: rtl;
        text-align: right;
        font-family: 'dejavu sans, arial', sans-serif;
    }
        .nonar{
        /*direction: rtl;
        text-align: right;*/
        font-family: 'dejavu sans, arial', sans-serif;
    }

</style>

    </head>

    <body>
    @if($lang == 'ar')
    <div class="ar">
    <h2>
            <div id="logo" class="site-logo text-center" style="font-size: 30px; text-decoration:none !important;">
                <center>
                    @if($system->url)
                    <a href="{!! $system->url !!}" rel="home">
                        @else
                        <a href="{{url('/')}}" rel="home" style="text-decoration:none !important;">
                            @endif
                            @if($company->use_logo == 1)
                            <img src="{!! public_path().'/uploads/company'.'/'.$company->logo !!}" width="100px;"/>
                            @else
                            @if($system->name)
                            {!! $system->name !!}
                            @else
                            <label>SUPPORT CENTER</label>
                            @endif
                            @endif
                        </a>
                </center>
            </div>
        </h2>
        <hr>
        <h4>{!! $thread->title !!} :{!! Lang::get('lang.subject') !!}</h4><br/>
      
            <h3>{{$thread->ticket_number}}:{!! Lang::get('lang.ticket_number') !!}</h3><br/>
            <h3>{{ucfirst($thread->first_name)}}:{!! Lang::get('lang.first_name') !!}</h3><br/>
            <h3>{{ucfirst($thread->last_name)}} :{!! Lang::get('lang.last_name') !!}</h3><br/>
            <h3>{{$thread->email}}:{!! Lang::get('lang.email') !!}</h3><br/>
            <h3>{{$thread->phone}}:{!! Lang::get('lang.phone') !!}</h3><br/>
            {{$thread->mobile}}:{!! Lang::get('lang.mobile') !!}</h3><br/>
             @if($ticket->getAssignedTo())
           <h3> {{$ticket->getAssignedTo()->first_name}} {{$ticket->getAssignedTo()->last_name}}:{!! Lang::get('lang.assigned_to') !!}</h3><br/>
            @endif
        </table>
        <hr>
        <div>
            <h3>Issue</h3>
        </div>
        <div>
            {!! $thread->body !!}
        </div>
        <hr>
        </div>

    @else
    <div class="nonar">
    <h2>
            <div id="logo" class="site-logo text-center" style="font-size: 30px;">
                <center>
                    @if($system->url)
                    <a href="{!! $system->url !!}" rel="home" style="text-decoration:none !important;">
                        @else
                        <a href="{{url('/')}}" rel="home" style="text-decoration:none !important;">
                            @endif
                            @if($company->use_logo == 1)
                            <img src="{!! public_path().'/uploads/company'.'/'.$company->logo !!}" width="100px;"/>
                            @else
                            @if($system->name)
                            {!! $system->name !!}
                            @else
                            <label>SUPPORT CENTER</label>
                            @endif
                            @endif
                        </a>
                </center>
            </div>
        </h2>
        <hr>
        <h4>Subject : {!! $thread->title !!}</h4><br/>
        <table> 
            <tr><td><b>{!! Lang::get('lang.ticket_number') !!}:</b></td>            <td>{{$thread->ticket_number}}</td></tr>
            <tr><td><b>{!! Lang::get('lang.first_name') !!}:</b></td>           <td>{{ucfirst($thread->first_name)}}</td></tr>
            <tr><td><b>{!! Lang::get('lang.last_name') !!}:</b></td>            <td>{{ucfirst($thread->last_name)}}</td></tr>
            <tr><td><b>{!! Lang::get('lang.email') !!}:</b></td>            <td>{{$thread->email}}</td></tr>
            <tr><td><b>{!! Lang::get('lang.phone') !!}:</b></td>            <td>{{$thread->phone}}</td></tr>
            <tr><td><b>{!! Lang::get('lang.mobile') !!}:</b></td>           <td>{{$thread->mobile}}</td></tr>
            @if($ticket->getAssignedTo())
            <tr><td><b>{!! Lang::get('lang.assigned_to') !!}:</b></td>          <td>{{$ticket->getAssignedTo()->first_name}} {{$ticket->getAssignedTo()->last_name}}</td></tr>
            @endif
        </table>
        <hr>
        <div>
            <h3>Issue</h3>
        </div>
        <div>
            {!! $thread->body !!}
        </div>
        <hr>
        </div>

    @endif
        
    </body>
</html>



