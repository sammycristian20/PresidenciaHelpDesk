@extends('themes.default1.agent.layout.agent')

<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>


@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.user_show-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.user_show-page-description') !!}">

@stop


@section('custom-css')
 
@endsection



@section('Users')
class="active"
@stop

@section('user-bar')
active
@stop

@section('user')
class="active"
@stop

@section('HeadInclude')


<link href="{{assetLink('css','blue')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>
 <link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css" />
 <!-- Select2 -->

@stop
<!-- header -->
@section('PageHeader')
<h1> {!! Lang::get('lang.profile') !!} </h1>

@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')

@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')

        
        <?php
            try {
                $authController = new App\Http\Controllers\Auth\AuthController();
                $authInfo = $authController->getLoggedInClientInfo();
            } catch(\Exception $e) {
                dd($e);
            }
        ?>

        @include('themes.default1.common.recaptcha')
        
        <div id="app-agent">
                
                <user-view :auth-info="{{ json_encode($authInfo) }}"></user-view>
        </div>

        <script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>
@stop