<?php

namespace database\seeds\v_2_2_3;

use database\seeds\DatabaseSeeder as Seeder;
use App\FaveoReport\Models\ReportColumn;

class ReportSeeder extends Seeder
{
    private $type;
    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->createAgentPerformanceColumns();

        $this->createDepartmentPerformanceColumns();

        $this->createTeamPerformanceColumns();
    }

    private function createAgentPerformanceColumns()
    {
        $this->type = 'agent_performance_report';

        $this->addCommonColumns();
    }

    private function createDepartmentPerformanceColumns()
    {
        $this->type = 'department_performance_report';

        $this->addCommonColumns();
    }

    private function createTeamPerformanceColumns()
    {
        $this->type = 'team_performance_report';

        $this->addCommonColumns();
    }

    private function addCommonColumns()
    {
        $this->createReportColumn('name', 'name', true,1);
        $this->createReportColumn('assigned_tickets', 'assigned_tickets', true, 2);
        $this->createReportColumn('reopened_tickets', 'reopened_tickets', true, 3);
        $this->createReportColumn('resolved_tickets', 'resolved_tickets', true, 4);
        $this->createReportColumn('tickets_with_response_sla_met', 'tickets_with_response_sla_met', true, 5);
        $this->createReportColumn('tickets_with_resolution_sla_met', 'tickets_with_resolution_sla_met', true, 6);
        $this->createReportColumn('avg_resolution_time', 'avg_resolution_time', true, 7);
        $this->createReportColumn('avg_response_time', 'avg_response_time', false, 8);
        $this->createReportColumn('responses', 'responses', false, 9);
    }

    private function createReportColumn($key, $label, $isSortable, $order, $isTimestamp = false, $isHtml = true, $isVisible = true)
    {
        ReportColumn::updateOrCreate(['key'=>$key, 'type'=>$this->type], ['key'=>$key, 'label'=>$label,
            'is_sortable'=>$isSortable, 'is_timestamp'=>$isTimestamp, 'is_html'=>$isHtml,
            'is_visible'=>$isVisible, 'type'=>$this->type, 'order'=> $order]);
    }
}
