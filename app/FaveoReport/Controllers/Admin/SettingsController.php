<?php

namespace App\FaveoReport\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use Exception;
use Illuminate\Http\Request;
use Lang;
use Validator;

/**
 * Setting for the reports module
 *
 * @abstract Controller
 * @author Abhishek Kumar Haith <abhishek.haith@ladybirdweb.com>
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
     * Get the setting view for reports
     *
     * @return view
     */
    public function showSettings()
    {
        try {
            // Return admin reports settings page
            return view('report::setting');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Get report settings
     * @return HTTP response
     */
    public function getReportSettings()
    {
        try {
            // Return saved report settings data
            $response = [
                'max_date_range'   => $this->settings->getOptionValue('reports_max_date_range')->first()->option_value,
                'records_per_file' => $this->settings->getOptionValue('reports_records_per_file')->first()->option_value,
            ];

            return successResponse('', $response);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Store report settings
     *
     * @param Request $request
     * @return HTTP response
     */
    public function storeSettings(Request $request)
    {
        // Validate user input
        $validator = Validator::make($request->all(), [
            'records_per_file' => 'required|numeric|min:1',
        ]);

        // If validation failed return error response with message
        if ($validator->fails()) {
            $all_errors = [];

            $errors = $validator->errors();

            $all_errors['records_per_file'] = $errors->first('records_per_file');

            return errorResponse($all_errors, FAVEO_VALIDATION_ERROR_CODE);
        }

        // Store report settings data in common settings db table
        try {

            $this->settings->updateOrCreate(['option_name' => 'reports_records_per_file'],
                ['option_value' => $request->records_per_file]);

            return successResponse(Lang::get('report::lang.settings-saved-successfully'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

}
