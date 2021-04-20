<!DOCTYPE html>
<html  ng-app="fbApp">
 <?php
    $company = App\Model\helpdesk\Settings\Company::where('id', '=', '1')->first();
    $portal = App\Model\helpdesk\Theme\Portal::where('id', '=', 1)->first();
    $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->value('name');
    $title_name = isset($title) ? $title : "SUPPORT CENTER";

 if ($portal->admin_header_color == "skin-red") {
            $selectedColor = '#dd4b39';
        }
        if ($portal->admin_header_color == "skin-yellow") {
            $selectedColor = '#f39c12';
        }
        if ($portal->admin_header_color == "skin-blue") {
            $selectedColor = '#3c8dbc';
        }
        if ($portal->admin_header_color == "skin-black") {
            $selectedColor = '#222d32';
        }
        if ($portal->admin_header_color == "skin-green") {
            $selectedColor = '#00a65a';
        }
        if ($portal->admin_header_color == "skin-purple") {
            $selectedColor = '#605ca8';
        }
        if ($portal->admin_header_color == "null") {
            $selectedColor = '#FFFFFF';
        }

 ?>
 <style type="text/css">


    .modal-backdrop.fade,.modal-backdrop.show {
    display: none;
    }

    .dropdown-item { backface-visibility: hidden; }
    
   .brand-link,  .navbar-custom {   background-color: {{$selectedColor}}!important; }

.sidebar-dark-custom .nav-sidebar>.nav-item>.nav-link.active{
    background-color: {{$selectedColor}}!important;
}

.breadcrumb-item+.breadcrumb-item::before {
 content: '>\00a0' !important; 
}

label {
    font-weight: 500 !important;
}

.card-title { font-weight: 500 !important; }

.form-control:focus { box-shadow: none !important; border-color: #ced4da !important; }


     body{
        padding-right: 0 !important;
        overflow-x: hidden;
    }

    div.dataTables_wrapper div.dataTables_processing { width: 0 !important; }

    #chumper{
      /*display: table-cell!important;*/
    }

 </style>
    <head>
        <meta charset="UTF-8">
           @yield('meta-tags')

         <title> @yield('title') {!! strip_tags($title_name) !!} </title>
        <meta name="api-base-url" content="{{ url('/') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="api-base-url" content="{{ url('/') }}" />
        <!-- faveo favicon -->
    
        <link href="{{$portal->icon}}" rel="shortcut icon">
       
        <!-- Bootstrap 3.4.1 -->
        <!-- <link rel="stylesheet" href="{{assetLink('css','bootstrap-latest')}}"> -->
        <link href="{{assetLink('css','bootstrap-4')}}" rel="stylesheet" type="text/css" />

        <!-- Font Awesome Icons -->
        <!-- <link href="{{assetLink('css','font-awesome')}}" rel="stylesheet" type="text/css" /> -->
        <link href="{{assetLink('css','font-awesome-5')}}" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="{{assetLink('css','ionicons')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
        <!-- Theme style -->
        <link href="{{assetLink('css','adminlte-3')}}" rel="stylesheet" type="text/css" id="adminLTR" media="none" onload="this.media='all';"/>

        <link rel="stylesheet" href="{{assetLink('css','new-overlay')}}">

        <link  href="{{assetLink('css','editor')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
        <script src="{{assetLink('js','jquery-3')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','ckeditor')}}"></script>
        <script src="{{assetLink('js','polyfill')}}"></script>
        <style type="text/css">
            .navbar-collapse{
        background-color: {{$selectedColor}}!important;
    }
    .logo{
        background-color: {{$selectedColor}}!important;
    }
    .skin-black .main-header .navbar .nav>li>a {
    color: #eee;
   }
