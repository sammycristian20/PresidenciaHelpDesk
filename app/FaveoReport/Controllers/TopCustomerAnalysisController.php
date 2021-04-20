<?php

namespace App\FaveoReport\Controllers;

use App\Events\ReportExportEvent;
use App\FaveoReport\Models\ReportDownload;
use App\FaveoReport\Structure\Chart;
use App\FaveoReport\Structure\Coordinate;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use Illuminate\Http\Request;
use DB;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Exception;
use Illuminate\Support\Str;
use Lang;
use Carbon\Carbon;

class TopCustomerAnalysisController extends BaseReportController
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
        'received_tickets',
        'resolved_tickets',
        'unresolved_tickets',
        'reopened_tickets',
        'has_response_sla_met',
        'has_resolution_sla_met',
        'client_responses',
        'agent_responses',
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Gets organization report data
     * @param int $reportId
     * @return Response
     */
    public function getOrganizationReport($reportId)
    {
        $this->setReportId($reportId, "top-customer-analysis");

        try{
            $viewBy = $this->request->input("view_by") ?: "top_5";

            $limit = str_replace("top_", "", $viewBy);

            $chartArray = [];

            foreach($this->chartTypes as $chartType){
                $chart = new Chart;

                $chart->id = $chartType;

                $chart->name = Lang::get('report::lang.'.$chartType);

                $chart->dataLabel = $this->getChartDataLabel($chartType);

                $this->injectChartDataByChartType($chart, $chartType, $limit);

                $chartArray[] = $chart;
            }

            return successResponse('', $chartArray);
        } catch (Exception $e){
            return errorResponse($e->getMessage());
        }
    }

    /**
     * gets chart data label based on chart type
     * @param string $chartType
     * @return string
     */
    private function getChartDataLabel(string $chartType) : string
    {
        if(in_array($chartType, ['client_responses', 'agent_responses'])){

            return Lang::get('report::lang.response_count');
        }

        return Lang::get('report::lang.ticket_count');
    }

    /**
     * @param Chart $chart
     * @param string $chartType
     * @param int $limit
     * @return void
     * @throws Exception
     */
    private function injectChartDataByChartType(Chart $chart, string $chartType, int $limit)
    {
        $baseQuery = $this->getBaseQueryForTickets($this->request);

        $threadCountFields = ['agent_responses', 'client_responses'];

        $countField = in_array($chartType, $threadCountFields) ? DB::raw('COUNT(DISTINCT ticket_thread.id) as record_count'): DB::raw('COUNT(DISTINCT tickets.id) as record_count');

        // get all filters and pass to ticketListController
        // get base query based on filters and then
        $baseQuery = $baseQuery
            ->join('users as client', 'tickets.user_id', '=', 'client.id')
            ->join('user_assign_organization', 'client.id', '=', 'user_assign_organization.user_id')
            ->join('organization', 'user_assign_organization.org_id', '=', 'organization.id')

            // making it unique so that it doesn't conflict with baseQuery in filters
            // after filtering, it has to be converted in id and name
            ->select('organization.name as org_name', 'organization.id as org_id', $countField, 'tickets.*')
            ->orderBy('record_count', 'desc')
            ->orderBy('organization.created_at', 'desc')
            ->groupBy('org_id');

        $baseQuery = $this->modifyQueryByParameter($chartType,$baseQuery);

        $baseQuery->take($limit)
            ->get()
            ->map(function($element) use (&$chart, $chartType){
                // need to append href too
                $coordinate = new Coordinate;
                $coordinate->id = $element->org_id;
                $coordinate->label = $element->org_name;
                $coordinate->value = $element->record_count;
                $coordinate->redirectTo = $this->getRedirectLink('organization-ids', [$element->org_id], $chartType);
                $chart->injectData($coordinate);
            });
    }


    /**
     * Gets redirect link, of an organization, which will redirect to inbox
     * @param string $key
     * @param array $value
     * @param string $chartType
     * @return string
     */
    private function getRedirectLink(string $key, array $value, string $chartType) : string
    {
        // params which will be there when clicked on an organization bar
        // it will be a merge of filter parameters and organisation
        $filterParams = array_merge($this->request->all(), [$key => $value], $this->getFilterByType($chartType));

        // make url params out of array
        return $this->getInboxUrl(). '&' . http_build_query($filterParams);
    }

    /**
     * Gets filter by its type
     * @param string $type
     * @return array
     */
    private function getFilterByType(string $type = null) : array
    {
        switch ($type) {

            case 'resolved_tickets':
                return ['is-resolved' => 1];

            case 'unresolved_tickets':
                return ['is-resolved' => 0];

            case 'reopened_tickets':
                return ['reopened' => 1];

            case 'has_response_sla_met':
                return ['has-response-sla-met'=> 1];

            case 'has_resolution_sla_met':
                return ['has-resolution-sla-met'=> 1];

            default:
                return [];
        }
    }

    /**
     * Modifies query according to type
     * @param string $graphType
     * @param Builder $baseQuery
     * @return Builder
     * @throws Exception
     */
    private function modifyQueryByParameter(string $graphType, Builder &$baseQuery) : Builder
    {
        // received_tickets, resolved_tickets, unresolved_tickets, reopened_tickets, response_sla, resolve_sla, agent_responses, client_responses
        switch ($graphType){
            case 'client_responses':
            case 'agent_responses':
                return $this->appendThreadQuery($graphType, $baseQuery);

            default:
                return $this->modifyQueryByCommonType($graphType, $baseQuery);
        }
    }
}