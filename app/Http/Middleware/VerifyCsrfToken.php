<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Lang;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends BaseVerifier
{
    
     /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'serial',
        'CheckSerial',
        'licenseVerification',
        'pre-license',
        'api/v1/*',
        'api/v2/*',
        'chunk/upload',
        'chunk/upload/public',
        'media/files/public',
        'media/files',
        'post-serial',
        'post-bill',
        'migration/upload',
        'zapier',
        'rating/*',
        'api/v3/*'
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            return errorResponse(Lang::get('lang.token_expired'),422);
        }
    }
}
