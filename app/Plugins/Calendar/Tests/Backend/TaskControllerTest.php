<?php

namespace App\Plugins\Calendar\Tests\Backend;

use App\Model\helpdesk\Ticket\Tickets;
use App\Model\MailJob\QueueService;
use App\Plugins\Calendar\Jobs\TaskNotificationJob;
use App\Plugins\Calendar\Model\TaskAssignees;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Queue;
use Tests\AddOnTestCase;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\Project;
use App\Plugins\Calendar\Model\TaskCategory;

class TaskControllerTest extends AddOnTestCase
{
    public function setUp() :void
    {
        parent::setUp();

        $this->getLoggedInUserForWeb('agent');
    }

    public function test_IndexMethod_ReturnsAllTasks_With_All_Details()
    {
        $taskCategory = factory(TaskCategory::class)->create();

        $task = factory(Task::class)->create(['task_category_id'=>$taskCategory->id]);

        $this->call('GET', '/tasks/task')
            ->assertOk()
            ->assertJsonFragment(['task_name' => $task->name]);
    }

    public function test_ReturnTasksMethod_returnsFilteredTasksBasedOnValidProjects()
    {
        $taskCategory = factory(TaskCategory::class)->create();

        $taskCount = rand(1, 10);

        factory(Task::class, $taskCount)->create(['task_category_id' => $taskCategory->id]);

        $this->call('GET', 'tasks/api/get-all-tasks', ['projects' => [$taskCategory->project_id]])
            ->assertOk()
            ->assertJsonFragment(['total' => $taskCount]);
    }

    public function test_ReturnTasksMethod_returnsNoTasksOnInValidProjects()
    {
        factory(Task::class)->create();

        $this->call('GET', 'tasks/api/get-all-tasks', ['projects' => array(10001)])
            ->assertOk()
            ->assertJsonFragment(['total' => 0]);
    }

    public function test_ReturnTasksMethod_returnsNoTasksBasedOnInValidTickets()
    {
        factory(Task::class)->create();

        $this->call('GET', 'tasks/api/get-all-tasks', ['ticket_ids' => array(1876)])
            ->assertOk()
            ->assertJsonFragment(['total' => 0]);
    }


    public function test_ReturnTaskMethod_returnsNumberOfTasks_SpecifiedInLimit()
    {
        factory(Task::class, 5)->create();

        $this->call('GET', 'tasks/api/get-all-tasks', ['limit' => 3])
            ->assertOk()
            ->assertJsonFragment(['per_page'=>3]);
    }

    public function test_ReturnTasksMethod_returnsFilteredTasksBasedOnValidAssignees()
    {
        $assignees = factory(TaskAssignees::class)->create(['user_id' => \Auth::user()->id]);

        $this->call('GET', 'tasks/api/get-all-tasks', ['assigned_to' => [$assignees->user_id]])
            ->assertOk()->assertJsonFragment(['total' => 1]);
    }
    
    public function test_DestroyMethod_Fails_WhenTriedToDelete_ByTheUserWhoHasNotCreatedIt()
    {
        $taskId = factory(Task::class)->create(['created_by' => 1000])->id;
        $this->call('DELETE', "tasks/task/$taskId")->assertStatus(400);
    }

    public function test_createMethodCreatesTheTaskAndPushesToQueueForNotifications_ForSuccess()
    {
        QueueService::where('status', 1)->update(['status' => 0]);
        QueueService::where('short_name', 'sync')->update(['status' => 1]);
        Queue::fake();
        $response = $this->post(url('/tasks/task'), [
            'task_start_date' => Carbon::now(),
            'task_end_date' => Carbon::now()->addDays(12),
            'task_name' => "xcdsd",
            'task_description' => "sda",
            'is_private' => 1,
            'status' => 'Open'
        ]);
        $response->assertOk()->assertJsonFragment(["message" => "Task Created Successfully."]);
        $this->assertDatabaseHas('tasks', ['task_name' => "xcdsd"]);
        Queue::assertPushed(TaskNotificationJob::class);
    }
}
