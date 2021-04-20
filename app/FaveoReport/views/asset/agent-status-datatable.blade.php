<script>
$(function() {
    $('#chumper').DataTable({
        scrollX: true,
        processing: true,
        serverSide: true,
        ajax: '{!! route('report.agent.status.datatable') !!}'
        
    });
});
</script>