<?php

namespace App\Http\Middleware;

use Closure;

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Lang;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class GetUserFromToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {

        if (! $token = $this->auth->setRequest($request)->getToken()) {
          return errorResponse(Lang::get('lang.token_not_provided'),$responseCode = 401);
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
          return errorResponse( Lang::get('lang.token_expired'),$responseCode = 401);
        } catch (JWTException $e) {
          return errorResponse( Lang::get('lang.token_invalid'),$responseCode = 401);
        }

        if (! $user) {
            return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
        }

        $this->events->dispatch('tymon.jwt.valid', $user);

        return $next($request);
    }
}
