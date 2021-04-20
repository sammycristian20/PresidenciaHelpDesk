<?php

namespace App\FaveoReport\Controllers;

use App\Facades\Attach;
use App\FaveoReport\Exceptions\TypeNotFoundException;
use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\ReportDownload;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Ticket\TicketFilter;
use App\Model\helpdesk\Ticket\Tickets;
use App\Traits\ClickableTicketParam;
use App\User;
use Config;
use DateTime;
use DateTimeZone;
use DB;
use Exception;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use MathParser\StdMathParser;
use MathParser\Interpreting\Evaluator;
use MathParser\Exceptions\UnknownTokenException;
use MathParser\Exceptions\UnknownVariableException;
use MathParser\Exceptions\DivisionByZeroException;
use Lang;
use App\FaveoReport\Traits\CustomEquationFunctions;
use App\FaveoReport\Exceptions\VariableNotFoundException;
use App\FaveoReport\Exceptions\NullValueException;
use App\Http\Controllers\Controller;
use App\FaveoReport\Models\ReportColumn;
use Zipper;
use Carbon\Carbon;

/**
 * Contains all methods which will be shared by multiple reports
 * mostly clickablity and export
 */
class BaseReportController extends Controller
{
    use CustomEquationFunctions, ClickableTicketParam;

    /**
     * Custom columns of management report
     * @var Collection
     */
    protected $visibleColumns;

    /**
     * Type of report (it will be used for querying columns for specific report)
     * @var string
     */
    protected $type = 'management_report';

    /**
     * Id of the report in request
     * @var int
     */
    protected $reportId;

    /**
     * Sub report if of the report
     * @var int
     */
    protected $subReportId;

    /**
     * The person who is trying to access reports
     * @var User
     */
    protected $user;


    /**
     * Converts Boolean(0/1) to human readable format (No/Yes)
     * @return string
     */
    protected function convertBooleanToHumanReadable(bool $param = null)
    {
        if($param === null){
            return "---";
        }

        return $param ? 'Yes' : 'No';
    }

    /**
     * Sets custom columns property
     * @param $reportId
     * @return void
     */
    protected function setVisibleColumns()
    {
        // if sub report id is not passed, it should take first sub report of the report
        if(!$this->subReportId){
            $this->subReportId = Report::where("id", $this->reportId)->select("id")->first()->subReports->first()->id;
        }

        $this->visibleColumns = $this->visibleColumns ? :
            ReportColumn::where('is_visible', 1)->where('sub_report_id', $this->subReportId)->orderBy('order', 'asc')->get();
    }

    /**
     * gives back value of a field based on the equation.
     * Whatever variables present in the equation, must be present in the rowObject,
     * @param string $equation
     * @param object $rowObject
     * @param bool $isValidating
     * @return string|int
     * @throws VariableNotFoundException
     */
    protected function getParsedEquationValue(string $equation, object $rowObject, bool $isValidating = false)
    {
        try {
            $parsedEquation = $this->populateEquation($equation, $rowObject);

            $parser = new StdMathParser();

            $instance = $parser->parse($parsedEquation);

            $evaluator = new Evaluator();

            // rounding the result upto 2 decimal places
            return round($instance->accept($evaluator), 2);
        } catch (UnknownTokenException | UnknownVariableException $e) {
            // this is a user level exception which has to ve visible to the user
            throw new Exception(Lang::get('report::lang.invalid_equation'));
        } catch (DivisionByZeroException $e) {
            return 'infinity';
        } catch (VariableNotFoundException $e) {
            if ($isValidating) {
                throw $e;
            }
            return '';
        } catch (NullValueException $e) {
            return '';
        }
    }

    /**
     * populates equation which corresponding values from row object
     * @param string $equation
     * @param object $rowObject
     * @return string
     * @throws NullValueException
     * @throws VariableNotFoundException
     */
    private function populateEquation(string $equation, object $rowObject) : string
    {
        // this gets calculated on every ticket call. its better to
        // object key cannot have special characters. so key can be replaced by double underscores
        // but in case of business hour implementation we are preparing a function, so cannot do
        // that too
        // if business hour was a function bh(arg1, arg2), if this could result in calling
        // of a function, problem will be solved
        $equationFunctions = $this->getEquationFunctions($equation);

        // handle function and replace it with value
        foreach ($equationFunctions as $function) {
            $this->updateEquationForFunctions($equation, $rowObject, $function);
        }

        $equationVariables = $this->getEquationVariables($equation);

        foreach ($equationVariables as $variable) {
            $this->updateEquationForVariables($equation, $rowObject, $variable);
        }

        return $equation;
    }

