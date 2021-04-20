<?php
/**
 * The objective of this file is to provide some common helper functions which are shared by
 * multiple testclasses. This will allow us to use those function without defining in multiple
 * test classes and help us to *refactor our tests clases.
 * Reference link 1: https://vegibit.com/laravel-testing-helpers/
 * Reference link 2: https://laracasts.com/series/lets-build-a-forum-with-laravel
 */

/**
 * Function to create rules in workflow/listeners parameter
 * @param   string   $for         denotes to create a rule for workflow or listeners 
 * @param   integer  $rulesCount  number of rules to create
 * @var     $rules, $key, $condition, $value , $custom_rule
 * @return  array   $rules
 */
function generateRules($for, $rulesCount, $ruleNumber = '', $emptyKeyName = '')
{	
	$rules = [];
	$key = ($for == 'listeners') ? 'key' : 'matching_scenario';
	$condition = ($for == 'listeners') ? 'condition' : 'matching_relation';
	$value = ($for == 'listeners') ? 'value': 'matching_value';
	$custom_rule = 'custom_rule';
	for ($i = 0; $i < $rulesCount; $i++) {
		$rules[$i][$key] = (($ruleNumber == $i) && ($emptyKeyName == $key)) ? '' : str_random(10);
		$rules[$i][$condition] = (($ruleNumber == $i) && ($emptyKeyName == $condition)) ? '' : 'equals';
		$rules[$i][$value] = (($ruleNumber == $i) && ($emptyKeyName == $value)) ? '' : str_random(10);
		$rules[$i][$custom_rule] = [];
 	}

 	return $rules;
}

/**
 * Function to create actions in workflow/listeners parameter
 * @param   string   $for         denotes to create a actions for workflow or listeners 
 * @param   integer  $rulesCount  number of actions to create
 * @var     $rules, $key, $condition, $value , $custom_action
 * @return  array   $rules
 */
function generateActions($for, $actionsCount, $actionNumber = '', $emptyKeyName = '')
{
	$actions = [];
	$key = ($for == 'listeners') ? 'key' : 'condition';
	$value = ($for == 'listeners') ? 'value': 'action';
	$custom_action = 'custom_action';
	for ($i = 0; $i < $actionsCount; $i++) {
		$actions[$i][$key] =  (($actionNumber == $i) && ($emptyKeyName == $key)) ? '' : str_random(10);
		$actions[$i][$value] = (($actionNumber == $i) && ($emptyKeyName == $value)) ? '' : str_random(10);
		$actions[$i][$custom_action] = []; 
 	}

 	return $actions;
}

/**
 * Function to generate events in listeners parameter
 * @param   integer  $eventsCount  number of events to create
 * @return  array    $events
 */
function generateEvents($eventsCount, $eventNumber = '', $emptyKeyName = '')
{
	$events = [];
	for ($i = 0; $i < $eventsCount; $i++) {
		$events[$i]['condition'] = (($eventNumber == $i) && ($emptyKeyName == 'condition')) ? '' : str_random(10); 
		$events[$i]['event'] = (($eventNumber == $i) && ($emptyKeyName == 'event')) ? '' : str_random(10);
		$events[$i]['new'] = (($eventNumber == $i) && ($emptyKeyName == 'new')) ? '' : str_random(10);
		$events[$i]['old'] = (($eventNumber == $i) && ($emptyKeyName == 'old')) ? '' : str_random(10);
	}
	return $events;
}

/** 
 * To create records which persist in database using factories
 * @return array     array containg sub arrays of listeners and related table data
 */
function createAndGetArray($class, $attributes = [], $times = null)
{
	return factory($class, $times)->create($attributes)->toArray();
}

/** 
 * To create instances of recrords which do not persist in databaseusing factories
 * @return array     array containg sub arrays of listeners and related table data
 */
function makeAndGetArray($class, $attributes = [], $times = null)
{
	return factory($class, $times)->make($attributes)->toArray();
}

/**
 * Function to generate parameters for workflow create and edit request
 * @param  integer   $rulesCout  	number of rules in a workflow
 * @param  integer   $actionsCount   number of actions in a workflow
 * @return array     $param
 */
function generateWorkflowParameters($rulesCount = 1, $actionsCount = 1, $values = [])
{
 	return array_merge([
 		'workflow' => [
 			'name' => str_random(10),
 			'target' => 'any'
 		],
 		'rules'    => generateRules('wrokflow', $rulesCount),
 		'actions'  => generateActions('wrokflow', $actionsCount)
 	], $values);
}

/**
 * Function to generate parameters for workflow create and edit request
 * @param  integer   $rulesCout  	number of rules in a listeners
 * @param  integer   $actionsCount  number of actions in a listeners
 * @param  integer   $eventsCount   number of events in a listens
 * @return array     $param
 */
function generateListenerParameters($rulesCount = 1, $actionsCount = 1, $eventsCount = 1, $value = [])
{
 	return array_merge([
 		'listeners' => [
 			'name' => str_random(10),
 			'status' => 1,
 			'rule_match' => 'any'
 		],
 		'events' => generateEvents($eventsCount),
 		'rules' => generateRules('listeners', $rulesCount),
 		'actions' => generateActions('listeners', $actionsCount)
 	],$value);
}
