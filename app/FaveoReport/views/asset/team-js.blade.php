<script>
    $(document).ready(function () {
    var period = 'week';
            var chart = 'doughnut';
            var team = $("#team").val();
            $('.period').click(function () {
    period = $(this).attr("value");
            team = $("#team").val();
            all(period, team);
            source(chart, period, team);
            priority(chart, period, team);
            sla(chart, period, team);
    });
            $('#team').on('change', function () {
    period = $(this).attr("value");
            team = $("#team").val();
            all(period, team);
            source(chart, period, team);
            priority(chart, period, team);
            sla(chart, period, team);
    });
            all(period, team);
            source(chart, period, team);
            priority(chart, period, team);
            sla(chart, period, team);
    });
            function all(period, team, chart = 'line', start = "", end = ""){
            $.ajax({
            type: "get",
                    dataType: "json",
                    url: "{{url('report/team/status')}}",
                    data: {'chart': chart, 'category': period, 'start':start, 'end':end, 'team':team},
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
                                            text: team + ' Tickets'
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
    function source(chart, period, team, start = "", end = "") {
    $.ajax({
    type: "GET",
            dataType: "json",
            url: "{{url('report/team/source')}}",
            data: {'chart': chart, 'category': period, 'start':start, 'end':end, 'team':team},
            beforeSend: function () {
                $('.loader1').css('display','block');
            $("#team-source").hide();
                    $("#loading-source").show();
            },
            complete: function () {
                $('.loader1').css('display','none');
            $("#loading-source").hide();
                    $("#team-source").show();
            },
            success: function (data) {
            var ctx = $("#team-source").get(0).getContext("2d");
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
    function priority(chart, period, team, start = "", end = "") {
    $.ajax({
    type: "GET",
            dataType: "json",
            url: "{{url('report/team/priority')}}",
            data: {'chart': chart, 'category': period, 'start':start, 'end':end, 'team':team},
            beforeSend: function () {
                $('.loader1').css('display','block');
            $("#team-priority").hide();
                    $("#loading-priority").show();
            },
            complete: function () {
                $('.loader1').css('display','none');
            $("#loading-priority").hide();
                    $("#team-priority").show();
            },
            success: function (data) {
            var ctx = $("#team-priority").get(0).getContext("2d");
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
    function sla(chart, period, team, start = "", end = "") {
    $.ajax({
    type: "GET",
            dataType: "json",
            url: "{{url('report/team/sla')}}",
            data: {'chart': chart, 'category': period, 'start':start, 'end':end, 'team':team},
            beforeSend: function () {
                $('.loader1').css('display','block');
            $("#team-sla").hide();
                    $("#loading-sla").show();
            },
            complete: function () {
                $('.loader1').css('display','none');
            $("#loading-sla").hide();
                    $("#team-sla").show();
            },
            success: function (data) {
            var ctx = $("#team-sla").get(0).getContext("2d");
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
                    dataType: "json",
                    url: "{{url('report/team/mail')}}",
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
