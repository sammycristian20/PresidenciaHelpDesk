<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Agent\helpdesk\UserController;
use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\DepartmentRequest;
use App\Http\Requests\helpdesk\DepartmentUpdate;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Agent\Permission;
use App\Model\helpdesk\Agent\Group_assign_department;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Email\Template;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
use Datatables;
use DB;
use Event;
use Exception;
use Illuminate\Http\Request;
use Lang;
use Auth;

/**
 * DepartmentController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class DepartmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles');
    }

    /**
     * Get index page.
     *
     * @param instance Department $department
     * @return HTML response
     */
    public function index(Department $department)
    {
        try {
            return view('themes.default1.admin.helpdesk.agent.departments.index');
        } catch (Exception $e) {

            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param int $id Department id
     * @return HTML response
     */
    public function DepartmentShow($id)
    {
        // Find the department
        $departments              = Department::find($id);
        $department_members_count = DepartmentAssignAgents::where('department_id', $departments->id)->select('agent_id')->count();
        $department_members_id    = DepartmentAssignAgents::where('department_id', $id)->pluck('agent_id')->toArray();
        $department_members       = User::whereIn('id', $department_members_id)->get();

        $departmentManager = DepartmentAssignManager::where('department_id', $id)->pluck('manager_id')->toArray();
        $deptManagerDetails = null;
        if ($departmentManager) {
            $deptManagerDetails = User::whereIn('id', $departmentManager)->select('first_name', 'last_name', 'user_name', 'email', 'profile_pic')->get();
        }
        $user = null;   
        if ($department_members_count != 0) {
            foreach ($department_members as $department_member) {
                $user[] = User::where('id', '=', $department_member->agent_id)->select('first_name', 'last_name', 'user_name', 'email', 'active')->first();
            }
        }
        $tickets       = new Tickets();
        $loggedInUser = Auth::user();
        return view('themes.default1.admin.helpdesk.agent.departments.show', compact('departments', 'deptManagerDetails', 'department_members', 'user', 'tickets', 'loggedInUser'));
    }

    public function create(User $user, Group_assign_department $group_assign_department, Department $department, Template $template, Emails $email)
    {
        try {
            $user           = $user->where([['role', '<>', 'user'], ['active', 1]])->get();
            $emails         = $email->select('email_name', 'id')->get();
            $templates      = $template->get();
            $department     = $department->get();
            $business_hours = BusinessHours::where('status', 1)->select('name', 'id')->pluck('name', 'id')->toArray();

            return view('themes.default1.admin.helpdesk.agent.departments.create', compact('department', 'templates', 'user', 'emails', 'business_hours'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param type Department        $department
     * @param type DepartmentRequest $request
     *
     * @return type Response
     */
    public function store(Department $department, DepartmentRequest $request)
    {
        try {

            $department->fill($request->except('manager'))->save();
            $requests = $request->input('group_id');
            $id       = $department->id;
            // if ($request->manager) {
            //     $manager_id=implode(",", $request->manager);
            //     $department->manager = $manager_id;
            //     // $department->manager = $request->input('manager');
            // } else {
            //     $department->manager = null;
            // }
            $department->manager = null;
            /* Succes And Failure condition */
            /*  Check Whether the function Success or Fail */
            if ($department->save() == true) {
                if ($request->input('sys_department') == 'on') {
                    DB::table('settings_system')
                        ->where('id', 1)
                        ->update(['department' => $department->id]);
                }
                if ($request->manager) {
                    foreach ($request->manager as $assign_manager) {
                        $dept_manager                = new DepartmentAssignManager();
                        $dept_manager->department_id = $department->id;
                        $dept_manager->manager_id    = $assign_manager;
                        $dept_manager->save();

                        $dept_assign_agent                = new DepartmentAssignAgents;
                        $dept_assign_agent->agent_id      = $assign_manager;
                        $dept_assign_agent->department_id = $department->id;
                        $dept_assign_agent->save();
                    }
                }

                // if ($request->manager) {
                //     $dept_assign_agent = new DepartmentAssignAgents;
                //     $dept_assign_agent->agent_id = $request->input('manager');
                //     $dept_assign_agent->department_id = $department->id;
                //     $dept_assign_agent->save();
                // }

                // Attach status with department in DepartmentStatusLink plugin
                Event::dispatch('saving-department', [$department, $request]);

                return redirect('departments')->with('success', Lang::get('lang.department_saved_sucessfully'));
            } else {
                return redirect('departments')->with('fails', Lang::get('lang.failed_to_create_department'));
            }
        } catch (Exception $e) {
            return redirect('departments')->with('fails', Lang::get('lang.failed_to_create_department'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id Department id
     * @return HTML response
     *
     * @return type Response
     */
    public function edit($id)
    {
        try {
            $sys_department = System::select('department')->where('id', '=', 1)->first();
            $user_array     = $this->getDepartmentMembers($id);

            $emails         = Emails::select('email_name', 'id')->get();
            $templates      = Template::get();
            $departments    = Department::whereId($id)->first();
            $business_hours = BusinessHours::where('status', 1)->select('name', 'id')->pluck('name', 'id')->toArray();
            $user           = User::where('role', '!=', 'user')->get();
            $team           = new Teams();

            return view('themes.default1.admin.helpdesk.agent.departments.edit', compact('team', 'templates', 'departments', 'user', 'emails', 'sys_department', 'business_hours'));
        } catch (Exception $e) {
            return redirect('departments')->with('fails', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param type int                     $id
     * @param type Group_assign_department $group_assign_department
     * @param type Department              $department
     * @param type DepartmentUpdate        $request
     *
     * @return type Response
     */
    public function update($id, DepartmentUpdate $request)
    {

        $user                    = new User();
        $group_assign_department = new Group_assign_department();
        $template                = new Template();
        $team                    = new Teams();
        $department              = new Department();
        $sla                     = new Sla_plan();
        $email                   = new Emails();
        $group                   = new Permission();
        // try {
        $table = $group_assign_department->where('department_id', $id);
        $table->delete();
        $requests = $request->input('group_id');
        // foreach ($requests as $req) {
        // DB::insert('insert into group_assign_department (group_id, department_id) values (?,?)', [$req, $id]);
        // }
        $departments = $department->whereId($id)->first();

        // if ($request->manager) {
        //     $departments->manager = implode(",", $request->manager);
        //     // $departments->manager = $request->input('manager');
        // } else {
        //     $departments->manager = null;
        // }
        $department->manager = null;
        $departments->save();
        $delete_department = DepartmentAssignManager::where('department_id', '=', $id)->delete();
        if ($request->manager) {
            foreach ($request->manager as $assign_manager) {
                $dept_manager                = new DepartmentAssignManager();
                $dept_manager->department_id = $id;
                $dept_manager->manager_id    = $assign_manager;
                $dept_manager->save();
                // $manager_id=implode(",", $request->manager);
                // $department->manager = $manager_id;

                $check_agent_belons_to_department = DepartmentAssignAgents::where('agent_id', '=', $assign_manager)->where('department_id', '=', $id)->count();
                // dd($check_agent_belons_to_department);
                if ($check_agent_belons_to_department == 0) {
                    $dept_assign_agent                = new DepartmentAssignAgents;
                    $dept_assign_agent->agent_id      = $assign_manager;
                    $dept_assign_agent->department_id = $id;
                    $dept_assign_agent->save();
                }

            }
        }

        if ($request->input('sys_department') == 'on') {
            DB::table('settings_system')
                ->where('id', 1)
                ->update(['department' => $id]);
        }

        // Attach status with department in DepartmentStatusLink plugin
        Event::dispatch('saving-department', [$departments, $request]);

        if ($departments->fill($request->except('group_access', 'manager', 'sla'))->save()) {
            return redirect('departments')->with('success', Lang::get('lang.department_updated_sucessfully'));
        } else {
            return redirect('departments')->with('fails', Lang::get('lang.department_not_updated'));
        }
        // } catch (Exception $e) {
        //     return redirect('departments')->with('fails', Lang::get('lang.department_not_updated'));
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param type int                     $id
     * @param type Department              $department
     * @param type Group_assign_department $group_assign_department
     *
     * @return type Response
     */
    public function destroy($id, Department $department, Group_assign_department $group_assign_department, System $system, Tickets $tickets)
    {
        // try {

        $querys = DB::table('sla_plan')
            ->whereRaw('FIND_IN_SET(?,apply_sla_depertment)', [$id])
            ->pluck('id')->toArray();
        if ($querys) {
            return redirect()->back()->with('fails', Lang::get('lang.you_cannot_delete_this_department,this_department_applied_sla_plan'));
        }

        $system = $system->where('id', '=', '1')->first();
        if ($system->department == $id) {
            return redirect('departments')->with('fails', Lang::get('lang.you_cannot_delete_default_department'));
        } else {
            $tickets = DB::table('tickets')->where('dept_id', '=', $id)->update(['dept_id' => $system->department]);
            if ($tickets > 0) {
                if ($tickets > 1) {
                    $text_tickets = 'Tickets';
                } else {
                    $text_tickets = 'Ticket';
                }
                $ticket = '<li>' . $tickets . ' ' . $text_tickets . Lang::get('lang.have_been_moved_to_default_department') . '</li>';
            } else {
                $ticket = '';
            }

            $emails = DB::table('emails')->where('department', '=', $id)->update(['department' => $system->department]);
            if ($emails > 0) {
                if ($emails > 1) {
                    $text_emails = 'Emails';
                } else {
                    $text_emails = 'Email';
                }
                $email = '<li>' . $emails . ' System ' . $text_emails . Lang::get('lang.have_been_moved_to_default_department') . ' </li>';
            } else {
                $email = '';
            }
            $helptopic = DB::table('help_topic')->where('department', '=', $id)->update(['department' => null], ['status' => '1']);
            if ($helptopic > 0) {
                $helptopic = '<li>' . Lang::get('lang.the_associated_helptopic_has_been_deactivated') . '</li>';
            } else {
                $helptopic = '';
            }
            $message = $ticket . $email . $helptopic;
            /* Becouse of foreign key we delete group_assign_department first */
            $group_assign_department = $group_assign_department->where('department_id', $id);
            $group_assign_department->delete();
            $departments = $department->whereId($id)->first();

            $delete_assign_manager_to_department = DepartmentAssignManager::where('department_id', '=', $id)->delete();

            /* Check the function is Success or Fail */
            if ($departments->delete() == true) {
                return redirect('departments')->with('success', Lang::get('lang.department_deleted_sucessfully') . $message);
            } else {
                return redirect('departments')->with('fails', Lang::get('lang.department_can_not_delete'));
            }
        }
    }

    /**
     * @category function to show departments table
     * @param null
     * @return json table data
     */
    public function getDepartmentTable()
    {

        $department = Department::select('id', 'name', 'type')->get();

        return \DataTables::of($department)
            ->editColumn('name', function ($department) {
                $defaultDepartmentId = System::where('id', '1')->value('department');

                $url            = 'departments/' . $department->id . '/edit';
                $departmentName = ((int) $defaultDepartmentId == $department->id) ? $department->name . "(Default)" : $department->name;

                return '<a href="' . $url . '">' . $departmentName . '</a>';

            })
            ->editColumn('type', function ($department) {
                if ($department->type == 0) {
                    $return_value = '<span style="color:red">' . Lang::get("lang.private") . '</span>';
                    ;
                } else {
                    $return_value = '<span style="color:green">' . Lang::get("lang.public") . '</span>';
                }
                return $return_value;
            })
            ->editColumn('user_name', function ($department) {

                $managerIds = DepartmentAssignManager::where('department_id', $department->d_id)->pluck('manager_id')->toArray();
                $managerIds = $department->managers;
                $display    = '--';
                if (count($managerIds)) {

                    foreach ($managerIds as $key => $value) {

                        $agentName[] = ($value->first_name != '' || $value->last_name != null) ? $value->first_name . ' ' . $value->last_name : $value->user_name;

                    }
                    $deptManager = (count($agentName)) ? implode(",", $agentName) : '--';

                    $display = '<span style="color:green;" title="' . $deptManager . '">' . ucfirst(str_limit($deptManager, 50)) . '</span>';
                }

                return $display;

            })

            ->addColumn('action', function ($department) {
                $defaultDepartment = System::where('id', '1')->value('department');
                $disable           = ((int) $defaultDepartment == $department->id) ? 'disabled' : '';

                $url          = url('delete/departmentpopup/' . $department->id);
                $confirmation = deletePopUp($department->id, $url, "Delete", 'btn btn-primary btn-xs  ' . $disable, "Delete", true, "delete");

                return
                '<a href="' . route('departments.edit', $department->id) . '" class="btn btn-primary btn-xs "><i class="fas fa-edit" style="color:white;"> </i> ' . Lang::get('lang.edit') . '</a>&nbsp;&nbsp;'
                . '<a href="' . route('department.profile.show', $department->id) . '" class="btn btn-primary btn-xs "><i class="fas fa-eye" style="color:white;"> </i> ' . Lang::get('lang.view') . '</a>&nbsp;&nbsp;'
                . $confirmation
                . \Form::close();
            })

            ->rawColumns(['name', 'type', 'user_name', 'action'])
            ->make();
    }

    /**
     *
     *
     *
     */
    public function getDepartmentMembers($id)
    {
        return DepartmentAssignAgents::where('department_id', '=', $id)->select('agent_id')->get()->pluck('agent_id')->all();
    }

    public function DepartmentUserprofile($id)
    {
        //Eloquent Relations- join queries
        $department = Department::join('department_assign_agents', 'department_assign_agents.department_id', '=', 'department.id')
            ->join('users', 'department_assign_agents.agent_id', '=', 'users.id')
            ->select(['users.id', 'users.first_name', 'users.last_name', 'users.user_name', 'users.email'])
            ->where('department.id', $id)
            ->where('users.is_delete', 0)
            ->distinct()->get(['users.id']);

        return \DataTables::of($department)
            ->addColumn('user_name', function ($department) {

                $name = Lang::get('lang.not-available');
                if ($department->first_name !== '' && $department->first_name !== null) {
                    $name = $department->first_name . ' ' . $department->last_name;
                    if (strlen($department->first_name . ' ' . $department->last_name) > 30) {
                        $name = mb_substr($department->first_name . ' ' . $department->last_name, 0, 30, 'UTF-8') . '...';
                    }
                    return '<a  href="' . route('user.show', $department->id) . '" title="' . $department->first_name . ' ' . $department->last_name . '">' . $name . '</a>';
                }
                return $name;
                // return $department->user_name;
            })
            ->addColumn('email', function ($department) {
                $email = $department->email;
                if (strlen($department->email) > 20) {
                    $email = mb_substr($department->email, 0, 20, 'UTF-8') . '...';
                }
                return '<a  href="' . route('user.show', $department->id) . '" title="' . $department->email . '">' . $email . '</a>';
            })
            ->addColumn('active', function ($department) {
                $user = User::where('id', $department->id)->select('active', 'email_verify', 'mobile_verify', 'is_delete')->first();
                return UserController::userStatus($user);
            })

            ->addColumn('actions', function ($department) {
                return '<a href="' . route('user.show', $department->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-eye" style="color:white;"> </i>&nbsp;&nbsp;' . Lang::get('lang.view') . '</a>';
            })
            ->rawColumns(['user_name', 'email', 'active', 'actions'])
            ->make(true);
    }

    /**
     * @category function
     * @param integer $id, string $date111. $date122
     * @return json respone
     */
    public function deptChartData($id, $date111 = '', $date122 = '')
    {
        $date11 = strtotime($date122);
        $date12 = strtotime($date111);
        if ($date11 && $date12) {
            $date2 = $date12;
            $date1 = $date11;
        } else {
            // generating current date
            $date2  = strtotime(date('Y-m-d'));
            $date3  = date('Y-m-d');
            $format = 'Y-m-d';
            // generating a date range of 1 month
            $date1 = strtotime(date($format, strtotime('-1 month' . $date3)));
        }
        $return = '';
        $last   = '';
        for ($i = $date1; $i <= $date2; $i = $i + 86400) {
            $thisDate = date('Y-m-d', $i);
            $created  = \DB::table('tickets')->select('created_at')->where('dept_id', '=', $id)->where('created_at', 'LIKE', '%' . $thisDate . '%')->count();
            $closed   = \DB::table('tickets')->select('closed_at')->where('dept_id', '=', $id)->where('closed_at', 'LIKE', '%' . $thisDate . '%')->count();
            $reopened = \DB::table('tickets')->select('reopened_at')->where('dept_id', '=', $id)->where('reopened_at', 'LIKE', '%' . $thisDate . '%')->count();

            $value = ['date' => date('j M', $i), 'open' => $created, 'closed' => $closed, 'reopened' => $reopened];
            $array = array_map('htmlentities', $value);
            $json  = html_entity_decode(json_encode($array));
            $return .= $json . ',';
        }
        $last  = rtrim($return, ',');
        $users = User::whereId($id)->first();

        return '[' . $last . ']';
    }

    /**
     * @category function
     * @param integer $id, string $date111. $date122
     * @return json respone
     */
    public function ManagerSearch(Request $request)
    {
        try {
            $term = trim($request->q);

            if (empty($term)) {
                return \Response::json([]);
            }
            if ($request->j) {

                $department_members = DepartmentAssignAgents::where('department_id', '=', $request->j)->pluck('agent_id')->toArray();

                if ($department_members) {

                    $users = \App\User::whereIn('id', $department_members)
                        ->where(function ($q) use ($term) {
                            $q->where('email', 'LIKE', '%' . $term . '%')
                                ->orWhere('user_name', 'LIKE', '%' . $term . '%')
                                ->orWhere('first_name', 'LIKE', '%' . $term . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $term . '%');
                        })
                        ->where('is_delete', '!=', 1)
                        ->where('active', '=', 1)
                        ->select('email', 'id', 'profile_pic', 'first_name', 'last_name')->get();

                    foreach ($users as $user) {
                        $formatted_users[] = ['id' => $user->id, 'text' => $user->email, 'profile_pic' => $user->profile_pic, 'first_name' => $user->first_name, 'last_name' => $user->last_name];
                    }

                } else {

                    $users = \App\User::where('role', '=', 'admin')
                        ->where(function ($q) use ($term) {
                            $q->where('email', 'LIKE', '%' . $term . '%')
                                ->orWhere('user_name', 'LIKE', '%' . $term . '%')
                                ->orWhere('first_name', 'LIKE', '%' . $term . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $term . '%');
                        })
                        ->where('is_delete', '!=', 1)
                        ->where('active', '=', 1)
                        ->select('email', 'id', 'profile_pic', 'first_name', 'last_name')->get();

                    foreach ($users as $user) {
                        $formatted_users[] = ['id' => $user->id, 'text' => $user->email, 'profile_pic' => $user->profile_pic, 'first_name' => $user->first_name, 'last_name' => $user->last_name];
                    }
                }
            } else {

                $users = \App\User::where('role', '=', 'admin')
                    ->where(function ($q) use ($term) {
                        $q->where('email', 'LIKE', '%' . $term . '%')
                            ->orWhere('user_name', 'LIKE', '%' . $term . '%')
                            ->orWhere('first_name', 'LIKE', '%' . $term . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $term . '%');
                    })
                    ->where('is_delete', '!=', 1)
                    ->where('active', '=', 1)
                    ->select('email', 'id', 'profile_pic', 'first_name', 'last_name')->get();

                foreach ($users as $user) {
                    $formatted_users[] = ['id' => $user->id, 'text' => $user->email, 'profile_pic' => $user->profile_pic, 'first_name' => $user->first_name, 'last_name' => $user->last_name];
                }
            }
            return \Response::json($formatted_users);
        } catch (\Exception $e) {
            // returns if try fails with exception meaagse
            return \Response::json([]);
        }
    }
}
