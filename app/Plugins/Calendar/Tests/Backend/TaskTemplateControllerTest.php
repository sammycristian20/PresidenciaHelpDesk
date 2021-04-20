<?php

namespace App\Plugins\Calendar\Tests\Backend;

use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskCategory;
use App\Plugins\Calendar\Model\TaskTemplate;
use App\Plugins\Calendar\Model\TemplateTask;
use Tests\AddOnTestCase;

class TaskTemplateControllerTest extends AddOnTestCase
{
    private $insertData;

    public function setUp() :void
    {
        parent::setUp();

        $this->insertData = [
            'name' => str_random(),
            'description' => str_random(10),
            'category_id' => factory(TaskCategory::class)->create()->id,
            'task_templates' => [
                [
                    'taskEnd' => 1,
                    'taskEndUnit' => 'minute',
                    'order' => '1',
                    'taskName' => str_random(8),
                    'assignees' => [],
                    'assignTaskToTicketAssignee' => false
                ]
            ]
        ];
    }

    public function test_SettingsMethod_RedirectsToProperView()
    {
        $this->getLoggedInUserForWeb('admin');
        $this->get(route('tasks.template.settings'))
            ->assertOk()
            ->assertViewIs('Calendar::task-template-settings');
    }

    public function test_NonAdmins_ShouldNotAccess_TaskTemplateSettingsView()
    {
        $this->getLoggedInUserForWeb();
        $this->get(route('tasks.template.settings'))->assertStatus(302);
    }

    public function test_CreateMethodShould_ReturnToCreateTemplateView()
    {
        $this->getLoggedInUserForWeb('admin');
        $this->get(route('tasks.template.create'))
            ->assertOk()
            ->assertViewIs('Calendar::task-template-create-edit');
    }

    public function test_NonAdminUsers_CannotView_CreateTemplatePage()
    {
        $this->getLoggedInUserForWeb();
        $this->get(route('tasks.template.create'))->assertStatus(302);
    }

    public function test_indexMethod_returnsJsonOf_allTaskTemplates()
    {
        $this->getLoggedInUserForWeb('admin');

        $templateTask = factory(TemplateTask::class)->create();

        $this->get(route('tasks.template.index'))
            ->assertOk()
            ->assertJsonFragment([
                "template_tasks" => [["template_id" => $templateTask->template_id,"id" => $templateTask->id,"name" => $templateTask->name]]
            ]);
    }
    
    public function test_indexMethod_failsForNonAdmins()
    {
        $this->getLoggedInUserForWeb();

        $this->get(route('tasks.template.index'))
            ->assertStatus(302);
    }

    public function test_editMethod_redirectsToEditForm_withTemplateData()
    {
        $this->getLoggedInUserForWeb('admin');

        $templateTask = factory(TemplateTask::class)->create();

        $response = $this->get(route('tasks.template.edit', $templateTask->template_id));

        $response->assertViewIs('Calendar::task-template-create-edit');

        $response->assertViewHas('template');

        $responseOriginalData = $response->getOriginalContent()->getData()['template'];

        $this->assertEquals($templateTask->template_id, $responseOriginalData['id']);

        $this->assertEquals($templateTask->name, reset($responseOriginalData['template_tasks'])['name']);
    }

    public function test_editMethod_isNotAccessible_byNonAdmins()
    {
        $this->getLoggedInUserForWeb();

        $this->get(route('tasks.template.edit', 1000))->assertStatus(302);
    }

