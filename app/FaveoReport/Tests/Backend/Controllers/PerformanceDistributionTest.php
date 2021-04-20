<?php


namespace App\FaveoReport\Tests\Backend\Controllers;


use App\FaveoReport\Controllers\PerformanceDistribution;
use App\FaveoReport\Exceptions\TypeNotFoundException;
use App\Model\helpdesk\Ticket\Tickets;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Lang;
use Tests\DBTestCase;

class PerformanceDistributionTest extends DBTestCase
{
    private $classObject;

    public function setUp(): void
    {
        parent::setUp();

        $this->classObject = new PerformanceDistribution(new Request);

        $this->getLoggedInUserForWeb('admin');

        $this->blockTicketEvents();
    }

    public function test_getAverageTimeReport_whenResponseTimeIsRequested_shouldGiveResponseTimeInInterval()
    {
        $closedAt = Carbon::now();

        factory(Tickets::class)->create(['resolution_time'=> 13, 'closed'=>1, 'closed_at'=>$closedAt]);
        factory(Tickets::class)->create(['resolution_time'=> 14,'closed'=>1, 'closed_at'=>$closedAt]);
        factory(Tickets::class)->create(['resolution_time'=> 28,'closed'=>1, 'closed_at'=>$closedAt]);
        factory(Tickets::class)->create(['resolution_time'=> 31,'closed'=>1, 'closed_at'=>$closedAt]);
        factory(Tickets::class)->create(['resolution_time'=> 33,'closed'=>1, 'closed_at'=>$closedAt]);
        factory(Tickets::class)->create(['resolution_time'=> 34,'closed'=>1, 'closed_at'=>$closedAt]);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTimeReportByChartType', ['resolution_time']);

        $this->assertEquals(Lang::get('report::lang.resolution_time'), $methodResponse->name);
        $this->assertEquals('resolution_time', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertCount(3, $data);
        $this->assertEquals(2, $data[0]->value);
        $this->assertEquals('<15 minutes', $data[0]->label);
        $this->assertEquals(1, $data[1]->value);
        $this->assertEquals('15-30 minutes', $data[1]->label);
        $this->assertEquals(3, $data[2]->value);
        $this->assertEquals('30-60 minutes', $data[2]->label);
    }

    public function test_getPerformanceDistributionData_whenFirstResponseTimeIsRequested_shouldGiveFirstResponseInTimeInterval()
    {
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>13, 'is_internal'=>0, 'poster'=>'support']);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>14, 'is_internal'=>0, 'poster'=>'support']);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>28, 'is_internal'=>0, 'poster'=>'support']);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>31, 'is_internal'=>0, 'poster'=>'support']);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>33, 'is_internal'=>0, 'poster'=>'support']);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>34, 'is_internal'=>0, 'poster'=>'support']);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>35, 'is_internal'=>0, 'poster'=>'support']);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTimeReportByChartType', ['first_response_time']);

        $this->assertEquals(Lang::get('report::lang.first_response_time'), $methodResponse->name);
        $this->assertEquals('first_response_time', $methodResponse->id);

        $data = $methodResponse->data->toArray();

        $this->assertCount(3, $data);
        $this->assertEquals(2, $data[0]->value);
        $this->assertEquals('<15 minutes', $data[0]->label);
        $this->assertEquals(1, $data[1]->value);
        $this->assertEquals('15-30 minutes', $data[1]->label);
        $this->assertEquals(4, $data[2]->value);
        $this->assertEquals('30-60 minutes', $data[2]->label);
    }

    public function test_getPerformanceDistributionData_whenAvgResponseTimeIsRequested_shouldGiveAvgResponseTimeInTimeInterval()
    {
        $ticket = factory(Tickets::class)->create(["average_response_time"=>22.5]);
        $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>15, 'is_internal'=>0, 'poster'=>'support']);
        $ticket->thread()->create([ 'response_time'=>20, 'is_internal'=>0, 'poster'=>'support']);
        $ticket->thread()->create([ 'response_time'=>25, 'is_internal'=>0, 'poster'=>'support']);

        $ticket = factory(Tickets::class)->create(["average_response_time"=> 30]);
        $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>30, 'is_internal'=>0, 'poster'=>'support']);
        $ticket->thread()->create([ 'response_time'=>30, 'is_internal'=>0, 'poster'=>'support']);


        $ticket = factory(Tickets::class)->create(["average_response_time"=> 45]);
        $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>40, 'is_internal'=>0, 'poster'=>'support']);
        $ticket->thread()->create([ 'response_time'=>50, 'is_internal'=>0, 'poster'=>'support']);


        $ticket = factory(Tickets::class)->create(["average_response_time"=> 45]);
        $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>40, 'is_internal'=>0, 'poster'=>'support']);
        $ticket->thread()->create([ 'response_time'=>50, 'is_internal'=>0, 'poster'=>'support']);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTimeReportByChartType', ['average_response_time']);

        $this->assertEquals(Lang::get('report::lang.average_response_time'), $methodResponse->name);
        $this->assertEquals('average_response_time', $methodResponse->id);

        $data = $methodResponse->data->toArray();
        $this->assertCount(2, $data);
        $this->assertEquals(2, $data[0]->value);
        $this->assertEquals('15-30 minutes', $data[0]->label);
        $this->assertEquals(2, $data[1]->value);
    }


    public function test_getPerformanceDistributionData_whenInvalidTypeIsPassed_shouldThrowTypeNotFoundException()
    {
        $this->expectException(TypeNotFoundException::class);

        $this->getPrivateMethod($this->classObject, 'getTimeReportByChartType', ['invalid_type']);
    }

    public function test_getChartObject_whenTrendChartTypesArePassed_shouldReturnTimeAsLabel()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getChartObject', ['avg_resolution_time_trend']);
        $this->assertEquals(Lang::get('report::lang.no_of_minutes'), $methodResponse->dataLabel);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getChartObject', ['avg_response_time_trend']);
        $this->assertEquals(Lang::get('report::lang.no_of_minutes'), $methodResponse->dataLabel);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getChartObject', ['avg_first_response_time_trend']);
        $this->assertEquals(Lang::get('report::lang.no_of_minutes'), $methodResponse->dataLabel);
    }


    public function test_getChartObject_whenNonTrendChartTypesArePassed_shouldReturnTicketCountAsLabel()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getChartObject', ['resolution_time']);
        $this->assertEquals(Lang::get('report::lang.ticket_count'), $methodResponse->dataLabel);
    }

    public function test_getTrendResolutionReport_whenResolutionTimeTrendIsRequestedInDays_ShouldGiveAvgResolutionTimeGroupedByDate()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2019 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');

        $this->createTicketsForResolutionTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendResolutionReport', ['day']);

        $this->assertEquals('avg_resolution_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('10 Aug 2019', $data[0]->label);
        $this->assertEquals(11, $data[0]->value);
        $this->assertEquals('11 Aug 2019', $data[1]->label);
        $this->assertEquals(15, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenResolutionTimeTrendIsRequestedInMonth_ShouldGiveAvgResolutionTimeGroupedByMonth()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2019 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '10-09-2019 12:00');

        $this->createTicketsForResolutionTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendResolutionReport', ['month']);
        $this->assertEquals('avg_resolution_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('Aug 2019', $data[0]->label);
        $this->assertEquals(11, $data[0]->value);
        $this->assertEquals('Sep 2019', $data[1]->label);
        $this->assertEquals(15, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenResolutionTimeTrendIsRequestedInYear_ShouldGiveAvgResolutionTimeGroupedByYear()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2018 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '10-09-2019 12:00');

        $this->createTicketsForResolutionTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendResolutionReport', ['year']);
        $this->assertEquals('avg_resolution_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('2018', $data[0]->label);
        $this->assertEquals(11, $data[0]->value);
        $this->assertEquals('2019', $data[1]->label);
        $this->assertEquals(15, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenResolutionTimeTrendIsRequestedInWeek_ShouldGiveAvgResolutionTimeGroupedByWeek()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '18-08-2019 12:00');// sunday
        $dateThree = Carbon::createFromFormat('d-m-Y H:i', '19-08-2019 12:00');// Monday

        $this->createTicketsForResolutionTrendReport($dateOne, $dateTwo, $dateThree);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendResolutionReport', ['week']);
        $this->assertEquals('avg_resolution_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertCount(2, $data);
        $this->assertEquals('11 Aug 2019', $data[0]->label);
        $this->assertEquals(11, $data[0]->value);
        $this->assertEquals('18 Aug 2019', $data[1]->label);
        $this->assertEquals(16, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenFirstResponseTimeTrendIsRequestedInDays_ShouldGiveAvgResolutionTimeGroupedByDate()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2019 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');

        $this->createTicketsForFirstResponseTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendAvgFirstResponseReport', ['day']);
        $this->assertEquals('avg_first_response_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('10 Aug 2019', $data[0]->label);
        $this->assertEquals(13, $data[0]->value);
        $this->assertEquals('11 Aug 2019', $data[1]->label);
        $this->assertEquals(17, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenFirstResponseTimeTrendIsRequestedInMonth_ShouldGiveAvgResolutionTimeGroupedByMonth()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2019 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '10-09-2019 12:00');

        $this->createTicketsForFirstResponseTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendAvgFirstResponseReport', ['month']);
        $this->assertEquals('avg_first_response_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('Aug 2019', $data[0]->label);
        $this->assertEquals(13, $data[0]->value);
        $this->assertEquals('Sep 2019', $data[1]->label);
        $this->assertEquals(17, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenFirstResponseTimeTrendIsRequestedInYear_ShouldGiveAvgResolutionTimeGroupedByYear()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2018 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '10-09-2019 12:00');

        $this->createTicketsForFirstResponseTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendAvgFirstResponseReport', ['year']);
        $this->assertEquals('avg_first_response_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('2018', $data[0]->label);
        $this->assertEquals(13, $data[0]->value);
        $this->assertEquals('2019', $data[1]->label);
        $this->assertEquals(17, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenFirstResponseTimeTrendIsRequestedInWeek_ShouldGiveAvgResolutionTimeGroupedByWeek()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '18-08-2019 12:00');// sunday
        $dateThree = Carbon::createFromFormat('d-m-Y H:i', '19-08-2019 12:00');// Monday

        $this->createTicketsForFirstResponseTrendReport($dateOne, $dateTwo, $dateThree);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendAvgFirstResponseReport', ['week']);
        $this->assertEquals('avg_first_response_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('11 Aug 2019', $data[0]->label);
        $this->assertEquals(13, $data[0]->value);
        $this->assertEquals('18 Aug 2019', $data[1]->label);
        $this->assertEquals(18, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenAvgResponseTimeTrendIsRequestedInDays_ShouldGiveAvgResponseTimeGroupedByDate()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2019 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');

        $this->createTicketsForAvgResponseTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendAvgResponseReport', ['day']);
        $this->assertEquals('avg_response_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('10 Aug 2019', $data[0]->label);
        $this->assertEquals(25, $data[0]->value);
        $this->assertEquals('11 Aug 2019', $data[1]->label);
        $this->assertEquals(55, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenAvgResponseTimeTrendIsRequestedInMonth_ShouldGiveAvgResponseTimeGroupedByMonth()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2019 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '11-09-2019 12:00');

        $this->createTicketsForAvgResponseTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendAvgResponseReport', ['month']);
        $this->assertEquals('avg_response_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('Aug 2019', $data[0]->label);
        $this->assertEquals(25, $data[0]->value);
        $this->assertEquals('Sep 2019', $data[1]->label);
        $this->assertEquals(55, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenAvgResponseTimeTrendIsRequestedInYear_ShouldGiveAvgResponseTimeGroupedByYear()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '10-08-2018 12:00');
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '11-09-2019 12:00');

        $this->createTicketsForAvgResponseTrendReport($dateOne, $dateTwo);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendAvgResponseReport', ['year']);
        $this->assertEquals('avg_response_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();
        $this->assertEquals('2018', $data[0]->label);
        $this->assertEquals(25, $data[0]->value);
        $this->assertEquals('2019', $data[1]->label);
        $this->assertEquals(55, $data[1]->value);
    }

    public function test_getTrendResolutionReport_whenAvgResponseTimeTrendIsRequestedInWeek_ShouldGiveAvgResponseTimeGroupedByWeek()
    {
        $dateOne = Carbon::createFromFormat('d-m-Y H:i', '11-08-2019 12:00');// sunday
        $dateTwo = Carbon::createFromFormat('d-m-Y H:i', '18-08-2019 12:00');// sunday
        $dateThree = Carbon::createFromFormat('d-m-Y H:i', '19-08-2019 12:00');// Monday

        $this->createTicketsForAvgResponseTrendReport($dateOne, $dateTwo, $dateThree);

        $methodResponse = $this->getPrivateMethod($this->classObject, 'getTrendAvgResponseReport', ['week']);
        $this->assertEquals('avg_response_time_trend', $methodResponse->id);
        $data = $methodResponse->data->toArray();

        $this->assertEquals('11 Aug 2019', $data[0]->label);
        $this->assertEquals(25, $data[0]->value);
        $this->assertEquals('18 Aug 2019', $data[1]->label);
        $this->assertEquals(65, $data[1]->value);
    }

    private function createTicketsForResolutionTrendReport($dateOne, $dateTwo, $dateThree = null)
    {
        factory(Tickets::class)->create(['resolution_time'=> 10, 'closed'=>1, 'closed_at'=>$dateOne]);
        factory(Tickets::class)->create(['resolution_time'=> 12, 'closed'=>1, 'closed_at'=>$dateOne]);
        factory(Tickets::class)->create(['resolution_time'=> 14,'closed'=>1, 'closed_at'=>$dateTwo]);
        factory(Tickets::class)->create(['resolution_time'=> 16,'closed'=>1, 'closed_at'=>$dateTwo]);

        if($dateThree){
            factory(Tickets::class)->create(['resolution_time'=> 18,'closed'=>1, 'closed_at'=>$dateThree]);
        }
    }

    private function createTicketsForFirstResponseTrendReport($dateOne, $dateTwo, $dateThree = null)
    {
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>12, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateOne]);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>14, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateOne]);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>16, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateTwo]);
        factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>18, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateTwo]);

        if($dateThree){
            factory(Tickets::class)->create()->thread()->create(['thread_type'=>'first_reply', 'response_time'=>20, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateThree]);
        }
    }

    private function createTicketsForAvgResponseTrendReport($dateOne, $dateTwo, $dateThree = null)
    {
        $ticket = factory(Tickets::class)->create();
        $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>15, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateOne]);
        $ticket->thread()->create([ 'response_time'=>20, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateOne->addMinute(1)]);
        $ticket->thread()->create([ 'response_time'=>25, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateOne->addMinute(1)]);

        $ticket = factory(Tickets::class)->create();
        $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>30, 'is_internal'=>0, 'poster'=>'support',  'created_at'=>$dateOne]);
        $ticket->thread()->create([ 'response_time'=>35, 'is_internal'=>0, 'poster'=>'support',  'created_at'=>$dateOne->addMinute(1)]);


        $ticket = factory(Tickets::class)->create();
        $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>40, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateTwo]);
        $ticket->thread()->create([ 'response_time'=>50, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateTwo->addMinute(1)]);


        $ticket = factory(Tickets::class)->create();
        $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>60, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateTwo]);
        $ticket->thread()->create([ 'response_time'=>70, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateTwo->addMinute(1)]);

        if($dateThree){
            $ticket = factory(Tickets::class)->create();
            $ticket->thread()->create(['thread_type'=>'first_reply', 'response_time'=>80, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateThree]);
            $ticket->thread()->create([ 'response_time'=>90, 'is_internal'=>0, 'poster'=>'support', 'created_at'=>$dateTwo->addMinute(1)]);
        }
    }

    public function test_getRedirectLinkForTimeReport_whenChartTypeIsFirstResponseTime_shouldGiveRedirectLinkForFirstResponse()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getRedirectLinkForTimeReport', ["first_response_time", 15]);
        $this->assertStringContainsString(http_build_query(["first-response-time"=>"interval::0~minute~15~minute"]), $methodResponse);
    }

    public function test_getRedirectLinkForTimeReport_whenChartTypeIsAverageResponseTime_shouldGiveRedirectLinkForAverageResponse()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getRedirectLinkForTimeReport', ["average_response_time", 15]);
        $this->assertStringContainsString(http_build_query(["avg-response-time"=>"interval::0~minute~15~minute"]), $methodResponse);
    }

    public function test_getRedirectLinkForTimeReport_whenChartTypeIsResolutionTime_shouldGiveRedirectLinkForResolution()
    {
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getRedirectLinkForTimeReport', ["resolution_time", 15]);
        $this->assertStringContainsString(http_build_query(["resolution-time"=>"interval::0~minute~15~minute", 'is-resolved'=>1]), $methodResponse);
    }
}