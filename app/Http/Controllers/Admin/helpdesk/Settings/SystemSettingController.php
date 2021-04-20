<?php
namespace App\Http\Controllers\Admin\helpdesk\Settings;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Http\Controllers\Admin\helpdesk\Request\Settings\SystemSettingUpdateRequest;
use App\Http\Controllers\Admin\helpdesk\SettingsController;

/**
 * Handles API's for System Setting Module
 *
 */
class SystemSettingController extends Controller
{

    /**
     * Creates a new controller instance
     * checks logged in user is admin or not
     * @return null
     */
    public function __construct()
    {
        $this->middleware('role.admin');
    }

    /**
     * method for system setting update blade page
     * @return view
     */
    public function getSystemSettingUpdatePage() {
        return view('themes.default1.admin.helpdesk.settings.system');
    }

    /**
     * method to get system setting existing data
     * @return $systemSetting (JSON Response)
     */
    public function getSystemSetting()
    {
        $systemSetting = System::with(['systemTimeZone','dateFormat:id,format,js_format','timeFormat:id,hours,format,js_format'])->select('id', 'name', 'status', 'time_format', 'date_format', 'time_zone_id')->first();
        $systemSetting->dateFormat->name = $systemSetting->dateFormat->format;
        $systemSetting->timeFormat->name = $systemSetting->timeFormat->hours;
        unset($systemSetting->dateFormat->format, $systemSetting->timeFormat->hours);
        $systemSetting->cdn = CommonSettings::where('option_name','cdn_settings')->value('option_value');
        if ($systemSetting) {
            $systemSetting->systemTimeZone->name = $systemSetting->systemTimeZone->time_zone_name;
            unset($systemSetting->time_zone_id, $systemSetting->systemTimeZone->location);
            $systemSetting->systemTimeZone->setAppends([]);
        }
    
        return successResponse('', $systemSetting);
    }

    /**
     * method to update system setting
     * @param SystemSettingUpdateRequest $request
     * @return response (JSON reponse)
     */
    public function updateSystemSetting(SystemSettingUpdateRequest $request)
    {
        $systemSettingRequestData = $request->toArray();
        $systemSettingRequestData['date_time_format'] = implode('',[$systemSettingRequestData['date_format'], ', ', $systemSettingRequestData['time_format']]);
        System::first()->update($systemSettingRequestData);

        if ($request->has('cdn')) {
            (new SettingsController)->cdnSettings($request->cdn);
        }
            
        return successResponse(trans('lang.system_settings_saved_successfully'));
    }
}
