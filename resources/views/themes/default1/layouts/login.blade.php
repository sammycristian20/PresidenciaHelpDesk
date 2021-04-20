<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
      <?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (isset($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
    
        ?>
    <title> @yield('title') {!! strip_tags($title_name) !!} </title>

    @yield('meta-tags')
    
    <meta name="api-base-url" content="{{ url('/') }}" />
    
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    
    <link href="{{assetLink('css','bootstrap-4')}}" rel="stylesheet" type="text/css" />
  
    <link href="{{assetLink('css','font-awesome-5')}}" rel="stylesheet" type="text/css" />
  
    <link href="{{assetLink('css','client-custom-css')}}" rel="stylesheet" type="text/css" />
    
    <style type="text/css">
      
      .site-hero {
          padding: 35px 0;
          padding-top: 1px !important;
          background: rgb(0, 154, 186) !important;
      }
      .breadcrumb {
            width: 80%;
            margin: 20px 0% !important;
        }
    </style>

    <?php
      
      try {
      
       $portal = App\Model\helpdesk\Theme\Portal::where('id', '=', '1')->first();
      } catch(\Exception $e) {
        dd($e);
      }
    ?>
     
    @if(\Event::dispatch('helpdesk.apply.whitelabel'))
      
      @if($portal->icon!=0)
        <link href="{{asset("uploads/icon/$portal->icon")}}" rel="shortcut icon">
      @else
        <link href="{{assetLink('image','whitefavicon')}}" rel="shortcut icon">
      @endif
    @else
      
      @if($portal->icon!=0)
        <link href="{{asset("uploads/icon/$portal->icon")}}" rel="shortcut icon">
      @else
        <link href="{{assetLink('image','favicon')}}" rel="shortcut icon"> 
      @endif
    @endif
  </head>
  
  <body>
  
    
  <div id="page" class="hfeed site">
    
    <header id="masthead" class="site-header" role="banner">
      
      <div class="container">

        <div id="logo" class="col-md-12 site-logo text-center">
          
          <?php
            
            $company = App\Model\helpdesk\Settings\Company::where('id', '1')->first();
            
            $system = App\Model\helpdesk\Settings\System::where('id', '1')->first();
          ?>
         
          @if($system->url)
            <a href="{!! $system->url !!}" rel="home">
          @else
            <a href="{{url('/')}}" rel="home">
            @endif
            
            @if($company->use_logo == 1)
              
              <img src="{{asset('uploads/company')}}{{'/'}}{{$company->logo}}" alt="User Image" width="200px" />
            @else
              @if($system->name)
                <h1>{!! $system->name !!}</h1>
              @else
                <h1><b>SUPPORT</b> CENTER</h1>
              @endif
            @endif
          </a>
        </div>

        <div id="header-search" class="site-search clearfix"></div>

      </div>
    </header>

    <div class="site-hero clearfix">

      <div class="container">
      
        <ol class="breadcrumb breadcrumb-custom">
      
          <li class="text">You are here :</li>
      
          <li class="active" id="active_breadcrumb"></li>
        </ol>
      </div>
    </div>

    <div id="main" class="site-main clearfix">
      
      <div class="container">
  
        <div class="content-area">

           @yield('body')
        </div>
      </div>
    </div>

    <footer id="colophon" class="site-footer" role="contentinfo">
      
      <div class="container">

        <div class="col-sm-12 text-center">
          
          <div class="site-info">

            @if(\Event::dispatch('helpdesk.apply.whitelabel'))
              <p class="text-muted">{!! Lang::get('lang.copyright') !!} &copy; {!! date('Y') !!}  <a href="{!! $company->website !!}">{!! $company->company_name !!}</a>. {!! Lang::get('lang.all_rights_reserved') !!}. {!! Lang::get('lang.powered_by') !!} <a href="http://www.productdemourl.com/"  target="_blank">Helpdesk</a></p>
            @else
              <p class="text-muted">{!! Lang::get('lang.copyright') !!} &copy; {!! date('Y') !!}  <a href="{!! $company->website !!}">{!! $company->company_name !!}</a>. {!! Lang::get('lang.all_rights_reserved') !!}. {!! Lang::get('lang.powered_by') !!} <a href="http://www.faveohelpdesk.com/"  target="_blank">Faveo</a></p>
            @endif
          </div>
        </div>
      </div>
    </footer> 
  </div>

    <script src="{{assetLink('js','bootstrap-4')}}" type="text/javascript"></script>

    <script src="{{assetLink('js','client-custom-js')}}" type="text/javascript"></script>

    <script type="text/javascript" src="{{assetLink('js','superfish')}}"></script>

    <script type="text/javascript">

      var locale = window.location.pathname.split('/');

      if(locale[locale.length - 3] === 'password'){

        document.getElementById('active_breadcrumb').innerText = 'Reset Password';
      } else {

        document.getElementById('active_breadcrumb').innerText = 'License';
      }
    </script>
  </body>
</html>
