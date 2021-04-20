@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.bans_list-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.bans_list-page-description') !!}">
@stop

@section('Emails')
active
@stop

@section('emails-bar')
active
@stop

@section('ban')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.ban_email') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">

</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')

<!-- check whether success or not -->
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa  fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.fails') !!} ! </b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<div class="box box-primary">
    <div class="box-header with-border">
        <h2 class="box-title">{{Lang::get('lang.list_of_banned_emails')}}</h2><a href="{{route('banlist.create')}}" class="pull-right btn btn-primary"><i class="fa fa-ban">&nbsp;&nbsp;</i>{{Lang::get('lang.ban_email')}}</a>
    </div>
    <div class="box-body">
        
        <table class="table table-bordered dataTable" style="overflow:hidden;">
            <tr>
                <th width="100px">{{Lang::get('lang.email_address')}}</th>
                <th width="100px">{{Lang::get('lang.last_updated')}}</th>
                <th width="100px">{{Lang::get('lang.action')}}</th>
            </tr>
            <!-- Foreach @var bans as @var ban -->
            @foreach($bans as $ban)
            <tr>
                <!-- Email Address with Link to Edit page along Id -->
                <td><a href="{{route('banlist.edit',$ban->id)}}">{!! $ban->email !!}</a></td>
                <!-- Last Updated -->
                <td> {!! faveoDate($ban->updated_at) !!} </td>
                <!-- Deleting Fields -->
            
                <td>
                    {!! Form::open(['route'=>['banlist.delete', $ban->id],'method'=>'post']) !!}
                    
                     <?php
                        $url = url('delete/banlistpopup/'.$ban->id);
                        $confirmation = deletePopUp($ban->id, $url, "Delete",'btn btn-primary btn-xs');
                       ?>
                    
                    <a href="{{route('banlist.edit', $ban->id)}}" class="btn btn-primary btn-xs "><i class="fa fa-edit" style="color:white;"> </i>&nbsp; Edit</a>&nbsp;&nbsp;{!!$confirmation!!}
          
                  
                    {!! Form::close() !!}
                </td>
            </tr>
        
            @endforeach
            
            <!-- Set a link to Create Page -->
        </table>
    </div>
</div>

@stop
