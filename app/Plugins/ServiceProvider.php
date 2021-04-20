<?php

namespace App\Plugins;
use App\Providers\ExtendServiceProvider;
abstract class ServiceProvider extends ExtendServiceProvider
{
    public function boot()
    {
        if ($module = $this->getModule(func_get_args())) {
            //$this->package('app/' . $module, $module, app_path() . '/modules/' . $module);
//            $this->publishes([
//                'app/' . $module => app_path() . '/Plugins/' . $module . '/config',
//            ]);
            $this->publishes([
                'app/plugins/'.$module.'/Config/config.php' => config_path($module.'/config.php'),
            ]);
        }
    }

    public function register()
    {
        if ($module = $this->getModule(func_get_args())) {
            //$this->app['config']->package('app/' . $module, app_path() . '/modules/' . $module . '/config');
//            $this->publishes([
//                'app/' . $module => app_path() . '/Plugins/' . $module . '/config',
//            ]);

            $this->publishes([
                'app/plugins/'.$module.'/Config/config.php' => config_path($module.'/config.php'),
            ]);

            // Add routes
            $routes = app_path().'/Plugins/'.$module.'/routes.php';
            if (file_exists($routes)) {
                $this->loadRoutesFrom($routes);
            }

            $this->registerFactory($module);
        }
    }

    public function getModule($args)
    {
        $module = (isset($args[0]) and is_string($args[0])) ? $args[0] : null;

        return $module;
    }

    private function registerFactory($pluginName)
    {
      // if in development mode, we register a factory
      if(\Config::get('app.env') != 'production'){
        $factoriesPath = app_path().DIRECTORY_SEPARATOR.'Plugins'.DIRECTORY_SEPARATOR.$pluginName.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'factories';
        $this->app->make(\Illuminate\Database\Eloquent\Factory::class)->load($factoriesPath);
      }
    }

    protected function registerMiddlewareOfPackage($middlewareClasses = [])
    {
        foreach ($middlewareClasses as $middlewareName => $middlewareClass) {
            app('router')->aliasMiddleware($middlewareName, $middlewareClass);
        }
    }

    /**
     * Method registers given Providers classes for different Services.Allows
     * plugin/package developers to register custom providers of their custom
     * package. Also restricting developers to use this method will help maintainers
     * easily track which all packages are registering custom providers from their
     * packages and will reduce the efforts to debug issues.
     *
     * @param   array  $providerClasses  Array containing string Provider classes
     *                                   as its element.
     * @return  void
     */
    protected function registerProvidersOfPackage($providerClasses = []):void
    {
        foreach ($providerClasses as $providerClass) {
            $this->app->register($providerClass);
        }
    }
}
