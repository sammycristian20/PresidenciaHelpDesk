<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\kb\Settings;
use Auth;
use Lang;
use App\Model\helpdesk\Settings\System;

class CheckKBsettings {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $kbSettings = Settings::where('id', 1)->first();
        $tz=System::where('id', 1)->select('time_zone_id')->first();
        date_default_timezone_set($tz->time_zone);
        if ($kbSettings->status != 1) {
            return redirect('/')->with('fails', Lang::get('lang.this_page_not_available_now'));
        }
        return $next($request);
    }

}
