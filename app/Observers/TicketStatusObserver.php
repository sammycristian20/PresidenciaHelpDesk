<?php

namespace App\Observers;

use App\Model\helpdesk\Ticket\Ticket_Status as Status;

use App\Observers\ListenDependencyDeletion;
/**
 * Observer for handling events on Department model
 *
 *
 */
class TicketStatusObserver extends ListenDependencyDeletion
{
	/**
     * Listen to the User deleting event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(Status $status)
    {
        $this->deleteFromWorkflowOrListeners(['status', 'status_id'], $status->id);
    }
}
