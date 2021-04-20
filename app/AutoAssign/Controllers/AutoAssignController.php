<?php

namespace App\AutoAssign\Controllers;

use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Settings\CommonSettings;
use App\User;
use Auth;
use DB;


class AutoAssignController
{
    /**
     * @var bool
     */
    private $shallAssignBasedOnLocation;

    /**
     * @var bool
     */
    private $shallAssignBasedOnType;

    /**
     * Get the agent id
     * @param string $dept_id
     * @param string $type_id
     * @param string $locationId
     * @return int|null
     */
    public function getAssigneeId($dept_id="", $type_id="", $locationId="")
    {
        if(!isAutoAssign()){
            return;
        }

        if(!$this->isAutoAssignmentAllowedInDepartment($dept_id)){
            return null;
        }

        return $this->getAgentIdForAutoAssignment($dept_id,$type_id,$locationId);
    }

    /**
     * Gets id of the agent to whom ticket is supposed to be assigned
     * @param int $deptId
     * @param int $type
     * @param int $location
     * @return int
     */
    public function getAgentIdForAutoAssignment($deptId = null, $type = null, $location = null)
    {
        // if ticket doesn't have a departmemt, auto assign should not work
        if(!$this->shallEnforceAutoAssignment($deptId, $type, $location)){
            return null;
        }

        // build a baseQuery based on department passed
        $baseAgentQuery = $this->getBaseAgentQueryForDepartment($deptId);

        // query based on checks
        $baseAgentQuery = $this->appendAutoAssignmentConditionalQuery($baseAgentQuery, $location, $type);

        // query based on max number of ticket allowed
        $statusToBeEnforced = array_merge(getStatusArray('open'), getStatusArray('unapproved'));

        $agent = $baseAgentQuery->leftJoin('tickets', 'tickets.assigned_to', '=', 'users.id')
            ->select('users.id',
                DB::raw("SUM(case when status IN (".implode(",",$statusToBeEnforced).") then 1 else 0 end) as assigned_ticket_count"))
            ->orderBy('assigned_ticket_count', 'asc')
            ->orderBy('users.created_at', 'asc')
            ->groupBy('assigned_to')
            ->first();

        if(!$agent){
            return null;
        }

        $threshold = $this->getAllowedThresholdOfTickets();

        if($threshold && (int)$agent->assigned_ticket_count >= (int)$threshold){
            // means person with minimum number of tickets has reached his limit
            return null;
        }

        return $agent->id;
    }

    /**
     * If auto-assignment should be enforced on not
     * EXPLANATION: if department is not present in the ticket, auto assignment will not enforce
     *              if "assign based on type" setting is checked BUT value passed is null, it will not enforce
     *                      if "assign based on type" is not checked, it should check the next condition
     *              if "assign based on location" setting is checked BUT value passed in null, it will not enforce
     *                      if "assign based on location" is not checked, it should return true
     * @param $deptId
     * @param $type
     * @param $location
     * @return bool
     */
    private function shallEnforceAutoAssignment($deptId, $type, $location)
    {
        if(!$deptId){
            return false;
        }

        if($this->shallAssignBasedOnType = isAssignWithType()){
            if(!$type){
                return false;
            }
        }

        if($this->shallAssignBasedOnLocation = isAssignWithLocation()){
            if(!$location){
                return false;
            }
        }

        return true;
    }

    /**
     * Gets base query for agent based on passed department
     * @param $deptId
     * @return mixed
     */
    private function getBaseAgentQueryForDepartment($deptId)
    {
        // build a baseQuery based on department passed
        return User::join('department_assign_agents','users.id', '=', 'department_assign_agents.agent_id')
            ->where('department_assign_agents.department_id', $deptId)
            ->where('users.role', '!=', 'user')
            ->where('users.active', 1)
            ->where('users.is_delete', 0);
    }

    /**
     * Appends auto-assignement queries
     * @param $baseQuery
     * @param $location
     * @param $type
     * @return mixed
     */
    private function appendAutoAssignmentConditionalQuery($baseQuery, $location, $type)
    {
        return $baseQuery->when($this->shallAssignBasedOnLocation, function($query) use($location) {
            return $query->where('users.location', '!=', null)->where('users.location', '=', $location);
        })
        ->when($this->shallAssignBasedOnType, function($query) use($type) {
            return $query->join('agent_type_relations', 'users.id', '=', 'agent_type_relations.agent_id')
                ->where('agent_type_relations.type_id', '=', $type);
        })
        ->when(!isAssignIfNotAccept(), function($query) {
            return $query->where('users.not_accept_ticket', '=', 0);
        })
        ->when(isOnlyLogin(), function($query) {
            return $query->where('users.is_login', '=', 1);
        });
    }

    /**
     * Gets allowed threshold in auto assign settings
     * @return int
     */
    private function getAllowedThresholdOfTickets()
    {
        $threshold = (new CommonSettings())->getOptionValue('auto_assign', 'threshold')->option_value;
        if(!$threshold){
            // returning some big number to ease number of logics
            return 10000000;
        }
        return $threshold;
    }

    /**
     * Checks if department of the ticket is available in the auto assign configuration list.
     * @param $agentId
     * @param $deptId
     * @return null
     */
    public function isAutoAssignmentAllowedInDepartment($deptId)
    {
        if (deptAssignOption() == 'specific') {
            return Department::where('en_auto_assign', 1)->where('id', $deptId)->count();
        }
        return true;
    }

    /**
     * Updates in ticket table when user logs out
     * NOTE: it should be handled at core level but its migration exists in Auto-assign, so not moving
     */
    public function handleLogout()
    {
        $user = Auth::user();
        if ($user) {
            $user->is_login = 0;
            $user->save();
        }
    }

    /**
     * When agent logs in
     */
    public function handleLogin() {
        $user = Auth::user();
        if ($user) {
            $user->is_login = 1;
            $user->save();
        }
    }
}