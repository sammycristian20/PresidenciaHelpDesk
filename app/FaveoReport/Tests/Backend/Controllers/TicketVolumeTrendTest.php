<?php


namespace App\FaveoReport\Tests\Backend\Controllers;


use App\FaveoReport\Controllers\TicketVolumeTrend;
use App\FaveoReport\Exceptions\TypeNotFoundException;
use App\FaveoReport\Models\Report;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\kb\Timezone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Lang;
use Tests\DBTestCase;

class TicketVolumeTrendTest extends DBTestCase
{

    private $classObject;

    private $reportId;

    public function setUp(): void
    {
        parent::setUp();

        $this->classObject = new TicketVolumeTrend(new Request);

        $this->getLoggedInUserForWeb('admin');

        $this->user->agent_tzone = Timezone::create(["name"=>"UTC", "location"=>"UTC"])->id;

        $this->reportId = Report::where("type", "ticket-volume-trends")->value("id");

        $this->blockTicketEvents();
    }

    public function test_getTicketTrendByChartType_whenForReceivedTickets_shouldReturnThemByDateGroup()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '12-08-2019 12:00');// sunday

        $this->createTicketForTicketTrend($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTicketTrendByChartType', ['received_tickets']);

        $this->assertEquals('received_tickets', $methodResponse->id);
        $this->assertEquals(Lang::get('report::lang.received_tickets'), $methodResponse->name);

        $data = $methodResponse->data->toArray();

        $this->assertEquals('11 Aug 2019', $data[0]->label);
        $this->assertEquals(2, $data[0]->value);
        $this->assertEquals('12 Aug 2019', $data[1]->label);
        $this->assertEquals(4, $data[1]->value);
    }

    public function test_getTicketTrendByChartType_whenForResolvedTickets_shouldReturnThemByDateGroup()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '12-08-2019 12:00');// sunday

        $this->createTicketForTicketTrend($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTicketTrendByChartType', ['resolved_tickets']);

        $this->assertEquals('resolved_tickets', $methodResponse->id);
        $this->assertEquals(Lang::get('report::lang.resolved_tickets'), $methodResponse->name);

        $data = $methodResponse->data->toArray();

        $this->assertEquals('11 Aug 2019', $data[0]->label);
        $this->assertEquals(1, $data[0]->value);
        $this->assertEquals('12 Aug 2019', $data[1]->label);
        $this->assertEquals(2, $data[1]->value);
    }


    public function test_getTicketTrendByChartType_whenForUnresolvedTickets_shouldReturnThemByDateGroup()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '12-08-2019 12:00');// sunday

        $this->createTicketForTicketTrend($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTicketTrendByChartType', ['unresolved_tickets']);

        $this->assertEquals('unresolved_tickets', $methodResponse->id);
        $this->assertEquals(Lang::get('report::lang.unresolved_tickets'), $methodResponse->name);

        $data = $methodResponse->data->toArray();

        $this->assertEquals('11 Aug 2019', $data[0]->label);
        $this->assertEquals(1, $data[0]->value);
        $this->assertEquals('12 Aug 2019', $data[1]->label);
        $this->assertEquals(2, $data[1]->value);
    }

    private function createTicketForTicketTrend($dateOne, $dateTwo)
    {
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateOne]);
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateOne]);
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateTwo]);
    }

    public function test_getDayTrendForReceivedTickets_whenValidDayIsPassed_shouldGiveThatDayDataGroupedByHour()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:05');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 13:05');// sunday
        $dateThree = Carbon::createFromFormat('d-m-Y H:i', '12-08-2019 13:05');// monday

        $this->createTicketForDayTrend($dateOne, $dateTwo, $dateThree);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getDayTrendForReceivedTickets', ['sunday']);
        $this->assertEquals('received_tickets', $methodResponse->id);

        $data = $methodResponse->data->toArray();

        $this->assertEquals('12',$data[0]->label);
        $this->assertEquals(2,$data[0]->value);
        $this->assertEquals('13',$data[1]->label);
        $this->assertEquals(3,$data[1]->value);
    }

    public function test_getDayTrendForResolvedTickets_whenValidDayIsPassed_shouldGiveThatDayDataGroupedByHour()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:05');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 13:05');// sunday
        $dateThree = Carbon::createFromFormat('d-m-Y H:i', '12-08-2019 13:05');// monday

        $this->createTicketForDayTrend($dateOne, $dateTwo, $dateThree);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getDayTrendForResolvedTickets', ['sunday']);
        $this->assertEquals('resolved_tickets', $methodResponse->id);

        $data = $methodResponse->data->toArray();

        $this->assertEquals('12',$data[0]->label);
        $this->assertEquals(1,$data[0]->value);
        $this->assertEquals('13',$data[1]->label);
        $this->assertEquals(1,$data[1]->value);
    }

    public function test_getDayOfWeek_forAnInvalidDay_throwsAnException()
    {
        $this->expectException(TypeNotFoundException::class);
        $this->getPrivateMethod($this->classObject, 'getDayOfWeek', ['invalid_day']);
    }

    public function test_getDayOfWeek_forAnValidDay_givesItsIndexOnWeekdays()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getDayOfWeek', ['saturday']);
        $this->assertEquals(7, $methodResponse);
    }

    private function createTicketForDayTrend($dateOne, $dateTwo, $dateThree)
    {
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateOne]);
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateOne, 'closed_at'=>$dateOne]);
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateTwo, 'closed_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateThree, 'closed_at'=>$dateThree]);
    }

    public function test_getOverallTicketTrendWidget_whenResolvedAndUnResolvedTicketsArePresent_shouldShowCountOfTheSame()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:05');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 13:05');// sunday
        $this->createTicketForTicketTrend($dateOne, $dateTwo);
        $response = $this->call('GET', "api/agent/ticket-volume-trend/overall-ticket-trend-widget/$this->reportId");
        $data = json_decode($response->getContent())->data;
        $this->assertEquals('received_tickets', $data[0]->id);
        $this->assertEquals(6, $data[0]->value);
        $this->assertEquals('unresolved_tickets', $data[1]->id);
        $this->assertEquals(3, $data[1]->value);
        $this->assertEquals('resolved_tickets', $data[2]->id);
        $this->assertEquals(3, $data[2]->value);
    }

    public function test_getDayTicketTrendWidget_whenResolvedAndUnResolvedTicketsArePresent_shouldShowCountOfTheSame()
    {
        $this->createTicketForDayTrendWidget();
        $response = $this->call('GET', "api/agent/ticket-volume-trend/day-ticket-trend-widget/$this->reportId");
        $data = json_decode($response->getContent())->data;

        $this->assertEquals('max_received_ticket_hour', $data[0]->id);
        $this->assertEquals('15:00 - 16:00 hours', $data[0]->value);
        $this->assertEquals('max_resolved_ticket_hour', $data[1]->id);
        $this->assertEquals('13:00 - 14:00 hours', $data[1]->value);
        $this->assertEquals('max_received_ticket_day', $data[2]->id);
        $this->assertEquals('Tuesday', $data[2]->value);
        $this->assertEquals('max_resolved_ticket_day', $data[3]->id);
        $this->assertEquals('Monday', $data[3]->value);
    }

    public function test_getDayTicketTrendWidget_whenNoTicketsArePresent_shouldShowEmptySymbol()
    {
        $response = $this->call('GET', "api/agent/ticket-volume-trend/day-ticket-trend-widget/$this->reportId");
        $data = json_decode($response->getContent())->data;
        $this->assertEquals('max_received_ticket_hour', $data[0]->id);
        $this->assertEquals('--', $data[0]->value);
        $this->assertEquals('max_resolved_ticket_hour', $data[1]->id);
        $this->assertEquals('--', $data[1]->value);
        $this->assertEquals('max_received_ticket_day', $data[2]->id);
        $this->assertEquals('--', $data[2]->value);
        $this->assertEquals('max_resolved_ticket_day', $data[3]->id);
        $this->assertEquals('--', $data[3]->value);
    }

    private function createTicketForDayTrendWidget()
    {
        // most ticket created on Tuesday
        // making most ticket resolved on Monday,
        // most ticket created bw 3pm - 4pm
        // most ticket resolved bw 1pm - 2pm
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '12-08-2019 13:05');// Monday 1pm
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '13-08-2019 15:05');// Tuesday 3pm

        // 3 tickets created and 3 tickets resolved on monday around 1pm
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateOne, 'closed_at'=>$dateOne]);
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateOne, 'closed_at'=>$dateOne]);
        factory(Tickets::class)->create(['closed'=>1, 'created_at'=>$dateOne, 'closed_at'=>$dateOne]);

        // 4 tickets created and 1 resolved on monday around 3pm
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create(['closed'=>0, 'created_at'=>$dateTwo, 'closed_at'=>$dateOne]);

    }
}