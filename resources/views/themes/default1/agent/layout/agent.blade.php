<!DOCTYPE html>
<html ng-app="fbApp">
    <?php
    $segment = "";
    $company = App\Model\helpdesk\Settings\Company::where('id', '=', '1')->first();
    $portal = App\Model\helpdesk\Theme\Portal::where('id', '=', 1)->first();
    $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->value('name');
    $title_name = isset($title) ? $title : "SUPPORT CENTER";
    ?>
    <head>
        <meta charset="UTF-8">
         @yield('meta-tags')
         <title> {!! strip_tags($title_name) !!} </title>

        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="_token" content="{!! csrf_token() !!}"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="api-base-url" content="{{ url('/') }}" />
        <!-- faveo favicon -->
    
        <link href="{{$portal->icon}}" rel="shortcut icon">
        <!-- Bootstrap 3.4.1 -->
            <link href="{{assetLink('css','bootstrap-4')}}" rel="stylesheet" type="text/css" id="bootstrapLTR"/>
            <link href="{{assetLink('css','adminlte-3')}}" rel="stylesheet" type="text/css" id="adminLTR"/>
      
        <!-- Font Awesome Icons -->
        <link href="{{assetLink('css','font-awesome-5')}}" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="{{assetLink('css','ionicons')}}" rel="stylesheet"  type="text/css" />
        <!-- Theme style -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <link href="{{assetLink('css','editor')}}" rel="stylesheet" type="text/css"/>
     
         <link href="{{assetLink('css','jquery-rating')}}" rel="stylesheet" type="text/css" />
        

         <link href="{{assetLink('css','daterangepicker')}}"  rel="stylesheet" type="text/css" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <link href="{{assetLink('css','nprogress')}}" rel="stylesheet" type="text/css"/>
        <link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>
        <link href="{{assetLink('css','ckeditor-css')}}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{{assetLink('css','new-overlay')}}">
        <!--calendar -->
              <?php
        if ($portal->agent_header_color == "skin-red") {
            $selectedColor = '#dd4b39';
        }
        if ($portal->agent_header_color == "skin-yellow") {
            $selectedColor = '#f39c12';
        }
        if ($portal->agent_header_color == "skin-blue") {
            $selectedColor = '#3c8dbc';
        }
        if ($portal->agent_header_color == "skin-black") {
            $selectedColor = '#222d32';
        }
        if ($portal->agent_header_color == "skin-green") {
            $selectedColor = '#00a65a';
        }
        if ($portal->agent_header_color == "skin-purple") {
            $selectedColor = '#605ca8';
        }
        if ($portal->agent_header_color == "null") {
            $selectedColor = '#FFFFFF';
        }
        ?>


        <!-- Browser Notification -->
        <?php
            use App\Model\helpdesk\Settings\Alert;
            $browser_status = false;
            $alert =  new Alert;
            $enabled = $alert->where('key', 'browser_notification_status')->value('value');
            $app_id = $alert->where('key', 'api_id')->value('value');
            $in_app_notification = $alert->where('key', 'browser-notification-status-inbuilt')->value('value');
            $in_app_notification_status = false;
            if($enabled == 1)
                $browser_status = 0;
            if($in_app_notification == 1)
                $in_app_notification_status = 1;


        ?>

        @if($enabled == 1)
        <link rel="manifest" href="/manifest.json">
        <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js"></script>
        <script type="text/javascript">
            // in app notification

            // in app notification ends
            var user =  "{{Auth::guest()}}";
            var OneSignal = window.OneSignal || [];
            OneSignal.push(["init", {
                appId: "<?php echo $app_id; ?>",
                autoRegister: false,
                notifyButton: {
                    enable: "{{$browser_status}}" /* Set to false to hide */
                }
            }]);

            if(!user){
                var user_id = "<?php if(Auth::user()){ echo Auth::user()->hash_ids;} ?>";
                var user_role = "<?php if(Auth::user()){ echo Auth::user()->role;} ?>";
                var user_name = "<?php if(Auth::user()){ echo Auth::user()->user_name;} ?>";
                OneSignal.push(function() {
                    //These examples are all valid
                    OneSignal.sendTag("user_name",user_name);
                    OneSignal.sendTag("user_id", user_id);
                    OneSignal.sendTag("user_role", user_role);
                });
            }

        </script>
        @endif
        <script type="text/javascript" src="{{asset('browser-detect.min.js')}}" async></script>
        <script src="{{assetLink('js','jquery-3')}}" type="text/javascript" media="none" onload="this.media='all';"></script>
        <script src="{{assetLink('js','polyfill')}}"></script>
        <script src="{{assetLink('js','select2')}}" type="text/javascript"></script>
        
        @yield('HeadInclude')
        <!-- rtl brecket -->
