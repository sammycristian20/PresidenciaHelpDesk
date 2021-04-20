<?php
$segments = \Request::segments();
$segment = "";
foreach ($segments as $seg) {
    $segment.="/".$seg;
}
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        var clicked = 1;
        var status = "Open";
        oTable = myFunction();

        function myFunction()
        {
            return jQuery('#chumper').dataTable({
                "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>"+
                        "t"+
                        "<'row'<'col-sm-6'i><'col-sm-6'p>>",
                "sPaginationType": "full_numbers",
                "bProcessing": true,
                "bServerSide": true,
                "bStateSave" : true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ Records per page",
                    "sSearch"    : "Search: ",
                    "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
                },
                "fnDrawCallback": function( oSettings ) {
                    $(".card-body").css({"opacity": "1"});
                    $('#blur-bg').css({"opacity": "1", "z-index": "99999"});
                    $('.loader1').css('display', 'none');
                    $('.loader').css('display', 'none');
                    t_id = [];
                    showAssign(t_id);
                },
                columns:[
                    {data:'id', name:'id'},
                    {data:'title', name:'title'},
                    {data:'ticket_number', name:'ticket_number'},
                    {data:'c_uname', name:'a_uname'},
                    {data:'a_uname', name:'a_uname'},
                    {data:'updated_at2', name:'updated_at2'}
                ],
                "fnPreDrawCallback": function(oSettings, json) {
                    $(".card-body").css({"opacity":"0.3"});
                    $('.loader1').css('display', 'block');
                    $('.loader').css('display', 'block');
                },
                "headerCallback": function headerCallback(thead, data, start, end, display) {
                    $(thead).find('th').first().css('text-align','center');
                    $(thead).find('th').first().css('padding','3px 5px 3px 5px');
                    if ($(thead).find('th').first().find('i').hasClass('fa-check-square-o')) {
                        $(thead).find('th').first().find('i').removeClass('fa-check-square-o');
                        $(thead).find('th').first().find('i').addClass('fa-square-o');
                    }
                },
                "lengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
                "ajax": {
                    url: "{{url('filter')}}",
                    data: function (d) {
                        d.segment = "{{$segment}}/"+status;
                    }
                },
                "aaSorting": [[5, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": 0},
                    { "searchable": false, "targets": [5] },
                    {"visible": true, "targets": 5 },
                    {
                        "aTargets": [0],
                        "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            var str = sData;
                            var start = str.indexOf('#');
                            var color = hexToRgbA(str.substr(start, 7));
                                $(nTd).css("border-left", "5px solid "+color);
                        }
                    } 
                ],
                "fnCreatedRow": function (nRow, aData, iDataIndex) {
                    var str = aData.id;
                    var length = aData.ticket_number.indexOf('*') - aData.ticket_number.indexOf('$');
                    var p = aData.ticket_number.substr(aData.ticket_number.indexOf('$')+1, length-1);
                    $("td", nRow).attr('title', "{!! Lang::get('lang.ticket-has-x-priority', ['priority' => '"+p+"']) !!}");
                    if (str.search("#000") == -1) {
                        $("td", nRow).css({"background-color": "#F3F3F3", "font-weight": "600", "border-bottom": "solid 0.5px #ddd", "border-right": "solid 0.5px #F3F3F3"});
                        $("td", nRow).mouseenter(function () {
                            $("td", nRow).css({"background-color": "#DEDFE0", "font-weight": "600", "border-bottom": "solid 0.5px #ddd", "border-right": "solid 0.5px #DEDFE0"});
                        });
                        $("td", nRow).mouseleave(function () {
                            $("td", nRow).css({"background-color": "#F3F3F3", "font-weight": "600", "border-bottom": "solid 0.5px #ddd", "border-right": "solid 0.5px #F3F3F3"});
                        });
                    } else {
                        $("td", nRow).css({"background-color": "white", "border-bottom": "solid 0.5px #ddd", "border-right": "solid 0.5px white"});
                        $("td", nRow).mouseenter(function () {
                            $("td", nRow).css({"background-color": "#DEDFE0", "border-bottom": "solid 0.5px #ddd", "border-right": "solid 0.5px #DEDFE0"});
                        });
                        $("td", nRow).mouseleave(function () {
                            $("td", nRow).css({"background-color": "white", "border-bottom": "solid 0.5px #ddd", "border-right": "solid 0.5px white"});
                        });
                    }
                }
            });
        }

        function hexToRgbA(hex){
            var c;
            if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
                c= hex.substring(1).split('');
                if(c.length== 3){
                    c= [c[0], c[0], c[1], c[1], c[2], c[2]];
                }
                c= '0x'+c.join('');
                return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+', 0.67)';
            }
            throw new Error('Bad Hex');
        }

        $('#open_tab').on('click', function(){
            redrawTable("Open");
        });

        $('#closed_tab').on('click', function(){
            redrawTable("Closed");
        });

        $('#deleted_tab').on('click', function(){
            redrawTable("Deleted");
        });

        $('#unapproved_tab').on('click', function(){
            redrawTable("Unapproved");
        })

        function redrawTable(showTicket) {
            status = showTicket;
            $("#chumper").dataTable().fnDestroy();
            myFunction();
        }
    });
</script>