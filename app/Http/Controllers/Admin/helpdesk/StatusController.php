<?php

namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\CompanyRequest;
use App\Http\Requests\helpdesk\EmailRequest;
use App\Http\Requests\helpdesk\RatingUpdateRequest;
use App\Http\Requests\helpdesk\StatusRequest;
// use App\Http\Requests\helpdesk\StatusRequest;
use App\Http\Requests\helpdesk\StatusUpdateRequest;
// models
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Email\Template;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla_plan;
use App\Model\helpdesk\Notification\UserNotification;
use App\Model\helpdesk\Ratings\Rating;
use App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Settings\Email;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Utility\Date_format;
use App\Model\helpdesk\Utility\Date_time_format;
use App\Model\helpdesk\Utility\Time_format;
use App\Model\helpdesk\Utility\Timezones;
use App\Model\helpdesk\Workflow\WorkflowClose;
use App\Model\helpdesk\Settings\CommonSettings;
use DateTime;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\TicketStatusType;
use App\Model\helpdesk\Ticket\Tickets;
// classes
use DB;
use Exception;
use File;
use Illuminate\Http\Request;
use Input;
use Lang;
use Finder;

/**
 * SettingsController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class StatusController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->smtp();
        $this->middleware('auth');
        $this->middleware('roles');
    }

    /**
     * @param int $id
     * @param $compant instance of company table
     *
     * get the form for company setting page
     *
     * @return Response
     */
    public function getStatuses() {
        try {
            /* Direct to Company Settings Page */
            return view('themes.default1.admin.helpdesk.settings.status.index');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * @category functuin to return json data for rendering yajra datatable
     * @param
     * @return json
     */
    public function getStatusesTable()
    {
        $status = Ticket_Status::with('type')->select('ticket_status.*');

        return \DataTables::of($status)
            ->editColumn('name', function($status){
                $name = $status->name;
                if($status->default == 1) {
                    $name .= " (".Lang::get('lang.default').")";
                }
                return $name;
            })
            ->editColumn('visibility_for_client', function($status){
                $visibility_for_client = '<button class="btn btn-warning btn-xs" style="pointer-events:none">'.Lang::get('lang.no').'</button>';
                if($status->visibility_for_client) {
                    $visibility_for_client = '<button class="btn btn-success btn-xs" style="pointer-events:none">'.Lang::get('lang.yes').'</button>';
                }
                return $visibility_for_client;
            })
            ->editColumn('send_email', function($status){
                $send_emails_to = '';
                $i = 0;
                foreach ($status->send_email as $key => $value) {
                    if($value == 1) {
                        if ($i > 0) {
                            $send_emails_to .= ', ';
                        }
                        $send_emails_to .= ucfirst(str_replace('_', ' ', $key)).' ';
                        $i++;
                    }
                }

                if($send_emails_to == "Client , Assigned agent team "){
                    $send_emails_to='Client , Assignee';
                }
                 elseif($send_emails_to == "Assigned agent team , Admin "){
                    $send_emails_to='Admin , Assignee';
                }
                  elseif($send_emails_to == "Client , Assigned agent team , Admin "){
                    $send_emails_to='Client , Assignee , Admin';
                }

                elseif($send_emails_to == "Assigned agent team "){
                    $send_emails_to='Assignee';
                }



                return $send_emails_to;
            })
            ->editColumn('order', function($status){
                return $status->order;
            })
            ->editColumn('icon', function($status){
                return '<span style="color:'.$status->icon_color.'"><i class="'.$status->icon.'"></i><span>';
            })
            ->addColumn('action', function($status){
                $action = '<a href="'.route('status.edit',$status->id).'"><button class="btn btn-primary btn-xs"> <i class="fas fa-edit">&nbsp;</i>'.Lang::get('lang.edit').'</button></a>&nbsp;&nbsp';
                if ($status->default == 1) {
                    $action .= '<button class="btn btn-primary btn-xs" disabled> <i class="fas fa-trash">&nbsp;</i>'.Lang::get('lang.delete').'</button>';
                } else {
                    $action .= '<button  class="btn btn-primary btn-xs" onClick="showConfirmModer('.$status->id.')"> <i class="fas fa-trash">&nbsp;</i>'.Lang::get('lang.delete').'</button>';
                }
                return $action;
            })
            ->rawColumns(['name', 'visibility_for_client', 'order', 'icon', 'action'])
            ->make();
    }

    /**
     * create a status.
     *
     * @param \App\Model\helpdesk\Ticket\Ticket_Status  $statuss
     * @param \App\Http\Requests\helpdesk\StatusRequest $request
     *
     * @return type redirect
     */
    public function createStatuses(Ticket_Status $statuss) {

        // deleted(3),merged(8) status has to be skipped

        $status_types = TicketStatusType::whereNotIn('id', [3,8,7])->get();

        $all_status = Ticket_Status::pluck('name', 'id')->toArray();

        $statusWithVisibility = Ticket_Status::where('visibility_for_client', 1)->get(['id', 'name']);
        
        return view('themes.default1.admin.helpdesk.settings.status.create', compact('status_types', 'all_status', 'statusWithVisibility'));
    }

    /**
     * create a status.
     *
     * @param \App\Model\helpdesk\Ticket\Ticket_Status  $statuss
     * @param \App\Http\Requests\helpdesk\StatusRequest $request
     *
     * @return type redirect
     */
    public function storeStatuses(StatusRequest $request) {
        try {

            $statuss = new Ticket_Status;
            /* fetch the values of company from company table */
            $statuss->name = $request->input('name');
            $statuss->order = $request->input('sort');
            $statuss->icon = $request->input('icon_class');
            $statuss->icon_color = $request->input('icon_color');
            if ($request->input('visibility_for_client') == 'yes') {
                $statuss->visibility_for_client = 1;
                $statuss->secondary_status = null;
            } else {
                $statuss->visibility_for_client = 0;
                $statuss->secondary_status = $request->input('secondary_status');
            }
            $statuss->purpose_of_status = $request->input('purpose_of_status');
            $default_send = ['client'=>'0','admin'=>'0','assigned_agent_team'=>'0'];
            $send  = $request->input('send',$default_send);
            $statuss->send_email = json_encode($send);
            $statuss->message = $request->message;
            if ($request->filled('send_sms')) {
                $statuss->send_sms = $request->input('send_sms');
            }
            $statuss->allow_client = $request->allow_client;

            if ($request->default == 'on' || $request->default == 1) {
                $default_statuses = Ticket_Status::where('purpose_of_status', $request->purpose_of_status)->get();
                foreach ($default_statuses as $default_status) {
                    $default_status->default = null;
                    $default_status->save();
                }
                $statuss->default = 1;
            }
            $statuss->halt_sla = $request->input('halt_sla');
            $statuss->comment = $request->input('comment');

            $statuss->save();

            if ($request->filled('target_status')) {
                foreach ($request->get('target_status') as $value) {
                    $statuss->overrideStatuses()->create([
                        'current_status' => $statuss->id,
                        'target_status'  => $value
                    ]);
                }
            }
            /* Direct to Company Settings Page */
            return redirect()->route('statuss.index')->with('success', Lang::get('lang.status_saved_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * @param int $id
     * @param $compant instance of company table
     *
     * get the form for company setting page
     *
     * @return Response
     */
    public function getEditStatuses($id) {
        try {
            /* fetch the values of company from company table */
            $status = Ticket_Status::find($id);

            if(!$status) return redirect()->to('setting-status')->with('fails', 'not found');
            
            /* Direct to Company Settings Page */
            $target_status = $status->overrideStatuses()->pluck('target_status')->toArray();

            /* all status */
            $all_status = Ticket_Status::pluck('name', 'id')->toArray();
            // foreach($status->overrideStatuses()->get() as $os) {
            //     dump($os->fromStatus()->value('name'));
            // }

            $statusWithVisibility = Ticket_Status::where([['id', '<>', $id], ['visibility_for_client', 1]])->get(['id', 'name']);

            return view('themes.default1.admin.helpdesk.settings.status.edit', compact('status', 'all_status', 'target_status', 'statusWithVisibility'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @param $compant instance of company table
     *
     * get the form for company setting page
     *
     * @return Response
     */
    public function editStatuses($id, StatusUpdateRequest $request)
    {
        try {
            $status = Ticket_Status::whereId($id)->first();
            if (!$request->filled('default')) {
                $isThereADefaultStatus = Ticket_Status::where('purpose_of_status', $status->purpose_of_status)
                    ->where('default', 1)->where('id', '!=', $id)->count();
                if ($isThereADefaultStatus == 0) {
                    return redirect()->back()->with('fails', trans('lang.can_not_remove_default_status_without_making_new_as_default'));
                }
            }

            $default_send = ['client'=>'0','admin'=>'0','assigned_agent_team'=>'0'];
            $send  = $request->input('send',$default_send);
            if(($status->purpose_of_status != $request->input('purpose_of_status'))) {
                $ticket_with_same_status = Tickets::where('status', $status->id)->first();
                if (isset($ticket_with_same_status)) {
                    return redirect()->back()->with('fails', Lang::get('lang.unable_to_change_the_purpose_of_status_there_are_tickets_with_this_status'));
                } else if($status->default){
                    return redirect()->back()->with('fails', Lang::get('lang.purpose_of_status_of_default_status_cannot_be_changed'));
                } else {
                    $status->purpose_of_status = $request->input('purpose_of_status');
                }
            }
            if ($request->filled('send_sms')) {
                $status->send_sms = $request->send_sms;
            }
            $status->send_email=json_encode($send);
            $status->message = $request->message;

            /* fetch the values of company from company table */
            $status->name = $request->input('name');
            $status->order = $request->input('sort');
            $status->icon = $request->input('icon_class');
            $status->icon_color = $request->input('icon_color');
            if ($request->input('visibility_for_client') == '1') {
                $status->secondary_status = null;
            } else {
                $status->secondary_status = $request->input('secondary_status');
            }

            $status->visibility_for_client = $request->input('visibility_for_client');

            $status->allow_client = $request->allow_client;

            if ($request->default == 'on' || $request->default == 1) {
                $default_statuses = Ticket_Status::where([
                    ['purpose_of_status', '=' ,$request->purpose_of_status],
                    ['id', '!=', $status->id]])->get();
                foreach ($default_statuses as $default_status) {
                    $default_status->default = null;
                    $default_status->save();
                }
                $status->default = 1;
            }
            $status->halt_sla = $request->input('halt_sla');
            $status->comment = $request->input('comment');
            /* save override statuses */
            $status->overrideStatuses()->delete();
            if($request->filled('target_status')) {
                foreach ($request->get('target_status') as $value) {
                    $status->overrideStatuses()->create([
                        'current_status' => $id,
                        'target_status'  => $value
                    ]);
                }
            }
            $status->save();
            /* Direct to Company Settings Page */
            return redirect()->route('statuss.index')->with('success', Lang::get('lang.status_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * delete a status.
     *
     * @param type $id
     *
     * @return type redirect
     */
    public function deleteStatuses($id) {
        try {
            if (Finder::getTicketDefaultStatus() == $id) {
                return errorResponse(Lang::get('lang.status_is_used_as_default_status_for_tickets'));
            }
            $status_to_delete = Ticket_Status::whereId($id)->first();
            if ($status_to_delete->default == 1 || $id == Finder::statusApproval()) {
                return errorResponse(Lang::get('lang.you_cannot_delete_a_default_ticket_status'));
            }

            $ticket_with_status = Tickets::where('status', $id)->first();
            $default_status = Finder::defaultStatus($status_to_delete->purpose_of_status);
            $default_status = (!$default_status) ? Finder::defaultStatus(1) : $default_status;
            $message = trans('lang.status_delete_successfully');
            if (isset($ticket_with_status)) {
                $tickets = DB::table('tickets')->where('status', '=', $id)->update(['status' => $default_status->id]);
                $message = '<li>' . trans('lang.associated_tickets_moved_to_default_status') . '<li>' .trans('lang.status_deleted_successfully');
            }
            if($status_to_delete->workflowClose) {
                \App\Model\helpdesk\Workflow\WorkflowClose::where('status', $id)->update(['status' => $default_status->id]);
            }
            $status_to_delete->overrideStatuses()->delete();
            $status_to_delete->delete();
             return successResponse($message);
            
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

}