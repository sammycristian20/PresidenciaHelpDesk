<div class="row">
    <div class="col-sm-8 form-group {{ $errors->has('department_status') ? 'has-error' : '' }}">
        {!! Form::label('department_status',Lang::get('DepartmentStatusLink::lang.department-status')) !!}
        <a data-toggle="tooltip" href="#" title="{!! Lang::get('DepartmentStatusLink::lang.department-status-tooptip') !!}">
            <i class="fa fa-question-circle" style="padding: 0px;">
            </i>
        </a>
        {!! Form::select('department_status[]', [Lang::get('DepartmentStatusLink::lang.department-status') => $status], null, ['class' => 'form-control select2', 'id' => 'departmentStatus', 'style' => "width:100%", 'multiple' => 'true']) !!}
    </div>
</div>
<script>
    $(function () {
        $('#departmentStatus').select2({
            minimumInputLength: 1,
            ajax: {
                url: '{{route("common.dependency", "statuses")}}',
                data: function(params) {
                    return {
                        "search-query": $.trim(params.term)
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.data.statuses, function (status) {
                            return {
                                text: status.name,
                                id: status.id
                            }
                        })
                    }
                },
                cache: true
            },
            templateResult: function (data) {
                return $('<div>' + data.text + '</div>');
            }
        });
    });
</script>