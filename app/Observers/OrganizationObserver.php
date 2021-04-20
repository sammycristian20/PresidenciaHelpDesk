<?php

namespace App\Observers;

use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Observers\ListenDependencyDeletion;
use Lang;

/**
 * Observer for handling events on Department model
 *
 *
 */
class OrganizationObserver extends ListenDependencyDeletion
{
    /**
     * Listen to the User deleting event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(Organization $organization)
    {

        $orgBelongsToSla = Sla_plan::whereRaw('FIND_IN_SET(?,apply_sla_company)', [$organization->id])->pluck('id')->toArray();
        if ($orgBelongsToSla) {
            throw new \Exception(Lang::get('lang.you_cannot_delete_this_organization,organization_associated_sla_plan'));

        }

        $this->deleteFromWorkflowOrListeners(['company_name'], $organization->id);

        $this->handleOrganizationDepartment($organization->id);
        $this->handleUserOrganization($organization->id);
    }
    /*
     *Delete User Organization
     */
    public function handleUserOrganization($orgId)
    {

        $userOrganizations = User_org::where('org_id', $orgId)->get();
        foreach ($userOrganizations as $userOrganization) {

            $userOrganization->delete();
        }
    }
    /*
     *Delete  Organization department
     */
    public function handleOrganizationDepartment($orgId)
    {

        $organizationDepartments = OrganizationDepartment::where('org_id', $orgId)->get();
        foreach ($organizationDepartments as $organizationDepartment) {

            $organizationDepartment->delete();
        }
    }
}
