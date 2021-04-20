<?php

namespace App\Plugins\Calendar\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Calendar\Activity\Models\Activity;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskCategory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskActivityController extends Controller
{
    private $lookupTable;

    public function __construct()
    {
        //setting activity lookup table, to avoid nested conditional statements
        $this->lookupTable = [
            'task_name' => [
                'created' => trans('Calendar::lang.task-plugin-activity-task-name-created'),
                'updated' => trans('Calendar::lang.task-plugin-activity-task-name-updated'),
                'valueType' => 'name'
            ],
            'task_start_date' => [
                'created' => trans('Calendar::lang.task-plugin-activity-task-start-created'),
                'updated' => trans('Calendar::lang.task-plugin-activity-task-start-updated'),
                'valueType' => 'date'
            ],
            'task_end_date' => [
                'created' => trans('Calendar::lang.task-plugin-activity-task-end-created'),
                'updated' => trans('Calendar::lang.task-plugin-activity-task-end-updated'),
                'valueType' => 'date'
            ],
            'status' => [
                'created' => trans('Calendar::lang.task-plugin-activity-task-status-created'),
                'updated' => trans('Calendar::lang.task-plugin-activity-task-status-updated'),
                'valueType' => 'status'
            ],
            'is_private' => [
                'created' => trans('Calendar::lang.task-plugin-activity-task-type-created'),
                'updated' => trans('Calendar::lang.task-plugin-activity-task-type-updated'),
                'valueType' => 'task_type'
            ],
            'ticket_id' => [
                'created' => trans('Calendar::lang.task-plugin-activity-task-ticket-created'),
                'updated' => trans('Calendar::lang.task-plugin-activity-task-ticket-updated'),
                'deleted' => trans('Calendar::lang.task-plugin-activity-task-ticket-deleted'),
                'valueType' => 'ticket'
            ],
            'user_id' => [
                'created' => trans('Calendar::lang.task-plugin-activity-task-agent-created'),
                'deleted' => trans('Calendar::lang.task-plugin-activity-task-agent-deleted'),
                'valueType' => 'agent'
            ],
            'task_category_id' => [
                'created' => trans('Calendar::lang.task-plugin-activity-task-list-created'),
                'updated' => trans('Calendar::lang.task-plugin-activity-task-list-updated'),
                'deleted' => trans('Calendar::lang.task-plugin-activity-task-list-deleted'),
                'valueType' => 'category'
            ]
        ];
    }

    public function show($id, Request $request)
    {
        $task = Task::find($id);

        if (!$task) {
            return errorResponse(trans('Calendar::task-plugin-no-activity'));
        }

        $activities = Activity::forSubject($task)
            ->orWhere(function($query) use ($task) {
                $query->where('subject_type', 'App\Plugins\Calendar\Model\TaskAssignees')
                      ->where('properties->attributes->task_id', $task->id);
            })
            ->orderBy($request->sort_field ?: 'id', $request->sort_order ?: 'desc')
            ->paginate($request->limit ?: 10);

        $activities->getCollection()->transform(function ($activity) {
            return [
                'message' => $this->getReadableLogMessages($activity),
                'creator' => $activity->causer_type::where('id', $activity->causer_id)
                    ->first(['id','first_name','last_name','user_name','email','profile_pic']),
                'created_at' => $activity->created_at
            ];
        });

        return successResponse('', $activities);
    }

    private function getReadableLogMessages($activity)
    {
        $type = ($activity->subject_type === 'App\Plugins\Calendar\Model\TaskAssignees')
                ? 'dependent-activity'
                : $activity->description;

        $activityTypeActionLookupTable = [
            'created' => 'logNewlyCreatedTask',
            'updated' => 'logUpdatedTask',
            'dependent-activity' => 'logAssignedAgents',
        ];

        return call_user_func([$this,$activityTypeActionLookupTable[$type]], $activity);

    }

    /**
     * Formats different types of data seamlessly in required format
     * @param $valueType
     * @param $value
     * @return string
     */
    private function formatValues($valueType, $value)
    {
        switch ($valueType) {
            case 'date':
                return Carbon::parse($value)
                    ->setTimeZone(agentTimeZone(\Auth::user()))->format( System::value('date_time_format'));

            case 'task_type':
                return ($value) ? 'Private' : 'Public';

            case 'ticket':
                return $this->getTicketDetailsLinkForReplacing($value);

            case 'agent':
                return $this->getUserDetailsLinkForReplacing($value);

            case 'category':
                return $this->getTaskCategoryInfoForReplacing($value);

            default:
                return $value;
        }
    }

