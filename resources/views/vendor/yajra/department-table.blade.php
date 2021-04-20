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
            "oLanguage": {
                    "sLengthMenu": "_MENU_ Records per page",
                    "sSearch"    : "Search: ",
                    "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink("image","gifloader3") !!}">'
                },
                columns:[
                    {data:'name', name:'name', orderable: true},
                    {data:'type', name:'type', orderable: true},
                    {data:'user_name', name:'user_name', orderable: true},
                    {data:'action', name:'action'},
                ],
                "fnDrawCallback": function( oSettings ) {
                    $('.loader').css('display', 'none');
                    $(".box-body").css({"opacity": "1"});
                    $('#blur-bg').css({"opacity": "1", "z-index": "99999"});
                },
                "fnPreDrawCallback": function(oSettings, json) {
                    $('.loader').css('display', 'block');
                    $(".box-body").css({"opacity":"0.3"});
                },
            "ajax": {
                url: "{{route('get-department-table-data')}}",
            },
            "columnDefs": [
                { "orderable": false, "targets": [1]},
                { "searchable": false, "targets": [1] },
            ]
        });
    }
</script>