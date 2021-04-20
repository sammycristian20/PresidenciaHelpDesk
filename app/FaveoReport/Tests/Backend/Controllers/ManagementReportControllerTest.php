<?php

namespace App\FaveoReport\Tests\Backend\Controllers;

use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\SubReport;
use Illuminate\Http\Request;
use Tests\DBTestCase;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Form\FormField;
use App\FaveoReport\Controllers\ManagementReportController;
use Config;
use App\FaveoReport\Models\ReportColumn;

class ManagementReportControllerTest extends DBTestCase
{

    private $reportId;

    private $subReportId;

    public function setUp(): void
    {
        parent::setUp();

        $this->reportId = Report::where("type", "management-report")->value("id");

        $this->subReportId = SubReport::where("report_id", $this->reportId)->value("id");

        $this->blockTicketEvents();
    }

    /** @group getManagementReportData */
    public function test_getManagementReportData_whenNoTicketRecordIsFound_shouldGiveSuccessResponse()
    {
        $this->getLoggedInUserForWeb('admin');

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id' => 1, 'status' => 1, 'type' => 1, 'location_id' => 1,
            'user_id' => 1, 'assigned_to' => 1, 'priority_id' => 1, 'source' => 1
        ]);

        $response = $this->call('GET', "api/agent/management-report/$this->reportId");

        $response->assertStatus(200);
        $tickets = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $tickets);
    }

  /** @group getManagementReportData */
  public function test_getManagementReportData_whenCustomColumnAsTimestampIsAddedWithNonEmptyValue_shouldFormatValueAsDateTimeString()
  {
        $this->getLoggedInUserForWeb('admin');
        ReportColumn::create(['key' => 'test_key', 'label' => 'Test Column', 'is_custom' => 1, 'is_timestamp'=>1,'is_visible' => 1, 'equation' => ':created_at', 'sub_report_id' => $this->subReportId]);

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id' => 1, 'status' => 1, 'type' => 1, 'location_id' => 1,
            'user_id' => 1, 'assigned_to' => 1, 'priority_id' => 1, 'source' => 1
        ]);

        $response = $this->call('GET', "api/agent/management-report/$this->reportId");

        $response->assertStatus(200);

        $tickets = json_decode($response->getContent())->data->data;

        $this->assertCount(1, $tickets);

        $this->assertEquals($ticket->created_at->toDateTimeString(), $tickets[0]->test_key);
  }

    /** @group getManagementReportData */
    public function test_getManagementReportData_whenCustomColumnAsTimestampIsAddedWithEmptyValue_shouldGiveThatValueAsEmptyString()
    {
        $this->getLoggedInUserForWeb('admin');

        ReportColumn::create(['key' => 'test_key', 'label' => 'Test Column', 'is_custom' => 1, 'is_visible' => 1, 'equation' => ':closed_at', 'is_timestamp' => 1, 'sub_report_id' => $this->subReportId]);

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id' => 1, 'status' => 1, 'type' => 1, 'location_id' => 1,
            'user_id' => 1, 'assigned_to' => 1, 'priority_id' => 1, 'source' => 1
        ]);

        $response = $this->call('GET', "api/agent/management-report/$this->reportId");

        $response->assertStatus(200);

        $tickets = json_decode($response->getContent())->data->data;

        $this->assertCount(1, $tickets);

        $this->assertEquals('', $tickets[0]->test_key);
    }

    public function test_getManagementReportData_whenCustomFieldsArePresentInTheTicket_shouldFormatCustomFieldInKeyValuPair()
    {
        $this->getLoggedInUserForWeb('admin');

        $formField = FormField::create(['type' => 'text', 'active' => 1]);

        $formField->labels()->create(['meant_for' => 'agent', 'label' => 'testLabel', 'language' => 'en']);

        ReportColumn::create(['key' => "custom_$formField->id", 'label' => 'Test Column', 'is_custom' => 1, 'is_visible' => 1, 'equation' => ':closed_at', 'is_timestamp' => 1, 'sub_report_id' => $this->subReportId]);

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id' => 1, 'status' => 1, 'type' => 1, 'location_id' => 1,
            'user_id' => 1, 'assigned_to' => 1, 'priority_id' => 1, 'source' => 1
        ]);

        $ticket->customFieldValues()->create(['value' => 'testValue', 'form_field_id' => $formField->id]);


        $response = $this->call('GET', "api/agent/management-report/$this->reportId");
        $response->assertStatus(200);

        $tickets = json_decode($response->getContent())->data->data;

        $this->assertCount(1, $tickets);
        $key = "custom_$formField->id";

        $this->assertStringContainsString('testValue', $tickets[0]->$key);
        $this->assertStringContainsString($key . "=testValue", $tickets[0]->$key);
    }

    public function test_getManagementReportData_whenCustomFieldsArePresentInTheTicketWithValueAsArray_shouldFormatCustomFieldInValueInCommaSeperatedValue()
    {
        $this->getLoggedInUserForWeb('admin');

        $formField = FormField::create(['type' => 'text', 'active' => 1]);

        $formField->labels()->create(['meant_for' => 'agent', 'label' => 'testLabel', 'language' => 'en']);

        ReportColumn::create(['key' => "custom_$formField->id", 'label' => 'Test Column', 'is_custom' => 1, 'is_visible' => 1, 'equation' => ':closed_at', 'is_timestamp' => 1, 'sub_report_id' => $this->subReportId]);

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id' => 1, 'status' => 1, 'type' => 1, 'location_id' => 1,
            'user_id' => 1, 'assigned_to' => 1, 'priority_id' => 1, 'source' => 1
        ]);

        $ticket->customFieldValues()->create(['value' => ['valueOne', 'valueTwo'], 'form_field_id' => $formField->id]);

        $response = $this->call('GET', "api/agent/management-report/$this->reportId");
        $response->assertStatus(200);

        $tickets = json_decode($response->getContent())->data->data;

        $this->assertCount(1, $tickets);
        $key = "custom_$formField->id";

        $this->assertStringContainsString('valueOne,valueTwo', $tickets[0]->$key);
        $this->assertStringContainsString($key . "=valueOne,valueTwo", $tickets[0]->$key);
    }

    public function test_getManagementReportData_whenOrganizationIsPresentForTheOwner_shouldFormatOrganizationsInCommaSeperatedValue()
    {
        $this->getLoggedInUserForWeb('admin');

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id' => 1, 'status' => 1, 'type' => 1, 'location_id' => 1,
            'assigned_to' => 1, 'priority_id' => 1, 'source' => 1, 'user_id' => $this->user->id
        ]);

        $this->user->organizations()->create(['name' => 'OrgOne']);
        $this->user->organizations()->create(['name' => 'OrgTwo']);

        $response = $this->call('GET', "api/agent/management-report/$this->reportId");

        $response->assertStatus(200);

        $tickets = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $tickets);
        $this->assertEquals("OrgOne, OrgTwo", $tickets[0]->organizations);
    }

    public function test_getManagementReportData_whenCustomAttachmentFieldIsPresent_attachmentFieldShouldNotComeInResponse()
    {
        $this->getLoggedInUserForWeb('admin');

        $formField = FormField::create(['type' => 'file', 'active' => 1]);

        $formField->labels()->create(['meant_for' => 'agent', 'label' => 'testLabel', 'language' => 'en']);

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id' => 1, 'status' => 1, 'type' => 1, 'location_id' => 1,
            'user_id' => 1, 'assigned_to' => 1, 'priority_id' => 1, 'source' => 1, 'user_id' => $this->user->id
        ]);

        $ticket->customFieldValues()->create(['value' => 'valueOne', 'form_field_id' => $formField->id]);

        $response = $this->call('GET', "api/agent/management-report/$this->reportId");
        $response->assertStatus(200);

        $tickets = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $tickets);
        $key = "custom_$formField->id";

        $this->assertFalse(isset($tickets[0]->$key));
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenTicketNumberIsPresent_shouldMakeTicketNumberAsHyperlink()
    {
        $this->getLoggedInUserForWeb('admin');

        $ticket = factory(Tickets::class)->create(['ticket_number' => 'test_ticket_number']);

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $ticketUrl = Config::get('app.url') . '/thread' . "/" . $ticket->id;

        $this->assertStringContainsString($ticketUrl, $ticket->ticket_number);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenSubjectIsPresent_shouldMakeSubjectAsHyperlink()
    {
        $this->getLoggedInUserForWeb('admin');

        $ticket = factory(Tickets::class)->create(['ticket_number' => 'test_ticket_number']);

        $ticket->thread()->create(['title' => 'test_title', 'is_internal' => 0]);

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $ticketUrl = Config::get('app.url') . '/thread' . "/" . $ticket->id;

        $this->assertStringContainsString($ticketUrl, $ticket->subject);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenStatusIsPresent_shouldMakeStatusAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['status' => 1]);

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('status-ids[]=1', $ticket->statuses->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenDepartmentIsPresent_shouldMakeDepartmentAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['dept_id' => 1]);

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('dept-ids[]=1', $ticket->department->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenPriorityIsPresent_shouldMakePriorityAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['priority_id' => 1]);

        $ticket = Tickets::whereId($ticket->id)->with('priority:priority_id,priority as name')->first();

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('priority-ids[]=1', $ticket->priority->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenTypeIsPresent_shouldMakeTypeAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['type' => 1]);

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('type-ids[]=1', $ticket->types->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenLocationIsPresent_shouldMakeLocationAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['location_id' => 1]);

        $ticket = Tickets::whereId($ticket->id)->with('location:id,title as name')->first();

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('location-ids[]=1', $ticket->location->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenHelptopicIsPresent_shouldMakeHelptopicAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['help_topic_id' => 1]);

        $ticket = Tickets::whereId($ticket->id)->with('helptopic:id,topic as name')->first();

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('helptopic-ids[]=1', $ticket->helptopic->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenSourceIsPresent_shouldMakeSourceAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['source' => 1]);

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('source-ids[]=1', $ticket->sources->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenAssignedAgentIsPresent_shouldMakeAssignedAgentAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['assigned_to' => 1]);

        $ticket = Tickets::whereId($ticket->id)->with('assigned')->first();

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('assignee-ids[]=1', $ticket->assigned->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenAssignedTeamIsPresent_shouldMakeAssignedTeamAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['team_id' => 1]);

        $ticket = Tickets::whereId($ticket->id)->with('assignedTeam')->first();
        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('team-ids[]=1', $ticket->assignedTeam->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenOwnerIsPresent_shouldMakeOwnerAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['user_id' => 1]);

        $ticket = Tickets::whereId($ticket->id)->with('user')->first();

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('owner-ids[]=1', $ticket->user->name);
    }

    /** @group makeHyperlinkValues */
    public function test_makeHyperlinkValues_whenCreatorIsPresent_shouldMakeCreatorAsHyperlink()
    {
        $ticket = factory(Tickets::class)->create(['creator_id' => 1]);

        $ticket = Tickets::whereId($ticket->id)->with('creator')->first();

        $classObject = new ManagementReportController(new Request);

        $this->getPrivateMethod($classObject, 'makeHyperlinkValues', [&$ticket]);

        $this->assertStringContainsString('creator-ids[]=1', $ticket->creator->name);
    }
}
