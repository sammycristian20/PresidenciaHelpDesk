<?php
$user_role = \Auth::user()->role;
$labels = \App\Model\helpdesk\Filters\Label::orderBy('order', 'asc')->select('id', 'title', 'color')->where('status', 1)->whereRaw("find_in_set('" .$user_role. "', visible_to)")->orWhereRaw("find_in_set('all', visible_to)")->orWhere('visible_to', null)->get();
$is_manager = \DB::table('department_assign_manager')->where('manager_id', \Auth::user()->id)->count();
if ($is_manager > 0) {
    $labels2 = \App\Model\helpdesk\Filters\Label::orderBy('order', 'asc')->select('id', 'title', 'color')->where('status', 1)->whereRaw("find_in_set('dept-mngr', visible_to)")->get();
    if ($labels2 != null) {
        $labels = $labels->merge($labels2);
    }
}
?>
<div id="labels-div" class="btn-group">
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" id="labels-button"><i class="fa fa-lastfm"> </i>
        {{Lang::get('lang.labels')}} <span class="caret"></span>
    </button>
    <ul  class="dropdown-menu pull-right label-menu" role="menu">
        <form id="form-label">
        @forelse($labels as $label)
        <li><p>&nbsp;&nbsp;<input type="checkbox" name="labels" id="label" value="{{$label->title}}" {{$label->isChecked($tickets->id)}}> {!!$label->titleWithColor()!!}</p></li>
        @empty
        @if (Auth::user()->role == "admin")
        <li><p><a href="{{url('labels/create')}}"  class="col-md-offset-3 btn btn-sm btn-primary">{{Lang::get('lang.new_label')}}</a></p></li>
        @else
        <li><center><a>{!! Lang::get('lang.not-available') !!}</a></center></li>
        @endif
        @endforelse
        </form>
            @if(count($labels) > 0)
            <li style="background:#E7E7E7"><a href="#" onClick="myfunction()"><center>{{Lang::get('lang.apply')}}</center></a></li>
            @endif
    </ul>
</div>

@section('FooterInclude')
<script>
    $('.label-menu').click(function(event){
          event.stopPropagation();
    });
    function myfunction() {
        var selected = [];
        $("#labels-div").find("input:checked").each(function (i, ob) {
            selected.push($(ob).val());
        });
        $.ajax({
            url : "{{url('labels-ticket')}}",
            dataType : 'html',
            data : {'ticket_id':'{{$tickets->id}}','labels':selected},
            success: function(){
                location.reload();
            }
        });
    }


</script>

@stop