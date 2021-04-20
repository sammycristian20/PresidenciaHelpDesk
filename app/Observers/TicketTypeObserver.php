<?php

namespace App\Observers;

use App\Model\helpdesk\Manage\Tickettype as Type;

use App\Observers\ListenDependencyDeletion;
/**
 * Observer for handling events on Department model
 *
 *
 */
class TicketTypeObserver extends ListenDependencyDeletion
{
	/**
     * Listen to the User deleting event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(Type $type)
    {
        $this->deleteFromWorkflowOrListeners(['type', 'type_id'], $type->id);
    }
}
