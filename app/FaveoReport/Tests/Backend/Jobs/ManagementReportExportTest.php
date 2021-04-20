<?php

namespace App\FaveoReport\Tests\Backend\Jobs;

use App\FaveoReport\Models\Report;
use Tests\DBTestCase;
use App\Model\helpdesk\Ticket\Tickets;
use App\FaveoReport\Controllers\BaseReportController;
use Config;
use App\FaveoReport\Jobs\ManagementReportExport;
use App\FaveoReport\Models\ReportDownload;
use Illuminate\Http\Request;
use App\Model\MailJob\QueueService;
use Queue;
use App\FaveoReport\Models\ReportColumn;

class ManagementReportExportTest extends DBTestCase
{
    /** @group getTickets */
    public function test_getRows_whenCalledWithRequestPopulated_shouldReturnTicketList()
    {
        $classObject = $this->getClassObject();

        $ticket = factory(Tickets::class)->create([
        'dept_id' => 1, 'help_topic_id' => 1, 'creator_id'=>1, 'status'=>1, 'type'=> 1, 'location_id'=>1,
        'user_id'=> 1, 'assigned_to'=>1, 'priority_id'=>1, 'source'=>1
        ]);

        $methodResponse = $this->getPrivateMethod($classObject, 'getRows');

        $this->assertCount(1, $methodResponse);

        $this->assertEquals($ticket->ticket_number, strip_tags($methodResponse[0]->ticket_number));
    }

    /** @group getTickets */
    public function test_getColumns_whenCalled_shouldReturnListOfAllManagementReportColumns()
    {
        $classObject = $this->getClassObject();

        $methodResponse = $this->getPrivateMethod($classObject, 'getColumns');

        $this->assertCount(ReportColumn::where('type', 'management_report')->count(), $methodResponse);
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
