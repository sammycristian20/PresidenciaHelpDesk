<?php


namespace App\FaveoReport\Tests\Backend\Jobs;


use App\FaveoReport\Jobs\PerformanceReportExport;
use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\ReportDownload;
use Lang;
use Tests\DBTestCase;

class PerformanceReportExportTest extends DBTestCase
{

    private $classObject;

    private function setUpPerformance($type)
    {
        $reportDownload = new ReportDownload;
        $reportDownload->report_id = Report::where("type", $type)->value("id");
        $this->classObject = new PerformanceReportExport([], $reportDownload, 1);
        $this->setPrivateProperty($this->classObject, 'report', (object)['type'=>$type]);
    }

    public function test_getLink_whenTypeAgentPerformanceReport_shouldReturnAgentLink()
    {
        $this->setUpPerformance('agent-performance');
        $rowObject = (object)['id'=>1];
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLink', [$rowObject]);
        $this->assertStringContainsString('agent/1', $methodResponse);
    }

    public function test_getLink_whenTypeTeamPerformanceReport_shouldReturnTeamLink()
    {
        $this->setUpPerformance('team-performance');
        $rowObject = (object)['id'=>1];
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLink', [$rowObject]);
        $this->assertStringContainsString('assign-teams/1', $methodResponse);
    }

    public function test_getLink_whenTypeDepartmentPerformanceReport_shouldReturnDepartmentLink()
    {
        $this->setUpPerformance('department-performance');
        $rowObject = (object)['id'=>1];
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLink', [$rowObject]);
        $this->assertStringContainsString('department/1', $methodResponse);
    }


    public function test_getLinkColumnName_whenTypeAgentPerformanceReport_shouldReturnAgentLinkString()
    {
        $this->setUpPerformance('agent-performance');
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLinkColumnName');
        $this->assertEquals($methodResponse, Lang::get('report::lang.agent_link'));
    }

    public function test_getLinkColumnName_whenTypeTeamPerformanceReport_shouldReturnTeamString()
    {
        $this->setUpPerformance('team-performance');
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLinkColumnName');
        $this->assertEquals($methodResponse, Lang::get('report::lang.team_link'));
    }

    public function test_getLinkColumnName_whenTypeDepartmentPerformanceReport_shouldReturnDepartmentString()
    {
        $this->setUpPerformance('department-performance');
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLinkColumnName');
        $this->assertEquals($methodResponse, Lang::get('report::lang.department_link'));
    }


    public function test_getLinkText_whenTypeAgentPerformanceReport_shouldReturnAgentLinkString()
    {
        $this->setUpPerformance('agent-performance');
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLinkText');
        $this->assertEquals($methodResponse, Lang::get('report::lang.click_here_to_view_agent'));
    }

    public function test_getLinkText_whenTypeTeamPerformanceReport_shouldReturnTeamString()
    {
        $this->setUpPerformance('team-performance');
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLinkText');
        $this->assertEquals($methodResponse, Lang::get('report::lang.click_here_to_view_team'));
    }

    public function test_getLinkText_whenTypeDepartmentPerformanceReport_shouldReturnDepartmentString()
    {
        $this->setUpPerformance('department-performance');
        $methodResponse = $this->getPrivateMethod($this->classObject, 'getLinkText');
        $this->assertEquals($methodResponse, Lang::get('report::lang.click_here_to_view_department'));
    }
}