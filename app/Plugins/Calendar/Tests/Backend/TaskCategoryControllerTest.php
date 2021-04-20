<?php

namespace App\Plugins\Calendar\Tests\Backend;

use App\Plugins\Calendar\Model\Project;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskCategory;
use Tests\AddOnTestCase;

class TaskCategoryControllerTest extends AddOnTestCase
{
    public function setUp() :void
    {
        parent::setUp();

        $this->getLoggedInUserForWeb('admin');
    }

    public function test_Create_AddsTaskList_Returns_SuccessResponse()
    {
        $projectId = factory(Project::class)->create()->id;

        $createPayload = array(
            "name" => 'TaskList default',
            'project_id' => $projectId
        );
        $response = $this->call('POST', '/tasks/api/category/create', $createPayload);
        $response->assertOk();
        $this->assertDatabaseHas('task_categories', $createPayload);
    }


    public function test_EditMethod_UpdatesTheTaskList_AndReturnsSuccessResponse()
    {
        $taskCategory = factory(TaskCategory::class)->create();

        $updatePayload = [
            'name'       => 'New TaskList 2',
            'project_id' => $taskCategory->project_id
        ];

        $this->call('PUT', '/tasks/api/category/edit/'.$taskCategory->id, $updatePayload)
            ->assertOk()
            ->assertExactJson(['success' => true,'message' => trans('Calendar::lang.tasklist_updated')]);
        ;
        $this->assertDatabaseMissing('projects', ['id' => $taskCategory->name]);

        $this->assertDatabaseHas('task_categories', ['name' => 'New TaskList 2']);
    }

    public function test_IndexMethod_ReturnsAllTaskLists()
    {
        $this->getLoggedInUserForWeb('agent');

        $taskCategory = factory(TaskCategory::class)->create();
        $this->call('GET', '/tasks/api/category/view')
            ->assertOk()
            ->assertJsonFragment(['name' => $taskCategory->name]);
    }

    public function test_DestroyMethod_DeletesTheTaskCategory_AndReturnsSuccessResponse()
    {
        $taskCategory = factory(TaskCategory::class)->create();

        $tasks = factory(Task::class)->create(['task_category_id' => $taskCategory->id]);

        $this->call('DELETE', '/tasks/api/category/delete/'.$taskCategory->id)->assertOk();

        $this->assertDatabaseMissing('task_categories', [
            'id' => $taskCategory->id
        ]);

        $this->assertDatabaseMissing('tasks', [
            'task_name' => $tasks->task_name
        ]);
    }
}
