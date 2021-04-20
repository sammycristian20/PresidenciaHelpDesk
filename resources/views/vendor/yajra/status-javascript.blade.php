<script type="text/javascript">
    jQuery(document).ready(function () {
        oTable = myFunction();
    });
    function myFunction()
    {
        return jQuery('#chumper').dataTable({
            "sPaginationType": "full_numbers",
            "bProcessing": true,
            "bServerSide": true,
            "bStateSave" : true,
            "oLanguage": {
                    "sLengthMenu": "_MENU_ Records per page",
                    "sSearch"    : "Search: ",
                    "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink("image","gifloader3") !!}">'
                },
            "columnDefs": [
                    { "searchable": false, "targets": [5,6] },
                    { "orderable": false, "targets": [5,6] }
                ],
                columns:[
                    {'name':'name','data':'name'},
                    {'name': 'visibility_for_client', 'data': 'visibility_for_client'},
                    {'name': 'type.name', 'data': 'type.name'},
                    {'name': 'send_email', 'data': 'send_email'},
                    {'name': 'order', 'data': 'order'},
                    {'name': 'icon', 'data': 'icon'},
                    {'name': 'action', 'data': 'action'}                    
                ],
                "fnDrawCallback": function( oSettings ) {
                    $('.loader1').css('display', 'none');
                    $(".box-body").css({"opacity": "1"});
                    $('#blur-bg').css({"opacity": "1", "z-index": "99999"});
                },
                "fnPreDrawCallback": function(oSettings, json) {
                    $('.loader1').css('display', 'block');
                    $(".box-body").css({"opacity":"0.3"});
                },
            "ajax": {
                url: "{{route('get-status-table')}}",
            }
        });
    }
</script>