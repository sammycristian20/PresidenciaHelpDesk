<?php


namespace App\FaveoReport\Controllers;

use App\FaveoReport\Exceptions\TypeNotFoundException;
use App\FaveoReport\Structure\Chart;
use App\FaveoReport\Structure\Coordinate;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Lang;

class PerformanceDistribution extends BaseReportController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Available chart types in current report
     * @var array
     */
    private $chartTypes = [
        'first_response_time',
        'average_response_time',
        'resolution_time',
    ];

    public function __construct(Request $request)
    {
        $this->middleware('role.agent');

        $this->request = $request;
    }

    /**
     * Gets time performance distribution report
     * @param int $reportId
     * @return \Response
     */
    public function getTimeReport($reportId)
    {
        try {
            $this->setReportId($reportId, "performance-distribution");

            $charts = [];

            foreach ($this->chartTypes as $chartType) {
                $charts[] = $this->getTimeReportByChartType($chartType);
            }

            return successResponse('', $charts);
        } catch (TypeNotFoundException $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    /**
     * Gets Performance distribution Report
     * @param string $chartType
     * @return Chart
     * @throws TypeNotFoundException
     */
    private function getTimeReportByChartType(string $chartType)
    {
        $chart = $this->getChartObject($chartType);

        $baseQuery = $this->getBaseQueryByFilter($chartType);

        $chartTypeCountQuery = $this->getQueryFromChartTypeValue($chartType);

        $queryForTicketCount = DB::raw('COUNT(DISTINCT(tickets.id)) as chart_value');

        $baseQuery->select($queryForTicketCount, $chartTypeCountQuery, 'tickets.*')
            ->orderBy('chart_key', 'asc')
            ->groupBy('chart_key')->get()
            ->map(function ($element) use (&$chart, $chartType) {
                $coordinate = new Coordinate;
                $coordinate->id = $element->chart_key;
                $coordinate->label = $this->getLabelByChartValue($element->chart_key);
                $coordinate->value = $element->chart_value;
                $coordinate->redirectTo = $this->getRedirectLinkForTimeReport($chartType, $element->chart_key);
                $chart->injectData($coordinate);
            });

        return $chart;
    }

    /**
     * Gets redirect link for time report
     * @param string $chartType
     * @param string $chartKey
     * @return string
     */
    private function getRedirectLinkForTimeReport($chartType, $chartKey) : string
    {
        $baseFilters = $this->request->all();

        $filterTimeKeys = array_keys($this->getIntervalArray());

        // get index of chartKey in $filterTimeKeys
        $indexOfChartKey = array_search($chartKey, $filterTimeKeys);

        $initialValue = $indexOfChartKey === 0 ? 0 : $filterTimeKeys[$indexOfChartKey - 1];

        $finalValue = $chartKey;

        $filterValue = "interval::{$initialValue}~minute~{$finalValue}~minute";

        switch ($chartType){
            case 'first_response_time':
                $baseFilters['first-response-time'] = $filterValue;
                break;

            case 'average_response_time':
                $baseFilters['avg-response-time'] = $filterValue;
                break;

            case 'resolution_time':
                $baseFilters['resolution-time'] = $filterValue;
                $baseFilters['is-resolved'] = 1;
                break;
        }

        return $this->getInboxFilterUrl($chartType, $baseFilters);
    }

    /**
     * Gets base query by its chart type after applying filters
     * @param $chartType
     * @return Builder
     * @throws TypeNotFoundException
     * @throws Exception
     */
    private function getBaseQueryByFilter($chartType)
    {
        $baseQuery = $this->getBaseQueryForTickets($this->request, false);

        return $this->modifyQueryByChartType($chartType, $baseQuery);
    }


    /**
     * Modifies query by its chart type
     * @param string $graphType
     * @param Builder $baseQuery
     * @return Builder
     * @throws TypeNotFoundException
     */
    private function modifyQueryByChartType(string $graphType, Builder &$baseQuery) : Builder
    {

        switch ($graphType) {
            case 'resolution_time':
            case 'avg_resolution_time_trend':
                return $this->modifyQueryByCommonType('resolution_time', $baseQuery);

            case 'average_response_time':
                return $baseQuery->where('average_response_time', '!=', null);

            case 'first_response_time':
            case 'avg_first_response_time_trend':
                return $this->appendThreadQuery('first_response_time', $baseQuery);

            case 'avg_response_time_trend':
                return $this->appendThreadQuery('avg_response_time', $baseQuery);

            default:
                throw new TypeNotFoundException("Invalid chart type passed. Allowed chart types are resolution_time, average_response_time and first_response_time");
        }
    }

    /**
     * Gets chart object
     * @param string $chartType
     * @return Chart
     */
    private function getChartObject(string $chartType) : Chart
    {
        $chart = new Chart;

        $chart->id = $chartType;

        $chart->name = Lang::get('report::lang.'.$chartType);

        $chart->dataLabel = $this->getDataLabelByType($chartType);

        return $chart;
    }

    /**
     * Gets data label by its type
     * @param string $chartType
     * @return array|string|null
     */
    private function getDataLabelByType(string $chartType)
    {
        if (in_array($chartType, ['avg_resolution_time_trend', 'avg_response_time_trend', 'avg_first_response_time_trend', 'performance_time_trend'])) {
            return Lang::get('report::lang.no_of_minutes');
        }

        return Lang::get('report::lang.ticket_count');
    }

    /**
     * Gets interval array for avg time chart
     * @return array
     */
    private function getIntervalArray()
    {
        // ["time value" => "formatted value]
        return [
            15 => '<15 minutes',
            30 => '15-30 minutes',
            60 => '30-60 minutes',
            120 => '1-2 hours',
            240 => '2-4 hours',
            480 => '4-8 hours',
            960 => '8-16 hours',
            1440 => '16-24 hours',
            2880 => '1-2 days',
            5760 => '2-4 days',
            11520 => '4-8 days',
            // simply kept a bigger value in assumption that in a sensible case, value will not go bigger than this
            1000000 => '>8 days',
        ];
    }

    /**
     * Gets query for chart type value with a column name as chart_type_value
     * Formatting SQL query to :
     * CASE WHEN {$columnName} <= 15 THEN 15
     *  WHEN {$columnName} > 15 AND {$columnName} <= 30 THEN 30
     *  WHEN {$columnName} > 30 AND {$columnName} <= 60 THEN 60
     *  WHEN {$columnName} > 60 AND {$columnName} <= 120 THEN 120
     *  WHEN {$columnName} > 120 AND {$columnName} <= 240 THEN 240
     *  WHEN {$columnName} > 240 AND {$columnName} <= 480 THEN 480
     *  WHEN {$columnName} > 480 AND {$columnName} <= 960 THEN 960
     *  WHEN {$columnName} > 960 AND {$columnName} <= 1440 THEN 1440
     *  WHEN {$columnName} > 1440 AND {$columnName} <= 2880 THEN 2880
     *  WHEN {$columnName} > 2880 AND {$columnName} <= 5760 THEN 5760
     *  WHEN {$columnName} > 5760 AND {$columnName} <= 11520 THEN 11520
     *  ELSE 1000000 END) as chart_key"
     * @param string $chartType
     * @return \Illuminate\Database\Query\Expression
     * @throws TypeNotFoundException
     */
    private function getQueryFromChartTypeValue(string $chartType)
    {
        $columnToChartTypeMapper = [
            'resolution_time' => "tickets.resolution_time",
            'average_response_time' => "tickets.average_response_time",
            'first_response_time'=>"ticket_thread.response_time"
        ];

        if(!array_key_exists($chartType, $columnToChartTypeMapper)){
            throw new TypeNotFoundException("Invalid chart type passed. Allowed chart types are resolution_time, average_response_time and first_response_time");
        }

        $sqlStatement = "";
        $columnName = $columnToChartTypeMapper[$chartType];

        $intervalArray = array_keys($this->getIntervalArray());

        foreach ($intervalArray as $index => $value) {

            if ($value == 15)
                $sqlStatement .= "CASE WHEN {$columnName} <= $value THEN $value\n";

            // if greater than 11520, assigning it to a big number instead of string, so that it can be sorted
            elseif ($value == 1000000)
                $sqlStatement .= "ELSE {$value} END";
            else
                $sqlStatement .= "WHEN {$columnName} > {$intervalArray[$index - 1]} AND {$columnName} <= $value THEN $value\n";

        }

        return  DB::raw("($sqlStatement) as chart_key");
    }

    /**
     * Gets chart label  by its value
     * @param $chartValue
     * @return string
     */
    private function getLabelByChartValue($chartValue)
    {
        $timeArray = $this->getIntervalArray();

        if(!array_key_exists($chartValue, $timeArray)){
            throw new \InvalidArgumentException("Invalid chart value passed");
        }

        return $timeArray[$chartValue];
    }


    /**
     * Gets performance distribution trend report
     * @param $reportId
     * @return \HTTP
     * @throws TypeNotFoundException
     */
    public function getTrendReport($reportId)
    {
        $this->setReportId($reportId, "performance-distribution");

        $format = $this->request->view_by;

        if (!in_array($format, ['day','week','month','year'])) {
            return errorResponse(Lang::get('report::lang.invalid_view_by_provided_possible_values_are_day_week_month_year'));
        }

        $chartType = 'performance_time_trend';

        $chartObject = $this->getChartObject($chartType);

        $chartObject->dataLabel = Lang::get('report::lang.minutes');

        $chartObject->categoryLabel = Lang::get('report::lang.time_period');

        $chartObject->injectChart($this->getTrendAvgFirstResponseReport($format));

        $chartObject->injectChart($this->getTrendAvgResponseReport($format));

        $chartObject->injectChart($this->getTrendResolutionReport($format));

        return successResponse('', $chartObject);
    }

    /**
     * Gets trend report for performance distribution
     * @param string $format
     * @return Chart
     * @throws TypeNotFoundException
     */
    private function getTrendResolutionReport(string $format)
    {
        $chartType = 'avg_resolution_time_trend';

        // get base query based on filters
        $baseQuery = $this->getBaseQueryByFilter($chartType);

        $valueQuery = DB::raw("ROUND(AVG(tickets.resolution_time)) as chart_value");

        $dateQuery = $this->dateQuery($format, 'tickets.closed_at', 'chart_key');

        $sortQuery = $this->dateQuery('sortable', 'tickets.closed_at', 'sort_referer');

        // group base query by data to find
        $baseQuery = $baseQuery->select($valueQuery, 'tickets.*', $dateQuery, $sortQuery);

        return $this->getChartData($baseQuery, $chartType);
    }

    /**
     * Gets trend report for performance distribution
     * @param string $format
     * @return Chart
     * @throws TypeNotFoundException
     */
    private function getTrendAvgFirstResponseReport(string $format)
    {
        $chartType = 'avg_first_response_time_trend';

        // get base query based on filters
        $baseQuery = $this->getBaseQueryByFilter($chartType);

        $dateQuery = $this->dateQuery($format, 'ticket_thread.created_at', 'chart_key');

        $valueQuery = DB::raw("ROUND(AVG(ticket_thread.response_time)) as chart_value");

        $sortQuery = $this->dateQuery('sortable', 'ticket_thread.created_at', 'sort_referer');

        // group base query by data to find
        $baseQuery = $baseQuery->select($valueQuery, 'tickets.*', $dateQuery, $sortQuery);

        return $this->getChartData($baseQuery, $chartType);
    }

    /**
     * Gets trend report for performance distribution
     * @param string $format
     * @return Chart
     * @throws TypeNotFoundException
     */
    private function getTrendAvgResponseReport(string $format)
    {
        $chartType = 'avg_response_time_trend';

        // get base query based on filters
        $baseQuery = $this->getBaseQueryByFilter($chartType);

        $dateQuery = $this->dateQuery($format, 'ticket_thread.created_at', 'chart_key');

        $valueQuery = DB::raw("ROUND(AVG(ticket_thread.response_time)) as chart_value");

        $sortQuery = $this->dateQuery('sortable', 'ticket_thread.created_at', 'sort_referer');

        $baseQuery = $baseQuery->select($valueQuery, 'tickets.*', $dateQuery, $sortQuery);

        return $this->getChartData($baseQuery, $chartType);
    }

    /**
     * Gets populated chart data for current report
     * ASSUMPTION: while querying key and value of the coordinate must be name as chart_key and chart_value
     * @param $baseQuery
     * @param $chartType
     * @return Chart
     */
    private function getChartData($baseQuery, $chartType)
    {
        $chart = $this->getChartObject($chartType);

        $baseQuery->groupBy('chart_key')
            ->orderBy('sort_referer', 'asc')
            ->get()->map(function ($element) use (&$chart, $chartType) {
                $coordinate = new Coordinate;
                $coordinate->id = $element->chart_key;
                $coordinate->label = $element->chart_key;
                $coordinate->value = $element->chart_value;
                $coordinate->redirectTo = null;
                $chart->injectData($coordinate);
            });

        return $chart;
    }
}
