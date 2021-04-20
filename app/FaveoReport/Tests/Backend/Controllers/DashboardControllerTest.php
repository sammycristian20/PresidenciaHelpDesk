<?php


namespace App\FaveoReport\Tests\Backend\Controllers;

use App\FaveoReport\Controllers\DashboardController;
use App\Model\helpdesk\Ticket\TicketFilter;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
use Illuminate\Http\Request;
use Lang;
use Tests\DBTestCase;

class DashboardControllerTest extends DBTestCase
{

    private $classObject;

    public function setUp(): void
    {
        parent::setUp();

        $this->classObject = new DashboardController(new Request);

        $this->getLoggedInUserForWeb("admin");

        $this->blockTicketEvents();
    }

    public function test_getResolutionTimeScore_whenNoTicketsArePresent_shouldThrowAnException()
    {
        $this->expectException(\UnexpectedValueException::class);
        // should say no data to display
        $this->setPrivateProperty($this->classObject, "request", new Request());

        $this->getPrivateMethod($this->classObject, "getResolutionTimeScore");
    }

    public function test_getResolutionTimeScore_whenTicketsArePresent_shouldTakeAggregateOfTicketCountAndResolutionTime()
    {
        $this->assignAgentToDepartment($this->user, [1]);

        $userOne = factory(User::class)->create(["role"=> "admin"]);
        $this->assignAgentToDepartment($userOne, [1]);

        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>1, "dept_id"=>1, "closed"=>1, "resolution_time"=>10]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>1, "dept_id"=>1, "closed"=>1, "resolution_time"=>10]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>1, "dept_id"=>1, "closed"=>1, "resolution_time"=>10]);

        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$this->user->id, "dept_id"=>1, "closed"=>1, "resolution_time"=>10]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$this->user->id, "dept_id"=>1, "closed"=>1, "resolution_time"=>10]);

        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$userOne->id, "dept_id"=>1, "closed"=>1, "resolution_time"=>10]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$userOne->id, "dept_id"=>1, "closed"=>1, "resolution_time"=>20]);

        $this->setPrivateProperty($this->classObject, "request", new Request());

        $this->setPrivateProperty($this->classObject, "user", $this->user);

        // passing 100 as total system resolution minutes and 10 is ticket count
        $methodResponse = $this->getPrivateMethod($this->classObject, "getResolutionTimeScore");

        $this->assertEquals(50, $methodResponse);
    }

    public function test_getResponseTimeScore_whenNoTicketsArePresent_shouldThrowAnException()
    {
        $this->expectException(\UnexpectedValueException::class);
        // should say no data to display
        $this->setPrivateProperty($this->classObject, "request", new Request());

        $this->getPrivateMethod($this->classObject, "getResponseTimeScore");
    }

    public function test_getResponseTimeScore_whenTicketsArePresent_shouldTakeAggregateOfTicketCountAndResponseTime()
    {
        $this->assignAgentToDepartment($this->user, [1]);

        $userOne = factory(User::class)->create(["role"=> "admin"]);
        $this->assignAgentToDepartment($userOne, [1]);

        $ticketOne = factory(Tickets::class)->create(["status"=> 1, "assigned_to"=>1]);
        $ticketOne->thread()->create(["user_id"=> 1, "response_time" => 10, "is_internal"=>0, "poster"=>"support"]);
        $ticketOne->thread()->create(["user_id"=> 1, "response_time" => 10, "is_internal"=>0, "poster"=>"support"]);
        $ticketOne->thread()->create(["user_id"=> 1, "response_time" => 20, "is_internal"=>0, "poster"=>"support"]);

        $ticketTwo = factory(Tickets::class)->create(["status"=> 1, "assigned_to"=>1]);
        $ticketTwo->thread()->create(["user_id"=> $this->user->id, "response_time" => 10, "is_internal"=>0, "poster"=>"support"]);
        $ticketTwo->thread()->create(["user_id"=> $this->user->id, "response_time" => 10, "is_internal"=>0, "poster"=>"support"]);

        $ticketThree = factory(Tickets::class)->create(["status"=> 1, "assigned_to"=>1]);
        $ticketThree->thread()->create(["user_id"=> $userOne->id, "response_time" => 10, "is_internal"=>0, "poster"=>"support"]);
        $ticketThree->thread()->create(["user_id"=> $userOne->id, "response_time" => 10, "is_internal"=>0, "poster"=>"support"]);

        $this->setPrivateProperty($this->classObject, "request", new Request());

        $this->setPrivateProperty($this->classObject, "user", $this->user);

        // passing 100 as total system resolution minutes and 10 is ticket count
        $methodResponse = $this->getPrivateMethod($this->classObject, "getResponseTimeScore");

        $this->assertEquals(50, $methodResponse);
    }

    public function test_getResolutionSlaMetScore_whenNoTicketsArePresent_shouldThrowAnException()
    {
        $this->expectException(\UnexpectedValueException::class);
        // should say no data to display
        $this->setPrivateProperty($this->classObject, "request", new Request());

        $this->getPrivateMethod($this->classObject, "getResolutionSlaMetScore");
    }

    public function test_getResolutionSlaMetScore_whenTicketsArePresent_shouldTakeAggregateOfTicketCountAndResponseTime()
    {
        $this->assignAgentToDepartment($this->user, [1]);

        $userOne = factory(User::class)->create(["role"=> "admin"]);
        $this->assignAgentToDepartment($userOne, [1]);

        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>1, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>1, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>1, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>1, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$this->user->id, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$this->user->id, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$this->user->id, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$userOne->id, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 1, "assigned_to"=>$userOne->id, "dept_id"=>1, "closed"=>1]);
        factory(Tickets::class)->create(["is_resolution_sla" => 0, "assigned_to"=>$userOne->id, "dept_id"=>1, "closed"=>1]);

        $this->setPrivateProperty($this->classObject, "request", new Request());

        $this->setPrivateProperty($this->classObject, "user", $this->user);

        // passing 100 as total system resolution minutes and 10 is ticket count
        $methodResponse = $this->getPrivateMethod($this->classObject, "getResolutionSlaMetScore");

        $this->assertEquals(50, $methodResponse);
    }

    public function test_getResponseSlaMetScore_whenNoTicketsArePresent_shouldThrowAnException()
    {
        $this->expectException(\UnexpectedValueException::class);
        // should say no data to display
        $this->setPrivateProperty($this->classObject, "request", new Request());

        $this->getPrivateMethod($this->classObject, "getResponseSlaMetScore");
    }

    public function test_getResponseSlaMetScore_whenTicketsArePresent_shouldTakeAggregateOfTicketCountAndResponseTime()
    {
        $this->assignAgentToDepartment($this->user, [1]);

        $userOne = factory(User::class)->create(["role"=> "admin"]);
        $this->assignAgentToDepartment($userOne, [1]);

        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>1, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>1, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>1, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>1, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>$this->user->id, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>$this->user->id, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>$this->user->id, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>$userOne->id, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 1, "assigned_to"=>$userOne->id, "dept_id"=>1]);
        factory(Tickets::class)->create(["is_response_sla" => 0, "assigned_to"=>$userOne->id, "dept_id"=>1]);

        $this->setPrivateProperty($this->classObject, "request", new Request());

        $this->setPrivateProperty($this->classObject, "user", $this->user);

        // passing 100 as total system resolution minutes and 10 is ticket count
        $methodResponse = $this->getPrivateMethod($this->classObject, "getResponseSlaMetScore");

        $this->assertEquals(50, $methodResponse);
    }

    public function test_createTodo_shouldCreateTodoWithOrderAsOneMoreThanTheLastOrderAndStatusAsPending()
    {
        $methodResponse = $this->call("POST", "api/agent/create-todo", ["name"=>"test todo one"]);

        $methodResponse->assertStatus(200);

        // creating another to check order
        $this->call("POST", "api/agent/create-todo", ["name"=>"test todo two"]);

        $todos = $this->user->todos()->orderBy("order", "asc")->get();

        $this->assertEquals("test todo one",$todos[0]->name);
        $this->assertEquals("pending",$todos[0]->status);
        $this->assertEquals("test todo two",$todos[1]->name);
        $this->assertEquals("pending",$todos[1]->status);
    }


    public function test_updateTodos_shouldCreateTodoWithOrderAsOneMoreThanTheLastOrderAndStatusAsPending()
    {
        $todoOne = $this->user->todos()->create(["name"=>"todo one", "status"=>"pending", "order"=>3]);
        $todoOne->status = "in-progress";

        $todoTwo = $this->user->todos()->create(["name"=>"todo two", "status"=>"in-progress", "order"=>2]);
        $todoTwo->status = "completed";

        $todoThree = $this->user->todos()->create(["name"=>"todo three", "status"=>"completed", "order"=>1]);
        $todoThree->status = "pending";

        $methodResponse = $this->call("POST", "api/agent/update-todos", ["todos"=>[$todoOne->toArray(), $todoTwo->toArray(), $todoThree->toArray()]]);

        $methodResponse->assertStatus(200);

        $todos = $this->user->todos()->orderBy("order", "asc")->get();

        $this->assertEquals("todo one",$todos[0]->name);
        $this->assertEquals("in-progress",$todos[0]->status);
        $this->assertEquals("todo two",$todos[1]->name);
        $this->assertEquals("completed",$todos[1]->status);
        $this->assertEquals("todo three",$todos[2]->name);
        $this->assertEquals("pending",$todos[2]->status);
    }

    public function test_deleteTodo_whenAUserTriesToDeleteAnotherUserTodo_shouldReturn400()
    {
        $todo = $this->user->todos()->create(["name" => "test todo", "is_completed"=> 0]);

        $anotherTodo = factory(User::class)->create()->todos()->create(["name" => "test todo", "is_completed"=> 0]);

        $methodResponse = $this->call("DELETE", "api/agent/todo/$anotherTodo->id");

        $methodResponse->assertStatus(404);

        $this->assertEquals($todo->id, $this->user->todos[0]->id);
    }

    public function test_deleteTodo_whenAUserTriesToDeleteHisTodo_shouldDeleteTheTodo()
    {
        $todo = $this->user->todos()->create(["name" => "test todo", "is_completed"=> 0]);

        $anotherTodo = factory(User::class)->create()->todos()->create(["name" => "test todo", "is_completed"=> 0]);

        $methodResponse = $this->call("DELETE", "api/agent/todo/".$todo->id);

        $methodResponse->assertStatus(200);

        $this->assertCount(0, $this->user->todos);
    }

    public function test_getTodoList_shouldReturnTodoListOfLoggedInUser()
    {
        $todo = $this->user->todos()->create(["name" => "test todo", "is_completed"=> 0]);

        $anotherTodo = factory(User::class)->create()->todos()->create(["name" => "test todo", "is_completed"=> 0]);

        $methodResponse = $this->call("GET", "api/agent/todo-list");

        $methodResponse->assertStatus(200);

        $data = json_decode($methodResponse->getContent())->data->data;

        $this->assertCount(1, $data);

        $this->assertEquals($todo->id, $data[0]->id);
    }

    public function test_getManagerSpecificReportAnalysis_whenTypeIsAgentAnalysis_shouldCallGetManagerSpecificReportForAgentAnalysis()
    {
        $methodResponse = $this->call("GET", "api/agent/dashboard-report/manager/agent-analysis");
        $data = json_decode($methodResponse->getContent())->data;
        $this->assertEquals(Lang::get("report::lang.agents_summary"),$data->title);
        $this->assertEquals("agents_summary",$data->type);
    }

    public function test_getManagerSpecificReportAnalysis_whenTypeIsDepartmentAnalysis_shouldCallGetManagerSpecificReportForDepartmentAnalysis()
    {
        $methodResponse = $this->call("GET", "api/agent/dashboard-report/manager/department-analysis");
        $data = json_decode($methodResponse->getContent())->data;
        $this->assertEquals(Lang::get("report::lang.department_summary"),$data->title);
        $this->assertEquals("department_summary",$data->type);
    }

    public function test_getDefaultTopWidgetTypes_whenLoggedInUserIsAdmin_shouldShowAllReports()
    {
        $this->getLoggedInUserForWeb('admin');
        $this->assertCount(8, $this->getPrivateMethod($this->classObject, 'getDefaultTopWidgetTypes'));
    }

    public function test_getDefaultTopWidgetTypes_whenLoggedInUserIsAgentWithGlobalAccess_shouldShowAllReports()
    {
        $this->getLoggedInUserForWeb('agent');
        $this->createPermissionForLoggedInUser(['global_access']);
        $this->assertCount(8, $this->getPrivateMethod($this->classObject, 'getDefaultTopWidgetTypes'));
    }

    public function test_getDefaultTopWidgetTypes_whenLoggedInUserIsAgentWithNeitherGlobalNorRestrictedAccess_shouldShowAllReports()
    {
        $this->getLoggedInUserForWeb('agent');
        $this->assertCount(8, $this->getPrivateMethod($this->classObject, 'getDefaultTopWidgetTypes'));
    }

    public function test_getDefaultTopWidgetTypes_whenLoggedInUserIsAgentWithRestrictedAccess_shouldShowAllReports()
    {
        $this->getLoggedInUserForWeb('agent');
        $this->createPermissionForLoggedInUser(['restricted_access']);
        $this->assertCount(4, $this->getPrivateMethod($this->classObject, 'getDefaultTopWidgetTypes'));
    }

    public function test_getFiltersByWidgetType_whenWidgetTypeIsInvalid_ThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->setPrivateProperty($this->classObject, 'user', $this->user);
        $this->getPrivateMethod($this->classObject, 'getFiltersByWidgetType', ['invalid widget type']);
    }

    public function test_getRedirectLink_whenTypeIsMyOverdue_callsInboxUrlMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->setPrivateProperty($mock, 'user', $this->user);
        $mock->shouldReceive('getInboxFilterUrl')->once()->withArgs(['', ["assignee-ids"=> [$this->user->id], "category" => "overdue"]])->andReturn('');
        $this->getPrivateMethod($mock, 'getRedirectLink', ['my_overdue_tickets']);
    }

    public function test_getRedirectLink_whenTypeIsMyDueTodayTickets_callsInboxUrlMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->setPrivateProperty($mock, 'user', $this->user);
        $mock->shouldReceive('getInboxFilterUrl')->once()->withArgs(['', ["assignee-ids"=> [$this->user->id], "due-on" => "next::1~day"]])->andReturn('');
        $this->getPrivateMethod($mock, 'getRedirectLink', ['my_due_today_tickets']);
    }

    public function test_getRedirectLink_whenTypeIsMyPendingApproval_callsInboxUrlMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getInboxFilterUrl')->once()->withArgs(['', ["category" => "waiting-for-approval"]])->andReturn('');
        $this->getPrivateMethod($mock, 'getRedirectLink', ['my_pending_approvals']);
    }

    public function test_getRedirectLink_whenTypeIsOpenTickets_callsInboxUrlMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getInboxFilterUrl')->once()->withArgs(['', ["category" => "inbox"]])->andReturn('');
        $this->getPrivateMethod($mock, 'getRedirectLink', ['open_tickets']);
    }

    public function test_getRedirectLink_whenTypeIsUnassignedTickets_callsInboxUrlMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getInboxFilterUrl')->once()->withArgs(['', ["category" => "unassigned"]])->andReturn('');
        $this->getPrivateMethod($mock, 'getRedirectLink', ['unassigned_tickets']);
    }

    public function test_getRedirectLink_whenTypeIsOverDueTickets_callsInboxUrlMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getInboxFilterUrl')->once()->withArgs(['', ["category" => "overdue"]])->andReturn('');
        $this->getPrivateMethod($mock, 'getRedirectLink', ['overdue_tickets']);
    }

    public function test_getRedirectLink_whenTypeIsMyTickets_callsInboxUrlMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getInboxFilterUrl')->once()->withArgs(['', ["category" => "mytickets"]])->andReturn('');
        $this->getPrivateMethod($mock, 'getRedirectLink', ['my_tickets']);
    }

    public function test_getRedirectLink_whenTypeIsUnansweredTickets_callsInboxUrlMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getInboxFilterUrl')->once()->withArgs(['', ["category" => "inbox", "answered"=>0]])->andReturn('');
        $this->getPrivateMethod($mock, 'getRedirectLink', ['unanswered_tickets']);
    }

    public function test_getRedirectLink_whenTypeIsFilter_returnsFilterUrl()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getRedirectLink', ['filter', 1]);
        $this->assertStringContainsStringIgnoringCase('tickets/filter/1', $methodResponse);
    }

    public function test_getBaseQueryForWidget_whenTypeIsMyOverdue_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->setPrivateProperty($mock, 'user', $this->user);
        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["assignee-ids"=> [$this->user->id], "category" => "overdue"] && $meta == false;
            })->andReturn(Tickets::query());
        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['my_overdue_tickets']);
    }

    public function test_getBaseQueryForWidget_whenTypeIsMyDueTodayTickets_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->setPrivateProperty($mock, 'user', $this->user);
        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["assignee-ids"=> [$this->user->id], "due-on" => "next::1~day"] && $meta == false;
            })->andReturn(Tickets::query());
        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['my_due_today_tickets']);
    }

    public function test_getBaseQueryForWidget_whenTypeIsMyPendingApprovals_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->setPrivateProperty($mock, 'user', $this->user);
        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["category" => "waiting-for-approval"] && $meta == false;
            })->andReturn(Tickets::query());
        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['my_pending_approvals']);
    }

    public function test_getBaseQueryForWidget_whenTypeIsOpenTickets_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["category" => "inbox"] && $meta == false;
            })->andReturn(Tickets::query());
        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['open_tickets']);
    }

    public function test_getBaseQueryForWidget_whenTypeIsUnassignedTickets_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["category" => "unassigned"] && $meta == false;
            })->andReturn(Tickets::query());
        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['unassigned_tickets']);
    }

    public function test_getBaseQueryForWidget_whenTypeIsOverdueTickets_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["category" => "overdue"] && $meta == false;
            })->andReturn(Tickets::query());
        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['overdue_tickets']);
    }

    public function test_getBaseQueryForWidget_whenTypeIsMyTickets_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["category" => "mytickets"] && $meta == false;
            })->andReturn(Tickets::query());
        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['my_tickets']);
    }

    public function test_getBaseQueryForWidget_whenTypeIsUnansweredTickets_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["category" => "inbox", "answered"=>0] && $meta == false;
            })->andReturn(Tickets::query());
        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['unanswered_tickets']);
    }

    public function test_getBaseQueryForWidget_whenTypeIsFilter_callsGetBaseQueryForTicketsMethodWithRequiredArguments()
    {
        $mock = \Mockery::mock(DashboardController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $ticketFilter = TicketFilter::create(["user_id"=>1, "name"=>"Test Filter", "display_on_dashboard"=>1]);
        $ticketFilter->filterMeta()->create(["key"=>"category", "value"=>"all"])->id;

        $mock->shouldReceive('getBaseQueryForTickets')->once()
            ->withArgs(function($request, $meta){
                return $request->all() == ["category"=>'all'] && $meta == false;
            })->andReturn(Tickets::query());

        $this->getPrivateMethod($mock, 'getBaseQueryForWidget', ['filter', $ticketFilter->id]);
    }
}