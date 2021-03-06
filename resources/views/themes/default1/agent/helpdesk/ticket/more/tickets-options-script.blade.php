<!--select 2-->
<script src="{{assetLink('js','select2')}}"></script>
<script>
var filterClick = 0;
            var clearlist = 0;
            var t_id = [];
            var submit_form = 0;
            var c_status = '';
            var option = null;
            $(function () {
                //Enable check and uncheck all functionality
                $(".checkbox-toggle").click(function () {
                    var clicks = $(this).data('clicks');
                    if (clicks) {
                    //Uncheck all checkboxes
                        $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
                        $(".far", this).removeClass("fa-check-square").addClass('fa-square');
                    } else {
                    //Check all checkboxes
                        $(".mailbox-messages input[type='checkbox']").iCheck("check");
                        $(".far", this).removeClass("fa-square").addClass('fa-check-square');
                    }
                    $(this).data("clicks", !clicks);
                });
            });
            $(function () {
                // Enable check and uncheck all functionality
                $(".checkbox-toggle").click(function () {
                    var clicks = $(this).data('clicks');
                    // alert(clicks);
                    if (clicks) {
                    //Uncheck all checkboxes
                        $("input[type='checkbox']", ".mailbox-messages").iCheck("uncheck");
                        // alert($("input[type='checkbox']").val());
                        t_id = $('.selectval').map(function () {
                            return $(this).val();
                        }).get();
                        @if ($loggedInUser->has('assign_ticket'))
                        showAssign(t_id);
                        @endif
                        // alert(checkboxValues);
                    } else {
                    //Check all checkboxes
                        $("input[type='checkbox']", ".mailbox-messages").iCheck("check");
                        // alert('Hallo');
                        t_id = [];
                        @if ($loggedInUser->has('assign_ticket'))
                        showAssign(t_id);
                        @endif
                    }
                    $(this).data("clicks", !clicks);
                });
            });

            function getValues() {
                return t_id;
            }

            $(".closemodal, .no").click(function () {
                $("#myModal").css("display", "none");
            });
            
            $(".closemodal, .no").click(function () {
                $("#myModal").css("display", "none");
            });
            
            $('.yes').click(function () {
                var values = getValues();
                if (values == "") {
                    $("#myModal").css("display", "none");
                } else {
                    if (c_status != 'hard-delete'){
                        var url = '{{url("ticket/change-status/")}}/' + values + '/' + c_status;
                        console.log($(this).serialize());
                        $.ajax({
                            type: "post",
                            url: url,
                            dataType: "html",
                            // data: { "_token": "{{ csrf_token() }}"},

                             data: $(this).serialize(),
                            beforeSend: function() {
                                $('.loader1').css('display','block');
                                $('.loader').css('display','block');
                                $('#d1').prop('disabled', true);
                                $("#hidespin").hide();
                                $("#spin").show();
                                $("#hide2").hide();
                                $("#show2").show();
                                $("#custom-alert-loader").show();
                                $("#custom-alert-body").hide();
                            },
                            success: function(response) {
                                $("#myModal").css("display", "none");
                                $('.loader1').css('display','none');
                                $('.loader').css('display','none');
                                $('#d1').prop('disabled', false);
                                $("#hide2").show();
                                $("#show2").hide();
                                $("#hidespin").show();
                                $("#spin").hide();
                                var message = "{!! Lang::get('lang.status-changed-successfully') !!} {!! Lang::get('lang.reload-be-patient-message') !!}";
                                $(".success-message, .success-msg, .get-success, #get-success").html(message);
                                $(".alert-success").show();
                                window.scrollTo(0, 0);
                                setTimeout(function(){
                                    location.reload();
                                }, 3000)
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                $("#myModal").css("display", "none");
                                $('.loader1').css('display','none');
                                $('.loader').css('display','none');
                                $('#d1').prop('disabled', false);
                                $("#hide2").show();
                                $("#show2").hide();
                                $("#hidespin").show();
                                $("#spin").hide();
                                var message = JSON.parse(xhr.responseText);
                                window.scrollTo(0, 0);
                                $(".error-message, #get-danger").html(message.message);
                                $(".alert-danger").show();
                            }
                        })
                        return false;
                    } else {
                        $("#modalpopup").unbind('submit');
                        submit_form = 1;
                        $('#hard-delete').click();
                    }
                }
            });
            
            function changeStatus(id, name) {
                $('#myModalLabel').html('{{Lang::get("lang.change-ticket-status-to")}}' + name);
                var msg = "{{Lang::get('lang.confirm-to-proceed')}}";
                var values = getValues();
                if (values == "") {
                    msg = "{{Lang::get('lang.select-ticket')}}";
                    $('.yes').html("{{Lang::get('lang.ok')}}");
                    $('#myModalLabel').html("{{Lang::get('lang.alert')}}");
                } else {
                    c_status = id;
                    $('.yes').html("Yes");
                }
                $('#custom-alert-body').html(msg);
                $('#custom-alert-loader').hide();
                $('#custom-alert-body').show();
                $("#myModal").css("display", "block");
            }

            $('#modalpopup').on('submit', function(e){
                if (submit_form == 0) {
                    e.preventDefault();
                    changeStatus('hard-delete', '{{Lang::get("lang.clean-")}}');
                }
                $('#hard-delete').val('Delete forever')
            });

            function someFunction(id) {
                if (document.getElementById(id).checked) {
                    t_id.push(id);
                    // alert(t_id);
                } else if (document.getElementById(id).checked === undefined) {
                    var index = t_id.indexOf(id);
                    if (index === - 1) {
                        t_id.push(id);
                    } else {
                        t_id.splice(index, 1);
                    }
                } else {
                    var index = t_id.indexOf(id);
                    t_id.splice(index, 1);
                    // alert(t_id);
                }
                @if ($loggedInUser->has('assign_ticket'))
                    showAssign(t_id);
                @endif
            }

            function showAssign(t_id) {
                if (t_id.length >= 1) {
                    $('#assign_Ticket').css('display', 'inline');
                } else {
                    $('#assign_Ticket').css('display', 'none');
                }
            }

            $(document).on("click", "#assign_Ticket", function () {
                var t_id = "";
                $("input[name='select_all[]']:checked:enabled").each(function() {
                    t_id = $(this).val() + "," + t_id;
                });
                $('#assign').select2({
                    maximumSelectionLength: 1,
                    minimumInputLength: 0,
                    language: {
                        searching: function() {
                            return "{{trans('lang.loading_data')}}";
                        },
                        errorLoading: function() {
                            return "{{trans('lang.searching')}}";
                        }
                    },
                    ajax: {
                        url: "{{ route('get.assigntickets') }}",
                        dataType: 'json',
                        data: function(params) { 
                            return {
                                ticket_id: t_id,
                                "search-query": $.trim(params.term),
                            }
                        },
                        processResults: function(data) {
                            return{
                                results: $.map(data.data.agents, function (value) {
                                    return {
                                        image:value.profile_pic,
                                        text:value.name,
                                        id:"user_"+value.id,
                                        email:value.email,
                                    }
                                })
                            }
                        },
                        cache: true
                    },
                    templateResult: formatState,
                });
            });
            function formatState (state) {
                if(state.loading) return state.text;
                 var onerrorImage = "'<?=assetLink('image','contacthead') ?>'";
                var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+'  onerror="this.src='+onerrorImage+'" width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;margin-left:5px;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
                return $state;
            }
            
        $(document).ready(function () {
            //checking merging tickets
            $('#MergeTickets').on('show.bs.modal', function () {

                // alert("hi");
                $.ajax({
                    type: "GET",
                    url: "{{route('check.merge.tickets',0)}}",
                    dataType: "html",
                    data: {data1: t_id},
                    beforeSend: function () {
                        $('.loader1').css('display','block');
                        $('.loader').css('display','block');
                        $("#merge_body").hide();
                        $("#merge_loader").show();
                    },
                    success: function (response) {
                        $('.loader1').css('display','none');
                        $('.loader').css('display','none');
                        if (response == 0) {
                            $("#merge_body").show();
                            $("#merge-succ-alert").hide();
                            $("#merge-body-alert").show();
                            $("#merge-body-form").hide();
                            $("#merge_loader").hide();
                            $("#merge-btn").attr('disabled', true);
                            var message = "{{Lang::get('lang.select-tickets-to merge')}}";
                            $("#merge-err-alert").show();
                            $('#message-merge-err').html(message);
                        } else if (response == 2) {
                            $("#merge_body").show();
                            $("#merge-succ-alert").hide();
                            $("#merge-body-alert").show();
                            $("#merge-body-form").hide();
                            $("#merge_loader").hide();
                            $("#merge-btn").attr('disabled', true);
                            var message = "{{Lang::get('lang.different-users')}}";
                            $("#merge-err-alert").show();
                            $('#message-merge-err').html(message);
                        } else {
                            $.ajax({
                                url: "{{ url('api/agent/tickets/get-merge-tickets') }}",
                                dataType: "json",
                                data: {"ticket-ids": t_id},
                                beforeSend: function(){
                                    $('.loader1').css('display','block');
                                    $('.loader').css('display','block');
                                    $("#select-merge-parent").html('');
                                },
                                success: function (data) {
                                    $('.loader1').css('display','none');
                                    $('.loader').css('display','none');
                                    $("#merge_body").show();
                                    $("#merge-body-alert").hide();
                                    $("#merge-body-form").show();
                                    $("#merge_loader").hide();
                                    $("#merge-btn").attr('disabled', false);
                                    $("#merge_loader").hide();
                                    $("#select-merge-parent").append('')
                                    data.data.forEach(function($item, $index){
                                        $option = "<option value=\""+$item.ticket_id+"\">"+$item.title+"</option";
                                        $("#select-merge-parent").append($option);
                                    });
                                }
                            });
                        }
                    }
                });
            });
            
            //submit merging form
            $('#merge-form').on('submit', function () {
                $.ajax({
                    type: "POST",
                    url: "{!! url('merge-tickets/') !!}/" + t_id,
                    dataType: "json",
                    data: $(this).serialize(),
                    beforeSend: function () {
                        $('.loader1').css('display','block');
                        $('.loader').css('display','block');
                        $("#merge_body").hide();
                        $("#merge_loader").show();
                    },
                    success: function (response) {
                        $('.loader1').css('display','none');
                        $('.loader').css('display','none');
                        if (response == 0) {
                            $("#merge_body").show();
                            $("#merge-succ-alert").hide();
                            $("#merge-body-alert").show();
                            $("#merge-body-form").hide();
                            $("#merge_loader").hide();
                            $("#merge-btn").attr('disabled', true);
                            var message = "{{Lang::get('lang.merge-error')}}";
                            $("#merge-err-alert").show();
                            $('#message-merge-err').html(message);
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            $("#merge_body").show();
                            $("#merge-err-alert").hide();
                            $("#merge-body-alert").show();
                            $("#merge-body-form").hide();
                            $("#merge_loader").hide();
                            $("#merge-btn").attr('disabled', true);
                            var message = "{{Lang::get('lang.merge-success')}}";
                            $("#merge-succ-alert").show();
                            $('#message-merge-succ').html(message);
                            setTimeout(function () {
                                $("#alert11").hide();
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function()
                    {
                        $("#merge_body").show();
                            $("#merge-succ-alert").hide();
                            $("#merge-body-alert").show();
                            $("#merge-body-form").hide();
                            $("#merge_loader").hide();
                            $("#merge-btn").attr('disabled', true);
                            var message = "{{Lang::get('lang.merge-error')}}";
                            $("#merge-err-alert").show();
                            $('#message-merge-err').html(message);
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                    }
                })
                return false;
            });

            $('#AssignTickets').on('show.bs.modal', function() {
                //select_assigen_list.val(null).trigger("change");
                $("#assign_body").hide();
                $("#assign_loader").show();
                setTimeout(function(){
                    $("#assign_body").show();
                    $("#assign_loader").hide();
                }, 2000);
            });
            
            $('#assign-form').on('submit', function() {
                var t_id = "";
                $("input[name='select_all[]']:checked:enabled").each(function() {
                    t_id = $(this).val() + "," + t_id;
                });
                //var t_id = $("input[name='select_all[]']").val();
                $.ajax({
                    type: "POST",
                    url: "{{url('ticket/assign')}}",
                    dataType: "html",
                    data: $(this).serialize() + '&ticket_id=' + t_id + '&_method=PATCH',
                    beforeSend: function() {
                        $('.loader1').css('display','block');
                        $('.loader').css('display','block');
                        $("#assign-succ-alert").hide();
                        $("#assign-err-alert").hide();
                        $("#assign_body").hide();
                        $("#assign_loader").show();
                    },
                    success: function(response) {
                        $message = JSON.parse(response);
                        console.log(response);
                        $('#message-assign-succ').html($message.message);
                        $("#assign-succ-alert").show();
                        $("#assign_body").show();
                        $("#assign_loader").hide();
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr){
                        $message = JSON.parse(xhr.responseText);
                        $('#message-assign-err').html($message.message.error);
                        $("#assign-err-alert").show();
                        $("#assign_body").show();
                        $("#assign_loader").hide();
                    }
                })
                return false;
            });
        });
</script>