<?php

namespace App\Http\Controllers\Admin\helpdesk;

// controller
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController as Notify;
// request
use App\Http\Requests\helpdesk\AgentRequest;
use App\Http\Requests\helpdesk\AgentUpdate;
use App\Exceptions\AgentLimitExceededException;
// model
use App\Model\helpdesk\Agent\Assign_team_agent;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Utility\CountryCode;
use App\Model\helpdesk\Utility\Timezones;
use App\Location\Models\Location;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\User;
// classes
use Illuminate\Http\Request;
use DB;
use Exception;
use Hash;
use Lang;
use Datatables;
use Validator;
use App\Http\Controllers\Agent\helpdesk\UserController;
use App\Http\Controllers\Admin\helpdesk\Request\AgentCreateRequest;
use App\Http\Controllers\Admin\helpdesk\Request\ChangeAgentRequest;
use Auth;
use App\Http\Controllers\Auth\AuthController;

/**
 * AgentController
 * This controller is used to CRUD Agents.
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AgentController extends Controller {

    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent.
     *
     * @return void
     */
    // public function __construct(PhpMailController $PhpMailController) {
    //     // creating an instance for the PhpmailController
    //     $this->PhpMailController = $PhpMailController;
    //     // checking authentication
    //     $this->middleware('auth');
    //     // checking admin roles
    //     $this->middleware('roles');
    //     $this->middleware('limit.reached', ['only' => ['store']]);
    // }


      public function __construct()
    {
        // creating an instance for the PhpmailController
        $this->PhpMailController = new PhpMailController();
        // checking admin roles
        $this->middleware('role.admin')->except(['getOwnDetails']);
        // by passing admin middleware to get logged in agent details
        $this->middleware('role.agent');
    }

    /**
     * Get all agent list page.
     *
     * @return type view
     */
    public function index() {
        try {
            return view('themes.default1.admin.helpdesk.agent.agents.index');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Editing a selected agent.
     *
     * @param type int               $id
     * @param type User              $user
     * @param type Assign_team_agent $team_assign_agent
     * @param type Timezones         $timezone
     * @param type Department        $department
     * @param type Teams             $team
     *
     * @return type Response
     */
    public function edit($id) {
        try {
            $role = User::where('id',$id)->value('role');
            if(!$role || $role == 'user') {
                return redirect('agents')->with('fails', Lang::get('lang.agent_not_found'));
            }

            return view('themes.default1.admin.helpdesk.agent.agents.edit');
        } catch (Exception $e) {

            return redirect('agents')->with('fails', $e->getMessage());
        }
    }

    /**
     * creating a new agent.
     *
     * @param Assign_team_agent $team_assign_agent
     * @param Timezones         $timezone
     * @param Groups            $group
     * @param Department        $department
     * @param Teams             $team_all
     *
     * @return type view
     */
    public function create()
    {
        try {
            return view('themes.default1.admin.helpdesk.agent.agents.create');
        } catch (Exception $e) {
            // returns if try fails with exception meaagse
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
    * Creates and Updates Agent
    * @param $request
    * @return Response
    */
    public function createUpdateAgent(AgentCreateRequest $request)
    {
        try {
            $user = $this->renameAgentParameter($request->toArray());
        // by default a new agent is agent
        if (!$request->has('role')) {
            $user['role'] = 'agent';
        }
        $authController = new AuthController;
        $userObj = (User::find($request->id))?: new User;
        $userUpdatedObj = $authController->postRegister($userObj, $request, $user['role']);
        $agent = User::updateOrCreate(['id' => $userUpdatedObj->id], $user);
        $this->updateUserMultipleFields($agent, $user);

        if ($agent['role'] == 'admin') {
            return successResponse(Lang::get('lang.admin_saved_success'));
        }
        return successResponse(Lang::get('lang.agent_saved_success'));
    } catch(Exception $e) {

        return errorResponse($e->getMessage());
    }
        
    }

    /**
    * Function to insert values in Department, Team, Type and Permission table
    * @param object $agent
    * @param array $user
    * @return 
    */
    private function updateUserMultipleFields($agent, $user)
    {
        if (array_key_exists('department_ids', $user)) {
            $departmentIds = (array) $user['department_ids'];
            $agent->departments()->sync($departmentIds);
            $this->removeDepartmentManagerIfNotADepartmentMember($agent, $departmentIds);
        }
        if (array_key_exists('team_ids', $user)) {
            $teams = (array) $user['team_ids'];
            $agent->teams()->sync($teams);
        }
        $agent->type()->delete();
        if (array_key_exists('type_ids', $user)) {
            $types = (array) $user['type_ids'];
            foreach ($types as $key => $value) {
                $agent->type()->create(['agent_id' => $agent->id, 'type_id' => $value]);
            }
        }
        $this->updateAgentPermission($agent, $user);
    }

    /**
    * Function to insert values in Permission
    * @param object $agent
    * @param array $user
    * @return 
    */
    private function updateAgentPermission($agent, &$user)
    {
        // assign permission to agent only
        // don't assign any permission to admin
        if ($agent->role == 'agent') {
            if (array_key_exists('permission_ids', $user)) {
                $agent->permissions()->sync($user['permission_ids']);
            }

            //Servicedesk Permission
            \Event::dispatch('agent-permission-data-submiting',[ $agent, $user ]);
        }
    }

    /**
    * Function to rename agent parameter
    * @param array $agent
    * @return $error
    */
    private function renameAgentParameter($agent)
    {
        $agent['agent_tzone'] = $agent['agent_tzone_id'];
        if (array_key_Exists('location_id', $agent)) {

            $locationId = Location::where('title',$agent['location_id'])->value('id');

            $agent['location'] = $locationId ? $locationId : null;
            unset($agent['location_id']);
        }
        unset($agent['agent_tzone_id']);
        return $agent;
    }

    /**
    * edit agent
    * @param $agentId
    * @return Response
    */
    public function editAgent($agentId)
    {
        $agent = User::where('role', '<>', 'user')
                       ->where('id', $agentId)
                       ->with(['timezone','type.type:id,name','departments:department.id,department.name','teams:teams.id,teams.name','permissions:user_permissions.id,key,name'])
                       ->select('id', 'first_name', 'last_name', 'user_name', 'location', 'role', 'email', 'ext', 
                        'country_code', 'mobile', 'phone_number', 'active', 'agent_tzone', 'agent_sign','iso' );

        if ($agent->get()->isEmpty()) {
            return errorResponse(Lang::get('lang.not_found'));
        }
        $agent = $this->editFormatAgent($agent->first()->toArray());
        //For getting servicedesk agent permissions
        \Event::dispatch('agent-permission-data-getting',[$agentId , &$agent ]);
        return successResponse('', ['agent' => $agent]);
    }

    /**
    * Function to format edit Agent
    * @param array $member
    * @return array $member
    */
    private function editFormatAgent($member)
    {
        $location = explode(' ', $member['timezone']['location']);
        $name = implode(' ', [$location[0], $member['timezone']['name']]);
        $member['agent_tzone'] = ['id' => $member['agent_tzone'], 'name' => $name];
        unset($member['timezone']);
        if (!is_null($member['country_code'])) {
            $member['country_code'] = ['id' => $member['country_code'], 'name' => CountryCode::where('phonecode', $member['country_code'])->first()->name];
        }
        foreach ($member['departments'] as &$department) {
            unset($department['pivot']);
        }
        $this->formatAgentcontacts($member);
        $this->extraFormatAgent($member);
        return $member; 
    }

     /**
    * Function to format edit Agent mobile and location field
    * @param  array $member
    * @return 
    */
    private function formatAgentcontacts(&$member)
    {
        if (empty($member['mobile'])) {
            unset($member['mobile'], $member['country_code']);
        }
        
        if (empty($member['location']) || is_null($member['location'])) {
            $member['location'] = [];
        }
        else {
            $member['location'] = ['id' =>$member['location'] ,
            'name' => Location::where('id', $member['location'])->value('title')];
        }
    }


    /**
    * Function to format edit Agent left out fields
    * @param  array $member
    * @return 
    */
    private function extraFormatAgent(&$member)
    {
        // if (empty($member['permision']['permision'])) {
        //     $member['permissions'] = [];
        // }
        // else {
        //     foreach (array_keys($member['permision']['permision']) as $key) {
        //         $member['permissions'][] = ['id' => $key, 'name' => $this->formatPermission($key)];
        //     }
        // }
        foreach ($member['teams'] as &$team) {
            unset($team['pivot']);
        }
        foreach ($member['type'] as &$types) {
            unset($types['id'], $types['type_id'], $types['agent_id'], $types['created_at'], $types['updated_at']);
            $types['id'] = $types['type']['id'];
            $types['name'] = $types['type']['name'];
            unset($types['type']);
        }
        unset($member['full_name']);
    }

    // /**
    // * Function to format permission
    // * @param $permissionId
    // * @return $permission name
    // */
    // private function formatPermission($permissionId)
    // {
    //     $permissions = $this->listPermission();
    //     foreach ($permissions as $permission) {
    //         if ($permission['id'] == $permissionId) {
    //             return $permission['name'];
    //         }
    //     } 
    // }

    // /**
    // * Function to list permission
    // * @return array $permission
    // */
    // private function listPermission()
    // {
    //     $permissions = [
    //             ['id' => 'create_ticket', 'name' => 'Create ticket'],
    //             ['id' => 'edit_ticket', 'name' => 'Edit ticket'],
    //             ['id' => 'close_ticket', 'name' => 'Close tickets'],
    //             ['id' => 'transfer_ticket', 'name' => 'Transfer Ticket'],
    //             ['id' => 'delete_ticket', 'name' => 'Delete tickets'],
    //             ['id' => 'assign_ticket', 'name' => 'Tickets Assigned'],
    //             ['id' => 'view_unapproved_tickets', 'name' => 'View unapproved tickets'],
    //             ['id' => 'apply_approval_workflow', 'name' => 'Apply Approval Workflow'],
    //             ['id' => 'access_kb', 'name' => 'Access knowledge base'],
    //             ['id' => 'report', 'name' => 'Access reports'],
    //             ['id' => 'ban_email', 'name' => 'Ban email'],
    //             ['id' => 'organisation_document_upload', 'name' => 'Upload organization documents'],
    //             ['id' => 'account_activate', 'name' => 'User account activation'],
    //             ['id' => 'agent_account_activate', 'name' => 'Agent account activation'],
    //             ['id' => 'change_duedate', 'name' => 'Change duedate'],
    //             ['id' => 're_assigning_tickets', 'name' => 'Re assigning tickets'],
    //             ['id' => 'global_access', 'name' => 'Global access'],
    //             ['id' => 'restricted_access', 'name' => 'Restricted access (view only tickets assigned to them)'],
    //             ['id' => 'access_user_profile', 'name' => 'Access user profile'],
    //             ['id' => 'access_organization_profile', 'name' => 'Access organization profile'],
    //             ['id' => 'recur_ticket', 'name' => 'Recur Ticket']
    //         ];
    //     return $permissions; 
    // }

    /**
    * get agent
    * @param $agentId
    * @return Response
    */
    public function getAgents($agentId)
    {
        $agent = User::where('role', '<>', 'user')
                       ->where('id', $agentId)
                       ->with(['departments:department.id,department.name','teams:teams.id,teams.name'])
                       ->select('id', 'first_name', 'last_name', 'user_name', 'role', 'email', 'profile_pic',
                                'ext', 'country_code', 'mobile', 'phone_number', 'mobile_verify',  'email_verify',
                                'active', 'is_delete', 'agent_tzone' )
                       ->get();
        if ($agent->isEmpty()) {
            return errorResponse(Lang::get('lang.not_found'));
        }

        $agent = $this->formatAgent($agent->toArray());
        return successResponse('', ['agent' => $agent]);
    }

    /**
    * Function to format agent
    * @param array $agent
    * @return array $agent
    */
    private function formatAgent($agent)
    {
        foreach ($agent as &$member) {
            if (empty($member['mobile'])) {
                unset($member['mobile'], $member['country_code']);
            }
            if (empty($member['phone_number'])) {
                unset($member['phone_number'], $member['ext']);
            }
            $member['name'] = $member['full_name'];
            unset($member['first_name'], $member['last_name'], $member['full_name']);
            foreach ($member['departments'] as &$department) {
                unset($department['pivot']);
            }
            foreach ($member['teams'] as &$team) {
                unset($team['pivot']);
            }
        }
        return $agent; 
    }

    /**
    * change agent property
    * @param $agentId
    * @param $request , it consist of 5 optional parameter: email, role, password, gen_pass or active.
    * @return Response
    */
    public function changeAgent($agentId, ChangeAgentRequest $request)
    {
        $this->request = $request;
        if ($agentId) {
            $agentQuery = User::where('role', '<>', 'user')
                       ->where('id', $agentId);
            if ($agentQuery->get()->isEmpty()) {
                return errorResponse(Lang::get('lang.not_found'));
            }
        }
        else {
            return errorResponse(Lang::get('lang.not_found'));
        }
        $message = $this->changeAcess($agentQuery);
        if (strlen($message) == 10) {
            return successResponse('',['password' => $message]);
        }
        return successResponse($message);
    }

    /**
    * Function to change access
    * @param $agentQuery
    * @return message
    */
    private function changeAcess(QueryBuilder $agentQuery)
    {
        switch ($this->request->type) {

            case 'email':
                return $this->changeEmail('email', 'email', $agentQuery);
            case 'role':
                return $this->changeRole('role', 'role', $agentQuery);
            case 'password':
                return $this->changePassword('password', 'password', $agentQuery);
            case 'gen_pass':
                return $this->generatePassword();
            case 'active':
                return $this->activateAgent('active', 'active', $agentQuery);
             default:
                return false;
        }
    }

    /**
    * Function to change email
    * @param $fieldNameInRequest
    * @param $fieldNameInDB
    * @param $agentQuery
    * @return message
    */
    private function changeEmail($fieldNameInRequest, $fieldNameInDB, &$agentQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $agentQuery->update([$fieldNameInDB => $this->request->input($fieldNameInRequest)]);
            return Lang::get('lang.email_changed_successfully');
        }
    }

    /**
    * Function to change Role
    * @param $fieldNameInRequest
    * @param $fieldNameInDB
    * @param $agentQuery
    * @return message
    */
    private function changeRole($fieldNameInRequest, $fieldNameInDB, &$agentQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $agentQuery->update([$fieldNameInDB => $this->request->input($fieldNameInRequest)]);
            return Lang::get('role_change_successfully');
        }
    }

    /**
    * Function to change password
    * @param $fieldNameInRequest
    * @param $fieldNameInDB
    * @param $agentQuery
    * @return message
    */
    private function changePassword($fieldNameInRequest, $fieldNameInDB, &$agentQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $agentQuery->update([$fieldNameInDB => Hash::make($this->request->input($fieldNameInRequest))]);
            return Lang::get('password_change_successfully');
        }
    }

    /**
    * Function to generate password
    * @param $fieldNameInRequest
    * @param $fieldNameInDB
    * @param $agentQuery
    * @return $randomString
    */
    private function generatePassword()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
    * Function to activate/deactivate agent
    * @param $fieldNameInRequest
    * @param $fieldNameInDB
    * @param $agentQuery
    * @return message
    */
    private function activateAgent($fieldNameInRequest, $fieldNameInDB, &$agentQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $agentQuery->update([$fieldNameInDB => $this->request->input($fieldNameInRequest)]);
            if ($this->request->input($fieldNameInRequest) == 1) {
                return Lang::get('lang.account_activated_successfully');
            }
            return Lang::get('lang.account_deactivated_successfully');
        }
    }

    /**
     * Funtion to get agent details when logged in user is agent only
     * logged in agent can get only their details
     * @return Response
     */
    public function getOwnDetails()
    {
        $loggedInAgentId = Auth::user()->id;

        $agentResponse = $this->editAgent($loggedInAgentId);

        return $agentResponse;
    }

    /**
     * method to remove manager from department, if he/she is not a department member of that department
     * @param User $agent
     * @param array $departmentIds
     * @return null
     */
    private function removeDepartmentManagerIfNotADepartmentMember($agent, $departmentIds){
        // finding the department ids for which current agent is department manager
        $departmentIdsForWhichAgentIsManager = $agent->managerOfDepartments()->pluck('department_id')->toArray();
        // finding the department ids for which current agent is department manager but he/she is not a department member
        $departmentIdsOfManagerWhoIsNotADepartmentMember = array_diff($departmentIdsForWhichAgentIsManager, $departmentIds);
        if (!empty($departmentIdsOfManagerWhoIsNotADepartmentMember)) {
            foreach ($departmentIdsOfManagerWhoIsNotADepartmentMember as $departmentId) {
                // remove department manager from the department in which he/she is not a department member
                $agent->managerOfDepartments()->wherePivot('department_id', $departmentId)->wherePivot('manager_id', $agent->id)->detach();
            }
        } 
    }


}
