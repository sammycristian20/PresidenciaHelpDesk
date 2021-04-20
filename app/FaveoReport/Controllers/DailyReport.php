<?php


namespace App\FaveoReport\Controllers;


use App\FaveoLog\Model\ExceptionLog;
use App\FaveoLog\Model\MailLog;
use App\FaveoReport\Structure\IndividualReport;
use App\FaveoReport\Structure\IndividualReportElement;
use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Validation\UnauthorizedException;
use Lang;

/**
 * Calculates and send daily report to agents
 * data which is calculated
 *
 * ALL AGENTS/ADMINS :
 *
 * REQUIRES IMMEDIATE ACTION
 * - tickets which are assigned to agent, reopened and still opened in last one day
 *
 * DUE IN NEXT 24 HOURS
 * - tickets which are due today
 *
 * OVERDUE IN LAST 24 HOURS
 * - tickets which got overdue yesterday
 *
 * TICKETS WHICH WERE RESOLVED IN LAST 24 HOURS
 * - tickets count which was resolved yesterday (percent of tickets whose resolution SLA WAS MET)
 *
 * TICKET WHICH REQUIRES HIS APPROVAL
 * - tickets which requires person's approval
 *
 * DEPARTMENT MANAGER
 *
 * UNASSIGNED TICKETS
 * - tickets which are unassigned in his department
 *
 * AGENTS WITH TICKET REOPENED AND OVERDUE
 * - list of agents which has reopened and overdue tickets with count
 */


class DailyReport extends BaseReportController
{

    private $recordsPerReport = 10;

    private $agentTimezone = "UTC";

    CONST CLASS_NAME_FOR_COUNT = "badge bg-blue";

    public function __construct()
    {
        $this->agentTimezone = Auth::check() ? agentTimeZone() : "UTC";
    }

    /**
     * Sends daily report to all agents
     * @throws \Throwable
     */
    public function sendDailyReport()
    {
        $agentList = User::whereIn("role", ["admin", "agent"])->get();

        foreach ($agentList as $agent) {
            Auth::onceUsingId($agent->id);
            $this->agentTimezone = agentTimeZone();
            $this->user = $agent;
            $this->sendMail($agent);
        }
    }

    ############################################################# AGENT SECTION #############################################################
    #
    # Contains following reports:
    # - Require immediate attention of the agent (tickets which are reopened and overdue)
    # - Tickets which are due in next 24 hours
    # - Tickets which were resolved before 24 hours
    # - Tickets which requires Agents approval
    #
    ##########################################################################################################################################

    /**
     * Gets reopened or overdue tickets and sort them in a way that tickets which are reopened, overdue and assigned to
     * current agent gets the first priority, ticket which is reopened and overdue gets the second priority,
     * tickets which are reopened gets the 3rd priority and tickets which are overdue gets the last priority
     * @param User $agent
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getRequireImmediateActionTickets(User $agent)
    {
        $loggedInAgentId = Auth::user()->id;
        // all tickets assigned to logged in agent, out of them
        $baseQuery = $this->getBaseQueryWithThread([], Auth::user()->id)
            ->select(
                DB::raw("(case when assigned_to = $loggedInAgentId then 1 else 0 end) as assigned_to_me"))
            ->where(function($q) use ($agent){
                $q->whereIn("status", getStatusArray("open"))
                    ->where(function($q){
                      $q->where("reopened", 1)->orWhere("duedate", "<", Carbon::now());
                    });
            })
            ->orderBy("assigned_to_me", "desc")
            ->orderBy("reopened", "desc")
            ->orderBy("duedate", "asc");

        return $this->getFormattedGeneralAgentReport($baseQuery, "require_immediate_action",
            'duedate', 'due_at', Closure::fromCallable([$this, 'addMetaDataToImmediateActionWidget']));
    }

    /**
     * Adds meta data (like if reopened, assigned_to_me, overdue) to elements passed
     * @param Tickets $ticketData
     * @param IndividualReportElement $reportElement the single report element from which data should be extracted
     */
    private function addMetaDataToImmediateActionWidget(Tickets $ticketData, IndividualReportElement &$reportElement)
    {
        $reportElement->metaData = ['assigned_to_me'=> $ticketData->assigned_to_me, 'reopened'=> $ticketData->reopened,
                    'overdue'=> $ticketData->duedate < Carbon::now()->toDateTimeString()];
    }

