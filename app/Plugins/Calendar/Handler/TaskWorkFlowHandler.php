<?php

namespace App\Plugins\Calendar\Handler;

use App\Model\helpdesk\Ticket\TicketAction;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Calendar\Controllers\TaskController;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskAssignees;
use App\Plugins\Calendar\Model\TaskTemplate;
use App\Plugins\Calendar\Model\TemplateTask;
use App\Repositories\FormRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TaskWorkFlowHandler
{
    /**
     * @param $templateId
     * @param array $payload array of ticket details
     * @param $templateTasks
     */
    public function handle($templateId, array $payload, $templateTasks = null)
    {
        if (isset($payload['ticket_id'])) {
            if (!$templateTasks) {
                $templateTasks = TemplateTask::where('template_id', $templateId)->get();
            }

            foreach ($templateTasks as $templateTask) {
                $createdTask = Task::create([
                    'task_name' => $templateTask->name,
                    'ticket_id' => $payload['ticket_id'],
                    'task_start_date' => Carbon::now(),
                    'task_end_date' => $this->calculateDueDateTimeValue($templateTask->end_unit, $templateTask->end),
                    'created_by' => \Auth::user()->id,
                    'is_private' => 0,
                    'task_template_id' => $templateId,
                    'task_category_id' => $templateTask->category
                ]);

                $assignedAgents = $templateTask->assignees;

                if (isset($payload['assigned_to'])) {
                    $ticketAssignee = $payload['assigned_to'];
                } else {
                    $ticketAssignee = Tickets::where('id', $payload['ticket_id'])->value('assigned_to');
                }
                
                if ($ticketAssignee && $templateTask->assign_task_to_ticket_agent) {
                    array_push($assignedAgents, $ticketAssignee);
                }

                $this->assignAgents($createdTask, array_unique($assignedAgents));
            }
        }
    }

    private function assignAgents(Task $task, $assignees = [])
    {
        $notificationController = new TaskController();

        $notificationController->dispatcher($task, 'task-created');

        $dataForBatchInserting = [];

        foreach ($assignees as $newAgent) {
            $dataForBatchInserting[] = new TaskAssignees(['user_id' => $newAgent]);
        }

        $task->assignedTo()->saveMany($dataForBatchInserting);

        $notificationController->dispatcher($task, 'task-assigned');
    }

    /**
     * @param String $unit Unit i.e (day|month|hour|year)
     * @param String $value actual value
     * @return Carbon
     */
    private function calculateDueDateTimeValue($unit, $value)
    {
        $now = Carbon::now();

        switch ($unit) {
            case 'minute':
                $taskEndDate = $now->addMinutes($value);
                break;
            case 'hour':
                $taskEndDate = $now->addHours($value);
                break;
            case 'month':
                $taskEndDate = $now->addMonths($value);
                break;
            case 'day':
                $taskEndDate = $now->addDays($value);
                break;
            default:
                $taskEndDate = $now->addDay();
        }

        return $taskEndDate;
    }

    public function setTaskListEntityValueForWorkflow($field, &$dependencyValue)
    {
        if ($field === 'task_list_id') {
            $dependencyValue = (object) $this->getTaskTemplateEntityValue($dependencyValue);
        }
    }

    /**
     * Adds task form to
     * @param $formFields
     */
    public function addTaskForm(Collection &$formFields)
    {
        $formRepository = FormRepository::getInstance();

        if ($formRepository->getCategory() == 'workflow' && $formRepository->getScenario() == 'actions') {
            $formFields->add((object)['id'=> uniqid(), 'label'=> 'Task Template', 'type'=>'api', 'unique'=>'task_list_id', 'api_info'=> 'url:=tasks/api/template/dropdown', 'default'=> 1]);
        }
    }

    /**
     * Gets the task template details
     * @param $templateId
     * @return array
     */
    private function getTaskTemplateEntityValue($templateId)
    {
        $template = TaskTemplate::find($templateId);

        return ($template) ? ['id' => $templateId,'name' => $template->name] : [];
    }
}
