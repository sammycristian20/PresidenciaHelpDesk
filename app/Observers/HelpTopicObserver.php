<?php

namespace App\Observers;

use App\Model\helpdesk\Manage\Help_topic as HelpTopic ;

use App\Observers\ListenDependencyDeletion;
/**
 * Observer for handling events on Department model
 *
 *
 */
class HelpTopicObserver extends ListenDependencyDeletion
{
	/**
     * Listen to the User deleting event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(HelpTopic $helpTopic)
    {
        $this->deleteFromWorkflowOrListeners(['helptopic', 'help_topic', 'help_topic_id'], $helpTopic->id);
    }
}
