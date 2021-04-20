<?php

namespace App\FaveoReport\Tests\Backend;

use App\FaveoReport\Models\Report;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use Illuminate\Http\Request;
use Lang;
use Tests\DBTestCase;
use App\FaveoReport\Controllers\TopCustomerAnalysisController;

class TopCustomerAnalysisControllerTest extends DBTestCase
{
    private $reportId;

    public function setUp(): void
    {
        parent::setUp();
        $this->reportId = Report::where("type", "top-customer-analysis")->value("id");
        $this->blockTicketEvents();
    }

    public function test_getOrganizationReport_whenAllReceivedTicketsAreRequested_shouldReturnAllTicketsCount()
    {
        $this->setUpForOrganizationReport();

        // open ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'status'=>1]);

        // closed ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'status'=>3]);

        $methodResponse = $this->call('GET', "/api/agent/top-customer-analysis/$this->reportId");

        $data = json_decode($methodResponse->getContent())->data[0]->data;

        $this->assertCount(1, $data);
        $this->assertEquals($this->organization->name, $data[0]->label);
        $this->assertEquals($this->organization->id, $data[0]->id);
        $this->assertEquals(2, $data[0]->value);
    }

    public function test_getOrganizationReport_whenAllResolvedTicketsAreRequested_shouldReturnResolvedTicketCount()
    {
        $this->setUpForOrganizationReport();

        // open ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'status'=>1, 'closed' => 0]);

        // closed ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'status'=>3, 'closed' => 1]);

        $methodResponse = $this->call('GET', "/api/agent/top-customer-analysis/$this->reportId");

        $data = json_decode($methodResponse->getContent())->data[1]->data;

        $this->assertCount(1, $data);
        $this->assertEquals($this->organization->name, $data[0]->label);
        $this->assertEquals($this->organization->id, $data[0]->id);
        $this->assertEquals(1, $data[0]->value);
    }


    public function test_getOrganizationReport_whenAllUnresolvedTicketsAreRequested_shouldReturnUnresolvedTicketCount()
    {
        $this->setUpForOrganizationReport();

        // open ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'status'=>1, 'closed' => 0]);

        // closed ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'status'=>3, 'closed' => 1]);

        $methodResponse = $this->call('GET', "/api/agent/top-customer-analysis/$this->reportId");

        $data = json_decode($methodResponse->getContent())->data[2]->data;

        $this->assertCount(1, $data);
        $this->assertEquals($this->organization->name, $data[0]->label);
        $this->assertEquals($this->organization->id, $data[0]->id);
        $this->assertEquals(1, $data[0]->value);
    }

    public function test_getOrganizationReport_whenAllReopenedTicketsAreRequested_shouldReturnReopenedTicketCount()
    {
        $this->setUpForOrganizationReport();

        // reopened ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'reopened'=>1]);

        // non-reopened ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'reopened'=>0]);

        $methodResponse = $this->call('GET', "/api/agent/top-customer-analysis/$this->reportId");

        $data = json_decode($methodResponse->getContent())->data[3]->data;

        $this->assertCount(1, $data);
        $this->assertEquals($this->organization->name, $data[0]->label);
        $this->assertEquals($this->organization->id, $data[0]->id);
        $this->assertEquals(1, $data[0]->value);
    }

    public function test_getOrganizationReport_whenResponseSlaMetTicketsAreRequested_shouldReturnResponseSlaMetTicketCount()
    {
        $this->setUpForOrganizationReport();

        // reopened ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'is_response_sla'=>1]);

        // non-reopened ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'is_response_sla'=>0]);

        $methodResponse = $this->call('GET', "/api/agent/top-customer-analysis/$this->reportId");

        $data = json_decode($methodResponse->getContent())->data[4]->data;

        $this->assertCount(1, $data);
        $this->assertEquals($this->organization->name, $data[0]->label);
        $this->assertEquals($this->organization->id, $data[0]->id);
        $this->assertEquals(1, $data[0]->value);
    }

    public function test_getOrganizationReport_whenResolutionSlaMetTicketsAreRequested_shouldReturnResolutionSlaMetTicketCount()
    {
        $this->setUpForOrganizationReport();

        // reopened ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'is_resolution_sla'=>1]);

        // non-reopened ticket
        factory(Tickets::class)->create(['user_id' => $this->user->id, 'is_resolution_sla'=>0]);

        $methodResponse = $this->call('GET', "/api/agent/top-customer-analysis/$this->reportId");

        $data = json_decode($methodResponse->getContent())->data[5]->data;

        $this->assertCount(1, $data);
        $this->assertEquals($this->organization->name, $data[0]->label);
        $this->assertEquals($this->organization->id, $data[0]->id);
        $this->assertEquals(1, $data[0]->value);
    }

