<?php

namespace App\Plugins\Calendar\Tests\Backend;

use Tests\AddOnTestCase;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\Project;
use App\Plugins\Calendar\Model\TaskCategory;

class ProjectControllerTest extends AddOnTestCase
{
    public function setUp() :void
    {
        parent::setUp();

        $this->getLoggedInUserForWeb('admin');
    }

    public function test_StoreMethod_ShouldCreateProject_ReturnSuccessResponse()
    {
        $data = [
            'name' => 'Project 1'
        ];
        $this->call('POST', 'tasks/api/project/create', $data)
            ->assertOk()
            ->assertExactJson(['success' => true,'message' => trans('Calendar::lang.project_created')]);

        $this->assertDatabaseHas('projects', $data);
    }

    public function test_IndexMethod_ReturnsAllProjects()
    {
        $project = factory(Project::class)->create();

        $this->call('GET', '/tasks/api/project/view')
            ->assertOk()
            ->assertJsonFragment(['name' => $project->name]);
    }

    public function test_EditMethod_UpdatesTheProject_AndReturnsSuccessResponse()
    {
        $project = factory(Project::class)->create();

        $updatePayload = [
            'name' => 'New Project 2'
        ];

        $this->call('PUT', '/tasks/api/project/edit/'.$project->id, $updatePayload)
            ->assertOk()
            ->assertExactJson(['success' => true,'message' => trans('Calendar::lang.project_updated')]);
        ;
        $this->assertDatabaseMissing('projects', ['id' => $project->name]);

        $this->assertDatabaseHas('projects', $updatePayload);
    }

    public function test_DestroyMethod_DeletesTheProjectWithAssociatedTaskLists_AndReturnsSuccessResponse()
    {
        $taskCategory = factory(TaskCategory::class)->create();

        $this->call('DELETE', '/tasks/api/project/delete/'.$taskCategory->project_id)->assertOk();

        $this->assertDatabaseMissing('projects', [
            'id' => $taskCategory->project_id
        ]);

        $this->assertDatabaseMissing('task_categories', [
            'name' => $taskCategory->name
        ]);
    }
}
