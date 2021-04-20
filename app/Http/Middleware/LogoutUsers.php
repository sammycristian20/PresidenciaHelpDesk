<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class LogoutUsers
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
        $user = Auth::user();
        if ($user && !$user->active) {
            // Log her out
            Auth::logout();
            if ($request->ajax()|| $request->wantsJson()) {
                return middlewareResponse(trans('lang.unauthorized_please_click_here_to_login_again', ["link"=> faveoUrl('auth/login')]), 400);
            } else {
                return redirect()->guest('auth/login');
            }
        }

        return $next($request);
    }
}