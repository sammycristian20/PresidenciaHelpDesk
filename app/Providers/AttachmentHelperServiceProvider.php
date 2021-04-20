<?php

namespace App\Providers;

use App\Helper\AttachmentHelper;
use Illuminate\Support\ServiceProvider;

class AttachmentHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('attachment-helper', function () {
            return new AttachmentHelper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
