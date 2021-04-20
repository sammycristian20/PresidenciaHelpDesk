@extends('themes.default1.agent.layout.agent')
@section('Staffs')
active
@stop

@section('staffs-bar')
active
@stop

@section('Report')
class="active"
@stop

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('report::lang.satisfaction-survey') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('content')
<style>
    .loader{
        position: fixed;
        z-index: 99;
        height: 2em;
        width: 5em;
        overflow: show;
        margin: auto;
        top: 0;
        bottom: 0;
        left: 50%;
        right: 50%;
    }
    .loader:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>
<div class="box box-primary">
    <div class="box-header">
        <button class="btn btn-primary" onclick="showhidefilter();">{!! Lang::get('report::lang.filter') !!}</button>
        @include('report::indepth.filter')
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group pull-right">
                    <button class="btn btn-default type" value="ticket">{!! Lang::get('report::lang.ticket') !!}</button>
                    <button class="btn btn-default type" value="thread">{!! Lang::get('report::lang.thread') !!}</button>
                </div>
                <canvas id="rating"></canvas>
            </div>
        </div><br>
    </div>
</div>
<div id="loader" class="loader">
    <img src="{{ url(assetLink('image','gifloader3')) }}" style="width: 50px; opacity: 0.7">
</div>
@stop
@section('FooterInclude')
<script src="{{assetLink('js','chart')}}"></script>
<script>
let ratingChart = false;
var options_bar = {
    scales: {
        xAxes: [{
            stacked: true,
            scaleLabel: {
                display: true,
                labelString: 'Rating Count',
                fontStyle:'bold',
            },
            ticks: {
                beginAtZero: true,
                min: 0,
                suggestedMin: 0,
                callback: function(value, index, values) {
                    if (Math.floor(value) === value) {
                        return value;
                    }
                }
            },
            gridLines: {
                display:false
            },
        }],
        yAxes: [{
            stacked: true,
            barThickness: 10,
            scaleLabel: {
                display: true,
                labelString: 'Rating',
                fontStyle:'bold',
            },
            gridLines: {
                display:false
            },
        }],
    },
    responsive: true,
    maintainAspectRatio: true,
};

Chart.plugins.register({
        afterDraw: function(chart) {
            var emptyChart = true;
            $.each(chart.data.datasets[0].data, function(index, el) {
                if (el > 0) emptyChart = false;
            });
            
            if (emptyChart) {
                  var ctx = chart.chart.ctx;
                  var width = chart.chart.width;
                  var height = chart.chart.height;
                  var img = $('<img/>');
                  img.attr('src', '{{ url(assetLink("image","nodatafound")) }}');
                  chart.clear();
                  ctx.save();                 
                  $(chart.canvas).siblings('.chart-filter').hide();
                  ctx.drawImage(img[0], width / 2 - img[0].width / 2, height / 2 - img[0].height / 2);
                  ctx.restore();
            } else {
                $(chart.canvas).siblings('.chart-filter').show();
            }
        }
    });

function thisAjax(data,rating_type) {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "{{url('report/rating/get')}}",
        data: data+"&rating_type="+rating_type,
        beforeSend: function() {
            $('#loader').show();
        },
        success: function (data) {
            $('#loader').hide();
            var ctx = $("#rating").get(0).getContext("2d");
            
            if (ratingChart) {
                ratingChart.data.labels = data.labels;
                ratingChart.data.datasets = data.datasets;
                ratingChart.update();
            } else {
                ratingChart = new Chart(ctx, {
                    type: 'horizontalBar',
                    data: data,
                    options: options_bar
                });
            }

        },
        error: function (error) {}
    });
}

$(function() {
    var type = 'ticket';
    var filterData = $("#filter").serialize();
    thisAjax(filterData,type);

    $("#submit-filter").click(function () {
        filterData = $("#filter").serialize();
        thisAjax(filterData,type);
    });

    $(".type").on('click',function(){
        type = $(this).attr("value");
        filterData = $("#filter").serialize();
        thisAjax(filterData,type);
    });
});
</script>
@stop