.skin-black .main-header .navbar>.sidebar-toggle {
    color: #fff;
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

.nav-sidebar .nav-header:not(:first-of-type) {
    padding-left : 0.5rem !important;
}

table.dataTable { width: -webkit-fill-available !important; }
button.close { margin-top: -4px !important; }
.modal-header h4 { margin-top: 0px !important; }

.modal-title {
  font-weight: 400 !important;
}

.custom-select-sm { font-size: 100% !important; margin: auto; }

    #lang_div{width: 290px;}

.user-panel img {
        height: 2.1rem !important;
    }
    #company_image{text-align: center !important;}
  
    .brand-image{float: none !important;margin-left: 0px !important;}

    .container-fluid { padding-bottom: 1px; }

.hide {
    display: none!important;
}

.select2-search__field {
    width: 100% !important;
}
.select2-container .select2-selection--multiple {
    height: 34px;
    border-radius: 0.25rem !important;
    border: 1px solid #d2d6de !important;
    overflow-y: auto;
}
.select2-container {
    display: block;
    width: 100%;
}
.loading {
    background-image: url(http://www.fotos-lienzo.es/media/aw_searchautocomplete/default/loading.gif);
    background-repeat: no-repeat;
}
.loading:after {
    content: "Sending...";
    text-align: right;
    padding-left: 25px;
}
.select2-selection__choice {
    color: #444 !important;
}
.list-group-item {
    margin-bottom: auto !important;
}

        </style>

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
        <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" ></script>
        <script type="text/javascript">
            var OneSignal = window.OneSignal || [];
            OneSignal.push(["init", {
                appId: "<?php echo $app_id; ?>",
                autoRegister: false,
                notifyButton: {
                    enable: "{{$browser_status}}" /* Set to false to hide */
                }
            }]);
            var user =  "{{Auth::guest()}}";
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
        <script type="text/javascript" src="{{asset('browser-detect.min.js')}}"></script>

        @yield('HeadInclude')
        <!-- rtl brecket -->
<!--  <style type="text/css">
     *:after {
    content: "\200E‎";
}
 </style> -->
       <style>

@if(isPlugin('ServiceDesk'))
     .content-heading-anchor{
          margin-top: -44px;
     }
@endif
@if(Lang::getLocale() == "ar")
.datepicker {
   direction: rtl;
}
.datepicker.dropdown-menu {
   right: initial;
}
@endif


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
    @if($portal->admin_header_color)
    <body class="{{$portal->admin_header_color}} sidebar-mini layout-fixed layout-navbar-fixed text-sm" style="display:none" ng-controller="MainCtrl">

    @else
    <body class="skin-yellow sidebar-mini layout-fixed layout-navbar-fixed text-sm" ng-controller="MainCtrl">

    @endif

        <div class="wrapper">

            <nav class="main-header navbar navbar-expand navbar-dark navbar-custom">
            
                <?php $notifications = App\Http\Controllers\Common\NotificationController::getNotifications(); ?>
              
                <ul class="navbar-nav">
                      
                    <li class="nav-item">
                    
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
                    </li>

                    <li class="nav-item d-none d-sm-inline-block" @yield('settings')>

                        <a href="{{url('dashboard')}}" class="nav-link">{!! Lang::get('lang.agent_panel') !!}</a>
                    </li>
                </ul>
                  <ul class="navbar-nav ml-auto">
                    
                    <li class="nav-item d-none d-sm-inline-block">

                        <a href="{{url('admin')}}" class="nav-link" onclick="removeurl()">{!! Lang::get('lang.admin_panel') !!}</a>
                    </li>

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
                                <a href="#" class="dropdown-item" id="{{$key}}" onclick="changeLang(this.id)">
                                    <img src="{{assetLink('image','flag').'/'.$src}}"></img>&nbsp;{{$value[0]}}&nbsp;
                                        @if(Lang::getLocale() == "ar")
                                        &rlm;
                                        @endif
                                        ({{$value[1]}})</a>
                                    @endforeach
                        </div>
                    </li>

                    <?php
                           $onerrorImage = assetLink('image','contacthead');
                            ?>

                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            @if(Auth::user())
                            <img src="{{Auth::user()->profile_pic}}" onerror="this.src='{{$onerrorImage}}'" class="user-image img-circle elevation-2" alt="User Image"/>
                           <span class="d-none d-md-inline" title="{{Auth::user()->fullname}}">{{(ucfirst(str_limit(Auth::user()->fullname, 15)))}}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <!-- User image -->
                            <li class="user-header bg-secondary">
                                @if(Auth::user())
                                <img src="{{Auth::user()->profile_pic}}" onerror="this.src='{{$onerrorImage}}'" 
                                class="img-circle elevation-2" alt="User Image" />
                                <p title="{{Auth::user()->fullname}}" style="margin-top: 0px;">{{Auth::user()->fullname}}
                                    <small class="text-capitalize">{{Auth::user()->role}}</small>
                                </p>
                                @endif
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                
                                <a href="{{url('profile')}}" class="btn btn-primary btn-flat">{!! Lang::get('lang.profile') !!}</a>
                                
                                <a href="#" class="btn btn-danger btn-flat float-right" id="logout">{!! Lang::get('lang.sign_out') !!}</a>
                                
                            </li>

                        </ul>

                    </li>
                </ul>
            </nav>

            <!-- Left side column. contains the logo and sidebar -->

            <aside class="main-sidebar sidebar-dark-custom elevation-4">

                <a href="{{$company->website}}" class="brand-link" id="company_image">
                  <img src='{{$portal->logo}}' class="brand-image" alt="Company Log0" style="opacity: .8">
                </a>
               
               <div id="navigation-container">

                    <admin-navigation-bar></admin-navigation-bar>
               </div>
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                  <div class="container-fluid">
                    <div class="row mb-2">
                      <div class="col-sm-4">
                        <h1 class="m-0 text-dark">@yield('PageHeader')</h1>
                      </div><!-- /.col -->
                      <div class="col-sm-8">

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
                    @if($dummy_installation == 1 || $dummy_installation == '1')
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="icon fa  fa-exclamation-triangle"></i> {{Lang::get('lang.dummy_data_installation_message')}} <a href="{{route('clean-database')}}">{{Lang::get('lang.click')}}</a> {{Lang::get('lang.clear-dummy-data')}}
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
                    <div class="custom-div-top" id="custom-div-top"></div>
                    @yield('content')
                    <div class="custom-div-bottom" id="custom-div-bottom"></div>
                </div>
                </section><!-- /.content -->
                <!-- /.content-wrapper -->
            </div>



            <footer class="main-footer">


                  @if(!\Event::dispatch('helpdesk.apply.whitelabel'))
                <!--  <div style="position: fixed;right:0;bottom:0">
                    <button data-toggle="control-sidebar" onclick="openSlide()" style="margin-right:20px"  class="btn btn-primary helpsection">
                        {!! Lang::get('lang.have_a_question') !!}
                   &nbsp;&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></button>
                </div> -->
             @endif

                <div class="float-right d-none d-sm-block">

                   @if(\Event::dispatch('helpdesk.apply.whitelabel'))
                    <span style="font-weight: 500">Version</span> {!! Config::get('app.tags') !!}

                    @else
                    <span style="font-weight: 500">Version</span> {!! Config::get('app.version') !!}

                    @endif



                </div>
                <?php
                $company = App\Model\helpdesk\Settings\Company::where('id', '=', '1')->first();
                ?>
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
        </script>

        <script type="text/javascript" src="{{assetLink('js','popper')}}"></script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="{{assetLink('js','bootstrap-4')}}" type="text/javascript"></script>
        <!-- <script src="{{assetLink('js','bootstrap-latest')}}" type="text/javascript"></script> -->

        <!-- AdminLTE App -->
        <script src="{{assetLink('js','adminlte-3')}}" type="text/javascript"></script>
        <!-- iCheck -->

        <!-- select2 -->
        <script src="{{assetLink('js','nprogress')}}" type="text/javascript"></script>



@if(isPlugin('ServiceDesk'))
<script>
    $(function(){
        $('.content-heading-anchor').next().removeClass('content');
    })
</script>
@endif
@if (trim($__env->yieldContent('no-toolbar')))
    <h1>@yield('no-toolbar')</h1>
@else

@endif
<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
    $('#Form').submit(function (e) {
        if ($('#mobile').parent().hasClass('has-error')) {
            var alert_msg = "{{Lang::get('lang.please-check-mobile-number')}}";
            alert(alert_msg);
            e.preventDefault();
        } else {
            var $this = $('#submit');
            $this.button('loading');
            $('#Edit').modal('show');
        }

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

<script type="text/javascript">
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
</script>
<script src="{{bundleLink('js/admin.js')}}" type="text/javascript"></script>
<script src="{{assetLink('js','angular')}}" type="text/javascript"></script>
<script src="{{assetLink('js','angular-moment')}}" type="text/javascript"></script>
<script src="{{assetLink('js','bsSwitch')}}" type="text/javascript"></script>
<script src="{{assetLink('js','angular-desktop-notification')}}" type="text/javascript"></script>

<script src="{{assetLink('js','ui-bootstrap-tpls')}}"></script>
<script src="{{assetLink('js','main')}}"></script>
<script src="{{assetLink('js','handleCtrl')}}"></script>
<script src="{{assetLink('js','nodeCtrl')}}"></script>
<script src="{{assetLink('js','nodesCtrl')}}"></script>

<script src="{{assetLink('js','treeCtrl')}}"></script>
<script src="{{assetLink('js','uiTree')}}"></script>
<script src="{{assetLink('js','uiTreeHandle')}}"></script>
<script src="{{assetLink('js','uiTreeNode')}}"></script>

<script src="{{assetLink('js','uiTreeNodes')}}"></script>
<script src="{{assetLink('js','helper')}}"></script>
<script src="{{assetLink('js','ng-flow-standalone')}}" ></script>
<script src="{{assetLink('js','fusty-flow')}}" ></script>

<script src="{{assetLink('js','fusty-flow-factory')}}" ></script>
<script src="{{assetLink('js','ng-file-upload')}}"></script>
<script src="{{assetLink('js','ng-file-upload-shim')}}"></script>
<script src="{{assetLink('js','tw-currency-select')}}"></script>
<script src="{{assetLink('js','angular-admin-script')}}" type="text/javascript"></script>
<script src="{{assetLink('js','new-overlay')}}" type="text/javascript"></script>
<?php \Event::dispatch('timeline-customjs', [['fired_at' => 'adminlayout','request' => Request()]]); ?>
<?php \Event::dispatch('admin-panel-scripts-dispatch') ?>
@yield('FooterInclude')
@stack('scripts')
</body>
</html>