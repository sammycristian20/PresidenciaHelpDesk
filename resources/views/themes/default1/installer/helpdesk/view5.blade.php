@extends('themes.default1.installer.layout.installer')

@section('license')
done
@stop

@section('environment')
done
@stop

@section('database')
done
@stop

@section('locale')
active
@stop

@section('content')

<!-- <body onbeforeunload="return myFunction()"> -->
 <div id="form-content">

<div ng-app="myApp">
        <h1 style="text-align: center;">Getting Started</h1>
        {!! Form::open(['url'=>route('license-code'), 'id' => 'postaccount']) !!}
        

        <!-- checking if the form submit fails -->
        @if($errors->first('firstname')||$errors->first('Lastname')||$errors->first('email')||$errors->first('username')||$errors->first('password')||$errors->first('confirm_password')||$errors->first('aws_access_key_id')||$errors->first('aws_access_key')||$errors->first('aws_default_region')||$errors->first('aws_bucket') || $errors->first('driver') || $errors->first('aws_endpoint'))
            <div class="woocommerce-message woocommerce-tracker">
                <div class="fail">
                    @if($errors->first('firstname'))
                        <span id="fail">{!! $errors->first('firstname', ':message') !!}</span><br/><br/>
                    @endif
                    @if($errors->first('Lastname'))
                        <span id="fail">{!! $errors->first('Lastname', ':message') !!}</span><br/><br/>
                    @endif
                    @if($errors->first('email'))
                        <span id="fail">{!! $errors->first('email', ':message') !!}</span><br/><br/>
                    @endif
                    @if($errors->first('username'))
                        <span id="fail">{!! $errors->first('username', ':message') !!}</span><br/><br/>
                    @endif
                    @if($errors->first('password'))
                        <span id="fail">{!! $errors->first('password', ':message') !!}</span><br/><br/>
                    @endif
                    @if($errors->first('confirm_password'))
                        <span id="fail">{!! $errors->first('confirm_password', ':message') !!}</span><br/><br/>
                    @endif

                    @if($errors->first('aws_access_key_id'))
                        <span id="fail">{!! $errors->first('aws_access_key_id', ':message') !!}</span><br/><br/>
                    @endif

                    @if($errors->first('aws_access_key'))
                        <span id="fail">{!! $errors->first('aws_access_key', ':message') !!}</span><br/><br/>
                    @endif

                    @if($errors->first('aws_default_region'))
                        <span id="fail">{!! $errors->first('aws_default_region', ':message') !!}</span><br/><br/>
                    @endif

                    @if($errors->first('aws_bucket'))
                        <span id="fail">{!! $errors->first('aws_bucket', ':message') !!}</span><br/><br/>
                    @endif

                    @if($errors->first('aws_endpoint'))
                        <span id="fail">{!! $errors->first('aws_endpoint', ':message') !!}</span><br/><br/>
                    @endif

                    @if($errors->first('driver'))
                        <span id="fail">{!! $errors->first('driver', ':message') !!}</span><br/><br/>
                    @endif

                </div>
            </div>        
        @endif

        <!-- checking if the system fails -->
        @if(Session::has('fails'))
            <div class="woocommerce-message woocommerce-tracker">
                <div class="fail">
                    <span id="fail">{{Session::get('fails')}} </span><br/><br/>
                </div>
            </div>
        @endif

    <div ng-controller="MainController"> 
            <table>                
                <h1>Sign up as Admin</h1>
                <div>
                    <tr>
                        <td>
                            <label for="box1">First Name<span style="color
                                : red;font-size:12px;">*</span></label>
                        </td>
                        <td>
                            {!! Form::text('firstname',null,['style' =>'margin-left:250px', 'required' => true]) !!}
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Nametitle}}" data-content="@{{Namecontent}}" style="padding: 0px;border: 0px; border-radius: 5px;">
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="box2">Last Name<span style="color
                                : red;font-size:12px;">*</span></label>
                        </td>
                        <td>
                            {!! Form::text('Lastname',null,['style' =>'margin-left:250px', 'required' => true]) !!}
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Lasttitle}}" data-content="@{{Lastcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;">
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="box2">Email<span style="color
                                : red;font-size:12px;">*</span></label>
                        </td>
                        <td>
                            {!! Form::email('email',null,['style' =>'margin-left:250px', 'required' => true]) !!}
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Emailtitle}}" data-content="@{{Emailcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;">
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label for="box4">Username <span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                        </td>
                        <td>
                            {!! Form::text('username',null,['style' =>'margin-left:250px', 'required' => true]) !!}
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{UserNametitle}}" data-content="@{{UserNamecontent}}" style="padding: 0px;border: 0px; border-radius: 5px;"><i class="fa fa-question-circle" style="padding: 0px;"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="box4">Password <span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                        </td>
                        <td>
                            <input type="password" name="password" style="margin-left: 250px" required="true">
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Passtitle}}" data-content="@{{Passcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;">
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="box5">Confirm Password<span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                        </td>
                        <td>
                            <input type="password" name="confirm_password" style="margin-left: 250px" required="true">
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Confirmtitle}}" data-content="@{{Confirmcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;"> </button>
                        </td>
                    </tr>
                </div>
            </table>
            <table>
                <h1>System Information</h1>
                <div>
                    
                    <tr>
                        <td>
                            {!! Form::label('time_zone',Lang::get('lang.time_zone')) !!}
                        </td>

                                  <?php  
                                    $timezonesList = \App\Model\helpdesk\Utility\Timezones::orderBy('id','ASC')->get();

                                                 //
                                    foreach ($timezonesList as $timezone) {
                                    $location = $timezone->location;
                                    $start  = strpos($location, '(');
                                    $end    = strpos($location, ')', $start + 1);
                                    $length = $end - $start;
                                    $result = substr($location, $start + 1, $length - 1);
                                    $display[]=(['id'=>$timezone->name ,'name'=> '('.$result.')'.' '.$timezone->name]);
                                    }
                                     //for display 
                                    $timezones = array_column($display,'name','id');

                                   $browserTimezone = \Cache::get('timezone');
                                   // dd($browserTimezon
                                   ?>
                        <td>
                             <div  style="margin-left: 230px;">
                              <select name="timezone" data-placeholder="Choose a timezone..." class="chosen-select" style="width:295px;" tabindex="2">
                                    @foreach($timezones as $key=>$value)
                                        dump($value);

                                        <option value="{!! $key !!}" @if($key==$browserTimezone) selected @endif>{!! $value !!}&nbsp;</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Timezonetitle}}" data-content="@{{Timezonecontent}}" style="padding: 0px;border: 0px; border-radius: 5px;"><i class="fa fa-question-circle" style="padding: 0px;"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {!! Form::label('language',Lang::get('lang.language')) !!}
                        </td>
                        <td>
                            <div  style="margin-left: 230px;">
                            <?php 
                            $path = base_path('resources/lang'); 
                            $values = scandir($path);
                            $values = array_slice($values, 2);
                            ?>  
                                <select name="language" data-placeholder="Choose a timezone..." class="chosen-select" style="width:295px;" >
                                    @foreach($values as $value)
                                        <option value="{!! $value !!}" @if($value=="en") selected @endif>{!! Config::get('languages.' . $value)[0] !!}&nbsp;({!! Config::get('languages.' . $value)[1] !!})</option>
                                    @endforeach
                                </select>
                           </div>
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Languagetitle}}" data-content="@{{Languagecontent}}" style="padding: 0px;border: 0px; border-radius: 5px;"><i class="fa fa-question-circle" style="padding: 0px;"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {!! Form::label('environment',Lang::get('lang.environment')) !!}
                        </td>
                        <td>
                            <div  style="margin-left: 230px;">
                                <select name="environment" data-placeholder="{{ trans('lang.select-environment')}}" class="chosen-select" style="width:295px;" tabindex="2">
                                    <option selected="true" value="production">{{ trans('lang.production') }}</option>
                                    @if(strpos(config('app.version'), "Enterprise") !== false)
                                    <option value="development">{{ trans('lang.development') }}</option>
                                    <option value="testing">{{ trans('lang.testing') }}</option>
                                    @endif
                                </select>
                           </div>
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{EnvTitle}}" data-content="@{{EnvContent}}" style="padding: 0px;border: 0px; border-radius: 5px;"><i class="fa fa-question-circle" style="padding: 0px;"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            {!! Form::label('probe_storage_driver',Lang::get('lang.probe_storage_driver')) !!}
                        </td>
    
                        <td>
    
                            <div  style="margin-left: 230px;">
                                
                                <?php 
    
                                    $disks = [];
                                
                                    $allDisks = config('filesystems.disks');
    
                                    foreach ($allDisks as $diskKey => $disk) {
                                        if (in_array($diskKey, ['private','rackspace','local','public'])) {
                                            //above check is not required once s3 and other disks are supported
                                            continue;
                                        }
                                        $disks[] = ['id' => $diskKey, 'name' => ucfirst($diskKey)];
                                    }
                                    
                                ?>
    
                                <select required id="driverSelect" name="driver" data-placeholder="{{ trans('lang.probe_storage_driver_placeholder')}}" class="chosen-select pnChosen" style="width:295px;" tabindex="2">
                                    
                                    @foreach($disks as $disk)
                                        <option value="{!! $disk['id'] !!}" @if ($disk['id'] == 'system') selected @endif >{!! $disk['name'] !!}</option>
                                    @endforeach

                        
                                </select>
                           </div>
    
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{StorageTitle}}" data-content="@{{StorageContent}}" style="padding: 0px;border: 0px; border-radius: 5px;"><i class="fa fa-question-circle" style="padding: 0px;"></i>
                            </button>
                        </td>
                    </tr>

                    
                        <tr class="hiddenCredentials">

                            <td>
                                <label for="box4">{{Lang::get('lang.probe_aws_access_key_id')}} <span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                                
                            </td>

                            <td>
                                {!! Form::text('aws_access_key_id',null,['style' =>'margin-left:230px;width:295px', 'required' => false,'class' => 'hiddenInput']) !!}
                            </td>

                        </tr>

                        <tr class="hiddenCredentials">

                            <td>
                                <label for="box4">{{Lang::get('lang.probe_aws_access_key')}} <span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                            </td>

                            <td>
                                {!! Form::text('aws_access_key',null,['style' =>'margin-left:230px;width:295px', 'required' => false,'class' => 'hiddenInput']) !!}
                    
                            </td>

                        </tr>

                        <tr class="hiddenCredentials">

                            <td>
                                <label for="box4">{{Lang::get('lang.probe_aws_default_region')}} <span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                            </td>

                            <td>
                                {!! Form::text('aws_default_region',null,['style' =>'margin-left:230px;width:295px', 'required' => false,'class' => 'hiddenInput']) !!}
                               
                            </td>

                        </tr>

                        <tr class="hiddenCredentials">

                            <td>
                                <label for="box4">{{Lang::get('lang.probe_aws_bucket')}} <span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                            </td>

                            <td>
                                {!! Form::text('aws_bucket',null,['style' =>'margin-left:230px;width:295px', 'required' => false, 'class' => 'hiddenInput']) !!}
                            </td>

                        </tr>

                    <tr class="hiddenCredentials">

                        <td>
                            <label for="box4">{{Lang::get('lang.probe_aws_endpoint')}} <span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                        </td>

                        <td>
                            {!! Form::text('aws_endpoint',null,['style' =>'margin-left:230px;width:295px', 'required' => false, 'class' => 'hiddenInput']) !!}
                        </td>

                    </tr>
        
                   
                </div>
               
            </table>
            <br><br>
            <p class="setup-actions step">


                <input type="submit" id="submitme" class="button-primary button button-large button-next" value="Continue">


            </p>
        </form>
    </div>
    </p>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular.min.js"></script>
    <script src="{{assetLink('js','angular2')}}" type="text/javascript"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js">
