<?php

namespace App\Http;

use App\Http\Middleware\SecurityEnforcer;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Kernel.
 */
class Kernel extends HttpKernel {

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [

        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\LanguageMiddleware::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CheckValidLicense::class,
            \App\Http\Middleware\LogoutUsers::class,

            SecurityEnforcer::class,
        ],
        'api' => [
                'throttle:60,1',
                'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'roles' => \App\Http\Middleware\CheckRole::class,
        'role.admin' => \App\Http\Middleware\CheckRoleAdmin::class,
        'role.agent' => \App\Http\Middleware\CheckRoleAgent::class,
        'role.user' => \App\Http\Middleware\CheckRoleUser::class,
        'api' => \App\Http\Middleware\ApiKey::class,
        'jwt.auth' => \App\Http\Middleware\GetUserFromToken::class,
        'board' => \App\Http\Middleware\CheckBoard::class,
        'install' => \App\Http\Middleware\Install::class,
        'limit.reached' => \App\Http\Middleware\UserLimitReached::class,
        'limit.exceeded' => \App\Http\Middleware\UserLimitExceeded::class,
        'force.json' => \App\Http\Middleware\AddJsonAcceptHeader::class,
        'redirect' => \App\Http\Middleware\Redirect::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'installer'=>\App\Http\Middleware\IsInstalled::class,
        'kbsettings'=>\App\Http\Middleware\CheckKBsettings::class,
        'directory.organization'=>\App\Http\Middleware\CheckOrganizationProfileAccess::class,
        'kbaccess' => \App\Http\Middleware\CheckKbAccess::class,
        '2fa' => \PragmaRX\Google2FALaravel\Middleware::class,
        'account.access' => \App\Http\Middleware\AccessAccountPolicy::class,
        'dbNotUpdated' => \App\Http\Middleware\IsSystemInLatestVersion::class,
        'password.confirm' => \App\Http\Middleware\RequirePassword::class,
    ];

}