    /**
     * Gets tickets which are due in next 24 hours
     * @param User $agent
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    private function getDueIn24HoursTickets(User $agent)
    {
        $baseQuery = $this->getBaseQueryWithThread( ["due-on"=> "next::24~hour", "assignee-ids"=> [$agent->id], "status-ids"=> getStatusArray("open")], $agent->id)
            // whichever ticket is due as earliest
            ->orderBy("duedate", "asc");

        return $this->getFormattedGeneralAgentReport($baseQuery, "tickets_due_in_next_24_hours");
    }

    /**
     * Gets tickets which were resolved in last 24 hours
     * @param User $agent
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    private function getResolvedIn24HoursTickets(User $agent)
    {
        $baseQuery = $this->getBaseQueryWithThread(["is-resolved"=> 1, "closed-at"=>"last::24~hour", "assigned"=> $agent->id], $agent->id);

        return $this->getFormattedGeneralAgentReport($baseQuery, "tickets_resolved_in_last_24_hours", "closed_at", "resolved_at");
    }


    /**
     * Gets tickets which requires approval of the agent
     * @param User $agent
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    private function getRequireMyApprovalTickets(User $agent)
    {
        $baseQuery = $this->getBaseQueryWithThread(["category"=>"waiting-for-approval"], $agent->id);

        return $this->getFormattedGeneralAgentReport($baseQuery, "tickets_which_requires_your_approval");
    }

    ############################################################ END ######################################################################


    ########################################################## MANAGER SECTION #############################################################
    #
    # Contains following reports
    #
    # - Agent Analysis:
    #   - Assigned open tickets for an agent
    #   - Reopened tickets for an agent
    #   - Overdue tickets for an agent
    #
    # - Department Analysis
    #   - Created tickets in last 24 hours in a department
    #   - Open Tickets in that department
    #   - Unapproved Ticket in that department
    #   - Overdue ticket in that department
    #   - Unassigned ticket in that department
    #   - Reopened tickets in that department
    ########################################################################################################################################

    /**
     * Gets data which is specific to manager
     * @param User $agent
     * @param string $type  possible values `agent-analysis-only` and `department-analysis-only`
     * @return object
     * @throws \Exception
     */
    public function getManagerSpecificReport(User $agent, string $type = null)
    {
        if($agent->role == "admin"){
            $departmentIdsOfManager = [];
            $agentIds = [];
        } else {
            // getting agent ids of department which manager belongs to
            $departmentIdsOfManager = $this->getDepartmentIdsOfManager($agent);

            if(!$departmentIdsOfManager){
                throw new UnauthorizedException("This data cannot be accessed by a non-manager agent");
            }

            $agentIds = DepartmentAssignAgents::whereIn("department_id", $departmentIdsOfManager)->where("agent_id", "!=", $agent->id)->pluck("agent_id")->toArray();
        }

        if($type == "agent-analysis-only"){
            return $this->getAgentsAnalysis($agentIds, $departmentIdsOfManager, $agent->id);
        }

        if($type == "department-analysis-only"){
            return $this->getDepartmentAnalysis($departmentIdsOfManager, $agent->id);
        }

        return [$this->getAgentsAnalysis($agentIds, $departmentIdsOfManager, $agent->id), $this->getDepartmentAnalysis($departmentIdsOfManager, $agent->id)];
    }

