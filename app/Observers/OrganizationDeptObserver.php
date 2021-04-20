<?php

namespace App\Observers;

use App\Model\helpdesk\Agent_panel\OrganizationDepartment;

use App\Observers\ListenDependencyDeletion;
/**
 * Observer for handling events on Department model
 *
 *
 */
class OrganizationDeptObserver extends ListenDependencyDeletion
{
	/**
     * Listen to the User deleting event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(OrganizationDepartment $orgDept)
    {
        $this->deleteFromWorkflowOrListeners(['org_dept'], $orgDept->id);
    }
}
