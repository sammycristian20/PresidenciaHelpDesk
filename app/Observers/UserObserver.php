<?php

namespace App\Observers;

use App\Model\helpdesk\Agent\Assign_team_agent;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\User;

class UserObserver
{

    protected $user;

    /**
     * Listen to an updated event in user update
     * @param User $user
     * @return void
     */
    public function updating(User $user)
    {
        $this->user = $user;
        $this->handleAccess();
        //for change role to user
        if ($user->role == 'user') {

            $this->handleTickets($user->id);

            $this->handleDepartment($user->id);

            $this->handleTeam($user->id);

            $this->handleDepartmentManager($user->id);

        }
        //for change role to admin/agent
        if ($user->role != 'user') {
            $this->handleOrganization($user->id);
        }


        //for deactivate
        if ($user->active == 0) {
            $this->removeUserId($user->id);
        }
    }
    public function handleAccess()
    {
        $changes = $this->user->isDirty() ? $this->user->getDirty() : false;
        if ($changes && (checkArray('is_delete', $changes) == '1' || checkArray('active', $changes) == '0')) {
               
            $this->user->managerOfDepartments()->detach();
            $this->user->teamLead()->update(['team_lead'=>null]);
            $this->user->orgManager()->update(['role'=>'members']);
        }
    }

    public function handleOrganization($userId)
    {

        $organizations = User_org::where('user_id', $userId)->get();

        foreach ($organizations as $organization) {

            $organization->delete();
        }

        $orgDepts = OrganizationDepartment::where('org_dept_manager', $userId)->get();

        foreach ($orgDepts as $orgDept) {

            $orgDept->org_dept_manager = null;
            $orgDept->save();
        }

    }

    public function handleTickets($userId)
    {

        $tickets = Tickets::where('assigned_to', $userId)->get();

        foreach ($tickets as $ticket) {

            $ticket->assigned_to = NULL;
            $ticket->save();
        }
    }
    public function handleDepartment($userId)
    {

        $deptAgents = DepartmentAssignAgents::where('agent_id', $userId)->get();

        foreach ($deptAgents as $deptAgent) {

            $deptAgent->delete();
        }

    }

    public function handleTeam($userId)
    {

        $teamAgents = Assign_team_agent::where('agent_id', $userId)->get();

        foreach ($teamAgents as $teamAgent) {

            $teamAgent->delete();
        }

        $teams = Teams::where('team_lead', '$userId')->get();
        foreach ($teams as $team) {

            $team->team_lead = null;
            $team->save();
        }

    }

    public function handleDepartmentManager($userId)
    {

        $deptManagers = DepartmentAssignManager::where('manager_id', $userId)->get();

        foreach ($deptManagers as $deptManager) {

            $deptManager->delete();
        }
    }

    /**
     * This method remove user dependency from other table
     * @param int $userId
     * @return boolean
     */
    private function removeUserId($userId)
    {
        DepartmentAssignManager::where('manager_id', $userId)->delete();

        Department::where('manager', $userId)->update(['manager' => null]);

        OrganizationDepartment::where('org_dept_manager', $userId)->update(['org_dept_manager' => null]);
    }
}
