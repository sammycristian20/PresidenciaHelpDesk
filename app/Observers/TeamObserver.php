<?php

namespace App\Observers;

use App\Model\helpdesk\Agent\Teams;

use App\Observers\ListenDependencyDeletion;
/**
 * Observer for handling events on Team model
 *
 *
 */
class TeamObserver extends ListenDependencyDeletion
{
    public function deleting(Teams $team)
    {
        //delete releated records from worklflow/listeners action model when team is deleted
        $this->deleteFromWorkflowOrListeners(['team', 'team_id'], $team->id);
    }

    public function updating(Teams $team)
    {
        //delete releated records from worklflow/listeners action model when team status is set as inactive
        if (array_key_exists('status', $team->getDirty()) && $team->getDirty()['status'] == 0) {
            $this->deleteFromWorkflowOrListeners(['team', 'team_id'], $team->id);
        }
    }
}
