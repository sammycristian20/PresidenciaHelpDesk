<!-- for micro organization view -->
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.list_of_org_department')!!}</h3>



        <a href='' class="btn btn-primary pull-right" data-toggle='modal' data-target='#create-department'><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;
            {{Lang::get('lang.create_department')}}</a>



    </div>  
    <div class="box-body">
        <table id="org_dept_list" class="display table table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>{!! Lang::get('lang.organization_department') !!}</th>
                    <th>{!! Lang::get('lang.business_hours') !!}</th>
                    <th>{!! Lang::get('lang.manager') !!}</th>
                    <th>{!! Lang::get('lang.action') !!}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="micro_org_edit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <!-- //for display error massage -->
            <div class="alert alert-danger alert-dismissable print-error-msg" style="display:none">
                <i class="fa fa-ban"></i>
                <b>{!! Lang::get('lang.alert') !!} !</b>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <ul></ul>
            </div>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="titleLabel">{{Lang::get('lang.edit_department_name')}}:</h4>
            </div>

            <div class="modal-body">
                <br/>
                <form name="myForm"  method="post" role="form">
                    {{ csrf_field() }}
                    <div class="form-group" id="register">
                        <input type="hidden" name="dept_id" value="" id="dept_id">

                        {!! Form::label('New department',Lang::get('lang.new_department')) !!} <span class="text-red"> *</span>
                        <div class="input-group">
                            <input type="text" class="form-control" name="change_org_dept" required="required" value="" id="change_org_dept">
                            {!! Form::label('Business Hours',Lang::get('lang.business_hours')) !!}
                            <select name="business_hour" class="form-control" id="business_hour">

                                @foreach($businessHoursLists as $businessHoursList)
                                <option value="{!! $businessHoursList->id !!}">{!! $businessHoursList->name !!}</option>
                                @endforeach
                            </select>
                            <br/>

                            {!! Form::label('Depertment Manager',Lang::get('lang.organization_department_manager')) !!} 
                            {!! Form::select('org_dept_manager', [Lang::get('lang.organization')=>$user->pluck('email','id')->toArray()],null,['multiple'=>true,'class'=>"form-control select2 org_dept_manager",'style'=>"width:100%;",'id'=>'EditDeptManager']) !!}
                        </div>
                    </div>

            </div>

        </div>
        <div class="box-footer" style="height: 60px">
            <button type="button" class="btn btn-primary pull-left" data-dismiss="modal"> <i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.close') !!}</button>

            <button type="submit" class="btn btn-primary pull-right" data-dismiss="modal" id="editButtonModal"> <i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.submit') !!}</button>

        </div>
        </form>
    </div>
</div>

<div class="modal fade micro_org_view" id="micro_org_view" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    {!! Lang::get('lang.list_of_users') !!}
                </h4>
            </div>
            <div class="modal-body">

                <table  class="display deptlistuser table table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>{!! Lang::get('lang.user_name') !!}</th>
                            <th>{!! Lang::get('lang.email') !!}</th>

                        </tr>
                    </thead>

                </table>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-right" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.close') !!}</button>
            </div>

        </div>

    </div>

</div>

<!-- //create department -->

<div class="modal fade" id="create-department" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- //for display error massage -->
            <div class="alert alert-danger alert-dismissable print-error-msg" style="display:none">
                <i class="fa fa-ban"></i>
                <b>{!! Lang::get('lang.alert') !!} !</b>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <ul></ul>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    {!! Lang::get('lang.organization_department') !!}
                </h4>
            </div>
            <div class="modal-body">

                <form name="myForm"  role="form" id="createdeptForm">
                    {{ csrf_field() }}
                    <div class="form-group" id="register">
                        <input type="hidden" name="dept_id" value="">

                        {!! Form::label('New department',Lang::get('lang.new_department')) !!} <span class="text-red"> *</span>
                        <div class="input-group">
                            <input type="text" class="form-control" name="org_deptname" required="required" id="new_org_department">


                            {!! Form::label('Business Hours',Lang::get('lang.business_hours')) !!}
                            <select name="newbusiness_hour" class="form-control" id="newbusiness_hour">

                                @foreach($businessHoursLists as $businessHoursList)
                                <option value="{!! $businessHoursList->id !!}">{!! $businessHoursList->name !!}</option>
                                @endforeach
                            </select>
                            <br/>

                            {!! Form::label('Depertment Manager',Lang::get('lang.organization_department_manager')) !!} 
                            {!! Form::select('org_dept_manager', [Lang::get('lang.organization')=>$user->pluck('email','id')->toArray()],null,['multiple'=>true,'class'=>"form-control select2 org_dept_manager",'style'=>"width:100%;" ,'id'=>'org_dept_manager']) !!}

                        </div>
                    </div>

            </div>

        </div>
        <div class="box-footer" style="height: 60px">
            <button type="button" class="btn btn-primary pull-left" data-dismiss="modal"> <i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.close') !!}</button>

            <button type="submit" class="btn btn-primary pull-right" data-dismiss="modal" id="submitButtonModal"> <i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.submit') !!}</button>

        </div>
        </form>

    </div>
