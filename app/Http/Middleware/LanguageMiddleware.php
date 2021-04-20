<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
// use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Support\Facades\Config;
use App\Model\helpdesk\Settings\System;
use Cache;
use Session;

class LanguageMiddleware
{
    public function handle($request, Closure $next)
    {
        if (\Auth::check() && \Auth::user()->user_language != null) {
             $this->setLocale(\Auth::user()->user_language);

             return $next($request);
        }
        $this->setLocale($this->getLangFromSessionOrCache());

        return $next($request);
    }

    protected function setLocale($lang)
    {
        if ($lang != '' && array_key_exists($lang, Config::get('languages'))) {
            /**App::setLocale(System::value('content'));**/ //Not required as we are caching default language
            $path = base_path('resources/lang');
            $values = scandir($path);  //Extracts names of directories present in lang directory
            $values = array_slice($values, 2);
            if(in_array($lang, $values)) {
                App::setLocale($lang);
            }
        }
    }

    public function getLangFromSessionOrCache()
    {
        $lang = '';
        if (Session::has('language')) {
            $lang = Session::get('language');
        } elseif (Cache::has('language')) {
            $lang = Cache::get('language');
        } elseif (!Cache::has('language') && isInstall()) {
            $lang = System::select('content')->where('id', 1)->first()->content;
            Cache::forever('language', $lang);
        }

        return $lang;
    }
}
