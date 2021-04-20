<?php

namespace App\Observers;

use App\Model\helpdesk\Ticket\Ticket_Priority as Priority;

use App\Observers\ListenDependencyDeletion;
/**
 * Observer for handling events on Department model
 *
 *
 */
class PriorityObserver extends ListenDependencyDeletion
{
	/**
     * Listen to the User deleting event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(Priority $priority)
    {
        $this->deleteFromWorkflowOrListeners(['priority', 'priority_id'], $priority->priority_id);
    }
}
