<link href="{{assetLink('css','daterangepicker')}}" media="none" onload="this.media='all';" rel="stylesheet" type="text/css"/>
<link href="{{assetLink('css','bootstrap-switch')}}" rel="stylesheet"/>
<link href="{{assetLink('css','select2')}}" media="none" onload="this.media='all';" rel="stylesheet" type="text/css"/>
<style>
    #reportrange>span, #updatedDate>span{
       letter-spacing: 2px;
    }
    .col-md-4>label{
        margin-top: 6px;
    }
</style>
<div id="filter-box" style="display: none;">
    <br>
        <div class="box box-primary">
            <form id="filter">
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('report::lang.show-inactive') !!}
                                <input id="inactive" name="inactive" type="checkbox" value="yes">
                                </input>
                            </label>
                            <a data-toggle="tooltip" href="#" title="{!! Lang::get('report::lang.show-inactive-tooltip') !!}">
                                <i class="fa fa-question-circle" style="padding: 0px;">
                                </i>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('report::lang.created-date-range') !!}
                            </label>
                            <br>
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar">
                                    </i>
                                    <span>
                                    </span>
                                    <b class="caret">
                                    </b>
                                </div>
                                <input id="start_date" name="start_date" type="hidden"/>
                                <input id="end_date" name="end_date" type="hidden"/>
                            </br>
                        </div>
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('report::lang.updated-date-range') !!}
                            </label>
                            <br>
                                <div id="updatedDate" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar">
                                    </i>
                                    <span>
                                    </span>
                                    <b class="caret">
                                    </b>
                                </div>
                                <input id="updateStart" name="update_start" type="hidden"/>
                                <input id="updateEnd" name="update_end" type="hidden"/>
                            </br>
                        </div>
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.agents') !!}
                            </label>
                            <br>
                                <select class="agents-ajax form-control" id="agents" multiple="multiple" name="agents[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.departments') !!}
                            </label>
                            <br>
                                <select class="departments-ajax form-control" id="departments" multiple="multiple" name="departments[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.source') !!}
                            </label>
                            <br>
                                <select class="sources-ajax form-control" id="sources" multiple="multiple" name="sources[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.priority') !!}
                            </label>
                            <br>
                                <select class="priorities-ajax form-control" id="priorities" multiple="multiple" name="priorities[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.client') !!}
                            </label>
                            <br>
                                <select class="clients-ajax form-control" id="clients" multiple="multiple" name="clients[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.type') !!}
                            </label>
                            <br>
                                <select class="types-ajax form-control" id="types" multiple="multiple" name="types[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.status') !!}
                            </label>
                            <br>
                                <select class="status-ajax form-control" id="status" multiple="multiple" name="status[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.helptopic') !!}
                            </label>
                            <br>
                                <select class="helptopic-ajax form-control" id="helptopic" multiple="multiple" name="helptopic[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.team') !!}
                            </label>
                            <br>
                                <select class="team-ajax form-control" id="team" multiple="multiple" name="team[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                        <div class="col-md-4">
                            <label>
                                {!! Lang::get('lang.creator') !!}
                            </label>
                            <br>
                                <select class="creator-ajax form-control" id="creator" multiple="multiple" name="creator[]" style="width: 100%">
                                </select>
                            </br>
                        </div>
                    </div>
                </div>
            </form>
            <div class="box-footer">
                <button class="btn btn-primary" id="submit-filter">
                    <i class="fa fa-refresh">
                    </i>
                    {!! Lang::get('report::lang.generate') !!}
                </button>
                <button class="btn btn-danger" id="clear-filter">
                    <i class="fa fa-trash">
                    </i>
                    {!! Lang::get('report::lang.clear-filter') !!}
                </button>
                <button class="btn btn-default pull-right" id="close">
                    <i class="fa fa-times">
                    </i>
                    {!! Lang::get('report::lang.close') !!}
                </button>
            </div>
        </div>
    </br>
</div>
@push('scripts')
<script src="{{assetLink('js','daterangepicker')}}" type="text/javascript"></script>

