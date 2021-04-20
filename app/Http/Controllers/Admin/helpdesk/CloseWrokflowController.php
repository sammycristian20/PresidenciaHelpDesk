<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\WorkflowCloseRequest;
use App\Model\helpdesk\Workflow\WorkflowClose;
use App\Model\helpdesk\Ticket\Ticket_Status;
use Lang;

/**
 * |=================================================
 * |=================================================
 * In this controller the functionalities fo close ticket workflow defined.
 */
class CloseWrokflowController extends Controller
{
    private $security;

    public function __construct(WorkflowClose $security)
    {
        $this->security = $security;
        $this->middleware('auth');
        $this->middleware('roles');
    }

    /**
     * get the workflow settings page.
     *
     * @param \App\Model\helpdesk\Workflow\WorkflowClose $securitys
     *
     * @return type view
     */
    public function index(WorkflowClose $securitys, Ticket_Status $statuses)
    {
        try {
            $security = $securitys->whereId('1')->first();
            $ticketStatus = $security->ticketStatus()->pluck('id')->toArray();
            return view('themes.default1.admin.helpdesk.settings.close-workflow.index', compact('security', 'ticketStatus', 'statuses'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * method to update the workflow settings for closing ticket.
     * @param WorkflowClose $workflowClose
     * @param WorkflowCloseRequest $request  
     * @param Ticket_Status $statuses
     * @return response
     */
    public function update(WorkflowClose $workflowClose, WorkflowCloseRequest $request, Ticket_Status $statuses)
    {   
        if(in_array($request->input('status'),$request->input('ticket_status')))
        {
            return errorResponse(trans('lang.ticket_status_cannot_contain_target_status'));
        }
        try {
            $workflowClose = $workflowClose->whereId('1')->first();
            $workflowClose->days = $request->input('days');
            $workflowClose->send_email = $request->input('send_email');
            $workflowClose->status = $request->input('status');
            $statuses->whereNotIn('id', $request->input('ticket_status'))->update(['auto_close' => null]);
            $statuses->whereIn('id', $request->input('ticket_status'))->update(['auto_close' => 1]);
            $workflowClose->save();

            return successResponse(Lang::get('lang.saved_your_settings_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * method to get workflow settings data.
     * @param (WorkflowClose $workflowClose
     * @return response
     */
    public function getworkflowCloseSetingsDetails(WorkflowClose $workflowClose)
    {
        $workflowClose = $workflowClose->whereId('1')->with(['closeStatus:id,name'])->first();
        $ticketStatus = $workflowClose->ticketStatus()->select('id','name')->get()->toArray();
        $workflowClose->ticket_status = $ticketStatus; 
        $workflowClose = $workflowClose->toArray();
        $workflowClose['status'] = $workflowClose['close_status'];
        unset($workflowClose['close_status'],$workflowClose['id'],$workflowClose['condition'],$workflowClose['created_at']);
        return successResponse('', ['workflowClose' => $workflowClose]);
    }
}
