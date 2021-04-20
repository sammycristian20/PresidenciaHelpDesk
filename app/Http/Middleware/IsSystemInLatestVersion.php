<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\helpdesk\Settings\System;


class IsSystemInLatestVersion
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
        if(request()->path() == 'auto-update-database' || $this->fileDbVersionMatches()) {
            return $next($request);
        } 
        return redirect('database-not-updated');
    }


   private function fileDbVersionMatches()
   {
        $filesystemVersion = \Config::get('app.tags');
        $dbVersion =   \Cache::remember($filesystemVersion, 3600, function () use($filesystemVersion)  {//Caching version for 1 hr
            return System::first()->value('version');
        });
        return ($filesystemVersion == $dbVersion); 
   }



}