    public function test_getOrganizationReport_whenClientResponsesAreRequested_shouldReturnClientResponseCount()
    {
        $this->setUpForOrganizationReport();

        // reopened ticket
        $ticket = factory(Tickets::class)->create(['user_id' => $this->user->id, 'is_response_sla'=>1]);

        $ticket->thread()->create(['title'=>'test', 'poster'=>'client']);
        $ticket->thread()->create(['title'=>'test', 'poster'=>'client']);
        $ticket->thread()->create(['title'=>'test', 'poster'=>'support']);

        $methodResponse = $this->call('GET', "/api/agent/top-customer-analysis/$this->reportId", ['type'=>'client_responses']);

        $data = json_decode($methodResponse->getContent())->data[6]->data;

        $this->assertCount(1, $data);
        $this->assertEquals($this->organization->name, $data[0]->label);
        $this->assertEquals($this->organization->id, $data[0]->id);
        $this->assertEquals(2, $data[0]->value);
    }

    public function test_getOrganizationReport_whenAgentResponsesAreRequested_shouldReturnAgentResponseCount()
    {
        $this->setUpForOrganizationReport();

        // reopened ticket
        $ticket = factory(Tickets::class)->create(['user_id' => $this->user->id, 'is_response_sla'=>1]);

        $ticket->thread()->create(['title'=>'test', 'poster'=>'client']);
        $ticket->thread()->create(['title'=>'test', 'poster'=>'support']);
        $ticket->thread()->create(['title'=>'test', 'poster'=>'support']);

        $methodResponse = $this->call('GET', "/api/agent/top-customer-analysis/$this->reportId", ['type'=>'agent_responses']);

        $data = json_decode($methodResponse->getContent())->data[7]->data;

        $this->assertCount(1, $data);
        $this->assertEquals($this->organization->name, $data[0]->label);
        $this->assertEquals($this->organization->id, $data[0]->id);
        $this->assertEquals(2, $data[0]->value);
    }

    public function test_linkBuilder_parametersArePassedWithoutKeyBeingPresentInParam_shouldReturnStringWithAllParams()
    {
        $classObject = new TopCustomerAnalysisController(new Request);

        $request = new Request(['test'=>'test']);

        // set request params
        // creating a request and assigning to request property of class
        $this->setPrivateProperty($classObject, 'request', $request);

        $methodResponse = $this->getPrivateMethod($classObject, 'getRedirectLink', ['organization-ids', [1,2], 'received_tickets']);

        $this->assertStringContainsString(http_build_query(['test'=>'test']), $methodResponse);
    }

    public function test_linkBuilder_parametersArePassedWithKeyBeingPresentInParam_shouldGiveTheFinalValue()
    {
        $classObject = new TopCustomerAnalysisController(new Request);

        $request = new Request(['organization-ids'=>[1]]);

        // set request params
        // creating a request and assigning to request property of class
        $this->setPrivateProperty($classObject, 'request', $request);

        $methodResponse = $this->getPrivateMethod($classObject, 'getRedirectLink', ['organization-ids', [2,3], 'received_tickets']);

        $this->assertStringContainsString(http_build_query(['organization-ids'=> [2,3]]), $methodResponse);
    }

    public function test_getChartDataLabel_whenChartTypeIsClientResponses_shouldReturnResponseCountAsLabel()
    {
        $classObject = new TopCustomerAnalysisController(new Request);

        $methodResponse = $this->getPrivateMethod($classObject, 'getChartDataLabel', ['client_responses']);

        $this->assertEquals(Lang::get('report::lang.response_count'), $methodResponse);
    }

    public function test_getChartDataLabel_whenChartTypeIsAgentResponses_shouldReturnResponseCountAsLabel()
    {
        $classObject = new TopCustomerAnalysisController(new Request);

        $methodResponse = $this->getPrivateMethod($classObject, 'getChartDataLabel', ['client_responses']);

        $this->assertEquals(Lang::get('report::lang.response_count'), $methodResponse);
    }

    public function test_getChartDataLabel_whenChartTypeIsNonResponse_shouldReturnTicketCountAsLabel()
    {
        $classObject = new TopCustomerAnalysisController(new Request);

        $methodResponse = $this->getPrivateMethod($classObject, 'getChartDataLabel', ['received_tickets']);

        $this->assertEquals(Lang::get('report::lang.ticket_count'), $methodResponse);
    }

    private function setUpForOrganizationReport()
    {
        $this->getLoggedInUserForWeb('admin');

        // create organisation with users and tickets
        $this->createOrganization();

        $this->assignUserWithOrganization($this->organization->id, $this->user->id);
    }

//    NOTE: chart export will be done as a separate task in another PR
//    public function test_exportChart_when()
//    {
//        $this->getLoggedInUserForWeb('admin');
//        $methodResponse = $this->call('POST','api/agent/export-chart/top-customer-analysis');
//        dd($methodResponse);
//    }
}