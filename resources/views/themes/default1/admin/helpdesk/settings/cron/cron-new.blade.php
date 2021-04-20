@section('custom-css')
<link href="{{assetLink('css','bootstrap-datetimepicker4')}}" media="none" onload="this.media='all';" rel="stylesheet" type="text/css">
<style type="text/css">

    .mt-13 { margin-top: 13px; }
.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
}
</style>
@stop

<div class="card card-light">
    <div class="card-header">
        <h3 class="card-title">{{Lang::get('lang.cron_settings')}}</h3>
    </div>
    <div class="card-body"style="overflow:hidden;">
        @if(!$execEnabled)
        <div class="alert alert-warning">
            {{ trans('lang.please_enable_php_exec_for_cronjob_check') }}
        </div>
        @endif
        <div>
                
            <span>{{ trans('lang.copy-cron-command-description') }}</p>    
        </div>
        <div class="alert  alert-dismissable noselect" style="background: #F8F8F8">
            <div class="row">
                <div class="col-md-2 copy-command1">
                    <span style="font-size: 30px">*&nbsp;&nbsp;*&nbsp;&nbsp;*&nbsp;&nbsp;*&nbsp;&nbsp;*</span>
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="phpExecutableList" onchange="checksome()">
                        <option value="0">{{trans('lang.specify-php-executable')}}</option>
                        @foreach($paths as $path)
                            <option>{{$path}}</option>
                        @endforeach
                        <option value="Other">Other</option>
                    </select>
                    <div class="has-feedback" id='phpExecutableTextArea' style="display: none;">
                        <div class="has-feedback d-flex">
                            <input type="text" class="form-control input-sm" style=" padding:5px;height:34px" name="phpExecutableText" id="phpExecutableText" placeholder="{{trans('lang.specify-php-executable')}}">
                            <span class="fas fa-times form-control-feedback" style="pointer-events: initial; cursor: pointer; color: #74777a;margin-left: -20px;margin-top: 3px;" onclick="checksome(false)"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 copy-command2">
                   <span style="font-size: 18px">-q {{$cronPath}} schedule:run 2>&1 </span>
                </div>
                <div class="col-md-1">
                    <span style="font-size: 20px; pointer-events: initial; cursor: pointer;" id="copyBtn" title="{{trans('lang.verify-and-copy-command')}}" onclick="verifyPHPExecutableAndCopyCommand()"><i class="far fa-clipboard"></i></span>
                    <span style="font-size: 20px; display:none;" id="loader"><i class="fas fa-circle-notch fa-spin"></i></span>
                </div>
            </div>
        </div>

{!! Form::model($condition,['url' => 'post-scheduler', 'method' => 'PATCH','id'=>'Form']) !!}
    <div class="row">
        @foreach($jobs as $job)
        <div class="col-md-6">

            <div class="info-box">

              <span class="info-box-icon bg-info"><i class="{{$job['icon']}}"></i></span>

              <div class="info-box-content">

                <div class="row">
                    
                    <div class="col-md-6">
                        <div class="form-group mt-4">
                            {!! Form::checkbox($job['job'].'[active]',1,$job['active'],[ 'id' => $job['job'], 'onClick' => "toggleVisibilityOfCommand(this.id)"]) !!}&nbsp;
                            {!! Form::label($job['job'].'[active]', (($job['plugin_job'])) ? Lang::get($job['plugin_name'].'::lang.'.$job['job']): Lang::get('lang.'.$job['job'])) !!}
                            <a href="#" data-toggle="tooltip" title="{!! ($job['plugin_job']) ? Lang::get($job['plugin_name'].'::lang.'.$job['job_info']) : Lang::get('lang.'.$job['job_info']) !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6 mt-13" id="{{$job['job']}}-command-block" @if(!$job['active']) style="display: none;" @endif>
                        {!! Form::select($job['job'].'[value][]',$commands,$condition->getConditionValue($job['job'])['condition'],['class'=>'form-control','id'=>$job['job'].'-commands', "onchange" => "toggleVisibilityOfDailyat(this.id)", "disabled" => !$job['active']]) !!}
                        <?php $at = $condition->getConditionValue($job['job'])['at'];?>
                        <div id="{{$job['job']}}-daily-at" @if($at == "") style="display: none;" @endif>
                            {!! Form::text($job['job'].'[value][]',$condition->getConditionValue($job['job'])['at'],['class'=>'form-control time-picker', "id" => $job['job'].'-timer', "placeholder" => "HH:MM", "disabled" => ($at == "")? true :false ]) !!}
                        </div>
                    </div>
                </div>
              </div>
              <!-- /.info-box-content -->
            </div>
        </div>
        @endforeach
    </div>
    </div>
    <div class="card-footer">
        <div id="clearClipBoard" style="display: none;" onclick="copyToClipboard()"></div>
        <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fas fa-circle-notch fa-spin'>&nbsp;</i> {!! Lang::get('lang.saving') !!}"><i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
    </div>
{!! Form::close() !!}
</div>
<script src="{{asset('js','moment')}}" type="text/javascript"></script>
<script src="{{assetLink('js','bootstrap-datetimepicker4')}}" type="text/javascript">
    </script>
