<?php

namespace App\Http\Controllers\Client\helpdesk;

// controllers
use App\Http\Controllers\Agent\helpdesk\TicketWorkflowController;
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\Ticket\ClientPanelTicketRequest;
use App\Model\helpdesk\Agent\Department;
// models
use App\Model\helpdesk\Form\Fields;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Ticket_attachments;
use App\Model\helpdesk\Ticket\Ticket_source;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Utility\CountryCode;
use App\User;
use Exception;
// classes
use Form;
use Illuminate\Http\Request;
use Input;
use Lang;
use Redirect;
use App\Model\helpdesk\Manage\Tickettype;
use App\Location\Models\Location;



/**
 * FormController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class FormController extends Controller
{
    /**
     * Create a new controller instance.
     * Constructor to check.
     *
     * @return void
     */
    public function __construct(TicketWorkflowController $TicketWorkflowController)
    {
        $this->middleware('board');
        // creating a TicketController instance
        $this->TicketWorkflowController = $TicketWorkflowController;
    }
    /**
     * getform.
     *
     * @param type Help_topic $topic
     *
     * @return type
     */
    public function getForm(Help_topic $topic, CountryCode $code)
    {
        try {
            \Event::dispatch('client.create.ticket.form');
            if (\Config::get('database.install') == '%0%') {
                return \Redirect::route('licence');
            }
            $login_mandatory = CommonSettings::select('status')->where('option_name', '=', 'allow_users_to_create_ticket')->first();
            if (!\Auth::check() && ($login_mandatory->status == 1 || $login_mandatory->status
                    == '1')) {
                return redirect('auth/login')->with(['error' => Lang::get('lang.login-required-for-ticket-creation'), 'referer' => 'form']);
            }
            if (System::first()->status == 1) {
                $topics = $topic->get();
                $codes  = $code->get();

                return view('themes.default1.client.helpdesk.form', compact('topics', 'codes'));
            }
            else {
                return \Redirect::route('home');
            }
        } catch (\Exception $e) {
            return redirect('/')->with('fails', $e->getMessage());
        }
    }

    /**
     * Posted form.
     *
     * @param type Request $request
     * @param type User    $user
     */
    public function postedForm(User $user, ClientPanelTicketRequest $request, Ticket $ticket_settings, Ticket_source $ticket_source, Ticket_attachments $ta, CountryCode $code)
    {
        try {
            $phone          = "";
            $collaborator   = null;
            $auto_response  = 0;
            $team_assign    = null;
            $sla            = "";
            $email          = null;
            $username           = null;
            $mobile_number  = null;
            $phonecode      = "";
            $user           = "";
            $domain_id      ="";
            $default_values = ['Requester', 'Requester_email', 'Requester_name', 'media_option',
                'Requester_mobile', 'Help_Topic', 'cc', 'Help Topic',
                'Requester_mobile', 'Requester_code', 'Help Topic', 'Assigned', 'Subject',
                'subject', 'priority', 'help_topic', 'body', 'Description', 'Priority',
                'Type', 'Status', 'attachment', 'inline', 'email', 'first_name','company','org_dept',
                'last_name', 'mobile', 'country_code', 'api', 'sla', 'dept', 'code',
                'user_id', 'media_attachment', 'requester', 'status', 'assigned', 'description', 'type', 'media_option', 'Department', 'department','domain_id','location'];

            $form_extras = $request->except($default_values);
            \Config::set('app.custom-fields', $form_extras);



            $requester = $request->input('requester');

            //if username is set, check for username
            // if found, all email and phone numbers will be of that user
        	  $user = User::where('user_name',$requester)->orWhere(function($q) use($requester){
              $q->where('email', $requester)->where('email', '!=', null);
            })->first();

            $email = $user->email;

            $username = $user->user_name;
            
            $mobile_number = $user->mobile;


           if (isset($requester['phone_code'])) {
                $phonecode = $requester['phone_code'];
            }
            elseif ($user != null) {
                $phonecode = $user->country_code;
            } else {
                $phonecode = 0;
            }

            if ($request->input('help_topic_id')) {
                $helptopic = $request->input('help_topic_id');
                $help = Help_topic::where('id', $helptopic)->first();
            } else {
                $defaultHelptopicId = Ticket::where('id',1)->value('help_topic');
                $help = Help_topic::where('id',$defaultHelptopicId)->first();
                $helptopic = $help->id;
            }

            $department = ($request->input('department_id')) ? $request->input('department_id') : (($help->department) ? $help->department : (int)defaultDepartmentId());

            if ($request->filled('location_id')) {
                $locationId = $request->input('location_id');
            } elseif ($user->location) {
                $locationId = $user->location; //added to satisfy ACT requirement to see user location on ticket details page
            }
            else {
                $locationId = null;
            }


            if ($request->filled('assigned_id')) {
                $assignto = $request->input('assigned_id');
            }
            else {
                $assignto = null;
            }
            if ($request->filled('subject')) {
                $subject = $request->input('subject');
            }
            else {
                $subject = null;
            }
            if ($request->filled('description')) {
                $details = $request->input('description');
            }
            else {
                $details = null;
            }
            if ($request->filled('priority_id')) {
                $priority = $request->input('priority_id');
            }
            else {
                $priority = null;
            }
            if ($request->input('type_id')) {
                $type = $request->input('type_id');
            }
            else {
                $default_type = Tickettype::where('is_default', '>', 0)->select('id')->first();
                $type         = $default_type->id;
            }
            if ($request->input('status_id')) {
                $status = $request->input('status_id');
            }
            else {
                $status = $ticket_settings->first()->status;
            }
            if ($request->input('source_id')) {
                $source = $request->input('source_id');
            }
            else {
                $source = Ticket_source::where('name', '=', 'web')->first()->id;
            }
            $company = "";
            if ($request->filled('company'))
            {
                $company = $request->input('company');
            }
            $attach        = [];
            $media_attach  = [];
            if ($request->filled('media_attachment'))
            {
                $media_attach = $request->input('media_attachment');
            }
            if ($request->file()) {
                $attach = $request->file();
            }

            $attachment = array_merge($attach, $media_attach);
            $domainId=($request->filled('domain_id'))?$request->input('domain_id'):0;

            \Event::dispatch(new \App\Events\ClientTicketFormPost($form_extras, $email, $source, $department));
            $respnse    = $this->TicketWorkflowController->workflow($email, $username, $subject, $details, $phone, $phonecode, $mobile_number, $helptopic, $sla, $priority, $source, $collaborator, $department, $assignto, $team_assign, $status, $form_extras, $auto_response, $type, $attachment, [], [], $company,$domainId,$locationId);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }

        $ticket = Tickets::where('ticket_number', '=', $respnse[0])->select('id','ticket_number')->first();
        $ticketLink = \Config::get('app.url')."/check-ticket/$ticket->encrypted_id";
        $clickableTicketNumber = "<a target='_blank' href=$ticketLink>$ticket->ticket_number</a>";

        $msg    = Lang::get('lang.Ticket-has-been-created-successfully-your-ticket-number-is') . ' ' . $clickableTicketNumber . '. ' . Lang::get('lang.Please-save-this-for-future-reference');
        
        // making only encrypted_id available. Removing other fields
        $ticket->appends = ["encrypted_id"];
        
        return successResponse($msg, $ticket);
    }
}
