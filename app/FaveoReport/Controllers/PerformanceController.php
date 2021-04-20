<?php


namespace App\FaveoReport\Controllers;

use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use App\Model\helpdesk\Agent\Assign_team_agent;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use Config;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use DB;
use Response;

class PerformanceController extends BaseReportController
{

    private $request;

    public function __construct(Request $request = null)
    {
        if($request){
            $this->request = $request;

            // in case of paginate, it is not required, but when a request is generated dynamically,
            // we need to change the page number dynamically but in that case paginate looks for
            // non-attribute property which is difficult to set
            $page = $this->request->input('page')?: 1;
            $this->setCurrentPage($page);
        }
    }

    /**
     * Short codes that are available in performance reports
     * @var array
     */
    public $availableShortCodes = [
            ':assigned_tickets',
            ':reopened_tickets',
            ':resolved_tickets',
            ':tickets_with_response_sla_met',
            ':tickets_with_resolution_sla_met',
            ':avg_resolution_time',
            ':avg_response_time',
            ':responses',
        ];

    /**
     * Gets Agent Performance report
     * @param $reportId
     * @return Response
     * @throws \Exception
     */
    public function getAgentPerformanceData($reportId)
    {
        $this->type = 'agent-performance';

        $this->setReportId($reportId, $this->type);

        $limit = $this->request->input('limit') ?: 10;


        $sortField = $this->request->input('sort-field') ?: 'updated_at';

        $sortOrder = $this->request->input('sort-order') ?: 'desc';

        if($sortField == 'name'){
            // we do not have a name column in user's table, so we sort by first name
            $sortField = 'first_name';
        }

        $data = $this->baseQueryForPerformance($this->request)
            ->join('users', 'tickets.assigned_to', '=', 'users.id')
            ->orderBy($sortField, $sortOrder)
            ->groupBy('assigned_to')
            ->where('assigned_to', '!=', null)
            ->paginate($limit);

        $data->getCollection()->transform(function ($element) use($reportId) {
            return $this->getFormattedPerformance($element, $reportId);
        });

        // get all agent ids, and query in same order for threads
        return successResponse('', $data);
    }

    /**
     * Gets Team Performance report
     * @return Response
     * @throws \Exception
     */
    public function getTeamPerformanceData($reportId)
    {
        $this->type = 'team-performance';

        $this->setReportId($reportId, $this->type);

        $limit = $this->request->input('limit') ?: 10;

        $sortField = $this->request->input('sort-field') ?: 'updated_at';

        $sortOrder = $this->request->input('sort-order') ?: 'desc';

        $data = $this->baseQueryForPerformance($this->request)
            ->join('teams', 'tickets.team_id', '=', 'teams.id')
            ->orderBy($sortField, $sortOrder)
            ->groupBy('team_id')
            ->where('team_id', '!=', null)
            ->paginate($limit);


        $data->getCollection()->transform(function ($element) use($reportId) {
            return $this->getFormattedPerformance($element, $reportId);
        });

        return successResponse('', $data);
    }


    /**
     * Gets Team Performance report
     * @param $reportId
     * @return Response
     * @throws \Exception
     */
    public function getDepartmentPerformanceData($reportId)
    {
        $this->type = 'department-performance';

        $this->setReportId($reportId, $this->type);

        $limit = $this->request->input('limit') ?: 10;

        $sortField = $this->request->input('sort-field') ?: 'updated_at';

        $sortOrder = $this->request->input('sort-order') ?: 'desc';

        $data = $this->baseQueryForPerformance($this->request)
            ->join('department', 'tickets.dept_id', '=', 'department.id')
            ->orderBy($sortField, $sortOrder)
            ->groupBy('dept_id')
            ->where('dept_id', '!=', null)
            ->paginate($limit);


        $data->getCollection()->transform(function ($element) use($reportId) {
            return $this->getFormattedPerformance($element, $reportId);
        });

        return successResponse('', $data);
    }

    /**
     * formats performance reports
     * @param $element
     * @param $reportId
     * @return object
     * @throws \App\FaveoReport\Exceptions\VariableNotFoundException
     */
    private function getFormattedPerformance($element, $reportId)
    {
        $threadObject = $this->getThreadDataByAgentIds($this->getAgentIdsByType($element));

        $object = (object)[
            'id' => $this->getRecordId($element),
            'name'=> $this->getProfileHyperLink($element),
            'assigned_tickets' => $this->getHyperlinkValues($element, 'assigned_tickets'),
            'resolved_tickets' => $this->getHyperlinkValues($element, 'resolved_tickets'),
            'reopened_tickets' => $this->getHyperlinkValues($element, 'reopened_tickets'),
            'tickets_with_response_sla_met' => $this->getHyperlinkValues($element, 'tickets_with_response_sla_met'),
            'tickets_with_resolution_sla_met' => $this->getHyperlinkValues($element, 'tickets_with_resolution_sla_met'),
            'avg_resolution_time' => $element->avg_resolution_time,
            'responses'=> $threadObject->responses,
            'avg_response_time'=> $threadObject->avg_response_time
        ];

        // appending custom columns data
        $this->appendCustomColumnsData($object, $reportId);

        return $object;
    }

