<?php
namespace App\FaveoReport\Controllers;

use App\FaveoReport\Models\ReportDownload;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Lang;
use Validator;
use Zipper;
use App\User;

class ReportExportController extends Controller
{
    /**
     * Get report export view page
     *
     * @return HTTP response
     */
    public function index()
    {
        // Check logged in user has access to reports
        if (!User::has('report')) {
            return redirect('/')->with('fails', Lang::get('lang.access-denied'));
        }

        return view("report::exports.exports");
    }

    /**
     * Get list of reports
     *
     * @param $request Request instance
     * @return HTTP Json response
     */
    public function indexList(Request $request)
    {
        // Check logged in user has access to reports
        if (!User::has('report')) {
            return errorResponse(Lang::get('lang.access-denied'), 401);
        }

        // Validate query params
        $this->validateQueryParams($request);

        try {
            $currentUser = auth()->user();
            $conditions  = [['is_completed', 1]];
            $search      = $request->search;

            // Return only report exported by logged in user if role is agent
            if ($currentUser->role == 'agent') {
                $conditions[] = ['user_id', $currentUser->id];
            }

            $reports = ReportDownload::where($conditions)->when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    return $query->orWhere('file', 'like', '%' . $search . '%')
                        ->orWhere('ext', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhere('created_at', 'like', '%' . $search . '%');
                });
            })->orderBy($request->input('sort_by', 'created_at'), $request->input('order', 'desc'))
                ->with('user:id,first_name,last_name,user_name')
                ->select(['id', 'file', 'ext', 'type', 'hash', 'user_id', 'created_at'])->paginate(10);

            return successResponse('', $reports);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Download generated report export file
     *
     * @param String $hash Hash generated of the file
     * @return Resource File resource if existis
     */
    public function downloadReportExport($hash)
    {
        $conditions = [
            ['hash', $hash],
            ['is_completed', 1],
        ];
        $currentUser = auth()->user();

        // Allow only report exported by logged in user if role is agent
        if ($currentUser->role == 'agent') {
            $conditions[] = ['user_id', $currentUser->id];
        }

        $report = ReportDownload::where($conditions)->first(['file']);

        if (is_null($report) || !file_exists(storage_path('reports/export/' . $report->file))) {
            return response()->view('errors/410', [], FAVEO_INVALID_URL_CODE);
        }

        $exportZip = storage_path('reports/export/' . $report->file . '/' . $report->file . '.zip');

        Zipper::make($exportZip)->add(storage_path('reports/export/' . $report->file . '/'))->close();

        return response()->download($exportZip)->deleteFileAfterSend(true);
    }

    /**
     * Delete exported reports
     *
     * @param int $id Report id
     * @return HTTP Json response
     */
    public function delete($id)
    {
        try {
            // Check logged in user has access to reports
            if (!User::has('report')) {
                return errorResponse(Lang::get('lang.access-denied'));
            }

            $currentUser = auth()->user();
            $report      = ReportDownload::find($id);

            if (is_null($report) || ($currentUser->role == 'agent' && $report->user_id != $currentUser->id)) {
                return errorResponse(Lang::get('report::lang.report-not-found'));
            }

            $reportFile = storage_path('reports/export/' . $report->file);

            // Delete exported file
            if (file_exists($reportFile)) {
                $this->removeExportDirectory($reportFile);
            }

            // Delete report from DB
            $report->delete();

            return successResponse(Lang::get('report::lang.report-removed-successfully'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Validate query parameters
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response If validation failed return error response
     */
    private function validateQueryParams($request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'sometimes|integer',
            'page'     => 'sometimes|integer',
            'sort_by'  => 'sometimes|string|in:file,ext,type,created_at',
            'order'    => 'sometimes|string|in:asc,desc',
            'search'   => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            $errors          = $validator->errors()->messages();
            $formattedErrors = array();

            foreach ($errors as $field => $message) {
                $formattedErrors[$field] = array_first($message);
            }

            throw new HttpResponseException(errorResponse($formattedErrors, FAVEO_VALIDATION_ERROR_CODE));
        }
    }

    /**
     * Remove export directory
     *
     * @param string $dir Report export dir full path
     * @return void
     */
    private function removeExportDirectory($dir)
    {
        // Get all file names
        $files = glob($dir . DIRECTORY_SEPARATOR . '*');

        // Iterate over files and delete
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // Remove export directory
        rmdir($dir);
    }
}
