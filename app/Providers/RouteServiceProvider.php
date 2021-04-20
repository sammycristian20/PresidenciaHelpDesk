<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Config;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapInstallerRoutes();
        $this->mapLicenseRoute();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
       $routeConfig = ['namespace'  => $this->namespace];

       $middlewares = ['redirect', 'limit.exceeded'];

       // if trying to access v3 APIs
       if($this->isV3Api()){

         $this->setV3ApiConfiguration();

         // for mysterious reasons `force.json` middleware checks if API is enabled or not
         array_push($middlewares, 'api', 'force.json');

         $routeConfig['prefix'] = 'v3';
       } else {
         // else web APIs
         array_push($middlewares, 'web');
       }

       $routeConfig['middleware'] = $middlewares;

       Route::group($routeConfig, function () {
            require base_path('routes/web.php');
       });
    }

    /**
     * Sets up version 3 authentication coonfiguration
     * @return null
     */
    private function setV3ApiConfiguration()
    {
        // if v3 is given, we will set a api guard
       Config::set('auth.defaults.guard', 'api');

       // Since existing APIs uses the same guard, so
       // it cannot be changed manually.
       // creating a new guard is not available in passport for now,
       // overriding their class in much more complicated than simply changing the
       // configuration and run time
       Config::set('auth.guards.api.driver', 'passport');
    }

    /**
     * If the url is for version 3 APIs (if it has v3 as prefix, it will be)
     * @return boolean
     */
    private function isV3Api() : bool
    {
       // check if url has v3 in it, it should be subjected to api middleware,
       // else web middleware
       $relativeUrl = str_replace(\Request::root()."/", '', \URL::current());

       return strpos($relativeUrl, 'v3/') !== false;
    }

    /**
     * Define the "installer" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapInstallerRoutes()
    {
        Route::group([
            'middleware' => ['web','installer'],
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/installer.php');
        });
    }

      protected function mapLicenseRoute()
      {
         Route::group([
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/license.php');
        });
      }

      /**
      * Define the "api" routes for the application.
      *
      * These routes are typically stateless.
      *
      * @return void
      */
     protected function mapApiRoutes()
     {
         Route::group([
             'middleware' => 'api',
             'namespace'  => $this->namespace,
             'prefix'     => 'api',
         ], function ($router) {
             require base_path('routes/api.php');
         });
     }

}
