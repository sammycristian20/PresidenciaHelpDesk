@extends('themes.default1.admin.layout.admin')

@section('Tickets')
active
@stop

@section('tickets-bar')
active
@stop

@section('location')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.location') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop

<!-- content -->
@section('content')
@if ( $errors->count() > 0 )
<div class="alert alert-danger" aria-hidden="true">
    <i class="fas fa-ban"></i>
    <b>{!! Lang::get('lang.alert') !!} !</b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <ul>
        @foreach( $errors->all() as $message )</p>
        <li class="error-message-padding">{{ $message }} </li>
        @endforeach
    </ul>
</div>
@endif


<div class="card card-light">
      <div class="card-header">
            <h3 class="card-title">{{Lang::get('lang.edit_location')}}</h3>
        </div>
    

    <!-- form start -->
    <form action="{!!URL::route('helpdesk.location.postedit',$hdLocation->id)!!}" method="post" role="form" id="Form" >
    {{ csrf_field() }}
        <input type="hidden" name="location_id" value="{{$hdLocation->id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="name" class="control-label">{{Lang::get('lang.name')}}<span class="text-red"> *</span></label> 
                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                        {!! Form::text('title',$hdLocation->title,['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="col-md-4 hide">
                    <label for="name" class="control-label">{{Lang::get('lang.email')}}<span class="text-red"> *</span></label> 

                    <?php
                    $location_email=$hdLocation->email;
                    if($location_email){
                        $email=$location_email;
                    }
                    else{

                        $email=Auth::user()->email;
                    }
                    ?>
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                         {!! Form::text('email',$email,['class' => 'form-control','readonly'=>'readonly']) !!}
                     
                    </div>
                </div>
               
            
                <div class="col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="name" class="control-label">{{Lang::get('lang.phone')}}</label> 
                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">

                           {!! Form::text('phone',$hdLocation->phone,['class' => 'form-control numberOnly']) !!}
                     </div>
                </div>
                </div> 
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12 form-group">
                    <label for="name" class="control-label">{{Lang::get('lang.address')}}</label> 
                    <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                         {!! Form::textarea('address',$hdLocation->address,['class' => 'form-control']) !!}
                      
                    </div>
                </div>
            </div> 
            <div class="form-group">
                <input type="checkbox" name="default_location" @if($hdLocation->is_default == 1) checked disabled @endif> {{ Lang::get('lang.make-default-location')}}
            </div>
            </div> 

            <div class="card-footer">
                  

             <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fas fa-sync fa-spin fa-1x fa-fw'>&nbsp;</i> updating..."><i class="fas fa-sync">&nbsp;</i>{!!Lang::get('lang.update')!!}</button>    
            </div>

    </form>
</div>
<script type="text/javascript">
 
    $(document).ready(function () {
        $(".numberOnly").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    // Allow: Ctrl/cmd+A
                            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                            // Allow: Ctrl/cmd+C
                                    (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                                    // Allow: Ctrl/cmd+X
                                            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                                            // Allow: home, end, left, right
                                                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                                        // let it happen, don't do anything
                                        return;
                                    }
                                    // Ensure that it is a number and stop the keypress
                                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                                        e.preventDefault();
                                    }
                                });
                    });

</script>
@stop