    /**
     * Updates equation by replacing functions with its equivalent value
     * @param string $equation
     * @param object $rowObject
     * @param string $variable
     * @return void
     * @throws NullValueException
     * @throws VariableNotFoundException
     */
    private function updateEquationForVariables(string &$equation, object $rowObject, string $variable)
    {
        $keyInObject = str_replace(':', '', $variable);

        // if pattern has status_change, look for status change
        // in other cases, simply replace the parameter with te value
        // value to be replaced
        // if any variable is not present in the object, it will
        // throw an exception that given variable is not present in
        // the object
        if (!isset($rowObject->$keyInObject)) {
            // this is a developer level exception, which should not be reaching
            // to the user
            throw new VariableNotFoundException("Variable $keyInObject is not present in given object");
        }

        $value = $this->getFormattedValue($rowObject->$keyInObject);

        $equation = str_replace($variable, $value, $equation);
    }

    /**
     * Updates equation by replacing functions with its equivalent value
     * @param string &$equation
     * @param object $row
     * @param string $function
     * @return void
     * @throws NullValueException
     */
    private function updateEquationForFunctions(string &$equation, object $row, string $function)
    {
        $methodName = $this->getMethodName($function);

        $arguments = $this->getFunctionArguments($function);

        // adding row to the arguments, so that those functions can use it to
        // extract data
        $arguments[] = $row;

        // if does exists, we call that function
        // this function will need ticket Id as extra argument as ticket Id so that it can query
        $value = call_user_func_array([$this, $methodName], $arguments);

        $value = $this->getFormattedValue($value);

        $equation = str_replace($function, $value, $equation);
    }

    /**
     * Gets method name from function call.
     * for eg. @last_status_change($argOne)
     * @param string $function
     * @return mixed|string|string[]|null
     * @throws Exception
     */
    private function getMethodName(string $function)
    {
        // call that function
        // if these functions exists in the class, it should call,
        // otherwise return 0
        $methodName = preg_replace('/\(.*?\)/', '', $function);
        $methodName = str_replace('@', '', $methodName);

        // convert method into camelcase
        $methodName = $this->camelize($methodName);

        if (!method_exists($this, $methodName)) {
            throw new Exception(Lang::get('report::lang.invalid_equation'));
        }

        return $methodName;
    }

    /**
     *
     * @param  string $input
     * @return string
     */
    private function camelize(string $input)
    {
        return str_replace('_', '', lcfirst(ucwords($input, '_')));
    }

    /**
     * Handles operations that has to be done after value update happens
     * @param string|int\null $value
     * @return string|int
     * @throws NullValueException
     */
    private function getFormattedValue($value)
    {

        // if value is null, throw exception which can be caught on parent and return value --
        // if value is an empty string, it still is considered as null
        if ($value === null || strip_tags($value) === "") {
            // this exception is meant for developers, not users
            throw new NullValueException("Null value encountered in equation");
        }

        // if it is a datetime, convert that into timestamp
        if ($value instanceof Carbon) {
            return $value->timestamp;
        }

        //  stripping tags so that hyperlink values can be handled
        return strip_tags($value);
    }

    /**
     * Gets function arguments by regex
     * @param string $functionString
     * @return array
     */
    private function getFunctionArguments(string $functionString) : array
    {
        preg_match('/\(.*?\)/', $functionString, $arguments);
        // removing function brackets
        $arguments = str_replace(['(', ')', ''], '', $arguments[0]);

        // making an array by exploding by comma
        return explode(',', $arguments);
    }

    /**
     * Gets equation variables out of the equation
     * @param string $equation
     * @return array  array of equation variables
     */
    private function getEquationVariables(string $equation) : array
    {
        // since regex returns array of arrays of all the patterns and we only have one
        // pattern, we will extract out the first element of the array
        preg_match_all('/:\w+/', $equation, $matches);

        return array_unique($matches[0]);
    }


    /**
     * Gets equation variables out of the equation
     * @param string $equation
     * @return array  array of function variables out of equation
     */
    private function getEquationFunctions(string $equation) : array
    {
        preg_match_all('/@\w+\(.*?\)/', $equation, $matches);

        return array_unique($matches[0]);
    }

