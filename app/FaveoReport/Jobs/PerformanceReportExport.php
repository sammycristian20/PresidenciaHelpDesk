<?php

namespace App\FaveoReport\Jobs;

use App\FaveoReport\Controllers\ApiReportController;
use App\FaveoReport\Controllers\PerformanceController;
use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\ReportDownload;
use App\FaveoReport\Models\SubReport;
use Exception;
use Illuminate\Support\Collection;
use Lang;
use Config;
use Logger;

class PerformanceReportExport extends BaseTableExport
{

    private $reportType;

    /**
     * Create a new job instance.
     * @param array $request
     * @param ReportDownload $report
     * @param int $userId
     * @param Collection $columns
     * @param int $page
     * @param string $fileName
     * @param int $recordsInCurrentFile
     */
    public function __construct(array $request, ReportDownload $report, int $userId, Collection $columns = null, int $page = 1, string $fileName = "", $recordsInCurrentFile = 0)
    {
        parent::__construct($request, $report, $userId, $columns, $page, $fileName, $recordsInCurrentFile);

        $this->reportType = Report::whereId($this->report->report_id)->value("type");
    }

    /**
     * gets the list of columns
     * @return Collection
     */
    protected function getColumns() : Collection
    {
        // since in performance reports we only have one sub report, so it can be handled by picking the first sub report
        $subReport = SubReport::where("report_id", $this->report->report_id)->first();

        return (new ApiReportController)->getSubReportColumns($subReport);
    }

    /**
     * Gets ticket by passed parameters
     * @return array
     * @throws Exception
     */
    protected function getRows() : array
    {
        // old one used to use page but new one uses page. Based on page,
        // update limit and page in the request
        $this->request = $this->request->merge(['page' => $this->page, 'limit'=> $this->limit]);

        $response = $this->getReportDataByType($this->request);

        return json_decode($response->getContent())->data->data;
    }

    /**
     * Gets
     * @param $request
     * @return \Response
     * @throws Exception
     */
    private function getReportDataByType($request)
    {
        $classObject = new PerformanceController($request);

        switch ($this->reportType){

            case 'agent-performance':
                return $classObject->getAgentPerformanceData($this->report->report_id);

            case 'team-performance':
                return $classObject->getTeamPerformanceData($this->report->report_id);

            case 'department-performance':
                return $classObject->getDepartmentPerformanceData($this->report->report_id);

            default:
                throw new Exception('invalid report type');
        }
    }

    /**
     * Gets link to ticket
     * @param object $row
     * @return string
     * @throws Exception
     */
    protected function getLink(object $row) : string
    {
        switch ($this->reportType) {

            case 'agent-performance':
                $relativeUrl = 'agent';
                break;

            case 'team-performance':
                $relativeUrl= 'assign-teams';
                break;

            case 'department-performance':
                $relativeUrl = 'department';
                break;

            default:
                throw new Exception('invalid report type');
        }

        return Config::get('app.url').'/'.$relativeUrl."/".$row->id;
    }

    /**
     * gets link label in report
     * @return string
     * @throws Exception
     */
    protected function getLinkText() : string
    {
        switch ($this->reportType) {

            case 'agent-performance':
                return Lang::get('report::lang.click_here_to_view_agent');

            case 'team-performance':
                return Lang::get('report::lang.click_here_to_view_team');

            case 'department-performance':
                return Lang::get('report::lang.click_here_to_view_department');

            default:
                throw new Exception('invalid report type');
        }
    }

    /**
     * Gets the name of the column which will have link to the record
     * @return string
     * @throws Exception
     */
    protected function getLinkColumnName() : string
    {
        switch ($this->reportType) {

            case 'agent-performance':
                return Lang::get('report::lang.agent_link');

            case 'team-performance':
                return Lang::get('report::lang.team_link');

            case 'department-performance':
                return Lang::get('report::lang.department_link');

            default:
                throw new Exception('invalid report type');
        }
    }
}