    /**
     * Gets agent analysis in a set of departments
     * @param array $agentIds
     * @param array $departmentIdsOfManager
     * @param int $managerId
     * @return object
     * @throws \Exception
     */
    protected function getAgentsAnalysis(array $agentIds, array $departmentIdsOfManager, int $managerId)
    {
        // get all agent of the department and get analysis one by one for those departments
        $baseFilter = ["dept-ids"=> $departmentIdsOfManager, "status-ids"=> getStatusArray("open")];

        // get tickets assigned to these agents and group by agents, and their count for the respective tickets
        $baseQuery = $this->getBaseQueryWithThread(array_merge(["assignee-ids"=> $agentIds], $baseFilter), $managerId);

        $result = $baseQuery->select("tickets.id", "ticket_number", "duedate", "assigned_to",

            $this->getRawQueryForCountByType("open_tickets"),

            $this->getRawQueryForCountByType("reopened_tickets"),

            $this->getRawQueryForCountByType("overdue_tickets")

        )
        ->where("assigned_to", "!=", null)
        ->orderBy("reopened_tickets", "desc")
        ->orderBy("overdue_tickets", "desc")
        ->orderBy("open_tickets", "desc")
        ->groupBy("assigned_to")
        ->paginate($this->recordsPerReport);

        $report = new IndividualReport();
        $report->setTitle("agents_summary");
        $report->setDescription("agents_summary_description");
        $report->type = "agents_summary";
        $report->total = $result->total();

        $result->map(function($element) use (&$report, $baseFilter){
            $report->injectData($this->getFormattedAgentAnalysisElement($element, $baseFilter));
        });

        return $report;
    }

    /**
     * Gets redirect link by its type
     * @param $type
     * @param $baseFilter
     * @param $textToDisplay
     * @return string
     */
    public function getTicketRedirectLinkByType($type, $baseFilter, $textToDisplay)
    {
        $filter = array_merge($this->getFilterByType($type), $baseFilter);

        $redirectLink = $this->getInboxFilterUrl($type, $filter);

        return $this->getHyperlink($redirectLink, $textToDisplay, self::CLASS_NAME_FOR_COUNT);
    }

    /**
     * Gets department analysis of the passed department ids
     * @param array $departmentIds
     * @param int $managerId
     * @return object
     * @throws \Exception
     */
    protected function getDepartmentAnalysis(array $departmentIds, int $managerId)
    {
        // open tickets, unapproved tickets, overdue tickets
        $baseQuery = $this->getBaseQueryWithThread(["dept-ids"=> $departmentIds], $managerId);

        $result = $baseQuery->select(
            "dept_id",

            $this->getRawQueryForCountByType("created_tickets_in_last_24_hours"),

            $this->getRawQueryForCountByType("reopened_tickets"),

            $this->getRawQueryForCountByType("open_tickets"),

            $this->getRawQueryForCountByType("unapproved_tickets"),

            $this->getRawQueryForCountByType("overdue_tickets"),

            $this->getRawQueryForCountByType("unassigned_tickets")

        )->groupBy("dept_id")
            ->orderBy("reopened_tickets", "desc")
        ->orderBy("overdue_tickets", "desc")
        ->orderBy("open_tickets", "desc")
        ->paginate($this->recordsPerReport);

        $report = new IndividualReport();
        $report->setTitle("department_summary");
        $report->setDescription("department_summary_description");
        $report->type = "department_summary";
        $report->total = $result->total();

        $result->getCollection()->map(function ($element) use (&$report){
            $report->injectData($this->getFormattedDepartmentReportElement($element));
        });

        return $report;
    }

    ################################################################## END ##########################################################################

    ############################################################## ADMIN SECTION ####################################################################
    #
    # Contains the following reports:
    #
    # - how many tickets were created in last 24 hours
    # - how many users were created in last 24 hours
    # - how many mails were received in the system in last 24 hours
    # - how many mails were sent out successfully from the system in last 24 hours
    # - how many mails were queued in the system in last 24 hours
    # - how many mails were failed to sent out from the system in last 24 hours
    # - how many exceptions were caught in the system in last 24 hours
    #
    #################################################################################################################################################