    public function test_editMethod_failsWhen_invalidTemplateIdSupplied()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->get(route('tasks.template.edit', 1000))
            ->assertStatus(302);
    }

    public function test_destroyMethod_destroysTheTemplate()
    {
        $this->getLoggedInUserForWeb('admin');

        $templateTask = factory(TemplateTask::class)->create();

        //assert value exists before deleting
        $this->assertDatabaseHas('task_templates', ['id' => $templateTask->template_id]);
        $this->assertDatabaseHas('template_tasks', ['name' => $templateTask->name,'id' => $templateTask->id]);

        $this->delete(route('tasks.template.delete', $templateTask->template_id))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('Calendar::lang.task-plugin-template-deleted')]);

        //assert value does not exists after deleting
        $this->assertDatabaseMissing('task_templates', ['id' => $templateTask->template_id]);
        $this->assertDatabaseMissing('template_tasks', ['name' => $templateTask->name,'id' => $templateTask->id]);
    }

    public function test_destroyMethod_fails_whenInvalidTemplate_isSupplied()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->delete(route('tasks.template.delete', 1000000))
            ->assertStatus(400)
            ->assertJsonFragment(['message' => trans('Calendar::lang.task-plugin-deleting-template-which-does-not-exist')]);
    }

    public function test_storeMethod_storesTaskTemplates_successfully()
    {
        $this->getLoggedInUserForWeb('admin');

        //assert data does not exist before creating
        $this->assertDatabaseMissing('task_templates', ['name' => $this->insertData['name']]);
        $this->assertDatabaseMissing('template_tasks', ['name' => reset($this->insertData['task_templates'])['taskName']]);

        $this->post(route('tasks.template.store'), $this->insertData)
            ->assertOk()
            ->assertJsonFragment(['message' => trans('Calendar::lang.task-plugin-template-created')]);

        //assert data exists after creating
        $this->assertDatabaseHas('task_templates', ['name' => $this->insertData['name']]);
        $this->assertDatabaseHas(
            'template_tasks',
            [
                'name' => reset($this->insertData['task_templates'])['taskName'],
                'category' => $this->insertData['category_id']
            ]
        );
    }

    public function test_updateMethod_fails_whenInvalidTemplate_isSupplied()
    {
        $this->getLoggedInUserForWeb('admin');

        $this->put(route('tasks.template.update', 10000), $this->insertData)
            ->assertStatus(400)
            ->assertJsonFragment(['message' => trans('Calendar::lang.task-plugin-updating-template-which-does-not-exist')]);
    }

    public function test_updateMethod_updatesTaskTemplates_successfully()
    {
        $this->getLoggedInUserForWeb('admin');

        $template = factory(TaskTemplate::class)->create();

        $templateTask = factory(TemplateTask::class)->create(['template_id' => $template->id]);

        //assert value is correct before update
        $this->assertDatabaseHas('task_templates', ['name' => $template->name,'id' => $template->id]);
        $this->assertDatabaseHas('template_tasks', ['name' => $templateTask->name, 'template_id' => $templateTask->template_id]);

        $this->put(route('tasks.template.update', $template->id), $this->insertData)
            ->assertOk()
            ->assertJsonFragment(['message' => trans('Calendar::lang.task-plugin-template-created')]);

        //assert data correctly updated
        $this->assertDatabaseHas('task_templates', ['name' => $this->insertData['name']]);
        $this->assertDatabaseHas(
            'template_tasks',
            [
                'template_id' => $templateTask->template_id,'name' => reset($this->insertData['task_templates'])['taskName'],
                'category' => $this->insertData['category_id']
            ]
        );
    }

    public function test_TaskTemplateRequestValidation_enforcesProperValidation()
    {
        $this->getLoggedInUserForWeb('admin');

        $expectedJsonFragment = [
            "name" => "This field is required",
            "description" => "This field is required",
            "task_templates" => "The task templates field is required."
        ];

        $this->put(route('tasks.template.update', 10000), [])
            ->assertStatus(412)
            ->assertJsonFragment($expectedJsonFragment);
    }

    public function test_applyTemplateMethod_failsIfTheTemplateIsAlreadyAppliedToTicket()
    {
        $this->getLoggedInUserForWeb('agent');

        $ticketId = factory(Tickets::class)->create()->id;

        $templateId = factory(TaskTemplate::class)->create()->id;

        factory(Task::class)->create(['ticket_id' => $ticketId,'task_template_id' => $templateId]);

        $this->post(route('tasks.template.apply'), ['template' => $templateId, 'ticketId' => $ticketId])
            ->assertStatus(400)
            ->assertJsonFragment(['message' => trans('Calendar::lang.task-plugin-template-already-applied-to-ticket')]);
    }

    public function test_applyMethodApplies_template_to_tickets_successfully()
    {
        $this->getLoggedInUserForWeb('agent');

        $ticketId = factory(Tickets::class)->create()->id;

        $templateTask = factory(TemplateTask::class)->create();

        $this->assertDatabaseMissing('tasks', ['task_name' => $templateTask->name,'ticket_id' => $ticketId]);

        $this->post(route('tasks.template.apply'), ['template' => $templateTask->template_id, 'ticketId' => $ticketId])
            ->assertOk()
            ->assertJsonFragment(['message' => trans('Calendar::lang.task-plugin-applying-tasks-from-template-success')]);

        $this->assertDatabaseHas('tasks', ['task_name' => $templateTask->name,'ticket_id' => $ticketId]);
    }
}
