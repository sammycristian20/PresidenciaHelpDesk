<?php

namespace App\Plugins\DepartmentStatusLink\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\DepartmentStatusLink\Model\Department;
use App\Plugins\DepartmentStatusLink\Model\TicketStatus;
use App\Plugins\SyncPluginToLatestVersion;
use Auth;
use Illuminate\Http\Request;

/**
 * Contains all the methods of department status link
 * @author Abhishek Kumar Haith <abhishek.haith@ladybirdweb.com>
 */
class DepartmentStatusLinkController extends Controller
{
    public function __construct()
    {
        // syncing ldap to latest version
        (new SyncPluginToLatestVersion)->sync('DepartmentStatusLink');
    }

    /**
     * Return department status create form field
     *
     * @return view
     */
    public function createFormField()
    {
        $status = TicketStatus::get(['id', 'name'])->pluck('name', 'id')->toArray();

        return view('DepartmentStatusLink::department.create', compact('status'));
    }

    /**
     * Return department status edit form field
     *
     * @return view
     */
    public function editFormField($department)
    {
        $status           = TicketStatus::get(['id', 'name'])->pluck('name', 'id')->toArray();
        $department       = Department::find($department->id);
        $departmentStatus = $department->statuses->pluck('id')->toArray();

        return view('DepartmentStatusLink::department.edit', compact('status', 'departmentStatus'));
    }

    /**
     * Store department status data
     *
     * @param instance $department Department
     * @param instance $request Instance of Request
     * @return void
     */
    public function attachStatusWithDepartment($department, Request $request)
    {
        $department = Department::find($department->id);
        $deptStatus = $request->input('department_status', array());

        $department->statuses()->sync($deptStatus);
    }

    /**
     * Function to get basic Query builder for department statuses
     *
     * @param  instance $baseQuery     Query builder of Ticket Status
     * @param  array $ticketIds        Array of ticket ids
     * @return void
     */
    public function getDepartmentStatuses(&$baseQuery, $ticketIds = [])
    {
        if (empty($ticketIds)) {
            return;
        }

        $departmentIds      = Tickets::whereIn('id', $ticketIds)->pluck('dept_id')->toArray();
        $deptHasAccosStatus = Department::whereIn('id', $departmentIds)->whereHas('statuses')->get()->count();

        if ($deptHasAccosStatus == 0) {
            return;
        }

        $baseQuery->setModel(new TicketStatus);

        $baseQuery->whereHas('departments', function ($query) use ($departmentIds) {
            foreach (array_unique($departmentIds) as $departmentId) {
                $query->where('department.id', $departmentId);
            }
        });
    }
}