    /**
     * Checks if equation is valid. If not, an exception will be thrown,
     * which can be caught in parent method
     * @param string $equation equation which needs to be validated
     * @param array $allowedVariables list of allowed shortcodes
     * @return bool
     * @throws VariableNotFoundException
     */
    public function validateEquation(string $equation, array $allowedVariables)
    {
        // check if any other variable is present in the equation other than
        // allowed ones, if yes, invalid
        // replace all variables with a normal number (lets say 1 and check if it is a valid expression)
        // make an object of out allowed variables and pass it for further check
        // if equation contains a variable which is not there in that object,
        // it itself will throw an exception
        $object = (object)[];

        foreach ($allowedVariables as $variable) {
            $keyInObject = str_replace(':', '', $variable);

            // just giving some value for it to get become an object that can be validated
            // as an airthmetic equation
            $object->$keyInObject = 1;
        }

        return (bool) $this->getParsedEquationValue($equation, $object, true);
    }

    /**
     * Appends custom column data
     * @param object $object $object
     * @param $reportId
     * @throws VariableNotFoundException
     */
    protected function appendCustomColumnsData(object &$object)
    {
        $this->setVisibleColumns();

        foreach ($this->visibleColumns as $column) {
            $key = $column->key;

            if ($column->is_custom) {
                // if custom column, its equation will be parsed, else same data
                $object->$key = $this->getParsedEquationValue($column->equation, $object);

                if ($column->is_timestamp && $object->$key) {

                    // converting into datetime string
                    $object->$key = $object->$key ? Carbon::createFromTimestamp($object->$key)->toDateTimeString() : '';
                }
            }
        }
    }

    /**
     * Sets current page
     * @param int $page current page
     * @return void
     */
    protected function setCurrentPage(int $page) : void
    {
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
    }


    /**
     * Download generated report export file
     * @param String $hash Hash generated of the file
     * @return Resource File resource if existis
     * @throws Exception
     */
    public function downloadReport($hash)
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

        $defaultDisk = FileSystemSettings::value('disk');

        $reportDirectory = 'reports/export/' . $report->file;

        if (is_null($report) || !Attach::exists($reportDirectory, $defaultDisk)) {
            return response()->view('errors/410', [], FAVEO_INVALID_URL_CODE);
        }

        $filesToZip = array_column(\Storage::disk($defaultDisk)->listContents($reportDirectory), 'path');

        array_walk($filesToZip, function (&$value) use ($defaultDisk) {
            if ($defaultDisk === 's3') {
                $value = 's3://' . env('AWS_BUCKET') . "/$value";
            } else {
                $value = config("filesystems.disks.{$defaultDisk}.root") . DIRECTORY_SEPARATOR . $value;
            }
        });

