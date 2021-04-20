@extends('themes.default1.admin.layout.admin')
@section('content')
   
   {{ Form::open(array('url' => url('update-order-details'))) }}
      <div class="box box-info">
         <div class="box-header text-center">
            <h4> Update your License Code</h4>
         </div>
            <div class="row">
               <div class="text-center col-md-3 col-lg-3 col-sm-5 col-xs-5">
                  <label>License Code</label> : 
               </div>
               <div class="col-md-8 col-lg-8 col-sm-6 col-xs-6">
                  <input type="text" name="serial_key"  class="form-control" required placeholder="Enter your License Code"><br>
               </div>
               <button type="submit" class="btn btn-primary">Submit</button><br>
            </div>
            
      </div>
   {{Form::close()}}
@stop
@section('FooterInclude')
  @if(App::isDownForMaintenance())
    <script src="{{assetLink('js','jquery-2')}}" type="text/javascript"></script>
  @endif
@stop