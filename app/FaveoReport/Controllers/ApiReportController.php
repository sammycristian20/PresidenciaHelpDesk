<?php

namespace App\FaveoReport\Controllers;

use App\FaveoReport\Jobs\ManagementReportExport;
use App\FaveoReport\Jobs\PerformanceReportExport;
use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\ReportDownload;
use App\FaveoReport\Models\ReportColumn;
use App\FaveoReport\Models\SubReport;
use App\FaveoReport\Request\CustomColumnRequest;
use App\FaveoReport\Request\ReportConfigRequest;
use App\FaveoReport\Traits\ReportConfigHelper;
use App\Http\Controllers\Controller;
use App\Model\MailJob\QueueService;
use App\Repositories\FormRepository;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lang;
use Response;

class ApiReportController extends Controller
{

    use ReportConfigHelper;

    /**
     * Gets list of reports
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReportList()
    {
        $reports = Report::orderBy('is_default', 'desc')
            ->where("is_default", 1)
            ->orWhere(function ($q){
                $q->where("user_id", Auth::user()->id)
                    ->orWhere("is_public", 1);
            })->orderBy("is_default", "desc")
            ->orderBy("created_at", "asc")
            ->get()
            ->groupBy('category');

        $formattedReports = [];

        foreach ($reports as $key => $value){
            $formattedReport["category"] = $key;
            $formattedReport["reports"] = $value;
            $formattedReports[] = $formattedReport;
        }

        return successResponse('', $formattedReports);
    }

    /**
     * Gets report config by report id
     * @param $reportId
     * @param Request $request
     * @return \HTTP
     */
    public function getReportConfigByReportId($reportId, Request $request)
    {
        $reportObj =  Report::with([
                "subReports:id,report_id,data_type,data_widget_url,data_url,selected_chart_type,list_view_by,selected_view_by,add_custom_column_url,identifier,layout"
            ])->select("id", "name", "description", "is_default", "type", "export_url", "is_public")
            ->whereId($reportId)
            ->first();

        if(!$reportObj){
            errorResponse(Lang::get("lang.not_found"), 404);
        }

        // append helplink by its url
        $reportObj->helplink = getHelplink("report-$reportObj->type");

        foreach ($reportObj->subReports as $subReport){
            $subReport->columns = $this->getSubReportColumns($subReport);
        }

        if($request->include_filters){
            $reportObj->load(["filter:id,parent_id,parent_type", "filter.filterMeta:id,key,value,ticket_filter_id"]);
            $reportObj->filters = $reportObj->filter->filterMeta;
            unset($reportObj->filter);
        }

        return successResponse("", $reportObj);
    }

    /**
     * If post report config wants to create a report or update existing one
     * @param $parentId
     * @return bool
     */
    private function isUpdating($parentId)
    {
        if(!$parentId){
            return true;
        }

        return false;
    }

    /**
     * Updates a report configuration
     * @param ReportConfigRequest $request
     * @param int|null $parentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReportConfigByReportId(ReportConfigRequest $request, int $parentId = null)
    {
        try{
            // NOTE: there's a lot of difference bw create and update operations for reports, so we are creating 2 different
            // methods to handle both
            if($this->isUpdating($parentId)) {

                // update a record
                $report = Report::find($request->id);
                $this->updateExistingReport($report, $request);

                foreach ($request->sub_reports as $subReportInRequest) {
                    $subReport = SubReport::find($subReportInRequest["id"]);
                    $this->updateExistingSubReport($subReport, $subReportInRequest);
                }
            } else {

                // create a record
                // if it is in update mode, parentId is not required
                // let's handle create part first and then think about update
                $parentReport = Report::find($parentId);

                $report = $this->createNewReport($parentReport, $request);

                foreach ($request->sub_reports as $subReportInRequest) {
                    // first find default report with this identifier
                    $parentSubReport = $parentReport->subReports->where("identifier", $subReportInRequest["identifier"])->first();

                    $this->createNewSubReport($report, $parentSubReport, $subReportInRequest);
                }
            }

            $this->saveReportFilters($report, $request->filters);

            //updating timestamp once updated
            $report->touch();

            $message = $request->id ? Lang::get("report::lang.report_successfully_updated") : Lang::get("report::lang.report_successfully_created");

            return successResponse($message);

        } catch(\UnexpectedValueException $e){

            return errorResponse($e->getMessage());
        }
    }

    /**
     * Deletes Custom Columns
     * @param  int $id  id of the field to be deleted
     * @return Response
     */
    public function deleteCustomColumn(int $id)
    {
        $columnToBeDeleted = ReportColumn::find($id);

        if(!$columnToBeDeleted){
            return errorResponse(Lang::get('lang.record_not_found'));
        }

        if(!$columnToBeDeleted->is_custom){
            return errorResponse(Lang::get('report::lang.cannot_delete_default_column'));
        }

        $columnToBeDeleted->delete();

        return successResponse(Lang::get('lang.deleted_successfully'));
    }