<script src="{{assetLink('js','bootstrap-switch')}}"></script>

<script src="{{assetLink('js','moment')}}" type="text/javascript"></script>

<script src="{{assetLink('js','select2')}}" type="text/javascript"></script>


<script>
    function select1(api){
        $("." + api + "-ajax").select2({
            ajax: {
                url: "{{url('report/api/get')}}" + '/' + api,
                dataType: 'json',
                data: function(params) {
                    return {
                        term: $.trim(params.term),
                        inactive: $('#inactive:checked').val()
                    };
                },
                 processResults: function(data) {
                    return{
                     results: $.map(data, function (value) {
                        return {
                            image:value.profile_pic,
                            text:value.first_name+" "+value.last_name,
                            id:value.id,
                            email:value.email,
                        }
                    })
                   }
                },
                cache: true
            },
            templateResult: formatState,
            allowClear: true,
        });
    }
    function formatState (state) { 
       var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
    }
    function select2(api){
        $("." + api + "-ajax").select2({        
            ajax: {
                url: "{{url('report/api/get')}}" + '/' + api,
                dataType: 'json',
                data: function(params) {
                    return {
                        term: $.trim(params.term),
                        inactive: $('#inactive:checked').val()
                    };
                },
                 processResults: function(data) {
                    return{
                     results: $.map(data, function (value) {
                        return {
                            image:value.profile_pic,
                            text:value.first_name+" "+value.last_name,
                            id:value.id,
                            email:value.email,
                        }
                    })
                   }
                },
                cache: true
            },
            templateResult: formatState,
            allowClear: true,
        });
    }
    function formatState (state) { 
       var $state = $( '<div><div style="width: 10%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 86%;display: inline-block;margin-left:10px;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
    }
    function select(api) {
        $("." + api + "-ajax").select2({
            ajax: {
                url: "{{url('report/api/get')}}" + '/' + api,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        inactive: $('#inactive:checked').val()
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            allowClear: true,
        });
    }
    function showhidefilter()
    {
        if ($('#filter-box').is(':hidden')) {
            $('#filter-box').slideDown();
        } else {
            $('#filter-box').slideUp();
        }
    }
    $(function () {
        $('#inactive').bootstrapSwitch({
            size: 'small',
            onColor: 'success',
            offColor: 'danger'
        });

        select1('agents');
        select('departments');
        select2('clients');
        select('sources');
        select('priorities');
        select('types');
        select('status');
        select('helptopic');
        select('team');
        select2('creator');

        var start = moment().subtract(30, 'days');
        var end = moment();

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
        }

        function setUpdateDateRange(start, end) {
            $('#updatedDate span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#updateStart').val(start.format('YYYY-MM-DD'));
            $('#updateEnd').val(end.format('YYYY-MM-DD'));
        }

        var dateRangeConfig = {
            endDate: end,
            maxDate: moment(),
            dateLimit: {
                "months": {{ $maxDateRange }}
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        };

        $('#reportrange').daterangepicker(dateRangeConfig, cb);
        $('#reportrange').data('daterangepicker').setStartDate(start);
        cb(start, end);

        $('#updatedDate').daterangepicker(dateRangeConfig, setUpdateDateRange);
        $('#updatedDate').data('daterangepicker').setStartDate(moment().startOf('month'));

        $("#close").on('click', function () {
            showhidefilter();
        });

        $('#submit-filter').click(function() {
            if ($('#reportrange span').html() == '' && $('#updatedDate span').html() == '') {
                cb(start, end);
            }
            showhidefilter();
        });

        $('#clear-filter').click(function() {
            $('#filter-box [class*=-ajax]').val(null).trigger('change');
            $('#reportrange span,#updatedDate span').html('');
            $('#start_date,#updateStart').val('');
            $('#end_date,#updateEnd').val('');
            $('#reportrange,#updatedDate').data('daterangepicker').setStartDate(moment().startOf('month'));
        });
    });
</script>
@endpush
