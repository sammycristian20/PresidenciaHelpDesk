@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.labels_create-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.labels_create-page-description') !!}">
@stop
@section('Tickets')
active
@stop

@section('manage-bar')
active
@stop

@section('labels')
class="active"
@stop

@section('HeadInclude')
 <!--select 2-->
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
<link href="{{assetLink('css','bootstrap-colorpicker')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.labels')}}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')

{!! Form::open(['url'=>'labels','method'=>'post', 'id' => 'label-form']) !!}
@if(Session::has('success'))
<div class="alert alert-success alert-dismissable">
    <i class="fas fa-check-circle"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('success')}}
</div>
@endif
@if(Session::has('fails'))
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('fails')}}
</div>
@endif
@if(Session::has('errors'))
<br><br>
<div class="alert alert-danger alert-dismissable">
    <i class="fas fa-ban"></i>
    <b>{!! Lang::get('lang.alert') !!}!</b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <br/>
    @if($errors->first('title'))
    <li class="error-message-padding">{!! $errors->first('title', ':message') !!}</li>
    @endif
    @if($errors->first('color'))
    <li class="error-message-padding">{!! $errors->first('color', ':message') !!}</li>
    @endif
    @if($errors->first('order'))
    <li class="error-message-padding">{!! $errors->first('order', ':message') !!}</li>
    @endif
    @if($errors->first('visible_to'))
    <li class="error-message-padding">{!! $errors->first('visible_to', ':message') !!}</li>
    @endif
</div>
@endif
@if(Session::has('warn'))
<div class="alert alert-warning alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{Session::get('warn')}}
</div>
@endif
<div class="card card-light">

    <div class="card-header">
        <h3 class="card-title">
            {!!Lang::get('lang.create_new_label')!!}
        </h3>

    </div>
    <div class="card-body">
        <table class="table table-borderless">

          
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <td style="width:10%">{!! Form::label('title','Title') !!}<span class="text-red"> *</span></td>
                <td>
                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                        {!! Form::text('title',null,['class'=>'form-control' , 'required' => true]) !!}
                    </div>
                </td>
            </div>
           
            <tr>
                <td style="width:10%">{!! Form::label('color','Color') !!}<span class="text-red"> *</span></td>
                <td>
                    <div class="form-group {{ $errors->has('color') ? 'has-error' : '' }}">
                        {!! Form::text('color', null,['class'=>'form-control my-colorpicker1 colorpicker-element', 'id' => 'color', 'placeholder' => 'pink or #ffc0cb', 'required' => true]) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td style="width:10%">{!! Form::label('order','Order') !!}<span class="text-red"> *</span></td>
                <td>
                    <div class="form-group {{ $errors->has('order') ? 'has-error' : '' }}">
                        {!! Form::input('number', 'order', null, array('class' => 'form-control','id' => 'test' ,'min' => '1','onkeypress'=>'return event.charCode >= 48 && event.charCode<=57', 'required' => true)) !!}
                    </div>
                </td>
            </tr>

            <tr>
                <td style="width:10%">{!! Form::label('status','Status') !!}</td>
                <td><input type="checkbox" value="1" name="status" id="status" checked="true">&nbsp;&nbsp;{{Lang::get('lang.enable')}}</td>
            </tr>

        </table>
    </div>
    <div class="card-footer">
     
        <button type="submit" id="submit" class="btn btn-primary">
            <i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}
        </button>

        {!! Form::close() !!}
    </div>
</div>
<!-- popup for alert-->
<div class="modal fade" id="alertpopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!!Lang::get('lang.alert') !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alertpopupmessage">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default float-right" data-dismiss="modal" id="dismis2">{!!Lang::get('lang.ok')!!}</button>
                    
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@stop
@section('FooterInclude')

