<?php
$request = Request::segments();
?>
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

<script type="text/javascript">
    oTable = myFunction();
    function myFunction()
    {
        return jQuery('#chumper').dataTable({
            "sPaginationType": "full_numbers",
            "sDom": "<'row'<'col-sm-6'><'col-sm-6'>r>"+
                        "t"+
                        "<'row'<'col-sm-6'><'col-sm-6'>>",
            "bProcessing": true,
            "bServerSide": true,
            "displayLength": 100,
            "columnDefs": [
                { "visible": false, "targets": 3 },
                { "orderable": false, "targets": [0,1,2]},
            ],
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
            },
            columns:[
                    {data:'type',name:'type'},
                    {data:'description',name:'description'},
                    {data:'action',name:'action'},
                    {data:'template_category',name:'template_category'}
                     ],
            "order": [[ 3, 'desc' ]],
            "ajax": {
                url: "{{route('get-sms-template-type', $request[2])}}",
            },
            "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
 
            api.column(3, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="5" style="background:#DDDDDD"><h3 class="card-title">'+group+'</h3></td></tr>'
                    );

                    last = group;
                }
            } );
        }
        });
    }

    oTable.on( 'click', 'tr.group', function () {
        var currentOrder = table.order()[0];
        if ( currentOrder[0] === 3 && currentOrder[1] === 'asc' ) {
            table.order( [ 3, 'desc' ] ).draw();
        }
        else {
            table.order( [ 3, 'asc' ] ).draw();
        }
    } );
</script>