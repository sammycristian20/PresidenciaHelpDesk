<?php

namespace database\seeds\v_3_1_0;

use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\ReportColumn;
use database\seeds\DatabaseSeeder as Seeder;

class ReportSeeder extends Seeder
{
    public function run()
    {
        $this->helpdeskInDepthSeeder();
        $this->ticketVolumeTrendsSeeder();
        $this->managementReportSeeder();

        $this->agentPerformanceReportSeeder();
        $this->departmentPerformanceReportSeeder();
        $this->teamPerformanceReportSeeder();
        $this->performanceDistributionSeeder();

        $this->topCustomerAnalysisSeeder();

        $this->migrationOldReportColumnToNew();
    }

    private function helpdeskInDepthSeeder()
    {
        $report = $this->createReport("helpdesk-in-depth", "helpdesk-analysis",
            "fa fa-support fa-stack-1x", "helpdesk-in-depth-description");

        $report->subReports()->create([
            "data_type"=>"category-chart",
            "identifier"=>"helpdesk-in-depth-graph",
            "data_widget_url"=>"api/agent/helpdesk-in-depth-widget/$report->id",
            "data_url"=>"api/agent/helpdesk-in-depth/$report->id",
            "selected_chart_type"=>"bar",
            "list_view_by"=> ["priority", "source", "type", "status"],
            "selected_view_by"=>"priority",
            "layout"=>"n*2",
        ]);
    }

    private function ticketVolumeTrendsSeeder()
    {
        $report = $this->createReport("ticket-volume-trends", "helpdesk-analysis",
            "fa fa-calendar fa-stack-1x", "ticket-volume-trends-description");

        $report->subReports()->create([
            "data_type"=>"time-series-chart",
            "identifier"=>"overall-ticket-trend-graph",
            "data_widget_url"=>"api/agent/ticket-volume-trend/overall-ticket-trend-widget/$report->id",
            "data_url"=>"api/agent/ticket-volume-trend/overall-ticket-trend/$report->id",
            "selected_chart_type"=> null,
            "list_view_by"=> ["day", "week", "month", "year"],
            "selected_view_by"=>"day"
        ]);

        $report->subReports()->create([
            "data_type"=>"time-series-chart",
            "identifier"=>"weekday-hour-trend-graph",
            "data_widget_url"=>"api/agent/ticket-volume-trend/day-ticket-trend-widget/$report->id",
            "data_url"=>"api/agent/ticket-volume-trend/day-ticket-trend/$report->id",
            "selected_chart_type"=> null,
            "list_view_by"=> ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"],
            "selected_view_by"=>"monday"
        ]);
    }

    private function managementReportSeeder()
    {
        $report = $this->createReport("management-report", "helpdesk-analysis",
            "fa fa-user-secret fa-stack-1x", "management-report-description");

        $subReport =$report->subReports()->create([
            "data_type"=>"datatable",
            "identifier"=>"management-report-table",
            "data_widget_url"=> null,
            "data_url"=> "api/agent/management-report/$report->id",
            "selected_chart_type"=> null,
            "list_view_by"=> null,
            "selected_view_by"=>null
        ]);

        $subReport->add_custom_column_url = "api/add-custom-column/$subReport->id";


        // order in management report is not correct
        $managementReportColumns = ReportColumn::where("type", "management_report")->get();

        foreach ($managementReportColumns as $managementReportColumn) {
            $managementReportColumn->order = $managementReportColumn->id;
            $managementReportColumn->save();
        }

        $subReport->save();
    }

    private function agentPerformanceReportSeeder()
    {
        $report = $this->createReport("agent-performance", "productivity",
            "fa fa-user fa-stack-1x", "agent-performance-description");

        $subReport = $report->subReports()->create([
            "data_type"=>"datatable",
            "identifier"=>"agent-performance-table",
            "data_widget_url"=> null,
            "data_url"=> "api/agent/agent-performance-report/$report->id",
            "selected_chart_type"=> null,
            "list_view_by"=> null,
            "selected_view_by"=>null,
        ]);

        $subReport->add_custom_column_url = "api/add-custom-column/$subReport->id";

        $subReport->save();
    }

