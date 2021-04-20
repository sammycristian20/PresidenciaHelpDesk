<?php
namespace App\Http\Controllers\Admin\helpdesk\Settings;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Http\Controllers\Admin\helpdesk\Request\UserOptionsUpdateRequest;
use Event;

/**
 * Handles API's for User Options
 *
 */
class UserOptionsController extends Controller
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
     * method for user options update blade page
     * @return view
     */
    public function userOptionsUpdatePage() {
        return view('themes.default1.admin.helpdesk.settings.users-option');
    }

    /**
     * method to get user options status
     * @return $commonSetting (JSON Response)
     */
    public function getUserOptions()
    {
        $userOptionsArray = ['allow_users_to_create_ticket', 'user_set_ticket_status', 'user_show_cc_ticket', 'user_registration', 'login_restrictions', 'user_show_org_ticket', 'user_reply_org_ticket'];
        // (isMicroOrg()) ? $userOptionsArray = array_merge($userOptionsArray, ['allow_organization_dept_mngr_approve_tickets']) : '';

        $commonSettings = CommonSettings::select('option_name', 'status', 'option_value')
            ->whereIn('option_name', $userOptionsArray)->orderBy('id', 'asc')->get()->transform(function($setting) {
                if ($setting->option_name == 'login_restrictions') {
                    $setting->option_value = array_filter(explode(',', $setting->option_value));
                }

                return $setting;
            });

        $userOptionsData = [];
        foreach ($commonSettings as $settingOption) {
            if ($settingOption['option_name'] == 'login_restrictions') {
                $userOptionsData[$settingOption['option_name']] = $settingOption['option_value'];
                continue;
            }
            $userOptionsData[$settingOption['option_name']] = $settingOption['status'];
        }

        return successResponse('', $userOptionsData);
    }

    /**
     * method to update user options
     * @param UserOptionsUpdateRequest $request
     * @return response (JSON reponse)
     */
    public function updateUserOptions(UserOptionsUpdateRequest $request)
    {
        $options = $request->toArray();
        $options['login_restrictions'] = array_key_exists('login_restrictions', $options) ? $options['login_restrictions'] : [];

        $options['login_restrictions'] = implode(',', $options['login_restrictions']);
        CommonSettings::where('option_name', 'login_restrictions')->update(['option_value' => $options['login_restrictions']]);
        unset($options['login_restrictions']);

        foreach ($options as $optionKey => $optionStatus) {
            CommonSettings::where('option_name', $optionKey)->update(['status' => $optionStatus]);
        }

        return successResponse(trans('lang.user-options-updated_successfully'));
    }
}