        return \Zip::create("{$report->file}.zip", $filesToZip);
    }

    /**
     * Modifies query according to type
     * NOTE; This method presents types which are common among reports. If a type is not present here but
     * will be used by an specific report, it should be handled in parent class instead
     * @param string $type
     * @param Builder $baseQuery
     * @return Builder
     * @throws TypeNotFoundException
     */
    protected function modifyQueryByCommonType(string $type, Builder &$baseQuery) : Builder
    {
        // NOTE : if a filter is selected which says status->open, now in resolved there should be closed tickets
        // to handle that we need AND logic, for that its not getting passed in ticket-list parameters but writing it separately

        // received_tickets, resolved_tickets, unresolved_tickets, reopened_tickets, is_response_sla_met, is_resolution_sla_met
        switch ($type) {
            case 'resolved_tickets':
                // where tickets are closed
                return $baseQuery->where('closed', 1);

            case 'unresolved_tickets':
                // where tickets are not closed
                // all statuses which are not
                return $baseQuery->where('closed', 0);

            case 'reopened_tickets':
                // where reopened is true
                return $baseQuery->where('reopened', '>', 0);

            case 'has_response_sla_met':
                // where is_response_sla is true
                return $baseQuery->where('is_response_sla', 1);

            case 'has_resolution_sla_met':
                // where is_resolution_sla is true
                return $baseQuery->where('is_resolution_sla', 1);

            case 'resolution_time':
                return $baseQuery->where('resolution_time', '!=', null)->where('closed', 1);

            case 'received_tickets':
                return $baseQuery;

            default:
                throw new TypeNotFoundException('Invalid type passed. Allowed type are : resolved_tickets, unresolved_tickets, 
                    reopened_tickets, has_response_sla_met, has_resolution_sla_met, received_tickets');
        }
    }

    /**
     * Appends thread query to ticket query by type
     * @param string $type
     * @param Builder $baseQuery
     * @return mixed
     * @throws TypeNotFoundException
     */
    protected function appendThreadQuery($type, &$baseQuery)
    {
        $baseQuery = $baseQuery->join('ticket_thread', 'tickets.id', '=', 'ticket_thread.ticket_id')
            ->where('ticket_thread.is_internal', 0);

        switch ($type){
            case "first_response_time":
                return $baseQuery->where('ticket_thread.thread_type', '=', 'first_reply')
                    ->where('ticket_thread.response_time', '!=', null)
                    ->where('ticket_thread.poster', 'support');

            case "avg_response_time":
                return $baseQuery->where('ticket_thread.response_time', '!=', null)
                    ->where('ticket_thread.poster', 'support');

            case "client_responses":
                return $baseQuery->where('ticket_thread.poster', '=', 'client');


            case "agent_responses":
                return $baseQuery->where('ticket_thread.poster', '=', 'support');

            default:
                throw new TypeNotFoundException("Invalid chart type passed. Allowed chart types are first_response_time and avg_response_time");
        }
    }

    /**
     * Gets hyperlink values for reports. It adds filters only report specific data. Additional filter has to be passed
     * in baseFilters
     * @param $key
     * @param array $baseFilters
     * @return string
     */
    protected function getInboxFilterUrl($key, $baseFilters = [])
    {

        switch ($key) {
            case "reopened_tickets":
                $baseFilters['reopened'] = 1;
                break;

            case "tickets_with_response_sla_met":
            case "has_response_sla_met":
                $baseFilters['has-response-sla-met'] = 1;
                break;

            case "tickets_with_resolution_sla_met":
            case "has_resolution_sla_met":
                $baseFilters['has-resolution-sla-met'] = 1;
                break;

            case "resolved_tickets":
                $baseFilters['is-resolved'] = 1;
                break;

            case "unresolved_tickets":
                $baseFilters['is-resolved'] = 0;
                break;
        }

        // check if base filter has category
        $category = isset($baseFilters['category']) ? $baseFilters['category'] : "all";

        unset($baseFilters['category']);

        return count($baseFilters) ? $this->getInboxUrl($category). '&' . http_build_query($baseFilters) : $this->getInboxUrl($category);
    }


    /***
     * Gets date query in SQL
     * @param string $format 'day', 'week', 'month', 'year
     * @param string $columnString column name in the table. for eg. 'tickets.created_at'
     * @param string $getAs the key by which it will be retrieved. For eg. ticket.created_at as 'getAs'
     * @return \Illuminate\Database\Query\Expression
     * @throws TypeNotFoundException
     * @throws Exception
     */
    protected function dateQuery($format, $columnString, $getAs)
    {
        $currentTimeInAgentTz = new DateTime('now', new DateTimeZone(agentTimeZone()));

        $timezoneOffset = $currentTimeInAgentTz->format('P'); //will give +hh:mm format

        $utcOffset = "+00:00";

        if ($format == 'week') {
            return DB::raw("DATE_FORMAT(DATE_ADD(CONVERT_TZ({$columnString}, '{$utcOffset}', '{$timezoneOffset}'), 
                INTERVAL(1-DAYOFWEEK(CONVERT_TZ({$columnString}, '{$utcOffset}', '{$timezoneOffset}'))) DAY), '%d %b %Y') as {$getAs}");
        }

        $dateFormat = $this->getDateFormat($format);

        return DB::raw("DATE_FORMAT(CONVERT_TZ({$columnString}, '{$utcOffset}', '{$timezoneOffset}'), '{$dateFormat}') as {$getAs}");
    }

    /**
     * Gets mysql date format by string like day, week, month, year
     * @param $format
     * @return string
     * @throws TypeNotFoundException
     */
    private function getDateFormat($format)
    {
        switch ($format) {
            case 'hour':
                return '%H';

            case 'day':
                return '%d %b %Y';

            case 'day_of_week':
                return "%W";

            case 'month':
                return '%b %Y';

            case 'year':
                return '%Y';

            case 'sortable':
                return '%Y %c %d';

            default:
                throw new TypeNotFoundException('Wrong date format encountered. Available formats are day, week, month, year');
        }
    }

    /**
     * Gets date range by passed format. for eg.
     *
     *  - If value is 12 Dec 2019 and format is day (startOfDay~EndOfDay)
     *      it will return 2019-12-12 00:00:00~2019-12-12 23:59:59
     *
     * - If value is 12 Dec 2019 and format is week (startOfWeek~EndOfWeek)
     *      it will return 2019-12-09 00:00:00~2019-12-15 23:59:59
     *
     *  - If value is Dec 2019 and format is month (startOfMonth~EndOfMonth)
     *      it will return 2019-12-01 00:00:00~2019-12-31 23:59:59
     *
     *  - If value is 2019 and format is month (startOfYear~EndOfYear)
     *      it will return 2019-01-01 00:00:00~2019-12-31 23:59:59

     * @param $format
     * @param $value
     * @return string
     * @throws TypeNotFoundException
     */
    protected function getDateRangeByFormat($format, $value)
    {
        switch ($format){

            case 'day':
                return $this->getDateBoundaryByFormat('d M Y', $value, 'day');

            case 'week':
                return $this->getDateBoundaryByFormat('d M Y', $value, 'week');

            case 'month':
                return $this->getDateBoundaryByFormat('M Y', $value, 'month');

            case 'year':
                return $this->getDateBoundaryByFormat('Y', $value, 'year');

            default:
                throw new TypeNotFoundException('Wrong date format encountered. Available formats are day, week, month, year');
        }
    }

    /**
     * Gets date boundary based on format and value. For eg.
     *
     *  - If value is 12 Dec 2019 and format is day (startOfDay~EndOfDay)
     *      it will return 2019-12-12 00:00:00~2019-12-12 23:59:59
     *
     * - If value is 12 Dec 2019 and format is week (startOfWeek~EndOfWeek)
     *      it will return 2019-12-8 00:00:00~2019-12-14 23:59:59
     *
     *  - If value is Dec 2019 and format is month (startOfMonth~EndOfMonth)
     *      it will return 2019-12-01 00:00:00~2019-12-31 23:59:59
     *
     *  - If value is 2019 and format is month (startOfYear~EndOfYear)
     *      it will return 2019-01-01 00:00:00~2019-12-31 23:59:59
     *
     * @param string $carbonFormat
     * @param string $value value of the date. It will be dependent on `getDateFormat` method
     * @param string $boundaryName possible values are day, week, month, year
     * @return string
     */
    private function getDateBoundaryByFormat(string $carbonFormat, string $value, string $boundaryName) : string
    {
        $startBoundaryMethod = "startOf".ucfirst($boundaryName);
        $endBoundaryMethod = "endOf".ucfirst($boundaryName);

        // making start of week as sunday and end of week and saturday
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        return Carbon::createFromFormat($carbonFormat, $value)->$startBoundaryMethod()->format('Y-m-d H:i:s')."~"
            .Carbon::createFromFormat($carbonFormat, $value)->$endBoundaryMethod()->format('Y-m-d H:i:s');
    }

    /**
     * Gets report type by report id
     * @param int $reportId
     * @return string
     */
    public static function getTypeByReportId(int $reportId) : string
    {
        $reportType = Report::whereId($reportId)->value("type");

        if(!$reportType){
            throw new \UnexpectedValueException("Invalid Report Id");
        }

        return $reportType;
    }

    /**
     * @param Request $request
     * @param bool $meta if relations are needed to be loaded in the query. In most of the reports, it is not required except management report
     * @return QueryBuilder
     * @throws Exception
     */
    protected function getBaseQueryForTickets(Request $request, $meta = true) : QueryBuilder
    {
        $parameters = $request->all();

        /**
         * fetching old General parameters from request to be passed in new
         * filtered request so that result can be updated for page, limit, etc.
         */
        $oldParameters = $request->only(['search-query', 'limit', 'page', 'sort-field', 'sort-order']);

        $request->replace(array_merge($parameters, $oldParameters));

        $ticketListControllerObject = new TicketListController;

        $ticketListControllerObject->setRequest($request);

        return $ticketListControllerObject->baseQueryForTickets($meta);
    }

    /**
     * Sets report id
     * @param $reportId
     * @param $reportType
     * @return \HTTP|null
     */
    protected function setReportId($reportId, $reportType)
    {
        // validating report id
        if(!Report::where("type", $reportType)->where("id", $reportId)->count()){
            return errorResponse(Lang::get("report::lang.invalid_report_id"));
        }

        $this->reportId = $reportId;
    }
}