<script src="{{assetLink('js','dataTables-bootstrap')}}"></script>
<script src="{{assetLink('js','select2')}}"></script>
<script src="{{assetLink('js','bootstrap-colorpicker')}}"></script>
<script>
//Colorpicker   
$(".my-colorpicker1").colorpicker({format: 'hex'}).colorpicker().on('changeColor',
        function (ev) {
            $('#submit').removeAttr('disabled');
        });
;
$(".my-colorpicker2").colorpicker().colorpicker().on('changeColor',
        function (ev) {
            $('#submit').removeAttr('disabled');
        });
;
</script>



<script type="text/javascript">
    $("#label-form").on('submit', function (e) {
        if (document.getElementById('status').checked) {
            checked = 1;
        } else {
            checked = 0;
        }
        $('<input />')
                .attr('type', 'hidden')
                .attr('name', "status")
                .attr('value', checked)
                .appendTo('#label-form');
    })

    $('.select2').select2();
    $('#visiblity').on('select2:select', function (e) {
        var data = e.params.data;
        if(data.id == 'all'){
            disableOptions(true);
        }
    });

    $('#visiblity').on('select2:unselect', function (e) {
        var data = e.params.data;
        if(data.id == 'all'){
            disableOptions(false);
        }
    });
    function disableOptions($status) {
        $('#all').siblings().prop('disabled', $status);
        if($status){
            $('.select2').val('all');
            $('.select2').trigger('change');
        }
        $('.select2').select2();

    }
</script>



<script>

    $("#test").keyup(function () {
        var val = $("#test").val();
        if (parseInt(val) < 0 || isNaN(val)) {
            alert("please enter valid values");
            $("#test").val("");
            $("#test").focus();
        }
    });
</script>

