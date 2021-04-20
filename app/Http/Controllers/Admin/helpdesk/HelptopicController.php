<?php

namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\HelptopicRequest;
use App\Http\Requests\helpdesk\HelptopicUpdate;
// models
use App\Model\helpdesk\Agent\Agents;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Form\Forms;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Manage\HeltopicAssignType;
use App\Model\helpdesk\Manage\Tickettype;
use App\User;
// classes
use DB;
use Exception;
use Lang;

/**
 * HelptopicController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class HelptopicController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return type vodi
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('roles');
    }

    /**
     * Display a listing of the helptopic.
     *
     * @param type Help_topic $topic
     *
     * @return type Response
     */
    public function index(Help_topic $topic) {
        try {
            $topics = $topic->get();

            return view('themes.default1.admin.helpdesk.manage.helptopic.index', compact('topics'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new helptopic.
     *
     * @param type Priority   $priority
     * @param type Department $department
     * @param type Help_topic $topic
     * @param type Form_name  $form
     * @param type Agents     $agent
     * @param type Sla_plan   $sla
     *
     * @return type Response
     */
    /*
      ================================================
      | Route to Create view file passing Model Values
      | 1.Department Model
      | 2.Help_topic Model
      | 3.Agents Model
      | 4.Sla_plan Model
      | 5.Forms Model
      ================================================
     */
    public function create(Department $department, Help_topic $topic) {
        try {
            $departments = $department->get();
            $topics = $topic->get();
            return view('themes.default1.admin.helpdesk.manage.helptopic.create', compact('departments', 'topics'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Store a newly created helptpoic in storage.
     *
     * @param type Help_topic       $topic
     * @param type HelptopicRequest $request
     *
     * @return type Response
     */
    public function store(Help_topic $topic, HelptopicRequest $request) {
        try {
            $linked_department = implode(',', $request->get('linked_departments'));
            $request->merge(['linked_departments' => $linked_department]);
            if ($request->get('department') == 0 || $request->get('department') == '0') {
                $department = \DB::table('settings_system')->select('department')->where('id', '=', 1)->first()->department;
                $request->merge(['department' => $department]);
            }
            /* Check whether function success or not */
            $topic->fill($request->except('custom_form'))->save();
            $tkt_types = $request->ticket_type;
            if ($tkt_types) {
                foreach ($tkt_types as $tkt_type) {
                    $assign_type = new HeltopicAssignType;
                    $assign_type->helptopic_id = $topic->id;
                    $assign_type->type_id = $tkt_type;
                    $assign_type->save();
                }
            }
            // $topics->fill($request->except('custom_form','auto_assign'))->save();
            /* redirect to Index page with Success Message */
            return redirect('helptopic')->with('success', Lang::get('lang.helptopic_saved_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('helptopic')->with('fails', Lang::get('lang.helptopic_can_not_create') . '<li>' . $e->getMessage() . '</li>');
        }
    }

    /**
     * Show the form for editing the specified helptopic.
     *
     * @param type            $id
     * @param type Priority   $priority
     * @param type Department $department
     * @param type Help_topic $topic
     * @param type Form_name  $form
     * @param type Agents     $agent
     * @param type Sla_plan   $sla
     *
     * @return type Response
     */
    public function edit($id, Department $department, Help_topic $topic) {
        try {
            $departments = $department->get();
            $topics = $topic->whereId($id)->first();
            if ($topics->linked_departments != null && $topics->linked_departments != '') {
                $linked_departments = explode(',', $topics->linked_departments);
                $dept = $department->whereIn('id', $linked_departments)->pluck('id')->toArray();
            } else {
                $dept = $department->where('id', '=', $topics->department)->pluck('id')->toArray();
            }
            $tk_type = Tickettype::all();

            $assign_type = HeltopicAssignType::where('helptopic_id', '=', $id)->pluck('type_id')->toArray();

            $sys_help_topic = \DB::table('settings_ticket')
                            ->select('help_topic')
                            ->where('id', '=', 1)->first();

            return view('themes.default1.admin.helpdesk.manage.helptopic.edit', compact('departments', 'topics', 'dept', 'sys_help_topic', 'tk_type', 'assign_type'));
        } catch (Exception $e) {
            return redirect('helptopic')->with('fails', '<li>' . $e->getMessage() . '</li>');
        }
    }

    /**
     * Update the specified helptopic in storage.
     *
     * @param type                 $id
     * @param type Help_topic      $topic
     * @param type HelptopicUpdate $request
     *
     * @return type Response
     */
    public function update($id, Help_topic $topic, HelptopicUpdate $request) {
        try {
            $linked_department = implode(',', $request->get('linked_departments'));
            $request->merge(['linked_departments' => $linked_department]);
            if ($request->get('department') == 0 || $request->get('department') == '0') {
                $department = \DB::table('settings_system')->select('department')->where('id', '=', 1)->first()->department;
                $request->merge(['department' => $department]);
            }

            $topics = $topic->whereId($id)->first();

            /* Check whether function success or not */
            $topics->fill($request->except('custom_form'))->save();

            $delete_type = HeltopicAssignType::where('helptopic_id', '=', $id)->delete();
            $tkt_types = $request->ticket_type;
            // dd($tkt_types);
            if ($tkt_types) {
                foreach ($tkt_types as $tkt_type) {
                    $assign_type = new HeltopicAssignType;
                    $assign_type->helptopic_id = $topics->id;
                    $assign_type->type_id = $tkt_type;
                    $assign_type->save();
                }
            }
            if ($request->input('sys_help_tpoic') == 'on') {
                \DB::table('settings_ticket')
                        ->where('id', '=', 1)
                        ->update(['help_topic' => $id]);
                 Help_topic::where('id', $id)->update(['status'=>1]);
            }
            /* redirect to Index page with Success Message */
            return redirect('helptopic')->with('success', Lang::get('lang.helptopic_updated_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('helptopic')->with('fails', Lang::get('lang.helptopic_can_not_update') . '<li>' . $e->getMessage() . '</li>');
        }
    }

    /**
     * Remove the specified helptopic from storage.
     *
     * @param type int        $id
     * @param type Help_topic $topic
     *
     * @return type Response
     */
    public function destroy($id, Help_topic $topic, Ticket $ticket_setting) {
        $ticket_settings = $ticket_setting->where('id', '1')->first();
        $assign_type = HeltopicAssignType::where('helptopic_id', $id)->count();

         $querys = DB::table('sla_plan')
                            ->whereRaw('FIND_IN_SET(?,apply_sla_helptopic)', [$id])
                            ->pluck('id')->toArray();
            if ($querys) {
                return redirect()->back()->with('fails', Lang::get('lang.you_cannot_delete_this_helptopic,helptopic_associated_sla_plan'));
            }
        if ($ticket_settings->help_topic == $id ) {
            return redirect('helptopic')->with('fails', Lang::get('lang.you_cannot_delete_default_help_topic'));
        }
        elseif ($assign_type > 0) {
            return redirect('helptopic')->with('fails', Lang::get('lang.you_cannot_delete_this_helptopic,helptopic_associated_ticket_type'));
        }
         else {
            $tickets = DB::table('tickets')->where('help_topic_id', '=', $id)->update(['help_topic_id' => $ticket_settings->help_topic]);
            if ($tickets > 0) {
                if ($tickets > 1) {
                    $text_tickets = 'Tickets';
                } else {
                    $text_tickets = 'Ticket';
                }
                $ticket = '<li>' . $tickets . ' ' . $text_tickets . Lang::get('lang.have_been_moved_to_default_help_topic') . ' </li>';
            } else {
                $ticket = '';
            }
            $emails = DB::table('emails')->where('help_topic', '=', $id)->update(['help_topic' => $ticket_settings->help_topic]);
            if ($emails > 0) {
                if ($emails > 1) {
                    $text_emails = 'Emails';
                } else {
                    $text_emails = 'Email';
                }
                $email = '<li>' . $emails . ' System ' . $text_emails . Lang::get('lang.have_been_moved_to_default_help_topic') . ' </li>';
            } else {
                $email = '';
            }
            $message = $ticket . $email;
            $topics = $topic->whereId($id)->first();
            /* Check whether function success or not */
            try {
                $topics->delete();
                /* redirect to Index page with Success Message */
                return redirect('helptopic')->with('success', Lang::get('lang.helptopic_deleted_successfully') . $message);
            } catch (Exception $e) {
                /* redirect to Index page with Fails Message */
                return redirect('helptopic')->with('fails', Lang::get('lang.helptopic_can_not_update') . '<li>' . $e->getMessage() . '</li>');
            }
        }
    }

}
