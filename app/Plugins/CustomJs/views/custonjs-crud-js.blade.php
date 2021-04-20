<script type="text/javascript">
	var url = [];
	var CustomJs = null;
	
	@if($customjs)
	CustomJs = "{{ $customjs->parameter }}";
	@endif
	
	$('#fired').on('change', function() {
        var selected = this.value;
        var key = 'adminlayout';
        $('#url').empty();
        $('#url').append("<option value=''>{!! Lang::get('CustomJs::lang.select-url') !!}</option>");
        $('#url').prop('disabled', false);
        if(selected == 'clientlayout') {
            $('#url').prop('disabled', true);
            return false;
        } else if(selected == 'agentlayout') {
            key = 'agentlayout';
        }
        updateUrlOptions(key, CustomJs);
    });

    function updateUrlOptions(key, CustomJs) {
        url[key].forEach(function($item) {
        	var select = $item == CustomJs ? 'selected' : '';
            $('#url').append('<option value="'+$item+'" '+select+'>'+$item+'</option>');
        });
    }

    $.ajax({
        type: "GET",
        url: "{{ route('fetch.routes') }}",
        dataType: "json",
        contentType: "application/json",
        beforeSend: function() {
            $('#url').prop('disabled', true);
        },
        success: function(response) {
        	$('#url').prop('disabled', false);
            url['agentlayout'] = response.data.agent;
            url['adminlayout'] = response.data.admin;
            updateUrlOptions($( "#fired option:selected" ).val(), CustomJs)
        }
    });

    $(function(){
        CKEDITOR.replace("script", {
            toolbarGroups: [
                {"name": "document", "groups": ["mode"]}
           	],
            removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar',
            disableNativeSpellChecker: false
        });
        CKEDITOR.config.startupMode = 'source';
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.menu_groups = 'tablecell,tablecellproperties,tablerow,tablecolumn,table,' +'anchor,link,image,flash';
    });
</script>
