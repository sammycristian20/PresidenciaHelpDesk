<?php

namespace App\Http\Middleware;

use App\Model\helpdesk\Settings\System;
use Closure;
use Schema;

class Redirect {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $root = $request->root(); //http://localhost/faveo/Faveo-Helpdesk-Pro-fork/public
        $url = (! isInstall()) ? $root.'/probe.php' : $root;
        if ($url == $root) {
            return $next($request);
        }
        $segments = $request->segments();
        $seg = implode('/', $segments);
        $url = strpos($url, '/probe.php') ? $url : $url .'/'. $seg;

        return redirect($url);
    }

    
}
