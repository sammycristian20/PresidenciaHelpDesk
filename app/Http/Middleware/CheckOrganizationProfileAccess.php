<?php

namespace App\Http\Middleware;

use Closure;
use Lang;
use App\User;

class CheckOrganizationProfileAccess
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
        if (!User::has('access_organization_profile')) {

            return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
        }

        return $next($request);
    }

}
