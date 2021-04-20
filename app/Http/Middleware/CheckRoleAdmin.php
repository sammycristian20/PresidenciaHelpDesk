<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Lang;

/**
 * CheckRoleAgent.
 *
 * @author      avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class CheckRoleAdmin
{
    
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }
    
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
        if ($this->auth->guest()) {
              return redirect()->guest('auth/login');
        }

        if ($request->user() && $request->user()->role != 'admin') {
             return redirect()->guest('/');
        }

        return $next($request);
    }
}