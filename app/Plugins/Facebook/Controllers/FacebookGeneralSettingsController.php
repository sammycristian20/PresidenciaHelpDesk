<?php

namespace App\Plugins\Facebook\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Facebook\Model\FacebookGeneralSettings;
use App\Plugins\Facebook\Requests\FacebookGeneralSettingsRequest;

class FacebookGeneralSettingsController extends Controller
{
    /**
     * return settings view page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsPage()
    {
        return view('facebook::secure-facebook');
    }

    /**
     * persists the settings resource
     * @param FacebookGeneralSettingsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FacebookGeneralSettingsRequest $request)
    {
        $created = FacebookGeneralSettings::create($request->only(['fb_secret','hub_verify_token']));
        return ($created)
            ? successResponse(trans('Facebook::lang.facebook_security_settings_saved'))
            : errorResponse(trans('Facebook::lang.facebook_security_settings_save_error'));
    }

    /**
     * returns settings as json
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $settings = FacebookGeneralSettings::first(['fb_secret', 'hub_verify_token', 'id']);

        return ($settings)
            ? successResponse('', $settings)
            : errorResponse(trans('Facebook::lang.facebook_no_security_settings'));
    }

    /**
     * Updates the settings resource
     * @param FacebookGeneralSettingsRequest $request
     * @param $settingsId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FacebookGeneralSettingsRequest $request, $settingsId)
    {
        $settings = FacebookGeneralSettings::find($settingsId);

        if (!$settings) {
            return errorResponse(trans('Facebook::lang.facebook_security_settings_save_error'));
        }

        $updated = $settings->update($request->only(['fb_secret','hub_verify_token']));

        return $updated
            ? successResponse(trans('Facebook::lang.facebook_security_settings_saved'))
            : errorResponse(trans('Facebook::lang.facebook_security_settings_save_error'));
    }

    /**
     * Destroys the settings resource
     * @param $settingsId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($settingsId)
    {
        $settings = FacebookGeneralSettings::find($settingsId);

        if (!$settings) {
            return errorResponse(trans('Facebook::lang.facebook_security_settings_delete_error'));
        }

        return ($settings->delete())
            ? successResponse(trans('Facebook::lang.facebook_security_settings_deleted'))
            : errorResponse(trans('Facebook::lang.facebook_security_settings_deletion_error'));
    }
}