<script type="text/javascript">
    function toggleVisibilityOfCommand(id)
    {
        $("#"+id+"-command-block").css('display', 'none');
        $("#"+id+"-commands").prop("disabled", true);
        $("#"+id+"-commands").prop("required", false);
        $("#"+id+"-timer").prop("disabled", true);
        if($("#"+id).prop("checked") == true) {
            $("#"+id+"-commands").prop("disabled", false);
            $("#"+id+"-commands").prop("required", true);
            $("#"+id+"-daily-at").css("display", "none");
            $("#"+id+"-commands").val("");
            $("#"+id+"-command-block").css('display', 'block');
        }
    }

    function toggleVisibilityOfDailyat(id)
    {
        $trimmedID = id.replace("-commands", "");
        $("#"+$trimmedID+"-timer").prop("disabled", true);
        $("#"+$trimmedID+"-timer").prop("required", false);
        $("#"+$trimmedID+"-daily-at").css("display", "none");
        if ($("#"+id).val() == 'dailyAt') {
            $("#"+$trimmedID+"-timer").prop("disabled", false);
            $("#"+$trimmedID+"-timer").prop("required", true);
            $("#"+$trimmedID+"-daily-at").css("display", "block");
        } else if ($("#"+id).val() == '') {
            $("#"+id).prop("required", true);
        }
    }

    $(".time-picker").datetimepicker({
        format: 'HH:ss',
        useCurrent: false, //Important! See issue #1075
    });

    function checksome(showtext = true)
    {
        if (!showtext) {
            $("#phpExecutableList").css('display', "block");
            $("#phpExecutableList").val(0)
            $("#phpExecutableTextArea").css('display', "none");
        } else if($("#phpExecutableList").val() == 'Other') {
            $("#phpExecutableList").css('display', "none");
            $("#phpExecutableTextArea").css('display', "block");
        }
    }

    function verifyPHPExecutableAndCopyCommand()
    {
        copy = false;
        var path = ($("#phpExecutableList").val()=="Other")? $("#phpExecutableText").val(): $("#phpExecutableList").val();
        var text = "* * * * * "+path.trim()+" "+$(".copy-command2").text().trim();
        copyToClipboard(text);

        $.ajax({
            'method': 'POST',
            'url': "{{route('verify-cron')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "path": path
            },
            beforeSend: function() {
                $("#loader").css("display", "block");
                $(".alert-danger, .alert-success, #copyBtn").css('display', 'none');
            },
            success: function (result,status,xhr) {
                $(".alert-success-message").html("{{trans('lang.cron-command-copied')}} "+result.message);
                $(".cron-success, #copyBtn").css('display', 'block');
                $("#loader").css("display", "none");
                copy = true
            },
            error: function(xhr,status,error) {
                $('#clearClipBoard').click();
                $(".cron-danger, #copyBtn").css('display', 'block');
                $("#loader").css("display", "none");
                $(".alert-danger-message").html("{{trans('lang.cron-command-not-copied')}} "+xhr.responseJSON.message);
            },
        });
    }

    function activatTab2() {
        $('#tab2').addClass('active');
        $('#tab1').removeClass('active');
    }

    function copyToClipboard(text = " ")
    {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
        } catch (err) {
        }
        console.log(msg);
        document.body.removeChild(textArea);
    }
</script>
