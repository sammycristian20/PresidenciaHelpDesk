<script>
    $(document).ready(function () {
    var period = 'week';
            var chart = 'doughnut';
            var agent = $("#agent").val();
            $('.period').click(function () {
    period = $(this).attr("value");
            agent = $("#agent").val();
            all(period, agent);
    });
            $('#agent').on('change', function () {
    period = $(this).attr("value");
            agent = $("#agent").val();
            all(period, agent);
    });
            all(period, agent);
    });
            function all(period, agent, chart = 'line', start = "", end = ""){
            $.ajax({
            type: "get",
                    dataType: "json",
                    url: "{{url('report/agent/status')}}",
                    data: {'chart': chart, 'category': period, 'start':start, 'end':end, 'agent':agent},
                    beforeSend: function () {
                        $('.loader1').css('display','block');
                    $("#all").hide();
                            $('.loader1').css('display','block');
                            $("#loading-all").show();
                    },
                    complete: function () {
                        $('.loader1').css('display','none');
                    $("#loading-all").hide();
                            $('.loader1').css('display','none');
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
                                            text: agent + ' Tickets'
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
    function source(chart, period, start = "", end = "") {
    $.ajax({
    type: "GET",
            dataType: "json",
            url: "{{url('report/source')}}",
            data: {'chart': chart, 'category': period, 'start':start, 'end':end},
            beforeSend: function () {
                $('.loader1').css('display','block');
            $("#source").hide();
                    $("#loading-source").show();
            },
            complete: function () {
            $("#loading-source").hide();
                    $("#source").show();
            },
            success: function (data) {
                $('.loader1').css('display','none');
            var ctx = $("#source").get(0).getContext("2d");
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
    function priority(chart, period, start = "", end = "") {
    $.ajax({
    type: "GET",
            dataType: "json",
            url: "{{url('report/priority')}}",
            data: {'chart': chart, 'category': period, 'start':start, 'end':end},
            beforeSend: function () {
                $('.loader1').css('display','block');
            $("#priority").hide();
                    $("#loading-priority").show();
            },
            complete: function () {
                $('.loader1').css('display','none');
            $("#loading-priority").hide();
                    $("#priority").show();
            },
            success: function (data) {
            var ctx = $("#priority").get(0).getContext("2d");
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
    function sla(chart, period, start = "", end = "") {
    $.ajax({
    type: "GET",
            dataType: "json",
            url: "{{url('report/sla')}}",
            data: {'chart': chart, 'category': period, 'start':start, 'end':end},
            beforeSend: function () {
                $('.loader1').css('display','block');
            $("#sla").hide();
                    $("#loading-sla").show();
            },
            complete: function () {
                $('.loader1').css('display','none');
            $("#loading-sla").hide();
                    $("#sla").show();
            },
            success: function (data) {
            var ctx = $("#sla").get(0).getContext("2d");
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

    $("#send").on('click', function(){
    var data = $("#mail-form").serialize();
            $.ajax({
            type: "POST",
                    dataType: "html",
                    url: "{{url('report/agent/mail')}}",
                    data: data,
                    beforeSend: function () {
                    $("#mail-form").hide();
                            $("#loading-mail").show();
                    },
                    success: function(json) {
                        console.log(json[0]);
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
