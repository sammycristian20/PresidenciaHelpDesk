<?php

namespace App\Http\Controllers\Type;

// controllers
use App\Http\Controllers\Common\NotificationController;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Common\FileuploadController;
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\CreateTicketRequest;
use App\Http\Requests\helpdesk\TicketRequest;
use App\Http\Requests\helpdesk\TickettypeRequest;
use App\Http\Requests\helpdesk\TickettypeUpdateRequest;
// models
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Form\Fields;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Tickettype;
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
class TicketTypeController extends Controller {

    public function __construct(PhpMailController $PhpMailController, NotificationController $NotificationController) {
        $this->PhpMailController = $PhpMailController;
        $this->NotificationController = $NotificationController;
        $this->middleware('auth');
        $this->middleware('roles');
    }

    /**
     * Show the Inbox ticket list page.
     *
     * @return type response
     */
    public function typeIndex() {
        try {
            return view('themes.default1.admin.helpdesk.manage.tickettype.index');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function typeIndex1() {
        try {
            $ticket_type = new Tickettype();
            $ticket_type = $ticket_type->select('id', 'name', 'status', 'type_desc', 'ispublic', 'is_default')->get();
            // dd( $ticket_type);

            return \DataTables::of($ticket_type)
                            // ->showColumns('name', 'type_desc')
                          
                            ->editColumn('name', function($model) {
                                if ($model->is_default > 0) {
                                if (strlen($model->name) > 25) {
                                    return '<p title="' . $model->name . '">' . mb_substr($model->name, 0, 30, 'UTF-8') . '...</p> ( Default )';
                                } else {
                                    return "$model->name ( Default )";
                                }

                            }
                            else{

                                if (strlen($model->name) > 25) {
                                    return '<p title="' . $model->name . '">' . mb_substr($model->name, 0, 30, 'UTF-8') . '...</p>';
                                } else {
                                    return $model->name;
                                }

                            }

                             })
                            ->editColumn('type_desc', function($model) {
                                if (strlen($model->type_desc) > 25) {
                                    return '<p title="' . $model->type_desc . '">' . mb_substr($model->type_desc, 0, 30, 'UTF-8') . '...</p>';
                                } else {
                                    return $model->type_desc;
                                }
                            })
                            ->editColumn('status', function($model) {

                                if ($model->status == 1) {
                                    return '<p class="btn btn-xs btn-default" style="pointer-events:none;color:green">' . Lang::get('lang.active') . '</p>';
                                }
                                return '<p class="btn btn-xs btn-default" style="pointer-events:none;color:red">' . Lang::get('lang.inactive') . '</p>';
                            })
                            ->editColumn('action', function($model) {
                                if ($model->is_default > 0) {
                                    return "<a href=" . url('ticket-types/' . $model->id . '/edit') . " class='btn btn-primary btn-xs'><i class='fas fa-edit' style='color:white;'></i>&nbsp;Edit</a>&nbsp;&nbsp;<button class='btn btn-primary btn-primary btn-xs' disabled='disabled' ><i class='fas fa-trash' style='color:white;'>&nbsp</i>Delete </button>";
                                } else {
                                     $url = url('ticket-types/'.$model->id.'/destroy');
                                     $confirmation = deletePopUp($model->id, $url, "Delete",'btn btn-primary btn-xs', $btn_name
                                    = "Delete", $button_check = true, $methodName = 'delete');
                                    
                                    return "<a href=" . url('ticket-types/' . $model->id . '/edit') . " class='btn btn-primary btn-xs'><i class='fas fa-edit' style='color:white;'></i>&nbsp;Edit</a>&nbsp;&nbsp;".$confirmation;
                                }
                            })
                            ->escapeColumns(['link' => 'link'])
                            ->rawColumns(['status', 'action'])
                            ->make();
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function typeCreate() {
        return view('themes.default1.admin.helpdesk.manage.tickettype.create');
    }

    public function typeCreate1(TickettypeRequest $request) {
        try {
            $tk_type = new Tickettype;
            $tk_type->name = $request->name;
            $tk_type->status = $request->status;
            $tk_type->type_desc = $request->type_desc;
            $tk_type->ispublic = $request->ispublic;
            $tk_type->save();

             if ($request->input('default_ticket_type') == "1") {
                Tickettype::where('is_default', '>', '0')
                        ->update(['is_default' => '0']);
                Tickettype::where('id', '=', $tk_type->id)
                        ->update(['is_default' => $tk_type->id,'status'=>1]);
            }
            return \Redirect::route('ticket.type.index')->with('success', Lang::get('lang.ticket_type_saved_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $priority_id
     * @return type
     */
    public function typeEdit($id) {

        try {
            $tk_type = Tickettype::whereid($id)->first();

            return view('themes.default1.admin.helpdesk.manage.tickettype.edit', compact('tk_type'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param PriorityRequest $request
     * @return type
     */
    public function typeEdit1(TickettypeUpdateRequest $request) {
        try {
            $id = $request->tk_type_id;
            $tk_type = Tickettype::findOrFail($id);
            $tk_type->name = $request->name;
            $tk_type->status = $request->status;
            $tk_type->type_desc = $request->type_desc;

            $tk_type->ispublic = $request->ispublic;
            $tk_type->save();
            if ($request->input('default_ticket_type') == 'on') {
                Tickettype::where('is_default', '>', '0')
                        ->update(['is_default' => '0']);
                Tickettype::where('id', '=', $id)
                        ->update(['is_default' => $id,'status'=>1]);
            }
            return \Redirect::route('ticket.type.index')->with('success', Lang::get('lang.ticket_type_updated_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $priority_id
     * @return type
     */
    public function destroy($id) {
        try {


             $querys = DB::table('sla_plan')
                            ->whereRaw('FIND_IN_SET(?,apply_sla_tickettype)', [$id])
                            ->pluck('id')->toArray();
            if ($querys) {
                \Session::flash('fails', (Lang::get('lang.you_cannot_delete_this_type,type_associated_sla_plan')));
            }

           $tk_type = Tickettype::findOrFail($id);
           $tk_type->delete();
           \Session::flash('success', (Lang::get('lang.ticket_type_deleted_successfully')));
        } catch (Exception $ex) {
            \Session::flash('fails', $ex->getMessage());
        }
    }

}
