<?php
	$segments = \Request::segments();
	$segment = "";
	foreach($segments as $seg){
 	   $segment.=$seg;
	}
?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		var show = '';
        var searchTerm = '';
        var filterBy = '';
        var ajaxUrl = '';
        @if($segments[0] == 'agents')
            show = 'active-users';
        	ajaxUrl = "{{url('get-agents-list')}}";
        @else
        	show = 'all';
        	ajaxUrl = "{{url('user-list')}}";
        @endif
        oTable = myFunction(ajaxUrl, show, searchTerm, filterBy);

        function myFunction(ajaxUrl, show, searchTerm, filterBy)
        {
            return jQuery('#chumper').dataTable({
                "sDom": "<'row'<'col-sm-6'l><'col-sm-6'>r>"+
                        "t"+
                        "<'row'<'col-sm-6'i><'col-sm-6'p>>",
                "sPaginationType": "full_numbers",
                "bProcessing": true,
                "bServerSide": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ Records per page",
                    "sSearch"    : "Search: ",
                    "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
                },
                columns:[
                    {data: 'first_name', name:'first_name'},
                    {data: 'user_name', name:'user_name'},
                    {data: 'email', name:'email'},
                    {data: 'mobile', name:'mobile'},
                    {data: 'active', name:'active'},
                    {data: 'updated_at', name:'updated_at'},
                    {data: 'role', name:'role'},
                    {data: 'actions', name:'Actions'},
                       ],
                "fnDrawCallback": function( oSettings ) {
                    $('.loader1').css('display', 'none');
                    $('.loader').css('display', 'none');
                    $(".box-body").css({"opacity": "1"});
                    $('#blur-bg').css({"opacity": "1", "z-index": "99999"});
                },
                "fnPreDrawCallback": function(oSettings, json) {
                    $('.loader1').css('display', 'block');
                    $('.loader').css('display', 'block');
                    $(".box-body").css({"opacity":"0.3"});
                },
                "ajax": {
                    url: ajaxUrl,
                    data: function (d) {
                        d.filterBy = filterBy;
                        d.profiletype = show;
                        d.searchTerm = searchTerm;
                    }
                }
                
            });
        }

        // $("select[name=type_of_profile]").change(function () {
        //     //alert($('select[name=type_of_profile]').val());
        //     $("#chumper").dataTable().fnDestroy();
        //     myFunction();
        // });

        $('#profile-type-filter li  a').on('click', function(){
            if($(this).attr('class') == 'all') {
            	show = 'all';
	            classname = '.'+show;
    	        filterTable(show);
        	    toggleActiveClass(classname);
            }
        });

        $('.active-users').on('click', function(){
            show = 'active-users';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
            
        });

        $('.inactive').on('click', function(){
            show = 'inactive';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
        });

        $('.agents').on('click', function(){
            show = 'agents';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
        });

        $('.admins').on('click', function(){
            show = 'admins';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
        });

        $('.users').on('click', function(){
            show = 'users';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
        });

        $('.banned').on('click', function(){
            show = 'banned';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
        });

        $('.deleted').on('click', function(){
            show = 'deleted';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
        });

        $('.verified').on('click', function(){
            show = 'verified';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
        });


        $('.unverified').on('click', function(){
            show = 'unverified';
            classname = '.'+show;
            filterTable(show);
            toggleActiveClass(classname);
        });

        document.getElementById('search-text').onkeypress = function(e){
            if (!e) e = window.event;
            var keyCode = e.keyCode || e.which;
            if (keyCode == '13'){ 
                searchTerm = $('input[name=search]').val();
                $("#chumper").dataTable().fnDestroy();
                myFunction(ajaxUrl, show, searchTerm, filterBy);
            }
        }

        $('#search-text').on('input keyup paste', function () {
            var hasValue = $.trim(this.value).length;
            if (hasValue == 0 && searchTerm != '') {
                searchTerm = '';
                $("#chumper").dataTable().fnDestroy();
                myFunction(ajaxUrl, show, searchTerm, filterBy);
            }
        });

        function filterTable(show) {
            $("#chumper").dataTable().fnDestroy();
            myFunction(ajaxUrl, show, searchTerm, filterBy);
        }

        function toggleActiveClass(classname) {
            $('#profile-type-filter li').removeClass('active');
            (classname == '.all') ? $('#profile-type-filter .all').parent('li').addClass('active') : $(classname).parent('li').addClass('active');
        }

        function applyFilter(class_name) {
        	if (class_name == 'all') {
        		filterBy = '';
        		class_name = '';
        	} else {
        		filterBy = class_name;
        	}
        	toggleDepartmentActiveClass(class_name);
        	$("#chumper").dataTable().fnDestroy();
        	myFunction(ajaxUrl, show, searchTerm, filterBy);
    	}

    	$('#department-filter li a').on('click', function(){
    		applyFilter($(this).attr('class'));
    	});

    	function toggleDepartmentActiveClass(classname) {
        	$('#department-filter li').removeClass('active');
        	(classname == '') ? $('#department-filter .all').parent('li').addClass('active') : $('.'+classname).parent('li').addClass('active');
    	}
	})

	function searchUSer() {
        	searchTerm = $('input[name=search]').val();
       	 	var e = $.Event( "keypress", { which: 13 } );
        	$('#search-text').trigger(e);
    }
</script>