    /**
     * This method is called when model is created
     * @param $activity
     * @return string
     */
    private function logNewlyCreatedTask($activity)
    {
        $message = [];

        $newAttributes = ($activity->properties->toArray()) ? $activity->properties->toArray()['attributes'] : [];

        foreach ($newAttributes as $key => $value) {
            if (!$value) {
                continue;
            }

            $lookUpEntry = $this->lookupTable[$key];

            $message[] = $this->replaceValues(
                $lookUpEntry['created'],
                $this->formatValues($lookUpEntry['valueType'], $value)
            );
        }

        return implode('<br>', $message);
    }

    /**
     * This method gets called when the model is updated
     * @param $activity
     * @return string
     */
    private function logUpdatedTask($activity)
    {
        $message = [];

        $attributes = $activity->properties->toArray();

        $updatedAttributes = (isset($attributes['attributes'])) ? $attributes['attributes'] : [];

        $oldAttributes = isset($attributes['old']) ?  $attributes['old'] : [];

        $mergedAttributes = array_merge_recursive($updatedAttributes, $oldAttributes);

        foreach ($mergedAttributes as $key => $value) {

            if (!$value) {
               continue;
            }

            $lookUpEntry = $this->lookupTable[$key];

            $oldValue = end($value);

            $newValue = reset($value);
            
            $message[] = $this->replaceValues(
                $lookUpEntry[$this->getProperUpdateAction($oldValue, $newValue)],
                $this->formatValues($lookUpEntry['valueType'], $newValue),
                $this->formatValues($lookUpEntry['valueType'], $oldValue)
            );
        }

        return implode('<br>', $message);
    }

    /**
     * This method determines the proper activity log action when updating the model
     * this method
     * @param $oldValue
     * @param $newValue
     * @return string
     */
    private function getProperUpdateAction($oldValue, $newValue)
    {
        if (is_null($oldValue) && !is_null($newValue)) {
            return "created";
        }

        if (is_null($newValue) && !is_null($oldValue)) {
            return "deleted";
        }

        if ( !is_null($newValue) && !is_null($oldValue)) {
            return "updated";
        }
    }

    /**
     * This method gets called for logging assigned agents
     * @param $activity
     * @return string
     */
    private function logAssignedAgents($activity)
    {
        $attributes = ($activity->properties->toArray()) ? $activity->properties->toArray()['attributes'] : [];

        $message = $this->replaceValues(
            $this->lookupTable['user_id'][$activity->description],
            $this->formatValues($this->lookupTable['user_id']['valueType'], $attributes['user_id'])
        );

        return ($message) ?: '';
    }

    /**
     * Replaces stubs with actual values
     * @param $literal
     * @param $value
     * @param string $oldValue
     * @return string|string[]
     */
    private function replaceValues($literal, $value, $oldValue = '')
    {
        return str_replace([':value',':oldvalue'], [$value,$oldValue], $literal);
    }

    /**
     * Generates user link for assigned agents
     * @param $userId
     * @return string
     */
    private function getUserDetailsLinkForReplacing($userId)
    {
        $user = User::where('id', $userId)->first(['id','first_name','last_name','user_name']);

        return ($user) ? '<a href="'. url("/user/$userId") .'" target="_blank">'.$user->full_name.'</a>' : '';
    }

    /**
     * Generates ticket link for assigned tickets
     * @param $ticketId
     * @return string
     */
    private function getTicketDetailsLinkForReplacing($ticketId)
    {
        $ticket = Tickets::where('id', $ticketId)->first('ticket_number');

        return ($ticket) ? '<a href="'. url("/thread/$ticketId") .'" target="_blank">'.$ticket->ticket_number.'</a>' : '';
    }

    /**
     * Return the category to which the task belongs to.
     * @param $categoryId
     * @return string
     */
    private function getTaskCategoryInfoForReplacing($categoryId)
    {
        $category = TaskCategory::where('id', $categoryId)->first(['name']);

        return $category ? $category->name : '';

    }
}
