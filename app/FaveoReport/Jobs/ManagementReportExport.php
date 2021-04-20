<?php

namespace App\FaveoReport\Jobs;

use App\Events\ReportExportEvent;
use App\FaveoReport\Controllers\ApiReportController;
use App\FaveoReport\Models\ReportDownload;
use App\FaveoReport\Models\SubReport;
use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Settings\CommonSettings;
use Carbon\Carbon;
use Excel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Lang;
use App\FaveoReport\Controllers\ManagementReportController;
use Config;
use PHPExcel_Cell;
use Logger;
use Auth;

class ManagementReportExport extends BaseTableExport
{

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
    }

    /**
     * gets the list of columns
     * @return Collection
     */
    protected function getColumns() : Collection
    {
        // since in management report we only have one sub report, so it can be handled by picking the first sub report
        $subReport = SubReport::where("report_id", $this->report->report_id)->first();

        return (new ApiReportController)->getSubReportColumns($subReport);
    }

    /**
     * Gets ticket by passed parameters
     * @return array
     * @throws \App\FaveoReport\Exceptions\VariableNotFoundException
     */
    protected function getRows() : array
    {
      // old one used to use page but new one uses page. Based on page,
      // update limit and page in the request
        $this->request = $this->request->merge(['page' => $this->page, 'limit'=> $this->limit]);

      // get ticket list based on the paramaters
        $response = (new ManagementReportController($this->request))->getManagementReportData($this->report->report_id);

        return json_decode($response->getContent())->data->data;
    }

    /**
     * Gets link to ticket
     * @param object $row
     * @return string
     */
    protected function getLink(object $row) : string
    {
        return Config::get('app.url').'/ticket-conversation-guest'."/".$row->encrypted_id;
    }

    /**
     * gets link label in report
     * @return string
     */
    protected function getLinkText() : string
    {
        return Lang::get('lang.click_here_to_view_ticket');
    }

    /**
     * Gets the name of the column which will have link to the record
     * @return string
     */
    protected function getLinkColumnName() : string
    {
        return Lang::get('lang.ticket_link');
    }
}
