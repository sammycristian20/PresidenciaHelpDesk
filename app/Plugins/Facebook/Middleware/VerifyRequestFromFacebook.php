<?php

namespace App\Plugins\Facebook\Middleware;

use App\Plugins\Facebook\Model\FacebookCredential;
use App\Plugins\Facebook\Model\FacebookGeneralSettings;
use Closure;
use Logger;

class VerifyRequestFromFacebook
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
        $settings = FacebookGeneralSettings::first(['hub_verify_token','fb_secret']);

        if ($settings) {
            if ($request->input("hub_mode") === "subscribe" && $request->input("hub_verify_token") === $settings->hub_verify_token) {
                return response($request->input("hub_challenge"), 200);
            } elseif ($request->header('X-Hub-Signature')) {
                if ( hash_equals(
                    'sha1=' . hash_hmac('sha1', $request->getContent(), $settings->fb_secret),
                    $request->header('X-Hub-Signature')
                )) {
                    return $next($request);
                }
            }
        } else {
            Logger::exception(new \Exception(trans('Facebook::lang.facebook_webhook_insecure')));
        }
    }
}
