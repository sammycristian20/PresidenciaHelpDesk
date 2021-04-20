<?php

namespace App\Http\Controllers\Agent\helpdesk;

// controllers
use App\Facades\Attach;
use App\Helper\UserExportHelper;
use App\Http\Controllers\Agent\helpdesk\DeactivateUserController;
use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController as Notify;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Common\ExcelController;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Common\TicketsGraphController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\FormController;
use App\Http\Requests\helpdesk\AgentUpdate;
use App\Http\Requests\helpdesk\ChangePasswordRequest;
use App\Http\Requests\helpdesk\OtpVerifyRequest;
use App\Http\Requests\helpdesk\ProfilePassword;
use App\Http\Requests\helpdesk\ProfileRequest;
use App\Http\Requests\helpdesk\Requester\UserEditRequest;
use App\Http\Requests\helpdesk\Requester\UserRegisterRequest;
use App\Http\Requests\helpdesk\Sys_userRequest;
use App\Model\helpdesk\Agent\Assign_team_agent;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\DepartmentAssignManager;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Utility\CountryCode;
use App\User;
use App\UserAdditionalInfo;
use Auth;
use DateTime;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Input;
use Lang;

/**
 * UserController
 * This controller is used to CRUD an User details, and proile management of an agent.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class UserController extends Controller {
    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent.
     *
     * @return void
     */
    public function __construct(PhpMailController $PhpMailController)
    {
        $this->PhpMailController = $PhpMailController;
        // checking authentication
        //$this->middleware('auth');
        // checking if role is agent
        $this->middleware('role.agent', ['except' => ['createUserApi', 'createRequester']]);
    }

    /**
     * Display all list of the users.
     *
     * @param type User $user
     *
     * @return type view
     */
    public function index()
    {
        try {
            if (!User::has('access_user_profile')) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }

            return view('themes.default1.agent.helpdesk.user.index');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function deletedUser()
    {
        try {
            // dd('here');
            /* get all values in Sys_user */
            return view('themes.default1.agent.helpdesk.user.deleteduser');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * This function is used to display the list of users using chumper datatables.
     *
     * @return datatable
     */
    public function userList(Request $request)
    {
        $type = $request->input('profiletype');
        $search = $request->input('searchTerm');
        $agentAndUserIds = (Auth::user()->role == "admin") ? User::where('role', '!=', 'admin')->pluck('id')->toArray() : $this->agentAndUserIds();
        if ($type === 'agents') {
            $users = User::whereIn('id', $agentAndUserIds)->where('role', 'agent')->where('is_delete', 0);
        } elseif ($type === 'users') {
            $users = User::where('role', 'user')->where('is_delete', 0);
        } elseif ($type === 'active-users') {
            $users = User::whereIn('id', $agentAndUserIds)->where('active', 1)->where('is_delete', 0);
        } elseif ($type === 'inactive') {
            $users = User::whereIn('id', $agentAndUserIds)->where('active', 0);
        } elseif ($type === 'deleted') {
            $users = User::whereIn('id', $agentAndUserIds)->where('is_delete', 1);
        }  elseif ($type === 'mobile-unverified') {
            $users = User::whereIn('id', $agentAndUserIds)->where('mobile_verify', 0);
        } elseif ($type === 'mobile-verified') {
            $users = User::whereIn('id', $agentAndUserIds)->where('mobile_verify', 1);
        } elseif ($type === 'verified') {
            $users = User::whereIn('id', $agentAndUserIds)->where('active', 1);
        } elseif ($type === 'unverified') {
            $users = User::whereIn('id', $agentAndUserIds)->where('active', 0);
        } else {
            $users = User::whereIn('id', $agentAndUserIds);
        }
        $users = $users->select('first_name', 'user_name', 'email', 'mobile', 'active', 'updated_at', 'role', 'id', 'last_name', 'country_code', 'phone_number', 'email_verify', 'mobile_verify', 'is_delete');
        if ($search !== '') {
            $users = $users->where(function ($query) use ($search) {
                $query->where('user_name', 'LIKE', '%' . $search . '%');
                $query->orWhere('email', 'LIKE', '%' . $search . '%');
                $query->orWhere('first_name', 'LIKE', '%' . $search . '%');
                $query->orWhere('last_name', 'LIKE', '%' . $search . '%');
                $query->orWhere('mobile', 'LIKE', '%' . $search . '%');
                $query->orWhere('updated_at', 'LIKE', '%' . $search . '%');
                $query->orWhere('country_code', 'LIKE', '%' . $search . '%');
            });
        }

        // displaying list of users with chumper datatables
        return \DataTables::of($users)
                        /* column username */
                        ->removeColumn('id', 'last_name', 'country_code', 'phone_number')
                        ->editColumn('first_name', function ($model) {
                            if (isViewUserProfile($model->id)) {
                                return '<a  href="' . route('user.show', $model->id) . '" title="' . $model->fullName . '">' . str_limit($model->fullName, 10) . '</a>';
                            }

                            return '<span title="' . $model->fullName . '">' . str_limit($model->fullName, 10) . '</span>';
                        })
                        ->editColumn('user_name', function ($model) {
                            $user_name = $model->user_name;
                            if (strlen($model->user_name) > 15) {
                                $user_name = mb_substr($model->user_name, 0, 10, 'UTF-8') . '...';
                            }
                            if (isViewUserProfile($model->id)) {
                                return '<a  href="' . route('user.show', $model->id) . '" title="' . $model->user_name . '">' . $user_name . '</a>';
                            }
                            return '<span title="' . $model->user_name . '">' . $user_name . '</span>';
                        })
                        /* column email */
                        ->addColumn('email', function ($model) {

                            $email = $model->email;
                            if (strlen($model->email) > 15) {
                                $email = mb_substr($model->email, 0, 10, 'UTF-8') . '...';
                            }
                            if (isViewUserProfile($model->id)) {
                                return '<a  href="' . route('user.show', $model->id) . '" title="' . $model->email . '">' . $email . '</a>';
                            }
                            return '<span title="' . $model->email . '">' . $email . '</span>';
                        })
                        /* column phone */
                        ->addColumn('mobile', function ($model) {
                            $phone = '';
                            if ($model->phone_number) {
                                $phone = $model->ext . ' ' . $model->phone_number;
                            }
                            $mobile = '';
                            if ($model->mobile) {
                                $mobile = $model->mobile;
                            }
                            $phone = $phone . ' ' . $mobile;
                            return $phone;
                        })
                        /* column account status */
                        ->addColumn('active', function ($model) {

                            return $this->userStatus($model);
                        })
                        /* column last login date */
                        ->addColumn('updated_at', function ($model) {
                            $t = $model->updated_at;
                            return faveoDate($t);
                        })
                        /* column Role */
                        ->addColumn('role', function ($model) {
                            if ($model->role === 'agent') {
                                return '<p class="btn btn-xs btn-default" style="pointer-events:none"><a href="#"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;' . ucfirst($model->role) . '</a></p>';
                            }
                            if ($model->role === 'user') {
                                return '<p class="btn btn-xs btn-default" style="pointer-events:none"><a href="#" style="color:black"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;' . ucfirst($model->role) . '</a></p>';
                            }
                        })

                        /* column actions */
                        ->addColumn('actions', function ($model) {
                            if (Auth::user()->role == 'admin' || $model->role == "user") {
                                return '<a href="' . route('user.edit', $model->id) . '" class="btn btn-primary btn-xs"><i class="fa fa-edit" style="color:white;">&nbsp;</i>' . \Lang::get('lang.edit') . '</a>&nbsp; <a href="' . route('user.show', $model->id) . '" class="btn btn-primary btn-xs"><i class="fa fa-eye" style="color:white;">&nbsp;</i>' . \Lang::get('lang.view') . '</a>';
                            } else {

                                if (isViewUserProfile($model->id)) {

                                    return '<a href="' . route('user.show', $model->id) . '" class="btn btn-primary btn-xs">' . \Lang::get('lang.view') . '</a>';
                                }
                            }
                        })
                        ->rawColumns(['first_name', 'user_name', 'email', 'active', 'actions', 'role', 'mobile'])
                        ->make();
    }

    /**
     * Show the form for creating a new users.
     *
     * @return type view
     */
    public function create(CountryCode $code)
    {
        try {
            if (!User::has('access_user_profile')) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }
            $aoption = getAccountActivationOptionValue();
            $email_mandatory = CommonSettings::select('status')->where('option_name', '=', 'email_mandatory')->first();
            $org = Organization::all();
            return view('themes.default1.agent.helpdesk.user.create', compact('org', 'email_mandatory', 'aoption'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Store a newly created users in storage.
     *
     * @param type User            $user
     * @param type Sys_userRequest $request
     *
     * @return type redirect
     */
    public function store(User $user, Sys_userRequest $request)
    {
        try {
            /* insert the input request to sys_user table */
            /* Check whether function success or not */
            // dd($request->org_id);
            if ($request->org_id != "") {
                $org_id = Organization::where('id', '=', $request->org_id)->select('id')->first();
                // dd($org_id);
                // ->route('user.edit', $id)
                if ($org_id == null) {
                    return redirect()->back()->with('fails', Lang::get('lang.please_type_correct_organization_name'));
                }
            }
            if ($request->input('email') != '') {
                $user->email = $request->input('email');
            } else {
                $user->email = null;
            }
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->user_name = strtolower($request->input('user_name'));
            if ($request->input('mobile') != '') {
                $user->mobile = $request->input('mobile');
            } else {
                $user->mobile = null;
            }
            $user->ext = $request->input('ext');
            $user->phone_number = $request->input('phone_number');
            $user->country_code = $request->input('country_code');
            $user->active = $request->input('active');
            $user->internal_note = $request->input('internal_note');
            $password = $this->generateRandomString();
            $user->password = Hash::make($password);
            $user->role = 'user';
            $active_code = '';
            if ($request->get('active') == 0) {
                $active_code = str_random(60);
                $user->email_verify = $active_code;
            }
            $user->mobile_verify = 'verifymobileifenable';
            if ($request->get('country_code') == '' && $request->get('mobile') != '') {
                return redirect()->back()->with(['fails' => Lang::get('lang.country-code-required-error'), 'country_code_error' => 1])->withInput();
            } else {
                $code = CountryCode::select('phonecode')->where('phonecode', '=', $request->get('country_code'))->get();
                if (!count($code)) {
                    return redirect()->back()->with(['fails' => Lang::get('lang.incorrect-country-code-error'), 'country_code_error' => 1])->withInput();
                }
            }
            // save user credentails
            if ($user->save() == true) {
                if ($request->input('org_id') != "") {
                    // $org_id = Organization::where('name', '=', $request->org_id)->select('id')->first();
                    // $add_user_from_org->org_id= $org_id->id;
                    $orgid = $request->org_id;
                    // $orgid = $request->input('org_id');
                    foreach ($orgid as $key => $value) {
                        $role = 'members';
                        $user_assign_organization = new User_org;
                        $user_assign_organization->org_id = $value;
                        $user_assign_organization->user_id = $user->id;
                        $user_assign_organization->role = $role;
                        $user_assign_organization->save();
                        // $this->storeUserOrgRelation($user->id, $value, $role);
                    }
                }
                // fetch user credentails to send mail
                $name = $user->first_name;
                $email = $user->email;
                // send mail on registration
                $notification[] = [
                    'registration_notification_alert' => [
                        'userid' => $user->id,
                        'from' => $this->PhpMailController->mailfrom('1', '0'),
                        'message' => ['subject' => null, 'scenario' => 'registration-notification'],
                        'variable' => ['new_user_name' => $name, 'new_user_email' => $email, 'user_password' => $password]
                    ],
                    'new_user_alert' => [
                        'model' => $user,
                        'userid' => $user->id,
                        'from' => $this->PhpMailController->mailfrom('1', '0'),
                        'message' => ['subject' => null, 'scenario' => 'new-user'],
                        'variable' => ['new_user_name' => $name, 'new_user_email' => $email, 'user_profile_link' => faveoUrl('user/' . $user->id)]
                    ],
                ];
                if ($active_code != '') {
                    $notification[] = [
                        'registration_alert' => [
                            'userid' => $user->id,
                            'from' => $this->PhpMailController->mailfrom('1', '0'),
                            'message' => ['subject' => null, 'scenario' => 'registration'],
                            'variable' => ['new_user_name' => $name, 'new_user_email' => $request->input('email'), 'account_activation_link' => faveoUrl('account/activate/' . $active_code)],
                        ]
                    ];
                }
                $notify = new Notify();
                if (!$request->input('email')) {
                    $notify->setParameter('send_mail', false);
                }
                $notify->setDetails($notification);
                \Event::dispatch(new \App\Events\LoginEvent($request));
                return redirect('user')->with('success', Lang::get('lang.user-saved-successfully'));
            }
            /* redirect to Index page with Success Message */
            return redirect()->back()->with('success', Lang::get('lang.user-saved-successfully'));
        } catch (\Exception $e) {
            /* redirect back with Fails Message */
            return redirect()->back()->with('fails', $e->getMessage())->withInput();
        }
    }

      /**
     * This method return random password
     * @return json
     */
    public function randomPassword()
    {
        try {
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890~!@#$%^&*(){}[]';
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 10; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            return successResponse('', implode($pass));
        } catch (Exception $ex) {
            /* redirect to Index page with Fails Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Random Password Generator for users
     *
     * @param type int  $userId
     * @return Response
     */
    public function randomPostPassword($userId, ChangePasswordRequest $request)
    {
        try {
            $user = User::whereId($userId)->first();
            $password = $request->change_password;
            $user->password = Hash::make($password);
            $user->save();
            $name = $user->first_name;
            $email = $user->email;
            $this->PhpMailController->sendmail($from = $this->PhpMailController
                    ->mailfrom('1', '0'), ['name' => $name, 'email' => $email],['subject' => null, 'scenario' => 'reset_new_password'],['user' => $name, 'user_password' => $password]);
            return successResponse(Lang::get('lang.password_change_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }


    /**
     * This method changing role to Admin
     * @param int $userId
     * @param Request $request
     *@return Response
     */
    public function changeRoleAdmin($userId, Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return errorResponse(trans('lang.only_admin_can_change_role_to_admin'));
        }
        try {
            $user = User::whereId($userId)->first();
            $user->role = 'admin';
            if(!$user->agent_tzone)
            {
                $user->agent_tzone = getGMT(true);
            }

            if (!$request->primary_department) {
                return errorResponse(Lang::get('lang.please_select_department'));
            }
            $user->departments()->sync($request->primary_department);

            $user->save();

            return successResponse(Lang::get('lang.role_change_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }
    /**
     * This method changing role to Agent
     * @param int $userId
     * @param Request $request
     * @return Response
     */
    public function changeRoleAgent($userId, Request $request)
    {
        try {
            $user = User::whereId($userId)->first();
            $user->role = 'agent';
            $user->agent_tzone = !$user->agent_tzone ? getGMT(true) : $user->agent_tzone;
            if (!$request->primary_department) {
                return errorResponse(Lang::get('lang.please_select_department'));
            }

            $user->departments()->sync($request->primary_department);

            $user->save();
            return successResponse(Lang::get('lang.role_change_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }
    /**
     * This method change role to user
     * @param int $userId of user
     * @return Response
     */
    public function changeRoleUser($userId)
    {
        try {
            $user = User::whereId($userId)->first();
            $user->permissions()->detach();
            $user->role = 'user';
            $user->assign_group = NULL;
            $user->primary_dpt = NULL;
            $user->save();
          return successResponse(trans('lang.role_change_successfully'));
        } catch (Exception $ex) {
            /* redirect to Index page with Fails Message */
            return errorResponse($ex->getMessage());
        }
    }



    /**
     * Display the specified user/agent.
     *
     * @param type int  $id
     *
     * @return type html
     */
    public function show($id)
    {
        try {

            $checkViewPermission = isViewUserProfile($id);

            if (Auth::user()->id != $id) {
                if (!User::has('access_user_profile') || !$checkViewPermission) {

                    return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
                }
            }
           $users = User::where('id', $id)->first();

            if ($users) {
                return view('themes.default1.agent.helpdesk.user.show');
            } else {
                return redirect()->back()->with('fails', Lang::get('lang.user-not-found'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param type int  $id
     * @param type User $user
     *
     * @return type Response
     */
    public function edit($id)
    {
        try {
            if (!User::has('access_user_profile')) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }

            $checkUserRole = User::where('id', $id)->value('role');
            if ((Auth::user()->role == "agent" && Auth::user()->id == $id) || (Auth::user()->role == "agent" && $checkUserRole != "user")) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }

            $email_mandatory = CommonSettings::select('status')->where('option_name', '=', 'email_mandatory')->first();
            $aoption = getAccountActivationOptionValue();
            $user = User::where('id', '=', $id)->first();
            if ($user) {
                $country_code = "auto";
                $country = CountryCode::select('iso')->where('phonecode', '=', $user->country_code)->first();
                if ($country && $user->country_code != 0) {
                    $country_code = $country->iso;
                }
                if ($user->role == 'admin' || $user->role == 'agent') {
                    $team = Teams::where('status', '=', 1)->get();
                    $teams1 = $team->pluck('name', 'id');
                    //use helper function
                    $timezones = timezoneFormat();

                    // $groups = Groups::where('group_status', '=', 1)->get();
                    $departments = Department::get();
                    $dept = DepartmentAssignAgents::where('agent_id', '=', $id)->pluck('department_id')->toArray();
                    $table = Assign_team_agent::where('agent_id', $id)->first();
                    $teams = $team->pluck('id', 'name')->toArray();
                    $assign = Assign_team_agent::where('agent_id', $id)->pluck('team_id')->toArray();
                    return view('themes.default1.agent.helpdesk.user.agentedit', compact('teams', 'assign', 'table', 'teams1', 'user', 'timezones', 'departments', 'dept', 'team', 'country_code', 'aoption'));
                } else {
                    $users = $user;
                    $orgs = Organization::all();
                    $organization = User_org::where('user_id', '=', $id)->where('role', '=', 'members')->pluck('org_id')->toArray();
                    $term = preg_replace('/.+@/', '', $users->email);
                    $query = DB::table('organization')
                                    ->whereRaw('FIND_IN_SET(?,domain)', [$term])
                                    ->pluck('id as org_id')->toArray();
                    $check_org = array_merge($organization, $query);
                    $organization_id = array_unique($check_org);
                    // dd($organization_id );
                    return view('themes.default1.agent.helpdesk.user.edit', compact('users', 'orgs', 'email_mandatory', 'organization_id', 'country_code', 'aoption'));
                }
            } else {
                return redirect()->back()->with('fails', Lang::get('lang.user-not-found'));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

// orgAutofill
    /**
     * Update the specified user in storage.
     *
     * @param type int            $id
     * @param type User           $user
     * @param type Sys_userUpdate $request
     *
     * @return type Response
     */
    public function update($id, AgentUpdate $request)
    {
        try {
            if (!User::has('access_user_profile')) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }

            if ($request->get('country_code') == '' && $request->get('mobile') != '') {
                return redirect()->back()->with(['fails' => Lang::get('lang.country-code-required-error'), 'country_code' => 1])->withInput();
            } else {
                $code = CountryCode::select('phonecode')->where('phonecode', '=', $request->get('country_code'))->get();
                if (!count($code)) {
                    return redirect()->back()->with(['fails' => Lang::get('lang.incorrect-country-code-error'), 'country_code' => 1])->withInput();
                }
            }
            $user = User::whereId($id)->first();
            $old_mobile_number = $user->mobile;
            if ($request->input('country_code') != '' or $request->input('country_code') != null) {
                $user->country_code = $request->input('country_code');
            }
            $user->mobile = ($request->input('mobile') == '') ? null : $request->input('mobile');
            $user->fill($request->except('daylight_save', 'limit_access', 'directory_listing', 'vocation_mode', 'assign_team', 'mobile'));
            if ($user->role != 'user') {

                //saving agent specific details
                // storing all the details
                $daylight_save = $request->input('daylight_save');
                $limit_access = $request->input('limit_access');
                $directory_listing = $request->input('directory_listing');
                $vocation_mode = $request->input('vocation_mode');
                //==============================================
                $table = Assign_team_agent::where('agent_id', $id);
                $table->delete();
                $requests = $request->input('team');
                if ($requests != null) {
                    // inserting team details
                    foreach ($requests as $req) {
                        DB::insert('insert into team_assign_agent (team_id, agent_id) values (?,?)', [$req, $id]);
                    }
                }
                $user->assign_group = $request->group;
                $permission = $request->input('permission');
                $user->permision()->updateOrCreate(['user_id' => $user->id], ['permision' => json_encode($permission)]);
                $delete_dept = DepartmentAssignAgents::where('agent_id', '=', $id)->delete();
                $primary_dpt = $request->primary_department;
                foreach ($primary_dpt as $primary_dpts) {
                    $dept_assign_agent = new DepartmentAssignAgents;
                    $dept_assign_agent->agent_id = $user->id;
                    $dept_assign_agent->department_id = $primary_dpts;
                    $dept_assign_agent->save();
                }
                $user->primary_dpt = $primary_dpt[0];
                $user->agent_tzone = $request->agent_time_zone;

                $this->typeRelation($user, $request->input('type'));
            } else {
                //saving client specific details
                if ($request->org_id != "") {
                    $org_id = Organization::where('id', '=', $request->org_id)->select('id')->first();
                    if ($org_id == null) {
                        return redirect()->route('user.edit', $id)->with('fails', Lang::get('lang.please_type_correct_organization_name'));
                    }
                }
                if ($request->input('org_id') == "") {
                    $delete_user_from_org = User_org::where('user_id', '=', $id)->delete();
                } else {
                    $delete_user_from_org = User_org::where('user_id', '=', $id)->delete();
                    $orgid = $request->org_id;
                    foreach ($orgid as $key => $value) {
                        $role = 'members';
                        $user_assign_organization = new User_org;
                        $user_assign_organization->org_id = $value;
                        $user_assign_organization->user_id = $user->id;
                        $user_assign_organization->role = $role;
                        $user_assign_organization->save();
                    }
                }
            }
            if ($old_mobile_number != $request->input('mobile')) {
                $user->mobile_verify = "verifymobileifenable";
            }
            $user->save();
            $this->removeGlobalAccess($id);
            return redirect()->back()->with('success', Lang::get('lang.User-profile-Updated-Successfully'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * get agent profile page.
     *
     * @return type HTML
     */
    public function getProfile()
    {

        $user = Auth::user();
        try {
            return view('themes.default1.agent.helpdesk.user.profile', compact('user'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Fetching auth user info.
     *
     * @return type Json
     */
    public function getProfileInfo(){
        try{
        $userInfo = User::where('id', Auth::user()->id)->
                with(['departments:department.id,name',
                'timezone:id,name',
                'teams:teams.id,name',
                'types:ticket_type.id,name',
                'location:location.id,title as name',
             ])
        ->select('id', 'first_name', 'last_name', 'user_name', 'location', 'role', 'email', 'ext','country_code','phone_number','mobile','agent_tzone','role','profile_pic','agent_tzone', 'agent_sign','internal_note','email_verify','not_accept_ticket' ,'google2fa_secret','is_2fa_enabled','google2fa_activation_date','iso')->first()->toArray();
        return successResponse('',$userInfo);

        } catch (Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * get profile edit page.
     *
     * @return type view
     */
    public function getProfileedit(CountryCode $code)
    {
        $user = Auth::user();
        $country_code = "auto";
        $code = CountryCode::select('iso')->where('phonecode', '=', $user->country_code)->first();
        if ($code && $user->country_code != 0) {
            $country_code = $code->iso;
        }
        $aoption = getAccountActivationOptionValue();
        try {
            return view('themes.default1.agent.helpdesk.user.profile-edit', compact('user', 'country_code', 'aoption'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Post profile edit.
     *
     * @param  ProfileRequest $request
     *
     * @return  json
     */
    public function profileEdit(ProfileRequest $request)
    {
        try {

            // geet authenticated user details
            $user = Auth::user();
            if ($request->get('country_code') == '' && $request->get('mobile') != '') {
                return redirect()->back()->with(['fails' => Lang::get('lang.country-code-required-error'), 'country_code_error' => 1])->withInput();
            } else {
                $code = CountryCode::select('phonecode')->where('phonecode', '=', $request->get('country_code'))->get();
                if (!count($code)) {
                    return redirect()->back()->with(['fails' => Lang::get('lang.incorrect-country-code-error'), 'country_code_error' => 1])->withInput();
                }
                $user->country_code = $request->country_code;
            }
            $user->fill($request->except('profile_pic', 'mobile'));
            $user->location = $request->input('location');
            $user->save();

            if ($request->file('profile_pic')) {
                $path = Attach::put('profile', $request->file('profile_pic'), null, null, true, 'public');
                $user->profile_pic = Attach::getUrlForPath($path, null, 'public');
            }

            $user->mobile = $request->get('mobile') ? : NULL;
            $user->save();


            return successResponse(Lang::get('lang.Profile-Updated-sucessfully'));

        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Post profile password.
     *
     * @param type ProfilePassword $request
     *
     * @return Message
     */
    public function postProfilePassword(ProfilePassword $request)
    {
        try {

          // get authenticated user
           $user = Auth::user();
           // checking if the old password matches the new password
          if (Hash::check($request->input('old_password'), $user->getAuthPassword())) {

            $user->password = Hash::make($request->input('new_password'));
            $user->save();
            return successResponse(Lang::get('lang.password_updated_sucessfully'));
          } else {
            return errorResponse(Lang::get('lang.password_was_not_updated_incorrect_old_password'));
          }
        } catch (Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Assigning an user to an organization.
     *
     * @param int $userId
     *
     * @return Response
     */
    public function userAssignOrg(Request $request, $userId)
    {
        try {
            $orgIdLists = $request->org ?: $request->organisation;
            $orgDeptId = $request->org_dept ?: $request->organisation_department;
            /////////////////handle organization/////////////
            $exitingOrgId = User_org::where('user_id', $userId)->pluck('org_id')->toArray();
            $currentOrgId = $orgIdLists;
            User_org::where('user_id', $userId)->whereIn('org_id', array_diff($exitingOrgId, $currentOrgId))->delete();



            foreach ($orgIdLists as $orgId) {

                $checkUser = User_org::where('user_id', $userId)->where('org_id', $orgId)->count();
                if (!$checkUser) {
                    User_org::Create(['org_id' => $orgId, 'user_id' => $userId, 'role' => 'members']);
                }
            }

            ///////handle organization department//////////
            if ($orgDeptId) {
                $orgIds = OrganizationDepartment::where('id', $orgDeptId)->value('org_id');
                if (!in_array($orgIds, $orgIdLists)) {
                    User_org::where('user_id', $userId)->update(['org_department' => null]);
                }
                User_org::where('user_id', $userId)->where('org_id', $orgIds)->update(['org_department' => $orgDeptId]);
            }
            return successResponse(Lang::get('lang.organization_assigned_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     *
     * @param type $id
     * @return int
     */
    public function UsereditAssignOrg($id)
    {
        $org_name = Input::get('org');
        if ($org_name) {
            $org = Organization::where('name', '=', $org_name)->pluck('id')->first();
            if ($org) {
                $user_org = User_org::where('user_id', '=', $id)->first();
                $user_org->org_id = $org;
                $user_org->user_id = $id;
                $user_org->role = 'members';
                $user_org->save();
                return 1;
            } else {
                return 0;
            }
        } else {
            return 2;
        }
    }

    /**
     *
     * @param type $id
     * @return int
     */
    public function orgAssignUser($id)
    {
        $org = Input::get('org');
        $user_org = new User_org();
        $user_org->org_id = $id;
        $user_org->user_id = $org;
        $user_org->role = 'members';
        $user_org->save();
        return 1;
    }

    /**
     * This method delete user organization and organization department
     * @param int $userId of user
     * @param int $orgId of organization id
     * @return Response
     */
    public function removeUserOrg($userId, $orgId)
    {
        try {
            $orgDept = User_org::where('user_id', $userId)->where('org_id', $orgId)->value('org_department');
            if ($orgDept) {
                UserAdditionalInfo::where('owner', $userId)->where('key', 'department')->update(['value' => null]);
            }
            User_org::where('user_id', $userId)->where('org_id', $orgId)->delete();
            return successResponse(Lang::get('lang.the_user_has_been_removed_from_this_organization'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * creating an organization in user profile page via modal popup.
     * @param type $id
     * @return type int
     */
    public function userCreateOrganization($id)
    {
        // checking if the entered value for website is available in database
        if (Input::get('website') != null) {
            // checking website
            $check = Organization::where('website', Input::get('website'))->first();
        } else {
            $check = null;
        }
        // checking if the name is unique
        $check2 = Organization::where('name', Input::get('name'))->first();
        // if any of the fields is not available then return false
        if (\Input::get('name') == null) {
            return 'Name is required';
        } elseif ($check2 != null) {
            return 'Name should be Unique';
        } elseif ($check != null) {
            return 'Website should be Unique';
        } else {
            // storing organization details and assigning the current user to that organization
            $org = new Organization();
            $org->name = Input::get('name');
            $org->phone = Input::get('phone');
            $org->website = Input::get('website');
            $org->address = Input::get('address');
            $org->internal_notes = Input::get('internal');
            $org->save();

            User_org::create(['org_id' => $org->id, 'user_id' => $id, 'role' => 'members']);
            // for success return 0
            return 0;
        }
    }

    /**
     * Generate a random string for password.
     *
     * @param type $length
     *
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        // list of supported characters
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // character length checked
        $charactersLength = strlen($characters);
        // creating an empty variable for random string
        $randomString = '';
        // fetching random string
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        // return random string
        return $randomString;
    }

    /**
     *
     * @param type $userid
     * @param type $orgid
     * @param type $role
     */
    public function storeUserOrgRelation($userid, $orgid, $role)
    {
        $org_relations = new User_org();
        $org_relation = $org_relations->where('user_id', $userid)->first();
        if ($org_relation) {
            $org_relation->delete();
        }
        $org_relations->create([
            'user_id' => $userid,
            'org_id' => $orgid,
            'role' => $role,
        ]);
    }

    /**
     *
     * @return type
     */
    public function getExportUser()
    {
        try {
            return view('themes.default1.agent.helpdesk.user.export');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     *This method for download user data as a excel sheet
     * @param Request $request
     * @return type
     */
    public function exportUser(Request $request)
    {
        try {
            $firstDate = $this->convertDate($request->start_date . " 00:00:00");
            $secondDate = $this->convertDate($request->end_date . " 23:59:59");

            $user = User::whereBetween('users.created_at', [$firstDate, $secondDate])->where('role','!=','admin')->count();

            //checking user/agent present in data range
            if(!$user)
            {
                return errorResponse( Lang::get('lang.no_data_found'));
            }

            $users = $this->getUsers($firstDate, $secondDate);
            $date = $request->start_date . '-' . $request->end_date;
            $filename = "users" . $date;
            return (new ExcelController())->export($filename, $users);
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     *
     * @param type $date
     * @return type
     */
    public function convertDate($date)
    {
        $converted_date = date('Y-m-d H:i:s', strtotime($date));
        return $converted_date;
    }

/**
     *
     * @param string $first
     * @param string $last
     * @return type array
     */
    public function getUsers($first, $last)
    {
        $user = new User();
        $users = $user
                ->whereBetween('users.created_at', [$first, $last])
                ->where('users.role', '!=', 'admin')
                ->select('users.id','users.user_name', 'users.email', 'users.first_name', 'users.last_name','users.role', 'users.active')
                ->get();
                foreach ($users as $user) {
                    $organizationId = User_org::where('user_id',$user->id)->pluck('org_id')->toArray();
                    $orgName = Organization::whereIn('id',$organizationId)->pluck('name')->toArray();
                    $active = $user->active ? 'YES' :'NO';
                    $outputData[] = (['Username'=>$user->user_name,'Email'=>$user->email,'Fisrtname'=>$user->first_name,'Lastname'=>$user->last_name,'Role'=>$user->role,'Active'=>$active,'Organization'=>implode(',', $orgName)]);
                }
        return $outputData;
    }

    /**
     *
     * @param OtpVerifyRequest $request
     * @return string|int
     */
    public function resendOTP(OtpVerifyRequest $request)
    {
        if (\Schema::hasTable('sms')) {
            $sms = DB::table('sms')->get();
            if (count($sms) > 0) {
                \Event::dispatch(new \App\Events\LoginEvent($request));
                return 1;
            }
        } else {
            return "Plugin has not been setup successfully.";
        }
    }

    /**
     *
     * @return int
     */
    public function verifyOTP()
    {
        $user = User::select('id', 'mobile', 'country_code', 'user_name', 'mobile_verify', 'updated_at')->where('id', '=', Input::get('u_id'))->first();
        $otp = Input::get('otp');
        if ($otp != null || $otp != '') {
            $otp_length = strlen(Input::get('otp'));
            if (($otp_length == 6 && !preg_match("/[a-z]/i", Input::get('otp')))) {
                $otp2 = Hash::make(Input::get('otp'));
                $date1 = date_format($user->updated_at, "Y-m-d h:i:sa");
                $date2 = date("Y-m-d h:i:sa");
                $time1 = new DateTime($date2);
                $time2 = new DateTime($date1);
                $interval = $time1->diff($time2);
                if ($interval->i > 10 || $interval->h > 0) {
                    $message = Lang::get('lang.otp-expired');
                    return $message;
                } else {
                    if (Hash::check(Input::get('otp'), $user->mobile_verify)) {
                        User::where('id', '=', $user->id)
                                ->update([
                                    'mobile_verify' => '1',
                                    'mobile' => Input::get('mobile'),
                                    'country_code' => str_replace('+', '', Input::get('country_code'))
                        ]);
                        return 1;
                    } else {
                        $message = Lang::get('lang.otp-not-matched');
                        return $message;
                    }
                }
            } else {
                $message = Lang::get('lang.otp-invalid');
                return $message;
            }
        } else {
            $message = Lang::get('lang.otp-not-matched');
            return $message;
        }
    }

    /**
     *
     * @param Request $request
     */
    public function getAgentDetails(Request $request)
    {
        // $ids=$request->ticket_id;
        // $ticket_dept_ids = Tickets::whereIn('id',$ids)->pluck('dept_id')->unique();
        // foreach($ticket_dept_ids as $id){
        //     $users_ids = DepartmentAssignAgents::where('department_id',$id);
        // }
        // $agent_ids = $users_ids->pluck('agent_id')->unique()->toArray();
        // $users_ids = DepartmentAssignAgents::whereIn('department_id',$ticket_dept_ids)->pluck('agent_id')->unique()->toArray();
        // $agents = User::whereIn('id',$agent_ids)->select('id','user_name','first_name','last_name')->get();
        $assignto_agent = User::where('role', '!=', 'user')->select('id', 'user_name', 'first_name', 'last_name')->where('active', '=', 1)->orderBy('first_name')->get();
        $count_assign_agent = count($assignto_agent);
        $teams = Teams::where('status', '=', '1')->where('team_lead', '!=', null)->get();
        $count_teams = count($teams);
        $html111 = "";
        $html11 = "";
        $html1 = "";
        // $html111.= "<option value=' '>" . 'select assigner' . "</option>";
        foreach ($assignto_agent as $agent) {
            $html1 .= "<option value='user_" . $agent->id . "'>" . $agent->first_name . ' ' . $agent->last_name . "</option>";
        }
        $html11 .= "<optgroup label=" . 'Agents(' . $count_assign_agent . ')' . ">";
        $html22 = "";
        $html2 = "";
        foreach ($teams as $team) {
            $html2 .= "<option value='team_" . $team->id . "'>" . $team->name . "</option>";
        }
        $html22 .= "<optgroup label=" . 'Teams(' . $count_teams . ')' . ">";
        echo $html11, $html1, $html22, $html2;
        //     if($agents){
        //     foreach ($agents as $user) {
        //          echo "<option value='user_$user->id'>".$user->name().'</option>';
        //     }
        // }
        // else{
        // }
    }

    /**
     * This method return user information
     *
     * @param  int  $userId
     * @return type json
     */
    public function getUserInfo($userId)
    {
        try {
            $checkViewPermission = isViewUserProfile($userId);

            if (Auth::user()->id != $userId) {
                if (!User::has('access_user_profile') || !$checkViewPermission) {

                    return errorResponse(Lang::get('lang.permission_denied'));
                }
            }

            $userRole = User::where('id', $userId)->value('role');
            //if role user can't view agent/{userid}
            if($userRole == 'user' && str_contains(url()->previous(), 'agent'))

            {
            return errorResponse(Lang::get('lang.you_cant_view_user_as_a_agent'));
            }

           $userInfo = User::where('id', $userId)->
                with([
                'departments:department.id,name',
                'timezone:id,name',
                'organizations:organization.id,name',
                'teams:teams.id,name',
                'types:ticket_type.id,name',
                'customFieldValues',
                'location:location.id,title'

            ])
            ->select('id', 'first_name', 'last_name', 'user_name', 'location', 'role', 'email', 'ext', 'active','is_delete','ext','country_code','phone_number','mobile','agent_tzone','role','profile_pic','country_code', 'mobile', 'phone_number', 'active',
                'agent_tzone', 'agent_sign','mobile_verify','email_verify','internal_note','is_2fa_enabled', 'processing_account_disabling','iso')->first()->toArray();

            $orgDeptStatus = CommonSettings::where('option_name', 'micro_organization_status')->value('status');

            $userInfo['OrganizationDepartmentStatus'] = $orgDeptStatus ? true : false;

            $orgDepts = User_org::where('user_id', $userId)->pluck('org_department')->toArray();



            $userInfo['organization_dept'] = $orgDepts ? OrganizationDepartment::whereIN('id', $orgDepts)->select('id', 'org_deptname as name')->get() : [];

           return successResponse('', $userInfo);

        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }
    /**
     *
     * @param Request $request
     * @return type
     */
    public function settingsUpdateMobileVerify(Request $request)
    {
        try {
            if (!User::has('mobile_verification')) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }
            $user_id = $request->user_id;
            $user_ban = $request->settings_ban;
            User::where('id', $user_id)->update(['mobile_verify' => $user_ban]);
            return Lang::get('lang.your_status_updated_successfully');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * @return type
     */
    public function orgAutofill()
    {

        return view('themes.default1.agent.helpdesk.user.orgautocomplite');
    }

    /**
     *
     * @param Request $request
     * @return type json
     */
    public function createRequester(Request $request, $type = null)
    {
        $this->validate($request, [
            'email' => 'required|unique:users,email|unique:emails,email_address',
                //   'full_name'     => 'required'
        ]);
        try {
            $auth_control = new \App\Http\Controllers\Auth\AuthController();
            $user_create_request = new \App\Http\Requests\helpdesk\RegisterRequest();
            $user = new User();
            $password = str_random(8);
            $all = $request->all() + ['password' => $password, 'password_confirmation' => $password];
            $user_create_request->replace($all);
            $response = $auth_control->postRegister($user, $user_create_request)->toArray();

            if ($request->company) {
                $orgids = $request->company;
                foreach ($orgids as $orgid) {
                    $role = 'members';
                    $user_assign_organization = new User_org;
                    $user_assign_organization->org_id = $orgid;
                    $user_assign_organization->user_id = $user->id;
                    $user_assign_organization->role = $role;
                    $user_assign_organization->save();
                }
            }
            //associate user to organization base on domain match
            $this->domainConnection($user->id);
            $status = 200;
        } catch (\Exception $e) {
            \Logger::exception($e);
            $response = ['error' => [$e->getMessage()]];
            $status = 500;
            return response()->json($response, $status);
        }

        if ($type == 'batch-ticket') {
            return $response;
        }
        return response()->json(compact('response'), $status);
    }

    /**
     *
     * @param Request $request
     * @return type json
     */
    public function getRequesterForCC(Request $request)
    {
        try {
            $this->validate($request, [
                'term' => 'required',
            ]);
            $term = $request->input('term');
            $requester = User::where('active', 1)
                    ->where('is_delete', 0)
                    ->whereNotNull('email')
                    ->where('email', 'LIKE', '%' . $term . '%')
                    ->select('id', 'user_name', 'email', 'first_name', 'last_name', 'profile_pic')
                    ->get();
            $response = $requester->toArray();
            $status = 200;
        } catch (\Exception $e) {
            $response = ['error' => [$e->getMessage()]];
            $status = 500;
            return response()->json($response, $status);
        }
        return response()->json(compact('response'), $status);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return type json
     */
    public function find(Request $request)
    {
        try {
            if ($request->q) {
                $term = trim($request->q);
                if (empty($term)) {
                    return \Response::json([]);
                }
            }
            if ($request->term) {
                $term = $request->term;
            }
            $orgs = Organization::where('name', 'LIKE', '%' . $term . '%')->select('id', 'name')->get();
            $formatted_tags = [];
            foreach ($orgs as $org) {
                $formatted_orgs[] = ['id' => $org->id, 'text' => $org->name];
            }
            return \Response::json($formatted_orgs);
        } catch (Exception $e) {
            // returns if try fails with exception meaagse
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * This method search organization department
     * @param \Illuminate\Http\Request $request
     * @return type json
     */
    public function findOrgDepartment(Request $request)
    {
        try {
            if (!$request->org_id) {
                return errorResponse(Lang::get('lang.organization_is_not_present'));
            }
            $limit = $request->input('limit');
            $orgId = explode(",", $request->org_id);
            $searchQuery = $request->input('search-query') ?: '';
            $formattedOrgsDept = OrganizationDepartment::join('organization', function ($q) {
                    $q->on('organization.id', '=', 'organization_dept.org_id');
                })
                ->whereIn('organization.id', $orgId)
                ->where('organization_dept.org_deptname', 'LIKE', '%' . $searchQuery . '%')
                ->select("organization_dept.id", "organization_dept.org_id", "organization_dept.org_deptname", "organization.name")
                ->paginate($limit);

            $formattedOrgsDept->transform(function ($formattedOrg){
                    $display = $formattedOrg['org_deptname'] . "(" . $formattedOrg['name'] . ")";
                    return (object)['id' => $formattedOrg['id'], 'name' => $display];
            });

            return successResponse('', $formattedOrgsDept);

        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method for create user information
     * @param \App\Http\Requests\helpdesk\RegisterRequest $request
     * @return type
     */
    public function createUserApi(UserRegisterRequest $request)
    {
        try {
            \Event::dispatch('user-form-submitted', [&$request]);
            $user = (new AuthController())->postRegister(new User(), $request);
            // $authController->postRegister($user, $request, true, true);
            $user->internal_note = $request->address;
            $user->save();
            if ($request->organisation) {
                $orgid = $request->organisation;
                foreach ($orgid as $key => $value) {
                    User_org::create(['org_id' => $value, 'user_id' => $user->id, 'role' => 'members']);
                }
            }
            if ($request->organisation_department && $request->organisation_department != 'null') {
                $orgDept = OrganizationDepartment::where('id', $request->organisation_department)->select('org_id')->first();
                User_org::where('user_id', $user->id)->where('org_id', $orgDept->org_id)->update(['org_department' => $request->organisation_department]);
            }

            //associate user to organization base on domain match
            $this->domainConnection($user->id);

            $user->name = $user->meta_name;

            // giving user data in response so that it can be used to grab user information to populate at frontend
            return successResponse(Lang::get('lang.registered_successfully'), $user);
        } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     *
     * @param int $id
     * @return  json
     */
    public function editUserApi($id)
    {
        try {
            $user = User::where('id', $id)->first();

            if($user->role != 'user'){

                return errorResponse(Lang::get('lang.user_not_found'));
            }

            $customFields = CustomFormValue::getCustomFields($user);

            $userAssignOrganizationId = User_org::where('user_id', $id)->pluck('org_id')->toArray();
            $organisations = Organization::whereIn('id', $userAssignOrganizationId)->select('id', 'name')->get()->toArray();

            $orgDepts = User_org::where('user_id', $id)->pluck('org_department')->toArray();

            $orgDeptNames = (count($orgDepts)) ? OrganizationDepartment::whereIN('id', $orgDepts)->select('id', 'org_deptname as name')->get() : [];

            $userDetails = (['id' => $user->id, 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'user_name' => $user->user_name, 'email' => $user->email, 'address' => $user->internal_note, 'phone_number' => $user->phone_number, 'mobile' => $user->mobile, 'country_code' => $user->country_code, 'ext' => $user->ext]);

            $organizationsData = (['organisation' => $organisations, 'organisation_department' => $orgDeptNames]);
            $userDetails = array_merge($userDetails, $customFields, $organizationsData);

            $editForm = (new FormController())->getFormWithEditValues($userDetails, 'user', 'edit', 'agent', $id);

            return successResponse('', $editForm);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * This method for update the user information
     * @param type $id
     * @param Request $request
     * @return type json
     */
    public function updateUserApi($id, UserEditRequest $request)
    {
        try {
            $user = User:: where('id', $id)->first();
            $user = (new AuthController())->postRegister($user, $request);
            $user->internal_note = $request->address;
            $user->save();

            $arrayOrgIds = User_org::where('user_id', $id)->pluck('org_id')->toArray();
            if ($request->organisation) {
                $user->organizations()->sync($request->organisation);
                User_org::where('role', "")->update(['role' => 'members']);

                // now remove all organisation departments first from all organisations user belongs to
                User_org::where('user_id', $id)->update(['org_department' => null]);

                $orgDept = OrganizationDepartment::where('id', $request->organisation_department)->first();

                if ($orgDept) {
                    // now update that particular organisation for organisation department
                    User_org::where('user_id', $user->id)->where('org_id', $orgDept->org_id)
                            ->update(['org_department' => $request->organisation_department]);
                }
            } else {
                User_org::where('user_id', $id)->delete();
            }
            //associate user to organization base on domain match
            $this->domainConnection($id);
            return successResponse(Lang::get('lang.updated_successfully'));
        } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     *
     * @param Request $request
     * @return type json
     */
    public function findType(Request $request)
    {
        try {
            $term = trim($request->q);
            if (empty($term)) {
                return \Response::json([]);
            }
            $depts = \App\Model\helpdesk\Manage\Tickettype::where('name', 'LIKE', '%' . $term . '%')->select('id', 'name')->where('status', '=', 1)->get();
            $formatted_tags = [];
            foreach ($depts as $dept) {
                $formatted_depts[] = ['id' => $dept->id, 'text' => $dept->name];
            }
            return \Response::json($formatted_depts);
        } catch (Exception $e) {
            // returns if try fails with exception meaagse
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     *
     * @param type $user
     * @param type $types
     */
    public function typeRelation($user, $types)
    {
        if ($types && count($types) > 0) {
            $user->type()->delete();
            foreach ($types as $type) {
                $user->type()->create([
                    'agent_id' => $user->id,
                    'type_id' => $type,
                ]);
            }
        } else {
            $user->type()->delete();
        }
    }

    /**
     *
     * @param Request $request
     * @return type json
     */
    public function findDept(Request $request)
    {
        try {
            $term = trim($request->q);
            if (empty($term)) {
                return \Response::json([]);
            }
            $depts = Department::where('name', 'LIKE', '%' . $term . '%')->select('id', 'name')->get();
            $formatted_tags = [];

            foreach ($depts as $dept) {
                $formatted_depts[] = ['id' => $dept->id, 'text' => $dept->name];
            }

            return \Response::json($formatted_depts);
        } catch (Exception $e) {
            // returns if try fails with exception meaagse
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

   public function postManualVerify(Request $request)
    {
        try{
            $user = User::find($request->get('id'));
            if ($request->has('country_code') && $request->get('country_code') != '') {
                $mobile_exists = User::where([
                    ['mobile', '=', $request->get('mobile')],
                    ['country_code', '=', $request->get('country_code')],
                ])->pluck('id')->toArray();
                if (!empty($mobile_exists) && !in_array($request->get('id'), $mobile_exists)) {
                    return errorResponse(Lang::get('lang.mobile-has-been-taken'));
                }
                $user->update([
                    'mobile' => $request->get('mobile'),
                    'iso'  => $request->iso,
                    'country_code' => $request->get('country_code'),
                    'mobile_verify' => 1
                ]);
            } else {
                $user->update([
                    'email' => $request->get('email'),
                    'email_verify' => 1
                ]);
            }

            return successResponse(Lang::get('lang.verified_manually'));
        } catch(\Exception $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    /**
     *  linking between user and organization
     *
     * @param type $userId  of user
     * @return type boolean true
     */
    public static function domainConnection($userId)
    {

        //associate user to organization base on domain match
        $orgs = Organization::where('domain', '!=', '')->select('id', 'domain')->get();
        if (count($orgs) > 0) {

            foreach ($orgs as $org) {
                $str = str_replace(",", '|@', '@' . $org->domain);
                $domainUserId = User::where('id', $userId)->where('role', '=', 'user')->whereRaw("email REGEXP '" . $str . "'")->value('id');
                $checkUserBelongsTo = User_org::where('user_id', $userId)->where('org_id', $org->id)->first();

                if (!$checkUserBelongsTo && $domainUserId != 0) {
                    User_org::updateOrCreate(['org_id' => $org->id, 'user_id' => $domainUserId, 'role' => 'members']);
                }
            }
        }
        return true;
    }

    /**
     * This method return user status with icon
     * @param User $model
     * @return string
     */
    public static function userStatus(User $model): string
    {


        $sColor = ($model->active == '1') ? "green" : "red";
        $sTitle = ($model->active == '1') ? Lang::get('lang.user_account_is_verified_tooltip') : Lang::get('lang.user_account_not_verified_tooltip');
        $eColor = ($model->email_verified != '1') ? "red" : "green";
        $eTitle = ($model->email_verified != '1') ? Lang::get('lang.user_has_not_verified_email_tooltip') : Lang::get('lang.user_email_is_verified_tooltip');
        $mColor = ($model->mobile_verified == '1') ? "green" : "red";
        $mTitle = ($model->mobile_verified == '1') ? Lang::get('lang.user_mobile_is_verified_tooltip') : Lang::get('lang.user_has_not_verified_contact_number_tooltip');

        $deactiveColor = ($model->active == 0) ? "green" : "red";
        $deactiveTitle = ($model->active == 0) ? Lang::get('lang.activated_mod') : Lang::get('lang.deactivated_mod');
        $html = '<span style="margin-left: 17px;"><span class="fas fa-envelope" title="' . $eTitle . '" style="font-size: 15px;color: ' . $eColor . '">&nbsp;&nbsp;<span class="fas fa-mobile-alt"  title="' . $mTitle . '" style="color:' . $mColor . '">&nbsp;&nbsp;<span class ="fas fa-trash" title="' . $deactiveTitle . '" style="color:' . $deactiveColor . '" > </span>';

        return $html;
    }

    /**
     *  If agent remove from global access or any department  if any open which is not matched with agent department that tickets will go to unassign mode
     *
     * @param type $userId  of user
     * @return type boolean true
     */
    public static function removeGlobalAccess(string $userId)
    {
        try {
            $agentId = getAgentbasedonPermission('global_access');
            if (in_array($userId, $agentId)) {
                return true;
            }
            $openTicketQuery = Tickets::where('assigned_to', $userId)->where(function ($query) {
                        $query->whereIN('tickets.status', getStatusArray('open'))
                                ->orWhereIN('tickets.status', getStatusArray('approval'))->get();
                    })->pluck('dept_id')->toArray();

            $deptId = DepartmentAssignAgents::where('agent_id', $userId)->pluck('department_id')->toArray();

            if (count($openTicketQuery) > 0) {

                foreach ($openTicketQuery as $ticketDeptId) {

                    if (!in_array($ticketDeptId, $deptId)) {
                        Tickets::where('assigned_to', $userId)->where('dept_id', $ticketDeptId)->update(['assigned_to' => NULL]);
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * This method return all user id based on agent type(agent may be team lead or department manager)
     * @return type array
     */
    public static function agentAndUserIds(): array
    {
        $authAgent = auth()->user()->id;
        $userIds = User:: where('role', 'user')->pluck('id')->toArray();
        $deptIds = DepartmentAssignManager::where('manager_id', $authAgent)->pluck('department_id')->toArray();
        $agentIds = ($deptIds) ? DepartmentAssignAgents::whereIn('department_id', $deptIds)->pluck('agent_id')->toArray() : [];
        $teamIds = Teams::where('team_lead', $authAgent)->pluck('id')->toArray();
        $agentIdsFromTeam = ( $teamIds) ? Assign_team_agent::whereIn('team_id', $teamIds)->pluck('agent_id')->toArray() : [];
        $ids = array_unique(array_merge([$authAgent], $userIds, $agentIds, $agentIdsFromTeam));

        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();
        return array_diff($ids, $adminIds);
    }


    /**
     * This method return user chart data information
     * @param Request $request
     * @return json
     */
    public function userChartData(Request $request)
    {
        $userChartData = new TicketsGraphController();
        return $userChartData->chartData($request);
    }
}