    private function departmentPerformanceReportSeeder()
    {
        $report = $this->createReport("department-performance", "productivity",
            "fa fa-building fa-stack-1x", "department-performance-description");

        $subReport = $report->subReports()->create([
            "data_type"=>"datatable",
            "identifier"=>"department-performance-table",
            "data_widget_url"=> null,
            "data_url"=> "api/agent/department-performance-report/$report->id",
            "selected_chart_type"=> null,
            "list_view_by"=> null,
            "selected_view_by"=>null,
        ]);

        $subReport->add_custom_column_url = "api/add-custom-column/$subReport->id";

        $subReport->save();
    }

    private function teamPerformanceReportSeeder()
    {
        $report = $this->createReport("team-performance", "productivity",
            "fa fa-users fa-stack-1x", "team-performance-description");

        $report->subReports()->create([
            "data_type"=>"datatable",
            "identifier"=>"team-performance-table",
            "data_widget_url"=> null,
            "data_url"=> "api/agent/team-performance-report/$report->id",
            "selected_chart_type"=> null,
            "list_view_by"=> null,
            "selected_view_by"=>null,
            "add_custom_column_url"=>"api/add-custom-column/$report->id"
        ]);
    }

    private function performanceDistributionSeeder()
    {
        $report = $this->createReport("performance-distribution", "productivity",
            "fa fa-cubes fa-stack-1x", "performance-distribution-description");

        $report->subReports()->create([
            "data_type"=>"category-chart",
            "identifier"=>"time-report-chart",
            "data_widget_url"=> null,
            "data_url"=>"api/agent/performance-distribution/time-report/$report->id",
            "selected_chart_type"=> "bar",
            "list_view_by"=> null,
            "selected_view_by"=> null
        ]);

        $report->subReports()->create([
            "data_type"=>"time-series-chart",
            "identifier"=>"trend-report-chart",
            "data_widget_url"=>null,
            "data_url"=>"api/agent/performance-distribution/trend-report/$report->id",
            "selected_chart_type"=> null,
            "list_view_by"=> ["day", "week", "month", "year"],
            "selected_view_by"=>"day"
        ]);
    }

    private function topCustomerAnalysisSeeder()
    {
        $report = $this->createReport("top-customer-analysis", "customer-happiness",
            "fa fa-bank fa-stack-1x", "top-customer-analysis-description");

        $report->subReports()->create([
            "data_type"=>"category-chart",
            "identifier"=>"top-customer-analysis-chart",
            "data_widget_url"=> null,
            "data_url"=>"api/agent/top-customer-analysis/$report->id",
            "selected_chart_type"=> "bar",
            "list_view_by"=> ["3", "5", "10"],
            "selected_view_by"=> "3",
            "layout"=>"n*2",
        ]);
    }

    /**
     * @param string $name
     * @param string $category
     * @param string $iconClass
     * @param $description
     * @return Report
     */
    private function createReport($name, $category, $iconClass, $description)
    {
        $reportInstance = Report::create(["name"=> $name, "type"=>$name, "icon_class"=> $iconClass,
            "category"=>$category, "is_default"=>1, "description"=> $description]);

        $reportInstance->view_url = "reports/$name/$reportInstance->id";

        $reportInstance->export_url = "api/agent/report-export/$reportInstance->id";

        $reportInstance->save();

        $filterInstance = $reportInstance->filter()->create(["status"=>1]);

        $filterInstance->filterMeta()->updateOrCreate(["key"=>"created-at", "value"=>"last::60~day"]);

        return $reportInstance;
    }

    private function migrationOldReportColumnToNew()
    {
        $reportColumns = ReportColumn::get();

        foreach ($reportColumns as $reportColumn) {

            switch ($reportColumn->type){
                case "management_report":
                    $this->updateReportId("management-report", $reportColumn);
                    break;

                case "agent_performance_report":
                    $this->updateReportId("agent-performance", $reportColumn);
                    break;

                case "department_performance_report":
                    $this->updateReportId("department-performance", $reportColumn);
                    break;

                case "team_performance_report":
                    $this->updateReportId("team-performance", $reportColumn);
                    break;
            }
        }
    }

    private function updateReportId(string $keyInReportTable, $reportColumn)
    {
        $subReports = Report::where('is_default', 1)->where('type', $keyInReportTable)->first()->subReports;

        foreach ($subReports as $subReport){

            $reportColumn->update(["sub_report_id" => $subReport->id]);
        }
    }
}

