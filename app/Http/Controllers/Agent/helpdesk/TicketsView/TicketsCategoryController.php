<?php
namespace App\Http\Controllers\Agent\helpdesk\TicketsView;

use Auth;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use Carbon\Carbon;
use App\Model\helpdesk\Agent\Assign_team_agent;
use App\Model\helpdesk\Agent\Teams as Team;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Manage\UserType;
use Exception;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Event;
use App\User;

/**
 * Handles queries for following categories: inbox, mytickets, all tickets, unassigned, closed, follow up, unapproved, deleted, overdues.
 * Methods inside this can be used by other classes to filter out tickets by category, userTypes and permissions
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketsCategoryController extends Controller
{

    /**
     * DepartmentIds to which logged in user is assigned
     * @param array
     */
    private $departmentIds;

    /**
     * currently logged in user
     * @param object
     */
    protected $user;

    /**
     * User has Restricted permission or not
     * @param boolean
     */
    private $isAccessRestricted;

    /**
     * User has Global permission or not
     * @param boolean
     */
    private $isAccessGlobal;

    /**
     * User has permission to view unapproved tickets or not
     * @param boolean
     */
    protected $canViewUnapprovedTickets;

    public function __construct()
    {
         $this->middleware(['auth', 'role.agent'])->except(['getAttachment','getTicketConversationByHash']);
    }

    private function populateLocalVariables()
    {
        $this->isAccessRestricted = User::has('restricted_access');
        $this->isAccessGlobal = User::has('global_access');
        $this->canViewUnapprovedTickets = User::has('view_unapproved_tickets');
    }

    /**
     * Gets the query for filtered tickets based on its category (inbox, mytickets etc).
     * @param string $category      Available categories are 'inbox', 'mytickets', 'closed', 'unassigned','deleted',
     *                              'unapproved' ,'approval'
     * @return QueryBuilder         Query for filtered tickets by category
     */
    public function ticketsQueryByCategory($category)
    {
        $this->user = $this->user ? : Auth::user();

        switch ($category) {
            case 'all':
                return $this->allTicketsQuery();

            case 'inbox':
                return $this->inboxQuery();

            case 'mytickets':
                return $this->myTicketsQuery();

            case 'closed':
                return $this->closedQuery();

            case 'unassigned':
                return $this->unassignedQuery();

            case 'overdue':
                return $this->overdueTicketsQuery();

            case 'due_today':
                return $this->dueTodayTicketsQuery();

            case 'deleted':
                return $this->deletedQuery();

            case 'unapproved':
                return $this->unapprovedQuery();

            case 'spam':
                return $this->spamQuery();

            case 'waiting-for-approval':
                return $this->waitingForApprovalQuery();

            default:
                return $this->allTicketsQuery();
        }
    }


    /**
     * It contains user specific logic of how tickets should be filtered by passed status
     * @param string $status        Required Status
     * @return QueryBuilder               Query after appending user specific restrictions
     */

    public function accessibleTickets($status = 'open')
    {
        $this->populateLocalVariables();

        $this->user = $this->user ? : Auth::user();

        $tickets = $this->ticketQueryBuilder($status);
        if ($this->user->role == 'agent') {
            if (!$this->isAccessGlobal && !$this->isAccessRestricted) {
                //so that departmentId only get populated once to avoid multiple querying
                $this->departmentIds = $this->departmentIds ?: DepartmentAssignAgents::where('agent_id', '=', $this->user->id)->pluck('department_id')->toArray();
                return $tickets->whereIN('dept_id', $this->departmentIds);
            }
            if ($this->isAccessRestricted) {
                return $tickets->where('assigned_to', $this->user->id);
            } else {
                return $tickets;
            }
        } else {
            return $tickets;
        }
    }

    /**
     * builds ticket query by joining status type with tickets query
     *
     * @param string $status    It is ticket status which can be open, closed, deleted etc.
     * @return QueryBuilder           Basic query based on status
     */
    private function ticketQueryBuilder($status)
    {
        $baseQuery = Tickets::query();
        // updating base query
        Event::dispatch('update-base-query', [&$baseQuery]);

        if(!$this->canViewUnapprovedTickets){
            $baseQuery = $baseQuery->whereIn('status', getStatusArray("unapproved", "!="));
        }

        if($status != "all"){
            $baseQuery = $baseQuery->whereIn('status', getStatusArray($status));
        }

        return $baseQuery;
    }

    /**
     * Gives query for all allowed tickets query for current user.
     * This query can also be used to check if a user is allowed a certain ticket or not.
     * @return QueryBuilder
     */
    public function allTicketsQuery()
    {
        return $this->accessibleTickets('all');
    }

    /**
     * all allowed open tickets query for current user
     * @return QueryBuilder
     */
    public function inboxQuery()
    {
        return $this->accessibleTickets();
    }

    /**
     * All open tickets query which are assigned to current user
     * @return QueryBuilder
     */
    public function myTicketsQuery()
    {
        $userId = \Auth::user()->id;
        $teamIds = Assign_team_agent::where('agent_id', $userId)->pluck('team_id')->toArray();


        if (\Auth::user()->role == "agent") {
            $agentId = getAgentbasedonPermission('global_access');
            if (!in_array($userId, $agentId)) {
                $departmentIds = DepartmentAssignAgents::where('agent_id', $userId)->pluck('department_id')->toArray();
                return $this->accessibleTickets()->whereIn('dept_id', $departmentIds)->where(function ($query) use ($userId, $teamIds) {
                    $query->where('assigned_to', $userId)->orWhereIn('team_id', $teamIds);
                });
            }
        }
        return $this->accessibleTickets()->where(function ($query) use ($userId, $teamIds) {
            $query->where('assigned_to', $userId)->orWhereIn('team_id', $teamIds);
        });
    }

    /**
     * all allowed closed tickets query for current user
     * @return QueryBuilder
     */
    public function closedQuery()
    {
        return $this->accessibleTickets('closed');
    }

    /**
     * all allowed unassigned tickets query for current user
     * @return QueryBuilder
     */
    public function unassignedQuery()
    {
        return $this->accessibleTickets()->where(function ($q) {
            $q->where('team_id', null)->orWhere('team_id', 0);
        })->where(function ($q) {
            $q->where('assigned_to', null)->orWhere('assigned_to', 0);
        });
    }

    /**
     * all allowed unapproved tickets query for current user
     * @return QueryBuilder
     */
    public function unapprovedQuery()
    {
        return $this->accessibleTickets('unapproved');
    }

    /**
     * all allowed deleted tickets query for current user
     * @return QueryBuilder
     */
    public function deletedQuery()
    {
        return $this->accessibleTickets('deleted');
    }

    /**
     * all allowed spam tickets query for current user
     * @return QueryBuilder
     */
    public function spamQuery()
    {
        return $this->accessibleTickets('spam');
    }


    /**
     * all allowed overdues tickets query for current user
     * @return QueryBuilder
     */
    public function overdueTicketsQuery()
    {
        return $this->accessibleTickets()
            ->whereNotNull('duedate')
            ->where('duedate', '<', Carbon::now());
    }


    /**
     * all allowed durToday tickets query for current user
     * @return QueryBuilder
     */
    public function dueTodayTicketsQuery()
    {
        //get EOD and currentTime in agentTZ. Convert that in UTC and filter due today accordingly
        $agentTimezone = agentTimeZone();

        //get eod in agent timezone. Convert that timestamp into UTC
        $todayEOD = Carbon::tomorrow($agentTimezone)->timezone('UTC');

        //current time
        $currentTime = Carbon::now();

        //query for duedate greater than current time and smaller than EOD
        return $this->accessibleTickets()->where('duedate', '>', $currentTime)->where('duedate', '<', $todayEOD);
    }

    /**
     * all allowed tickets that are waiting for approval from logged in user
     * @return QueryBuilder
     */
    public function waitingForApprovalQuery()
    {
        return $this->unapprovedQuery()
            ->whereHas('approvalStatus.approvalLevels', function ($q) {
                $q->where('is_active', 1)->whereHas('approverStatuses', function ($q2) {
                    $q2->where(function ($q3) {
                        $q3->where('approver_id', Auth::user()->id)
                        ->where('approver_type', 'App\User')
                        ->where('status', 'PENDING');
                    })
                    ->orWhere(function ($q3) {
                        $this->userTypeQueryBuilder($q3, 'department_manager');
                    })
                    ->orWhere(function ($q3) {
                        $this->userTypeQueryBuilder($q3, 'team_lead');
                    });
                });
            });
    }

    /**
     * Builds query for approver when approver is either team_lead or department_manager
     * NOTE: this method is specifically made as a helper method of `waitingForApprovalTicketsQuery` and
     * should not be used by any other method
     * @param  object $parentQuery
     * @param  number $approverId
     * @param  string $approverType   `department_manager` or `team_lead`
     * @return QueryBuilder
     */
    private function userTypeQueryBuilder($parentQuery, $approverType)
    {
        if ($approverType == 'department_manager') {
            $holderIds = DepartmentAssignManager::where('manager_id', Auth::user()->id)->pluck('department_id')->toArray();
            $holderKey = 'dept_id';
        } else {
            $holderIds = Team::where('team_lead', Auth::user()->id)->pluck('id')->toArray();
            $holderKey = 'team_id';
        }

        $approverId = UserType::where('key', $approverType)->value('id');

        return $parentQuery->where(function ($q) use ($approverId, $holderIds, $holderKey) {
            $q->where('approver_id', $approverId)
            ->where('approver_type', 'App\Model\helpdesk\Manage\UserType')
            ->where('status', 'PENDING')
            ->whereHas('approvalLevelStatus.approvalWorkflow.ticket', function ($q2) use ($holderIds, $holderKey) {
                $q2->whereIn($holderKey, $holderIds);
            });
        });
    }
}