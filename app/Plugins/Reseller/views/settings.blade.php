@extends('themes.default1.admin.layout.admin')

@section('Settings')
class="active"
@stop

@section('settings-bar')
active
@stop

@section('reseller')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>ResellerClub</h1>
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



<!-- open a form -->

{!! Form::model($reseller,['url' => 'reseller/postsettings', 'method' => 'PATCH','files'=>true,'id'=>'Form']) !!}

<!-- <div class="form-group {{ $errors->has('company_name') ? 'has-error' : '' }}"> -->
<!-- table  -->
<div id="response"></div>
<div class='alert alert-danger' id="error-div"  style="display:none">
        <ul id="error">

        </ul>
    </div>
    <!-- check whether success or not -->
    @if(Session::has('success'))
     
    <div class="alert alert-success alert-dismissable">
        <i class="fas fa-check-circle"></i>
        <span>Success!</span>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{Session::get('success')}}
    </div>
    @endif
    <!-- failure message -->
    @if(Session::has('fails'))
    <div class="alert alert-danger alert-dismissable">
        <i class="fas fa-ban"></i>
        <span>Alert!</span> Failed.
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{Session::get('fails')}}
    </div>
    @endif
<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">ResellerClub settings</h3>
    </div>

    <div id="gif" style="display:none">
        <div class="text-center mt-2">
            <img src="{{asset("themes/default/common/images/gifloader.gif")}}">
        </div>
    </div>
    
    

    <!-- Name text form Required -->
    <div class="card-body">
        <div class="row">



            <div class="col-md-6 form-group {{ $errors->has('userid') ? 'has-error' : '' }}">

                {!! Form::label('userid','Reseller User ID') !!}
                {!! Form::text('userid',null,['class' => 'form-control']) !!}

            </div>

            <div class="col-md-6 form-group {{ $errors->has('apikey') ? 'has-error' : '' }}">

                {!! Form::label('apikey','Reseller API Key') !!}
                {!! Form::text('apikey',null,['class' => 'form-control']) !!}

            </div>
            <div class="col-md-6 form-group {{ $errors->has('mode') ? 'has-error' : '' }}" >


                {!! Form::label('mode','Test/Live Mode') !!}
                <div class="row">
                        <div class="col-md-3">
                            <p>{!! Form::radio('mode',0,true) !!}   Test</p>
                        </div>
                        <div class="col-md-3">
                            <p>{!! Form::radio('mode',1) !!}  Live</p>
                        </div>
                    </div>

            </div>
            <div class="col-md-6 form-group {{ $errors->has('enforce') ? 'has-error' : '' }}">


                {!! Form::label('enforce','Enforcing User Action') !!}
                <div class="row">
                    <div class="col-md-12">
                        
                            <p>{!! Form::radio('enforce',1,true) !!}  Enforce to report reseller club</p>
                       
                        
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card-footer">
                   <a href="#" class="btn btn-primary " id="submit" onclick="submit();"><i class="fas fa-save">&nbsp;</i>Save</a>
                </div>
             </div>

        @stop

        <script>
            function submit() {
                $.ajax({
                    type: "POST",
                    url: "{{url('reseller/postsettings')}}",
                    data: $('form').serialize(),
                    beforeSend: function () {
                        $('.loader1').css('display','block');
                        $("#gif").show();
                    },
                    success: function (data) {
                        $('.loader1').css('display','none');
                        $("#gif").hide();
                        $("#response").html(data)
                    },
                    error: function (data) {
                        $("#gif").hide();
                        $("#error-div").show();
                        var errors = data.responseJSON;
                        $.each(errors, function (index, value) {
                            $("#error").append("<li>" + value + "</li>");
                        });

                    }
                });
            }
        </script>

