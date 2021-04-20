<?php

namespace App\FaveoReport\Tests\Backend\Jobs;

use App\FaveoReport\Jobs\ManagementReportExport;
use App\FaveoReport\Models\Report;
use Illuminate\Support\Collection;
use Tests\DBTestCase;
use App\Model\helpdesk\Ticket\Tickets;
use App\FaveoReport\Models\ReportDownload;
use App\FaveoReport\Models\ReportColumn;
use App\FaveoReport\Jobs\BaseTableExport;

class BaseTableExportTest extends DBTestCase
{
    public function test_getReportRow_whenObjectIsPassed_shouldReturnValueInSameOrderAsColumn()
    {
        // making everything invisible, so that selected columns order can be tested
        ReportColumn::whereNotIn('key', ['statuses.name','sources.name'])->update(['is_visible'=> 0]);

        $classObject = $this->getClassObject();

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id'=>1, 'status'=>1, 'type'=> 1, 'location_id'=>1,
            'user_id'=> 1, 'assigned_to'=>1, 'priority_id'=>1, 'source'=>1
        ]);

        // result should come is same order [statusName(open), sources()]
        $methodResponse = $this->getPrivateMethod($classObject, 'getReportRow', [$ticket]);

        $this->assertEquals('Open', $methodResponse[0]);

        $this->assertEquals('Web', $methodResponse[1]);
    }

    public function test_getReportRow_whenHtmlDataIsPassed_shouldStripTagsAndReturnResult()
    {
        // making everything invisible, so that selected columns order can be tested
        ReportColumn::whereNotIn('key', ['statuses.name'])->update(['is_visible'=> 0]);

        $classObject = $this->getClassObject();

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id'=>1, 'status'=>1, 'type'=> 1, 'location_id'=>1,
            'user_id'=> 1, 'assigned_to'=>1, 'priority_id'=>1, 'source'=>1
        ]);

        $ticket->statuses->name = "<p>body</p>";

        // result should come is same order [statusName(open), sources()]
        $methodResponse = $this->getPrivateMethod($classObject, 'getReportRow', [$ticket]);

        $this->assertEquals('body', $methodResponse[0]);
    }

    // public function test_getReportRow_whenTimestampColumnIsPassed_shouldConvertThatIntoExcelDate()
    // {
    //     // making everything invisible, so that selected columns order can be tested
    //     ReportColumn::whereNotIn('key', ['created_at'])->update(['is_visible'=> 0]);

    //     $classObject = $this->getClassObject();

    //     $ticket = factory(Tickets::class)->create([
    //         'dept_id' => 1, 'help_topic_id' => 1, 'creator_id'=>1, 'status'=>1, 'type'=> 1, 'location_id'=>1,
    //         'user_id'=> 1, 'assigned_to'=>1, 'priority_id'=>1, 'source'=>1
    //     ]);


    //     // result should come is same order [statusName(open), sources()]
    //     $methodResponse = $this->getPrivateMethod($classObject, 'getReportRow', [$ticket]);

    //     $timeInAgentTimezone = changeTimezoneForDatetime($ticket->created_at, 'UTC', agentTimezone());

    //     $this->assertEquals(\PHPExcel_Shared_Date::PHPToExcel($timeInAgentTimezone), $methodResponse[0]);
    // }

    // public function test_getReportRow_whenCustomTimestampColumnIsPassed_shouldConvertThatInToExcelFormat()
    // {
    //     // making everything invisible, so that selected columns order can be tested
    //     ReportColumn::whereNotIn('key', ['created_at'])->update(['is_visible'=> 0]);
    //     ReportColumn::where("key", "created_at")->update(["is_custom"=>1]);

    //     $classObject = $this->getClassObject();

    //     $ticket = factory(Tickets::class)->create([
    //         'dept_id' => 1, 'help_topic_id' => 1, 'creator_id'=>1, 'status'=>1, 'type'=> 1, 'location_id'=>1,
    //         'user_id'=> 1, 'assigned_to'=>1, 'priority_id'=>1, 'source'=>1
    //     ]);

    //     // result should come is same order [statusName(open), sources()]
    //     $methodResponse = $this->getPrivateMethod($classObject, 'getReportRow', [$ticket]);

    //     $timeInAgentTimezone = changeTimezoneForDatetime($ticket->created_at, 'UTC', agentTimezone());

    //     $this->assertEquals(\PHPExcel_Shared_Date::PHPToExcel($timeInAgentTimezone), $methodResponse[0]);
    // }

    public function test_getReportRow_whenTimestampColumnIsPassedButValueIsNull_shouldNotAlterThatValue()
    {
        // making everything invisible, so that selected columns order can be tested
        ReportColumn::whereNotIn('key', ['created_at'])->update(['is_visible'=> 0]);

        $classObject = $this->getClassObject();

        $ticket = factory(Tickets::class)->create([
            'dept_id' => 1, 'help_topic_id' => 1, 'creator_id'=>1, 'status'=>1, 'type'=> 1, 'location_id'=>1,
            'user_id'=> 1, 'assigned_to'=>1, 'priority_id'=>1, 'source'=>1
        ]);

        $ticket->created_at = null;

        // result should come is same order [statusName(open), sources()]
        $methodResponse = $this->getPrivateMethod($classObject, 'getReportRow', [$ticket]);

        $this->assertEquals($ticket->created_at, $methodResponse[0]);
    }
    
    public function test_setColumns_whenColumnIsPassedAsNull_shouldFillColumnPropertyWithVisibleColumnsFromDatabase()
    {
        $classObject = $this->getClassObject();

        $this->getPrivateMethod($classObject, 'setColumns');

        $columns = $this->getPrivateProperty($classObject, 'columns');

        $columnCount = ReportColumn::where('is_visible', 1)->where('type', 'management_report')->count();

        $this->assertCount($columnCount, $columns);
    }

    public function test_setColumns_whenColumnIsPassedAsValidColumns_shouldNotFillColumnPropertyWithVisibleColumnsFromDatabase()
    {
        $classObject = $this->getClassObject();

        $columnOne = ReportColumn::create(["label"=>"columnOne", "is_timestamp"=>1]);
        $columnTwo = ReportColumn::create(["label"=>"columnTwo", "is_timestamp"=>0]);

        $testColumns = Collection::make([$columnOne, $columnTwo]);

        $this->getPrivateMethod($classObject, 'setColumns', [$testColumns]);

        $columns = $this->getPrivateProperty($classObject, 'columns');

        $this->assertEquals($testColumns, $columns);
    }

    public function test_setColumns_whenColumnIsPassedAsValidColumnsWithTimestampColumns_shouldPopulateTimestampColumnsWithTimestampColumns()
    {
        $classObject = $this->getClassObject();

        $columnOne = ReportColumn::create(["label"=>"columnOne", "is_timestamp"=>1]);
        $columnTwo = ReportColumn::create(["label"=>"columnTwo", "is_timestamp"=>0]);

        $testColumns = Collection::make([$columnOne, $columnTwo]);

        $this->getPrivateMethod($classObject, 'setColumns', [$testColumns]);

        $columns = $this->getPrivateProperty($classObject, 'columns');

        $timestampColumns = $this->getPrivateProperty($classObject, 'timestampColumns');

        $this->assertEquals($testColumns, $columns);
        $this->assertEquals($timestampColumns, Collection::make([$columnOne]));
    }

    public function test_getColumnsAsLabelList_whenArrayOfObjectsOfColumnIsPassed_shouldReturnArrayOfLabelPropertyByAddingTicketLinkAtTheEnd()
    {
        $classObject = $this->getClassObject();

        $testColumns = ['columnOne', 'columnTwo'];

        $this->setPrivateProperty($classObject, 'columns',
            Collection::make([ (object)['label'=>'labelOne'], (object)['label'=>'labelTwo'] ]));

        $methodResponse = $this->getPrivateMethod($classObject, 'getColumnsAsLabelList');

        $this->assertEquals(['labelOne', 'labelTwo', 'Ticket Link'], $methodResponse);
    }

    private function getClassObject($params = [])
    {
        $this->getLoggedInUserForWeb('admin');

        $report = new ReportDownload;

        $report->report_id = Report::where("type", "management-report")->value("id");

        // instantiating ManagementReportExport class since BaseTableExport is an abstract class whose method cannot be tested directly
        return new ManagementReportExport($params, $report, $this->user->id);
    }
}