    /**
     * Gets overall system health report for last 24 hours
     */
    public function getSystemAnalysis()
    {
        $report = new IndividualReport();

        $report->setTitle("system_summary");

        $report->injectData($this->getSystemAnalysisElementObject("received_tickets", $this->getBaseQueryForSystemAnalysis(Tickets::query())->count()));

        $report->injectData($this->getSystemAnalysisElementObject("users_created", $this->getBaseQueryForSystemAnalysis(User::query())->count()));

        $report->injectData($this->getSystemAnalysisElementObject("mails_received", $this->getBaseQueryForMailLogs(true)->count()));

        $report->injectData($this->getSystemAnalysisElementObject("mails_sent", $this->getBaseQueryForMailLogs(false, "sent")->count()));

        $report->injectData($this->getSystemAnalysisElementObject("mails_queued", $this->getBaseQueryForMailLogs(false, "queued")->count()));

        $report->injectData($this->getSystemAnalysisElementObject("mails_failed", $this->getBaseQueryForMailLogs(false, "failed")->count()));

        $report->injectData($this->getSystemAnalysisElementObject("exceptions_caught", $this->getBaseQueryForSystemAnalysis(ExceptionLog::query())->count()));

        return $report;
    }

    ################################################################## END ##########################################################################


    /**
     * Gets department ids of a manager
     * @param User $agent
     * @return mixed
     */
    private function getDepartmentIdsOfManager(User $agent) : array
    {
        return DepartmentAssignManager::where("manager_id", $agent->id)->pluck("department_id")->toArray();
    }

    /**
     * Gets base query for ticket
     * @param array $params
     * @param int $userId
     * @return Builder
     * @throws \Exception
     */
    private function getBaseQueryWithThread(array $params, int $userId)
    {
        $request = new Request($params);

        // Auth::loginUsingId($userId);

        return $this->getBaseQueryForTickets($request);
    }

    /**
     * This is a helper method to transform general agent report
     * @param $baseQuery
     * @param $reportTitle
     * @param string $columnForDateTime
     * @param string $dateText
     * @param \Closure|null $modifyElement if any function wants to modify individual elements, they can use this callback
     * @return mixed
     */
    private function getFormattedGeneralAgentReport(&$baseQuery, $reportTitle, $columnForDateTime = "duedate", $dateText = "due_at", \Closure $modifyElement = null)
    {
        // selecting duedate 2 time so that query in TicketListController stays disrupted
        $result = $baseQuery->addSelect(["tickets.id", "ticket_number", "duedate", "$columnForDateTime as date_time", 'reopened'])
            ->orderBy("tickets.created_at", "asc")
            ->paginate($this->recordsPerReport);

        $report = new IndividualReport();

        $report->setTitle($reportTitle);

        $report->setDescription($reportTitle."_description");

        $report->total = $result->total();

        $result->getCollection()->map(function ($element) use ($dateText, &$report, $modifyElement) {

            $title = isset($element->firstThread->title) ? $element->firstThread->title : "";
            // updating ticket hyperlink in title
            $this->ticketHyperLink("title",$element,$element->ticket_number. " ". $title);

            if($element->date_time){
                $timeInAgentTimezone = changeTimezoneForDatetime($element->date_time, 'UTC', $this->agentTimezone)->format(dateTimeFormat());
            } else {
                $timeInAgentTimezone = null;
            }

            $reportElement = new IndividualReportElement();
            $reportElement->title = $element->title;
            $reportElement->setAttribute($dateText, $timeInAgentTimezone);

            $modifyElement && $modifyElement($element, $reportElement);

            $report->injectData($reportElement);
        });

        return $report;
    }

