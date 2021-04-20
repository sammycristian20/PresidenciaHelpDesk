<?php


namespace App\FaveoReport\Controllers;

use App\FaveoReport\Exceptions\TypeNotFoundException;
use App\FaveoReport\Models\Report;
use App\FaveoReport\Structure\Chart;
use App\FaveoReport\Structure\Coordinate;
use App\FaveoReport\Structure\Widget;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Lang;

class HelpdeskInDepth extends BaseReportController
{

    /**
     * Available chart types in current report
     * @var array
     */
    private $chartTypes = [
        'received_tickets',
        'unresolved_tickets',
        'resolved_tickets',
        'reopened_tickets',
        'has_response_sla_met',
        'has_resolution_sla_met',
        'avg_first_response_time',
        'avg_response_time',
        'avg_resolution_time',
    ];

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;


    public function __construct(Request $request)
    {
        $this->middleware('role.agent');

        $this->request = $request;
    }

    /**
     * Gets report base don chart category
     * @param Request $request
     * @param int $reportId
     * @return \Response
     */
    public function getReports($reportId)
    {
        $this->setReportId($reportId, "helpdesk-in-depth");

        $chartCategory = $this->request->view_by;

        try {
            switch ($chartCategory) {
                case 'status':
                    return $this->getStatusInDepthReport();

                case 'priority':
                    return $this->getPriorityInDepthReport();

                case 'source':
                    return $this->getSourceInDepthReport();

                case 'type':
                    return $this->getTypeInDepthReport();

                default:
                    return errorResponse(Lang::get("report::lang.invalid_chart_category"));
            }
        } catch (TypeNotFoundException $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    /**
     * Groups data by its status
     * @return \Response
     * @throws TypeNotFoundException
     */
    private function getStatusInDepthReport()
    {
        $charts = [];

        foreach ($this->chartTypes as $chartType) {
            $baseQuery = $this->getBaseQueryByFilter($chartType);

            $chart = $this->getChartObject($chartType);

            $chartTypeCountQuery = $this->getQueryFromChartTypeValue($chartType);

            $baseQuery->select($chartTypeCountQuery, 'tickets.*')
                ->groupBy('status')
                ->get()
                ->map(function ($element) use (&$chart, $chartType) {
                    if ($element->statuses) {
                        $coordinate = new Coordinate;
                        $coordinate->id = $chartType;
                        $coordinate->label = $element->statuses->name;
                        $coordinate->value = $element->chart_type_value;
                        $coordinate->redirectTo = $this->getRedirectLink($chartType, 'status', $element->status);
                        $chart->injectData($coordinate);
                    }
                });

            $charts[] = $chart;
        }

        return successResponse('', $charts);
    }

    /**
     * Groups data by its status
     * @return \Response
     * @throws TypeNotFoundException
     */
    private function getPriorityInDepthReport()
    {
        $charts = [];

        foreach ($this->chartTypes as $chartType) {
            $baseQuery = $this->getBaseQueryByFilter($chartType);

            $chart = $this->getChartObject($chartType);

            $chartTypeCountQuery = $this->getQueryFromChartTypeValue($chartType);

            $baseQuery->select($chartTypeCountQuery, 'tickets.*')
                ->groupBy('priority_id')
                ->get()
                ->map(function ($element) use (&$chart, $chartType) {
                    if ($element->priority) {
                        $coordinate = new Coordinate;
                        $coordinate->id = $chartType;
                        $coordinate->label = $element->priority->name;
                        $coordinate->value = $element->chart_type_value;
                        $coordinate->redirectTo = $this->getRedirectLink($chartType, 'priority', $element->priority_id);
                        $chart->injectData($coordinate);
                    }
                });

            $charts[] = $chart;
        }

        return successResponse('', $charts);
    }

    /**
     * Groups data by its status
     * @return \Response
     * @throws TypeNotFoundException
     */
    private function getTypeInDepthReport()
    {
        $charts = [];

        foreach ($this->chartTypes as $chartType) {
            $baseQuery = $this->getBaseQueryByFilter($chartType);

            $chart = $this->getChartObject($chartType);

            $chartTypeCountQuery = $this->getQueryFromChartTypeValue($chartType);

            $baseQuery->select($chartTypeCountQuery, 'tickets.*')
                ->groupBy('type')
                ->get()
                ->map(function ($element) use (&$chart, $chartType) {
                    if ($element->types) {
                        $coordinate = new Coordinate;
                        $coordinate->id = $chartType;
                        $coordinate->label = $element->types->name;
                        $coordinate->value = $element->chart_type_value;
                        $coordinate->redirectTo = $this->getRedirectLink($chartType, 'type', $element->type);
                        $chart->injectData($coordinate);
                    }
                });

            $charts[] = $chart;
        }

        return successResponse('', $charts);
    }

    /**
     * Groups data by its status
     * @return \Response
     * @throws TypeNotFoundException
     */
    private function getSourceInDepthReport()
    {
        $charts = [];

        foreach ($this->chartTypes as $chartType) {
            $baseQuery = $this->getBaseQueryByFilter($chartType);

            $chart = $this->getChartObject($chartType);

            $chartTypeCountQuery = $this->getQueryFromChartTypeValue($chartType);

            $baseQuery->select($chartTypeCountQuery, 'tickets.*')
                ->groupBy('source')
                ->get()
                ->map(function ($element) use (&$chart, $chartType) {
                    if ($element->sources) {
                        $coordinate = new Coordinate;
                        $coordinate->id = $chartType;
                        $coordinate->label = $element->sources->name;
                        $coordinate->value = $element->chart_type_value;
                        $coordinate->redirectTo = $this->getRedirectLink($chartType, 'source', $element->source);
                        $chart->injectData($coordinate);
                    }
                });

            $charts[] = $chart;
        }

        return successResponse('', $charts);
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

        $chart->name = $this->isAvgTimeChartType($chartType) ? Lang::get('report::lang.chart_'.$chartType): Lang::get('report::lang.'.$chartType);

        $chart->dataLabel = $this->getChartDataLabel($chartType);

        return $chart;
    }

    /**
     * Gets query for chart type value with a column name as chart_type_value
     * @param string $chartType
     * @return \Illuminate\Database\Query\Expression
     */
    private function getQueryFromChartTypeValue(string $chartType)
    {
        if (in_array($chartType, ['avg_response_time', 'avg_first_response_time'])) {
            return DB::raw('ROUND(AVG(ticket_thread.response_time)) AS chart_type_value');
        }

        if ($chartType == 'avg_resolution_time') {
            return DB::raw('ROUND(AVG(tickets.resolution_time)) AS chart_type_value');
        }

        return DB::raw('COUNT(DISTINCT tickets.id) as chart_type_value');
    }

    /**
     * Gets base ticket query by filter parameters
     * @param string $chartType
     * @return Builder
     * @throws TypeNotFoundException
     * @throws \Exception
     */
    private function getBaseQueryByFilter(string $chartType)
    {
        $baseQuery = $this->getBaseQueryForTickets($this->request);

        return $this->modifyQueryByParameter($chartType, $baseQuery);
    }

    /**
     * Modifies query according to type
     * @param string $graphType
     * @param Builder $baseQuery
     * @return Builder
     * @throws TypeNotFoundException
     */
    private function modifyQueryByParameter(string $graphType, Builder &$baseQuery) : Builder
    {
        // received_tickets, resolved_tickets, unresolved_tickets, reopened_tickets, response_sla, resolve_sla, agent_responses, client_responses
        switch ($graphType) {
            case 'avg_resolution_time':
                return $this->modifyQueryByCommonType('resolution_time', $baseQuery);

            case 'avg_first_response_time':
                return $this->appendThreadQuery('first_response_time', $baseQuery);

            case 'avg_response_time':
                return $this->appendThreadQuery('avg_response_time', $baseQuery);

            default:
                return $this->modifyQueryByCommonType($graphType, $baseQuery);
        }
    }

    /**
     * Gets chart label
     * @param $chartType
     * @return array|string|null
     */
    private function getChartDataLabel(string $chartType) : string
    {
        if ($this->isAvgTimeChartType($chartType)) {
            return Lang::get("report::lang.minutes");
        }
        return Lang::get('report::lang.ticket_count');
    }


    /**
     * @param string $chartType
     * @param string $chartCategory
     * @param int $categoryId
     * @return string
     * @throws TypeNotFoundException
     */
    private function getRedirectLink(string $chartType, string $chartCategory = null, int $categoryId = null) : ?string
    {
        if ($this->isAvgTimeChartType($chartType)) {
            return null;
        }

        // add request params to it
        $baseFilters = $this->request->all();

        // modify base filters with
        if ($chartCategory) {
            $this->modifyFilterByChartCategory($chartCategory, $categoryId, $baseFilters);
        }

        return $this->getInboxFilterUrl($chartType, $baseFilters);
    }


    /**
     * If chart type is of avg time
     * @param string $chartType
     * @return bool
     */
    private function isAvgTimeChartType(string $chartType) : bool
    {
        if (in_array($chartType, ['avg_first_response_time', 'avg_response_time', 'avg_resolution_time'])) {
            return true;
        }
        return false;
    }

    /**
     * Appends required filters to baseFilters based on chart category
     * @param $chartCategory
     * @param $categoryId
     * @param &$baseFilters
     * @return array
     * @throws TypeNotFoundException
     */
    private function modifyFilterByChartCategory($chartCategory, $categoryId, &$baseFilters)
    {
        switch ($chartCategory) {
            case 'status':
                return $baseFilters['status-ids'] = [$categoryId];

            case 'priority':
                return $baseFilters['priority-ids'] = [$categoryId];

            case 'source':
                return $baseFilters['source-ids'] = [$categoryId];

            case 'type':
                return $baseFilters['type-ids'] = [$categoryId];

            default:
                throw new TypeNotFoundException("Invalid Chart category. Allowed categories are status, priority, source and type");
        }
    }

    /**
     * Groups data by its status
     * @param $reportId
     * @return \HTTP
     * @throws TypeNotFoundException
     */
    public function getWidgetData($reportId)
    {
        $this->setReportId($reportId, "helpdesk-in-depth");

        $widgets = [];

        foreach ($this->chartTypes as $chartType) {
            $widget = new Widget();

            $widget->id = $chartType;

            $widget->key = Lang::get('report::lang.overall_'.$chartType);

            $widget->value = $this->getWidgetValue($chartType);

            $widget->redirectTo = $this->getRedirectLink($chartType);

            $widgets[] = $widget;
        }

        return successResponse('', $widgets);
    }

    /**
     * Gets widget value
     * @param string $chartType
     * @return int|string
     * @throws TypeNotFoundException
     */
    private function getWidgetValue(string $chartType)
    {
        $baseQuery = $this->getBaseQueryByFilter($chartType);

        $widgetTypeCountQuery = $this->getQueryFromChartTypeValue($chartType);

        if (in_array($chartType, ['avg_response_time', 'avg_first_response_time'])) {
            return round($baseQuery->select($widgetTypeCountQuery, 'tickets.*')->avg("ticket_thread.response_time"))." minutes";
        }

        if ($chartType == 'avg_resolution_time') {
            return round($baseQuery->select($widgetTypeCountQuery, 'tickets.*')->avg("tickets.resolution_time"))." minutes";
        }

        return $baseQuery->select($widgetTypeCountQuery, 'tickets.*')
            ->count();
    }
}
