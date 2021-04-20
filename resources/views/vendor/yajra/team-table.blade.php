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
                    "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
                },
                   columns:[
                        {'name':'name','data':'name'},
                        {'name':'status','data':'status'},
                        {'name':'count','data':'count'},
                        {'name':'user_name','data':'user_name'},
                        {'name':'action','data':'action'},
                    
                        ],
                "fnDrawCallback": function( oSettings ) {
                    $('.loader1').css('display', 'none');
                    $(".card-body").css({"opacity": "1"});
                    $('#blur-bg').css({"opacity": "1", "z-index": "99999"});
                },
                "fnPreDrawCallback": function(oSettings, json) {
                    $('.loader1').css('display', 'block');
                    $(".card-body").css({"opacity":"0.3"});
                },
            "ajax": {
                url: "{{route('get-team-table-data')}}",
            },
            "columnDefs": [
                { "orderable": false, "targets": [4]},
                { "searchable": false, "targets": [4] },
            ]
        });
    }
</script>