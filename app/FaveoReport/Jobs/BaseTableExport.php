<?php

namespace App\FaveoReport\Jobs;

use App\Events\ReportExportEvent;
use App\Facades\Attach;
use App\FaveoReport\Controllers\ApiReportController;
use App\FaveoReport\Exports\ManageReportExport;
use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\ReportDownload;
use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Ticket\TicketFilter;
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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

abstract class BaseTableExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * all request parameters
     * @var Request
     */
    protected $request;

    /**
     * Report Model instance
     * @var ReportDownload
     */
    protected $report;

    /**
     * Number of records that has to be fetched
     * @var int
     */
    protected $limit;

    /**
     * list of columns in management report
     * @var array
     */
    protected $columns;

    /**
     * The page which is getting exported.
     * for eg. if limit is 10, page 1 means 1-10, page 2 means 11-20
     * @var int
     */
    protected $page;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 180;

    /**
     * User Id if the person who crated the job
     * @var int
     */
    protected $userId;

    /**
     * Timezone of the agent
     * @var string
     */
    protected $agentTimezone;

    /**
     * folder path of report excel
     * @var string
     */
    protected $reportFilePath;

    /**
     * Name of the file
     * @var string
     */
    protected $fileName;

    /**
     * how many records a file can accomodate
     * @var int
     */
    protected $recordsInCurrentFile;

    /**
     * Columns which are to be formatted as timestamp
     * @var object
     */
    protected $timestampColumns;

    protected $upperLimit;

    /**
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
        $this->report         = $report;
        $this->limit          = 100;
        $this->agentTimezone  = agentTimezone();
        $this->reportFilePath = 'reports/export/' . $this->report->file;
        $this->setRequest($request);
        $this->setColumns($columns);
        $this->page = $page;
        $this->userId = $userId;
        $this->fileName = $fileName ? : $this->report->file.'_00001'; //after 9 will give 0; so increasing number of zeroes
        $this->recordsInCurrentFile = $recordsInCurrentFile;
        $this->upperLimit = (new CommonSettings)->getOptionValue('reports_records_per_file')->first()->option_value;
    }

    /**
     * Exports records to excel
     * @param array $columns
     * @param array $rows
     * @return bool
     */
    public function handle()
    {
        // increasing memory and timeout to maximum possible to reduce the chances to out of memory issue
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);

        // logging user so that his relative tickets can be queried
        Auth::loginUsingId($this->userId);

        $columns = $this->getColumnsAsLabelList();

        $limitedRows = [];

        $iterativeIndex = $this->upperLimit / $this->limit;

        for ($i = 0; $i< $iterativeIndex; $i++) {
            $limitedRows[] = $this->getRows();
            $this->page++;
        }

        $rows = call_user_func_array('array_merge', $limitedRows);

        if (($this->page != 1 && count($rows) == 0) || $this->report->is_completed == 1) {
            $this->afterReportExportCompleted();
            return true;
        }
        $this->createExcelExport($columns, $rows);

        // Trigger next part of export job
        $this->triggerChildExportJob();
    }


    /**
     * Set columns for the report
     * @param Collection|null $columns
     * @return void
     */
    protected function setColumns(Collection $columns = null) : void
    {
        if ($columns) {
            $this->columns = $columns;
        } else {
            $allColumns = $this->getColumns();

            // filtering just visible ones

            // keeping it in another collection to reindex
            $this->columns = collect($allColumns->filter(function ($column) {
                return (bool) $column->is_visible;
            })->values());
        }

        $this->timestampColumns = $this->columns->filter( function ($column) {
            return (bool) $column->is_timestamp;
        });
    }

    /**
     * Gets column as label list
     * For eg. if $this->column is [['key'=>'test_key', 'label'=>'test_label']],
     * this method will return ['test_label']
     * @return array
     */
    protected function getColumnsAsLabelList() : array
    {
        $columnsAsLabelList = $this->columns->map(function ($column) {
            return $column->label;
        })->toArray();

        $columnsAsLabelList[] = $this->getLinkColumnName();

        return $columnsAsLabelList;
    }

    /**
     * Set request for the report
     * @param Request $request
     * @return void
     */
    private function setRequest(array $request) : void
    {
        // adding filter_id to the request
        $filterId = Report::find($this->report->report_id)->filter->id;

        $request['filter_id'] = $filterId;

        $this->request = (new Request)->replace($request);
    }

    /**
     * The job failed to process.
     * @param Exception $exception
     * @throws Exception
     */
    public function failed(Exception $exception)
    {
        // Delete exported file from file system if exists
        $this->removeGeneratedReport();

        // Delete report form database
        $this->report->delete();

        Logger::exception($exception, 'report');
    }

    /**
     * Create new report excel file
     *
     * @param array $reportColumns Report columns
     * @param collection Tickets collection
     * @return void
     */
    protected function createExcelExport(array $reportColumns, array $tickets)
    {
        try {
            $this->createNewSheet($reportColumns, $tickets, $this->fileName);
        } catch (Exception $e) {
            Logger::exception($e, 'report');
        }
    }

    private function getTimeStampCoordinates($rowIndex)
    {
        $coordinatesAndDateValues = [];

        $columnsArray = array_column($this->columns->toArray(), 'key');

        foreach ($this->timestampColumns as $index => $column) {

            $colIndex = array_search($column->key, $columnsArray);

            $coordinatesAndDateValues[$index]['coordinate'] = Coordinate::stringFromColumnIndex($colIndex + 1). $rowIndex;

            $coordinatesAndDateValues[$index]['date_in_excel'] = $this->getExcelFormatByCarbonFormat($column->timestamp_format);
        }

        return $coordinatesAndDateValues;
    }

    /**
     * @param $tickets
     * @param $columnCount
     * @return array
     */
    private function getReportRowInformation($tickets, $columnCount): array
    {
        $reportRow = $rowColumnIndex = $columnIndexForDate = [];

        foreach ($tickets as $index => $row) {
            $reportRow[$index] = $this->getReportRow($row);

            $reportRow[$index][$this->getLinkColumnName()] = $this->getLinkText();

            $rowIndex = $index + 2;

            $rowColumnIndex[$index]['coordinate'] = Coordinate::stringFromColumnIndex($columnCount) . $rowIndex;

            $rowColumnIndex[$index]['link'] = $this->getLink((object) $row);

            $rowColumnIndex[$index]['date'] = $this->getTimeStampCoordinates($rowIndex);
        }

        return compact('reportRow', 'rowColumnIndex');
    }

    /**
     * Creates a new sheet
     * @param array $reportColumns
     * @param array $tickets
     * @param string $fileName
     * @return void
     */
    protected function createNewSheet(array $reportColumns, array $tickets, string $fileName) : void
    {
        $rowsInformation = $this->getReportRowInformation($tickets, count($reportColumns));

        array_unshift($rowsInformation['reportRow'], $reportColumns);

        $this->fileName++;

        Excel::store(
            new ManageReportExport($rowsInformation),
            "{$this->reportFilePath}/{$fileName}.{$this->report->ext}",
            FileSystemSettings::value('disk')
        );
    }

    /**
     * Gets excel date form by carbon's format
     * @param string $carbonFormat
     * @return string
     */
    private function getExcelFormatByCarbonFormat(string $carbonFormat)
    {
        // maintaining a seperate mapping for Carbon to Excel.
        $dateFormatMapper = [
            "F j, Y g:i  a" => "mmmm dd, yyyy h:mm AM/PM",
            "Y-m-d g:i a" => "yyyy-mm-dd h:mm AM/PM",
            "d-m-Y g:i a" => "dd-mm-yyyy h:mm AM/PM",
            "m-d-Y g:i a" => "mm-dd-yyyy h:mm AM/PM",
            "F j, Y" => "mmmm dd, yyyy",
            "Y-m-d"=> "yyyy-mm-dd",
            "d-m-Y"=> "dd-mm-yyyy",
            "m-d-Y"=> "mm-dd-yyyy",
            "g:i  a"=>"h:mm AM/PM",
        ];

        return $dateFormatMapper[$carbonFormat];
    }

    /**
     * Trigger clild export job
     *
     * @return void
     */
    protected function triggerChildExportJob()
    {
        try {
            // Setting queue driver
            (new PhpMailController)->setQueue();

            self::dispatch($this->request->all(), $this->report, $this->userId, $this->columns, $this->page + 1, $this->fileName, $this->recordsInCurrentFile + $this->limit)
                ->onQueue('reports')
                ->delay(now()->addSeconds(2));
        } catch (Exception $e) {
            Logger::exception($e, 'report');
        }
    }

    /**
     * Store exported report details
     *
     * @return Stored report model instance
     */
    protected function markExportAsCompleted()
    {
        $this->report->expired_at   = Carbon::now()->addHours(6);

        $this->report->is_completed = 1;

        return $this->report->save();
    }

    /**
     * Remove generated file
     *
     * @return void
     */
    protected function removeGeneratedReport()
    {
        Attach::delete($this->reportFilePath);
    }

    /**
     * Post report export actions
     *
     * @return void
     */
    protected function afterReportExportCompleted()
    {
        try {
            $this->markExportAsCompleted();

            event(new ReportExportEvent($this->report));
        } catch (Exception $e) {
            Logger::exception($e, 'report');
        }
    }

    /**
     * Get row data out of o object by formatting it
     * @param object $row
     * @return array Ticket data
     */
    protected function getReportRow(object $row) : array
    {
        // Set all fields as key and set value to null
        $reportRow = [];
        // in column variable, we have key that has to be matched in ticket array
        foreach ($this->columns as $column) {
            // need to handle case where value exists inside name parameter.
            // something like key.path
            // dot notation will tell that if it is location
            // $keyInTicketObject is in dot notation.
            $reportRow[$column->label] = strip_tags(getValueByDotNotation($row, $column->key));

            // if timestamp, it should be converted into agent timezone and human readable format
            // check if date is valid. for non custom column, we are formatting to human readable.
            // custom columns are already formatted
            // if ($column->is_timestamp && $reportRow[$column->label]) {
            //     $timeInAgentTimezone = changeTimezoneForDatetime($reportRow[$column->label], 'UTC', $this->agentTimezone);
            //     // formatting it in human readable form
            //     $reportRow[$column->label] = \PHPExcel_Shared_DatPHPExcel_Shared_DateePHPExcel_Shared_Date::PHPToExcel($timeInAgentTimezone);
            // }
        }

        return array_values($reportRow);
    }

    /**
     * Gets link of individual row
     * @param object $row
     * @return string
     */
    abstract protected function getLink(object $row) : string;

    /**
     * Gets text visible on the clickable link
     * @return string
     */
    abstract protected function getLinkText() : string;

    /**
     * Gets column name of clickable link
     * @return string
     */
    abstract protected function getLinkColumnName() : string;

    /**
     * Sets columns for the report
     * @return Collection
     */
    abstract protected function getColumns() : Collection;

    /**
     * Gets list of rows to be exported
     * @return array
     */
    abstract protected function getRows() : array;
}
