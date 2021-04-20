<?php
$filter = new \App\Model\helpdesk\Filters\Filter();
$tags = $filter->getTagsByTicketId($tickets->id);
?>

<tr>
    <td>
        <b>{{Lang::get('lang.tags')}}:</b>
    </td>   
    <td  id="tagsCreate">
        
    </td>
</tr>
<script>
$(function(){
    $('#tagsCreate').append('<select class="form-control" id="tagee" multiple></select>');
setTimeout(function(){
    @foreach($tags as $tag)
        @if($tag != '')
        $('#tagee').append('<option value="{{$tag}}" selected="selected">{{$tag}}</option>')
        @endif
    @endforeach
    $('#tagee').select2({
                  tags: true,
                  maximumInputLength: 20,
                  tokenSeparators: [','],
                  ajax: {
                           url: "{{url('get-tag')}}",
                           dataType: 'json',
                           type: "GET",
                           data: function(term) {
                                    return term;
                            },
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (value) {
                                         return {
                                              text: value,
                                              id: value
                                             }
                                          })
                                        };
                            },
                            cache: true
                        },
                  createTag: function(item) {
                      console.log(item)
                                return {
                                    id: item.term,
                                    text: item.term,
                                };
                    },
             }).on("change", function(event) {
                                 var values = [];
                                        // copy all option values from selected
                                 $(event.currentTarget).find("option:selected").each(function(i, selected){ 
                                         values[i] = $(selected).text();
                                         });
                                 console.log(values)
                                 $.ajax({
                                        type: "GET",
                                                url: "{{url('add-tag')}}?tags="+values+"&ticket_id={{$tickets->id}}",
                                                success:function(data){
                                                          console.log(data)
                                                }
                                        })
                           });
},2000);
});
</script>