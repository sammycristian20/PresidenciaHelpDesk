<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * CheckRoleUser.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class CheckRoleUser
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
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
            if ($request->ajax()|| $request->wantsJson()) {
                return middlewareResponse(trans('lang.unauthorized_please_click_here_to_login_again', ["link"=> faveoUrl('auth/login')]), 400);
            } else {
                return redirect()->guest('auth/login');
            }
        }
        
        if ($request->user()->role == 'user') {
            return $next($request);
        }

        if($request->ajax()|| $request->wantsJson()){
            $result = ['fails' => 'Access denied'];
            return response()->json(compact('result'),402);
        }

        return redirect('/')->with('fails', 'Access denied');
    }
}