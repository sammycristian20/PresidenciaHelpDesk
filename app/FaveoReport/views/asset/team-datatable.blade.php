<script>
$(function() {
    $('#chumper').DataTable({
        scrollX: true,
        processing: true,
        serverSide: true,
        ajax: "{!! route('report.team.datatable') !!}",
        
        
    });
});
</script>