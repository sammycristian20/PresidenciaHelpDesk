<?php


namespace App\FaveoReport\Tests\Backend\Controllers;

use App\FaveoReport\Controllers\HelpdeskInDepth;
use App\FaveoReport\Models\Report;
use App\Model\helpdesk\Ticket\Tickets;
use Lang;
use Tests\DBTestCase;
use Illuminate\Http\Request;

class HelpdeskInDepthTest extends DBTestCase
{

    private $classObject;

    private $reportId;

    public function setUp(): void
    {
        parent::setUp();

        $this->classObject = new HelpdeskInDepth(new Request());

        $this->setPrivateProperty($this->classObject, 'request', new Request());

        $this->reportId = Report::where("type", "helpdesk-in-depth")->value("id");

        $this->blockTicketEvents();
    }

    public function test_getStatusInDepthReport_whenChartCategoryIsStatus_ShouldReturnTicketCountGroupedByStatus()
    {
        $this->getLoggedInUserForWeb('admin');

        factory(Tickets::class)->create(['status'=>1]);

        factory(Tickets::class)->create(['status'=>3]);

        factory(Tickets::class)->create(['status'=>7]);

        $this->setPrivateProperty($this->classObject, "reportId", $this->reportId);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getStatusInDepthReport');

        $receivedTicketData = json_decode($methodResponse->getContent())->data[0];

        $this->assertEquals("Received Tickets", $receivedTicketData->name);
        $this->assertEquals("received_tickets", $receivedTicketData->id);
        $data = $receivedTicketData->data;
        $this->assertEquals(3, count($data));
        $this->assertEquals("Open", $receivedTicketData->data[0]->label);
        $this->assertEquals(1, $receivedTicketData->data[0]->value);
        $this->assertEquals("Closed", $receivedTicketData->data[1]->label);
        $this->assertEquals(1, $receivedTicketData->data[1]->value);
        $this->assertEquals("Unapproved", $receivedTicketData->data[2]->label);
        $this->assertEquals(1, $receivedTicketData->data[2]->value);
    }

    public function test_getPriorityInDepthReport_whenChartCategoryIsPriority_ShouldReturnTicketCountGroupedByPriority()
    {
        $this->getLoggedInUserForWeb('admin');

        factory(Tickets::class)->create(['status'=>1, 'priority_id'=>1]);

        factory(Tickets::class)->create(['status'=>1, 'priority_id'=>2]);

        factory(Tickets::class)->create(['status'=>1, 'priority_id'=>3]);

        $this->setPrivateProperty($this->classObject, "reportId", $this->reportId);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getPriorityInDepthReport');

        $receivedTicketData = json_decode($methodResponse->getContent())->data[0];

        $this->assertEquals("Received Tickets", $receivedTicketData->name);
        $this->assertEquals("received_tickets", $receivedTicketData->id);
        $data = $receivedTicketData->data;
        $this->assertEquals(3, count($data));
        $this->assertEquals("Low", $receivedTicketData->data[0]->label);
        $this->assertEquals(1, $receivedTicketData->data[0]->value);
        $this->assertEquals("Normal", $receivedTicketData->data[1]->label);
        $this->assertEquals(1, $receivedTicketData->data[1]->value);
        $this->assertEquals("High", $receivedTicketData->data[2]->label);
        $this->assertEquals(1, $receivedTicketData->data[2]->value);
    }

