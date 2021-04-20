<button class="btn btn-primary pull-right" id="agent_export_submit">{!! Lang::get('lang.export') !!}</button>
@push('scripts')
<script type="text/javascript">
    $("#agent_export_submit").on('click', function () {
        var data = $("#filter").serialize();
        $.ajax({
            url: "{{url('report/agent/export')}}",
            data: data,
            type: "post",
            beforeSend: function() {
                $('.loader1').css('display','block');
            },
            success: function (data) {
                $('.loader1').css('display','none');
                if (data === 'success') {
                    window.location = "{{url('report/download/csv')}}";
                }else{
                    alert(data);
                }
            }
        });
    });
   
</script>
@endpush
