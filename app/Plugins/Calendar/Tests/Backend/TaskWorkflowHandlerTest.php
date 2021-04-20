<?php

namespace App\Plugins\Calendar\Tests\Backend;

use App\Model\helpdesk\Ticket\Tickets;
use App\Model\MailJob\QueueService;
use App\Plugins\Calendar\Handler\TaskWorkFlowHandler;
use App\Plugins\Calendar\Jobs\TaskNotificationJob;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskTemplate;
use App\Plugins\Calendar\Model\TemplateTask;
use Tests\AddOnTestCase;
use Queue;

class TaskWorkflowHandlerTest extends AddOnTestCase
{
    /**
     * @var TaskWorkFlowHandler
     */
    private $handler;

    public function setUp() :void
    {
        parent::setUp();

        $this->handler = new TaskWorkFlowHandler();

        QueueService::where('status', 1)->update(['status' => 0]);
        QueueService::where('short_name', 'sync')->update(['status' => 1]);
        Queue::fake();
    }

    public function test_handleMethod_createsTasks_basedOnPassedTemplateAndTicket()
    {
        $this->getLoggedInUserForWeb('admin');

        $templateId = factory(TaskTemplate::class)->create()->id;

        $templateTasks = factory(TemplateTask::class,5)->create(['template_id' => $templateId]);

        $tasksArray = $templateTasks->toArray();

        $ticketId = factory(Tickets::class)->create()->id;

        $this->handler->handle($templateId, ['ticket_id' => $ticketId]);

        $this->assertDatabaseHas('tasks', ['task_name' => reset($tasksArray)['name'], "ticket_id" => $ticketId]);
        $this->assertDatabaseHas('tasks', ['task_name' => end($tasksArray)['name'], "ticket_id" => $ticketId]);

        Queue::assertPushed(TaskNotificationJob::class);
    }

    public function test_assignAgentsMethod_assignesAgents_toTask_successfully()
    {
        $task = factory(Task::class)->create();

        $this->getPrivateMethod($this->handler, 'assignAgents', [$task, [1]]);

        $this->assertDatabaseHas('task_assignees', ['task_id' => $task->id,'user_id' => 1]);

        Queue::assertPushed(TaskNotificationJob::class);
    }

    public function test_getTaskTemplateEntityValue_returnTaskTemplateDetails_inRequiredArrayFormat()
    {
        $template = factory(TaskTemplate::class)->create();

        $requiredTaskTemplateEntityValue = $this->getPrivateMethod($this->handler, 'getTaskTemplateEntityValue', [$template->id]);

        $this->assertEquals(['id' => $template->id,'name' => $template->name], $requiredTaskTemplateEntityValue);
    }
}
