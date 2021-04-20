<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Schema;
use App\Model\helpdesk\Settings\System;
use Illuminate\Contracts\Foundation\Application;

class CheckValidLicense
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/js/lang',
        '/server-error',
        '/auto-update-application',
        '/auto-update-database',
    ];
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     * Checks weather a user has valid license. if not,redirect to licenseError view.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     */
    public function handle($request, Closure $next)
    {
        // if product is not installed, it should not enforce validation
        if ($this->app->runningInConsole() || $this->exceptUrl() || !isInstall()) {
            return $next($request);
        }

        // so whenever license verification is false, it is going to force refresh the cache and make sure
        // it is actually false. If it is true, result is going to come from cache. If false then it will recheck and
        // cache it back
        if(!$this->isLicenseVerified() && !$this->isLicenseVerified(true)){
            return redirect('licenseError');
        }

        return $next($request);
    }

    public function exceptUrl() {
        foreach ($this->except as $value) {
            return strpos(\Request::url(), $value) !== false ? true : false;
        }
    }

    /**
     * If license is verified or not
     * NOTE FROM AVINASH: it checks if license is verified or not, if found verified, it will skip the check
     * for next 60 seconds to avoid duplicate checks. If license files are changed, it is going to reevaluate those
     * after 60 seconds
     * @param bool $forceRefresh whenever it is passed as true, method is going to get the fresh value
     * @return mixed
     */
    private function isLicenseVerified($forceRefresh = false)
    {
        if($forceRefresh){
            Cache::forget('license_verification_status');
        }
        return Cache::remember('license_verification_status', 60, function () {

            $host = \Config::get('database.connections.mysql.host');
            $username = \Config::get('database.connections.mysql.username');
            $password = \Config::get('database.connections.mysql.password');
            $database = \Config::get('database.connections.mysql.database');

            //verify license (Auto PHP Licenser will determine when connection to your server is needed)
            $GLOBALS["mysqli"] = @mysqli_connect($host, $username, $password, $database);

            //establish connection to MySQL database
            if (!Schema::hasTable('faveo_license')) {
                Schema::create('faveo_license', function ($table) {
                    $table->increments('SETTING_ID');
                });
                \DB::table('faveo_license')->insert(['SETTING_ID'=>'1']);
            }

            $isAccessViaIpAllowed = false;

            if(Schema::hasColumn('settings_system', 'access_via_ip')) {
                $isAccessViaIpAllowed = (bool)System::first()->value('access_via_ip');
            }
            $license_notifications_array=  aplVerifyLicense($GLOBALS["mysqli"], 0, $isAccessViaIpAllowed);
            return $license_notifications_array['notification_case']=="notification_license_ok";
        });
    }
}