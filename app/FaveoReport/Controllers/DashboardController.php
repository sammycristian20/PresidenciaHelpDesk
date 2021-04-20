<?php


namespace App\FaveoReport\Controllers;


use App\FaveoReport\Models\UserTodo;
use App\FaveoReport\Request\CreateToDoRequest;
use App\FaveoReport\Request\UpdateToDosRequest;
use App\FaveoReport\Structure\IndividualReport;
use App\FaveoReport\Structure\Widget;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\TicketFilter;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Lang;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * DASHBOARD REPORTS (below reports will be for last seven days, except TOP WIDGET):
 *
 * TOP WIDGET
 * - my_overdue_tickets : open tickets which are overdue and assigned to currently logged in person
 * - my_due_today_tickets : open tickets which are due today and assigned to logged in user
 * - my_pending_approvals : open tickets which are waiting for approval from the user
 *
 * SECOND WIDGET:
 * - recent activities : notifications basically
 * - require immediate actions tickets: tickets which are reopened or overdue or due today in same order
 *
 * THIRD WIDGET:
 * - Performance graph :
 *      - Each day below parameters plotting
 *
 * - Performance analysis:
 *      - performance in meeting resolution SLA (comparision with other agents)
 *      - performance in meeting response SLA (comparision with other agents)
 *      - performance in average resolution time (comparision with other agents)
 *      - performance in average response time (comparision with other agents)
 *
 * FOURTH WIDGET:
 * - manager widget:
 *      - list of agents with open, reopened, overdue_tickets
 *      - list of departments with the same parameters
 *
 * FIFTH WIDGET:
 * - admin widget
 *      - same as daily report
 *
 *
 * @todo split this class into core and non-core functionality. For eg. todo can be a general functionality
 * @todo graph trend of created tickets
 * @todo system health summary
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class DashboardController extends BaseReportController
{

    private $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    ########################################################## TOP WIDGET ################################################################

    /**
     * Gets dashboard top widget data
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getDashboardTopWidget()
    {
        $this->user = Auth::user();

        $widgetTypes = $this->getDefaultTopWidgetTypes();

        $widgets = [];

        foreach ($widgetTypes as $widgetType => $icon) {
            $widget = new Widget();
            $widget->id = $widgetType;
            $widget->key = Lang::get("report::lang.{$widgetType}");
            $widget->value = $this->getBaseQueryForWidget($widgetType)->count();
            $widget->redirectTo = $this->getRedirectLink($widgetType);
            $widget->icon_class = $icon['icon_class'];
            $widget->icon_color = $icon['icon_color'];
            $widgets[] = $widget;
        }

        // append other widget types from filters table
        TicketFilter::where("user_id", Auth::user()->id)->where("display_on_dashboard", 1)
            ->select("id", "name", 'icon_class', 'icon_color')->get()->map(function($element) use(&$widgets){
                $widget = new Widget();
                $widget->id = "filter_".$element->id;
                $widget->key = $element->name;
                $widget->value = $this->getBaseQueryForWidget('filter', $element->id)->count();
                $widget->redirectTo = $this->getRedirectLink('filter',$element->id);
                $widget->icon_class = $element->icon_class;
                $widget->icon_color = $element->icon_color;
                $widgets[] = $widget;
            });

        return successResponse("", $widgets);
    }

    ########################################################## END #####################################################################

    ####################################################### SECOND WIDGET ###########################################################

    /**
     * Adds/Updates "todo"
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTodo(CreateToDoRequest $request)
    {
        $todo = Auth::user()->todos()->create(["name" => $request->name]);
        $todo->order = (int)Auth::user()->todos()->orderBy("order", "desc")->value("order") + 1;
        $todo->status = "pending";
        $todo->save();
        return successResponse(Lang::get("lang.created_successfully"));
    }

    /**
     * Adds/Updates "todo"
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTodos(UpdateToDosRequest $request)
    {
        $todos = $request->todos;

        foreach ($todos as $index => $todo) {
            $todObject = Auth::user()->todos()->where("id", $todo["id"])->first();
            $todObject->name = $todo["name"];
            $todObject->status = $todo["status"];
            $todObject->order = $index;
            $todObject->save();
        }

        return successResponse(Lang::get("lang.updated_successfully"));
    }

    /**
     * Deletes a "todo"
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTodo($id)
    {
        $todo = UserTodo::find($id);

        if($todo->user_id != Auth::user()->id) {
            return errorResponse(Lang::get("lang.not_found"), 404);
        }

        $todo->delete();

        return successResponse(Lang::get("lang.successfully_deleted"));
    }

    /**
     * Gets "todo" list of the user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTodoList()
    {
        $searchQuery = $this->request->input("search-query") ? : "";

        $limit = $this->request->input('limit')?: 10;

        $sortField = $this->request->input('sort-field') ?: 'updated_at';

        $sortOrder = $this->request->input('sort-order') ?: 'desc';

        $todoList = Auth::user()->todos()->where("name", "LIKE", "%$searchQuery%")
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit);

        return successResponse("", $todoList);
    }

    ########################################################## END #####################################################################


    ####################################################### THIRD WIDGET ###########################################################

    /**
     * Tickets which requires immediate action from user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getRequireImmediateAction()
    {
        $this->user = Auth::user();

        $dailyReport = new DailyReport();

        $agent = User::find(Auth::user()->id);

        return successResponse("", $dailyReport->getRequireImmediateActionTickets($agent));
    }

    /**
     * Gets list of best agent
     * @throws \Exception
     */
    public function getAgentPerformanceWidget()
    {
        $this->user = Auth::user();

        $this->request->request->add(["created-at"=> "last::7~day"]);

        try{
            $report = new IndividualReport;

            $report->setTitle("dashboard_performance_widget");

            $report->setDescription("dashboard_performance_widget_description");

            $report->helpLink = getHelplink("report-dashboard-my-performance");

            $widgetTypes = ["resolution_time_score", "response_time_score", "resolution_sla_met_score", "response_sla_met_score"];

            foreach ($widgetTypes as $widgetType) {
                try{
                    $widget = new Widget();
                    $widget->id = $widgetType;
                    $widget->key = Lang::get("report::lang.$widgetType");
                    $widget->value = $this->getPerformanceScoreByWidgetType($widgetType);
                    $widget->description = Lang::get("report::lang.better_than_x_percent_of_agents", ["x" => $widget->value]);
                } catch (\UnexpectedValueException $e){
                    $widget->description = Lang::get("report::lang.no_data_available");
                }
                $report->injectWidget($widget);
            }

            return successResponse("", $report);

        } catch(\UnexpectedValueException $e){

            return errorResponse($e->getMessage());
        }
    }

    ########################################################## END #####################################################################

    ####################################################### MANAGER WIDGET ##############################################################

    /**
     * Gets manager specific report
     * @param $type
     * @param DailyReport $dailyReport
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getManagerSpecificReportAnalysis($type, DailyReport $dailyReport)
    {
        $type = $type == "agent-analysis" ? "agent-analysis-only" : "department-analysis-only";

        return successResponse("", $dailyReport->getManagerSpecificReport(Auth::user(), $type));
    }

    ########################################################## END #####################################################################


    ####################################################### ADMIN WIDGET ##############################################################

    /**
     * Gives a system analysis
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSystemAnalysis()
    {
        if(Auth::user()->role != "admin"){
            return errorResponse(Lang::get("lang.permission_denied"));
        }
        return successResponse("", (new DailyReport())->getSystemAnalysis());
    }


    ########################################################## END #####################################################################

    /**
     * @param $widgetType
     * @return float|int
     * @throws \Exception
     */
    private function getPerformanceScoreByWidgetType($widgetType)
    {
        switch ($widgetType){

            case "resolution_time_score":
                return $this->getResolutionTimeScore();

            case "response_time_score":
                return $this->getResponseTimeScore();

            case "resolution_sla_met_score":
                return $this->getResolutionSlaMetScore();

            case "response_sla_met_score":
                return $this->getResponseSlaMetScore();

            default:
                throw new \InvalidArgumentException("Invalid widget type");
        }
    }



    /**
     * Gets average resolution time score out of 100
     * NOTE: Equation for avg resolution time score
     *  (1 - (sum of resolution time of tickets assigned to given agent)/(sum of resolution time on all tickets) + (ticket assigned to agent)/(overall tickets))
     * @return float|int
     * @throws \Exception
     */
    private function getResolutionTimeScore()
    {
        $systemAvgResolutionTime = $this->getSystemResolutionTimeScoreObject();

        if(!$systemAvgResolutionTime){
            throw new \UnexpectedValueException("No resolved tickets present in the system for the given time range");
        }

        $result = $this->getBaseQueryForAgentPerformance()->select(DB::raw("(1 - SUM(resolution_time)/$systemAvgResolutionTime->total_resolution_time + COUNT(tickets.id)/$systemAvgResolutionTime->total_ticket_count)/2 as score"), "assigned_to")
            ->where("closed", 1)
            ->orderBy("score", "desc")
            ->orderBy("assigned_to", "desc")
            ->groupBy("assigned_to")->get()
            ->transform(function($element){
                return (object)[
                    "value" => $element->score,
                    "referer_id"=> $element->assigned_to
                ];
            });

        return $this->getAgentPercentageComparision($result);
    }

    /**
     * Gets average resolution time score out of 100
     * NOTE: Equation for avg response time score
     *  (1 - (sum of response time of tickets assigned to given agent)/(sum of response time on all tickets) + (ticket assigned to agent)/(overall tickets))
     * @return float|int
     * @throws \Exception
     */
    private function getResponseTimeScore()
    {
        $systemAvgResponseTime = $this->getSystemResponseTimeScoreObject();

        if(!$systemAvgResponseTime){
            throw new \UnexpectedValueException("No threads present in the system for the given time range");
        }

        $result = $this->getBaseQueryForAgentPerformance()->join("ticket_thread","tickets.id", "=","ticket_thread.ticket_id")
            ->select(
                DB::raw("(1 - SUM(response_time)/$systemAvgResponseTime->total_response_time + COUNT(ticket_thread.id)/$systemAvgResponseTime->total_thread_count)/2 as score"),
                "ticket_thread.poster", "ticket_thread.user_id"
            )->where("ticket_thread.is_internal", 0)
            ->where("ticket_thread.poster", "support")
            ->orderBy("score", "desc")
            ->orderBy("ticket_thread.user_id", "desc")
            ->groupBy("ticket_thread.user_id")->get()
            ->transform(function($element){
                return (object)[
                    "value" => $element->score,
                    "referer_id"=> $element->user_id
                ];
            });

        return $this->getAgentPercentageComparision($result);
    }

    /**
     * Gets resolution SLA score of current user
     * @return float|int
     * @throws \Exception
     * @throws \Throwable
     */
    private function getResolutionSlaMetScore()
    {
        // get SLA score of the system and then compare it with logged in user
        $systemResolutionSlaScore = $this->getSystemResolutionSlaMetScore();

        if(!$systemResolutionSlaScore){
            throw new \UnexpectedValueException("No ticket present in the system for the given filters");
        }

        $result = $this->getBaseQueryForAgentPerformance()->select(DB::raw("SUM(is_resolution_sla)/$systemResolutionSlaScore as score"), "assigned_to")
            ->where("is_resolution_sla", 1)
            ->groupBy("assigned_to")
            ->orderBy("score", "desc")
            ->orderBy("assigned_to", "desc")
            ->get()->transform(function ($element){
                return (object) [
                    "value" => $element->score,
                    "referer_id" => $element->assigned_to
                ];
            });

        return $this->getAgentPercentageComparision($result);
    }

    /**
     * Gets resolution SLA score of current user
     * @return float|int
     * @throws \Exception
     * @throws \Throwable
     */
    private function getResponseSlaMetScore()
    {
        // get SLA score of the system and then compare it with logged in user
        $systemResponseSlaScore = $this->getSystemResponseSlaMetScore();

        if(!$systemResponseSlaScore){
            throw new \UnexpectedValueException("No ticket present in the system for the given filters");
        }
        $result = $this->getBaseQueryForAgentPerformance()->select(DB::raw("SUM(is_response_sla)/$systemResponseSlaScore as score"), "assigned_to")
            ->groupBy("assigned_to")
            ->where("is_response_sla", 1)
            ->orderBy("score", "desc")
            ->orderBy("assigned_to", "desc")
            ->get()->transform(function ($element){
                return (object) [
                    "value" => $element->score,
                    "referer_id" => $element->assigned_to
                ];
            });

        return $this->getAgentPercentageComparision($result);
    }

    /**
     * gets agent percentage comparision
     * @param Collection $records
     * @return int
     * @throws \Throwable
     */
    private function getAgentPercentageComparision(Collection $records) : int
    {
        $betterScorerCountThanUser = $records->search(function($element){
            return  $element->referer_id == $this->user->id;
        });

        if ($betterScorerCountThanUser === false){
            throw new \UnexpectedValueException("No agent threads present in the system for the given time range");
        }

        return $this->calculatePercentage($betterScorerCountThanUser, $this->getAgentCountExceptLoggedInAgent());
    }

    /**
     * Gets count for agents which are there in the system except the logged in user
     * @return mixed
     */
    private function getAgentCountExceptLoggedInAgent()
    {
        return User::whereIn("role", ["agent", "admin"])->where("id", "!=", $this->user->id)->where("active", 1)->count();
    }

    /**
     * Calculates percentage based on max value
     * @param float $value
     * @param float $maxValue
     * @return float|int
     * @throws \Throwable
     */
    private function calculatePercentage(float $value, float $maxValue) : int
    {
        throw_if($maxValue == 0, new \UnexpectedValueException("division by zero not allowed"));

        return (int)((($maxValue - $value)/$maxValue)*100);
    }

    /**
     * Gets resolution time for the whole system
     * @return QueryBuilder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    private function getSystemResolutionTimeScoreObject()
    {
        return $this->getBaseQueryForAgentPerformance()->select(DB::raw("SUM(resolution_time) as total_resolution_time"),
            DB::raw("COUNT(tickets.id) as total_ticket_count"), "assigned_to")
            ->where("closed", 1)
            ->orderBy("total_resolution_time", "desc")
            ->groupBy("closed")
            ->first();
    }

    /**
     * Gets system avg response object
     * @return QueryBuilder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    private function getSystemResponseTimeScoreObject()
    {
        return $this->getBaseQueryForAgentPerformance()->join("ticket_thread","tickets.id", "=","ticket_thread.ticket_id")
            ->select(
                DB::raw("SUM(response_time) as total_response_time"),
                DB::raw("COUNT(ticket_thread.id) as total_thread_count"), "assigned_to", "ticket_thread.poster")
            ->where("ticket_thread.is_internal", 0)
            ->where("ticket_thread.poster", "support")
            ->orderBy("total_response_time", "desc")
            ->groupBy("ticket_thread.poster")
            ->first();
    }

    /**
     * Gets system resolution SLA object
     * @throws \Exception
     */
    private function getSystemResolutionSlaMetScore()
    {
        return $this->getBaseQueryForAgentPerformance()->select(DB::raw("SUM(is_resolution_sla) as total_resolution_sla"))
            ->where("is_resolution_sla", 1)
            ->groupBy("is_resolution_sla")
            ->value("total_resolution_sla");
    }

    /**
     * Gets system resolution SLA object
     * @throws \Exception
     */
    private function getSystemResponseSlaMetScore()
    {
        return $this->getBaseQueryForAgentPerformance()->select(DB::raw("SUM(is_response_sla) as total_response_sla"))
            ->where("is_response_sla", 1)
            ->groupBy("is_response_sla")
            ->value("total_response_sla");
    }

    /**
     * Gets base query for widgets
     * @param string $widgetType
     * @param int|null $filterId
     * @return QueryBuilder
     * @throws \Exception
     */
    protected function getBaseQueryForWidget(string $widgetType, int $filterId = null) : QueryBuilder
    {
        if($widgetType == 'filter'){
            return $this->getBaseQueryForTickets(new Request(TicketFilter::getFilterParametersByFilterId($filterId)), false);
        }

        $filtersForQuery = $this->getFiltersByWidgetType($widgetType);

        return $this->getBaseQueryForTickets(new Request($filtersForQuery), false);
    }

    /**
     * Gets base query for widgets
     * @param $widgetType
     * @return QueryBuilder
     * @throws \Exception
     */
    private function getBaseQueryForAgentPerformance() : QueryBuilder
    {
        return Tickets::where("tickets.created_at", ">", Carbon::now()->subDays(7))->where("assigned_to", "!=", null);
    }

    /**
     * Gets redirect link
     * @param string $widgetType
     * @param int $filterId
     * @return string
     */
    protected function getRedirectLink(string $widgetType, int $filterId = null) : string
    {
        if($widgetType == 'filter'){
            return Config::get('app.url')."/tickets/filter/$filterId";
        }

        return $this->getInboxFilterUrl("", $this->getFiltersByWidgetType($widgetType));
    }

    /**
     * Gets filters by widget type
     * @param $widgetType
     * @return array
     */
    private function getFiltersByWidgetType(string $widgetType)
    {
        switch ($widgetType){
            case "my_overdue_tickets":
                return ["assignee-ids"=> [$this->user->id], "category" => "overdue"];

            case "my_due_today_tickets":
                return ["assignee-ids"=> [$this->user->id], "due-on" => "next::1~day"];

            case "my_pending_approvals":
                return ["category" => "waiting-for-approval"];

            case "open_tickets":
                return ["category" => "inbox"];

            case "unassigned_tickets":
                return ["category" => "unassigned"];

            case "overdue_tickets":
                return ["category" => "overdue"];

            case "my_tickets":
                return ["category" => "mytickets"];

            case "unanswered_tickets":
                return ["category" => "inbox", "answered"=>0];

            default:
                throw new \InvalidArgumentException("widget type not found");
        }
    }

    /**
     * Gets default widgets type for top dashboard layout
     * @return array
     */
    private function getDefaultTopWidgetTypes()
    {
        $widgetTypes = [
            "my_tickets"=> ['icon_class'=>'fas fa-inbox', 'icon_color'=>'#007bff'],
            "my_overdue_tickets"=> ['icon_class'=>'far fa-calendar-times', 'icon_color'=>'#dd4b39'],
            "my_due_today_tickets" => ['icon_class'=>'far fa-calendar-times', 'icon_color'=>'#f39c12'],
            "my_pending_approvals"=> ['icon_class'=>'far fa-clock', 'icon_color'=>'#00c0ef'],
        ];

        // check if user doesn't have restricted access OR have global access. In that case he will be seeing "unassigned_tickets", "total_overdue_tickets"
        if(!User::has('restricted_access') || User::has('global_access')) {
            $widgetTypes = array_merge($widgetTypes,
                [
                    "open_tickets"=> ['icon_class'=>'fas fa-inbox', 'icon_color'=>'#007bff'],
                    "overdue_tickets"=> ['icon_class'=>'far fa-calendar-times', 'icon_color'=>'#dd4b39'],
                    "unassigned_tickets"=> ['icon_class'=>'fas fa-user-times', 'icon_color'=>'#f39c12'],
                    "unanswered_tickets"=> ['icon_class'=>'fas fa-reply', 'icon_color'=>'#00c0ef'],
                ]);
        }

        return $widgetTypes;
    }

    /**
     * Get the dashboard page.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDashboardView()
    {
        // pass roles along
        $roles = ["agent"];

        if(Auth::user()->role == "admin"){
            $roles[] = "admin";
        }

        // check if agent is a manager
        if(DepartmentAssignManager::where('manager_id', Auth::user()->id)->count()){
            $roles[] = "manager";
        }

        // get count of widgets
        $topWidgetCount =  count($this->getDefaultTopWidgetTypes()) + TicketFilter::where(["user_id"=> Auth::user()->id, 'display_on_dashboard'=> 1])->count();

        return view('report::dashboard', compact("roles", "topWidgetCount"));
    }


    /**
     * Get the dashboard page.
     * @deprecated
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOldDashboardView()
    {
        return (new \App\Http\Controllers\Agent\helpdesk\DashboardController)->index();
    }
}