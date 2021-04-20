<!DOCTYPE html>
<html ng-app="Presidencia de la Republica">
    <head>
        <meta charset="UTF-8">
          @yield('meta-tags')
    <?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (isset($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SERVICIO DE ATENCION AL CIUDADANO";
        }
        $portal = App\Model\helpdesk\Theme\Portal::where('id', '=', '1')->first();
        ?>
        @if($portal->client_header_color != 'null')
         <style type="text/css">

            .site-navigation > ul > li.active > a, .site-navigation > ul > li:hover > a, .site-navigation > ul > li > a:hover, .site-navigation > ul > li > a:focus {
                background: none !important;
                border-top-color:{{$portal->client_header_color}}!important;
                color:{{$portal->client_header_color}}!important;

            }
            .site-navigation li.active > a, .site-navigation li:hover > a, .site-navigation li > a:hover, .site-navigation li > a:focus {

                color:{{$portal->client_header_color}}!important
                .navbar-default .navbar-nav > li > a {
                    color:{{$portal->client_header_color}}!important;

                }
                </style>
  @endif
  @if($portal->client_input_field_color !== 'null')
                  <style type="text/css">
                .form-control {
                    border-radius: 0px !important;
                    box-shadow: none;
                    border-color:{{$portal->client_input_field_color}}!important;
                }
                </style>
  @endif
@if($portal->client_button_color !== 'null' && $portal->client_button_color !== 'null')
                  <style type="text/css">

                .btn {
                    font-weight: 600;
                    text-transform: uppercase;
                    color: #fff;
                    background-color:{{$portal->client_button_color}}!important;
                    border-color: {{$portal->client_button_border_color}}!important;


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
 @endif
<!-- rtl brecket -->
        <title> @yield('title') {!! strip_tags($title_name) !!} </title>
      <!-- faveo favicon -->
    
      



        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- Bootstrap 3.3.2 -->


        <link rel="stylesheet" href="{{assetLink('css','bootstrap')}}" media="none" onload="this.media='all';">
        <link href="{{assetLink('css','AdminLTEsemi')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>
        <link href="{{assetLink('css','font-awesome')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';" />
        <link href="{{assetLink('css','ionicons')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>
        <link href="{{assetLink('css','app-min')}}" rel="stylesheet" type="text/css"/>
        <link href="{{assetLink('css','nprogress')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
        <link href="{{assetLink('css','flags')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>



        <meta name="api-base-url" content="{{ url('/') }}" />

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


        <script src="{{assetLink('js','jquery-3')}}" type="text/javascript" media="none" onload="this.media='all';"></script>
        <link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';" />


        <script type="text/javascript" src="{{assetLink('js','browser-detect')}}"></script>
        <script src="{{assetLink('js','polyfill')}}"></script>
        <script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer></script>

        @yield('HeadInclude')
        <style type="text/css">

     body{
    padding-right: 0 !important;
        }


            #nprogress .bar {
  background: #29d;
}
            .section-title>.line{
                width: 50% !important;
                @if($portal->client_header_color != 'null')
                border-color:{{$portal->client_header_color}}!important;
                @endif

            }
            .select2-container{
                 display:block !important;
                 width:100%;
            }
            #tmp_banner{
    display: none!important;
}
        </style>
        @yield('custom-css')
    </head>
    <body style="display:none">
        <div id="page" class="hfeed site">
            <header id="masthead" class="site-header" role="banner">
                <div class="container" style="">
                    <div id="logo" class="site-logo text-center" style="font-size: 30px;">
                        <?php
                        $company = App\Model\helpdesk\Settings\Company::where('id', '=', '1')->first();
                        $system = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
                        ?>
                        @if($system->url)
                        <a href="{!! $system->url !!}" rel="home">
                            @else
                            <a href="{{url('/')}}" rel="home">
                                @endif
                                @if($company->use_logo == 1)
                                <img src="{{asset('uploads/company')}}{{'/'}}{{$company->logo}}" alt="User Image" width="200px" height="200px"/>
                                @else
                                @if($system->name)
                                {!! $system->name !!}
                                @else
                                <b>CENTRO DE </b> SERVICIOS
                                @endif
                                @endif
                            </a>
                    </div><!-- #logo -->
                    <div id="navbar" class="navbar-wrapper text-center">
                        <nav class="navbar navbar-default site-navigation" role="navigation" style="margin-right: 100px;">
                            <ul class="nav navbar-nav navbar-menu">
                                <li @yield('home')><a href="{{url('/')}}">{!! Lang::get('lang.home') !!}</a></li>
                                @if($system->first()->status == 1)
                                <li @yield('submit')><a href="{{url('create-ticket')}}">{!! Lang::get('lang.submit_a_ticket') !!}</a></li>
                                @endif

                                <?php $kb_status=App\Model\kb\Settings::where('id','=', '1')->first(); ?>
                                @if($kb_status->status!=0)
                                <li @yield('kb')><a href="{!! url('knowledgebase') !!}">{!! Lang::get('lang.knowledge_base') !!}<i class="fa fa-angle-down fa-fw text-muted"></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="category-list">{!! Lang::get('lang.categories') !!}</a></li>
                                        <li><a href="article-list">{!! Lang::get('lang.articles') !!}</a></li>
                                    </ul>
                                </li>
                                @endif
                                    <?php
                                       if(!Auth::guest() && Auth::user()->role=="admin")
                                        {
                                            $pages = App\Model\kb\Page::where('status', '1')->get();
                                        }
                                        else{
                                            $pages = App\Model\kb\Page::where('status', '1')->where('visibility', '1')->get();
                                        }
                                        ?>
                                    @if(count($pages)>0)
                                     <li @yield('pages')><a href="">{!! Lang::get('lang.pages') !!}</a>
                                      <ul class="dropdown-menu" style="height:auto; width:18%; overflow-y:auto;">
                                          @foreach($pages as $page)
                                            <li title="{{$page->name}}"> <a href="{{'pages/'.$page->slug}}">{{str_limit($page->name, 10)}} <i class="fa fa-angle-down fa-fw text-muted"></i></a></li>
                                             @endforeach
                                      </ul>
                                    </li>
                                    @endif

                                @if(Auth::user())
                                <li @yield('myticket')><a href="{{url('mytickets')}}">{!! Lang::get('lang.my_tickets') !!}</a></li>

                                    <?php
                                         $checkId=App\Model\helpdesk\Agent_panel\User_org::where('user_id', Auth::user()->id)->where('role', 'manager')->pluck('org_id')->toArray();
                                         $organizations=App\Model\helpdesk\Agent_panel\Organization::whereIn('id',$checkId)->pluck('name','id')->toArray();
                                    ?>

                                    @if(count($organizations)>0)
                                     <li @yield('organizations')><a href="">{!! Lang::get('lang.organizations') !!}<i class="fa fa-angle-down fa-fw text-muted"></i></a>
                                      <ul class="dropdown-menu" style="height:auto; width:18%; overflow-y:auto;">
                                          @foreach($organizations as $key => $value)
                                        <li title="{{$value}}">
                                            <a href="{{url('/organizations/'.Auth::user()->id ).'/'.$key}}">
                                                  {!! str_limit($value, 13) !!}
                                            </a>
                                        </li>
                                             @endforeach
                                      </ul>
                                    </li>
                                    @endif
                                    <li @yield('profile')><a href="#" >{!! Lang::get('lang.my_profile') !!}<i class="fa fa-angle-down fa-fw text-muted"></i></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <div class="banner-wrapper user-menu text-center clearfix">
                                                <img src="{{Auth::user()->profile_pic}}"class="img-circle" alt="User Image" height="80" width="80"/><br/><br/>
                                                 <span>{!! Lang::get('lang.hello') !!}</span><br/>
                                                <h3 class="banner-title text-info h4">{{Auth::user()->first_name." ".Auth::user()->last_name}}</h3>
                                                <div class="banner-content">
                                                    {{-- <a href="{{url('kb/client-profile')}}" class="btn btn-custom btn-xs">{!! Lang::get('lang.edit_profile') !!}</a> --}} <a href="{{url('auth/logout')}}" class="btn btn-custom btn-xs">{!! Lang::get('lang.log_out') !!}</a>
                                                </div>
                                                @if(Auth::user())
                                                @if(Auth::user()->role != 'user')
                                                <div class="banner-content">
                                                    <a href="{{url('dashboard')}}" class="btn btn-custom btn-xs">{!! Lang::get('lang.dashboard') !!}</a>
                                                </div>
                                                @endif
                                                @endif
                                                @if(Auth::user())
                                                @if(Auth::user()->role == 'user')
                                                <div class="banner-content">
                                                    <a href="{{url('client-profile')}}" class="btn btn-custom btn-xs">{!! Lang::get('lang.profile') !!}</a>
                                                </div>
                                                @endif
                                                @endif
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul><!-- .navbar-user -->
                            @else
                            </ul>
                            @if(isset($errors))
                            <ul class="nav navbar-nav navbar-login">
                                <li
                                <?php
                                if (is_object($errors)) {
                                    if ($errors->first('email') || $errors->first('password')) {
                                        ?> class="sfHover"
                                            <?php
                                        }
                                    }
                                    ?>
                                    ><a href="#"  data-toggle="collapse"
                                        <?php
                                        if (is_object($errors)) {
                                            if ($errors->first('email') || $errors->first('password')) {

                                            } else {
                                                ?> class="collapsed"
                                            <?php
                                        }
                                    }
                                    ?>
                                    data-target="#login-form">{!! Lang::get('lang.login') !!} <i class="sub-indicator fa fa-chevron-circle-down fa-fw text-muted"></i></a></li>
                            </ul><!-- .navbar-login -->
                            @endif
                            <div id="login-form" @if(isset($errors))<?php if ($errors->first('email') || $errors->first('password')) { ?> class="login-form collapse fade clearfix in" <?php } else { ?> class="login-form collapse fade clearfix" <?php } ?>@endif >
                                 <div class="row">
                                    <div class="col-md-12">
                                        @if(Session::has('errors'))
                                        @if(Session::has('check'))
                                        <?php goto b; ?>
                                        @endif
                                        @if(Session::has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                        {!! Session::get('error') !!}
                                        </div>
                                         @endif
                                        <?php b: ?>
                                        @endif


                        <div class="alert-danger print-error-msg" style="display:none;margin: -12px;height: 5pc;">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button><br/>
  
                                    <span></span>
                        </div><br/>
                                       <div class="form-group has-feedback @if(isset($errors)) {!! $errors->has('email') ? 'has-error' : '' !!} @endif">
                                            {!! Form::text('email',null,['placeholder'=>Lang::get('lang.email_or_username'),'class' => 'form-control','id'=>'loginemail']) !!}
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                        </div>
                                        <div class="form-group has-feedback @if(isset($errors)) {!! $errors->has('password') ? 'has-error' : '' !!} @endif">
                                            {!! Form::password('password',['placeholder'=>Lang::get('lang.password'),'class' => 'form-control','id'=>'loginpassword']) !!}
                                            {!! Form::hidden('redirect',true) !!}

                                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                            <?php \Event::dispatch('auth.login.form'); ?>
                                            <a href="{{url('password/email')}}" style="font-size: .8em" class="pull-left">{!! Lang::get('lang.forgot_password') !!}</a>
                                        </div>
                                        <div class="form-group pull-left">
                                         <input type="checkbox" name="remember"> {!! Lang::get("lang.remember") !!}
                                        </div>
                                    </div>
                                <div class="row">

                                    <div class="col-md-12">
                                         <button type="submit" class="btn btn-custom  .btn-sm " id="postlogin">{!! Lang::get('lang.login') !!}</button>
                                    </div>
                                </div>

                           <?php $system = App\Model\helpdesk\Settings\CommonSettings::
                           where('option_name', '=', 'user_registration')
                         ->first();
                           ?>

                                  @if($system->status!=1)
                                  {{Lang::get('lang.or')}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="list-unstyled">
                                            <a href="{{url('auth/register')}}" style="font-size: 1.2em">{!! Lang::get('lang.create_account') !!}</a>
                                        </ul>
                                    </div>
                                </div>
                                @endif

                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('themes.default1.client.layout.social-login')
                                    </div>
                                </div>
                            </div><!-- #login-form -->
                            @endif


                            <ul class="nav navbar-nav navbar-menu" style="margin-right: -1px">
                            <?php $src = Lang::getLocale().'.png'; ?>
                                <li><a href="#"><img src="{{assetLink('image','flag').'/'.$src}}"></img><i class="fa fa-angle-down fa-fw text-muted"></i></a>
                                    <ul class="dropdown-menu ">
                                        @foreach($langs as $key => $value)
                                            <?php $src = $key.".png"; ?>
                                            <li><a href="#" id="{{$key}}" onclick="changeLang(this.id)"><img src="{{assetLink('image','flag').'/'.$src}}"'.$src'"."></img>&nbsp;{{$value[0]}}&nbsp;
                                            @if(Lang::getLocale() == "ar")
                                            &rlm;
                                            @endif
                                            ({{$value[1]}})</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                                </ul>
                        </nav><!-- #site-navigation -->
                    </div><!-- #navbar -->
                                <!-- <div id="header-search" class="site-search clearfix" style="padding-bottom:5px"><!-- #header-search -->
                                    {!! Form::open(['class'=>'search-form clearfix'])!!}

                                    <div class="form-border">
                                        <div class="form-inline ">
                                            <div class="form-group">
                                                <input type="text" name="s" class="search-field form-control input-lg" title="Enter search term" placeholder="{!! Lang::get('lang.have_a_question?_type_your_search_term_here') !!}" required disabled style="background: none" />
                                            </div>
                                            <button type="submit" disabled class="search-submit btn btn-custom btn-lg pull-right" style="background-color: #009aba;border-color: #00c0ef;" >{!! Lang::get('lang.search') !!}</button>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div> -->


                </div>
            </header>
            <div ng-controller="browserNotifyCtrl"></div>
            <!-- Left side column. contains the logo and sidebar -->

                         @if($portal->client_header_color!==0)
                        <div class="site-hero clearfix" style="background-color="red" >

                            {!! Breadcrumbs::render() !!}
                        </div>
                        @else
                        <div class="site-hero clearfix"  >

                            {!! Breadcrumbs::render() !!}
                        </div>
                        @endif
            <!-- Main content -->
            <div id="main" class="site-main clearfix" style="background-color:red">
                <div class="container">
                    <div class="content-area">
                        <div class="row">
                            @if(Session::has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <i class="fa  fa-check-circle"></i>
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{Session::pull('success')}}
                            </div>
                            @endif
                            @if(Session::has('warning'))
                            <div class="alert alert-warning alert-dismissable">
                                <i class="fa  fa-check-circle"></i>
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {!! Session::get('warning') !!}
                            </div>
                            @endif
                            <!-- failure message -->
                            @if(Session::has('fails'))
                            @if(Session::has('check'))
                            <?php goto a; ?>
                            @endif
                            <div class="alert alert-danger alert-dismissable">
                                <i class="fa fa-ban"></i>
                                <b>{!! Lang::get('lang.alert') !!} !</b>
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{Session::pull('fails')}}
                            </div>
                            <?php a: ?>
                            @endif
                            @yield('content')
                            <div id="sidebar" class="site-sidebar col-md-3">
                                <div class="widget-area">
                                    <section id="section-banner" class="section">
                                        @yield('check')
                                    </section><!-- #section-banner -->
                                    <section id="section-categories" class="section">
                                        @yield('category')
                                    </section><!-- #section-categories -->
                                </div>
                            </div><!-- #sidebar -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-wrapper -->
            <?php
            $footer1 = App\Model\helpdesk\Theme\Widgets::where('name', '=', 'footer1')->first();
            $footer2 = App\Model\helpdesk\Theme\Widgets::where('name', '=', 'footer2')->first();
            $footer3 = App\Model\helpdesk\Theme\Widgets::where('name', '=', 'footer3')->first();
            $footer4 = App\Model\helpdesk\Theme\Widgets::where('name', '=', 'footer4')->first();
            ?>
            <footer id="colophon" class="site-footer" role="contentinfo">
                <div class="container">
                    <div class="row col-md-12">
                        @if($footer1->title == null)
                        @else
                        <div class="col-md-3">
                            <div class="widget-area">
                                <section id="section-about" class="section">
                          <h2 class="section-title h4 clearfix" style="word-wrap: break-word; width:100%; min-height: 43.5px;">

                                    {!!$footer1->title!!}</h2>


                                    <div class="textwidget">

                                    <span style="word-wrap: break-word;"><?php  echo ($footer1->value);?></span>

                                    </div>
                                </section><!-- #section-about -->
                            </div>
                        </div>
                        @endif
                        @if($footer2->title == null)
                        @else
                        <div class="col-md-3">
                            <div class="widget-area">
                                <section id="section-latest-news" class="section">
                                    <h2 class="section-title h4 clearfix" style="word-wrap: break-word; min-height: 43.5px;">{!!$footer2->title!!}</h2>
                                    <div class="textwidget">
                                           <span style="word-wrap: break-word;"><?php  echo ($footer2->value);?></span>
                                    </div>
                                </section><!-- #section-latest-news -->
                            </div>
                        </div>
                        @endif
                        @if($footer3->title == null)
                        @else
                        <div class="col-md-3">
                            <div class="widget-area">
                                <section id="section-newsletter" class="section">
                                    <h2 class="section-title h4 clearfix" style="word-wrap: break-word; min-height: 43.5px;">{!!$footer3->title!!}</h2>
                                    <div class="textwidget">
                                            <span style="word-wrap: break-word;"><?php  echo ($footer3->value);?></span>
                                    </div>
                                </section><!-- #section-newsletter -->
                            </div>
                        </div>
                        @endif

                        @if($footer4->title == null)
                        @else
                        <div class="col-md-3">
                            <div class="widget-area">
                                <section id="section-newsletter" class="section">
                                    <h2 class="section-title h4 clearfix" style="word-wrap: break-word; min-height: 43.5px;">{{$footer4->title}}</h2>
                                    <div class="textwidget">
                                           <span style="word-wrap: break-word;"><?php  echo ($footer4->value);?></span>
                                    </div>
                                </section>
                            </div>
                        </div>
                        @endif


                    </div>
                    <div class="clearfix"></div>
                    <hr style="color:#E5E5E5"/>
                    <div class="row">
                        <div class="site-info col-md-6">
                            <p class="text-muted">{!! Lang::get('lang.copyright') !!} &copy; {!! date('Y') !!}  <a href="{!! $company->website !!}" target="_blank">{!! $company->company_name !!}</a>.


                @if(\Event::dispatch('helpdesk.apply.whitelabel'))
                  {!! Lang::get('lang.all_rights_reserved') !!}
                 @else
                 {!! Lang::get('lang.all_rights_reserved') !!}. {!! Lang::get('lang.powered_by') !!} <a href="https://www.faveohelpdesk.com/" target="_blank">Faveo</a>

                 @endif

                             <!-- {!! Lang::get('lang.powered_by') !!} <a href="http://www.faveohelpdesk.com/"  target="_blank">Faveo</a></p> -->
                        </div>
                        <div class="site-social text-right col-md-6">
                            <?php $socials = App\Model\helpdesk\Theme\Widgets::all(); ?>
                            <ul class="list-inline hidden-print">
                                @foreach($socials as $social)
                                @if($social->name == 'facebook')
                                @if($social->value)
                                <li><a href="{!! $social->value !!}" class="btn btn-social btn-facebook" target="_blank"><i class="fa fa-facebook fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "twitter")
                                @if($social->value)
                                <li><a href="{{ $social->value }}" class="btn btn-social btn-twitter" target="_blank"><i class="fa fa-twitter fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "google")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-google-plus" target="_blank"><i class="fa fa-google-plus fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "linkedin")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-linkedin" target="_blank"><i class="fa fa-linkedin fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "vimeo")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-vimeo" target="_blank"><i class="fa fa-vimeo-square fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "youtube")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-youtube" target="_blank"><i class="fa fa-youtube-play fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "pinterest")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-pinterest" target="_blank"><i class="fa fa-pinterest fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "dribbble")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-dribbble" target="_blank"><i class="fa fa-dribbble fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "flickr")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-flickr" target="_blank"><i class="fa fa-flickr fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "instagram")
                                @if($social->value)
                                <li><a href="{{$social->value }}" class="btn btn-social btn-instagram" target="_blank"><i class="fa fa-instagram fa-fw"></i></a></li>
                                @endif
                                @endif

                                @if($social->name == "rss")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-rss" target="_blank"><i class="fa fa-rss fa-fw"></i></a></li>
                                @endif
                                @endif

                                @if($social->name == "skype")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-facebook" target="_blank"><i class="fa fa-skype fa-fw"></i></a></li>
                                @endif
                                @endif
                                @if($social->name == "deviantart")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-facebook" target="_blank"><i class="fa fa-deviantart fa-fw"></i></a></li>
                                @endif
                                @endif
                                 @if($social->name == "stumble")
                                @if($social->value)
                                <li><a href="{{$social->value}}" class="btn btn-social btn-danger" target="_blank"><i class="fa fa-stumbleupon fa-fw"></i></a></li>
                                @endif
                                @endif


                                @endforeach

                            </ul>
                        </div>
                    </div>
            </footer><!-- #colophon -->
            <!-- jQuery 2.1.1 -->

            <script src="{{assetLink('js','jquery-3')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','bootstrap')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','nprogress')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','superfish')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','ckeditor')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','mobilemenu')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','know')}}" type="text/javascript"></script>



      <script type="text/javascript">
          NProgress.configure({ showSpinner: false });
          NProgress.start();

        if($('#ckeditor')[0]){
          CKEDITOR.replace('ckeditor', {
              toolbarGroups: [
                {"name": "basicstyles", "groups": ["basicstyles"]},
                {"name": "links", "groups": ["links"]},
                {"name": "paragraph", "groups": ["list", "blocks"]},
                {"name": "document", "groups": ["mode"]},
                {"name": "insert", "groups": ["insert"]},
                {"name": "styles", "groups": ["styles"]}
            ],
            removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar',
            disableNativeSpellChecker: false
            }).on('change',function(){
                $('#submit').removeAttr('disabled');
            });
            CKEDITOR.config.scayt_autoStartup = true;
            CKEDITOR.config.extraPlugins = 'colorbutton,autolink,bidi,colordialog';
            CKEDITOR.config.menu_groups =
    'tablecell,tablecellproperties,tablerow,tablecolumn,table,' +
    'anchor,link,image,flash';
      }
      </script>
            <script>

$(function () {
//Enable check and uncheck all functionality
    $(".checkbox-toggle").click(function () {
        var clicks = $(this).data('clicks');
        if (clicks) {
            //Uncheck all checkboxes
            $("input[type='checkbox']", ".mailbox-messages").iCheck("uncheck");
        } else {
            //Check all checkboxes
            $("input[type='checkbox']", ".mailbox-messages").iCheck("check");
        }
        $(this).data("clicks", !clicks);
    });
//Handle starring for glyphicon and font awesome
    $(".mailbox-star").click(function (e) {
        e.preventDefault();
//detect type
        var $this = $(this).find("a > i");
        var glyph = $this.hasClass("glyphicon");
        var fa = $this.hasClass("fa");
//Switch states
        if (glyph) {
            $this.toggleClass("glyphicon-star");
            $this.toggleClass("glyphicon-star-empty");
        }
        if (fa) {
            $this.toggleClass("fa-star");
            $this.toggleClass("fa-star-o");
        }
    });
});
            </script>
            <script type="text/javascript">
                function changeLang(lang) {
                    var link = "{{url('swtich-language')}}/"+lang;
                    window.location = link;
                }
                 $(document).ready(function () {
                     $(document).ajaxStart(function() {
                                NProgress.start();
                             });

                            $(document).ajaxComplete(function() {
                                NProgress.done();
                            });
                         $('.sidebar-toggle').click(function () {
                               $('.main-sidebar').toggle();;
                         });
                });
            </script>

            <script src="{{assetLink('js','angular')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','angular-moment')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','angular-recaptcha')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','angular-desktop-notification')}}" type="text/javascript"></script>
            <script src="{{assetLink('js','ng-file-upload')}}"></script>
            <script src="{{assetLink('js','ng-file-upload-shim')}}"></script>
            <?php \Event::dispatch('timeline-customjs', [['fired_at' => 'clientlayout','request' => Request()]]); ?>




<script>
    var app=angular.module('clientApp',['ngFileUpload','vcRecaptcha','ngDesktopNotification']);
    app.factory('httpInterceptor', ['$q', '$rootScope',
    function ($q, $rootScope) {
        var loadingCount = 0;

        return {
            request: function (config) {
                if(++loadingCount === 1) $rootScope.$broadcast('loading:progress');
                return config || $q.when(config);
            },

            response: function (response) {
                if(--loadingCount === 0) $rootScope.$broadcast('loading:finish');
                return response || $q.when(response);
            },

            responseError: function (response) {
                if(--loadingCount === 0) $rootScope.$broadcast('loading:finish');
                return $q.reject(response);
            }
        };
    }
]).config(['$httpProvider', function ($httpProvider) {
    $httpProvider.interceptors.push('httpInterceptor');
}]);
app.run(function($http,$rootScope) {
    $rootScope.$on('loading:progress', function (){
        NProgress.start();
    });

    $rootScope.$on('loading:finish', function (){
        NProgress.done();
    });
});
app.directive('numbersOnly', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                if (text) {
                    var transformedInput = text.replace(/[^0-9]/g, '');

                    if (transformedInput !== text) {
                        ngModelCtrl.$viewValue=0;
                        ngModelCtrl.$render();
                    }
                    return transformedInput;
                }
                return undefined;
            }
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});
    app.controller('browserNotifyCtrl',function($scope,$http,desktopNotification){
        var inAppEnable="{{$in_app_notification}}";
    console.log(inAppEnable);
    if(inAppEnable=='1'){
        activate();
}
        function activate() {
            $scope.isSupported = desktopNotification.isSupported();
            $scope.permission = desktopNotification.currentPermission();
            $scope.autoClose = false;
            if($scope.permission=="granted"){
            var user =  "{{Auth::guest()}}";
            // in app notification
            if(!user){
                var user_id = "<?php if(Auth::user()){ echo Auth::user()->hash_ids;} ?>";
                var user_role = "<?php if(Auth::user()){ echo Auth::user()->role;} ?>";
                var user_name = "<?php if(Auth::user()){ echo Auth::user()->user_name;} ?>";
                $scope.user_data =  "user="+user_id + "&user_agent=" + navigator.userAgent + "&browser_name=" + browserName + "&version="+fullVersion + "&platform=" + navigator.platform;
                console.log($scope.user_data);
                $.ajax({
                    type: 'GET',
                    url: '{{url("subscribe-user")}}',
                    data: $scope.user_data,
                    success: function(data){
                        if(data.success){
                             getNotification()
                        }
                    }
                })
            }
        }
    }
    function getNotification(){
        setInterval(function() {
                    $http({
                        method: 'GET',
                        url: '{{url("get-push-notifications")}}?'+$scope.user_data
                    }).success(function(data){
                            console.log(data)
                            $.each(data, function(key, value){
                                desktopNotification.show(value.message, {
                                    icon: 'https://secure.gravatar.com/avatar/153664630910409be4e20d0a747a23bc?s=80&r=g&d=identicon',
                                    body: value.created_at,
                                    autoClose:$scope.autoClose,
                                    onClick:function() {
                                        window.open(value.url,'_blank');
                                    }
                                });
                                closeNotification(value.id);
                            })
                    })

        },10000)
    };
    function closeNotification(x){
                    $http({
                        method: 'GET',
                        url: '{{url("notification-delivered")}}/'+x
                    }).success(function(data){

                    })
    };
    })

      $('.dropdown-menu').css('z-index','1001');

      $('.site-navigation .login-form').css({'z-index':'1000'})
            </script>
            <script type="text/javascript">
 $(function () {
    if('{{Lang::getLocale()}}'=='ar'){

      $('.has-feedback .form-control').css('direction','rtl')
      $('.dropdown-menu').attr('dir','rtl');
      $('.dropdown-menu').css('z-index','1001');
      $('.search-submit').css('border-radius','0px');
      $('.site-navigation .login-form').css({'right':'inherit','z-index':'1000'})
      $('#navbar').css('margin-right','200px');
      $('.pull-right').toggleClass("pull-left");
          $('.content-area').attr('dir','rtl');
         $('.hfeed').find('.text-center').addClass("pull-right");
         $('.nav-tabs li,.form-border,.navbar-nav li,.navbar-nav,.col-md-1,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9,.col-md-10,.col-md-11,.col-md-12,.col-xs-6').css('float','right');
         $('#colophon').attr('dir','rtl');
         $('.text-right').css('text-align','left');
         $('#respond').css('width',"100%");
         $('.embed-responsive-item').contents().find("html").attr('dir','rtl');
         $('.comment-body').css({"padding-right":"45px","padding-left":"0px","margin-right":"25px","border-right":"1px dotted #ccc","border-left":"0px"});
         $('.comment-author .avatar').css({"margin-left":"0","margin-right":"-90px"})
         $('.avatar').css('float','right');
          $('.col-sm-1,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-10,.col-sm-11,.col-sm-12').css('float','right');
          $('.box-categories  .fa-ul > li').css('height','60px');
          $('.box-categories  .more-link').css('height','25px');

          $('.articles-list').find('h3>a').append('&rlm;')

          $('.mailbox-attachments').css('margin-left','77%');
           // clearfix

            CKEDITOR.config.extraPlugins = 'font,bidi';
         // myticket inbox
         setTimeout(function(){
                 $('.cke_ltr').attr('dir','rtl');
                 $('.cke_ltr').toggleClass('cke_rtl');
                 $('.iframe').contents().find('html').attr('dir','rtl');;
                 $('.dropdown-menu>li').css({'float':'none','text-align':'right'});
                 //$('.navbar-nav').find('.sub-menu>li>a>img').after("&rlm;");
              },500);
        setTimeout(function(){
        $('#cke_details').addClass( "cke_rtl" );

        }, 3000);

        $('.breadcrumb').attr('dir','RTL');


        $('.search-field').attr('dir','RTL');

        $('.container').find('.site-logo').addClass("pull-right");
        $('body').css('display','block');

       }
       else{
           $('body').css('display','block');
       }
    });
</script>
                <script>localStorage.setItem('BaseUrl', "{{url('/')}}");</script>
      @stack('scripts')
    </body>
</html>
