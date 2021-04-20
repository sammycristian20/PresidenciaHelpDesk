<?php


namespace App\FaveoReport\Tests\Backend\Controllers;

use App\FaveoLog\Model\ExceptionLog;
use App\FaveoLog\Model\LogCategory;
use App\FaveoLog\Model\MailLog;
use App\FaveoReport\Controllers\DailyReport;
use App\Model\helpdesk\Ticket\Tickets;
use Carbon\Carbon;
use Illuminate\Validation\UnauthorizedException;
use Lang;
use Mockery;
use Tests\DBTestCase;
use App\User;

class DailyReportTest extends DBTestCase
{

    private $classObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->getLoggedInUserForWeb("admin");
        $this->assignAgentToDepartment($this->user, [1]);
        $this->classObject = new DailyReport();
        $this->blockTicketEvents();
    }

    private function createTicket($ticketParams, $threadParams)
    {
        $ticket = factory(Tickets::class)->create($ticketParams);
        $ticket->thread()->create(array_merge($threadParams, ["poster"=> "client", "is_internal"=> 0]));
        return $ticket;
    }

    public function test_getRequiresImmediateActionTickets_whenMultipleTicketsArePresent_shouldSortThemInOrderOfTheirCriticality()
    {
        /*
         * It sorts them in a way that tickets which are reopened, overdue and assigned to
         * current agent gets the first priority, ticket which is reopened and overdue gets the second priority,
         * tickets which are reopened gets the 3rd priority and tickets which are overdue gets the last priority
         */
        // will not come in the list
        $this->createTicket(["reopened"=>0, "assigned_to"=> null, "status"=>3, "dept_id"=> 1], ["title"=>"will not come in the list"]);
        $this->createTicket(["reopened"=>0, "assigned_to"=> null, "status"=>1, "dept_id"=> 1], ["title"=>"will not come in the list"]);


        // ticket just reopened
        $this->createTicket(["reopened"=>1, "assigned_to"=> null, "duedate"=>Carbon::now()->addYear(), "status"=>1, "dept_id"=> 1], ["title"=>"ticket just reopened"]);

        // ticket reopened and overdue
        $this->createTicket(["reopened"=>1, "assigned_to" => 1, "duedate"=>Carbon::now()->subYear(), "status"=>1, "dept_id"=> 1], ["title"=>"ticket reopened and overdue"]);

        // tickets assigned to logged in agent, reopened and overdue
        $this->createTicket(["reopened"=>1, "assigned_to" => $this->user->id, "duedate"=>Carbon::now()->subYear(), "status"=>1, "dept_id"=> 1], ["title"=>"tickets assigned to logged in agent, reopened and overdue"]);

        // ticket just overdue
        $this->createTicket(["reopened"=>0, "assigned_to"=> null, "duedate"=>Carbon::now()->subYear(), "status"=>1, "dept_id"=> 1], ["title"=>"ticket just overdue"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getRequireImmediateActionTickets", [$this->user]);

        $this->assertEquals(4, $methodResponse->total);
        $this->assertStringContainsStringIgnoringCase("tickets assigned to logged in agent, reopened and overdue", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase("ticket reopened and overdue", $methodResponse->data[1]->title);
        $this->assertStringContainsStringIgnoringCase("ticket just reopened", $methodResponse->data[2]->title);
        $this->assertStringContainsStringIgnoringCase("ticket just overdue", $methodResponse->data[3]->title);
    }

    public function test_getDueIn24HoursTickets_whenTicketsWhoseDueDatesAreMoreThan24HoursArePresent_shouldGiveTicketsWhichHasDueDateInNext24Hours()
    {
        $this->createTicket(["assigned_to"=> null, "status"=>3, "dept_id"=> 1], ["title"=>"ticket one"]);
        $this->createTicket(["assigned_to"=> $this->user->id, "status"=>1, "dept_id"=> 1, "duedate"=>Carbon::now()->addHours(20)], ["title"=>"ticket two"]);
        $this->createTicket(["assigned_to"=> $this->user->id, "status"=>1, "dept_id"=> 1, "duedate"=>Carbon::now()->addHours(10)], ["title"=>"ticket three"]);
        $this->createTicket(["assigned_to" => $this->user->id, "status"=>1, "dept_id"=> 1, "duedate"=>Carbon::now()->addHours(25)], ["title"=>"ticket four"]);
        $this->createTicket(["assigned_to" => $this->user->id, "status"=>1, "dept_id"=> 1, "duedate"=>Carbon::now()->subHours(10)], ["title"=>"ticket five"]);
        $this->createTicket(["assigned_to" => 1, "status"=>1, "dept_id"=> 1, "duedate"=>Carbon::now()->addHours(25)], ["title"=>"ticket six"]);
        $methodResponse = $this->getPrivateMethod($this->classObject, "getDueIn24HoursTickets", [$this->user]);
        $this->assertEquals(2, $methodResponse->total);
        $this->assertStringContainsStringIgnoringCase("ticket three", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase("ticket two", $methodResponse->data[1]->title);
    }

    public function test_getResolvedIn24HoursTickets_whenTicketsWithDifferentDueDatesArePresent_shouldGiveTicketsWhichWereResolvedBefore24Hours()
    {
        $this->createTicket(["assigned_to"=> null, "closed"=>0, "dept_id"=> 1], ["title"=>"ticket one"]);
        $this->createTicket(["assigned_to"=> $this->user->id, "closed"=>1, "dept_id"=> 1, "closed_at"=>Carbon::now()->subHours(20)], ["title"=>"ticket two"]);
        $this->createTicket(["assigned_to"=> $this->user->id, "closed"=>1, "dept_id"=> 1, "closed_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket three"]);
        $this->createTicket(["assigned_to" => $this->user->id, "closed"=>1, "dept_id"=> 1, "closed_at"=>Carbon::now()->subHours(25)], ["title"=>"ticket four"]);
        $this->createTicket(["assigned_to" => $this->user->id, "closed"=>0, "dept_id"=> 1, "closed_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket five"]);
        $this->createTicket(["assigned_to" => 1, "closed"=>1, "dept_id"=> 1, "closed_at"=>Carbon::now()->subHours(25)], ["title"=>"ticket six"]);
        $methodResponse = $this->getPrivateMethod($this->classObject, "getResolvedIn24HoursTickets", [$this->user]);
        $this->assertEquals(2, $methodResponse->total);
        $this->assertStringContainsStringIgnoringCase("ticket two", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase("ticket three", $methodResponse->data[1]->title);
    }

    public function test_getAgentsAnalysis_whenReopenedTicketsArePresent_shouldGiveListOfAgentsWithReopenedTicketExceptTheManagerInHighToLowOrder()
    {
        // create 2 agents and assign both to department A
        // now, create another agent add him to department A and make him team manager
        $agentOne = factory(User::class)->create(["role"=>"admin"]);
        $this->assignAgentToDepartment($agentOne, [1]);

        $agentTwo = factory(User::class)->create(["role"=>"admin"]);
        $this->assignAgentToDepartment($agentTwo, [1]);

        $this->user->managerOfDepartments()->sync([1]);

        // agentOne
        $this->createTicket(["assigned_to"=> $agentOne->id, "reopened"=>1, "dept_id"=> 1, "status"=>1], ["title"=>"ticket one"]);
        $this->createTicket(["assigned_to"=> $agentOne->id, "reopened"=>1, "dept_id"=> 2, "status"=>1], ["title"=>"ticket two"]);
        $this->createTicket(["assigned_to"=> $agentOne->id, "reopened"=>0, "dept_id"=> 1, "status"=>1], ["title"=>"ticket three"]);

        // agentTwo
        $this->createTicket(["assigned_to"=> $agentTwo->id, "reopened"=>1, "dept_id"=> 1, "status"=>1], ["title"=>"ticket four"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "reopened"=>1, "dept_id"=> 1, "status"=>1], ["title"=>"ticket five"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "reopened"=>1, "dept_id"=> 2, "status"=>1], ["title"=>"ticket six"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "reopened"=>0, "dept_id"=> 1, "status"=>1], ["title"=>"ticket seven"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getAgentsAnalysis",
            [[$agentOne->id, $agentTwo->id], [1], $this->user->id]);

        $this->assertEquals(2, $methodResponse->total);

        $this->assertStringContainsStringIgnoringCase($agentTwo->full_name, $methodResponse->data[0]->title);
        $this->assertEquals(Lang::get("report::lang.reopened_tickets"), $methodResponse->data[0]->attributes[0]->key);
        $this->assertEquals(2, strip_tags($methodResponse->data[0]->attributes[0]->value));

        $this->assertStringContainsStringIgnoringCase($agentOne->full_name, $methodResponse->data[1]->title);
        $this->assertEquals(Lang::get("report::lang.reopened_tickets"), $methodResponse->data[1]->attributes[0]->key);
        $this->assertEquals(1, strip_tags($methodResponse->data[1]->attributes[0]->value));
    }

    public function test_getAgentsAnalysis_whenOverdueTicketsArePresent_shouldGiveListOfAgentsWithOverDueTicketExceptTheManagerInHighToLowOrder()
    {
        // create 2 agents and assign both to department A
        // now, create another agent add him to department A and make him team manager
        $agentOne = factory(User::class)->create(["role"=>"admin"]);
        $this->assignAgentToDepartment($agentOne, [1]);

        $agentTwo = factory(User::class)->create(["role"=>"admin"]);
        $this->assignAgentToDepartment($agentTwo, [1]);

        $this->user->managerOfDepartments()->sync([1]);

        // agentOne
        $this->createTicket(["assigned_to"=> $agentOne->id, "duedate"=> Carbon::now()->subHours(3), "dept_id"=> 1, "status"=>1], ["title"=>"ticket one"]);
        $this->createTicket(["assigned_to"=> $agentOne->id, "duedate"=> Carbon::now()->subHours(3), "dept_id"=> 1, "status"=>1], ["title"=>"ticket one"]);
        $this->createTicket(["assigned_to"=> $agentOne->id, "duedate"=> Carbon::now()->subHours(3), "dept_id"=> 1, "status"=>1], ["title"=>"ticket one"]);
        $this->createTicket(["assigned_to"=> $agentOne->id, "duedate"=> Carbon::now()->subHours(4), "dept_id"=> 2, "status"=>3], ["title"=>"ticket two"]);
        $this->createTicket(["assigned_to"=> $agentOne->id, "duedate"=> Carbon::now()->subHours(5), "dept_id"=> 1, "status"=>4], ["title"=>"ticket three"]);

        // agentTwo
        $this->createTicket(["assigned_to"=> $agentTwo->id, "duedate"=> Carbon::now()->subHours(4), "dept_id"=> 1, "status"=>1], ["title"=>"ticket four"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "duedate"=> Carbon::now()->subHours(5), "dept_id"=> 1, "status"=>1], ["title"=>"ticket five"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "duedate"=> Carbon::now()->subHours(6), "dept_id"=> 2, "status"=>4], ["title"=>"ticket six"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "duedate"=> Carbon::now()->subHours(7), "dept_id"=> 1, "status"=>5], ["title"=>"ticket seven"]);


        $methodResponse = $this->getPrivateMethod($this->classObject, "getAgentsAnalysis",
            [[$agentOne->id, $agentTwo->id], [1], $this->user->id]);

        $this->assertEquals(2, $methodResponse->total);

        $this->assertStringContainsStringIgnoringCase($agentOne->full_name, $methodResponse->data[0]->title);
        $this->assertEquals(Lang::get("report::lang.overdue_tickets"), $methodResponse->data[0]->attributes[1]->key);
        $this->assertEquals(3, strip_tags($methodResponse->data[0]->attributes[1]->value));

        $this->assertStringContainsStringIgnoringCase($agentTwo->full_name, $methodResponse->data[1]->title);
        $this->assertEquals(Lang::get("report::lang.overdue_tickets"), $methodResponse->data[1]->attributes[1]->key);
        $this->assertEquals(2, strip_tags($methodResponse->data[1]->attributes[1]->value));
    }

    public function test_getAgentsAnalysis_whenAssignedTicketsArePresent_shouldGiveListOfAgentsWithAssignedCountOfOpenTickets()
    {
        // create 2 agents and assign both to department A
        // now, create another agent add him to department A and make him team manager
        $agentOne = factory(User::class)->create(["role"=>"admin"]);
        $this->assignAgentToDepartment($agentOne, [1]);

        $agentTwo = factory(User::class)->create(["role"=>"admin"]);
        $this->assignAgentToDepartment($agentTwo, [1]);

        $this->user->managerOfDepartments()->sync([1]);

        // agentOne
        $this->createTicket(["assigned_to"=> $agentOne->id, "dept_id"=> 1, "status"=>1], ["title"=>"ticket one"]);
        $this->createTicket(["assigned_to"=> $agentOne->id, "dept_id"=> 1, "status"=>1], ["title"=>"ticket two"]);
        $this->createTicket(["assigned_to"=> $agentOne->id, "duedate"=> Carbon::now()->subHours(5), "dept_id"=> 1, "status"=> 3], ["title"=>"ticket three"]);

        // agentTwo
        $this->createTicket(["assigned_to"=> $agentTwo->id, "dept_id"=> 1, "status"=>1], ["title"=>"ticket four"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "dept_id"=> 1, "status"=>1], ["title"=>"ticket five"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "dept_id"=> 2, "status"=>1], ["title"=>"ticket six"]);
        $this->createTicket(["assigned_to"=> $agentTwo->id, "dept_id"=> 1, "status"=>1], ["title"=>"ticket seven"]);


        $methodResponse = $this->getPrivateMethod($this->classObject, "getAgentsAnalysis",
            [[$agentOne->id, $agentTwo->id], [1], $this->user->id]);

        $this->assertEquals(2, $methodResponse->total);

        $this->assertStringContainsStringIgnoringCase($agentTwo->full_name, $methodResponse->data[0]->title);
        $this->assertEquals(Lang::get("report::lang.assigned_open_tickets"), $methodResponse->data[0]->attributes[2]->key);
        $this->assertEquals(3, strip_tags($methodResponse->data[0]->attributes[2]->value));

        $this->assertStringContainsStringIgnoringCase($agentOne->full_name, $methodResponse->data[1]->title);
        $this->assertEquals(Lang::get("report::lang.assigned_open_tickets"), $methodResponse->data[1]->attributes[2]->key);
        $this->assertEquals(2, strip_tags($methodResponse->data[1]->attributes[2]->value));
    }

    public function test_getDepartmentAnalysis_whenCreatedTicketsArePresent_shouldGiveCountOfTicketsWhichAreLessThan24HoursOld()
    {
        // put a manager in 2 departments
        $this->assignAgentToDepartment($this->user, [1, 2]);
        $this->user->managerOfDepartments()->sync([1, 2]);

        $this->createTicket(["dept_id"=> 1, "status"=>1, "created_at"=>Carbon::now()->subHours(26)], ["title"=>"ticket one"]);
        $this->createTicket(["dept_id"=> 1, "status"=>1, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket two"]);

        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(26)], ["title"=>"ticket three"]);
        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket four"]);
        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket five"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getDepartmentAnalysis", [[1, 2], $this->user->id]);
        $this->assertEquals(2, $methodResponse->total);

        $this->assertStringContainsStringIgnoringCase("Sales", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.created_tickets_in_last_24_hours"), $methodResponse->data[0]->attributes[0]->key);
        $this->assertStringContainsStringIgnoringCase(2, $methodResponse->data[0]->attributes[0]->value);

        $this->assertStringContainsStringIgnoringCase("Support", $methodResponse->data[1]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.created_tickets_in_last_24_hours"), $methodResponse->data[1]->attributes[0]->key);
        $this->assertStringContainsStringIgnoringCase(1, $methodResponse->data[1]->attributes[0]->value);
    }

    public function test_getDepartmentAnalysis_whenOpenAndCloseTicketsArePresent_shouldGiveCountOfOpenTicketsInOpenKey()
    {
        // put a manager in 2 departments
        $this->assignAgentToDepartment($this->user, [1, 2]);

        $this->user->managerOfDepartments()->sync([1, 2]);

        $this->createTicket(["dept_id"=> 1, "status"=>1, "created_at"=>Carbon::now()->subHours(26)], ["title"=>"ticket one"]);
        $this->createTicket(["dept_id"=> 1, "status"=>2, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket two"]);

        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(26)], ["title"=>"ticket three"]);
        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket four"]);
        $this->createTicket(["dept_id"=> 2, "status"=>2, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket five"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getDepartmentAnalysis", [[1, 2], $this->user->id]);

        $this->assertEquals(2, $methodResponse->total);
        $this->assertStringContainsStringIgnoringCase("Sales", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.open_tickets"), $methodResponse->data[0]->attributes[1]->key);
        $this->assertStringContainsStringIgnoringCase(2, $methodResponse->data[0]->attributes[1]->value);

        $this->assertStringContainsStringIgnoringCase("Support", $methodResponse->data[1]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.open_tickets"), $methodResponse->data[1]->attributes[1]->key);
        $this->assertStringContainsStringIgnoringCase(1, $methodResponse->data[1]->attributes[1]->value);
    }

    public function test_getDepartmentAnalysis_whenUnapprovedTicketsArePresent_shouldGiveCountOfUnapprovedTicketsInUnapprovedKey()
    {
        // put a manager in 2 departments
        $this->assignAgentToDepartment($this->user, [1, 2]);

        $this->user->managerOfDepartments()->sync([1, 2]);

        // 0 overdue open tickets
        $this->createTicket(["dept_id"=> 1, "status"=>7, "created_at"=>Carbon::now()->subHours(26)], ["title"=>"ticket one"]);
        $this->createTicket(["dept_id"=> 1, "status"=>2, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket two"]);

        // 1 overdue open tickets
        $this->createTicket(["dept_id"=> 2, "status"=>7, "created_at"=>Carbon::now()->subHours(26)], ["title"=>"ticket three"]);
        $this->createTicket(["dept_id"=> 2, "status"=>7, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket four"]);
        $this->createTicket(["dept_id"=> 2, "status"=>2, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket five"]);
        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket six"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getDepartmentAnalysis", [[1, 2], $this->user->id]);

        $this->assertEquals(2, $methodResponse->total);

        $this->assertStringContainsStringIgnoringCase("Sales", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.unapproved_tickets"), $methodResponse->data[0]->attributes[2]->key);
        $this->assertStringContainsStringIgnoringCase(2, $methodResponse->data[0]->attributes[2]->value);

        $this->assertStringContainsStringIgnoringCase("Support", $methodResponse->data[1]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.unapproved_tickets"), $methodResponse->data[1]->attributes[2]->key);
        $this->assertStringContainsStringIgnoringCase(1, $methodResponse->data[1]->attributes[2]->value);
    }

    public function test_getDepartmentAnalysis_whenOverdueTicketsArePresent_shouldGiveCountOfOverdueTicketsInOverdueKey()
    {
        // put a manager in 2 departments
        $this->assignAgentToDepartment($this->user, [1, 2]);

        $this->user->managerOfDepartments()->sync([1, 2]);

        $this->createTicket(["dept_id"=> 1, "status"=>7, "created_at"=>Carbon::now()->subHours(26), "duedate"=> Carbon::now()->subHours(2)], ["title"=>"ticket one"]);
        $this->createTicket(["dept_id"=> 1, "status"=>2, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket two"]);

        $this->createTicket(["dept_id"=> 2, "status"=>7, "created_at"=>Carbon::now()->subHours(26), "duedate"=>Carbon::now()->subHours(2)], ["title"=>"ticket three"]);
        $this->createTicket(["dept_id"=> 2, "status"=>7, "created_at"=>Carbon::now()->subHours(10), "duedate"=>Carbon::now()->subHours(5), "closed"=>1], ["title"=>"ticket four"]);
        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(10), "duedate"=>Carbon::now()->subHours(10)], ["title"=>"ticket five"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getDepartmentAnalysis", [[1, 2], $this->user->id]);

        $this->assertEquals(2, $methodResponse->total);
        $this->assertStringContainsStringIgnoringCase("Sales", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.overdue_tickets"), $methodResponse->data[0]->attributes[3]->key);
        $this->assertStringContainsStringIgnoringCase(2, $methodResponse->data[0]->attributes[3]->value);

        $this->assertStringContainsStringIgnoringCase("Support", $methodResponse->data[1]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.overdue_tickets"), $methodResponse->data[1]->attributes[3]->key);
        $this->assertStringContainsStringIgnoringCase(1, $methodResponse->data[1]->attributes[3]->value);
    }

    public function test_getDepartmentAnalysis_whenUnassignedTicketsArePresent_shouldGiveCountOfUnassignedTicketsInUnassignedKey()
    {
        // put a manager in 2 departments
        $this->assignAgentToDepartment($this->user, [1, 2]);

        $this->user->managerOfDepartments()->sync([1, 2]);

        $this->createTicket(["dept_id"=> 1, "status"=>1, "created_at"=>Carbon::now()->subHours(26), "duedate"=> Carbon::now()->subHours(2)], ["title"=>"ticket one"]);
        $this->createTicket(["dept_id"=> 1, "status"=>1, "created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket two"]);

        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(26), "duedate"=>Carbon::now()->subHours(2)], ["title"=>"ticket three"]);
        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(10), "duedate"=>Carbon::now()->subHours(5), "closed"=>1], ["title"=>"ticket four"]);
        $this->createTicket(["dept_id"=> 2, "status"=>1, "created_at"=>Carbon::now()->subHours(10), "duedate"=>Carbon::now()->subHours(10)], ["title"=>"ticket five"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getDepartmentAnalysis", [[1, 2], $this->user->id]);

        $this->assertEquals(2, $methodResponse->total);
        $this->assertStringContainsStringIgnoringCase("Sales", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.unassigned_tickets"), $methodResponse->data[0]->attributes[4]->key);
        $this->assertStringContainsStringIgnoringCase(3, $methodResponse->data[0]->attributes[4]->value);

        $this->assertStringContainsStringIgnoringCase("Support", $methodResponse->data[1]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.unassigned_tickets"), $methodResponse->data[1]->attributes[4]->key);
        $this->assertStringContainsStringIgnoringCase(2, $methodResponse->data[1]->attributes[4]->value);
    }

    public function test_getDepartmentAnalysis_whenReopenedTicketsArePresent_shouldGiveCountOfReopenedTicketsWhichWereReopenedAndStillOpened()
    {
        // put a manager in 2 departments
        $this->assignAgentToDepartment($this->user, [1, 2]);

        $this->user->managerOfDepartments()->sync([1, 2]);

        $this->createTicket(["dept_id"=> 1, "reopened"=>1, "status"=>1,"reopened_at"=> Carbon::now(), "duedate"=> Carbon::now()->addHours(10)], ["title"=>"ticket one"]);
        $this->createTicket(["dept_id"=> 1, "reopened"=>1, "status"=>3,"reopened_at"=> Carbon::now()], ["title"=>"ticket two"]);

        $this->createTicket(["dept_id"=> 2, "reopened"=>1, "status"=>1,"reopened_at"=>Carbon::now(), "duedate"=> Carbon::now()->addHours(10)], ["title"=>"ticket three"]);
        $this->createTicket(["dept_id"=> 2, "reopened"=>1, "status"=>1,"reopened_at"=>Carbon::now(), "duedate"=> Carbon::now()->addHours(10)], ["title"=>"ticket four"]);
        $this->createTicket(["dept_id"=> 2, "reopened"=>1, "status"=>3,"reopened_at"=>Carbon::now()], ["title"=>"ticket five"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getDepartmentAnalysis", [[1, 2], $this->user->id]);

        $this->assertEquals(2, $methodResponse->total);

        $this->assertStringContainsStringIgnoringCase("Sales", $methodResponse->data[0]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.reopened_tickets"), $methodResponse->data[0]->attributes[5]->key);
        $this->assertStringContainsStringIgnoringCase(2, $methodResponse->data[0]->attributes[5]->value);

        $this->assertStringContainsStringIgnoringCase("Support", $methodResponse->data[1]->title);
        $this->assertStringContainsStringIgnoringCase(Lang::get("report::lang.reopened_tickets"), $methodResponse->data[1]->attributes[5]->key);
        $this->assertStringContainsStringIgnoringCase(1, $methodResponse->data[1]->attributes[5]->value);
    }

    public function test_getSystemAnalysis_whenTicketsWerePresent_shouldShowTicketCountOnlyForLast24Hours()
    {
        $this->createTicket(["created_at"=>Carbon::now()->subHours(26)], ["title"=>"ticket one"]);
        $this->createTicket(["created_at"=>Carbon::now()->subHours(10)], ["title"=>"ticket one"]);
        $this->createTicket(["created_at"=>Carbon::now()->subHours(8)], ["title"=>"ticket one"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getSystemAnalysis");
        $this->assertEquals(Lang::get("report::lang.received_tickets"), $methodResponse->data[0]->title);
        $this->assertEquals(2, $methodResponse->data[0]->attributes[0]->value);
    }

    public function test_getSystemAnalysis_whenUsersWerePresent_shouldShowUsersCountWhichWereCreatedInLast24hours()
    {
        // deleting all users, so that correct user calculation could be made
        User::where("id", ">", 0)->delete();

        factory(User::class)->create(["created_at" => Carbon::now()->subHours(26)]);
        factory(User::class)->create(["created_at" => Carbon::now()->subHours(30)]);
        factory(User::class)->create(["created_at" => Carbon::now()->subHours(10)]);
        factory(User::class)->create(["created_at" => Carbon::now()->subHours(20)]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getSystemAnalysis");

        $this->assertEquals(Lang::get("report::lang.users_created"), $methodResponse->data[1]->title);
        $this->assertEquals(2, $methodResponse->data[1]->attributes[0]->value);
    }

    public function test_getSystemAnalysis_whenMailsWereReceivedInTheSystem_shouldShowCountOfMailsReceivedInLast24hours()
    {
        $logCategoryId = LogCategory::where("name", "mail-fetch")->value("id");

        $this->createMailLog($logCategoryId, Carbon::now()->subHours(26));
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(10));
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(12));

        $methodResponse = $this->getPrivateMethod($this->classObject, "getSystemAnalysis");
        $this->assertEquals(Lang::get("report::lang.mails_received"), $methodResponse->data[2]->title);
        $this->assertEquals(2, $methodResponse->data[2]->attributes[0]->value);
    }

    public function test_getSystemAnalysis_whenMailsWereSentFromTheSystem_shouldShowCountOfMailsSentSuccessfullyInLast24hours()
    {
        $logCategoryId = LogCategory::where("name", "mail-send")->value("id");

        $this->createMailLog($logCategoryId, Carbon::now()->subHours(26), "sent");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(10), "sent");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(12), "sent");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(12), "queued");

        $methodResponse = $this->getPrivateMethod($this->classObject, "getSystemAnalysis");

        $this->assertEquals(Lang::get("report::lang.mails_sent"), $methodResponse->data[3]->title);
        $this->assertEquals(2, $methodResponse->data[3]->attributes[0]->value);
    }

    public function test_getSystemAnalysis_whenMailsWereQueuedTheSystem_shouldShowCountOfMailsQueuedInLast24hours()
    {
        $logCategoryId = LogCategory::where("name", "mail-send")->value("id");

        $this->createMailLog($logCategoryId, Carbon::now()->subHours(26), "sent");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(10), "sent");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(12), "queued");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(12), "queued");

        $methodResponse = $this->getPrivateMethod($this->classObject, "getSystemAnalysis");

        $this->assertEquals(Lang::get("report::lang.mails_queued"), $methodResponse->data[4]->title);
        $this->assertEquals(2, $methodResponse->data[4]->attributes[0]->value);
    }

    public function test_getSystemAnalysis_whenMailsWereFailedTheSystem_shouldShowCountOfMailsFailedInLast24hours()
    {
        $logCategoryId = LogCategory::where("name", "mail-send")->value("id");

        $this->createMailLog($logCategoryId, Carbon::now()->subHours(26), "failed");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(10), "failed");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(12), "failed");
        $this->createMailLog($logCategoryId, Carbon::now()->subHours(12), "queued");

        $methodResponse = $this->getPrivateMethod($this->classObject, "getSystemAnalysis");

        $this->assertEquals(Lang::get("report::lang.mails_failed"), $methodResponse->data[5]->title);
        $this->assertEquals(2, $methodResponse->data[5]->attributes[0]->value);
    }

    public function test_getSystemAnalysis_whenExceptionsWereCaughtInTheSystem_shouldShowCountOfExceptionsInLast24hours()
    {
        $logCategoryId = LogCategory::where("name", "mail-send")->value("id");

        $log = ExceptionLog::create(["log_category_id"=> $logCategoryId]);
        $log->created_at = Carbon::now()->subHours(26);
        $log->save(['timestamps' => false]);

        $log = ExceptionLog::create(["log_category_id"=> $logCategoryId]);
        $log->created_at = Carbon::now()->subHours(23);
        $log->save(['timestamps' => false]);

        $log = ExceptionLog::create(["log_category_id"=> $logCategoryId]);
        $log->created_at = Carbon::now()->subHours(10);
        $log->save(['timestamps' => false]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getSystemAnalysis");

        $this->assertEquals(Lang::get("report::lang.exceptions_caught"), $methodResponse->data[6]->title);
        $this->assertEquals(2, $methodResponse->data[6]->attributes[0]->value);
    }

    private function createMailLog($logCategoryId, $createdAt, $status = "")
    {
        $log = MailLog::create(["log_category_id"=> $logCategoryId]);
        $log->created_at = $createdAt;

        if($status){
            $log->status = $status;
        }

        $log->save(['timestamps' => false]);

    }

    public function test_getTicketRedirectLinkByType_whenTypeIsCreatedWithInLast24Hrs_shouldGiveCorrespondingFilter()
    {
        Carbon::setTestNow(Carbon::create(2020, 8, 10, 10));
        $beginningOfDate = Carbon::create(2020, 8, 9, 10)->toDateTimeString();
        $endOfDate = Carbon::create(2020, 8, 10, 10)->toDateTimeString();
        $this->setPrivateProperty($this->classObject, "agentTimezone", "UTC");
        $methodResponse = $this->getPrivateMethod($this->classObject, "getTicketRedirectLinkByType", ["created_tickets_in_last_24_hours", ["test-param"=> "one"], "test"]);
        $string = "date::$beginningOfDate~$endOfDate";
        $this->assertStringContainsStringIgnoringCase(http_build_query(["created-at"=>$string, "test-param"=> "one"]), $methodResponse);
        $this->assertStringContainsStringIgnoringCase("test", $methodResponse);
    }

    public function test_getTicketRedirectLinkByType_whenTypeIsOpenTickets_shouldGiveCorrespondingFilter()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, "getTicketRedirectLinkByType", ["open_tickets", ["test-param"=> "one"], "test"]);
        $this->assertStringContainsStringIgnoringCase(http_build_query(["status-ids"=>[1], "test-param"=> "one"]), $methodResponse);
    }

    public function test_getTicketRedirectLinkByType_whenTypeIsUnapprovedTickets_shouldGiveCorrespondingFilter()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, "getTicketRedirectLinkByType", ["unapproved_tickets", ["test-param"=> "one"], "test"]);
        $this->assertStringContainsStringIgnoringCase(http_build_query(["category"=>"unapproved", "test-param"=> "one"]), $methodResponse);
    }

    public function test_getTicketRedirectLinkByType_whenTypeIsUnassignedTickets_shouldGiveCorrespondingFilter()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, "getTicketRedirectLinkByType", ["unassigned_tickets", ["test-param"=> "one"], "test"]);
        $this->assertStringContainsStringIgnoringCase(http_build_query(["category"=>"unassigned", "test-param"=> "one"]), $methodResponse);
    }

    public function test_getTicketRedirectLinkByType_whenTypeIsReopenedTickets_shouldGiveCorrespondingFilter()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, "getTicketRedirectLinkByType", ["reopened_tickets", ["test-param"=> "one"], "test"]);
        $this->assertStringContainsStringIgnoringCase(http_build_query(["reopened"=>1, "test-param"=> "one"]), $methodResponse);
    }

    public function test_getTicketRedirectLinkByType_whenTypeIsOverdueTickets_shouldGiveCorrespondingFilter()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, "getTicketRedirectLinkByType", ["overdue_tickets", ["test-param"=> "one"], "test"]);
        $this->assertStringContainsStringIgnoringCase(http_build_query(["category"=>"overdue", "test-param"=> "one"]), $methodResponse);
    }

    public function test_getTicketRedirectLinkByType_whenTypeIsInvalid_shouldThrowAnException()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->getPrivateMethod($this->classObject, "getTicketRedirectLinkByType", ["invalid_type", ["test-param"=> "one"], "test"]);
    }

    public function test_getManagerSpecificReport_whenLoggedInUserAsAdmin_shouldCallReportMethodsWithEmptyDepartmentAndAgent()
    {
        $this->getLoggedInUserForWeb("admin");
        // assigning to department and making manager also.
        $this->assignAgentToDepartment($this->user, [1, 2]);
        $this->user->managerOfDepartments()->sync([1, 2]);
        $mock = Mockery::mock(DailyReport::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->allows("getAgentsAnalysis");
        $mock->allows("getDepartmentAnalysis");
        $mock->getManagerSpecificReport($this->user);
        $mock->shouldHaveReceived("getAgentsAnalysis")->with([], [], $this->user->id);
        $mock->shouldHaveReceived("getDepartmentAnalysis")->with([], $this->user->id);
    }

    public function test_getManagerSpecificReport_whenLoggedInUserAsManager_shouldCallReportMethodsWithDepartmentIdsAndAgentIds()
    {
        $agentOne = $this->user;
        $this->getLoggedInUserForWeb("agent");
        // assigning to department and making manager also.
        $this->assignAgentToDepartment($this->user, [1, 2]);
        $this->user->managerOfDepartments()->sync([1, 2]);
        $mock = Mockery::mock(DailyReport::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->allows("getAgentsAnalysis");
        $mock->allows("getDepartmentAnalysis");
        $mock->getManagerSpecificReport($this->user);
        $mock->shouldHaveReceived("getAgentsAnalysis")->with([1, $agentOne->id], [1, 2], $this->user->id);
        $mock->shouldHaveReceived("getDepartmentAnalysis")->with([1, 2], $this->user->id);
    }

    public function test_getManagerSpecificReport_ifAgentIsNotManager_shouldThrowAnException()
    {
        $this->expectException(UnauthorizedException::class);
        $this->getLoggedInUserForWeb("agent");
        // assigning to department and making manager also.
        $mock = Mockery::mock(DailyReport::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->allows("getAgentsAnalysis");
        $mock->allows("getDepartmentAnalysis");
        $mock->getManagerSpecificReport($this->user);
    }

    public function test_getRequiresImmediateActionTickets_whenDuedateIsMoreThanCurrentDate_appendsMetaDataToTicketWithOverdueIsFalse()
    {
        $this->createTicket(["reopened"=>1, "assigned_to"=> null, "duedate"=>Carbon::now()->addYear(), "status"=>1, "dept_id"=> 1], ["title"=>"test ticket"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getRequireImmediateActionTickets", [$this->user]);

        $this->assertEquals(1, $methodResponse->total);
        $this->assertFalse((bool)$methodResponse->data[0]->metaData['overdue']);
    }

    public function test_getRequiresImmediateActionTickets_whenDuedateIsLessThanCurrentDate_appendsMetaDataToTicketWithOverdueIsTrue()
    {
        $this->createTicket(["reopened"=>0, "assigned_to"=> null, "duedate"=>Carbon::now()->subYear(), "status"=>1, "dept_id"=> 1], ["title"=>"test ticket"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getRequireImmediateActionTickets", [$this->user]);

        $this->assertEquals(1, $methodResponse->total);
        $this->assertTrue((bool)$methodResponse->data[0]->metaData['overdue']);
    }

    public function test_getRequiresImmediateActionTickets_whenTicketIsAssignedToLoggedInPerson_appendsMetaDataToTicketWithAssignedToMeAsTrue()
    {
        $this->createTicket(["reopened"=>0, "assigned_to"=> \Auth::user()->id, "duedate"=>Carbon::now()->subYear(), "status"=>1, "dept_id"=> 1], ["title"=>"test ticket"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getRequireImmediateActionTickets", [$this->user]);

        $this->assertEquals(1, $methodResponse->total);
        $this->assertTrue((bool)$methodResponse->data[0]->metaData['assigned_to_me']);
    }

    public function test_getRequiresImmediateActionTickets_whenTicketIsNotAssignedToLoggedInPerson_appendsMetaDataToTicketWithAssignedToMeAsFalse()
    {
        $this->createTicket(["assigned_to"=> null, "duedate"=>Carbon::now()->subYear(), "status"=>1, "dept_id"=> 1], ["title"=>"test ticket"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getRequireImmediateActionTickets", [$this->user]);

        $this->assertEquals(1, $methodResponse->total);
        $this->assertFalse((bool)$methodResponse->data[0]->metaData['assigned_to_me']);
    }

    public function test_getRequiresImmediateActionTickets_whenTicketIsReopened_appendsMetaDataToTicketWithReopenedAsTrue()
    {
        $this->createTicket(["reopened"=> 1, "duedate"=>Carbon::now()->subYear(), "status"=>1, "dept_id"=> 1], ["title"=>"test ticket"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getRequireImmediateActionTickets", [$this->user]);

        $this->assertEquals(1, $methodResponse->total);
        $this->assertTrue((bool)$methodResponse->data[0]->metaData['reopened']);
    }

    public function test_getRequiresImmediateActionTickets_whenTicketIsNotReopened_appendsMetaDataToTicketWithReopenedAsFalse()
    {
        $this->createTicket(["reopened"=> 0, "duedate"=>Carbon::now()->subYear(), "status"=>1, "dept_id"=> 1], ["title"=>"test ticket"]);

        $methodResponse = $this->getPrivateMethod($this->classObject, "getRequireImmediateActionTickets", [$this->user]);

        $this->assertEquals(1, $methodResponse->total);
        $this->assertFalse((bool)$methodResponse->data[0]->metaData['reopened']);
    }
}