</div>




<!-- Delete Department -->

<div class="modal fade" id="deleteDept" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="titleLabel">{{Lang::get('lang.delete_department')}}</h4>
            </div>
            <form name="myForm"  role="form" >
                <div class="modal-body">
                    <br/>

                    {{ csrf_field() }}
                    <div class="form-group" id="register">
                        <input type="hidden" name="delete_dept_id" value="" id="deleteDeptId">
                        {{Lang::get('lang.are_you_sure_want_to_delete_organization_department')}}?
                    </div>

                </div>


                <div class="box-footer" style="height: 60px">
                    <button type="button" class="btn btn-primary pull-left" data-dismiss="modal"> <i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.close') !!}</button>
                    <button type="submit" class="btn btn-danger pull-right" data-dismiss="modal" id="submitFordeleteDept"> <i class="fa fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.delete') !!}</button>

                </div>
        </div>
        </form>

    </div>
</div>

<script type="text/javascript">
    // Create organization Department
    $("#submitButtonModal").click(function () {
        var deptName = $('#new_org_department').val();
        var businessHoursId = $('#newbusiness_hour').val();
        var managerId = $('#org_dept_manager').val();
        $.ajax({
            url: '{{route("create.org.dept",$orgs->id)}}',

            type: 'post',
            data: {"_token": "{{ csrf_token() }}",
                org_deptname: deptName, businessHoursId: businessHoursId, managerId: managerId},
            success: function (result) {
                if ($.isEmptyObject(result.error)) {
                    console.log(result.success);
                    $('#new_org_department').val('');
                    $('#org_dept_manager').val('');

                    $('#create-department').hide();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    $('.success-msg').html(result.success);
                    $('.alert-success').css('display', 'block');
                    setInterval(function () {
                        $('.alert-success').slideUp(3000, function () {
                        });
                    }, 500);
                    window.location.reload(true);

                } else {
                    //for reopen modalpopup
                    $('#create-department').modal({'show': true});
                    console.log(result.error);
                    $(".print-error-msg").find("ul").html('');
                    $(".print-error-msg").css('display', 'block');
                    $(".print-error-msg").attr('style', 'margin-left:18px;margin-right:18px; position: relative;top:18px;');
                    $.each(result.error, function (key, value) {
                        $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
                    });
                }
            }

        });

    });


    //Edit organization department
    $("#editButtonModal").click(function () {
        var deptId = $('#dept_id').val();
        var deptName = $('#change_org_dept').val();
        var businessHoursId = $('#business_hour').val();
        var managerId = $('#EditDeptManager').val();

        $.ajax({
            url: '{{route("org.post.changeorgdept",$orgs->id)}}',

            type: 'post',
            data: {"_token": "{{ csrf_token() }}", deptId: deptId,
                org_deptname: deptName, businessHoursId: businessHoursId, managerId: managerId},
            success: function (result) {
                console.log(result.success);
                if ($.isEmptyObject(result.error)) {
                    $('#micro_org_edit').hide();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    $('.success-msg').html(result.success);
                    $('.alert-success').css('display', 'block');
                    setInterval(function () {
                        $('.alert-success').slideUp(3000, function () {
                        });
                    }, 500);
                    window.location.reload(true);

                } else {
                    //for reopen modalpopup
                    $('#micro_org_edit').modal({'show': true});
                    console.log(result.error);
                    $(".print-error-msg").find("ul").html('');
                    $(".print-error-msg").css('display', 'block');
                    $(".print-error-msg").attr('style', 'margin-left:18px;margin-right:18px; position: relative;top:18px;');
                    $.each(result.error, function (key, value) {
                        $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
                    });
                }
            }
        });
    });



</script>
