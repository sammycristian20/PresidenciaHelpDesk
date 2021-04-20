<?php

namespace App\Observers;

use App\Model\helpdesk\Ticket\Ticket_source as Source;

use App\Observers\ListenDependencyDeletion;
/**
 * Observer for handling events on Department model
 *
 *
 */
class TicketSourceObserver extends ListenDependencyDeletion
{
	/**
     * Listen to the User deleting event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(Source $source)
    {
        $this->deleteFromWorkflowOrListeners(['source', 'source_id'], $source->id);
    }
}
