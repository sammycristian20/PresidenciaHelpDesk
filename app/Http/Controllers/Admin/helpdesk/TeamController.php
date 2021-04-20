<?php

namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\TeamRequest;
use App\Http\Requests\helpdesk\TeamUpdate;
use Illuminate\Http\Request;
// models
use App\Model\helpdesk\Agent\Assign_team_agent;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Agent\TeamsDepartment;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\Permission;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\Schema;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
// classes
use DB;
use Exception;
use Lang;
use App\Http\Controllers\Agent\helpdesk\UserController;
use Validator;
use Auth;



/**
 * TeamController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class TeamController extends Controller {
    /**
     * Datatables Html Builder
     * @var Builder
     */
    protected $htmlBuilder;

    /**
     * Create a new controller instance.
     *
     * @return type void
     */

    public function __construct(Builder $htmlBuilder) {
        $this->middleware('auth');
        $this->middleware('roles');
        $this->htmlBuilder = $htmlBuilder;
    }

    /**
     * get Index page.
     *
     * @param type Teams             $team
     * @param type Assign_team_agent $assign_team_agent
     *
     * @return type Response
     */
    public function index()
    {
        try {
            
            return view('themes.default1.admin.helpdesk.agent.teams.index');
        } catch (Exception $e) {

            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function teamShow($id)
    {
        try {
            $html = $this->htmlBuilder
                ->addColumn(['data' => 'user_name', 'name' => 'user_name', 'title' => Lang::get('lang.user_name')])
                ->addColumn(['data' => 'first_name', 'name' => 'first_name', 'title' => Lang::get('lang.name')])
                ->addColumn(['data' => 'active', 'name' => 'active', 'title' => Lang::get('lang.status')])
                ->addColumn(['data' => 'role', 'name' => 'role', 'title' => Lang::get('lang.role')]);

            $teams = Teams::where('id', '=', $id)->first();
            $team_members = Assign_team_agent::where('team_id', '=', $teams->id)->select('agent_id')->count();
            $users=user::where('id','=',$teams->team_lead)->select('user_name','first_name','last_name')->first();
            if ($users) {
                if ($users->first_name || $users->last_name) {
                    $team_lead_name =$users->first_name.' '. $users->last_name;
                } else {
                    $team_lead_name = $users->user_name;
                }
            } else {
                $team_lead_name=Lang::get('lang.no_team_lead');
            }

            $loggedInUser = Auth::user();

            return view('themes.default1.admin.helpdesk.agent.teams.show', compact('teams', 'team_members', 'team_lead_name', 'html', 'loggedInUser'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @param type User $user
     *
     * @return type Response
     */
    public function create(User $user, Department $department)
    {
        try {


             $departments = $department->get();
             $user = $user->where('role', '<>', 'user')->where('active', '=', 1)->orderBy('first_name')->get();

            return view('themes.default1.admin.helpdesk.agent.teams.create', compact('user','departments'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param type Teams       $team
     * @param type TeamRequest $request
     *
     * @return type Response
     */
    public function store(Teams $team, TeamRequest $request)
    {
        try {
            /* Check whether function success or not */
            $team->fill($request->except('team_lead'))->save();
            if ($request->team_lead) {
                $team_assign_agent = new Assign_team_agent;
                $team_assign_agent->create([
                    'team_id'  => $team->id,
                    'agent_id' => $request->team_lead
                ]);
                $team->update([
                    'team_lead' => $request->team_lead
                ]);
            } 
            // if($request->primary_department){
            //     $primary_dpt = $request->primary_department;

            //     foreach ($primary_dpt as $primary_dpts) {
            //         $team_assign_dept=new TeamsDepartment;
            //         $team_assign_dept->team_id=$team->id;
            //         $team_assign_dept->dept_id=$primary_dpts;
            //         $team_assign_dept->save();
            //     }
            // }
            /* redirect to Index page with Success Message */
            return redirect('teams')->with('success', Lang::get('lang.team_saved_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('teams')->with('fails', Lang::get('lang.teams_can_not_create').'<li>'.$e->getMessage().'</li>');
        }
    }
       /**
     * Show the form for editing the specified resource.
     *
     * @param type                   $id
     * @param type User              $user
     * @param type Assign_team_agent $assign_team_agent
     * @param type Teams             $team
     *
     * @return type Response
     */
    public function show($id, User $user, Assign_team_agent $assign_team_agent, Teams $team)
    {
        try {
            $user = $user->whereId($id)->first();
            $teams = $team->whereId($id)->first();
           
            $team_lead_name=User::whereId($teams->team_lead)->first();
            if($team_lead_name){
                $team_lead = $team_lead_name->first_name . " " . $team_lead_name->last_name;
            }
            else{
                $team_lead ="";
            }
            
              
            $total_members = $assign_team_agent->where('team_id',$id)->count();

    
            return view('themes.default1.admin.helpdesk.agent.teams.show', compact('user', 'teams','id','team_lead','total_members'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function getshow($id)
    {
        $users = DB::table('team_assign_agent')->select('users.id','users.user_name', 'users.first_name', 'users.last_name', 'users.active', 'users.role','users.email_verify','users.mobile_verify','users.is_delete')
                ->join('users', 'users.id', '=', 'team_assign_agent.agent_id')
                ->where('users.is_delete', 0)
                ->where('team_assign_agent.team_id', '=', $id);
        return \DataTables::of($users)
                            ->removeColumn('last_name', 'id')
                        ->addColumn('user_name', function ($model){
                            return $model->user_name;
                        })
                        ->addColumn('first_name', function ($model) {
                            $full_name = ucfirst($model->first_name) . ' ' . ucfirst($model->last_name);
                            return $full_name;
                        })
                        ->addColumn('active', function ($model) {
                         $user =User::where('id',$model->id)->select('active', 'email_verify','mobile_verify','is_delete')->first();
                            return UserController::userStatus($user);
                        })
                        ->addColumn('role', function ($model) {
                            if ($model->role == 'admin') {
                                $role = "<a class='btn btn-default btn-xs' style='pointer-events:none;'>" . ucfirst($model->role) . "</a>";
                            } elseif ($model->role == 'agent') {
                                $role = "<a class='btn btn-default btn-xs' style='pointer-events:none;'>" .ucfirst($model->role) . "</a>";
                            }
                            return $role;
                        })
                        ->rawColumns(['active','role'])
                        ->make();
        


        } 




    /**
     * Show the form for editing the specified resource.
     *
     * @param type                   $id
     * @param type User              $user
     * @param type Assign_team_agent $assign_team_agent
     * @param type Teams             $team
     *
     * @return type Response
     */
    public function edit($id, User $user, Assign_team_agent $assign_team_agent, Teams $team)
    {
        try {
            $teams = $team->whereId($id)->first();
            $agent_team = $assign_team_agent->where('team_id', $id)->get();
            $agent_id = $agent_team->pluck('agent_id', 'agent_id');
            // $dept = TeamsDepartment::where('team_id', '=', $id)->pluck('dept_id')->toArray();
            $user = User::whereIn('id', $agent_id)->where('active', '=', 1)->orderBy('first_name')->get();
            // $departments = Department::get();
              
            return view('themes.default1.admin.helpdesk.agent.teams.edit', compact('agent_id', 'user', 'teams'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }




    /**
     * Update the specified resource in storage.
     *
     * @param type int        $id
     * @param type Teams      $team
     * @param type TeamUpdate $request
     *
     * @return type Response
     */
    public function update($id, Teams $team, TeamUpdate $request)
    {
        $teams = $team->whereId($id)->first();
        //updating check box

        if ($request->team_lead) {
            $team_lead = $request->team_lead;
        } else {
            $team_lead = null;
        }
        $teams->team_lead = $team_lead;
        $teams->save();

        $alert = $request->input('assign_alert');
        $teams->assign_alert = $alert;
        $teams->save(); //saving check box
        //updating whole field
        /* Check whether function success or not */


         // $delete_dept = TeamsDepartment::where('team_id', '=', $id)->delete();
         // if($request->primary_department){
         //     $primary_dpt = $request->primary_department;

         //        foreach ($primary_dpt as $primary_dpts) {
         //            $team_assign_dept = new TeamsDepartment;
         //            $team_assign_dept->team_id = $teams->id;
         //            $team_assign_dept->dept_id = $primary_dpts;
         //            $team_assign_dept->save();
         //        }
         //    }
        try {
            $teams->fill($request->except('team_lead'))->save();
            /* redirect to Index page with Success Message */
            return redirect('teams')->with('success', Lang::get('lang.team_updated_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('teams')->with('fails', Lang::get('lang.teams_can_not_update').'<li>'.$e->getMessage().'</li>');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param type int               $id
     * @param type Teams             $team
     * @param type Assign_team_agent $assign_team_agent
     *
     * @return type Response
     */
    public function destroy($id, Teams $team, Assign_team_agent $assign_team_agent)
    {
        try {
            $assign_team_agent->where('team_id', $id)->delete();
            $teams = $team->whereId($id)->first();
            // $team_dept=TeamsDepartment::where('team_id', $id)->delete();
            $tickets = DB::table('tickets')->where('team_id', '=', $id)->update(['team_id' => null]);
            /* Check whether function success or not */
            $teams->delete();
            /* redirect to Index page with Success Message */
            return redirect('teams')->with('success', Lang::get('lang.team_deleted_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('teams')->with('fails', Lang::get('lang.teams_can_not_delete').'<li>'.$e->getMessage().'</li>');
        }
    }

    /**
     *
     *
     *
     *
     */
    public function getTeamTable()
    {
        $teams = Teams::with(['lead:id,first_name,last_name,user_name'])
        ->select('id', 'name', 'status', 'team_lead')
        ->withCount('agents')
        ->get();

        return \DataTables::of($teams)
        ->editColumn('name', function($teams){
            $url = 'teams/'.$teams->id.'/edit';
            return '<a href="'.$url.'">'.$teams->name.'</a>';
        })
        ->editColumn('status', function($teams){
            if($teams->status == 0) {
                return '<span class="btn btn-xs btn-default" style="color:red;pointer-events:none;">'.trans('lang.inactive').'</span>';
            }
            return '<span class="btn btn-xs btn-default"  style="color:green;pointer-events:none;">'.trans('lang.active').'</span>';
        })
        ->editColumn('count', function($teams){
            return $teams->agents_count;
        })
        ->editColumn('user_name', function($teams){
            if($teams->team_lead != null) {
                $name = $teams->lead->user_name;
                if ($teams->first_name != '' && $teams->first_name != null) {
                    $name = $teams->lead->first_name.' '.$teams->lead->last_name;
                }
                return '<a  href="'.route('user.show', $teams->lead->id).'" title="'.$name.'">'.$name.'</a>';
            }
            return '';
        })
        ->addColumn('action', function($teams){
            $show = '';
            if ($teams->status == 1) {
                $show = '<a href="'.route('teams.profile.show', $teams->id).'" class="btn btn-primary btn-xs " ><i class="fas fa-eye" style="color:white;">&nbsp;</i>'.trans('lang.view').'</a> ';
            }
            
                    $url = url('delete/teampopup/'.$teams->id);
                    $confirmation = deletePopUp($teams->id, $url, "Delete",'btn btn-primary btn-xs');
            
            return '<a href="'.route('teams.edit', $teams->id).'" class="btn btn-xs btn-primary"><i class="fas fa-edit" style="color:white;"> </i> '.trans('lang.edit').'</a>&nbsp;'.$show.'&nbsp;'.$confirmation
            .\Form::close();
        })
        ->rawColumns(['name','status','user_name','action'])
        ->make();
    }


   
}