    /**
     * Gets base query for mail logs by appending mail log category and status check
     * @param $isIncomingMail
     * @param string $status
     * @return mixed
     */
    private function getBaseQueryForMailLogs($isIncomingMail, $status = "")
    {
        $relationToMailFetch = $isIncomingMail ? "=" : "!=";

        $baseQuery = $this->getBaseQueryForSystemAnalysis(MailLog::query())->whereHas("category", function ($q) use ($relationToMailFetch) {
            $q->where("name", $relationToMailFetch, "mail-fetch");
        });

        if($status){
            $baseQuery = $baseQuery->where("status", $status);
        }

        return $baseQuery;
    }

    /**
     * Gets IndividualReportElement object for system analysis report elements
     * @param string $title
     * @param int $value
     * @return IndividualReportElement
     */
    private function getSystemAnalysisElementObject($title, $value) : IndividualReportElement
    {
        $reportElement = new IndividualReportElement();

        $reportElement->setTitle($title);

        $reportElement->setAttribute(null, $value);

        return $reportElement;
    }

    /**
     * Gets base query for system analysis by appending 24 hour interval
     * @param $baseQuery
     * @return mixed
     */
    private function getBaseQueryForSystemAnalysis($baseQuery)
    {
        return $baseQuery->where("created_at", "<=", Carbon::now())->where("created_at", ">=", Carbon::now()->subHours(24));
    }

    /**
     * Gets view for report
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getView()
    {
        $this->user = Auth::user();

        $company = Company::first();
        $companyLogo = $company->logo;
        $companyName = $company->company_name;

        $reports = [];

        $reports[] = $this->getRequireImmediateActionTickets($this->user);
        $reports[] = $this->getDueIn24HoursTickets($this->user);
        $reports[] = $this->getResolvedIn24HoursTickets($this->user);
        $reports[] = $this->getRequireMyApprovalTickets($this->user);

        $isDepartmentManager = DepartmentAssignManager::where('manager_id', $this->user->id)->count();

        if($isDepartmentManager){
            $reports = array_merge($reports, $this->getManagerSpecificReport($this->user));
        }

        if(Auth::user()->role = "admin"){
            $reports[] = $this->getSystemAnalysis();
        }

        $date = Carbon::now($this->agentTimezone)->format("F j, Y");

        return view("report::daily-report", compact('reports', "companyName", "companyLogo", "date"));
    }

    /**
     * Gets raw query for ticket count by its type
     * @param $type
     * @return \Illuminate\Database\Query\Expression
     */
    private function getRawQueryForCountByType($type)
    {
        switch ($type) {

            case "created_tickets_in_last_24_hours":
                return DB::raw("SUM(case when tickets.created_at > '".Carbon::now()->subHours(24)->toDateTimeString()."' AND tickets.created_at < '".Carbon::now()->toDateTimeString()."' then 1 else 0 end) as created_tickets_in_last_24_hours");

            case "open_tickets":
                return DB::raw("SUM(case when status IN (".implode(",",getStatusArray("open")).") then 1 else 0 end) as open_tickets");

            case "unapproved_tickets":
                return DB::raw("SUM(case when status IN (".implode(",",getStatusArray("unapproved")).") then 1 else 0 end) as unapproved_tickets");

            case "unassigned_tickets":
                return DB::raw("SUM(case when assigned_to IS NULL AND team_id IS NULL AND status IN (".implode(",",getStatusArray("open")).") then 1 else 0 end) as unassigned_tickets");

            case "reopened_tickets":
                return DB::raw("SUM(case when reopened != 0 AND reopened IS NOT NULL then 1 else 0 end) as reopened_tickets");

            case "overdue_tickets":
                return DB::raw("SUM(case when duedate < '".Carbon::now()->toDateTimeString()."' AND status IN (".implode(",",getStatusArray("open")).")  then 1 else 0 end) as overdue_tickets");

            default:
                throw new \UnexpectedValueException("invalid type passed");
        }
    }

