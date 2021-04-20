<?php

namespace App\TimeTrack\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use Exception;
use Illuminate\Http\Request;
use Lang;
use Validator;

/**
 * Setting for the time track module
 *
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name SettingsController
 *
 */
class SettingsController extends Controller
{
    protected $settings;

    /**
     * Contructor of the class
     */
    public function __construct()
    {
        $this->settings = new CommonSettings;
    }

    /**
     * Get the setting view for time track
     *
     * @return view
     */
    public function showSetting()
    {
        try {
            // Return admin time track settings page
            return view('timetrack::settings.setting');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Get time track settings
     * @return HTTP response
     */
    public function getTimeTrackSettings()
    {
        try {
            // Return saved time track optional settings data
            $response['additional'] = (new CommonSettings)->getStatus('time_track_option');

            return successResponse('', $response);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store time track settings
     *
     * @param Request $request
     * @return HTTP response
     */
    public function storeSetting(Request $request)
    {
        // Validate user input
        $validator = Validator::make($request->all(), [
            'additional' => 'required|boolean',
        ]);

        // If validation failed return error response with message
        if ($validator->fails()) {
            $all_errors = [];

            $errors = $validator->errors();

            $all_errors['additional'] = $errors->first('additional');

            return errorResponse($all_errors, 422);
        }

        // Store time track settings data in common settings db table
        try {
            $timeTrackOption         = $this->settings->firstOrCreate(['option_name' => 'time_track_option', 'optional_field' => 'additional']);
            $timeTrackOption->status = $request->additional;
            $timeTrackOption->save();

            return successResponse(Lang::get('timetrack::lang.settings-saved-successfully'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

}