    public function test_getSourceInDepthReport_whenChartCategoryIsSource_ShouldReturnTicketCountGroupedBySource()
    {
        $this->getLoggedInUserForWeb('admin');

        factory(Tickets::class)->create(['status'=>1, 'source'=>1]);

        factory(Tickets::class)->create(['status'=>1, 'source'=>2]);

        factory(Tickets::class)->create(['status'=>1, 'source'=>3]);

        $this->setPrivateProperty($this->classObject, "reportId", $this->reportId);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getSourceInDepthReport');

        $receivedTicketData = json_decode($methodResponse->getContent())->data[0];

        $this->assertEquals("Received Tickets", $receivedTicketData->name);
        $this->assertEquals("received_tickets", $receivedTicketData->id);
        $data = $receivedTicketData->data;
        $this->assertEquals(3, count($data));
        $this->assertEquals("Web", $receivedTicketData->data[0]->label);
        $this->assertEquals(1, $receivedTicketData->data[0]->value);
        $this->assertEquals("Email", $receivedTicketData->data[1]->label);
        $this->assertEquals(1, $receivedTicketData->data[1]->value);
        $this->assertEquals("Agent", $receivedTicketData->data[2]->label);
        $this->assertEquals(1, $receivedTicketData->data[2]->value);
    }

    public function test_getTypeInDepthReport_whenChartCategoryIsType_ShouldReturnTicketCountGroupedByType()
    {
        $this->getLoggedInUserForWeb('admin');

        factory(Tickets::class)->create(['status'=>1, 'type'=>1]);

        factory(Tickets::class)->create(['status'=>1, 'type'=>2]);

        factory(Tickets::class)->create(['status'=>1, 'type'=>3]);

        $this->setPrivateProperty($this->classObject, "reportId", $this->reportId);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTypeInDepthReport');

        $receivedTicketData = json_decode($methodResponse->getContent())->data[0];

        $this->assertEquals("Received Tickets", $receivedTicketData->name);
        $this->assertEquals("received_tickets", $receivedTicketData->id);
        $data = $receivedTicketData->data;
        $this->assertEquals(3, count($data));
        $this->assertEquals("Question", $receivedTicketData->data[0]->label);
        $this->assertEquals(1, $receivedTicketData->data[0]->value);
        $this->assertEquals("Incident", $receivedTicketData->data[1]->label);
        $this->assertEquals(1, $receivedTicketData->data[1]->value);
        $this->assertEquals("Problem", $receivedTicketData->data[2]->label);
        $this->assertEquals(1, $receivedTicketData->data[2]->value);
    }

    public function test_getChartDataLabel_returnHourLabel_ifChartTypeIsAvgTime()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getChartDataLabel', ['avg_first_response_time']);

        $this->assertEquals(Lang::get("report::lang.minutes"), $methodResponse);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getChartDataLabel', ['avg_response_time']);

        $this->assertEquals(Lang::get("report::lang.minutes"), $methodResponse);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getChartDataLabel', ['avg_resolution_time']);

        $this->assertEquals(Lang::get("report::lang.minutes"), $methodResponse);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getChartDataLabel', ['anything_else']);

        $this->assertEquals(Lang::get('report::lang.ticket_count'), $methodResponse);
    }

    public function test_getRedirectLink_whenChartCategoryIsStatus_shouldAppendStatusParameterToRedirectUrl()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getRedirectLink', ['received_tickets','status',1]);

        $this->assertStringContainsString(http_build_query(['status-ids'=>[1]]), $methodResponse);
    }

    public function test_getRedirectLink_whenChartCategoryIsPriority_shouldAppendPriorityParameterToRedirectUrl()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getRedirectLink', ['received_tickets','priority',1]);

        $this->assertStringContainsString(http_build_query(['priority-ids'=>[1]]), $methodResponse);
    }

    public function test_getRedirectLink_whenChartCategoryIsSource_shouldAppendSourceParameterToRedirectUrl()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getRedirectLink', ['received_tickets','source',1]);

        $this->assertStringContainsString(http_build_query(['source-ids'=>[1]]), $methodResponse);
    }

    public function test_getRedirectLink_whenChartCategoryIsType_shouldAppendTypeParameterToRedirectUrl()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getRedirectLink', ['received_tickets','type',1]);

        $this->assertStringContainsString(http_build_query(['type-ids'=>[1]]), $methodResponse);
    }
}