    /**
     * Adds sub reports columns
     * @param Request $request
     * @param $subReportId
     * @return \HTTP
     */
    public function postSubReportColumnsBySubReportId(Request $request, $subReportId)
    {
        $subReport = SubReport::whereId($subReportId)->select("id", "report_id")->first();

        if(!$subReport){
            return errorResponse("report::lang.invalid_sub_report");
        }

        try{
            foreach ($request->all() as $column) {
                $this->saveSubReportColumn($subReport, $column);
            }

            // updating parent timestamp
            $subReport->report->touch();

            return successResponse(Lang::get("lang.updated_successfully"));

        } catch (Exception $e){
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Gets sub report columns by its id
     * @param $subReportId
     * @return \HTTP
     */
    public function getSubReportColumnsBySubReportId($subReportId)
    {
        $subReport = SubReport::whereId($subReportId)->select("id", "report_id")->first();

        if(!$subReport){
            return errorResponse("report::lang.invalid_sub_report");
        }

        return successResponse("", $this->getSubReportColumns($subReport));
    }

    /**
     * Gets columns that are required in reports along with the label that we want to show
     * @param SubReport $subReport
     * @return Collection
     */
    public function getSubReportColumns(SubReport $subReport)
    {
        // check if a custom field key is missing, if yes, create an entry in DB and get
        // all columns
        $reportColumns = ReportColumn::where('sub_report_id', $subReport->id)->orderBy('order','asc')->get();

        $type = BaseReportController::getTypeByReportId($subReport->report_id);

        if($type == 'management-report') {

            // for custom field label, check all custom fields for ticket and get their labels
            $formFields = FormRepository::getTicketCustomFieldList();

            foreach ($formFields as $formField) {

                // a key doesn't exist, add that key. If extra key is found,
                // remove that key (in case of a form field delete)
                $fieldKey = 'custom_'.$formField->id;

                $reportColumn = $reportColumns->where('key', $fieldKey)->first();

                // instead of querying it again, we are pushing at the bottom of the collection
                if(!$reportColumn){
                    $reportColumn = ReportColumn::create(['key'=> $fieldKey,
                        'is_visible'=>false, 'is_sortable'=>false, 'is_timestamp'=>false, 'is_html'=>true,
                        'is_custom'=>false, 'sub_report_id'=>$subReport->id
                    ]);

                    // making report column order same as id
                    $reportColumn->order = $reportColumn->id;
                    $reportColumn->save();

                    $reportColumns->push($reportColumn);
                }

                $reportColumn->label = $formField->label;
            }

        }

        return $reportColumns;
    }


    /**
     * Adds management report column to database
     * NOTE: has to be moved to BaseReportController
     * @param CustomColumnRequest $request
     * @param $subReportId
     * @return \Response
     */
    public function addCustomColumn(CustomColumnRequest $request, $subReportId)
    {
        $reportId = SubReport::whereId($subReportId)->value("report_id");

        $type = BaseReportController::getTypeByReportId($reportId);

        // converting all special characters into underscores
        $key = $this->getKeyByString($request->name);

        // sanitizing the equation
        $request->equation = trim(preg_replace('/\s+/', '', $request->equation));

        try{
            $availableShortCodes = $this->getClassObjectByType($type)->availableShortCodes;

            (new BaseReportController)->validateEquation($request->equation, $availableShortCodes);

            // convert name into underscores
            $column = ReportColumn::updateOrCreate(['id' => $request->id], ['key'=> $key, 'label'=> $request->name,
                'equation'=> $request->equation, 'is_visible'=> true, 'is_custom'=>true,
                'is_timestamp'=> (bool) $request->is_timestamp, 'sub_report_id' => $subReportId, 'timestamp_format' => $request->timestamp_format
            ]);

            // if order is not present, make its id as its order
            if(!$column->order){
                $column->order = $column->id;
                $column->save();
            }

            return successResponse(Lang::get('lang.updated_successfully'));

        } catch (Exception $e){
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Converts string into a valid apha mumeric key by replacing
     * special characters into underscores
     * @param  string $name
     * @return string
     */
    private function getKeyByString(string $name) : string
    {
        $strRandom = str_random(5);

        // appending it with a random string to make key unique and unpredictable
        $name = strtolower($name).'_' . $strRandom;

        return preg_replace("/[^a-zA-Z]/", "_", $name);
    }

    /**
     * Gets class object by its type
     * @param string $type
     * @return object
     */
    private function getClassObjectByType(string $type)
    {
        switch ($type){
            case 'management_report':
            case 'management-report':
                return new ManagementReportController(new Request);

            case 'top-customer-analysis':
                return new TopCustomerAnalysisController(new Request);

            case 'agent-performance':
            case 'team-performance':
            case 'department-performance':
                return new PerformanceController(new Request);
        }
    }

    /**
     * NOTE : has to be moved to BaseReportController
     * Trigger management report export job
     * @param Request $request
     * @param $reportId
     * @return Response
     */
    public function triggerReportExport(Request $request, $reportId)
    {
        $activeQueue = QueueService::where('status', 1)->first();

        if ($activeQueue) {
            $short = $activeQueue->short_name;
            if ($short == 'sync') {
                return errorResponse(Lang::get('report::lang.report_export_not_supported_with_sync'));
            }

            app('queue')->setDefaultDriver($short);
        }

        try {
            $this->dispatchExportJobByType($request->all(), $reportId);

            return successResponse(Lang::get('report::lang.report_export_successful'));
        } catch (Exception $e) {
            return errorResponse(Lang::get('report::lang.report_export_failed'));
        }
    }

    /**
     * Exports report by its id
     * @param array $filterParams
     * @param $reportId
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    private function dispatchExportJobByType(array $filterParams, $reportId)
    {
        $reportType = Report::where("id", $reportId)->value("type");

        if(!$reportType){
            throw new \UnexpectedValueException("Invalid Report Id");
        }

        $reportDownload = $this->createReportExport($reportType, $reportId);

        switch ($reportType){
            case 'management-report':
                return ManagementReportExport::dispatch($filterParams, $reportDownload, Auth::user()->id)->onQueue('reports');

            case 'agent-performance':
            case 'team-performance':
            case 'department-performance':
                return PerformanceReportExport::dispatch($filterParams, $reportDownload, Auth::user()->id)->onQueue('reports');
        }
    }

    /**
     * Create fresh report export
     * @param string $reportType
     * @param int $reportId
     * @return ReportDownload instance
     */
    private function createReportExport(string $reportType, int $reportId)
    {
        return auth()->user()->reports()->create([
            'file'       => "$reportType-" . faveoDate(null, 'dmYhmi'),
            'ext'        => 'xlsx',
            'type'       => $reportType,
            'report_id'  => $reportId,
            'hash'       => Str::random(60),
            'expired_at' => Carbon::now()->addHours(6)
        ]);
    }

    /**
     * Gets available short codes in management report
     * NOTE: when more reports are rewritten, this method can be generalised to
     * return short codes for reports based on argument.
     * @param string $type
     * @return Response
     */
    public function getReportShortCodes($type = 'management-report')
    {
        return successResponse('', $this->getClassObjectByType($type)->availableShortCodes);
    }

    /**
     * gets the helpdesk in-depth view
     * @param int $reportType
     * @param string $reportId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getReportView($reportType, $reportId)
    {
        if(!Report::where("type", $reportType)->where("id", $reportId)->count()){
            return redirect("/404");
        }

        $reportTitle = Lang::get("report::lang.$reportType");

        return view('report::report', compact('reportType','reportId', 'reportTitle'));
    }

    /**
     * Deletes custom reports
     * @param $reportId
     * @return \HTTP
     */
    public function deleteReport($reportId)
    {
        $report = Report::whereId($reportId)->where("is_default", 0)->first();

        if(!$report){
            return errorResponse(Lang::get("report::lang.invalid_report_id"));
        }

        $report->delete();

        return successResponse(Lang::get("lang.successfully_deleted"));
    }
}