    /**
     * Gets required filter by type
     * @param $type
     * @return array
     */
    private function getFilterByType($type)
    {
        switch ($type) {

            case "created_tickets_in_last_24_hours":
                // get date in agent's timezone
                $startTime = changeTimezoneForDatetime(Carbon::now()->subHours(24), "UTC", $this->agentTimezone)->toDateTimeString();
                $endTime = changeTimezoneForDatetime(Carbon::now(), "UTC", $this->agentTimezone)->toDateTimeString();
                return ["created-at"=> "date::$startTime~$endTime"];

            case "open_tickets":
                return ["status-ids" => getStatusArray("open")];

            case "unapproved_tickets":
                return ["category" => "unapproved"];

            case "unassigned_tickets":
                return ["category" => "unassigned"];

            case "reopened_tickets":
                return ["reopened" => 1];

            case "overdue_tickets":
                return ["category" => "overdue"];

            default:
                throw new \UnexpectedValueException("invalid type passed");
        }
    }

    /**
     * Formats agent analysis report into required format
     * @param $object
     * @param $baseFilter
     * @return IndividualReportElement
     */
    private function getFormattedAgentAnalysisElement($object, $baseFilter)
    {
        $baseFilter = array_merge(["assignee-ids"=> [$object->assigned_to]], $baseFilter);

        $reportElement = new IndividualReportElement();

        $reportElement->title = $object->assigned ? $this->getHyperlink(url('agent/'.$object->assigned->id), $object->assigned->full_name) : "";

        $reportElement->picture = $object->assigned ? $object->assigned->profile_pic : null;

        $reportElement->setAttribute("reopened_tickets", $this->getTicketRedirectLinkByType("reopened_tickets", $baseFilter, (int)$object->reopened_tickets));

        $reportElement->setAttribute("overdue_tickets", $this->getTicketRedirectLinkByType("overdue_tickets", $baseFilter, (int)$object->overdue_tickets));

        $reportElement->setAttribute("assigned_open_tickets", $this->getTicketRedirectLinkByType("open_tickets", $baseFilter, (int)$object->open_tickets));

        return $reportElement;
    }

    /**
     * Formats Department report in required way
     * @param $element
     * @return IndividualReportElement
     */
    private function getFormattedDepartmentReportElement($element)
    {
        $reportElement = new IndividualReportElement();

        $reportElement->title = $element->departments->name;

        $baseFilter = ["dept-ids"=> [$element->dept_id]];

        $reportElement->setAttribute("created_tickets_in_last_24_hours",
            $this->getTicketRedirectLinkByType("created_tickets_in_last_24_hours", $baseFilter, (int)$element->created_tickets_in_last_24_hours));

        $reportElement->setAttribute("open_tickets",
            $this->getTicketRedirectLinkByType("open_tickets", $baseFilter, (int)$element->open_tickets));

        $reportElement->setAttribute("unapproved_tickets",
            $this->getTicketRedirectLinkByType("unapproved_tickets", $baseFilter, (int)$element->unapproved_tickets));

        $reportElement->setAttribute("overdue_tickets",
            $this->getTicketRedirectLinkByType("overdue_tickets", $baseFilter, (int)$element->overdue_tickets));

        $reportElement->setAttribute("unassigned_tickets",
            $this->getTicketRedirectLinkByType("unassigned_tickets", $baseFilter, (int)$element->unassigned_tickets));

        $reportElement->setAttribute("reopened_tickets",
            $this->getTicketRedirectLinkByType("reopened_tickets", $baseFilter, (int)$element->reopened_tickets));

        return $reportElement;
    }

    /**
     * Sends mail to concerned agent for daily report
     * @param User $agent
     * @throws \Throwable
     */
    public function sendMail(User $agent)
    {
        if(filter_var($agent->email, FILTER_VALIDATE_EMAIL)) {

            // get view and render it, now pass that as body to phpMailController
            $phpMailController = new PhpMailController;

            $from = $phpMailController->mailfrom('0', null);

            $to = ['name' => '', 'email' => $agent->email];

            $message = $this->getView()->render();

            $phpMailController->sendmail($from, $to, $message, []);
        }
    }
}