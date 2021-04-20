<?php

namespace App\Observers;

use App\Model\Listener\ListenerAction;
use App\Model\Listener\ListenerRule;
use App\Model\Listener\ListenerEvent;
use App\Model\helpdesk\Workflow\WorkflowAction;
use App\Model\helpdesk\Workflow\WorkflowRules;

/**
 * Class to handle deletion of dependent workflow and listeners for deleting helptopic,
 * department, priority, source, status, type, organization and organization departments
 *
 */
class ListenDependencyDeletion
{
	
	/**
	 * function which can be called by derived observer classes to delete dependent data in listener and workflow
	 * @param   array   $keyNames    string values to search entity name in entity column of tables
	 * @param   Mixed   $value       value of the entity to be deleted from database
	 *
	 */
	public function deleteFromWorkflowOrListeners($keyNames, $value)
	{
		$this->deleteListenerRules($keyNames, $value);
		$this->deleteListenerActions($keyNames, $value);
		$this->deleteListenerEvents($keyNames, $value);
		$this->deleteWorkflowActions($keyNames, $value);
		$this->deleteWorkflowRules($keyNames, $value);
	}

	/**
	 * function To delete records which contains entity and entity value in listener rules table
	 * @param   array   $keyNames    string values to search entity name in entity column of tables
	 * @param   Mixed   $value       value of the entity to be deleted from database
	 *
	 */
	protected function deleteListenerRules($keyNames, $value)
	{
		$rules = new ListenerRule();
		return $this->deleteModerRecords($rules, $keyNames, $value, ['key', 'value']);
	}

	/**
     * function To delete records which contains entity and entity value in listener actions table
	 * @param   array   $keyNames    string values to search entity name in entity column of tables
	 * @param   Mixed   $value       value of the entity to be deleted from database
	 *
	 */
	protected function deleteListenerActions($keyNames, $value)
	{
		$actions = new ListenerAction();
		return $this->deleteModerRecords($actions, $keyNames, $value, ['key', 'value']);
	}

	/**
     * function To delete records which contains entity and entity value in listener events table
	 * @param   array   $keyNames    string values to search entity name in entity column of tables
	 * @param   Mixed   $value       value of the entity to be deleted from database
	 *
	 */
	protected function deleteListenerEvents($keyNames, $value)
	{
		$events = new ListenerEvent();
		$this->deleteModerRecords($events, $keyNames, $value, ['event', 'old']);
		return $this->deleteModerRecords($events, $keyNames, $value, ['event', 'new']);
	}

	/**
     * function To delete records which contains entity and entity value in workflow rules table
	 * @param   array   $keyNames    string values to search entity name in entity column of tables
	 * @param   Mixed   $value       value of the entity to be deleted from database
	 *
	 */
	protected function deleteWorkflowRules($keyNames, $value)
	{
		$rules = new WorkflowRules();
		return $this->deleteModerRecords($rules, $keyNames, $value, ['matching_scenario', 'matching_value']);
	}

	/**
     * function To delete records which contains entity and entity value in workflow actions table
	 * @param   array   $keyNames    string values to search entity name in entity column of tables
	 * @param   Mixed   $value       value of the entity to be deleted from database
	 *
	 */
	protected function deleteWorkflowActions($keyNames, $value)
	{
		$actions = new WorkflowAction();
		return $this->deleteModerRecords($actions, $keyNames, $value, ['condition', 'action']);
	}

	/**
     * function To delete records from given modal after checking entity and entity value
	 * @param   array   $keyNames    string values to search entity name in entity column of tables
	 * @param   Mixed   $value       value of the entity to be deleted from database
	 * @param   array   $keys        array consisting two elements which are names of columns to search
	 * entity and entity value respctively
	 *
	 */
	protected function deleteModerRecords($model, $keyNames, $value, $keys)
	{	
		$test = $model->where(function($query) use ($keyNames, $keys){
			foreach ($keyNames as $keyName) {
				$query->orWhere($keys[0], '=', $keyName);
			}
		})->where($keys[1], '=', $value)->delete();
	}
}