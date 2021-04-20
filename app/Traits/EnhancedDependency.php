<?php

namespace App\Traits;

use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Manage\HeltopicAssignType;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Status as TicketStatus;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use App\Model\helpdesk\Agent\UserPermission;
use App\Constants\Permission;

trait EnhancedDependency
{

    /**
     * Function to get basic Query builder for getting allowed override statuses for the given status/es
     * @param  array       $ticketIds     Array of ticket Ids for which we need allowed override statuses
     * @param  string      $searchQuery   search query string passed for status with name
     * @var    Collection  $tickets       Collection of tickets with given IDs
     * @var    array       $allowes       Array containing unique Ids of allowed statuses
     * @var    Tickets     $baseQeury     Base query builder for ticket status list
     */
    protected function getOverrideStatuses($ticketIds = [], $searchQuery = '')
    {
        $statusIds = Tickets::whereIn('id', $ticketIds)->pluck('status')->toArray();
        $tickets   = TicketStatus::whereIn('id', $statusIds)->get();
        $allowed   = [];
        $baseQuery = TicketStatus::where('name', 'LIKE', "%$searchQuery%");
        foreach ($tickets as $ticket) {
            $targetStatusIds = $ticket->overrideStatuses()->pluck('target_status')->toArray();
            if ($targetStatusIds) {
                array_push($allowed, $targetStatusIds);
            }
        }

        if ($allowed) {
            $allowed = (count($allowed) < 2) ? reset($allowed) : call_user_func_array('array_intersect', $allowed);
            $baseQuery = $baseQuery->whereIn('id', $allowed);
        }

        return $baseQuery;
    }

    /**
     * Appends query to get helpTopic only which are linked with given departments
     * @param  QueryBuilder $baseQuery  query to which it has to appended
     * @param  array  $departmentIds
     * @return QueryBuilder
     */
    protected function limitLinkedHelpTopics($baseQuery, array $departmentIds)
    {
        // query into all helptopics, get all department ids,
        // for getting linked departments, get all departments, then query into department table
        return $baseQuery->where(function ($q) use ($departmentIds) {
            foreach ($departmentIds as $departmentId) {
                //to avoid sql injection
                $departmentId = (int) $departmentId;
                $q = $q->orWhereRaw("FIND_IN_SET($departmentId, linked_departments)")
                ->orWhereRaw("FIND_IN_SET($departmentId, department)");
            }
            return $q;
        });
    }

    /**
     * Appends query to get departments only which are linked with given helpTopics
     * @param  QueryBuilder $baseQuery  query to which it has to appended
     * @param  array  $helptopicIds
     * @return QueryBuilder
     */
    protected function limitLinkedDepartments($baseQuery, array $helptopicIds)
    {
      //query all helptopics, get all comma seperated values
        $departmentIds = HelpTopic::whereIn('id', $helptopicIds)->get()->pluck('linked_departments');
        $formattedDeptIds = [];
        foreach ($departmentIds as $commaSeperatedDeptIds) {
            if ($commaSeperatedDeptIds) {
                $formattedDeptIds = array_merge(explode(',', $commaSeperatedDeptIds), $formattedDeptIds);
            }
        }
        return $baseQuery->whereIn('id', $formattedDeptIds);
    }

    /**
     * Appends query to get types only which are linked with given helpTopics
     * @param  QueryBuilder $baseQuery  query to which it has to appended
     * @param  array  $helptopicIds
     * @return QueryBuilder
     */
    protected function limitLinkedTypes($baseQuery, array $helptopicIds = [])
    {
        $isLinkingModuleOn = CommonSettings::where('option_name', '=', 'helptopic_link_with_type')->value('status');

        if ($isLinkingModuleOn && count($helptopicIds) && $typeIds = HeltopicAssignType::where('helptopic_id', $helptopicIds)->pluck('type_id')->toArray()) {
            return $baseQuery->whereIn('id', $typeIds);
        }

        return $baseQuery;
    }

    /**
     * Method removed agent records from basequery filter if
     * - They do not belong to all departments of all the tickets
     * - They do not have global access permission
     *
     * @param   QueryBuilder  $baseQuery  Base query builder for User
     * @param   array         $ticketIds  array containing integer ticket IDs passed
     *                        in suppelements
     * @return  QueryBuilder  modified User query builder
     */
    protected function filterAgentByDepartmentsAndGlobalAccess($baseQuery, array $ticketIds)
    {
        $deptIds = Tickets::whereIn('id', $ticketIds)->distinct('dept_id')->pluck('dept_id')->toArray();
        $globalAccessPermissionId = UserPermission::where('key', Permission::GLOBALACCESS)->value('id');
        $depratmentAgentIDs = DepartmentAssignAgents::select('agent_id')->whereIn('department_id', $deptIds)->groupBy('agent_id')->havingRaw("COUNT(`agent_id`) = " . count($deptIds))->pluck('agent_id')->toArray();
        return $baseQuery->where(function ($query) use ($depratmentAgentIDs, $globalAccessPermissionId) {
            // if role is admin, there won't be any entry
            $query->whereIn('id', $depratmentAgentIDs)->orWhere('role', 'admin')->orWhereHas('permissions', function ($query) use($globalAccessPermissionId) {
                $query->where('user_permissions.id', $globalAccessPermissionId);
            });
        });
    }

    /**
     * Appends query to get agents which belong to specific departments
     * @param QueryBuilder $baseQuery query to which it has to appended
     * @param array $departmentIds
     * @return QueryBuilder
     */
    protected function limitLinkedAgents($baseQuery, array $departmentIds = [])
    {
        $agentIds = DepartmentAssignAgents::whereIn('department_id', $departmentIds)->pluck('agent_id')->toArray();
        $globalAccessPermissionId = UserPermission::where('key', Permission::GLOBALACCESS)->value('id');

        $baseQuery->where(function ($query) use ($agentIds, $globalAccessPermissionId) {
            // if role is admin, there won't be any entry
            $query->whereIn('id', $agentIds)->orWhere('role', 'admin')->orWhereHas('permissions', function ($query) use($globalAccessPermissionId) {
                $query->where('user_permissions.id', $globalAccessPermissionId);
            });
        });

        return $baseQuery;
    }
}