<!-- for submit button loader-->
<script>
    $(function () {
        $('#submit').attr('disabled', 'disabled');
        $('#label-form').on('input', function () {
            $('#submit').removeAttr('disabled');
        });
        $('#label-form').on('change', ':input', function () {
            $('#submit').removeAttr('disabled');
        });
    });
    $('#label-form').submit(function () {
        var $this = $('#submit');
        $this.button('loading');
        $('#Edit').modal('show');

    });

    $('#color').on('focus', function () {
        $(this).parent().removeClass('has-error');
    });
    $('#color').on('blur', function () {
        var value = $(this).val();
        if (value.startsWith("#") && (value == "#fff" || value == "#ffffff")) {
            $(this).val('');
            $('#alertpopup').modal('show');
            $('.alertpopupmessage').html('{!! Lang::get("lang.white-label-not-allowed-message") !!}');
            $(this).parent().addClass('has-error');
        }
        if (value != '' && !value.startsWith("#")) {
            if (colourNameToHex(value)) {
                $(this).val(colourNameToHex(value));
            } else {
                $(this).val('');
                $(this).parent().addClass('has-error');
            }
        }
    });

    function colourNameToHex(colour)
    {
        var colours = {"aliceblue": "#f0f8ff", "antiquewhite": "#faebd7", "aqua": "#00ffff", "aquamarine": "#7fffd4", "azure": "#f0ffff",
            "beige": "#f5f5dc", "bisque": "#ffe4c4", "black": "#000000", "blanchedalmond": "#ffebcd", "blue": "#0000ff", "blueviolet": "#8a2be2", "brown": "#a52a2a", "burlywood": "#deb887",
            "cadetblue": "#5f9ea0", "chartreuse": "#7fff00", "chocolate": "#d2691e", "coral": "#ff7f50", "cornflowerblue": "#6495ed", "cornsilk": "#fff8dc", "crimson": "#dc143c", "cyan": "#00ffff",
            "darkblue": "#00008b", "darkcyan": "#008b8b", "darkgoldenrod": "#b8860b", "darkgray": "#a9a9a9", "darkgreen": "#006400", "darkkhaki": "#bdb76b", "darkmagenta": "#8b008b", "darkolivegreen": "#556b2f",
            "darkorange": "#ff8c00", "darkorchid": "#9932cc", "darkred": "#8b0000", "darksalmon": "#e9967a", "darkseagreen": "#8fbc8f", "darkslateblue": "#483d8b", "darkslategray": "#2f4f4f", "darkturquoise": "#00ced1",
            "darkviolet": "#9400d3", "deeppink": "#ff1493", "deepskyblue": "#00bfff", "dimgray": "#696969", "dodgerblue": "#1e90ff",
            "firebrick": "#b22222", "floralwhite": "#fffaf0", "forestgreen": "#228b22", "fuchsia": "#ff00ff",
            "gainsboro": "#dcdcdc", "ghostwhite": "#f8f8ff", "gold": "#ffd700", "goldenrod": "#daa520", "gray": "#808080", "green": "#008000", "greenyellow": "#adff2f",
            "honeydew": "#f0fff0", "hotpink": "#ff69b4",
            "indianred ": "#cd5c5c", "indigo": "#4b0082", "ivory": "#fffff0", "khaki": "#f0e68c",
            "lavender": "#e6e6fa", "lavenderblush": "#fff0f5", "lawngreen": "#7cfc00", "lemonchiffon": "#fffacd", "lightblue": "#add8e6", "lightcoral": "#f08080", "lightcyan": "#e0ffff", "lightgoldenrodyellow": "#fafad2",
            "lightgrey": "#d3d3d3", "lightgreen": "#90ee90", "lightpink": "#ffb6c1", "lightsalmon": "#ffa07a", "lightseagreen": "#20b2aa", "lightskyblue": "#87cefa", "lightslategray": "#778899", "lightsteelblue": "#b0c4de",
            "lightyellow": "#ffffe0", "lime": "#00ff00", "limegreen": "#32cd32", "linen": "#faf0e6",
            "magenta": "#ff00ff", "maroon": "#800000", "mediumaquamarine": "#66cdaa", "mediumblue": "#0000cd", "mediumorchid": "#ba55d3", "mediumpurple": "#9370d8", "mediumseagreen": "#3cb371", "mediumslateblue": "#7b68ee",
            "mediumspringgreen": "#00fa9a", "mediumturquoise": "#48d1cc", "mediumvioletred": "#c71585", "midnightblue": "#191970", "mintcream": "#f5fffa", "mistyrose": "#ffe4e1", "moccasin": "#ffe4b5",
            "navajowhite": "#ffdead", "navy": "#000080",
            "oldlace": "#fdf5e6", "olive": "#808000", "olivedrab": "#6b8e23", "orange": "#ffa500", "orangered": "#ff4500", "orchid": "#da70d6",
            "palegoldenrod": "#eee8aa", "palegreen": "#98fb98", "paleturquoise": "#afeeee", "palevioletred": "#d87093", "papayawhip": "#ffefd5", "peachpuff": "#ffdab9", "peru": "#cd853f", "pink": "#ffc0cb", "plum": "#dda0dd", "powderblue": "#b0e0e6", "purple": "#800080",
            "rebeccapurple": "#663399", "red": "#ff0000", "rosybrown": "#bc8f8f", "royalblue": "#4169e1",
            "saddlebrown": "#8b4513", "salmon": "#fa8072", "sandybrown": "#f4a460", "seagreen": "#2e8b57", "seashell": "#fff5ee", "sienna": "#a0522d", "silver": "#c0c0c0", "skyblue": "#87ceeb", "slateblue": "#6a5acd", "slategray": "#708090", "snow": "#fffafa", "springgreen": "#00ff7f", "steelblue": "#4682b4",
            "tan": "#d2b48c", "teal": "#008080", "thistle": "#d8bfd8", "tomato": "#ff6347", "turquoise": "#40e0d0",
            "violet": "#ee82ee",
            "wheat": "#f5deb3", "white": "#ffffff", "whitesmoke": "#f5f5f5",
            "yellow": "#ffff00", "yellowgreen": "#9acd32"};

        if (typeof colours[colour.toLowerCase()] != 'undefined')
            return colours[colour.toLowerCase()];

        return false;
    }

</script>

@stop