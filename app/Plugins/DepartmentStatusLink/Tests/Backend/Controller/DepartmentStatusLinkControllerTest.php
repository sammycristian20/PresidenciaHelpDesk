<?php

namespace App\Plugins\DepartmentStatusLink\Tests\Backend\Controllers;

use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Status as CoreTicketStatus;
use App\Plugins\DepartmentStatusLink\Controllers\DepartmentStatusLinkController;
use App\Plugins\DepartmentStatusLink\Model\Department;
use App\Plugins\DepartmentStatusLink\Model\TicketStatus;
use Tests\AddOnTestCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentStatusLinkControllerTest extends AddOnTestCase
{
    protected $controllerInstance;

    public function setUp():void
    {
        parent::setUp();

        $this->controllerInstance = new DepartmentStatusLinkController;
    }

    /** @group department-status-link */
    public function test_createFormField_getDepartmentStatusCreateFormField()
    {
        $response = $this->controllerInstance->createFormField();

        $this->assertInstanceOf(View::class, $response);
    }

    /** @group department-status-link */
    public function test_editFormField_getDepartmentStatusEditFormField()
    {
        $department = Department::first();
        $response   = $this->controllerInstance->editFormField($department);

        $this->assertInstanceOf(View::class, $response);
    }

    /** @group department-status-link */
    public function test_attachStatusWithDepartment_syncDepartmentStatuses()
    {
        $department = Department::first();
        $status     = TicketStatus::first();
        $request    = new Request();

        $request->replace(['department_status' => [$status->id]]);

        $this->controllerInstance->attachStatusWithDepartment($department, $request);

        $this->assertDatabaseHas('ticket_status_attachables', [
            'ticket_status_id'              => $status->id,
            'ticket_status_attachable_id'   => $department->id,
            'ticket_status_attachable_type' => Department::class,
        ]);
    }

    /** @group department-status-link */
    public function test_getDepartmentStatuses_getDepartmentAssociatedStatusesWithCoreTicketStatus()
    {
        $baseQuery = CoreTicketStatus::where('name', 'LIKE', "%%");
        $ticket    = $this->createTicketWithDepartmentStatus();

        $this->controllerInstance->getDepartmentStatuses($baseQuery, [$ticket->id]);

        $this->assertEquals(1, $baseQuery->get()->count());
    }

    /** @group department-status-link */
    public function test_getDepartmentStatuses_getDepartmentAssociatedStatuses()
    {
        $baseQuery = TicketStatus::where('name', 'LIKE', "%%");
        $ticket    = $this->createTicketWithDepartmentStatus();

        $this->controllerInstance->getDepartmentStatuses($baseQuery, [$ticket->id]);

        $this->assertEquals(1, $baseQuery->get()->count());
    }

    private function createTicketWithDepartmentStatus()
    {
        $department = Department::first();
        $status     = TicketStatus::first(['id'])->toArray();

        $department->statuses()->sync($status);

        return factory(Tickets::class)->create(['status' => array_first($status), 'dept_id' => $department->id]);
    }
}
