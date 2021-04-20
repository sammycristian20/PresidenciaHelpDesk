<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\System;
use App\Http\Controllers\Common\Request\UpdateApiSettingRequest;
use App\Model\Api\ApiSetting;

/**
 * ApiSettingController to update API setting
 */
class ApiSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('role.admin');
    }

    /**
     * method for API settings update blade page
     * @return view
     */
    public function apiSettingUpdatePage() {
        return view('themes.default1.common.api.settings');
    }

    /** 
     * method to update API setting
     * @param UpdateApiSettingRequest $request
     * @return JSON response
     */
    public function updateApiSetting(UpdateApiSettingRequest $request) {
        $apiSettingsData = $request->toArray();
        System::first()->update($apiSettingsData);
        foreach ($apiSettingsData as $key => $value) {
            ApiSetting::updateOrCreate(['key' => $key], ['key' => $key, 'value' => $value]);
        }

        return successResponse(trans('lang.api_setting_updated_successfully'));
    }

    /**
     * method to get API setting data
     * @return $apiSetting (API setting data in JSON response)
     */
    public function getApiSetting() {
        $apiSetting = System::select('id', 'api_enable', 'api_key_mandatory', 'api_key')->first();

        return successResponse('', $apiSetting);
    }
}
