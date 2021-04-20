@extends('themes.default1.admin.layout.admin')
@section('content')
@if (count($errors) > 0)

        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- fail message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.failed')}}.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<section class="content-heading-anchor">
    <h1>
        {{Lang::get('chat::lang.chat-integrations')}}  
    </h1>
    <link href="{{assetLink('css','bootstrap-switch')}}" rel="stylesheet">
</section>


<!-- Main content -->

<div class="box box-primary">
    <div class="box-header with-border">
        <h4> {{Lang::get('chat::lang.applications')}}  </h4>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-borderless table-responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Application</th>
                            <th>Action</th>
                            <th>URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1 ?>
                        @forelse($apps as $app)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{ucfirst(camel_case($app))}}</td>
                            <td>
                                {!! Form::checkbox($app,null,$chat->status($app),['id'=>'status']) !!}
                            </td>
                            @if($chat->status($app))
                            <td>@include('chat::core.url-popup')</td>
                            @else 
                            <td>--</td>
                            @endif
                        </tr>
                        <?php $i++; ?>
                        @empty 
                        <tr>
                            <td>--</td>
                            <td>No applications</td>
                            <td>--</td>
                        </tr>
                        @endforelse 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('FooterInclude')
<script src="{{assetLink('js','bootstrap-switch')}}"></script>
<script>
$.fn.bootstrapSwitch.defaults.size = 'normal';
$.fn.bootstrapSwitch.defaults.onColor = 'success';
$.fn.bootstrapSwitch.defaults.offColor = 'danger';
$("[id='status']").bootstrapSwitch({
    onSwitchChange: function(event, state){
        var app = event.currentTarget.name;
        $.ajax({
            url : "{{url('chat/activate')}}/"+app,
            data : {'status':state,'_token': "{{ csrf_token() }}"},
            type: 'POST',
            success: function(){
                //location.reload();
            }
        });
    }
});

</script>
<script>
    $(document).ready(function () {
        var department = $("#department").val();
        send(department,'department');
        $("#department").on('change', function () {
            department = $("#department").val();
            send(department,'department');
        });
        $("#help").on('change', function () {
            var help = $("#help").val();
            send(help,'helptopic');
        });
        function send(modelid,model) {
            var app = "{{strtolower($app)}}";
            $.ajax({
                type: "get",
                dataType: "html",
                url: "{{url('chat/ajax-url')}}",
                data: {'modelid': modelid,'model':model ,'app': app},
                beforeSend: function() {
                    $('.loader1').css('display','block');
                },
                success: function (data) {
                    $('.loader1').css('display','none');
                    $("#url").html(data);
                },
            });
        }
    });
</script>
@stop
