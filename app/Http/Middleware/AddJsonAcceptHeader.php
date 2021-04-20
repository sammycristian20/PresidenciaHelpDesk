<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\HeaderBag;
use App\Model\Api\ApiSetting;
use Lang;

class AddJsonAcceptHeader {

    /**
     * Add Json HTTP_ACCEPT header for an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        $api_enable = 0;
        if (isInstall()) {
            $api = ApiSetting::whereIn('key', ['api_enable', 'api_key', 'api_key_mandatory'])->get();
            //by default
            if (!$api->count()) {
                return errorResponse(Lang::get('lang.api_disabled'));
            }
            $apiEnable = $api->where('key', 'api_enable')->first()->value;
            if (!$apiEnable) {
                return errorResponse(Lang::get('lang.api_disabled'));
            }
            $apiKey = $api->where('key', 'api_key')->first()->value;
            $apiKeyMandatory = $api->where('key', 'api_key_mandatory')->first()->value;

            if ($apiKeyMandatory) {

                if (!array_key_exists('api_key', $request->all())) {
                    return errorResponse(Lang::get('lang.api_key_is_required'));
                }
                if ($request->api_key != $apiKey) {
                    return errorResponse(Lang::get('lang.wrong_api_key'));
                }
            }
        }

        $request->server->set('HTTP_ACCEPT', 'application/json');
        $request->headers = new HeaderBag($request->server->getHeaders());
        return $next($request);
    }

}
