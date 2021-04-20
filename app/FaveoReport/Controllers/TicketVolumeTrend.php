<?php


namespace App\FaveoReport\Controllers;

use App\FaveoReport\Exceptions\TypeNotFoundException;
use App\FaveoReport\Request\WeekDayTrendRequest;
use App\FaveoReport\Request\TicketTrendRequest;
use App\FaveoReport\Structure\Chart;
use App\FaveoReport\Structure\Coordinate;
use App\FaveoReport\Structure\Widget;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Lang;

class TicketVolumeTrend extends BaseReportController
{

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Gets ticket trend for received_tickets, resolved_tickets and unresolved_tickets
     * @param TicketTrendRequest $request
     * @param $reportId
     * @return \Response
     */
    public function getOverallTicketTrend(TicketTrendRequest $request, $reportId)
    {
        try {
            $this->setReportId($reportId, "ticket-volume-trend");

            $chartTypes = ['received_tickets', 'unresolved_tickets', 'resolved_tickets'];

            $chart = $this->getChartObject('overall_ticket_trend');

            $chart->categoryLabel = Lang::get('report::lang.time_period');

            $format = $request->view_by;

            foreach ($chartTypes as $chartType) {
                $chart->injectChart($this->getTicketTrendByChartType($chartType, $format));
            }

            return successResponse('', $chart);
        } catch (TypeNotFoundException $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    /**
     * Gets ticket trend chart data by chart type
     * @param string $chartType
     * @param string $format
     * @return Chart
     * @throws TypeNotFoundException
     */
    private function getTicketTrendByChartType(string $chartType, string $format = 'day')
    {
        $baseQuery = $this->getBaseQueryByFilter($chartType);

        // just group by date
        $dateQuery = $this->dateQuery($format, 'tickets.created_at', 'chart_key');

        $countQuery = DB::raw('COUNT(DISTINCT tickets.id) as chart_value');

        $sortQuery = $this->dateQuery('sortable', 'tickets.created_at', 'sort_referer');

        // get chartObject
        $chart = $this->getChartObject($chartType);

        $baseQuery->select($dateQuery, $countQuery, 'tickets.*', $sortQuery)->groupBy('chart_key')
            ->orderBy('sort_referer', 'asc')
            ->get()
            ->map(function ($element) use (&$chart, $chartType, $format) {
                // need to append href too
                $coordinate = new Coordinate;
                $coordinate->id = $element->chart_key;
                $coordinate->label = $element->chart_key;
                $coordinate->value = $element->chart_value;
                $coordinate->redirectTo = $this->getRedirectLink($chartType, 'created-at', $element->chart_key, $format);
                $chart->injectData($coordinate);
            });

        return $chart;
    }

    /**
     * Gets base query by its chart type after applying filters
     * @param $chartType
     * @return Builder
     * @throws TypeNotFoundException
     */
    private function getBaseQueryByFilter($chartType)
    {
        $baseQuery = $this->getBaseQueryForTickets($this->request, false);

        return $this->modifyQueryByCommonType($chartType, $baseQuery);
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

        $chart->dataLabel = Lang::get('report::lang.ticket_count');

        return $chart;
    }

    /**
     * Gets redirect link for the specific date
     * @param string $key
     * @param string $value
     * @param string $chartType
     * @param string $format available formats are day, week, month, year
     * @return string
     * @throws TypeNotFoundException
     */
    private function getRedirectLink(string $chartType, string $key = null, string $value = null, $format = 'day') : string
    {
        $baseFilters = $this->request->all();

        if ($key) {
            $baseFilters[$key] = "date::".$this->getDateRangeByFormat($format, $value);
        }

        return $this->getInboxFilterUrl($chartType, $baseFilters);
    }


    /**
     * Gets day ticket trend
     * @param WeekDayTrendRequest $request
     * @param $reportId
     * @return \Response
     * @throws TypeNotFoundException
     */
    public function getDayTicketTrend(WeekDayTrendRequest $request, $reportId)
    {
        $this->setReportId($reportId, "ticket-volume-trend");

        $day = $request->view_by;

        $chartType = 'day_ticket_trend';

        $chart = $this->getChartObject($chartType);

        $chart->categoryLabel = Lang::get("report::lang.time_period");

        $chart->injectChart($this->getDayTrendForReceivedTickets($day));

        $chart->injectChart($this->getDayTrendForResolvedTickets($day));

        return successResponse('', $chart);
    }

    /**
     * Gets day trend for received tickets
     * @param string $day
     * @return Chart
     * @throws TypeNotFoundException
     */
    private function getDayTrendForReceivedTickets(string  $day)
    {
        $chartType = 'received_tickets';

        // query from database for just monday and then count resolved and created tickets
        $baseQuery = $this->getBaseQueryByFilter($chartType);

        $dateQuery = $this->dateQuery('hour', 'tickets.created_at', 'chart_key');

        // average ticket count
        $countQuery = DB::raw('COUNT(DISTINCT tickets.id) as chart_value');

        $dayOfWeek = $this->getDayOfWeek($day);

        // filter baseQuery only for tickets which were created on monday
        // and then group by hour
        $baseQuery->whereRaw("DAYOFWEEK(tickets.created_at)=$dayOfWeek")
            ->select($countQuery, $dateQuery, 'tickets.*');

        return $this->getChartDataForDayTrend($baseQuery, $chartType);
    }

    /**
     * Gets day trend for resolved tickets
     * @param string $day
     * @return Chart
     * @throws TypeNotFoundException
     */
    private function getDayTrendForResolvedTickets(string  $day)
    {
        $chartType = 'resolved_tickets';

        // query from database for just monday and then count resolved and created tickets
        $baseQuery = $this->getBaseQueryByFilter($chartType);

        // grouping data on hourly basis
        $dateQuery = $this->dateQuery('hour', 'tickets.closed_at', 'chart_key');

        $countQuery = DB::raw('COUNT(DISTINCT tickets.id) as chart_value');

        $dayOfWeek = $this->getDayOfWeek($day);

        // filter baseQuery only for tickets which were created on monday
        // and then group by hour
        $baseQuery = $baseQuery->where('closed_at', '!=', null)
            ->whereRaw("DAYOFWEEK(tickets.closed_at)=$dayOfWeek")
            ->select($countQuery, $dateQuery, 'tickets.*');

        return $this->getChartDataForDayTrend($baseQuery, $chartType);
    }

    /**
     * Gets populated chart data for current report
     * ASSUMPTION: while querying key and value of the coordinate must be name as chart_key and chart_value
     * @param $baseQuery
     * @param $chartType
     * @return Chart
     */
    private function getChartDataForDayTrend($baseQuery, $chartType)
    {
        $chart = $this->getChartObject($chartType);

        $baseQuery->orderBy('chart_key', 'asc')
            // sorting by chart key, because sorting by date won't make any sense since its a overall hour graph
            ->groupBy('chart_key')->get()
            ->map(function ($element) use (&$chart, $chartType) {
                $coordinate = new Coordinate;
                $coordinate->id = $element->chart_key;
                $coordinate->label = $element->chart_key;
                $coordinate->value = $element->chart_value;
                $coordinate->redirectTo = null;
                $chart->injectData($coordinate);
            });

        return $chart;
    }

    /**
     * Gives index of week day.
     * Sunday = 1, Monday = 2, Tuesday = 3, Wednesday = 4, Thursday = 5, Friday = 6, Saturday = 7
     *
     * @param string $weekDay
     * @return int
     * @throws TypeNotFoundException
     */
    private function getDayOfWeek(string $weekDay)
    {
        $weekDay = strtolower($weekDay);

        $weekDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        $weekDayIndex = array_search($weekDay, $weekDays);

        if ($weekDayIndex === false) {
            throw new TypeNotFoundException("Invalid weekday given");
        }

        return array_search($weekDay, $weekDays) + 1;
    }

    /**
     * Gets dat trend widget data
     * @param $reportId
     * @return \Response
     */
    public function getOverallTicketTrendWidget($reportId)
    {
        try {
            $this->setReportId($reportId, "ticket-volume-trend");

            $widgetTypes = ['received_tickets',  'unresolved_tickets', 'resolved_tickets'];

            $widgets = [];

            foreach ($widgetTypes as $widgetType) {
                $widget = new Widget;

                $widget->id = $widgetType;

                $widget->key = Lang::get('report::lang.total_'.$widgetType);

                $widget->value = $this->getBaseQueryByFilter($widgetType)->count();

                $widget->redirectTo = $this->getRedirectLink($widgetType);

                $widgets[] = $widget;
            }
            return successResponse('', $widgets);
        } catch (TypeNotFoundException $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    /**
     * Gets widget data for day trend report
     * Elements are needed in the widgets
     * 1. most tickets were received around
     * 2. most tickets were resolved around
     * 3. most tickets were received on
     * 4. most ticket were resolved on
     *
     * @param $reportId
     * @return \Response
     * @throws \Exception
     */
    public function getDayTicketTrendWidget($reportId)
    {
        $widgetTypes = ['max_received_ticket_hour',  'max_resolved_ticket_hour', 'max_received_ticket_day','max_resolved_ticket_day'];

        $widgets = [];

        $this->setReportId($reportId, "ticket-volume-trend");

        try {
            foreach ($widgetTypes as $widgetType) {
                $widget = new Widget;

                $widget->id = $widgetType;

                $widget->key = Lang::get('report::lang.'.$widgetType);

                $widgetMetaObject = $this->getDayWidgetMetaDetails($widgetType);

                // baseQuery has to be grouped by hours and then sort by ticket count
                $dateQuery = $this->dateQuery($widgetMetaObject->format, $widgetMetaObject->column, 'chart_key');

                $countQuery = DB::raw('COUNT(DISTINCT tickets.id) as chart_value');

                $maxTicketHourObj = $this->getBaseQueryByFilter($widgetMetaObject->chartType)
                    ->select($dateQuery, $countQuery, 'tickets.*')
                    ->groupBy('chart_key')
                    ->orderBy('chart_value', 'desc')
                    ->first();

                if(!$maxTicketHourObj){
                    $widget->value = "--";
                } else {
                    $widget->value = $widgetMetaObject->format == 'hour' ? $this->getFormattedHourRange($maxTicketHourObj->chart_key)
                        : $maxTicketHourObj->chart_key;
                }
                $widgets[] = $widget;
            }
            return successResponse('', $widgets);
        } catch (TypeNotFoundException $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    /**
     * Gets meta details for day widget
     * @param string $widget
     * @return object
     * @throws TypeNotFoundException
     */
    private function getDayWidgetMetaDetails(string $widget)
    {
        switch ($widget) {
            case 'max_received_ticket_hour':
                return (object)['chartType'=>'received_tickets', 'format'=>'hour', 'column'=>'tickets.created_at'];

            case 'max_resolved_ticket_hour':
                return (object)['chartType'=>'resolved_tickets', 'format'=>'hour', 'column'=>'tickets.closed_at'];

            case 'max_received_ticket_day':
                return (object)['chartType'=>'received_tickets', 'format'=>'day_of_week', 'column'=>'tickets.created_at'];

            case 'max_resolved_ticket_day':
                return (object)['chartType'=>'resolved_tickets', 'format'=>'day_of_week', 'column'=>'tickets.closed_at'];

            default:
                throw new TypeNotFoundException('Invalid widget type received. Support widget types are max_received_ticket_hour,
                max_resolved_ticket_hour, max_received_ticket_day, max_resolved_ticket_day');
        }
    }

    /**
     * Gets formatted hour range
     * @param int $value
     * @return string
     */
    private function getFormattedHourRange(int $value) : string
    {
        $endValue = (int)$value + 1;
        return "$value:00 - $endValue:00 hours";
    }
}
