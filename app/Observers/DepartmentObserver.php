<?php

namespace App\Observers;

use App\Model\helpdesk\Agent\Department;

use App\Observers\ListenDependencyDeletion;
/**
 * Observer for handling events on Department model
 *
 *
 */
class DepartmentObserver extends ListenDependencyDeletion
{
	/**
     * Listen to the User deleting event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(Department $department)
    {
        $this->deleteFromWorkflowOrListeners(['department', 'dept_id'], $department->id);
    }
}