<style type="text/css">

    a:hover {
    text-decoration: none!important;
    }

    .form-control:focus { box-shadow: none !important;border-color: #ced4da !important; }

    .breadcrumb-item+.breadcrumb-item::before {
     content: '>\00a0' !important; 
    }
    
    .modal-backdrop.fade,.modal-backdrop.show {
    display: none;
    }

     .dropdown-item { backface-visibility: hidden; }
     
    @-moz-document url-prefix() {
      .content-wrapper {
        min-height: 570px !important;
      }
    }
#chumper{
      display: table-cell!important;
    }

.nav-sidebar .nav-item>.nav-link:hover {
    background-color: rgba(255,255,255,.1) !important;
    color: #fff !important;
}
.nav-sidebar>.nav-item>.nav-link.active {
    background-color: #f8f9fa !important;
    color: #ffffffbf !important;
}

.nav-sidebar .nav-item .active-navigation-element {
    background-color: #f8f9fa !important;
    color: #1f2d3d !important;
}

.nav-sidebar #nav_child.active { color: #ffffffbf !important; background-color: {{$selectedColor}} !important; }

.nav-sidebar .nav-header:not(:first-of-type) {
    padding-left : 0.5rem !important;
}

@media only screen and (max-width: 600px) {

    .modal-dialog {
        width: auto !important;
    }

    .btn { margin-bottom: 3px !important; }

    .VueTables__search {
        float: unset !important;
    }

    .VueTables__search-field input{
        width : auto !important;
    }
    
    #article_item { padding-bottom: 20px; }

    .column-list-right { float: unset !important; display: flex;}

    .mx-range-wrapper {  width: auto !important;}

    .btn-right { float: unset !important; }

    .height-limit { max-height: 150px !important; }
    }

.skin-black .main-header .navbar .nav>li>a {
    color: #eee;
}
body{
    padding-right: 0 !important;
        overflow-x: hidden;

}
.skin-black .main-header .navbar>.sidebar-toggle {
    color: #fff;
}

.brand-link, .navbar-custom {   background-color: {{$selectedColor}}!important; }

.sidebar-dark-custom .nav-sidebar>.nav-item>.nav-link.active{
    background-color: {{$selectedColor}}!important;
}

.container-fluid { padding-bottom: 1px; }
    .logocolor{
        background-color: {{$selectedColor}}!important;
    }
.tabs-horizontal > .active, .tabs-stacked > .active {
        background-color: {{$selectedColor}}!important;
    }
    .tab-content1  {
        background-color: {{$selectedColor}}!important;
    }
    #bar a:focus, #bar a:hover{
        background-color: {{$selectedColor}}!important;
    }

            .loading{
                background-image : url('{{assetLink('image','loading')}}');
                background-repeat:no-repeat;
            }
            .loading:after {
                content: "{!! Lang::get('lang.sending') !!}";
                text-align : right;
                padding-left : 25px;
            }

        #lang_div{width: 290px;}

.user-panel img {
        height: 2.1rem !important;
    }
    #company_image{text-align: center !important;}
  
    .brand-image{float: none !important; margin-left: 0 !important;}

    .select2-container--default .select2-selection--multiple {
    overflow-y: auto;
}
.select2-container .select2-selection--multiple {
    height: 34px;
    border-radius: 0.25rem !important;
    border: 1px solid #d2d6de !important;
}
.select2-container {
    display: block !important;
    width: 100%;
}
.table-bordered {
    border: 1px solid #dee2e6 !important;
}
.h1,
.h2,
.h3,
.h4,
.h5,
.h6,
h1,
h2,
h3,
h4,
h5,
h6 {
    font-weight: 400 !important;
}
.list-group-item {
    margin-bottom: auto !important;
}
.select2-selection__choice {
    color: #444 !important;
}
.help-block {
    color: #d73925;
}
label {
    font-weight: 500 !important;
}

.card-title { font-weight: 500 !important; }

