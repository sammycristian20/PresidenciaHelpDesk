<?php

namespace App\Http\Middleware;

use Closure;

class RequirePassword
{
    
    /**
     * The password timeout.
     *
     * @var int
     */
    protected $passwordTimeout;

    /**
     * Create a new middleware instance.
     *
     * @param  int|null  $passwordTimeout
     * @return void
     */
    public function __construct($passwordTimeout = null)
    {
        $this->passwordTimeout = $passwordTimeout ?: 3600;//Time in seconds for which faveo should remeber password
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->shouldConfirmPassword($request)) {
            return errorResponse('password_confirmation_required');
        } 
        return $next($request);
    }

    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldConfirmPassword($request)
    {
        $confirmedAt = time() - \Session::get('auth.password_confirmed_at', 0);
        return $confirmedAt > $this->passwordTimeout;
    }
}
