<script>
    $(document).ready(function () {
        var period = 'week';
        var chart = 'doughnut';
        var department = $("#department").val();
        $('.period').click(function () {
            period = $(this).attr("value");
            department = $("#department").val();
            all(period,department);
            source(chart, period,department);
            priority(chart, period,department);
            sla(chart, period,department);
        });
        $('#department').on('change',function () {
            period = $(this).attr("value");
            department = $("#department").val();
            all(period,department);
            source(chart, period,department);
            priority(chart, period,department);
            sla(chart, period,department);
        });
        all(period,department);
        source(chart, period,department);
        priority(chart, period,department);
        sla(chart, period,department);

    });
    function all(period, department,chart = 'line',start="",end=""){
            $.ajax({
                type: "get",
                dataType: "json",
                url: "{{url('report/department/status')}}",
                data: {'chart': chart, 'category': period,'start':start,'end':end,'department':department},
                beforeSend: function () {
                    $('.loader1').css('display','block');
                    $("#all").hide();
                    $("#loading-all").show();
                },
                complete: function () {
                    $('.loader1').css('display','none');
                    $("#loading-all").hide();
                    $("#all").show();
                },
                success: function (data) {
                    console.log(data);
                    var ctx = $("#all").get(0).getContext("2d");
                    new Chart(ctx, {
                        type: chart,
                        data: data,
                        options: {
                            title: {
                                display: true,
                                text: department+' Tickets'
                            },
                            scales: {
                                yAxes: [{
                                        stacked: false
                                    }]

                            }
                        }
                    });
                }
            });
        }
        function source(chart, period,department,start="",end="") {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{url('report/department/source')}}",
                data: {'chart': chart, 'category': period,'start':start,'end':end,'department':department},
                beforeSend: function () {
                    $('.loader1').css('display','block');
                    $("#department-source").hide();
                    $("#loading-source").show();
                },
                complete: function () {
                    $('.loader1').css('display','none');
                    $("#loading-source").hide();
                    $("#department-source").show();
                },
                success: function (data) {
                    var ctx = $("#department-source").get(0).getContext("2d");
                    new Chart(ctx, {
                        type: chart,
                        data: data,
                        options: {
                            title: {
                                display: true,
                                text: 'Created tickets split by source'
                            }

                        }
                    });
                }
            });
        }
        function priority(chart, period,department,start="",end="") {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{url('report/department/priority')}}",
                data: {'chart': chart, 'category': period,'start':start,'end':end,'department':department},
                beforeSend: function () {
                    $('.loader1').css('display','block');
                    $("#department-priority").hide();
                    $("#loading-priority").show();
                },
                complete: function () {
                    $('.loader1').css('display','none');
                    $("#loading-priority").hide();
                    $("#department-priority").show();
                },
                success: function (data) {
                    var ctx = $("#department-priority").get(0).getContext("2d");
                    new Chart(ctx, {
                        type: chart,
                        data: data,
                        options: {
                            title: {
                                display: true,
                                text: 'Created tickets split by priority'
                            }

                        }
                    });
                }
            });
        }
        function sla(chart, period,department,start="",end="") {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{url('report/department/sla')}}",
                data: {'chart': chart, 'category': period,'start':start,'end':end,'department':department},
                beforeSend: function () {
                    $('.loader1').css('display','block');
                    $("#department-sla").hide();
                    $("#loading-sla").show();
                },
                complete: function () {
                    $("#loading-sla").hide();
                    $("#department-sla").show();
                },
                success: function (data) {
                    $('.loader1').css('display','none');
                    var ctx = $("#department-sla").get(0).getContext("2d");
                    new Chart(ctx, {
                        type: chart,
                        data: data,
                        options: {
                            title: {
                                display: true,
                                text: 'Created tickets split by SLA'
                            }

                        }
                    });
                }
            });
        }
        $("#send").on('click',function(){
            var data = $("#mail-form").serialize();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{url('report/department/mail')}}",
                data: data,
                beforeSend: function () {
                    $('.loader1').css('display','block');
                    $("#mail-form").hide();
                    $("#loading-mail").show();
                },
                success: function(json) {
                    $('.loader1').css('display','none');
                    $("#loading-mail").hide();
                            $("#mail-form").show();
                            $("#mail-response").html("<div class='alert alert-success'><ul>" + json[0] + "</ul></div>");
                    },
                
                error: function(json) {
                    $("#loading-mail").hide();
                            $("#mail-form").show();
                            var res = "";
                            $.each(json.responseJSON, function (idx, topic) {
                            res += "<li>" + topic + "</li>";
                            });
                            $("#mail-response").html("<div class='alert alert-danger'><strong>Whoops!</strong> There were some problems with your input.<br><br><ul>" + res + "</ul></div>");
                    }
            });
        });
    
</script>