@if(Lang::getLocale() == "ar")
.datepicker {
   direction: rtl;
}
.datepicker.dropdown-menu {
   right: initial;
}
@endif
select.form-control{
    line-height: 2;
}
        </style>
            <style>
               input[type=number]::-webkit-inner-spin-button,
               input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                margin: 0;
               }
            </style>
        @yield('custom-css')
    </head>
    @if($portal->agent_header_color)
    <body class="{{$portal -> agent_header_color}} sidebar-mini layout-fixed layout-navbar-fixed text-sm" style="display:none">
     <!-- <div class="loader1" style="z-index:2000"></div> -->
        @else
        <body class="skin-yellow sidebar-mini layout-fixed layout-navbar-fixed text-sm" style="display:none">
     <!-- <div class="loader1" style="z-index:2000"></div> -->
        @endif
        <div class="wrapper" >
            <nav class="main-header navbar navbar-expand navbar-dark navbar-custom">

                @php
                    $onerrorImage = assetLink('image','contacthead');
                @endphp

                <!-- Header Navbar: style can be found in header.less -->

                <!-- Sidebar toggle button-->
                    <ul class="navbar-nav">
                      
                        <li class="nav-item">
                    
                            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                        </li>
                    </ul>

               
                    <ul class="navbar-nav ml-auto">
                        
                        @if($auth_user_role == 'admin')
                        
                        <li class="nav-item d-none d-sm-inline-block">

                            <a href="{{url('admin')}}" class="nav-link">{!! Lang::get('lang.admin_panel') !!}</a>
                        </li>

                            @endif
                            <!-- This is a temp event must be removed with dynamic navigation-->
                            <?php \Event::dispatch('update_topbar_right', []); ?>
                            @include('themes.default1.update.notification')
                            <!-- START NOTIFICATION -->
                            @include('themes.default1.inapp-notification.notification')

                            <!-- END NOTIFICATION -->
                            <li class="nav-item dropdown">
                                <?php $src = Lang::getLocale().'.png'; ?>
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <img src="{{assetLink('image','flag').'/'.$src}}" ></img>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right p-0" id="lang_div">
                                    @foreach($langs as $key => $value)
                                            <?php
                                             $src = $key.".png";
                                            ?>
                                            <a href="#" class="dropdown-item" id="{{$key}}" onclick="changeLang(this.id)"><img src="{{assetLink('image','flag').'/'.$src}}"></img>&nbsp;{{$value[0]}}&nbsp;
                                            @if(Lang::getLocale() == "ar")
                                            &rlm;
                                            @endif
                                            ({{$value[1]}})</a>
                                    @endforeach
                                </div>
                            </li>

                          <!-- User Account: style can be found in dropdown.less -->
                            <li class="nav-item dropdown user-menu">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                    @if($auth_user_id)
                                    <img src="{{$auth_user_profile_pic}}" onerror="this.src='{{$onerrorImage}}'" class="user-image img-circle elevation-2" alt="User Image"/>
                                   <span class="d-none d-md-inline" title="{{Auth::user()->fullname}}">{{(ucfirst(str_limit(Auth::user()->fullname, 15)))}}</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <!-- User image -->
                                    <li class="user-header bg-secondary">
                                        <img src="{{$auth_user_profile_pic}}" onerror="this.src='{{$onerrorImage}}'" 
                                        class="img-circle elevation-2" alt="User Image" />
                                        <p title="{{Auth::user()->fullname}}" style="margin-top: 0px;">{{Auth::user()->fullname}}
                                            <small class="text-capitalize">{{Auth::user()->role}}</small>
                                        </p>
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        
                                        <a href="{{URL::route('profile')}}" class="btn btn-primary btn-flat">{!! Lang::get('lang.profile') !!}</a>
                                        
                                        <a href="#" class="btn btn-danger btn-flat float-right" id="logout">{!! Lang::get('lang.sign_out') !!}</a>
                                        
                                    </li>

                                </ul>

                            </li>

                        </ul>
                </nav>

            @include('themes.default1.common.socket')

            <!-- Left side column. contains the logo and sidebar -->
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar sidebar-dark-custom elevation-4">

                <a href="{{$company->website}}" class="brand-link" id="company_image">
                  <img src='{{$portal->logo}}' class="brand-image" alt="Company Log0" style="opacity: .8">
                </a>
               
               <div id="navigation-container">

                    <agent-navigation-bar></agent-navigation-bar>
               </div>
            </aside>

            <?php
                $segments = \Request::segments();
                $segment = "";
                foreach($segments as $seg){
                    $segment.="/".$seg;
                }
            ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <!-- do nothing-->
                <div class="content-header">
                  <div class="container-fluid">
                    <div class="row mb-2">
                      <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@yield('PageHeader')</h1>
                      </div><!-- /.col -->
                      <div class="col-sm-6">

                        @if(Breadcrumbs::exists())
                        {!! Breadcrumbs::render() !!}
                        @endif
                      </div><!-- /.col -->
                    </div><!-- /.row -->
                  </div><!-- /.container-fluid -->
                </div>
                <!-- Main content -->
                <section class="content">

                    <div class="container-fluid">

                    @if($dummy_installation == 1)
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="icon fa  fa-exclamation-triangle"></i> @if (\Auth::user()->role == 'admin')
                            {{Lang::get('lang.dummy_data_installation_message')}} <a href="{{route('clean-database')}}">{{Lang::get('lang.click')}}</a> {{Lang::get('lang.clear-dummy-data')}}
                        @else
                            {{Lang::get('lang.clear-dummy-data-agent-message')}}
                        @endif
                    </div>
                    @else
                        @if (!$is_mail_conigured)
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            @if (\Auth::user()->role == 'admin')
                            {{Lang::get('lang.system-outgoing-incoming-mail-not-configured')}}&nbsp;<a href="{{URL::route('emails.create')}}">{{Lang::get('lang.confihure-the-mail-now')}}</a>
                            @else
                            {{Lang::get('lang.system-mail-not-configured-agent-message')}}
                            @endif
                        </div>
                        @endif
                    @endif

                    {{--  this script can be used to store data which is common among all pages  --}}


                        <script type="text/javascript">
                        @php
                            $user = Auth::user();
                        @endphp
                        sessionStorage.setItem('full_name', '{{ $user->full_name }}');
                        sessionStorage.setItem('profile_pic', '{!! $user->profile_pic !!}');
                        sessionStorage.setItem('user_id', '{{ $user->id }}');
                        sessionStorage.setItem('is_rtl', '{{ App::getLocale() == "ar" ? 1 : 0}}');
                        sessionStorage.setItem('header_color', '{{ $selectedColor }}');
                        sessionStorage.setItem('data_time_format', '{{ dateTimeFormat() }}');
                        sessionStorage.setItem('date_format', '{{ dateFormat() }}');
                        sessionStorage.setItem('timezone', '{{ agentTimeZone() }}');
                        sessionStorage.setItem('user_role', '{{ $user->role }}');
                    </script>
                    <script src="{{bundleLink('js/lang')}}" type="text/javascript"></script>
                    <script src="{{bundleLink('js/common.js')}}" type="text/javascript"></script>
                    <script src="{{bundleLink('js/navigation.js')}}" type="text/javascript"></script>
                    <script src="{{bundleLink('js/headerMenu.js')}}" type="text/javascript"></script>
                    <div class="custom-div-top" id="custom-div-top"></div>
                    @yield('content')
                    <?php
                      // plugins can add inject scripts or bundles by listening to this event in agent panel.
                      Event::dispatch('agent-panel-scripts-dispatch');
                    ?>
                    <div class="custom-div-bottom" id="custom-div-bottom"></div>
                </div>
                </section><!-- /.content -->
            </div>
             <footer class="main-footer">

                 @if(!\Event::dispatch('helpdesk.apply.whitelabel'))
                 <!-- <div style="position: fixed;right:0;bottom:0">
                    <button data-toggle="control-sidebar" onclick="openSlide()" style="margin-right:20px"  class="btn btn-primary helpsection">
                        {!! Lang::get('lang.have_a_question') !!}
                   &nbsp;&nbsp;<i class="fas fa-question-circle" aria-hidden="true"></i></button>
                </div> -->
                @endif


                <div class="float-right d-none d-sm-block">
                     @if(\Event::dispatch('helpdesk.apply.whitelabel'))
                    <span style="font-weight: 500">Version</span> {!! Config::get('app.tags') !!}

                    @else
                    <span style="font-weight: 500">Version</span> {!! Config::get('app.version') !!}

                    @endif



                </div>
                <span style="font-weight: 500">{!! Lang::get('lang.copyright') !!} &copy; {!! date('Y') !!}  <a href="{!! $company->website !!}" target="_blank">{!! $company->company_name !!}</a>.</span>



                 @if(\Event::dispatch('helpdesk.apply.whitelabel'))
                   {!! Lang::get('lang.all_rights_reserved') !!}
                 @else
                 {!! Lang::get('lang.all_rights_reserved') !!}. {!! Lang::get('lang.powered_by') !!} <a href="https://www.faveohelpdesk.com/" target="_blank">Faveo</a>

                 @endif



                <!-- {!! Lang::get('lang.powered_by') !!} <a href="http://www.faveohelpdesk.com/" target="_blank">Faveo</a> -->
            </footer>

        </div><!-- ./wrapper -->

        <script  type="text/javascript">
            localStorage.setItem('PATH', '{{asset("/")}}');
            localStorage.setItem('CSRF', '{{ csrf_token() }}');
            localStorage.setItem('NOTI_COND', '{{$in_app_notification}}');
            localStorage.setItem('GUEST', '{{Auth::guest()}}');
            localStorage.setItem('LANGUAGE', '{{Lang::getLocale()}}');
            localStorage.setItem('PLUGIN', '{{isPlugin()}}');
            localStorage.setItem('SEGMENT', '{{$segment}}');

            //tabs
                //var segment = '<?= $segment ?>';
                var user=0;
                var tool=0;
                $('.tabs').find('a').on('click',function(){
                     var tab=$(this).html();
                   if(tab=="{!! Lang::get('lang.users') !!}"){

                     user++;
                        if(user%2==0){

                           $('.content-header').prevUntil('.tab-content').remove();
                           $('#slideUp').slideUp();
                        }
                        else{

                           $('.tab-content + .content-header').before($('<br><br>'));
                            $('#slideUp').slideDown();
                            tool=0;
                        }
                    }
                    if(tab=="{!! Lang::get('lang.tools') !!}"){
                     tool++;
                        if(tool%2==0){
                           $('.content-header').prevUntil('.tab-content').remove();
                           $('#slideUp').slideUp();
                        }
                        else{
                           $('.tab-content + .content-header').before($('<br><br>'));
                            $('#slideUp').slideDown();
                            user=0;
                        }
                    }
                })
