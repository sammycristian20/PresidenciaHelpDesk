<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Config;

class ExtendServiceProvider extends ServiceProvider
{
	/**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = null;

	/**
     * Load the given routes file if routes are not already cached.
     *
     * @param  string  $path
     * @return void
     */
    protected function loadRoutesFrom($path)
    {
        $routeConfig =[];
        // if trying to access v3 APIs
        if($this->isV3Api()){

            $this->setV3ApiConfiguration();

            // for mysterious reasons `force.json` middleware checks if API is enabled or not
            $routeConfig['middleware'] = [ 'api', 'force.json'];
            $routeConfig['prefix'] = 'v3';
        }

        Route::group($routeConfig, function () use($path){
            require $path;
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
}