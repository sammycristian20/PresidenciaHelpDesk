<?php

namespace App\AutoAssign\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use Illuminate\Http\Request;
use App\AutoAssign\Requests\AssignmentRequest;
use App\Model\helpdesk\Agent\Department;
use Exception;
use Lang;

/**
 * Assigning the ticket via Auto assign module
 * 
 * @abstract controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name SettingsController
 * 
 */


class SettingsController extends Controller {

    public function __contructor() {

        $this->middleware(['roles', 'install']);
    }
    /**
     * 
     * get the settings blade view
     * 
     * @return view
     */
    public function getSettings() {
        $departments = Department::all();
        $dept = Department::where('en_auto_assign', 1)->pluck('id')->toArray();
        return view('assign::setting', compact('departments', 'dept'));
    }
    /**
     * method to update auto assign settings 
     * @param AssignmentRequest $request
     * @return response
     */
    public function postSettings(AssignmentRequest $request) {
        try {
            $all = $request->except('_token');
            ($request->filled('department_list')) && $all = $request->except('department_list');
            if (count($all) > 0) {
                $this->delete();
                $this->save($all);
            }
            ($request->filled('department_list')) ? $this->enableAutoAssignInDepartment($request->get('department_list')) : $this->disableAutoAssignInDepartment();
            return successResponse(Lang::get('lang.auto_assign_updated'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * method to get auto assign settings details
     * @return response
     */
    public function getAutoAssignSettingsDetails() {
            try {
                $settings = CommonSettings::where('option_name', 'auto_assign')->get()->toArray(); 
                if($settings)
                {
                    foreach ($settings as $setting) {
                        $autoAssign[$setting['optional_field']] = $setting['option_value'];
                        if($setting['option_value'] == 'specific' && $setting['optional_field'] == 'assign_department_option')
                        {
                            $autoAssign['department_list'] = Department::where('en_auto_assign', 1)->select('id','name')->get()->toArray();
                        }
                    }
                }

                else
                {
                    $autoAssign = [
                        "status" => 0,
                        "only_login" => 0,
                        "assign_not_accept" => 0,
                        "assign_with_type" => 0,
                        "is_location" => 0,
                        "assign_department_option" => 'all',
                        "threshold" => 0,
                    ];
                }

                return successResponse('',['autoAssign' => $autoAssign]);
            } catch (Exception $e) {
                return errorResponse($e->getMessage());
            }
    }

    /**
     * 
     * get the icon on admin panel
     * Note: added pass by reference parameter, so, that html string should not be duplicated
     * Two auto assign icons were coming in admin panel
     * @param string $settingHtmlString
     * @return null
     */
    public function settingsView(string &$settingHtmlString) {
        $settingHtmlString = ' <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="' . url('auto-assign') . '" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-check-square fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                       <div class="text-center text-sm">'.trans('lang.auto_assign').'</div>
                    </div>
                </div>';
    }
    /**
     * deleting the settings
     */
    public function delete() {
        $setting = new CommonSettings();
        $settings = $setting->where('option_name', 'auto_assign')->get();
        if ($settings->count() > 0) {
            foreach ($settings as $assign) {
                $assign->delete();
            }
        }
    }
    /**
     * 
     * Saving the posted values
     * 
     * @param array $fields
     */
    public function save($fields) {
        $setting = new CommonSettings();
        foreach ($fields as $optional => $value) {
            $setting->create([
                'option_name' => 'auto_assign',
                'optional_field' => $optional,
                'option_value' => $value
            ]);
        }
    }

    /**
     * function to enable auto assignment for selected department lsit
     * @param    Array    $deptArray  Array list of department ids
     * @return   Boolean  true
     */
    public function enableAutoAssignInDepartment($deptArray = [])
    {
        if (count($deptArray) > 0) {
            $this->disableAutoAssignInDepartment();
            Department::whereIn('id', $deptArray)
            ->update([
                'en_auto_assign' => 1
            ]);
        }
        return true;
    }

    /**
     * function to disable auto-assignment for departments when all is selected in auto-assignment settings
     * @param    void
     * @return   boolean  true
     */
    public function disableAutoAssignInDepartment()
    {
        Department::where('en_auto_assign', 1)->update([
            'en_auto_assign' => 0
        ]);
        return true;
    }
}
