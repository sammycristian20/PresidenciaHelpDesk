<div class="box-body" >
@if(Session::has('success'))
    <div class="alert alert-success alert-dismissable">
        <i class="fa  fa-check-circle"></i>
        <b>Success</b>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{Session::get('success')}}
    </div>
    @endif
    <!-- failure message -->
    @if(Session::has('fails'))
    <div class="alert alert-danger alert-dismissable">
        <i class="fa fa-ban"></i>
        <b>Fail!</b>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{Session::get('fails')}}
    </div>
    @endif

    <div class="row">

		<div class="col-xs-12 form-group {{ $errors->has('catagory_name') ? 'has-error' : '' }}">

			{!! Form::label('catagory_name',Lang::get('lang.name')) !!}<span class="text-red"> *</span>
			{!! $errors->first('catagory_name', '<spam class="help-block">:message</spam>') !!}
			{!! Form::text('catagory_name',null,['id'=>'catagory_name','class' => 'form-control','required'=>'required']) !!}
                <p id="catagory_name_error" style="color:red;"></p>
        </div>
        </div>
    
         <div class="row">
        
             <div class="col-md-6">
                <div class="form-group {{ $errors->has('display_order') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.display_order') !!}: <span class="text-red"> *</span></label><br>
                     {!! Form::number('display_order',null,['id'=>'catagory_display_order','class' => 'form-control numberOnly','required'=>'required','min'=>'0']) !!}
                    
                   <p id="catagory_display_order_error" style="color:red;"></p>
                </div>
            </div>

            <div class="col-xs-6 form-group {{ $errors->has('status') ? 'has-error' : '' }}">

            {!! Form::label('status',Lang::get('lang.status')) !!}
            {!! $errors->first('new_category_status', '<spam class="help-block">:message</spam>') !!}
            <div class="row">
             <div class="col-md-3">
                        {!! Form::radio('new_category_status','1',true) !!} &nbsp;&nbsp;&nbsp;<span style="position: absolute;">{{Lang::get('lang.active')}}</span>
                    </div>
                    <div class="col-md-3">
                        {!! Form::radio('new_category_status','0') !!} &nbsp;&nbsp;&nbsp;<span style="position: absolute;">{{Lang::get('lang.inactive')}}</span>
                    </div>
                
          </div>
        </div>

            </div>
		<div class="form-group {{ $errors->has('catagory_description') ? 'has-error' : '' }}">
				{!! Form::label('catagory_description',Lang::get('lang.description')) !!}<span class="text-red"> *</span>
				{!! $errors->first('catagory_description', '<spam class="help-block">:message</spam>') !!}

					{!! Form::textarea('catagory_description',null,['class' => 'form-control','size' => '50x10','id'=>'catagorydesc','placeholder'=>Lang::get('lang.enter_the_description'),'required'=>'required']) !!}

                    <p id="catagory_display_order_error" style="color:red;"></p>
		</div>
</div>