    /**
     * Gets thread data by agent ids
     * @param array $agentIds
     * @return object
     * @throws \Exception
     */
    private function getThreadDataByAgentIds(array $agentIds)
    {
        // get baseQuery for ticket
        $ticketListControllerObject = new TicketListController;

        $ticketListControllerObject->setRequest($this->request);

        $baseQuery = $ticketListControllerObject->baseQueryForTickets();

        $threadObject = $baseQuery->join('ticket_thread', 'tickets.id', '=', 'ticket_thread.ticket_id')
            ->where('ticket_thread.poster', '=', 'support')
            ->where('ticket_thread.is_internal', '=', 0)
            ->whereIn('ticket_thread.user_id', $agentIds)
            ->select(
                'ticket_thread.poster',
                DB::raw('count(*) as response_count'),
                DB::raw('AVG(ticket_thread.response_time) as response_time')
            )
            // grouping by poster so that sum can be performed on all rows since poster for all rows will be same
            ->groupBy('ticket_thread.poster')
            ->first();

        if (!$threadObject) {
            return (object)['responses'=> null, 'avg_response_time'=> null];
        }
        return (object)['responses' => (int)$threadObject->response_count, 'avg_response_time'=> (int)$threadObject->response_time];
    }

    /**
     * Gets base query for performance after which it can be categorized by its type
     * @param Request $request
     * @param $reportId
     * @return Builder
     * @throws \Exception
     */
    private function baseQueryForPerformance(Request $request)
    {
        $baseQuery = $this->getBaseQueryForTickets($request, false);

        return $baseQuery->select(
            'tickets.*',
            DB::raw('count(DISTINCT tickets.id) AS assigned_tickets'),
            // reopened is an integer which can have a value greater than 1
            DB::raw('SUM(tickets.reopened > 0) AS reopened_tickets'),
            DB::raw('SUM(tickets.closed > 0) AS resolved_tickets'),
            DB::raw('SUM(tickets.is_response_sla > 0) AS tickets_with_response_sla_met'),
            DB::raw('SUM(tickets.is_resolution_sla > 0) AS tickets_with_resolution_sla_met'),
            DB::raw('ROUND(AVG(tickets.resolution_time)) AS avg_resolution_time')
        );
    }

    /**
     * Gets hyperlink values for performance reports
     * @param $element
     * @param $key
     * @return string
     * @throws \Exception
     */
    private function getHyperlinkValues($element, $key)
    {
        $baseFilters = $this->getBaseFilterByType($element);

        $redirectUrl = $this->getInboxFilterUrl($key, $baseFilters);

        return $this->getHyperlink($redirectUrl, $element->$key);
    }

    /**
     * Gets base URL for clickability
     * @param object $element
     * @return array   associative array of filter key and filter values
     * @throws \Exception
     */
    private function getBaseFilterByType($element) : array
    {
        $baseFilters = $this->request->except(['page', 'search-query','limit','sort-order']);

        switch ($this->type) {
            case 'department-performance':
                $baseFilters['dept-ids'] = [$element->dept_id];
                break;

            case 'team-performance':
                $baseFilters['team-ids'] = [$element->team_id];
                break;

            case 'agent-performance':
                $baseFilters['assignee-ids'] = [$element->assigned_to];
                break;

            default:
                throw new \Exception('invalid report type');
        }

        return $baseFilters;
    }

    /**
     * Gets agent Ids by type. For type as agent, it will simply give agent Id,
     * for department, it will give agents(ids) in that department. For team, it will give agents(ids) in that team.
     * @param $element
     * @return array
     * @throws \Exception
     */
    private function getAgentIdsByType($element) : array
    {
        switch ($this->type) {
            case 'agent-performance':
                return [$element->assigned_to];

            case 'department-performance':
                return DepartmentAssignAgents::where('department_id', $element->dept_id)->pluck('agent_id')->toArray();

            case 'team-performance':
                return Assign_team_agent::where('team_id', $element->team_id)->pluck('agent_id')->toArray();

            default:
                throw new \Exception('invalid report type');
        }
    }

    /**
     * Gets Profile hyperlink for a department/team/agent
     * @param $element
     * @return string
     * @throws \Exception
     */
    private function getProfileHyperLink($element) : string
    {
        switch ($this->type) {
            case 'agent-performance':
                return $this->getAgentProfileLink($element);

            case 'department-performance':
                return $this->getDepartmentProfileLink($element);

            case 'team-performance':
                return $this->getTeamProfileLink($element);

            default:
                throw new \Exception('invalid report type');
        }
    }

    /**
     * Gets agent profile link
     * @param $element
     * @return string
     */
    private function getAgentProfileLink($element)
    {
        if (!$element->assigned) {
            return "Unassigned";
        }
        $redirectUrl = Config::get('app.url')."/agent/".$element->assigned->id;

        return $this->getHyperlink($redirectUrl, $element->assigned->full_name);
    }

    /**
     * Gets agent profile link
     * @param $element
     * @return string
     */
    private function getTeamProfileLink($element)
    {
        if (!$element->assignedTeam) {
            return "Unassigned";
        }

        $redirectUrl = Config::get('app.url')."/assign-teams/".$element->assignedTeam->id;

        return $this->getHyperlink($redirectUrl, $element->assignedTeam->name);
    }

    /**
     * Gets id of the record. For agent, agent Id. For department, department id. For team, team id.
     * @param $element
     * @return mixed
     * @throws \Exception
     */
    private function getRecordId($element)
    {
        switch ($this->type) {
            case 'agent-performance':
                return $element->assigned_to;

            case 'department-performance':
                return $element->dept_id;

            case 'team-performance':
                return $element->team_id;

            default:
                throw new \Exception('invalid report type');
        }
    }

    /**
     * Gets agent profile link
     * @param $element
     * @return string
     */
    private function getDepartmentProfileLink($element)
    {
        if (!$element->department) {
            return "Unassigned";
        }

        $redirectUrl = Config::get('app.url')."/department/".$element->department->id;

        return $this->getHyperlink($redirectUrl, $element->department->name);
    }
}
