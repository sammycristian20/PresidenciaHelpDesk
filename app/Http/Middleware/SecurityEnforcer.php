<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Handles all security related headers
 * @refer https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Strict_Transport_Security_Cheat_Sheet.html
 * @refer https://www.owasp.org/index.php/Cross_Frame_Scripting
 */
class SecurityEnforcer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if(method_exists($response, 'header')){

            // TODO: add HSTS header. HSTS header will not work with http. It will be done in v2.2.4
            // TODO: mark cookies as secure. Will not work with http. It will be done in v2.2.4

            // tells browser that faveo cannot be used within in i-frame. ( XFS vulnerability )
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-Content-Type-Options', 'nosniff');
        }

        return $response;
    }
}