$.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
// logout click function
    $("#logout").click(function(){
    $.ajax({
        /* the route pointing to the post function */
        url: '{{url("auth/logout")}}',
        type: 'POST',
        data: { "_token": "{{ csrf_token() }}"},
        /* remind that 'data' is the response of the AjaxController */
        success: function (response) { 
            window.location.href = response.data;
            }
        }); 
    });
        </script>

       <script type="text/javascript" src="{{assetLink('js','popper')}}"></script>
        <!-- Bootstrap 4. JS -->
        <script src="{{assetLink('js','bootstrap-4')}}" type="text/javascript"></script>
        <!-- Slimscroll -->
        <script src="{{assetLink('js','nprogress')}}" type="text/javascript"></script>
        <!-- full calendar-->

        <script src="{{assetLink('js','moment')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','adminlte-3')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','daterangepicker-min')}}" type="text/javascript" ></script>
        <script src="{{assetLink('js','moment')}}"></script>
        <script src="{{assetLink('js','moment-timezone')}}"></script>
        <script type="text/javascript" src="{{assetLink('js','moment-timezone-with-data')}}"></script>
        <script src="{{assetLink('js','ng-file-upload-shim')}}" ></script>
        <script src="{{assetLink('js','angular')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','angular-moment')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','bsSwitch')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','angular-desktop-notification')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','angular-recaptcha')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','ng-flow-standalone')}}" ></script>
        <script src="{{assetLink('js','fusty-flow')}}" ></script>
        <script src="{{assetLink('js','fusty-flow-factory')}}" ></script>
        <script src="{{assetLink('js','ng-file-upload')}}"></script>
        <script src="{{assetLink('js','ng-file-upload-shim')}}"></script>
        <script src="{{assetLink('js','tw-currency-select')}}"></script>
        <script data-require="ui-bootstrap-tpls@1.2.5" data-semver="1.2.5" src="{{assetLink('js','ui-bootstrap-tpls')}}"></script>
        <script src="{{assetLink('js','angular-agent-scripts')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','new-overlay')}}" type="text/javascript"></script>

        <?php Event::dispatch('show.calendar.script', array()); ?>
        <?php Event::dispatch('load-calendar-scripts', array()); ?>
        <?php \Event::dispatch('timeline-customjs', [['fired_at' => 'agentlayout','request' => Request()]]); ?>
                @yield('FooterInclude')
                @stack('scripts')
    </body>
</html>