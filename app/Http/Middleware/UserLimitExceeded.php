<?php

namespace App\Http\Middleware;

use Closure;
use Lang;
use App\User;

class UserLimitExceeded {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if(!isInstall()){
            return $next($request);
        }

        try {
            (new User())->validateForAgentLimit(new User);
            return $next($request);
        } catch(\Exception $e){
            return middlewareResponse($e->getMessage(), 400);
        }
    }
}