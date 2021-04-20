<?php


namespace App\FaveoReport\Tests\Backend\Controllers;


use App\FaveoReport\Jobs\ManagementReportExport;
use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\ReportColumn;
use App\FaveoReport\Models\SubReport;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Ticket\TicketFilter;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\MailJob\QueueService;
use Queue;
use Tests\DBTestCase;
use Lang;

class ApiReportControllerTest extends DBTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->getLoggedInUserForWeb('admin');
    }

    public function test_getReportList_whenNoCustomReportIsPresent_shouldGiveAllReportsInCategories()
    {
        $response = $this->call('GET', 'api/agent/report-list');

        $reports = json_decode($response->getContent())->data;

        $this->assertEquals(Lang::get("report::lang.helpdesk-analysis"), $reports[0]->category);
        $this->assertEquals(Lang::get("report::lang.productivity"), $reports[1]->category);
        $this->assertEquals(Lang::get("report::lang.customer-happiness"), $reports[2]->category);

        $this->assertEquals("helpdesk-in-depth", $reports[0]->reports[0]->type);
        $this->assertEquals("ticket-volume-trends", $reports[0]->reports[1]->type);
        $this->assertEquals("management-report", $reports[0]->reports[2]->type);

        $this->assertEquals("agent-performance", $reports[1]->reports[0]->type);
        $this->assertEquals("department-performance", $reports[1]->reports[1]->type);
        $this->assertEquals("team-performance", $reports[1]->reports[2]->type);
        $this->assertEquals("performance-distribution", $reports[1]->reports[3]->type);

        $this->assertEquals("top-customer-analysis", $reports[2]->reports[0]->type);
    }

    public function test_getReportList_whenCustomReportIsPresent_shouldSortDefaultReportAndMakeCustomReportComeAtBottom()
    {
        Report::create(["name"=>"Custom Report", "type"=>"helpdesk-in-depth", 'is_default'=>0, "category"=>"helpdesk-analysis"]);

        $response = $this->call('GET', 'api/agent/report-list');

        $reports = json_decode($response->getContent())->data;

        $this->assertEquals(Lang::get("report::lang.helpdesk-analysis"), $reports[0]->category);
        $this->assertEquals(Lang::get("report::lang.productivity"), $reports[1]->category);
        $this->assertEquals(Lang::get("report::lang.customer-happiness"), $reports[2]->category);

        $this->assertEquals("helpdesk-in-depth", $reports[0]->reports[0]->type);
        $this->assertEquals("ticket-volume-trends", $reports[0]->reports[1]->type);
        $this->assertEquals("management-report", $reports[0]->reports[2]->type);

        $this->assertEquals("agent-performance", $reports[1]->reports[0]->type);
        $this->assertEquals("department-performance", $reports[1]->reports[1]->type);
        $this->assertEquals("team-performance", $reports[1]->reports[2]->type);
        $this->assertEquals("performance-distribution", $reports[1]->reports[3]->type);

        $this->assertEquals("top-customer-analysis", $reports[2]->reports[0]->type);
    }

    /** @group deleteCustomColumn */
    public function test_deleteCustomColumn_whenADefaultColumnIsDeleted_shouldNotDeleteThatColumn()
    {
        $response = $this->call('DELETE', 'api/delete-custom-column/1');

        $response->assertStatus(400);

        $this->assertEquals(1, ReportColumn::where('id', 1)->count());
    }

    /** @group deleteCustomColumn */
    public function test_deleteCustomColumn_whenACustomColumn_shouldDeleteThatColumn()
    {
        $reportColumnId = ReportColumn::create(['is_custom'=>1])->id;

        $response = $this->call('DELETE', "api/delete-custom-column/$reportColumnId");

        $response->assertStatus(200);

        $this->assertEquals(0, ReportColumn::where('id', $reportColumnId)->count());
    }

    /** @group postReportColumns */
    public function test_postReportColumns_whenVisibilityOfAColumnIsChanged_shouldUpdateTheChangeInDB()
    {
        $column = ReportColumn::where('key', 'ticket_number')->first();

        $isVisibleBefore = $column->is_visible;

        // changing the visibility
        $column->is_visible = !$column->is_visible;

        $subReportId = SubReport::where("data_type", "datatable")->first()->id;

        $response = $this->call('POST', "api/agent/report-columns/$subReportId",
            [$column->toArray()]);

        $response->assertStatus(200);

        $isVisibleAfter = ReportColumn::where('key', 'ticket_number')->value('is_visible');

        $this->assertEquals(!$isVisibleBefore, (bool)$isVisibleAfter);
    }

    /** @group postSubReportColumnsBySubReportId */
    public function test_postSubReportColumnsBySubReportId_whenOrderOfAColumnIsChanged_shouldUpdateTheChangeInDB()
    {
        $column = ReportColumn::where('key', 'ticket_number')->first();

        $subReportId = SubReport::where("data_type", "datatable")->first()->id;

        $column->order = 100;

        // by default ticket_number's order is 1
        $response = $this->call('POST', "api/agent/report-columns/$subReportId",
            [$column->toArray()]);

        $response->assertStatus(200);

        $this->assertEquals(100, ReportColumn::where('key', 'ticket_number')->value('order'));
    }


    /** @group postSubReportColumnsBySubReportId */
    public function test_postSubReportColumnsBySubReportId_whenInvalidSubReportIdIsPassed_shouldReturnStatus400()
    {
        $column = ReportColumn::where('key', 'ticket_number')->first();

        // by default ticket_number's order is 1
        $response = $this->call('POST', 'api/agent/report-columns/invalidId',
            [$column->toArray()]);

        $response->assertStatus(400);
    }

    /** @group addCustomColumn */
    public function test_addCustomColumn_whenAValidEquationIsGiven_shouldSaveTheDataAndUpdateOrderByItsId()
    {
        $subReportId = Report::where("type", "management-report")->first()->subReports->first()->id;

        $response = $this->call('POST', "api/add-custom-column/$subReportId", ['name'=>'Test Column',
            'equation'=>':created_at+30', "timestamp_format"=> "yy-mm-dd"]);

        $response->assertStatus(200);

        $latestCreatedColumn = ReportColumn::orderBy('id','desc')->first();

        $this->assertStringContainsString('test_column', $latestCreatedColumn->key);
        $this->assertEquals('Test Column', $latestCreatedColumn->label);
        $this->assertTrue((bool)$latestCreatedColumn->is_visible);
        $this->assertFalse((bool)$latestCreatedColumn->is_sortable);
        $this->assertTrue((bool)$latestCreatedColumn->is_custom);
        $this->assertEquals(':created_at+30', $latestCreatedColumn->equation);
        $this->assertEquals($latestCreatedColumn->id, $latestCreatedColumn->order);
        $this->assertEquals($latestCreatedColumn->sub_report_id, $subReportId);
    }

    /** @group addCustomColumn */
    public function test_addCustomColumn_whenDateFormatIsPassed_shouldSaveDateFormatOnlyIfColumnIsATimestamp()
    {
        $subReportId = Report::where("type", "management-report")->first()->subReports->first()->id;

        $response = $this->call('POST', "api/add-custom-column/$subReportId", ['name'=>'Test Column One',
            'equation'=>':created_at+30', "timestamp_format"=> "yy-mm-dd", "is_timestamp"=>1]);

        $response->assertStatus(200);

        $latestCreatedColumn = ReportColumn::orderBy('id','desc')->first();
        $this->assertEquals('yy-mm-dd', $latestCreatedColumn->timestamp_format);

        $response = $this->call('POST', "api/add-custom-column/$subReportId", ['name'=>'Test Column Two',
            'equation'=>':created_at+30', "timestamp_format"=> "yy-mm-dd", "is_timestamp"=>0]);

        $response->assertStatus(200);

        $latestCreatedColumn = ReportColumn::orderBy('id','desc')->first();

        $this->assertEquals(null, $latestCreatedColumn->timestamp_format);
    }

    /** @group addCustomColumn */
    public function test_addCustomColumn_whenAValidEquationIsGivenWhenTypeIsPassed_shouldSaveTheRecord()
    {
        $subReportId = Report::where("type", 'management-report')->first()->subReports->first()->id;

        $response = $this->call('POST', "api/add-custom-column/$subReportId", ['name'=>'Test Column',
            'equation'=>'30']);

        $response->assertStatus(200);

        $latestCreatedColumn = ReportColumn::orderBy('id','desc')->first();

        $this->assertEquals($latestCreatedColumn->sub_report_id, $subReportId);
    }

    /** @group addCustomColumn */
    public function test_addCustomColumn_whenAInValidEquationIsGiven_shouldNotSaveTheData()
    {
        $subReportId = Report::where("type", 'management-report')->first()->subReports->first()->id;

        $response = $this->call('POST', "api/add-custom-column/$subReportId", ['name'=>'Test Column',
            'equation'=>'invalid_parameter + 30']);

        $response->assertStatus(400);
    }

    /** @group reports */
    public function test_triggerManagementReportExport_asAdminWithQueueDriverSync()
    {
        QueueService::where('status', 1)->update(['status' => 0]);
        QueueService::where('short_name', 'sync')->update(['status' => 1]);

        Queue::fake();

        $this->getLoggedInUserForWeb('admin');

        $managementReportId = Report::where("type", "management-report")->value("id");

        $response = $this->call('POST', "api/agent/report-export/$managementReportId");

        $response->assertStatus(400);

        Queue::assertNotPushed(ManagementReportExport::class);
    }

    /** @group reports */
    public function test_triggerManagementReportExport_asAdminWithQueueDriverDatabase()
    {
        QueueService::where('status', 1)->update(['status' => 0]);
        QueueService::where('short_name', 'database')->update(['status' => 1]);

        Queue::fake();

        $this->getLoggedInUserForWeb('admin');

        $reportId = Report::where("type", "management-report")->value("id");
        $response = $this->call('POST', "api/agent/report-export/$reportId");

        $response->assertStatus(200);

        Queue::assertPushed(ManagementReportExport::class);
        Queue::assertPushedOn('reports', ManagementReportExport::class);
    }

    /** @group getReportConfigByReportId */
    public function test_getReportConfigByReportId_forNonTabularReport_shouldGiveColumnsAsNull()
    {
        $reportId = Report::where("type", "helpdesk-in-depth")->value("id");

        $response = $this->call('GET',"api/agent/report-config/$reportId");

        $response->assertStatus(200);

        $data = json_decode($response->getContent())->data;

        $this->assertEquals(1, count($data->sub_reports));

        $this->assertEquals([], $data->sub_reports[0]->columns);
    }

    /** @group getReportConfigByReportId */
    public function test_getReportConfigByReportId_forTabularReport_shouldGiveColumnsAsArray()
    {
        $reportId = Report::where("type", "management-report")->value("id");

        $response = $this->call('GET',"api/agent/report-config/$reportId");

        $response->assertStatus(200);

        $data = json_decode($response->getContent())->data;

        $this->assertEquals(1, count($data->sub_reports));

        $this->assertGreaterThanOrEqual(1, $data->sub_reports[0]->columns);

        $this->assertStringContainsString("api/add-custom-column/", $data->sub_reports[0]->add_custom_column_url);
    }

    /** @group getReportConfigByReportId */
    public function test_getReportConfigByReportId_whenCustomFieldsArePresent_shouldShowCustomFieldKeyAndLabelInManagementReport()
    {
        $formCategory = FormCategory::where('category','ticket')->first();

        $formField = $formCategory->FormFields()->create(['default'=>0, 'type'=>'test', 'display_for_agent'=> 1]);

        $formField->labels()->create(['meant_for'=>'form_field', 'language'=>'en', 'label'=>'test_label']);

        factory(Tickets::class)->create(['dept_id' => 1, 'help_topic_id' => 1, 'creator_id'=>1]);

        $reportId = Report::where("type", "management-report")->value("id");

        $response = $this->call('GET',"api/agent/report-config/$reportId");

        $data = json_decode($response->getContent())->data->sub_reports[0]->columns;

        $customFieldObject = array_reverse($data)[0];

        $this->assertEquals("custom_".$formField->id, $customFieldObject->key);

        $this->assertStringContainsString("test_label", $customFieldObject->label);
    }

    /** @group getReportConfigByReportId */
    public function test_getReportConfigByReportId_whenCustomTimestampColumnIsPresent_shouldShowTimeFormatForTimestampColumn()
    {
        ReportColumn::first()->update(["is_timestamp"=>1, "is_custom"=> 1]);

        $reportId = Report::where("type", "management-report")->value('id');

        $response = $this->call('GET',"api/agent/report-config/$reportId");

        $data = json_decode($response->getContent())->data->sub_reports[0]->columns;

        $this->assertEquals("F j, Y g:i  a", $data[0]->timestamp_format);

        $this->assertEquals(null, $data[1]->timestamp_format);
    }

    /** @group getReportConfigByReportId */
    public function test_getReportConfigByReportId_whenIncludeFilterParameterIsPresent_shouldShowFilters()
    {
        $reportId = Report::where("type", "management-report")->value("id");

        $response = $this->call('GET',"api/agent/report-config/$reportId");

        $data = json_decode($response->getContent())->data;

        $this->assertEquals(false, isset($data->filters));

        $response = $this->call('GET',"api/agent/report-config/$reportId", ["include_filters"=>1]);

        $data = json_decode($response->getContent())->data;

        $this->assertEquals(true, isset($data->filters));

        $this->assertGreaterThanOrEqual(1, $data->filters);
    }

    /** @group getReportConfigByReportId */
    public function test_getReportConfigByReportId_whenCustomFieldLabelIsUpdated_shouldReflectNewLabel()
    {
        $formCategory = FormCategory::where('category','ticket')->first();

        $formField = $formCategory->FormFields()->create(['default'=>0, 'type'=>'test', 'display_for_agent'=> 1]);

        $label = $formField->labels()->create(['meant_for'=>'form_field', 'language'=>'en', 'label'=>'test_label']);

        factory(Tickets::class)->create(['dept_id' => 1, 'help_topic_id' => 1, 'creator_id'=>1]);

        $reportId = Report::where("type", "management-report")->value('id');

        $this->call('GET',"api/agent/report-config/$reportId");

        // changing the label now
        $label->label = 'new_label';
        $label->save();

        $response = $this->call('GET',"api/agent/report-config/$reportId");

        $data = json_decode($response->getContent())->data->sub_reports[0]->columns;

        $customFieldObject = array_reverse($data)[0];

        $this->assertStringContainsString("new_label", $customFieldObject->label);
    }

    /** @group postReportConfigByReportId */
    public function test_postReportConfigByReportId_forNonTabularReport_shouldStoreSubReportAndFiltersAndNotColumns()
    {
        $reportConfig = $this->getReportConfigByKey("report_config_for_non_tabular_report");

        $initialCount = Report::count();

        $parentReportId = Report::where("type", "helpdesk-in-depth")->value("id");

        $response = $this->call("POST", "api/agent/report-config/$parentReportId", $reportConfig);

        $finalCount = Report::count();

        $response->assertStatus(200);

        $this->assertEquals(1, $finalCount - $initialCount);

        $reportInstance = Report::orderBy("id", "desc")->first();
        $defaultReportInstance = Report::where("type", $reportInstance->type)->where("is_default", 1)->first();

        $this->assertEquals($defaultReportInstance->subReports->count(), $reportInstance->subReports->count());
        $this->assertEquals("reports/helpdesk-in-depth/$reportInstance->id", $reportInstance->view_url);
        $this->assertEquals($defaultReportInstance->icon_class, $reportInstance->icon_class);

        $subReport = $reportInstance->subReports[0];
        $defaultSubReport = $defaultReportInstance->subReports[0];

        $this->assertEquals($defaultSubReport->data_type, $subReport->data_type);

        $this->assertEquals("api/agent/helpdesk-in-depth-widget/$reportInstance->id", $subReport->data_widget_url);
        $this->assertEquals("api/agent/helpdesk-in-depth/$reportInstance->id", $subReport->data_url);
        $this->assertEquals("pie", $subReport->selected_chart_type);
        $this->assertEquals( "status", $subReport->selected_view_by);
        $this->assertEquals( 0, $subReport->columns->count());

        $this->assertEquals(1, $reportInstance->filter->filterMeta->count());
    }

    public function test_postReportConfigByReportId_forTabularReport_shouldStoreSubReportAndFilters()
    {
        $reportConfig = $this->getReportConfigByKey("report_config_for_tabular_report");

        $initialCount = Report::count();

        $parentReportId = Report::where("type", "management-report")->value("id");

        $response = $this->call("POST", "api/agent/report-config/$parentReportId", $reportConfig);

        $finalCount = Report::count();

        $response->assertStatus(200);

        $this->assertEquals(1, $finalCount - $initialCount);

        $reportInstance = Report::orderBy("id", "desc")->first();
        $defaultReportInstance = Report::where("type", $reportInstance->type)->where("is_default", 1)->first();

        $this->assertEquals($defaultReportInstance->subReports->count(), $reportInstance->subReports->count());
        $this->assertEquals("reports/management-report/$reportInstance->id", $reportInstance->view_url);
        $this->assertEquals($defaultReportInstance->icon_class, $reportInstance->icon_class);

        $subReport = $reportInstance->subReports[0];
        $defaultSubReport = $defaultReportInstance->subReports[0];

        $this->assertEquals($defaultSubReport->data_type, $subReport->data_type);

        $this->assertEquals("", $subReport->data_widget_url);
        $this->assertEquals("api/agent/management-report/$reportInstance->id", $subReport->data_url);

        $this->assertEquals(1, $reportInstance->filter->filterMeta->count());
    }

    public function test_postReportConfigByReportId_whenUpdatingAnExistingRecord_shouldReflectInFinalResult()
    {
        $initialCount = Report::count();

        $reportId = Report::where("type", "management-report")->value('id');

        $response = $this->call('GET',"api/agent/report-config/$reportId", ["include_filters"=>1]);

        $reportConfig = json_decode($response->getContent(), true)['data'];

        $reportConfig["name"] = "testName";

        $response = $this->call("POST", "api/agent/report-config", $reportConfig);

        $finalCount = Report::count();

        $response->assertStatus(200);

        $this->assertEquals(0, $finalCount - $initialCount);

        $this->assertEquals("testName", Report::whereId($reportId)->value("name"));
    }
    
    private function getReportConfigByKey($key)
    {
        $basePath = app_path().DIRECTORY_SEPARATOR."FaveoReport".DIRECTORY_SEPARATOR."Tests".DIRECTORY_SEPARATOR."Backend".DIRECTORY_SEPARATOR
            ."FakeData";

        return (include $basePath."/ReportConfigs.php")[$key];
    }

    public function test_deleteReport_whenDefaultReportIsTriedToBeDeleted_shouldReturn400()
    {
        $reportId = Report::first()->id;

        $response = $this->call("DELETE", "api/report/$reportId");

        $response->assertStatus(400);

        $this->assertNotNull(Report::find($reportId));
    }

    public function test_deleteReport_whenInvalidIdIsPassed_shouldReturn404()
    {
        $response = $this->call("DELETE", "api/report/invalid_id");

        $response->assertStatus(404);
    }

    public function test_deleteReport_whenValidIdIsPassed_shouldDeleteReportAlongWithItsSubReportsAndColumnsAndFilter()
    {
        $report = Report::with("subReports")->where("type", "management-report")->first();

        $subReportId = $report->subReports[0]->id;

        // making as non default
        $report->is_default = 0;
        $report->is_public = 1;

        $report->save();

        $response = $this->call("DELETE", "api/report/$report->id");

        $response->assertStatus(200);

        $this->assertNull(Report::find($report->id));

        $this->assertCount(0, SubReport::where("report_id", $report->id)->get());

        $this->assertNull(TicketFilter::where("parent_id", $report->id)->first());

        $this->assertNull(ReportColumn::where("sub_report_id", $subReportId)->first());
    }

}