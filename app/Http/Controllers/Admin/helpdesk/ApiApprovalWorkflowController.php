<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\helpdesk\Request\CreateUpdateApprovalWorkflowRequest;
use App\Http\Controllers\Admin\helpdesk\Request\ApprovalWorkflowListRequest;
use App\Model\helpdesk\Workflow\ApprovalLevel;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\User;
use Auth;
use Lang;
use Event;

/**
 * ApiApprovalWorkflowController is updated controller of ApprovalWorkflowController
 * used for approval workflow crud and type is approval workflow for approval workflow module
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiApprovalWorkflowController extends Controller
{   
    private $type = 'approval_workflow';

    private $typeName = null;

    public function __construct()
    {
        $this->middleware('role.admin');
        Event::dispatch('approvalworkflow-type', [&$this->type]); 
    }

    /**
     * Function to show list page of approval workflow
     * @return view
     */
    public function showIndex()
    {
        return view('themes.default1.admin.helpdesk.manage.approval_workflow.index');
    }

    /**
     * Function to show create page of approval workflow
     * @return view
     */
    public function create()
    {
        return view('themes.default1.admin.helpdesk.manage.approval_workflow.create');
    }

    /**
     * Function to show edit page of approval workflow
     * @return view
     */
    public function edit()
    {
        return view('themes.default1.admin.helpdesk.manage.approval_workflow.edit');
    }

    /** 
     * Function to create or update Approval Workflow
     * @param CreateUpdateApprovalWorkflowRequest $request
     * @return response
     */
    public function createUpdateApprovalWorkflow(CreateUpdateApprovalWorkflowRequest $request)
    {   
        $approvalWorkflow['type'] = $this->type;
        $approvalWorkflow = array_merge($approvalWorkflow, $request->toArray());
        $approvalWorkflow['action_on_deny'] = $this->type != 'approval_workflow' ? 6 : $request->action_on_deny;
        $approvalWorkflow['user_id'] = Auth::user()->id;
        $approvalWorkflowObject = ApprovalWorkflow::updateOrCreate(['id' => $approvalWorkflow['id']], $approvalWorkflow);
        foreach ($approvalWorkflow['levels'] as $level) {
            $approvalLevel = $approvalWorkflowObject->approvalLevels()->updateOrCreate(['id' => $level['id']], $level);
            $approvalLevel->approveUsers()->sync($level['approvers']['users']);
            (!array_key_exists('user_types', $level['approvers'])) ?: $approvalLevel->approveUserTypes()->sync($level['approvers']['user_types']);
        }

        return successResponse(Lang::get('lang.saved_successfully'));
    }

    /**
     * Function to get list of Approval Workflows
     * @param ApprovalWorkflowListRequest $request
     * @return response
     */
    public function getApprovalWorkflowList(ApprovalWorkflowListRequest $request)
    {
        $search = $request->search ?? '';
        $order = $request->order ?? 'asc';
        $sortBy = $request->sort_by ?? 'name';
        $limit = $request->limit ?? 10;

        // Get all approval workflow with pagination
        $approvalWorkflows = ApprovalWorkflow::where('type', $this->type)
            ->where('name', 'like', '%' . $search . '%')
            ->orderBy($sortBy, $order);
        if($request->restricted) {
            $approvalWorkflows = $approvalWorkflows->select('id', 'name');
            $approvalWorkflows = ($limit) ? $approvalWorkflows->paginate($limit) : $approvalWorkflows->get()->toArray();
            return successResponse('', $approvalWorkflows);
        }
        $approvalWorkflows = $approvalWorkflows->paginate($limit);

        return successResponse('', $approvalWorkflows);
    }

    /**
     * Function to get Approval workflow based on approval workflow id
     * @param $approvalWorkflowId
     * @return response
     */
    public function getApprovalWorkflow($approvalWorkflowId)
    {
        // Check approval workflow exists
        $approvalWorkflow = ApprovalWorkflow::find($approvalWorkflowId);

        if (is_null($approvalWorkflow)) {
            return errorResponse(Lang::get('lang.not_found'));
        }

        $data = [];
        $this->formatApprovalWorkflow($data, $approvalWorkflow);


        return successResponse('', $data);
    }


    /**
     * Function to format Approval workflow
     * @param array $data
     * @param ApprovalWorkflow $approvalWorkflow
     * @return void
     */
    private function formatApprovalWorkflow(&$data, $approvalWorkflow)
    {
        $approvalLevels = $approvalWorkflow->approvalLevels()
            ->with(['approveUsers', 'approveUserTypes'])->orderBy('order', 'asc')->get();
        // Approval workflow data
        $data['id']   = $approvalWorkflow->id;
        $data['name'] = $approvalWorkflow->name;
        $data['type'] = $approvalWorkflow->type;
        $data['action_on_approve'] = $approvalWorkflow->actionOnApprove()->first();
        $data['action_on_deny']    = $approvalWorkflow->actionOnDeny()->first();
        // event to update action on approve and deny statuses
        Event::dispatch('approvalworkflow-action-status', [&$data]); 
        $data['name']    = $approvalWorkflow->name;
        $data['user_id'] = $approvalWorkflow->user_id;
        $data['levels']  = [];

        $this->formatApprovalWorkflowLevels($data, $approvalLevels);
    }

    /**
     * Function to format approval workflow levels
     * @param array $data
     * @param ApprovalLevel $approvalLevels
     * @return void
     */
    private function formatApprovalWorkflowLevels(&$data, $approvalLevels)
    {
        // Approval workflow levels
        foreach ($approvalLevels as $level) {
            $levelData['id']                      = $level->id;
            $levelData['name']                    = $level->name;
            $levelData['match']                   = $level->match;
            $levelData['order']                   = $level->order;
            $levelData['approvers']['users']      = [];
            $levelData['approvers']['user_types'] = [];

            // Approver users
            $levelData['approvers']['users'] = $this->getApprovers($level->approveUsers);

            // Approver user types
            $levelData['approvers']['user_types'] = $this->getApprovers($level->approveUserTypes);

            array_push($data['levels'], $levelData);
        }
    }

    /**
     * Function to delete approval workflow based on approval workflow id or it's level id
     * @param $approvalId (it could be either approval workflow id or level id)
     * @param $type (either "workflow" or "level")
     * @return response
     */
    public function deleteApprovalWorkflow($approvalWorkflowOrLevelId, $type)
    {
        if ($type == 'workflow') {
            // Check approval workflow exists
            $approvalWorkflow = ApprovalWorkflow::where([['id', $approvalWorkflowOrLevelId], ['type', $this->type]])->first();
            if (is_null($approvalWorkflow)) {
                return errorResponse(Lang::get('lang.not_found'));
            }
            $approvalWorkflow->delete();
            $response = Lang::get('lang.deleted_successfully');
        }
        else if ($type == 'level') {
            // Check approval workflow level exists
            $approvalLevel = ApprovalLevel::find($approvalWorkflowOrLevelId);
            if (is_null($approvalLevel)) {
                return errorResponse(Lang::get('lang.level_not_found'));
            }
            $approvalLevel->delete();
            $response = Lang::get('lang.level_deleted_successfully');
        }
        else {
            return errorResponse(Lang::get('lang.invalid_type'));
        }
        return successResponse($response);
    }

    /**
     * Function to get approvers for user type and user
     * @param User/UserType $approvers
     * @return array $approverData
     */
    private function getApprovers($approvers)
    {
        $approverData = [];

        if ($approvers) {
            foreach ($approvers as $approver) {
                $approverData[] = [
                    'id'   => $approver->id,
                    'name' => $approver instanceof User ? $approver->full_name : $approver->name,
                ];
            }
        }

        return $approverData;
    }
}
