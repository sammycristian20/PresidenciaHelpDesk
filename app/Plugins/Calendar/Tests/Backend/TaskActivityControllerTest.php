<?php

namespace App\Plugins\Calendar\Tests\Backend;

use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Calendar\Activity\Models\Activity;
use App\Plugins\Calendar\Controllers\TaskActivityController;
use App\Plugins\Calendar\Model\Task;
use Carbon\Carbon;
use Tests\AddOnTestCase;

class TaskActivityControllerTest extends AddOnTestCase
{

    public function test_formatValues_method_formats_the_data_in_required_format()
    {
        $activity = new TaskActivityController();

        //checking dateformat
        $formattedDateValue = $this->getPrivateMethod($activity, 'formatValues', ['date', '1995-07-08']);
        $this->assertEquals("July 8, 1995, 1:00 am", $formattedDateValue);

        //checking status format
        $formattedStatusValue = $this->getPrivateMethod($activity, 'formatValues', ['task_type', 0]);
        $this->assertEquals("Public", $formattedStatusValue);

        //checking ticket format
        $ticket = factory(Tickets::class)->create();
        $formattedTicketValue = $this->getPrivateMethod($activity, 'formatValues', ['ticket', $ticket->id]);
        $expected = '<a href="http://localhost:8000/thread/'.$ticket->id.'" target="_blank">'.$ticket->ticket_number.'</a>';
        $this->assertEquals($expected, $formattedTicketValue);

        //checking agent format
        $formattedAgentValue = $this->getPrivateMethod($activity, 'formatValues', ['agent', 1]);
        $this->assertEquals('<a href="http://localhost:8000/user/1" target="_blank">Demo Admin</a>', $formattedAgentValue);
    }

    public function test_show_displays_all_activities_of_task()
    {
        $this->getLoggedInUserForWeb('agent');

        $taskId = factory(Task::class)->create()->id;

        factory(Activity::class)->create(['subject_id' => $taskId]);

        $this->get("tasks/api/activity/$taskId")
            ->assertOk()
            ->assertJsonFragment(["message" => "Set name as <b>Factory Task</b>"]);
    }

    public function test_show_fails_when_invalid_taskId_is_passed()
    {
        $this->getLoggedInUserForWeb('agent');

        $this->get("tasks/api/activity/10000000")
            ->assertStatus(400)
            ->assertJsonFragment(['message' => trans('Calendar::task-plugin-no-activity')]);
    }
}