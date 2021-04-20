<?php


namespace App\FaveoReport\Middleware;

use App\FaveoReport\Models\Report;
use Closure;
use Auth;
use Lang;
use App\User;

class AccessReport
{
    /**
     * Add Json HTTP_ACCEPT header for an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!(Auth::user() && Auth::user()->role != "user")) {
            // check agent report permission
            // if reportId is present in the request, check for data permission too
            return middlewareResponse(Lang::get("lang.not_found"), 404);
        }

        if(!User::has('report')){
            return middlewareResponse(Lang::get("lang.not_found"), 404);
        }

        // if url contains reportId
        if($request->route('reportId') && !Report::isReportAccessible($request->route('reportId'))){
            return middlewareResponse(Lang::get("lang.not_found"), 404);
        }

        return $next($request);
    }
}