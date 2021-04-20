<?php

namespace App\FaveoReport\Tests\Backend\Controllers;

use App\FaveoReport\Models\ReportDownload;
use App\User;
use Auth;
use Tests\DBTestCase;

class ReportExportControllerTest extends DBTestCase
{
    public function setUp():void
    {
        parent::setUp();

        $this->getLoggedInUserForWeb('agent');

        $this->createPermissionForLoggedInUser(['report']);

        $this->blockTicketEvents();
    }

    /** @group report-exports */
    public function test_index_accessDeniedAsUser()
    {
        Auth::logout();

        $this->getLoggedInUserForWeb();

        $response = $this->call('GET', route('report.exports.list'));

        $response->assertStatus(302);
    }

    /** @group report-exports */
    public function test_index_accessDeniedForUnauthorizedAgent()
    {
        Auth::logout();

        $this->getLoggedInUserForWeb('agent');

        $response = $this->call('GET', route('report.exports.list'));
        $response->assertStatus(FAVEO_ACCESS_DENIED_CODE);
    }

    /** @group report-exports */
    public function test_index_accessGrantedAsAgent()
    {
        $response = $this->call('GET', route('report.exports.list'));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
    }

    /** @group report-exports */
    public function test_index_accessGrantedAsAadmin()
    {
        Auth::logout();

        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('GET', route('report.exports.list'));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
    }

    /** @group report-exports */
    public function test_indexList_getListOfReportExportsAsAgentExceptIncompleteExports()
    {
        $this->createReport($this->user, 0);

        $response = $this->call('GET', route('report.exports.list'));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $reports = $this->getResponseData($response);

        $this->assertObjectHasAttribute('data', $reports);
        $this->assertEmpty($reports->data);
    }

    /** @group report-exports */
    public function test_indexList_accessDeniedAsAgent()
    {
        Auth::logout();

        $this->getLoggedInUserForWeb('agent');

        $this->createReport($this->user);

        $response = $this->call('GET', route('report.exports.list'));

        $response->assertStatus(FAVEO_ACCESS_DENIED_CODE);
        $response->assertJson(['success' => false]);
    }

    /** @group report-exports */
    public function test_indexList_getListOfReportExportsAsAgent()
    {
        $this->createReport($this->user);

        $response = $this->call('GET', route('report.exports.list'));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $reports    = $this->getResponseData($response);
        $reportData = array_first($reports->data);

        $this->assertObjectHasAttribute('data', $reports);
        $this->assertArrayHasKeys(['id', 'file', 'ext', 'type', 'hash', 'user'], $reportData);
        $this->assertArrayHasKeys(['id', 'first_name', 'last_name', 'user_name'], $reportData->user);
    }

    /** @group report-exports */
    public function test_indexList_getListOfReportExportsAsAgentInvalidSortBy()
    {
        $this->createReport($this->user);

        $response = $this->call('GET', route('report.exports.list') . '?sort_by=sort');

        $response->assertStatus(FAVEO_VALIDATION_ERROR_CODE);
        $response->assertJson(['success' => false]);
    }

    /** @group report-exports */
    public function test_indexList_getListOfReportExportsAsAgentInvalidOrder()
    {
        $this->createReport($this->user);

        $response = $this->call('GET', route('report.exports.list') . '?sort_by=name&order=order');

        $response->assertStatus(FAVEO_VALIDATION_ERROR_CODE);
        $response->assertJson(['success' => false]);
    }

    /** @group report-exports */
    public function test_indexList_getListOfReportExportsAsAgentWithSortAndOrder()
    {
        $this->createReport($this->user);

        $response = $this->call('GET', route('report.exports.list') . '?sort_by=file&order=asc');

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $reports = $this->getResponseData($response);

        $this->assertObjectHasAttribute('data', $reports);
        $this->assertArrayHasKeys(['id', 'file', 'ext', 'type', 'hash', 'user'], array_first($reports->data));
    }

    /** @group report-exports */
    public function test_indexList_getListOfReportExportsAsAgentWithSearch()
    {
        $report = $this->createReport($this->user);

        $response = $this->call('GET', route('report.exports.list') . '?search=' . $report->file);

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $reports = $this->getResponseData($response);

        $this->assertObjectHasAttribute('data', $reports);
        $this->assertArrayHasKeys(['id', 'file', 'ext', 'type', 'hash', 'user'], array_first($reports->data));
    }

    /** @group report-exports */
    public function test_indexList_getListOfReportExportsAsAdmin()
    {
        $this->createReport($this->user);

        Auth::logout();

        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('GET', route('report.exports.list'));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $reports = $this->getResponseData($response);

        $this->assertObjectHasAttribute('data', $reports);
        $this->assertNotEmpty($reports->data);
    }

    /** @group report-exports */
    public function test_downloadReportExport_asAgent()
    {
        $report = $this->createReport($this->user);

        $response = $this->call('GET', route('report.exports.download', $report->hash));

        $response->assertStatus(FAVEO_INVALID_URL_CODE);
    }

    /** @group report-exports */
    public function test_delete_asAgent()
    {
        $report = $this->createReport($this->user);

        $response = $this->call('DELETE', route('report.exports.delete', $report->id));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('report_downloads', ['id' => $report->id]);
    }

    /** @group report-exports */
    public function test_delete_asAdminDeletingOthersReport()
    {
        $report = $this->createReport($this->user);

        Auth::logout();

        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('DELETE', route('report.exports.delete', $report->id));

        $response->assertStatus(FAVEO_SUCCESS_CODE);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('report_downloads', ['id' => $report->id]);
    }

    /** @group report-exports */
    public function test_delete_asAgentDeletingOthersReportFailurTest()
    {
        $report = $this->createReport($this->user);

        Auth::logout();

        $this->getLoggedInUserForWeb('agent');
        $this->createPermissionForLoggedInUser(['report']);

        $response = $this->call('DELETE', route('report.exports.delete', $report->id));

        $response->assertStatus(FAVEO_ERROR_CODE);
        $response->assertJson(['success' => false]);

        $this->assertDatabaseHas('report_downloads', ['id' => $report->id]);
    }

    private function getResponseData($response)
    {
        $responseContent = json_decode($response->content());

        return $responseContent->data;
    }

    private function createReport(User $user, $completed = 1)
    {
        $report = factory(ReportDownload::class)->create(['is_completed' => $completed, 'user_id' => $user->id]);

        $this->assertDatabaseHas('report_downloads', [
            'file'    => $report->file,
            'hash'    => $report->hash,
            'user_id' => $this->user->id,
        ]);

        return $report;
    }
}
