<?php

namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App\Http\Controllers\Common\NotificationController;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Common\FileuploadController;
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\CreateTicketRequest;
use App\Http\Requests\helpdesk\TicketRequest;
use App\Http\Requests\helpdesk\PriorityRequest;
use App\Http\Requests\helpdesk\PriorityUpdateRequest;
// models
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Form\Fields;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla_plan;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Notification\Notification;
use App\Model\helpdesk\Notification\UserNotification;
use App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Settings\Email;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Ticket\Ticket_attachments;
use App\Model\helpdesk\Ticket\Ticket_Collaborator;
use App\Model\helpdesk\Ticket\Ticket_Form_Data;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Ticket\Ticket_source;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Utility\CountryCode;
use App\Model\helpdesk\Utility\Date_time_format;
use App\Model\helpdesk\Utility\Timezones;
use App\User;
use Auth;
use DB;
use Exception;
use ForceUTF8\Encoding;
// classes
use Hash;
use Illuminate\Http\Request;
use Illuminate\support\Collection;
use Input;
use Lang;
use Mail;
use PDF;
use UTC;

/**
 * TicketController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class PriorityController extends Controller {

    public function __construct(PhpMailController $PhpMailController, NotificationController $NotificationController) {
        $this->middleware('auth');
        $this->middleware('roles')->only('priorityIndex','priorityCreate','priorityEdit','priorityEdit1','destroy');
        $this->PhpMailController = $PhpMailController;
        $this->NotificationController = $NotificationController;
        
    }

    /**
     * Show the Inbox ticket list page.
     *
     * @return type response
     */
    public function priorityIndex() {
        $user_status=CommonSettings::where('option_name','=', 'user_priority')->first();
        return view('themes.default1.admin.helpdesk.manage.ticket_priority.index', compact('user_status'));
    }
       /**
     * Show the Inbox ticket list page.
     *
     * @return type response
     */


    public function userPriorityIndex(Request $request) 
    {
          try {
        $user_status= $request->user_settings_priority;
        CommonSettings::where('option_name','=', 'user_priority')->update(['status' => $user_status]);
        
     return Lang::get('lang.your_status_updated_successfully');
         } catch (Exception $e) {
            return Redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function priorityIndex1() 
    {
        try {
            $ticket = new Ticket_Priority();
            $tickets = $ticket->select('priority_id', 'priority', 'priority_desc', 'priority_color', 'status', 'is_default', 'ispublic')->get();

            return \DataTables::of($tickets)
                            // ->showColumns('priority', 'priority_desc')

                            ->editColumn('priority', function($model) {

                                 if ($model->is_default > 0) {
                                if (strlen($model->priority) > 25) {
                                    return '<p title="' . $model->priority . '">' . mb_substr($model->priority, 0, 30, 'UTF-8') . '...</p> ( Default )';
                                } else {
                                    return "$model->priority ( Default )";
                                }

                            }
                            else{

                                if (strlen($model->priority) > 25) {
                                    return '<p title="' . $model->priority . '">' . mb_substr($model->priority, 0, 30, 'UTF-8') . '...</p>';
                                } else {
                                    return $model->priority;
                                }

                            }

                             })
                            ->editColumn('priority_color', function($model) {
                                return "<button class='btn' style = 'background-color:$model->priority_color'></button>";
                            })
                            ->editColumn('status', function($model) {

                                 if ($model->status == 1) {
                return '<p class="btn btn-xs btn-default" style="pointer-events:none;color:green">'.Lang::get('lang.active').'</p>';
            }
            return '<p class="btn btn-xs btn-default" style="pointer-events:none;color:red">'.Lang::get('lang.inactive').'</p>';
                                // if ($model->status == 1) {
                                //     return "<a style='color:green'>active</a>";
                                // } elseif ($model->status == 0) {
                                //     Ticket_Priority::where('priority_id', '=', '$priority_id')
                                //     ->update(['priority_id' => '']);
                                //     return "<a style='color:red'>inactive</a>";
                                // }
                            })
                            ->addColumn('action', function($model) {
                                if ($model->is_default > 0) {
                                    return "<a href=" . url('ticket/priority/' . $model->priority_id . '/edit') . " class='btn btn-primary btn-xs'><i class='fas fa-edit' style='color:white;'></i>&nbsp;Edit</a>&nbsp;&nbsp;<button class='btn btn-primary btn-xs' disabled='disabled' ><i class='fas fa-trash' style='color:white;'></i>&nbsp;Delete</button>";
                                } else {
                                    
                                        $url = url('ticket/priority/'.$model->priority_id.'/destroy');
                                        $confirmation = deletePopUp($model->priority_id, $url, "Delete",'btn btn-primary btn-xs');
                                                        
                                    return "<a href=" . url('ticket/priority/' . $model->priority_id . '/edit') . " class='btn btn-primary btn-xs'><i class='fas fa-edit' style='color:white;'></i>&nbsp;Edit</a>&nbsp;&nbsp;"
                                            .$confirmation;
                                }
                            })
                              ->escapeColumns(['link' => 'link'])
                            ->rawColumns(['priority_color', 'status', 'action'])
                            ->make();
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function priorityCreate() {
        return view('themes.default1.admin.helpdesk.manage.ticket_priority.create');
    }
/**
 * 
 * @param PriorityRequest $request
 * @return type
 */
    public function priorityCreate1(PriorityRequest $request) {
        try{
      
        $tk_priority = new Ticket_Priority;
        $tk_priority->priority = $request->priority;
        $tk_priority->status = $request->status;
        $tk_priority->priority_desc = $request->priority_desc;
        $tk_priority->priority_color = $request->priority_color;
        $tk_priority->ispublic = $request->ispublic;
        $tk_priority->save();
        if ($request->input('default_priority') == "1") {
            Ticket_Priority::where('is_default', '>', 0)
                    ->update(['is_default' => 0]);
            Ticket_Priority::where('priority_id', '=', $tk_priority->priority_id)
                    ->update(['is_default' => 1,'status'=>1]);

        }
        return \Redirect::route('priority.index')->with('success', Lang::get('lang.priority_saved_successfully'));

         } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $priority_id
     * @return type
     */
    public function priorityEdit($priority_id) {

        try{
       $tk_priority = Ticket_Priority::wherepriority_id($priority_id)->first();
      
        return view('themes.default1.admin.helpdesk.manage.ticket_priority.edit', compact('tk_priority'));
     } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }

    }

    /**
     * 
     * @param PriorityRequest $request
     * @return type
     */
    public function priorityEdit1($priority_id,PriorityUpdateRequest $request) {

         try {
        $tk_priority = Ticket_Priority::findOrFail($priority_id);
        $tk_priority->priority = $request->priority;
        $tk_priority->status = $request->status;
        $tk_priority->priority_desc = $request->priority_desc;
        $tk_priority->priority_color = $request->priority_color;
        $tk_priority->ispublic = $request->ispublic;
        $tk_priority->save();
        if ($request->input('default_priority') == 'on') {
            Ticket_Priority::where('is_default', '>', 0)
                    ->update(['is_default' => 0]);
            Ticket_Priority::where('priority_id', '=', $priority_id)
                  ->update(['is_default' => 1,'status'=>1]);
        }
        return \Redirect::route('priority.index')->with('success', (Lang::get('lang.priority_updated_successfully')));

         } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $priority_id
     * @return type
     */
    public function destroy($priority_id) {

        try {
            $default_priority = Ticket_Priority::where('is_default', '>', '0')->first();
            $ticket_check_priority = Tickets::where('priority_id', '=', $priority_id)->count();
            if ($ticket_check_priority > 0) {
                return \Redirect::route('priority.index')->with('fails', Lang::get('lang.you_cannot_delete_this_priority,this_priority_applied_some_tickets'));
            }
            $sla_check_priority = \App\Model\helpdesk\Manage\Sla\SlaTargets::where('priority_id', '=', $priority_id)->count();

            if ($sla_check_priority > 0) {
                return \Redirect::route('priority.index')->with('fails', Lang::get('lang.you_cannot_delete_this_priority,this_priority_applied_sla_plan'));
            }
            $tk_priority = Ticket_Priority::findOrFail($priority_id);
            $tk_priority->delete();
            return \Redirect::route('priority.index')->with('success', (Lang::get('lang.priority_deleted_successfully')));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

}