</script>


    </div>
    </div>

    <script type="text/javascript">

        $(document).ready(function(){
            $('.hiddenCredentials').hide();
            $('input.hiddenInput').prop('required',false);
        })

        $('#driverSelect').change(function (){
            var value = $(this).val();
            if (value == 's3') {
                $('.hiddenCredentials').show();
                $('input.hiddenInput').prop('required',true);
            } else {
                $('.hiddenCredentials').hide();
                $('input.hiddenInput').prop('required',false);

            }
        })
        
            @if($errors->has('firstname'))
                addErrorClass('firstname');
            @endif
            @if($errors->has('Lastname'))
                addErrorClass('Lastname');
            @endif
            @if($errors->has('email'))
                addErrorClass('email');
            @endif
            @if($errors->has('username'))
                addErrorClass('username');
            @endif
            @if($errors->has('password'))
                addErrorClass('password');
            @endif
            @if($errors->has('confirmpassword'))
                addErrorClass('confirmpassword');
            @endif
            @if($errors->has('driver'))
                addErrorClass('driver');
            @endif
        $('#postaccount').on('submit', function(e) {
            $empty_field = 0;
            $("#postaccount input").each(function() {
                if($(this).attr('name') == 'firstname' ||
                   $(this).attr('name') == 'Lastname' ||
                   $(this).attr('name') == 'email' ||
                   $(this).attr('name') == 'username' ||
                   $(this).attr('name') == 'password' ||
                   $(this).attr('timezone') == 'password' ||
                   $(this).attr('name') == 'confirmpassword'){
                    if ($(this).val() == '') {
                        $(this).css('border-color','red')
                        $(this).css('border-width','1px');
                        $empty_field = 1;
                    } else {
                        $empty_field = 0;
                    }
                }
            });
            if ($empty_field !=0 ) {
                alert('Please fill all required values.');
                e.preventDefault();
                $('#submitme').attr('disabled', false);
                $('#submitme').val('Install');
            }
        });
        $('input').on('focus', function(){
            $(this).css('border-color','#A9A9A9')
            $(this).css('border-width','1px');
        })
        $('input').on('blur', function(){
            if($(this).val() == ''){
                addErrorClass($(this).attr('name'));
            }
        });
        function addErrorClass(name){
            var target = document.getElementsByName(name);
            $(target[0]).css('border-color','red');
            $(target[0]).css('border-width','1px');
        }
    </script>
@stop