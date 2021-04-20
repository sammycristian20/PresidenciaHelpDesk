<?php

namespace App\FaveoReport\Tests\Backend\Controllers;

use App\FaveoReport\Controllers\PerformanceController;
use App\FaveoReport\Models\Report;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
use Illuminate\Http\Request;
use Tests\DBTestCase;

class PerformanceControllerTest extends DBTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->blockTicketEvents();
    }

    public function test_getAgentPerformanceData_whenApiIsCalled_shouldGiveSuccessResponseWithCorrespondingData()
    {
        $this->getLoggedInUserForWeb('admin');

        $ticket = factory(Tickets::class)->create(['assigned_to' => 1]);

        $ticket->thread()->create(['is_internal'=>1, 'title'=>'test_title', 'body'=>'test_body']);

        $reportId = Report::where("type", "agent-performance")->value("id");
        $methodResponse = $this->call('GET', "/api/agent/agent-performance-report/$reportId");

        $methodResponse->assertStatus(200);

        $dataArray = json_decode($methodResponse->getContent())->data->data;

        $this->assertPerformanceStructure($dataArray);
    }

    public function test_getTeamPerformanceData_whenApiIsCalled_shouldGiveSuccessResponseWithCorrespondingData()
    {
        $this->getLoggedInUserForWeb('admin');

        $ticket = factory(Tickets::class)->create(['team_id' => 1]);

        $ticket->thread()->create(['is_internal'=>1, 'title'=>'test_title', 'body'=>'test_body']);

        $reportId = Report::where("type", "team-performance")->value("id");

        $methodResponse = $this->call('GET', "/api/agent/team-performance-report/$reportId");

        $methodResponse->assertStatus(200);

        $dataArray = json_decode($methodResponse->getContent())->data->data;

        $this->assertPerformanceStructure($dataArray);
    }

    public function test_getDepartmentPerformanceData_whenApiIsCalled_shouldGiveSuccessResponseWithCorrespondingData()
    {
        $this->getLoggedInUserForWeb('admin');

        $ticket = factory(Tickets::class)->create(['dept_id' => 1]);

        $ticket->thread()->create(['is_internal'=>1, 'title'=>'test_title', 'body'=>'test_body']);

        $reportId = Report::where("type", "department-performance")->value("id");

        $methodResponse = $this->call('GET', "/api/agent/department-performance-report/$reportId");

        $methodResponse->assertStatus(200);

        $dataArray = json_decode($methodResponse->getContent())->data->data;

        $this->assertPerformanceStructure($dataArray);
    }

    private function assertPerformanceStructure($dataArray)
    {
        $this->assertCount(1, $dataArray);

        $this->assertTrue(property_exists($dataArray[0], 'id'));

        $this->assertTrue(property_exists($dataArray[0], 'name'));

        $this->assertTrue(property_exists($dataArray[0], 'assigned_tickets'));

        $this->assertTrue(property_exists($dataArray[0], 'resolved_tickets'));

        $this->assertTrue(property_exists($dataArray[0], 'reopened_tickets'));

        $this->assertTrue(property_exists($dataArray[0], 'tickets_with_response_sla_met'));

        $this->assertTrue(property_exists($dataArray[0], 'tickets_with_resolution_sla_met'));

        $this->assertTrue(property_exists($dataArray[0], 'avg_resolution_time'));

        $this->assertTrue(property_exists($dataArray[0], 'responses'));

        $this->assertTrue(property_exists($dataArray[0], 'avg_response_time'));
    }

    public function test_getThreadDataByAgentIds_whenExistingAgentIsPassed_shouldReturnAverageResponseTimeAndResponseCount()
    {
        $this->getLoggedInUserForWeb('admin');

        $ticket = factory(Tickets::class)->create();

        $ticket->thread()->create(['user_id'=>$this->user->id, 'response_time'=> 10, 'poster'=>'support']);
        $ticket->thread()->create(['user_id'=>$this->user->id, 'response_time'=> 20, 'poster'=>'support']);

        $request = new Request();

        $classObject = $this->getClassObject();
        $this->setPrivateProperty($classObject, 'request', $request);

        $methodResponse = $this->getPrivateMethod($classObject, 'getThreadDataByAgentIds', [[$this->user->id]]);

        $this->assertEquals(2,$methodResponse->responses);
        $this->assertEquals(15,$methodResponse->avg_response_time);
    }

    /** @group getProfileHyperLink */
    public function test_getProfileHyperLink_whenTypeIsAgent_shouldGiveAgentProfileLink()
    {
        $classObject = $this->getClassObject();

        $this->setPrivateProperty($classObject, 'type', 'agent-performance');

        $elementObject = (object)['assigned'=>(object)['id'=>'testId', 'full_name'=>'Test Name']];

        $methodResponse = $this->getPrivateMethod($classObject, 'getProfileHyperLink', [$elementObject]);

        $this->assertStringContainsString('Test Name', $methodResponse);

        $this->assertStringContainsString('agent/testId', $methodResponse);
    }

    /** @group getProfileHyperLink */
    public function test_getProfileHyperLink_whenTypeIsDepartment_shouldGiveDepartmentProfileLink()
    {
        $classObject = $this->getClassObject();

        $this->setPrivateProperty($classObject, 'type', 'department-performance');

        $elementObject = (object)['department'=>(object)['id'=>'testId', 'name'=>'Test Name']];

        $methodResponse = $this->getPrivateMethod($classObject, 'getProfileHyperLink', [$elementObject]);

        $this->assertStringContainsString('Test Name', $methodResponse);

        $this->assertStringContainsString('department/testId', $methodResponse);
    }

    /** @group getProfileHyperLink */
    public function test_getProfileHyperLink_whenTypeIsTeam_shouldGiveTeamProfileLink()
    {
        $classObject = $this->getClassObject();

        $this->setPrivateProperty($classObject, 'type', 'team-performance');

        $elementObject = (object)['assignedTeam'=>(object)['id'=>'testId', 'name'=>'Test Name']];

        $methodResponse = $this->getPrivateMethod($classObject, 'getProfileHyperLink', [$elementObject]);

        $this->assertStringContainsString('Test Name', $methodResponse);

        $this->assertStringContainsString('assign-teams/testId', $methodResponse);
    }

    /** @group getAgentIdsByType */
    public function test_getAgentIdsByType_whenTypeIsAgent_shouldGivePassedAgentId()
    {
        $classObject = $this->getClassObject();

        $this->setPrivateProperty($classObject, 'type', 'agent-performance');

        $elementObject = (object)['assigned_to'=> 1];

        $methodResponse = $this->getPrivateMethod($classObject, 'getAgentIdsByType', [$elementObject]);

        $this->assertEquals([1], $methodResponse);
    }

    /** @group getAgentIdsByType */
    public function test_getAgentIdsByType_whenTypeIsDepartment_shouldGiveAgentIdsofThatDepartment()
    {
        $classObject = $this->getClassObject();

        $this->setPrivateProperty($classObject, 'type', 'department-performance');

        $departmentId = Department::create()->id;
        $elementObject = (object)['dept_id'=> $departmentId];
        $agentOne = factory(User::class)->create(['role'=>'agent'])->id;
        $agentTwo = factory(User::class)->create(['role'=>'agent'])->id;

        DepartmentAssignAgents::create(['department_id'=>$departmentId, 'agent_id'=> $agentOne]);
        DepartmentAssignAgents::create(['department_id'=>$departmentId, 'agent_id'=> $agentTwo]);

        $methodResponse = $this->getPrivateMethod($classObject, 'getAgentIdsByType', [$elementObject]);

        $this->assertEquals([$agentOne, $agentTwo], $methodResponse);
    }

    public function test_getRecordId_whenTypeIsAgent_shouldGiveAgentId()
    {
        $agent = User::create(['role'=>'admin']);

        $agent->departments()->sync([1]);

        $element = factory(Tickets::class)->create(['assigned_to'=> $agent->id, 'dept_id'=>1]);

        $classObject = $this->getClassObject();

        $this->setPrivateProperty($classObject, 'type', 'agent-performance');

        $methodResponse = $this->getPrivateMethod($classObject, 'getRecordId', [$element]);

        $this->assertEquals($agent->id, $methodResponse);
    }

    public function test_getRecordId_whenTypeIsDepartment_shouldGiveDepartmentId()
    {
        $agent = User::create(['role'=>'admin']);

        $deptId = $agent->departments()->create()->id;

        $element = factory(Tickets::class)->create(['assigned_to'=> $agent->id, 'dept_id'=>$deptId]);

        $classObject = $this->getClassObject();

        $this->setPrivateProperty($classObject, 'type', 'department-performance');

        $methodResponse = $this->getPrivateMethod($classObject, 'getRecordId', [$element]);

        $this->assertEquals($deptId, $methodResponse);
    }

    public function test_getRecordId_whenTypeIsTeam_shouldGiveTeamId()
    {
        $teamId = Teams::create()->id;

        $element = factory(Tickets::class)->create(['team_id'=> $teamId]);

        $classObject = $this->getClassObject();

        $this->setPrivateProperty($classObject, 'type', 'team-performance');

        $methodResponse = $this->getPrivateMethod($classObject, 'getRecordId', [$element]);

        $this->assertEquals($teamId, $methodResponse);
    }

    private function getClassObject()
    {
        $request = new Request;
        return new PerformanceController($request);
    }
}