<?php

namespace App\Http\Controllers\Agent\helpdesk;

// controllers
use App\Events\Ticket\TicketUpdating;
use App\Facades\Attach;
use App\Helper\BatchTicketImport;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketsCategoryController;
use App\Events\Ticket\TicketThreadCreating;
use App\Http\Controllers\Common\NotificationController as Notify;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Common\FileuploadController;
use App\Http\Controllers\Common\TicketsWrite\SlaEnforcer;
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\Ticket\AgentPanelTicketRequest;
// models
use App\Listeners\Ticket\ReplyListener;
use App\Model\Common\TicketActivityLog;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Settings\Email;
use App\Model\helpdesk\Settings\FileSystemSettings;
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
use App\Model\helpdesk\Settings\CommonSettings;
use App\Http\Requests\helpdesk\AgentReplyRequest;
use App\Model\MailJob\QueueService;
use App\Repositories\TicketActivityLogRepository;
use App\User;
use Auth;
use DB;
use Exception;
use App\Model\helpdesk\Manage\Tickettype;
// classes
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Input;
use Lang;
use Mail;
use PDF;
use Carbon\Carbon;
use App\Bill\Models\Bill;
use App\Jobs\BatchTicketCreate;

use App\Http\Controllers\Utility\FormController;
use App\Http\Controllers\Common\AlertAndNotification;
use App\Model\helpdesk\Manage\HeltopicAssignType;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Http\Controllers\Agent\helpdesk\UserController;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Location\Models\Location;
use App\Model\helpdesk\Ticket\TicketStatusType;
use App\Http\Controllers\Auth\AuthController;
use App\Traits\UserVerificationHelper;



/**
 * TicketController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class TicketController extends Controller {
    use UserVerificationHelper;

    /**
     * Create a new controller instance.
     *
     * @return type response
     */
    public function __construct() {
        $PhpMailController = new PhpMailController();
        $NotificationController = new Notify();
        $this->PhpMailController = $PhpMailController;
        $this->NotificationController = $NotificationController;
        $this->middleware('auth', ['except' => ['rating','helptopicType', 'updateTicketModelForStatusChange', 'haltDue']]);

        // NOTE FROM AVINASH:  more methods can be added to it but we are not sure which method is used by "only agents"
        $this->middleware('role.agent')->only(['ticket_print']);
    }

    /**
     * @category function to return ticket view page
     * @param null
     * @return repsone/view
     */
    public function getTicketsView()
    {
        return view('themes.default1.agent.helpdesk.ticket.tickets');
    }

    /**
     * Show the New ticket page.
     *
     * @return type response
     */
    public function newticket(CountryCode $code) {
        if (!User::has('create_ticket')) {
            return redirect('dashboard')->with('fails', Lang::get('lang.permission-denied'));
        }
        return view('themes.default1.agent.helpdesk.ticket.new');
    }

    /**
     * Save the data of new batch ticket and show the New ticket page with result.
     *
     * @param type CreateTicketRequest $request
     *
     * @return type response
     */

    public function post_newbatchticket(AgentPanelTicketRequest $request){
        set_time_limit(-1);
        try{
            $data = (array)$request->except(['requester']);
            $file = $request->file('requester');

            $attachmentKeys = array_keys($request->file());
            // remove requester out of it

            foreach ($attachmentKeys as $key) {

              if($key != 'requester'){
                  $disk = FileSystemSettings::value('disk');
                //store the attachment temporarily and assign the path as value
                foreach ($data[$key] as $index => $attachment) {
                    $storageAdapter = Storage::disk($disk);

                    $filename =  Attach::put("ticket_attachments/" . now()->year . '/' . now()->month . '/' . now()->day, $attachment, $disk, null, false);

                    $fullPath = Attach::getFullPath($filename, $disk);

                    $data['attachments'][] = [
                        'filename' => $filename,
                        'path' =>  strstr($fullPath, $filename, true) ?: $fullPath,
                        'size' => $storageAdapter->size($filename),
                        'type' => pathinfo($filename, PATHINFO_EXTENSION),
                        'disk' => $disk,
                        'name' => basename($filename),
                        'contentId' => null
                    ];

                    unset($data[$key][$index]); //deleting because UploadedFile cannot be serialized.
                }
              }
            }

            $filename = Attach::put('batch', $file[0], FileSystemSettings::value('disk'), null, false, 'public');

            BatchTicketCreate::dispatch($data, $filename);

            return response()->json(['message' => Lang::get('lang.batch-ticket-created-success')]);
        }

        catch(\Exception $e){
            return response()->json(['fail' => $e->getMessage()]);
        }
    }

    /**
     * Save the data of new ticket and show the New ticket page with result.
     *
     * @param type CreateTicketRequest $request
     *
     * @return type response
     */

    public function post_newticket(AgentPanelTicketRequest $request) {
        try {
            // if its a batch ticket, it we can redirect request to batch ticket method
            if($request->file('requester')){
                return $this->post_newbatchticket($request);
            }

            $email = null;
            $username = null;
            $mobile_number = null;
            $phonecode = null;
            $attach_name = [];
            if ($request->file()) {
                $attach_name = array_keys($request->file());
            }

            $default_values = ['Requester', 'Requester_email', 'Requester_name', 'media_option',
                'Requester_mobile', 'Help_Topic', 'cc', 'Help Topic',
                'Requester_mobile', 'Requester_code', 'Help Topic', 'Assigned', 'Subject',
                'subject', 'priority', 'help_topic', 'body', 'Description', 'Priority',
                'Type', 'Status', 'attachment', 'inline', 'email', 'first_name', 'company', 'org_dept',
                'last_name', 'mobile', 'country_code', 'api', 'sla', 'dept', 'code', 'source',

                'user_id', 'media_attachment', 'requester', 'status', 'assigned', 'description', 'type', 'media_option', 'Department', 'department','linkDept','captcha','domain_id','location'];

            $default_values = array_merge($default_values, $attach_name);
            $form_data      = $request->except($default_values);
            \Config::set('app.custom-fields', $form_data);

            $requester = $request->input('requester');

            //if username is set, check for username
            // if found, all email and phone numbers will be of that user
            // check for id instead
            $user = User::whereId($requester)->first();

            $email = $user->email;
            $username = $user->user_name;
            $mobile_number = $user->mobile;

            if ($request->filled('api')) {
                $api = $request->input('api');
            }

            if (isset($requester['phone_code'])) {
                $phonecode = $requester['phone_code'];
            }
            elseif ($user != null) {
                $phonecode = $user->country_code;
            } else {
                $phonecode = 0;
            }

            if ($request->input('assigned_id')) {
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
                $body = $request->input('description');
            } elseif ($request->filled('body')) {
                $body = $request->input('body');
            } else {
                $body = null;
            }

            if ($request->filled('priority_id')) {
                $priority = $request->input('priority_id');
            }
            else {
                $priority = null;
            }

            if ($request->input('type_id')) {
                $type = $request->input('type_id');
            } else {

                $type = null;
            }

            if ($request->input('status_id')) {
                $status = $request->input('status_id');
            } else {
                $status = null;
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

            $phone = "";
            if ($request->filled('phone')) {
                $phone = $request->input('phone');
            }

            if ($request->filled('source_id')) {
                $source_id = $request->input('source_id');
            } else {
                $source = Ticket_source::where('name', '=', 'agent')->first();
                $source_id = $source->id;
            }

            $headers = null;
            if ($request->filled('cc')) {
                // NOTE: The new code sends array of ids instead of array if emails for cc.
                // so, for a workaround,  we add `cc` as as array of ids, intead of array of emails
                $arrayOfIds = $request->input('cc');
                $headers = User::whereIn('id',$arrayOfIds)->pluck('email')->toArray();
            }

            $company = "";
            if ($request->filled('company')) {
                $company = $request->input('company');
            }

            $auto_response = 0;
            $sla = "";
            $attach = [];
            $media_attach = [];

            if ($request->filled('media_attachment')) {
                $media_attach = $request->input('media_attachment');
            }

            // NOTE: added for handling attachment in Navins code
            if ($request->filled('attachments')) {
                $media_attach = $request->input('attachments');
            }

            if ($request->file()) {
                $attach = $request->file();
            }

            $attachment = array_merge($attach, $media_attach);

            $inline = $request->input('inline');

            $domainId=($request->filled('domain_id'))?$request->input('domain_id'):0;

            $locationId = $request->location_id ? $request->location_id : ($user->location ?: null);

            $result = $this->create_user($email, $username, $subject, $body, $phone, $phonecode, $mobile_number, $helptopic, $sla, $priority, $source_id, $headers, $department, $assignto, $form_data, $auto_response, $status, $type, $attachment, $inline, [], $company,$domainId, true,$locationId);

            $ticket = Tickets::where('ticket_number', '=', $result[0])->select('id','ticket_number')->first();
            $ticketLink = \Config::get('app.url')."/thread/$ticket->id";
            $clickableTicketNumber = "<a target='_blank' href=$ticketLink>$ticket->ticket_number</a>";

            return successResponse(Lang::get('lang.Ticket-created-successfully', ['ticketNumber'=>$clickableTicketNumber]), $ticket->toArray());
        } catch (Exception $e) {

            return errorResponse($e->getMessage());
        }
    }

    /**
     * Shows the ticket thread details.
     *
     * @param int $ticketId
     */
    public function thread($ticketId)
    {
        // if not accessible, it should be redirected to 404
        if(!(new TicketsCategoryController())->accessibleTickets("all")->whereId($ticketId)->count()){
            return redirect("404");
        }
        /**
         * Till mail template sends encrypted ticket links need to ensure users are not
         * agent panel timeline so redirecting them to client panel
         */
        if (Auth::user()->role == 'user') {
            return redirect(faveourl('check-ticket/').\Crypt::encryptString($ticketId));
        }

        return view('themes.default1.agent.helpdesk.ticket.timeline', compact('ticketId'));
    }

    /**
     * @deprecated since v3.4.0
     * @internal mobile apps as still using this API call which is handled by this method. We will
     * remove it after updating clients and mobile apps.
     */
    public function reply(AgentReplyRequest $request, $ticketid = "", $mail = true, $system_reply = true, $user_id = '', $api = true)
    {
        if (\Input::get('billable')) {
            $this->validate($request, [
                'hours' => ['required', 'regex:/^[0-9]*:[0-5][0-9]$/'],
                'amount_hourly' => ['required_if:billable,1']
            ]);
        }
        $this->validate($request, [
            'content' => 'required',
                ], [
            'content.required' => 'Reply Content Required',
        ]);

        try {

            if (!$ticketid) {
                $ticketid = $request->input('ticket_id');
            }
            $body = $request->input('content');
            $email = $request->input('email');
            $inline = $request->input('inline');
            $attachment = $request->input('attachment')?:$request->file('attachment');//
            $source = source($ticketid);
            $internalNote = $request->input('is_internal_note') == 1;
            $poster = ($internalNote) ? Auth::user()->role : 'support';
            $form_data = $request->except('content', 'ticket_id', 'attachment', 'inline');
            //\Event::dispatch(new \App\Events\ClientTicketFormPost($form_data, $email, $source));
            if (!$request->filled('do-not-send')) {
                \Event::dispatch('Reply-Ticket', [['ticket_id' => $ticketid, 'body' => $body,'attachment' => $attachment]]);
            }
            if ($system_reply == true && Auth::user()) {
                $user_id = Auth::user()->id;
            } else {
                $user_id = requester($ticketid);
                if ($user_id !== "") {
                    $user_id = $user_id;
                }
            }

            $replyToRecipent = $request->to ? $request->to : [];
            $collaborators = $request->cc ? $request->cc : [];

            // if cc is there in the request, we have to delete the collaborators which are not there in the request
            $syncCollaborators = $request->has('cc');

            $formattedCollaborators = [];
            array_map(function($collaborator) use (&$formattedCollaborators){
              $formattedCollaborators[$collaborator] = $collaborator;
            }, $collaborators);

            $thread = $this->saveReply($ticketid, $body, $user_id, $system_reply, $attachment, $inline, $mail, $poster, [], $formattedCollaborators, $replyToRecipent, $syncCollaborators, false, $internalNote);
            if (!$api) {
                return $thread;
            }

            if (\Input::get('billable')) {

                $bill = new Bill();
                $bill->level = 'thread';
                $bill->model_id = $request->input('ticket_id');
                $bill->agent = Auth::user()->id;
                $bill->ticket_id = $request->input('ticket_id');
                $bill->hours = \Input::get('hours');
                $bill->billable = \Input::get('billable');
                $bill->amount_hourly = \Input::get('amount_hourly');
                $bill->note = $body;
                $bill->save();
            }
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }

        return successResponse(Lang::get("lang.successfully_posted"));
    }

    public function saveReply($ticket_id, $body, $user_id, $system_reply, $attachment = [], $inline = [], $mail = true, $poster = 'support', $email_content = [], $headers = '', $replyToRecipent = [], $syncCollaborators = false, $replyFromClientPortal=false, $internalNote = false) {
//        dd($body, $inline);

        $user = User::where('id', $user_id)->select('id', 'role')->first();

        $ticket = Tickets::find($ticket_id);

        $response_time = null;
        if ($poster == 'support') {
            $response_time = (new SlaEnforcer($ticket))->getResponseTime();
        }
        $threadData = [
            'ticket_id' => $ticket_id,
            'user_id' => $user_id,
            'poster' => $poster,
            'body' => $body,
            'response_time' => $response_time
        ];
        if($internalNote) {
            $threadData['thread_type'] = 'note';
            $threadData['is_internal'] = 1;
        }
        $thread = $ticket->thread()->create($threadData);
        $ticket = $this->saveReplyTicket($ticket_id, $system_reply, $user, $internalNote);
        $this->saveEmailThread($thread, $email_content);
        $this->saveReplyAttachment($thread, $attachment, $inline);

        if (!$internalNote) {
            // adding collaborator before notification is sent
            // if $syncCollaborators is passed as true, it will remove all the collaborators
            // that is not there in the request
            $collabs = $this->storeCollaborators($headers, $ticket_id, $syncCollaborators);
            $replyToRecipent = $this->updateRecipent($ticket, $replyToRecipent, $collabs, $replyFromClientPortal);
        }
        $this->replyNotification($ticket, $thread, $mail, $replyToRecipent, $internalNote);

        return $thread;
    }

    /**
     * Method updates $replyToRecipent to appends when reply is made from client panel
     * - CC users if reply is made from client panel and CC users are passed in reply request
     * - Tickets owner if the reply is not from tickets owner(in case of reply by cc)
     *
     * @param   Tickets $ticket                 Ticket instance on which reply has been made
     * @param   array   $replyToRecipent        Array containing recipent of reply
     * @param   array   $collabs                Array containing Collaborator ids
     * @param   bool    $replyFromClientPortal  If the reply is from client panel or not
     *
     * @return  array                           Updated array data for recipent
     */
    protected function updateRecipent(Tickets $ticket, array $replyToRecipent, array $collabs, bool $replyFromClientPortal)
    {
        if(!$replyFromClientPortal) return $replyToRecipent;

        if((Auth::user()->id !== $ticket->user_id)) {
            array_push($replyToRecipent, $ticket->user_id);
        }

        return array_merge($replyToRecipent, $collabs);
    }

    public function saveReplyAttachment($thread, $attachments, $inlines) {
        $drive = FileSystemSettings::value('disk');
        $thread_id = $thread->id;
        $attach = $thread->attach();
        $this->callToCreateAttachment($attach, $attachments, $thread, $drive, 'ATTACHMENT');
        $this->callToCreateAttachment($attach, $inlines, $thread, $drive, 'INLINE');

        return $thread;
    }

    /* calls to createAttachments depending upon the given document is sent as an array or an object */
    protected function callToCreateAttachment($attach, $files, $thread, $drive, $poster){
        if ($files && count($files) > 0) {
            foreach ($files as $key=>$file) {
                if (is_array($file) && !checkArray('filename', $file)) {
                    foreach ($file as $fileParameter) {

                        $this->createAttachments($attach, $key, $fileParameter, $thread, $drive, $poster);
                    }
                } else {
                    $this->createAttachments($attach, $key, $file, $thread, $drive, $poster);
                }
            }
        }
    }


    /* attachment/inline files storage call */
    public function createAttachments($attach, $key, $attachment, $thread, $drive, $poster = 'ATTACHMENT') {
        $thread_id = $thread->id;
        if (is_object($attachment)) {

            $storage = new \App\FaveoStorage\Controllers\StorageController();

            $storage->saveObjectAttachments($thread->id, $attachment, false, $poster, $drive);

        }


        if (is_array($attachment)) {
            $driver = (!empty($attachment['disk'])) ? $attachment['disk'] : $drive;

            $attachmentPath = Attach::getFullPath($attachment['filename'], $driver);

            if ($driver === 's3') {
                $fileContents = \Storage::disk($driver)->get($attachment['filename']);
                $attachmentPath = sys_get_temp_dir() . '/' . basename($attachment['filename']);
                file_put_contents($attachmentPath, $fileContents);
            }

            $datesFolder = now()->year . '/' . now()->month . '/' . now()->day;

            $pathForNewAttachment = Attach::put(
                "ticket_attachments/{$datesFolder}",
                new UploadedFile($attachmentPath, basename($attachmentPath), null, 0, false),
                $driver,
                true,
                false
            );

            $attach->create([
                'thread_id' => $thread_id,
                'name' => $pathForNewAttachment,
                'size' => $attachment['size'],
                'type' => $attachment['type'],
                'poster' => $poster,
                'path' => strstr(Attach::getFullPath($pathForNewAttachment, $driver), $pathForNewAttachment, true),
                'driver' => $driver,
                'content_id' => (!empty($attachment['contentId'])) ? $attachment['contentId'] : null
            ]);
        }
    }

    public function saveReplyTicket($ticket_id, $system_reply, $user = "", $internalNote=false) {
        $tickets = new Tickets();
        $ticket = $tickets->find($ticket_id);
        if (!$ticket) {
            throw new Exception('Invalid ticket number');
        }

        if($internalNote) return $ticket;

        $ticket->system = true;

        //if user is replying isanswered will be 0, if agent/admin replies isanswered will be 1
        $ticket->isanswered = ($user && $user->role != 'user') ? 1 : 0;

        if ($ticket->sla && $ticket->statuses->halt_sla != '1') {
            if ($user && $user->role !== 'user' && $this->isFirstResponse($ticket_id)) {
                $ticket->notify = false;
            }
        } else {
            $ticket->notify = false;
        }

        $ticket->save();
        $agentDepart = DepartmentAssignAgents::where('department_id',$ticket->dept_id)->where('agent_id',$user->id)->count();
        //get agent id as a array based on permission
        $agentId = getAgentbasedonPermission('global_access');
        $assignAgentId = ($agentId && (in_array($user->id, $agentId))) || ($user->role == 'admin') ? $user->id : ($agentDepart ? $user->id : NUll);

        if ($ticket->assigned_to == 0) {
            if ($user && $user->role !== 'user') {
                $ticket->notify = true;
                $ticket->assigned_to = $assignAgentId;
                $ticket->save();
            }
            $data = [
                'id' => $ticket_id,
            ];
            \Event::dispatch('ticket-assignment', [$data]);
        }
        if(($user && ($user->id == $ticket->user_id || $user->role == 'user')) && ($ticket->statuses->type->name == 'closed' || $ticket->statuses->type->name == 'open')) {
            $this->open($ticket_id, $tickets, true);
        }
        return $ticket;
    }

    public function replyNotification($ticket, $thread, $mail, $replyToRecipent = [], $internalNote=false) {
        $request = new Request();
        $reply_content = $request->input('content');
        $ticketid = $ticket->id;
        $ticket_subject = title($ticketid);
        $client_name = '';
        $client_email = '';
        $client_contact = '';
        $agent_email = '';
        $agent_name = '';
        $agent_contact = '';
        $requester = $ticket->user;
        $email = $requester->email;
        $assign_agent = $ticket->assigned;
        if ($requester) {
            $client_name = ($requester->first_name != '' || $requester->last_name != null) ? $requester->first_name . ' ' . $requester->last_name : $requester->user_name;
            $client_email = $requester->email;
            $client_contact = $requester->mobile;
        }
        if ($assign_agent) {
            $agent_email = $assign_agent->email;
            $agent_name = ($assign_agent->first_name != '' || $assign_agent->last_name != null) ? $assign_agent->first_name . ' ' . $assign_agent->last_name : $assign_agent->user_name;
            $agent_contact = $assign_agent->mobile;
        }
        $ticket_number = $ticket->ticket_number;
        $ticket_link = url('thread', $ticket->id);
        $username = $requester->first_name;
        if (!empty(Auth::user()->agent_sign)) {
            $agentsign = Auth::user()->agent_sign;
        } else {
            $agentsign = ($thread->user->role != 'user') ? $thread->user->agent_sign : null;
        }

        // Event
        \Event::dispatch(new \App\Events\FaveoAfterReply($reply_content, $requester->mobile, $requester->country_code, $request, $ticket, $thread));

        if (Auth::user()) {
            $u_id = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        } else {
            $u_id = $this->getAdmin()->first_name . ' ' . $this->getAdmin()->last_name;
        }
        $data = [
            "ticket_id" => $ticketid,
            'u_id' => $u_id,
            'body' => $reply_content,
        ];

        $activity_by = ($thread->user->first_name != '' || $thread->user->first_name != null) ? $thread->user->first_name . ' ' . $thread->user->last_name : $thread->user->user_name;

        $line = ""; //"---Reply above this line---<br><br>";
        $collaborators = Ticket_Collaborator::where('ticket_id', '=', $ticketid)->get();
        if (!$email) {
            $mail = false;
        }
        if ($thread->user->role == 'user') {
            $key = 'reply_notification_alert';
            $scenario = 'ticket-reply-agent';
        } else {
            $key = 'reply_alert';
            $scenario = 'ticket-reply';
            if($internalNote) {
                $key = 'internal_activity_alert';
                $scenario = 'internal_change';
            }
        }
        $message = str_replace("Â", "", utfEncoding($line . $thread->purify(false)));
        $ticket_due_date = "";
        $ticket_created_date = "";
        if ($ticket->duedate) {
            $ticket_due_date = $ticket->duedate->tz(timezone());
        }
        if ($ticket->created_at) {
            $ticket_created_date = $ticket->created_at->tz(timezone());
        }
        $notifications[] = [
            $key => [
                'to' => $replyToRecipent,
                'from' => $this->PhpMailController->mailfrom('1', $ticket->dept_id),
                'message' => ['subject' => $ticket_subject . '[#' . $ticket_number . ']',
                    'body' => $message,
                    'scenario' => $scenario,
                    'attachments' => $thread->attach()->get(),
                ],
                'variable' => [
                    'ticket_subject' => title($ticket->id),
                    'ticket_number' => $ticket_number,
                    'ticket_link' => $ticket_link,
                    'ticket_due_date' => $ticket_due_date,
                    'ticket_created_at' => $ticket_created_date,
                    'client_name' => $client_name,
                    'client_email' => $client_email,
                    'client_contact' => $client_contact,
                    'agent_email' => $agent_email,
                    'agent_name' => $agent_name,
                    'agent_contact' => $agent_contact,
                    'agent_sign' => $agentsign,
                    'activity_by' => $activity_by,
                    'department_signature' => $this->getDepartmentSign($ticket->dept_id)
                ],
                'ticketid' => $ticket->id,
                'send_mail' => $mail,
                'model' => $thread,
                'thread' => $thread,
            ],
        ];
        $notification = new Notifications\NotificationController();
        $notification->setDetails($notifications);
    }

    public function storeAttachment($attachment, $thread) {
        $storage = new \App\FaveoStorage\Controllers\StorageController();
        $storage->saveAttachments($thread->id, $attachment);
    }

    public function isFirstResponse($ticketid) {
        $first = false;
        $thread = Ticket_Thread::where('ticket_id', $ticketid)
                ->where('is_internal', '!=', 1)
                ->where('poster', 'support')
                ->where('title', "")
                ->where('thread_type', 'first_reply')
                ->select('id')
                ->first();
        if (!$thread) {
            $first = true;
        }
        return $first;
    }

    public function setResponse($tickets, $due = false) {
        $tickets->first_response_time = \Carbon\Carbon::now();
        $due = ($due) ? $due : (($tickets->duedate) ? $tickets->duedate : $tickets->last_estd_duedate);
        /**
         * setting response_due_by column to duedate of first response as accessor
         * of due date may have set response_due_by as null due to changes in it.
         */
        $tickets->response_due_by = $due;
        $tickets->is_response_sla = $this->isSla($due);
        return $tickets;
    }

    public function isSla($duedate) {
        $sla = 0;
        $now = \Carbon\Carbon::now();
        if ($now < $duedate) {
            $sla = 1;
        }
        return $sla;
    }

    /**
     * Changes department of the asked ticket after validating it
     * NOTE : test-cases are pending
     * @param \Illuminate\Http\Request $request
     * @return Response                             success or error response
     */
    public function changeDepartment(Request $request) {
        if (!User::has('transfer_ticket')) {
            return errorResponse(Lang::get('lang.permission_denied'));
        }

        $newDepartment = Department::where('id', $request->input('dept-id'))->select('id')->first();

        if (!$newDepartment) {
            return errorResponse(Lang::get('lang.this_deparment_not_exists'));
        }
        $ticket = Tickets::findOrFail($request->input('ticket-id'));

        //ticket status of unapproved ticket should only be changed by the agent who is allowed to change it
        if($this->ifUnapproved($ticket)){
            return errorResponse(Lang::get('lang.permission_denied'));
        }

        $ticket->dept_id = $newDepartment->id;
        $ticket->assigned_to = null;
        $ticket->save();

        try{
            event(new \App\Events\WebHookEvent($ticket,"ticket_department_updated"));
        }
        catch(\Exception $e){
            \Log::info("Webhook Exception Caught:  ".$e->getMessage());
        }
        return successResponse(Lang::get('lang.ticket_department_successfully_changed'));
    }

    /**
     * Updates due-date of a ticket
     * NOTE : test-cases are pending
     * @param Request $request
     * @return Response         success|fails
     */
    public function changeDuedate(Request $request) {

        if (!User::has('change_duedate')) {
            return errorResponse(Lang::get('lang.permission_denied'));
        }

        $duedate = $request->input('duedate');

        //check for user's timezone and convert it into UTC
        $duedateInUTC = Carbon::createFromFormat('Y-m-d H:i:s', $duedate, agentTimeZone())->setTimezone('UTC');

        $ticket = Tickets::findOrFail($request->input('ticket-id'));

        if($ticket->statuses->halt_sla){
            return errorResponse(Lang::get("lang.duedate_cannot_be_changed_if_it_is_halted"));
        }

        //ticket status of unapproved ticket should only be changed by the agent who is allowed to change it
        if($this->ifUnapproved($ticket)){
            return errorResponse(Lang::get('lang.permission_denied'));
        }

        $ticket->duedate = $duedateInUTC;
        // duedate is getting changed manually
        $ticket->is_manual_duedate = 1;
        $ticket->save();
        try{
            event(new \App\Events\WebHookEvent($ticket,"ticket_due_date_updated"));
        }
        catch(\Exception $e){
            \Log::info("Webhook Exception Caught:  ".$e->getMessage());
        }
        return successResponse(Lang::get('lang.ticket_duedate_successfully_changed'));
    }

    /**
     * Print Ticket Details.
     *
     * @param type $id
     * _
     * @return type respponse
     */
    public function ticket_print($id, Request $request)
    {
        if (!(new TicketsCategoryController())->allTicketsQuery()->whereId($id)->count()) {
            return errorResponse(Lang::get('lang.not_found'), 404);
        }

        // check if allow_url_fopen is enabled or not,
        // if not return a page saying its not enabled, click here to proceed
        if(!ini_get('allow_url_fopen') && !$request->force_download){
            $downloadWithoutPdfLink = \Config::get("app.url")."/"."/ticket/print/$id?force_download=1";
            echo "<span>".Lang::get("lang.allow_url_fopen_inline_image_warning_message", ["link"=>$downloadWithoutPdfLink])."</span>";
            die;
        }

        $tickets = Tickets::
                leftJoin('ticket_thread', function ($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id')
                    ->whereNotNull('ticket_thread.title');
                })
                ->leftJoin('department', 'tickets.dept_id', '=', 'department.id')
                ->leftJoin('help_topic', 'tickets.help_topic_id', '=', 'help_topic.id')
                ->where('tickets.id', '=', $id)
                ->select('ticket_thread.title', 'tickets.ticket_number', 'department.name as department', 'help_topic.topic as helptopic', 'tickets.created_at', \DB::raw('CONVERT_TZ(tickets.created_at, "+00:00", "'.getGMT().'") as created_at'))
                ->first();
        $ticket = Tickets::where('tickets.id', '=', $id)->first();
        $html = view('themes.default1.agent.helpdesk.ticket.pdf', compact('id', 'ticket', 'tickets'))->render();
        $html1 = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // it returns a file object which can be viewed in browser and can be downloaded afterwards
        return PDF::load($html1)->show(false, false, false);
    }

    /**
     * check email for dublicate entry.
     *
     * @param type $email
     *
     * @return type bool
     */
    public function checkEmail($username, $email = "") {
        $check = User::where('email', '=', $username)->orWhere('user_name', $username)->orWhere('mobile', $username)->first();
        $checkEmailSettings=Emails::where('email_address',$email)->count();

        if (!$check && $email && $checkEmailSettings == 0) {
            $check = User::where('email', '=', $email)->orWhere('user_name', $username)->orWhere('mobile', $email)->first();
        }
        if ($check) {
            return $check;
        }
        return false;
    }

    /**
     * @category fucntion to check if mobile number is unqique or not
     * @param string $mobile
     * @return boolean true(if mobile exists in users table)/false (if mobile does not exist in user table)
     */
    public function checkMobile($mobile) {
        $check = User::where('mobile', $mobile)->first();
        if ($check) {
            return true;
        }
        return false;
    }

    /**
     * Create User while creating ticket.
     *
     * @param type $emailadd
     * @param type $username
     * @param type $subject
     * @param type $phone
     * @param type $helptopic
     * @param type $sla
     * @param type $priority
     * @param type $system
     *
     * @return type bool
     */
    public function create_user($emailadd, $username, $subject, $body, $phone, $phonecode, $mobile_number, $helptopic, $sla, $priority, $source, $headers, $dept, $assignto, $form_data, $auto_response, $status, $type, $attachment = [], $inline = [], $email_content = [], $org = "",$domainId='', $loggedByAgent = false, $locationId = null, $parentTicketId = null) {
        $email;
        $username;
        $unique = $emailadd;

        if (!$unique) {
            $unique = $username;
        }

        if (!$unique) {
            $unique = $mobile_number;
        }

        // check emails
        $ticket_creator = $username;
        $checkemail = $this->checkEmail($unique, $emailadd);

        $company = $this->company();
        \Event::dispatch(new \App\Events\ClientTicketFormPost($form_data, $emailadd, $source, $dept));
        if ($checkemail == false) {
            $newUser =  $this->callUserRegister([
                'email' => $emailadd,
                'phone' => $phone,
                'code' => $phonecode,
                'mobile' => $mobile_number,
                'full_name' => $username,
            ]);
            $this->setEntitiesVerifiedByModel($newUser);
            $user_id = $newUser->id;
        } else {
            $username = $checkemail->first_name;
            $user_id = $checkemail->id;
        }

        $checkUserRole = User::where('id',$user_id)->value('role');

        $ticket_number = $this->check_ticket($user_id, $subject, $body, $helptopic, $sla, $priority, $source, $headers, $dept, $assignto, $form_data, $status, $type, $attachment, $inline, $email_content, $org,$domainId, $locationId, $parentTicketId);
        $ticket_number2 = $ticket_number[0];
        $ticketdata = Tickets::where('ticket_number', '=', $ticket_number2)->first();
        $assigner = "";
        if (Auth::user()) {
            $assigner = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        } elseif ($ticketdata->assigned) {
            $assigner = $ticketdata->assigned->first_name . ' ' . $ticketdata->assigned->last_name;
        }

        // fetching ticket template variables
        $templateVariables = array_merge($ticketdata->ticketTemplateVariables($body), ['activity_by'=> $assigner]);

        if ($ticketdata->assigned_to) {
            $notification[] = [
                'ticket_assign_alert' => [
                    'ticketid' => $ticketdata->id,
                    'from' => $this->PhpMailController->mailfrom('1', $ticketdata->dept_id),
                    'message' => ['subject' => 'Assign ticket ' . '[#' . $ticketdata->ticket_number . ']',
                        'scenario' => 'assign-ticket'],
                    'variable' => $templateVariables,
                    'model' => $ticketdata
                ],
            ];
        }

        if ($ticketdata->team_id) {
            $team_detail = Teams::where('id', '=', $ticketdata->team_id)->first();
            $assignee = $team_detail->name;
            $notification[] = [
                'ticket_assign_alert' => [
                    'ticketid' => $ticketdata->id,
                    'from' => $this->PhpMailController->mailfrom('1', $ticketdata->dept_id),
                    'message' => ['subject' => 'Assign ticket ' . '[#' . $ticketdata->ticket_number . ']',
                        'scenario' => 'team_assign_ticket'],
                    'variable' => $templateVariables,
                    'model' => $ticketdata
                ],
            ];
        }

        $threaddata = Ticket_Thread::where('title', '!=', '')
                        ->where('ticket_id', '=', $ticketdata->id)->first();
        $is_reply = $ticket_number[1];
        //dd($source);
        $system = $this->system();
        $updated_subject = title($ticketdata->id) . '[#' . $ticket_number2 . ']';
        //$body = $threaddata->purify();
        //dd($body);
        if ($ticket_number2) {
            // send ticket create details to user
            if ($is_reply == 0) {
                $mail = 'create-ticket-agent';
                $message = $threaddata->purify(false);
                if (Auth::user()) {
                    $sign = Auth::user()->agent_sign;
                } else {
                    $sign = $company;
                }
            } elseif ($is_reply == 1) {
                $this_thread = Ticket_Thread::where('ticket_id', '=', $ticketdata->id)->where('is_internal', 0)->orderBy('id', 'DESC')->first();
                $mail = 'ticket-reply-agent';
                $message = $body;
            }
            $message2 = str_replace("Â", "", utfEncoding($message));
            if ($is_reply != 1) {
                $acknowledgeScenario = ($loggedByAgent) ? 'create-ticket-by-agent' : 'create-ticket';
                $notification[] = ['new_ticket_alert' => [
                        'from' => $this->PhpMailController->mailfrom('0', $ticketdata->dept_id),
                        'message' => [
                            'subject' => $updated_subject,
                            'body' => $message,
                            'scenario' => $mail,
                        ],
                        'variable' => $templateVariables,
                        'ticketid' => $ticketdata->id,
                        'model' => $ticketdata,
                        'userid' => $ticketdata->user_id,
                        'thread' => $threaddata,
                    ],
                    'new_ticket_confirmation_alert' => [
                        'from' => $this->PhpMailController->mailfrom('0', $ticketdata->dept_id),
                        'message' => [
                            'subject' => $updated_subject,
                            'body' => $message,
                            'scenario' => $acknowledgeScenario,
                            'cc' => [], //not sending ticket confirmation alerts to collaborators
                        ],
                        'variable' => $templateVariables,
                        'ticketid' => $ticketdata->id,
                        'model' => $ticketdata,
                        'userid' => $ticketdata->user_id,
                        // 'thread' => $threaddata, it results in attachment sent out to clients in annmgt
                    ],
                ];


                $data = array(
                    'ticket_number' => $ticket_number2,
                    'user_id' => $user_id,
                    'subject' => $subject,
                    'body' => $body,
                    'status' => $status,
                    'Priority' => $priority,
                );
                \Event::dispatch('Create-Ticket', array($data));
                $alert = new Notifications\NotificationController();
                $alert->setDetails($notification);
            }
            $data = [
                'id' => $ticketdata->id,
            ];
            \Event::dispatch('ticket-assignment', [$data]);
            return ['0' => $ticket_number2, '1' => true];
        }
    }

    public function updateThread($thread, $attachments, $inline = []) {
        if (file_exists(app_path('/FaveoStorage/Controllers/StorageController.php'))) {

            try {
                $storage = new \App\FaveoStorage\Controllers\StorageController();
                return $storage->saveAttachments($thread->id, $attachments, $inline);
            } catch (\Exception $ex) {
                loging('attachment', $ex->getMessage());
            }
        } else {
            loging('attachment', 'FaveoStorage not installed');
        }
    }

    /**
     * Default helptopic.
     *
     * @return type string
     */
    public function default_helptopic() {
        $helptopic = '1';

        return $helptopic;
    }

    /**
     * Default SLA plan.
     *
     * @return type string
     */
    public function default_sla() {

         $default_sla = Sla_plan::where('is_default', '>', '0')->first();
         $sla = $default_sla->id;

        return $sla;
    }

    /**
     * Default Priority.
     *
     * @return type string
     */
    public function default_priority() {
        $prioirty = Ticket_Priority::select('priority_id')->where('is_default', '=', 1)->first();
        $prioirty = $prioirty->priority_id;
        return $prioirty;
    }


    public function checkTicketForEmailReply($subject, $emailContent, $userId = "")
    {
        $ticket = null;
        $readSubject = explode('[#', $subject);
        if (!empty($emailContent)) {
            $emailThread = "";
            $referenceId = checkArray('reference_id', $emailContent);
            if ($referenceId && is_array($referenceId)) {
                $emailThread = \App\Model\helpdesk\Ticket\EmailThread::whereIn('message_id', $referenceId)->select('id', 'ticket_id')->orderBy('id', 'desc')->first();
            }
            if ($emailThread) {
                $ticket = Tickets::find($emailThread->ticket_id);
            }
        }
        if (!$ticket  && isset($readSubject[1])) {
            $separate = explode(']', $readSubject[1]);
            $number = substr($separate[0], 0, 20);
            if ($userId != '') {
                if (User::where('id', '=', $userId)->select('role')->first()->toArray()['role'] != 'user') {
                    $userId = "";
                }
            }
            $ticket = $this->getTicketForEmailCheck(Tickets::where('ticket_number', '=', $number), $userId);
        }
        // recursive way for checking parent ticket id when child ticket is merged with parent ticket
        while ($ticket) {
            $parentTicket = $ticket->parentTicket()->first();
            if (is_null($parentTicket)) {
                break;
            }
            $ticket = $parentTicket;
        }


        return $ticket;
    }

    /**
     * Check the response of the ticket.
     *
     * @param type $user_id
     * @param type $subject
     * @param type $body
     * @param type $helptopic
     * @param type $sla
     * @param type $priority
     *
     * @return type string
     */
    public function check_ticket($user_id, $subject, $body, $helptopic, $sla, $priority, $source, $headers, $dept, $assignto, $form_data, $status, $type, $attachment, $inline = [], $email_content = [], $company = "",$domainId="",$locationId, $parentTicketId = null) {

        $ticket = $this->checkTicketForEmailReply($subject, $email_content, $user_id);
        $thread_body = explode('---Reply above this line---', $body);

        $ticketId =  $ticket ? $ticket->id : null;

        $createNewTicketResponse = true;
        if($ticketId){
            $createNewTicketResponse = $this->checkReplyTime($ticketId);
        }
        $body = $thread_body[0];
        if (!$createNewTicketResponse && $ticket) {

            $user = User::find($user_id);

            $poster = ($user->role == 'user') ? 'client': 'support';
            $thread = $this->saveReply($ticket->id, $body, $user_id, false, $attachment, $inline, true, $poster, $email_content, $headers);

            if ($thread) {
                \Event::dispatch('ticket.details', ['ticket' => $thread]);
                return [$ticket->ticket_number, 1];
            }

        } else {
            /**
             * NOTE: This event is triggered just before ticket creation and  currenlty being used
             * to validate custom details like selected package or purchase code for billing or envato.
             * Currenlty we are just passing the required data used by billing.
             * At this point account for new user will be registered but ticket creation will be processed
             * after event listeners
             *
             * WARNING: Do not try usr or update this event as it must and it will be removed in future.
             */
            \Event::dispatch('before_ticket_creation_event', [[$form_data, $user_id, $source, $dept], &$form_data]);
            $ticket_number = $this->createTicket($user_id, $subject, $body, $helptopic, $sla, $priority, $source, $headers, $dept, $assignto, $form_data, $status, $type, $attachment, $inline, $email_content, $company,$domainId,'',$locationId, $parentTicketId);

           return [$ticket_number, 0];
        }
    }

    public function createTicket($user_id, $subject, $body, $helptopic, $sla, $priority, $source, $headers, $dept, $assignto, $form_data, $status, $type, $attachment, $inline = [], $email_content = [], $company = "",$domainId="", $fork = false,$locationId = null, $parentTicketId = null) {



        $user_status = User::select('active')->where('id', '=', $user_id)->first();
        $ticket = new Tickets();

        $ticket->creator_id = Auth::user() ? Auth::user()->id : $user_id;
        $ticket->user_id = $user_id;
        $ticket->help_topic_id = $helptopic;
        $ticket->dept_id = $dept;
        $ticket->sla = $sla;
        $ticket_assign = $assignto;
        $ticket->location_id = $locationId;
        $satelliteModule = CommonSettings::where('option_name', 'satellite_helpdesk')->select('status')->first();
        if($satelliteModule && $satelliteModule->status == 1)
          {$ticket->domain_id = $domainId;}

        ($parentTicketId) ? $ticket->parent_ticket_id = $parentTicketId : null;

        $assigned_to = NULL;
        $team_id = NULL;
        if (!$ticket_assign || $ticket_assign == " ") {
            $assigned_to = null;
            $team_id = null;
        } elseif (is_numeric($ticket_assign)) {
            $assigned_to = $ticket_assign;
        } else {
            $assignto = explode('_', $ticket_assign);
            if ($assignto[0] == 'team') {
                $team_id = $assignto[1];
                $assigned_to = null;
            } elseif ($assignto[0] == 'user') {
                $assigned_to = $assignto[1];
                $team_id = null;
            }
            //dd($ticket_assign);
        }

        if (!$assigned_to) {
            $assigned_to = \Event::dispatch('ticket.assign', [['department' => $dept, 'type' => $type,'location' =>$locationId, 'extra' => $form_data]])[0];
        }
        $ticket->team_id = $team_id;
        // dd($ticket->team_id);
        $ticket->assigned_to = $assigned_to;
        $ticket->priority_id = $priority;
        $ticket->type = $type;
        $ticket->source = $source;
        $ticket->status = $this->getStatus($user_id, $status);
        if ($ticket->status == null) {
            $ticket->status = \App\Helper\Finder::getTicketDefaultStatus();
        }
        if (\Input::has('duedate')) {
            $ticket->duedate = getCarbon(\Input::get('duedate'), '/', 'm-d-Y');
        }

        if (!$subject) {
            $helptopic_topic = Help_topic::where('id',$ticket->help_topic_id)->first();
            $subject = $helptopic_topic->topic;
        }

        $subject = strip_tags($subject);

        // autoassign saves assignment data in $ticket->assigned_to, so it must be sent to workflow/listenr
        $ticket_assign = $ticket_assign ?? $ticket->assigned_to;

        $fields = $this->ticketFieldsInArray($user_id, $subject, $body, $helptopic, $sla, $priority, $source, $headers, $dept, $ticket_assign, $form_data, $ticket->status, $type, $attachment, $inline, $email_content, $company, $fork);

        $ticket = event(new \App\Events\WorkFlowEvent(['values' => $fields, 'ticket' => $ticket]))[0];
        //$ticket->save();
        if ($fork) {

            return $ticket;
        }

        // $create_thread = $this->ticketThread($subject, $body, $ticket->id, $user_id, $attachment, $inline, $email_content);

        //dd($faveotime);
        // assign email send
        if ($team_id != null) {


            $team_detail = Teams::where('id', '=', $ticket->team_id)->first();
            $assignee = $team_detail->name;

            $ticket_number = $ticket->ticket_number;
            $thread = new Ticket_Thread();
            $thread->ticket_id = $ticket->id;
            $thread->user_id = Auth::user()->id;
            $thread->is_internal = 1;
            $thread->body = 'This Ticket has been assigned to ' . $assignee;
            $thread->save();
        }
        if ($ticket->assigned_to) {
            $id = $assigned_to;
            $user_detail = User::where('id', '=', $ticket->assigned_to)->first();
            $assignee = $user_detail->first_name . ' ' . $user_detail->last_name;


            $thread = new Ticket_Thread();
            $thread->ticket_id = $ticket->id;
            $thread->user_id = $user_detail->id;
            $thread->is_internal = 1;
            $thread->body = 'This Ticket has been assigned to ' . $assignee;
            $thread->save();


            $ticket_number = $ticket->ticket_number;
            $data = [
                'id' => $ticket->id,
            ];
            \Event::dispatch('ticket-assignment', [$data]);
        }

        \Event::dispatch('after.ticket.created', array(['ticket' => $ticket, 'form_data' => $form_data]));

        $ticket_number = $ticket->ticket_number;
        $id = $ticket->id;
        //$this->customFormCreate($form_data, $id);
        // store collaborators

        $this->storeCollaborators($headers, $id);

        // checking first thread exist or not
        if ($ticket->firstThread()->first()) {
            return $ticket_number;
        }
    }

    public function getStatus($requester_id, $status = "") {
        $requester = User::where('id', $requester_id)->first();
        $status_type = new \App\Model\helpdesk\Ticket\TicketStatusType();
        if ($requester->isDeleted()) {
            $purpose = 'spam';
            $ticket_status = $status_type->where('name', $purpose)->first();
            $status = $ticket_status->status()->first()->id;
        }

        return $status;
    }

    public function ticketFieldsInArray($user_id, $subject, $body, $helptopic, $sla, $priority, $source, $headers, $dept, $assignto, $form_data, $status, $type, $attachment, $inline, $email_content, $company = "", $fork)
    {
        $default = [
            'user_id'          => $user_id,
            'subject'          => $subject,
            'body'             => $body,
            'help_topic_id'        => $helptopic,
            'sla'              => $sla,
            'priority_id'      => $priority,
            'source'           => $source,
            'cc'               => $headers,
            'dept_id'          => $dept,
            'assigned_to'      => $assignto,

            //'team'           => $team_assign,
            'status'           => $status,
            'type'             => $type,
            'attachment'       => $attachment,
            'inline'           => $inline,
            'email_content'    => $email_content,
            'fork'             => $fork,
        ];
        return array_merge($default, $form_data);
    }

    /**
     * Generate Ticket Thread.
     *
     * @param type $subject
     * @param type $body
     * @param type $id
     * @param type $user_id
     *
     * @return type
     */
    public function ticketThread($subject, $body, $id, $user_id, $attachment, $inline = [], $email_content = []) {
        // dd($subject);
        $thread = new Ticket_Thread();
        $thread->user_id = $user_id;
        $thread->ticket_id = $id;
        $thread->poster = 'client';
        $thread->title = $subject;
        $thread->body = $body;
        if ($thread->save()) {
            $this->saveEmailThread($thread, $email_content);
            if ($attachment || $inline) {
                $this->updateThread($thread, $attachment, $inline);
            }
            \Event::dispatch('ticket.details', ['ticket' => $thread]); //get the ticket details
            return true;
        }
    }

    public function saveEmailThread($thread, $content) {
        $ticket_id = $thread->ticket_id;
        if (is_array($content) && count($content) > 0) {
            $refer_id = checkArray('reference_id', $content);
            if ($refer_id && is_array($refer_id)) {
                $refer_id = implode(',', $refer_id);
            }
            $thread->emailThread()->create([
                'ticket_id' => $ticket_id,
                'message_id' => checkArray('message_id', $content),
                'uid' => checkArray('uid', $content),
                'reference_id' => $refer_id ?: "",
                'fetching_email' => checkArray('fetching_email', $content),
            ]);
        }
    }

    /**
     * Generate a random string for password.
     *
     * @param type $length
     *
     * @return type string
     */
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * function to Open Ticket.
     *
     * @param type         $id
     * @param type Tickets $ticket
     * @return type
     */
    public function open($id, Tickets $ticket, $called_from_reply = false) {
        if (Auth::check() && Auth::user()->role == 'user') {
            $ticket_status = $ticket->where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->first();
        } else {
            $ticket_status = $ticket->where('id', '=', $id)->first();
        }
        // checking for unautherised access attempt on other than owner ticket id
        if ($ticket_status == null) {
            return redirect()->route('unauth');
        }
        $set_status_to = ($called_from_reply) ? \App\Helper\Finder::getTicketDefaultStatus() : \App\Helper\Finder::defaultStatus(1);

        if($ticket_status->statuses->type->name == 'closed') {
            $ticket_status->closed = 0;
            $ticket_status->reopened = $ticket_status->reopened+1;
            $ticket_status->reopened_at = date('Y-m-d H:i:s');
        }
        $ticket_status->status = $set_status_to;
        $ticket_status->save();

        return 'your ticket' . $ticket_status->ticket_number . ' has been opened';
    }

    /**
     * function to assign ticket.
     *
     * @param type $id
     *
     * @return type bool
     */
    public function assign(Request $request, $api=false)
    {
        if (Auth::user()->role == "agent") {
            $assignTicketPermissionStatus = User::has('assign_ticket');
            $reassignTicketPermissionStatus = User::has('re_assigning_tickets');

            if ($api && (!$reassignTicketPermissionStatus || !$assignTicketPermissionStatus)) {
                $message = ['error' => Lang::get('lang.permission_denied')];
                return response()->json(compact('message'), 403);
            }

            //when user has none of assign or reassign permission
            if(!$reassignTicketPermissionStatus && !$assignTicketPermissionStatus){
                return errorResponse(Lang::get("lang.you_dont_have_permission_to_assign_tickets"));
            }

            if ($reassignTicketPermissionStatus && !$assignTicketPermissionStatus) {

                $check_id = $request->ticket_id;
                $ids = array_filter(explode(",", $check_id));
                $tickets = Tickets::whereIn('id', $ids)->where(function($query){
                    $query->where('team_id', '!=', null)->orWhere('assigned_to', '!=', null);
                })->pluck('id')->toArray();

                if ($tickets) {
                    $id = implode(",", $tickets);
                } else {
                    return errorResponse(Lang::get('lang.cannot_assign_current_ticket'));
                }

            } else if ($assignTicketPermissionStatus && !$reassignTicketPermissionStatus) {

                $check_id = $request->ticket_id;
                $ids = array_filter(explode(",", $check_id));
                $tickets = Tickets::where('team_id', '=', null)->where('assigned_to', '=', null)->whereIn('id', $ids)->pluck('id')->toArray();


                if ($tickets) {
                    $id = implode(",", $tickets);
                } else {
                    return errorResponse(Lang::get('lang.cannot_assign_current_ticket'));
                }
            } else {

                $id = $request->ticket_id;
            }
        } else {
            $id = $request->ticket_id;
        }

        $master = (Auth::user()->first_name != '' || Auth::user()->first_name != null) ? Auth::user()->first_name . ' ' . Auth::user()->last_name : Auth::user()->user_name;
        // $agentsign = Auth::user()->agent_sign;
        $id = $id;
        $UserEmail = $request->get('assign_to');
        $assign_to = explode('_', $UserEmail);
        $ticket = Tickets::where('id', '=', $id)->first();
        $notifications = [];
        if ($assign_to[0] == 'team') {
            $team_detail = Teams::where('id', '=', $assign_to[1])->first();
            $ticket->team_id = $team_detail->id;
            $ticket->assigned_to = null;
            $assignee = $team_detail->name;
            $ids = array_filter(explode(",", $id));
            foreach ($ids as $id) {
                $ticket = Tickets::where('id', '=', $id)->first();
                $ticket_number = $ticket->ticket_number;
                $ticket->assigned_to = null;
                $ticket->team_id = $team_detail->id;
                $ticket->save();
            }

            $client_detail = User::where('id', '=', $ticket->user_id)->first();
            $client_name = ($client_detail->first_name != '' || $client_detail->first_name != null) ? $client_detail->first_name . ' ' . $client_detail->last_name : $client_detail->user_name;
            $client_email = $client_detail->email;
            $client_contact = $client_detail->mobile;

            $team_lead_name = User::whereId($team_detail->team_lead)->first();

            $ticket = Tickets::where('id', '=', $id)->first();
            $ticket_number = $ticket->ticket_number; //ticket number

            $ticket_thread = Ticket_Thread::where('ticket_id', '=', $id)->first();
            $ticket_subject = title($ticket->id); //ticket subject
            $ticket_link = URL('thread/', $id);

            $notifications[] = [
                'ticket_assign_alert' => [
                    'ticketid' => $id,
                    'from' => $this->PhpMailController->mailfrom('1', $ticket->dept_id),
                    'message' => ['subject' => $ticket_subject . '[#' . $ticket_number . ']',
                        'scenario' => 'team_assign_ticket'],
                    'variable' => array_merge($ticket->ticketTemplateVariables(), ['activity_by' => $master]),
                ],
            ];
        } elseif ($assign_to[0] == 'user') {
            $agent_detail = User::where('id', '=', $assign_to[1])->first();

            if($agent_detail->active == 0)
                return 0;
            $ticket->assigned_to = $agent_detail->id;
            if ($agent_detail) {
                $assignee_name = ($agent_detail->first_name != '' || $agent_detail->first_name != null) ? $agent_detail->first_name . ' ' . $agent_detail->last_name : $agent_detail->user_name;
                $assignee_email = $agent_detail->email;
                $assignee_contact = $agent_detail->mobile;
            }

            $ids = array_filter(explode(",", $id));

            foreach ($ids as $id) {
                $ticket = Tickets::where('id', '=', $id)->first();
                $ticket_number = $ticket->ticket_number;
                $ticket->assigned_to = $agent_detail->id;
                $ticket->team_id = null;
                $ticket->save();
                $client_detail = User::where('id', '=', $ticket->user_id)->first();
                $client_name = ($client_detail->first_name != '' || $client_detail->first_name != null) ? $client_detail->first_name . ' ' . $client_detail->last_name : $client_detail->user_name;
                $client_email = $client_detail->email;
                $client_contact = $client_detail->mobile;
                $data = [
                    'id' => $id,
                ];
                \Event::dispatch('ticket-assignment', [$data]);
                $ticket_thread = Ticket_Thread::where('ticket_id', '=', $id)->first();
                $ticket_subject = title($ticket->id);
                $ticket_link = URL('thread/', $id);
                $due_date = ($ticket && $ticket->duedate) ? $ticket->duedate->tz(timezone()) : null;
                $created = ($ticket && $ticket->created_at) ? $ticket->created_at->tz(timezone()) : null;
                $notifications[] = [
                    'ticket_assign_alert' . $id => [
                        'ticketid' => $id,
                        'from' => $this->PhpMailController->mailfrom('1', $ticket->dept_id),
                        'message' => ['subject' => $ticket_subject . '[#' . $ticket_number . ']',
                            'scenario' => 'assign-ticket'],
                        'variable' => array_merge($ticket->ticketTemplateVariables(), ['activity_by' => $master]),
                    ],
                ];
            }
        }
        $notification = new Notifications\NotificationController();
        $notification->setDetails($notifications);
        if ($api) {
            $message = ['success' => 'Assigned successfully'];
            return response()->json(compact('message'), 200);
        }
        return successResponse(Lang::get('lang.assigned_successfully'));

    }

    /**
     * Function to post internal note.
     *
     * @deprecated since v3.4.0
     * @internal mobile apps as still using this API call which is handled by this method. We will
     * remove it after updating clients and mobile apps.
     * @param type $id
     *
     * @return type bool
     */
    public function InternalNote($id, $InternalContent = '')
    {
        try {
            $InternalContent = Input::has('InternalContent') ? Input::get('InternalContent') : $InternalContent;
            $thread = Ticket_Thread::where('ticket_id', '=', $id)->first();
            $NewThread = new Ticket_Thread();
            $NewThread->ticket_id = $thread->ticket_id;
            $NewThread->user_id = Auth::user()->id;
            $NewThread->is_internal = 1;
            $NewThread->thread_type = 'note';
            $NewThread->poster = Auth::user()->role;
            $NewThread->title = $thread->title;
            $NewThread->body = $InternalContent;
            $NewThread->save();
            $data = [
                "ticket_id" => $id,
                'u_id' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'body' => $InternalContent,
            ];
            \Event::dispatch('Reply-Ticket', array($data));

            return successResponse(Lang::get("lang.successfully_added"));

        } catch(Exception $e){
            return errorResponse($e->getMessage());
        }
    }
    /**
     * Function to surrender a ticket.
     *
     * @param type $id
     *
     * @return type bool
     */
    public function surrender($id)
    {
        $ticket = Tickets::where('id', '=', $id)->first();
        $ticket->assigned_to = null;
        $ticket->save();

        return successResponse(Lang::get("lang.ticket_has_been_surrendered"));
    }

    /**
     * Search.
     *
     * @param type $keyword
     *
     * @return type array
     */
    public function search($keyword) {
        if (isset($keyword)) {
            $data = ['ticket_number' => Tickets::search($keyword)];

            return $data;
        } else {
            return 'no results';
        }
    }

    /**
     * store_collaborators.
     *
     * @param array $headers    key value pair of name => email collaborators
     * @param int $id           ticketId
     * @param bool $syncCollaborators  if collaborators needs to be sync
     *                                 (means deleting collaborators which are not there in the list)
     * @return array            Array containing id of CC users
     */
    public function storeCollaborators($headers, $id, $syncCollaborators = false) {
        $notification = [];
        $userIds = [];
        if (isset($headers) && is_array($headers)) {
            foreach ($headers as $name => $email) {
                $user = $this->checkEmail($email);
                $user_id = "";
                if ($user) {
                    if ((int) $user->is_delete != 1) {
                        $user_id = $user->id;
                    }

                } else {
                    $user_id = $this->callUserRegister([
                        'first_name' => !is_string($name) ? '': $name,
                        'email' => $email,
                        'user_name' => $email
                    ])->id;
                    //associate user to organization base on domain match
                    UserController::domainConnection($user_id);
                }
                if ($user_id) {
                    $userIds[] = $user_id;
                    $alert = new Notifications\NotificationController();
                    $alert->setDetails($notification);

                    Ticket_Collaborator::firstOrCreate([
                        'ticket_id' => $id,
                        'user_id' => $user_id,
                        'isactive' => 1,
                        'role' => 'ccc'
                    ]);
                }
            }
            if($syncCollaborators){
              Ticket_Collaborator::where('ticket_id', $id)->whereNotIn('user_id', $userIds)->delete();
            }


            event(new TicketUpdating(["cc_ids"=> $userIds]));
            TicketActivityLog::saveActivity($id);
        }
        return $userIds;
    }

    private function callUserRegister($requesArray)
    {
        $request = new Request;
        $request->merge(array_filter($requesArray));

        return (new AuthController)->postRegister(new User, $request, 'user');
    }
    /**
     * company.
     *
     * @return type
     */
    public function company($fetch = 'name') {
        $company = Company::Where('id', '=', '1')->first();
        if ($fetch == 'name') {
            if ($company->company_name == null) {
                $company = 'Support Center';
            } else {
                $company = $company->company_name;
            }
        } else {
            $company = $company->website;
        }

        return $company;
    }

    /**
     * system.
     *
     * @return type
     */
    public function system() {
        $system = System::Where('id', '=', '1')->first();
        if ($system->name == null) {
            $system = 'Support Center';
        } else {
            $system = $system->name;
        }

        return $system;
    }

    /**
     * Adds cc to a ticket.
     * NOTE: can be moved to ticket edit part
     * @param  Request $request
     * @return Response         successResponse if success else failure
     */
    public function addCollaborators(Request $request)
    {
        $collaboratorIds = $request->input('collaborator-ids');
        $ticketId = $request->input('ticket-id');

        //only selecting those users who are not blocked
        $filteredCollaboratorIds = User::where('active', 1)
            ->where('is_delete', '!=', 1)->whereIn('id',$collaboratorIds)->pluck('id')->toArray();

        if(!$filteredCollaboratorIds){
            return errorResponse(Lang::get('lang.users_not_found'));
        }

        foreach ($filteredCollaboratorIds as $collaboratorId) {
            Ticket_Collaborator::updateOrCreate([
                'ticket_id'=>$ticketId, 'user_id'=> $collaboratorId, 'role'=>'ccc', 'isactive'=> 1
            ]);
        }

        return successResponse(Lang::get('lang.successfully_updated'));
    }



    /**
     * Creates a user and add it to collaborators
     * NOTE: can be moved to ticket edit part
     * @param  Request $request
     * @return Response         successResponse if success else failure
     */
    public function createNewUserAndMakeCollaborator(Request $request)
    {
        $name = $request->input('name');

        $email = $request->input('email');

        $ticketId = $request->input('ticket-id');

        $doesEmailExists = User::where('email', $email)->first();

         $checkEmailSettings=Emails::where('email_address',$email)->first();

        if($doesEmailExists || $checkEmailSettings ){
            return errorResponse(Lang::get('lang.user_already_exists'));
        }

        //creating user
        $password = $this->generateRandomString();

        $user = User::create(['first_name'=>$name ,'user_name'=>$email,'email'=>$email, 'password'=> \Hash::make($password), 'role'=>'user','active'=>1]);

        $notifications[] = [
                'registration_notification_alert' => [
                    'userid' => $user->id,
                    'from' => $this->PhpMailController->mailfrom('1', '0'),
                    'message' => ['subject' => null, 'scenario' => 'registration-notification'],
                    'variable' => ['new_user_name' => $name, 'new_user_email' => $email,
                        'user_password' => $password]
                ],

                'new_user_alert' => [
                    'model' => $user,
                    'userid' => $user->id,
                    'from' => $this->PhpMailController->mailfrom('1', '0'),
                    'message' => ['subject' => null, 'scenario' => 'new-user'],
                    'variable' => ['new_user_name' => $name, 'new_user_email' => $email,
                        'user_profile_link' => faveoUrl('user/' . $user->id)]
                ],
            ];

        $alert = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
        $alert->setDetails($notifications);

        Ticket_Collaborator::create(['ticket_id'=>$ticketId, 'user_id'=> $user->id, 'role'=>'ccc', 'isactive'=> 1]);

        return successResponse(Lang::get('lang.added_successfully'));
    }

    /**
     * user time zone.
     *
     * @param type $utc
     *
     * @return type date
     */
    public static function usertimezone($utc) {
        $set = System::whereId('1')->first();
        $timezone = Timezones::whereId($set->time_zone)->first();
        $tz = $timezone->name;
        $format = $set->date_time_format;
        date_default_timezone_set($tz);
        $offset = date('Z', strtotime($utc));
        $format = Date_time_format::whereId($format)->first()->format;
        $date = date($format, strtotime($utc) + $offset);

        return $date;
    }

    /**
     * lock.
     *
     * @param type $id
     *
     * @return type null
     */
    public function lock($id) {
        $ticket = Tickets::where('id', '=', $id)->first();
        $ticket->lock_by = Auth::user()->id;
        $ticket->lock_at = date('Y-m-d H:i:s');
        $ticket->save();
    }



    /**
     * Show the deptclose ticket list page.
     *
     * @return type response
     */
    public function deptclose($id) {
        $dept = Department::where('name', '=', $id)->first();
        if (Auth::user()->role == 'agent') {
            if (Auth::user()->primary_dpt == $dept->id) {
                return view('themes.default1.agent.helpdesk.dept-ticket.closed', compact('id'));
            } else {
                return redirect()->back()->with('fails', 'Unauthorised!');
            }
        } else {
            return view('themes.default1.agent.helpdesk.dept-ticket.closed', compact('id'));
        }
    }

    /**
     * Show the deptinprogress ticket list page.
     *
     * @return type response
     */
    public function deptinprogress($id) {
        $dept = Department::where('name', '=', $id)->first();
        if (Auth::user()->role == 'agent') {
            if (Auth::user()->primary_dpt == $dept->id) {
                return view('themes.default1.agent.helpdesk.dept-ticket.inprogress', compact('id'));
            } else {
                return redirect()->back()->with('fails', 'Unauthorised!');
            }
        } else {
            return view('themes.default1.agent.helpdesk.dept-ticket.inprogress', compact('id'));
        }
    }

    /**
     * Store ratings of the user.
     *
     * @return type Redirect
     */
    public function rating($id, Request $request, \App\Model\helpdesk\Ratings\RatingRef $rating_ref)
    {
        foreach ($request->all() as $key => $value) {
            if ($key == '_token') {
                continue;
            }
            if (strpos($key, '_') !== false) {
                $ratName = str_replace('_', ' ', $key);
            } else {
                $ratName = $key;
            }
            $ratID = \App\Model\helpdesk\Ratings\Rating::where('name', '=', $ratName)->first();
            $ratingrefs = $rating_ref->where('rating_id', '=', $ratID->id)->where('ticket_id', '=', $id)->first();
            if ($ratingrefs !== null) {
                if ($ratID->allow_modification) {
                    $ratingrefs->rating_id = $ratID->id;
                    $ratingrefs->ticket_id = $id;
                    $ratingrefs->thread_id = '0';
                    $ratingrefs->rating_value = $value;
                    $ratingrefs->save();

                } else {
                    return response()->json(['status' => 'fails', 'message' => Lang::get('lang.rating-modification-not-allowed')]);
                }
            } else {
                $rating_ref->rating_id = $ratID->id;
                $rating_ref->ticket_id = $id;

                $rating_ref->thread_id = '0';
                $rating_ref->rating_value = $value;
                $rating_ref->save();
            }
        }

        if(!$request->ajax()){
            $ticket =  Tickets::find($id);
            $alert = new AlertAndNotification;
            $notificationStatus = $alert->checkAlertAndNotification("rating_confirmation");
            $notificationStatus = ($notificationStatus == null) ? [] : $notificationStatus;
            if(isset($ratingrefs->rating_value))
                $this->sendRatingNotify($notificationStatus, $ticket, $ratingrefs->rating_value);
            else
                $this->sendRatingNotify($notificationStatus, $ticket, $rating_ref->rating_value);

            return view('rating');
        }
        else{
            return response()->json(['status' => 'success', 'message' => Lang::get('lang.thanks-for-rating')]);
        }
    }


    public function sendRatingNotify($notificationStatus, $ticket, $rating_value) {
        $notifications =[[
            "rating_confirmation" => [
                'ticketid' =>  $ticket,
                'from' => $this->PhpMailController->mailfrom('1', $ticket->dept_id),
                'message' => ['subject' => 'Rating Submitted', 'scenario' => 'rating-confirmation'],
                'variable' => array_merge($ticket->ticketTemplateVariables(),['rating_value' => $rating_value ])
            ]
        ]];
        $alert = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
        $alert->setDetails($notifications);
    }

    /**
     * Store Client rating about reply of agent quality.
     *
     * @return type Redirect
     */
    public function ratingReply($id, Request $request, \App\Model\helpdesk\Ratings\RatingRef $rating_ref) {
        foreach ($request->all() as $key => $value) {
            if ($key == '_token') {
                continue;
            }
            $key1 = explode(',', $key);
            if (strpos($key1[0], '_') !== false) {
                $ratName = str_replace('_', ' ', $key1[0]);
            } else {
                $ratName = $key1[0];
            }
            $ratID = \App\Model\helpdesk\Ratings\Rating::where('name', '=', $ratName)->first();
            $ratingrefs = $rating_ref->where('rating_id', '=', $ratID->id)->where('thread_id', '=', $key1[1])->first();

            if ($ratingrefs !== null) {
                if ($ratID->allow_modification) {
                    $ratingrefs->rating_id = $ratID->id;
                    $ratingrefs->ticket_id = $id;
                    $ratingrefs->thread_id = $key1[1];
                    $ratingrefs->rating_value = $value;
                    $ratingrefs->save();
                } else {
                    return response()->json(['status' => 'fails', 'message' => Lang::get('lang.rating-modification-not-allowed')]);
                }
            } else {
                $rating_ref->rating_id = $ratID->id;
                $rating_ref->ticket_id = $id;
                $rating_ref->thread_id = $key1[1];
                $rating_ref->rating_value = $value;
                $rating_ref->save();
            }
        }


        return response()->json(['status' => 'success', 'message' => Lang::get('lang.thanks-for-rating')]);
    }

    /**
     * System default email.
     */
    public function system_mail() {
        $email = Email::where('id', '=', '1')->first();

        return $email->sys_email;
    }

    /**
     * checkLock($id)
     * function to check and lock ticket.
     *
     * @param int $id
     *
     * @return int
     */
    public function checkLock($id) {
        $ticket = DB::table('tickets')->select('id', 'lock_at', 'lock_by')->where('id', '=', $id)->first();
        $cad = DB::table('settings_ticket')->select('collision_avoid')->where('id', '=', 1)->first();
        $cad = $cad->collision_avoid; //collision avoid duration defined in system

        $to_time = strtotime($ticket->lock_at); //last locking time

        $from_time = time(); //user system's cureent time
        // difference in last locking time and user system's current time
        $diff = round(abs($to_time - $from_time) / 60, 2);

        if ($diff < $cad && Auth::user()->id != $ticket->lock_by) {
            $user_data = User::select('user_name', 'first_name', 'last_name')->where('id', '=', $ticket->lock_by)->first();
            if ($user_data->first_name != '') {
                $name = $user_data->first_name . ' ' . $user_data->last_name;
            } else {
                $name = $user_data->username;
            }

            return Lang::get('lang.locked-ticket') . " <a href='" . route('user.show', $ticket->lock_by) . "'>" . $name . '</a>&nbsp;' . $diff . '&nbsp' . Lang::get('lang.minutes-ago');  //ticket is locked
        } elseif ($diff < $cad && Auth::user()->id == $ticket->lock_by) {
            $ticket = Tickets::where('id', '=', $id)->first();
            $ticket->lock_at = date('Y-m-d H:i:s');
            $ticket->save();

            return 4;  //ticket is locked by same user who is requesting access
        } else {
            if (Auth::user()->id == $ticket->lock_by) {
                $ticket = Tickets::where('id', '=', $id)->first();
                $ticket->lock_at = date('Y-m-d H:i:s');
                $ticket->save();

                return 1; //ticket is available and lock ticket for the same user who locked ticket previously
            } else {
                $ticket = Tickets::where('id', '=', $id)->first();
                $ticket->lock_by = Auth::user()->id;
                $ticket->lock_at = date('Y-m-d H:i:s');
                $ticket->save(); //ticket is available and lock ticket for new user
                return 2;
            }
        }
    }

    /**
     * function to Change owner.
     *
     * @param type $id
     *
     * @return type bool
     */
    public function changeOwner($id) {
        $action = Input::get('action');
        $email = Input::get('email');
        $ticket_id = Input::get('ticket_id');
        $send_mail = Input::get('send-mail');

        if ($action === 'change-add-owner') {
            $name = Input::get('name');
            $returnValue = $this->changeOwnerAdd($email, $name, $ticket_id);
            if ($returnValue === 0) {
                return 4;
            } elseif ($returnValue === 2) {
                return 5;
            } else {
                //do nothing
            }
        }
        $user = User::where('email', $email)->first();
        if ($user) {
            $user_id = $user->id;
            $ticket = Tickets::where('id', '=', $id)->first();
            if ($user_id === (int) $ticket->user_id) {
                return 400;
            }
            if (($user->is_delete == 1)) {
                return 500;
            }
            $ticket_number = $ticket->ticket_number;
            $ticket->user_id = $user_id;
            $ticket->save();
            $ticket_thread = Ticket_Thread::where('ticket_id', '=', $id)->first();
            $ticket_subject = $ticket_thread->title;
            $thread = new Ticket_Thread();
            $thread->ticket_id = $ticket->id;
            $thread->user_id = Auth::user()->id;
            $thread->is_internal = 1;


            $name_of_user = $user->user_name;
            if ($user->first_name) {
                $name_of_user = $user->first_name;
                if ($user->last_name) {
                    $name_of_user .= ' ' . $user->last_name;
                }
            }

            $thread->body = 'This ticket now belongs to  <b>' . $name_of_user . '</b>';
            $thread->save();

            //mail functionality
            $company = $this->company();
            $system = $this->system();

            $agent = $user->first_name;
            $agent_email = $user->email;

            $master = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            if (Alert::first()->internal_status == 1 || Alert::first()->internal_assigned_agent == 1) {
                // ticket assigned send mail
                Mail::send('emails.Ticket_assign', ['agent' => $agent, 'ticket_number' => $ticket_number,
                    'from' => $company, 'master' => $master, 'system' => $system], function ($message) use ($agent_email, $agent, $ticket_number, $ticket_subject) {
                    $message->to($agent_email, $agent)->subject($ticket_subject . '[#' . $ticket_number . ']');
                });
            }

            return 1;
        } else {
            return 0;
        }
    }

    /**
     * useradd.
     *
     * @param type Image $image
     *
     * @return type json
     */
    public function changeOwnerAdd($email, $name, $ticket_id) {
        $name = $name;
        $email = $email;
        $ticket_id = $ticket_id;
        $validator = \Validator::make(
                        [
                    'email' => $email,
                    'name' => $name,
                        ], [
                    'email' => 'required|email',
                        ]
        );
        $user = User::where('email', '=', $email)->first();
        $checkEmailSettings=Emails::where('email_address',$email)->count();
        if ($user || $checkEmailSettings == 1) {
            return 0;
        } elseif ($validator->fails()) {
            return 2;
        } else {
            $company = $this->company();
            $user = new User();
            $user->first_name = $name;
            $user->email = $email;
            $user->user_name =$email;
            $password = $this->generateRandomString();
            $user->password = \Hash::make($password);
            $user->role = 'user';
            $user->active = 1;
            if ($user->save()) {
                $user_id = $user->id;
            //associate user to organization base on domain match
                UserController::domainConnection($user_id);
                try {
                    $notifications[] = [
                        'registration_notification_alert' => [
                            'userid' => $user->id,
                            'from' => $this->PhpMailController->mailfrom('1', '0'),
                            'message' => ['subject' => null, 'scenario' => 'registration-notification'],
                            'variable' => ['new_user_name' => $name, 'new_user_email' => $email,
                                'user_password' => $password]
                        ],
                        'new_user_alert' => [
                            'model' => $user,
                            'userid' => $user->id,
                            'from' => $this->PhpMailController->mailfrom('1', '0'),
                            'message' => ['subject' => null, 'scenario' => 'new-user'],
                            'variable' => ['new_user_name' => $name, 'new_user_email' => $email,
                                'user_profile_link' => faveoUrl('user/' . $user->id)]
                        ],
                    ];
                    $alert = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
                    $alert->setDetails($notifications);
                } catch (\Exception $e) {

                }
            }

            return 1;
        }
    }
    /**
     * This method display select drop down option value
     * @param integer $id     ticket id
     * @return type
     */
    public function getMergeTickets($id)
    {
        try {
            if ($id == 0) {
                $t_id = Input::get('data1');
                foreach ($t_id as $value) {
                    $title = Ticket_Thread::select('title')->where('ticket_id', $value)->where('title', '<>', '')->first();
                    $ticketNumber = Tickets::where('id', $value)->value('ticket_number');
                    $displayData = '#' . $ticketNumber . '(' . $title->getSubject() . ')';
                    echo "<option value='$value'>" . str_limit($displayData, 45) . ')' . '</option>';
                }
            } else {
                $ticket = Tickets::select('user_id', 'status')->where('id', $id)->first();
                $purpose = $ticket->statuses()->first()->type()->first()->name;
                $ticket_data = Tickets::select('ticket_number', 'id')
                                ->where('user_id', $ticket->user_id)->where('id', '!=', $id)->whereIn('status', getStatusArray($purpose))->get();
                foreach ($ticket_data as $value) {
                    $title = Ticket_Thread::select('title')->where('ticket_id', $value->id)->where('title', '<>', '')->first();
                    $ticketNumber = Tickets::where('id', $value->id)->value('ticket_number');
                    $displayData = '#' . $ticketNumber . '(' . $title->getSubject() . ')';
                    echo "<option value='$value->id' title='$displayData'>" . str_limit($displayData, 45) . '</option>';
                }
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function checkMergeTickets($id) {
        if ($id == 0) {
            if (Input::get('data1') == null || count(Input::get('data1')) == 1) {
                return 0;
            } else {
                $t_id = Input::get('data1');
                $previousValue = null;
                $match = 1;
                foreach ($t_id as $value) {
                    $ticket = Tickets::select('user_id', 'status')->where('id', '=', $value)->first();
                    if ($previousValue == null || $previousValue == $ticket->user_id) {
                        $previousValue = $ticket->user_id;
                        $match = 1;
                    } else {
                        $match = 2;
                        break;
                    }
                }

                return $match;
            }
        } else {
            $ticket = Tickets::select('user_id', 'status')->where('id', '=', $id)->first();
            $purpose = $ticket->statuses()->first()->type()->first()->name;
            $ticket_data = Tickets::select('ticket_number', 'id')
                            ->where('user_id', '=', $ticket->user_id)
                            ->where('id', '!=', $id)
                            ->whereIn('status', getStatusArray($purpose))->get();
            if (isset($ticket_data) && count($ticket_data) >= 1) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function mergeTickets($id) {
        $success = 0;


        $statusType = TicketStatusType::where('name','merged')->first();

        $defaultMergeStatus = $statusType->status()->where('default', 1)->first();
        //in some case default status was getting empty. This is just a workaournd and must be removed once
        //that issue is fixed
        if(!$defaultMergeStatus) {
           return errorResponse(trans('lang.unable_to_merge_due_to_default_close_status'));
        }
        $defaultMergeStatusId = $defaultMergeStatus->id;
        // split the phrase by any number of commas or space characters,
        // which include " ", \r, \t, \n and \f
        $t_id = explode(",", $id);
        if (count($t_id) > 1) {
            $p_id = Input::get('p_id'); //parent ticket id
            $t_id = array_values(array_diff($t_id, [$p_id]));
        } else {
            $t_id = Input::get('t_id'); //getting array of tickets to merge
            if ($t_id == null) {
                return 2;
            } else {
                $temp_id = Input::get('p_id'); //getting parent ticket
                if ($id == $temp_id) {
                    $p_id = $id;
                } else {
                    $p_id = $temp_id;
                    array_push($t_id, $id);
                    $t_id = array_values(array_diff($t_id, [$temp_id]));
                }
            }
        }
        //if only one ticket is selected and tries to merge with itself
        if(count($t_id) == 1 && $t_id[0] == $p_id){
            return errorResponse(Lang::get('lang.nothing_to_merge'));
        }
        $parent_ticket = Tickets::select('ticket_number','status')->where('id', '=', $p_id)->first();
        //ticket status of unapproved ticket should only be changed by the agent who is allowed to change it
        if($this->ifUnapproved($parent_ticket)){
                $message = Lang::get('lang.permission_denied');
                return response()->json(compact('message'), 403);
        }

        // getting parent purpose of status to check if tickets that are merged are of same status
        $parentPurposeOfStatus = $parent_ticket->statuses()->first()->type()->first()->id;
        foreach ($t_id as $value) {//to create new thread of the tickets to be merged with parent
            $thread = Ticket_Thread::where('ticket_id', '=', $value)->first();
            if ($thread) {
                //check for purpose of status
                $ticket = Tickets::select('ticket_number','status')->where('id', '=', $value)->first();

                //if child purpose of status is not same as parent, it is going to skip the rest of the loop
                if($ticket->statuses()->first()->type()->first()->id !== $parentPurposeOfStatus) {
                    continue;
                }

                //updating the thread
                // create new threads
                Ticket_Thread::where('ticket_id', '=', $value)->get()->map(function($thread) use ($p_id){
                    $newThread = $thread->replicate();
                    $newThread->ticket_id = $p_id;
                    $newThread->saveQuietly();
                });

                Tickets::where('id', '=', $value)->first()->update(['status' => $defaultMergeStatusId, 'parent_id' => $p_id]);

                //event has $p_id and $value
                \Event::dispatch('ticket.merge', [['parent' => $p_id, 'child' => $value]]);
                if (!empty(Input::get('reason'))) {
                    $reason = Input::get('reason');
                } else {
                    $reason = Lang::get('lang.no-reason');
                }
                if (!empty(Input::get('title'))) {
                    Ticket_Thread::where('ticket_id', '=', $p_id)->first()
                            ->update(['title' => Input::get('title')]);
                }


                $childLogMessage = Lang::get('lang.get_merge_message') .
                        '&nbsp;&nbsp;<a href="' . route('ticket.thread', [$p_id]) .
                        '">#' . $parent_ticket->ticket_number . '</a><br><br><b>' . Lang::get('lang.merge-reason') . ':</b>&nbsp;&nbsp;' . $reason;

                // child ticket logging
                TicketActivityLogRepository::log($childLogMessage, $thread->ticket_id);

                // parent ticket logging
                $parentLogMessage = Lang::get('lang.ticket') . '&nbsp;<a href="' . route('ticket.thread', [$value]) . '">#' . $ticket->ticket_number . '</a>&nbsp;' . Lang::get('lang.ticket_merged') . '<br><br><b>' . Lang::get('lang.merge-reason') . ':</b>&nbsp;&nbsp;' . $reason;
                TicketActivityLogRepository::log($parentLogMessage, $p_id);

                $success = 1;

            }
        }

        $this->mergeCollaborators($p_id, $t_id, $success);

        if($success){
             return successResponse(Lang::get('lang.ticket_merged_successfully'));
        }

         return errorResponse(Lang::get('lang.ticket_not_merged'));
    }
    /**
     * This method display select drop down option value
     * @param integer $id     ticket id
     * @return type
     */
    public function getParentTickets($id)
    {
        try {
            $term = Input::get('data1');
            $threads = Ticket_Thread::where('ticket_id', '!=', $id)->whereIn('ticket_id', $term)->groupBy('ticket_id')->get();
            if (count($threads) > 0) {
                foreach ($threads as $value) {
                    $ticketNumber = Tickets::where('id', $value->ticket_id)->value('ticket_number');
                    $displayData = '#' . $ticketNumber . '(' . $value->getSubject() . ')';
                    echo "<option value='$value->ticket_id' title='$displayData'>" . str_limit($displayData, 30) . '</option>';
                }
            } else {
                echo "<option value=''></option>";
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /*
     * chumper's function to return data to chumper datatable.
     * @param Array-object $tickets
     *
     * @return Array-object
     */

    public static function getTable($tickets) {
        return \DataTables::of($tickets)
                        // ->removeColumn('a_fname','a_lname','a_uid','c_fname','c_lname','c_uid','assigned_to','color','countattachement','acountcollaborator','countthread','created_at','created_at2','css','dept_id','due','duedate','last_replier','name','priority','source','ticket_title','updated_at','verified')
                        ->editColumn('id', function ($tickets) {
                            $rep = ($tickets->last_replier == 'client') ? '#F39C12' : '#000';
                            return "<center><input type='checkbox' name='select_all[]' id='" . $tickets->id . "' onclick='someFunction(this.id)' class='selectval icheckbox_flat-blue " . $tickets->color . " " . $rep . "' value='" . $tickets->id . "'></input></center>";
                        })
                        ->addColumn('title', function ($tickets) {
                            if (isset($tickets->ticket_title)) {
                                $string = utfEncoding($tickets->ticket_title);
                                if (strlen($string) > 25) {
                                    $string = str_limit($string, 30) . '...';
                                }
                            } else {
                                $string = Lang::get('lang.no-subject');
                            }
                            $collab = $tickets->countcollaborator;
                            if ($collab > 0) {
                                $collabString = '&nbsp;<i class="fa fa-users" title="' . Lang::get('lang.ticket_has_collaborator') . '"></i>';
                            } else {
                                $collabString = null;
                            }
                            $attachCount = $tickets->countattachment;
                            if ($attachCount > 0) {
                                $attachString = '&nbsp;<i class="fa fa-paperclip" title="' . Lang::get('lang.ticket_has_attachments') . '"></i>';
                            } else {
                                $attachString = '';
                            }
                            $css = $tickets->css;
                            $source = $tickets->source;
                            $titles = '';
                            if ($tickets->ticket_title) {
                                $titles = $tickets->ticket_title;
                            }
                            $due = '';
                            if ($tickets->duedate != NULL) {
                                $now = strtotime(\Carbon\Carbon::now()->tz(timezone()));
                                $duedate = strtotime($tickets->duedate);
                                $check_due_time = strtotime($tickets->closed_at);

                                if ($tickets->is_closed != 1) {
                                    $check_due_time = $now;
                                }
                                if ($duedate - $check_due_time < 0) {
                                    $due = '&nbsp;<span style="background-color: rgba(221, 75, 57, 0.67) !important" title="' . Lang::get("lang.is_overdue") . '" class="label label-danger">' . Lang::get('lang.overdue') . '</span>';
                                } else {
                                    if (date('Ymd', $duedate) == date('Ymd', $now) && $tickets->is_closed != 1) {
                                        $due = '&nbsp;<span style="background-color: rgba(240, 173, 78, 0.67) !important" title="' . Lang::get("lang.going-overdue-today") . '" class="label label-warning">' . Lang::get('lang.duetoday') . '</span>';
                                    }
                                }
                            }
                            if ($tickets->is_deleted == 1) {
                                $due = '';
                            }

                            $thread_count = '(' . $tickets->countthread . ')';
                            if (Lang::getLocale() == "ar") {
                                $thread_count = '&rlm;(' . $tickets->countthread . ')';
                            }
                            $dept = "";
                            if ($tickets->departments) {
                                $dept = "\n\n" . Lang::get('lang.department') . ": <span style='color:green'>" . $tickets->departments->name . "</span>";
                            }
                            $tooltip_script = self::tooltip($tickets->id);
                            return "<div class='tooltip1' id='tool" . $tickets->id . "'>
                            <a href='" . route('ticket.thread', [$tickets->id]) . "'>" . $string . "&nbsp;<span style='color:green'>" . $thread_count . "</span>
                            </a> <span><i style='color:green' title='" . Lang::get('lang.ticket_created_source', ['source' => $source]) . "' class='" . $css . "'></i></span>" . $collabString . $attachString . $due . $tooltip_script .
                                    "<span class='tooltiptext' id='tooltip" . $tickets->id . "' style='height:auto;width:300px;background-color:#fff;color:black;border-radius:3px;border:2px solid gainsboro;position:absolute;z-index:1;top:150%;left:50%;margin-left:-23px;word-wrap:break-word;'>" . Lang::get('lang.loading') . "</span></div>" . $dept;
                        })
                        ->editColumn('ticket_number', function ($tickets) {
                            return "<a href='" . route('ticket.thread', [$tickets->id]) . "' class='$" . ucfirst($tickets->priority) . "*' title='" . Lang::get('lang.click-here-to-see-more-details') . "'>#" . $tickets->ticket_number . '</a>';
                        })
                        ->editColumn('c_uname', function ($tickets) {
                            $from = $tickets->c_fname;
                            $url = route('user.show', $tickets->c_uid);
                            $name = $tickets->c_uname;
                            if ($from) {
                                $name = utfEncoding($tickets->c_fname) . ' ' . utfEncoding($tickets->c_lname);
                            }
                            $color = '';
                            if ($tickets->verified == 0 || $tickets->verified == '0') {
                                $color = "<i class='fa fa-exclamation-triangle'  title='" . Lang::get('lang.accoutn-not-verified') . "'></i>";
                            }
                            return "<a href='" . $url . "' title='" . Lang::get('lang.see-profile1') . ' ' . $name . '&apos;' . Lang::get('lang.see-profile2') . "'><span style='color:#508983'>" . str_limit($name, 30) . ' <span style="color:#f75959">' . $color . '</span></span></a>';
                        })
                        ->editColumn('a_uname', function ($tickets) {
                            if ($tickets->assigned_to == null && $tickets->name == null) {
                                return "<span style='color:red'>Unassigned</span>";
                            } else {
                                $assign = $tickets->assign_user_name;
                                if ($tickets->assigned_to != null) {
                                    $assign = utfEncoding($tickets->a_fname) . ' ' . utfEncoding($tickets->a_lname);
                                    $url = route('user.show', $tickets->assigned_to);
                                    return "<a href='" . $url . "' title='" . Lang::get('lang.see-profile1') . ' ' . $assign . '&apos;' . Lang::get('lang.see-profile2') . "'><span style='color:green'>" . mb_substr($assign, 0, 30, 'UTF-8') . '</span></a>';
                                } else {
                                    $url1 = "#";
                                    return "<a href='" . $url1 . "' title='" . Lang::get('lang.see-profile1') . ' ' . ucfirst($tickets->name) . '&apos;' . Lang::get('lang.see-profile2') . "'><span style='color:green'>" . mb_substr(ucfirst($tickets->name), 0, 30, 'UTF-8') . '</span></a>';
                                }
                            }
                        })
                        ->editColumn('updated_at2', function ($tickets) {
                            $TicketDatarow = $tickets->updated_at;
                            $updated = '--';
                            if ($TicketDatarow) {
                                $updated = faveoDate($tickets->updated_at);
                            }
                            return '<span style="display:none">' . $updated . '</span>' . $updated;
                        })
                        ->filterColumn('a_uname', function($query, $keyword) {
                            $sql = "CONCAT(u2.first_name,' ',u2.last_name)  like ?";
                            $sql2 = "u2.user_name like ?";
                            $query->whereRaw($sql, ["%{$keyword}%"])->orWhereRaw($sql2, ["%{$keyword}%"]);
                        }
                        )
                        ->filterColumn('c_uname', function($query, $keyword) {
                            $sql = "CONCAT(u1.first_name,' ',u1.last_name)  like ?";
                            $sql2 = "u1.user_name like ?";
                            $query->whereRaw($sql, ["%{$keyword}%"])->orWhereRaw($sql2, ["%{$keyword}%"]);
                        }
                        )
                        ->filterColumn('title', function($query, $keyword) {
                            $sql = 'th.title  like ?';
                            $query->whereRaw($sql, ["%{$keyword}%"]);
                        }
                        )
                        ->rawColumns(['id', 'title', 'ticket_number', 'c_uname', 'a_uname', 'updated_at2'])
                        ->make();
    }

    /**
     * @category function to call and show ticket details in tool tip via ajax
     * @param null
     * @return string //script to load tooltip data
     *
     */
    public static function tooltip($ticketid) {
        return "<script>
                var timeoutId;
                $('#tool" . $ticketid . "').hover(function() {
                    if (!timeoutId) {
                        timeoutId = window.setTimeout(function() {
                        timeoutId = null; // EDIT: added this line
                                $.ajax({
                                url:'" . url('ticket/tooltip') . "',
                                dataType:'json',
                                type:'get',
                                data:{'ticketid':" . $ticketid . "},
                                success : function(json){
                                console.log(json.first_name);
                                    $('#tooltip" . $ticketid . "').html('<div class=tooltipHead><div style='+'width:20%;float:left'+'><img src='+json.profile_pic+' width='+'45px'+' height='+'45px'+'></div><div style='+'width:70%'+'><p style='+'margin: 2px;'+'><b>'+json.first_name+'</b></p><p style='+'margin: 2px;'+'>'+json.created_at+'</p></div></div><div class='+'tooltipbody'+'>'+json.body+'</div>');
                                },
                            });
                        }, 2000);
                    }
                },
                function () {
                    if (timeoutId) {
                        window.clearTimeout(timeoutId);
                        timeoutId = null;
                    } else {
                    }
                });
                </script>";
    }

    public function getTooltip(Request $request) {
        $ticketid = $request->input('ticketid');
        $ticket = Tickets::find($ticketid);
        if(!$ticket) return errorResponse(trans('lang.ticket_not_found'), FAVEO_NOT_FOUND_CODE);
        $requester = $ticket->user()->select('first_name', 'last_name', 'user_name', 'profile_pic', 'email', 'id')
                ->first()
                ->toArray();
        $thread = $ticket
                ->thread()
                ->select('body', 'ticket_id')
                ->where('is_internal', '!=', '1')
                ->orderBy('id', 'desc')
                ->first();
        $body = ['body' => str_limit(strip_tags($thread->body), 50), 'ticket_id' => $thread->ticket_id,
            'created_at' => $ticket->created_at->format('Y-m-d h:i:s')];
        $result = array_merge($requester, $body);
        return response()->json($result);
    }

    /**
     * @category function to chech if user verifcaition required for creating tickets or not
     * @param null
     * @return int 0/1
     */
    public function checkUserVerificationStatus() {
        $status = CommonSettings::select('status')
                ->where('option_name', '=', 'send_otp')
                ->first();
        if ($status->status == 0 || $status->status == "0") {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * This function is used for auto filling in new ticket
     * @return type view
     */
    public function autofill() {
        return view('themes.default1.agent.helpdesk.ticket.getautocomplete');
    }

    public static function getSourceByname($name) {
        $sources = new Ticket_source();
        $source = $sources->where('name', $name)->first();
        if ($source) {
            return $source;
        } else {
            return $sources->first();
        }
    }

    public static function getSourceById($sourceid) {
        $sources = new Ticket_source();
        $source = $sources->where('id', $sourceid)->first();
        return $source;
    }

    public static function getSourceCssClass($sourceid) {
        $css = "fa fa-comment";
        $source = self::getSourceById($sourceid);
        if ($source) {
            $css = $source->css_class;
        }
        return $css;
    }

    public function getSystemDefaultHelpTopic() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->find(1);
        $help_topicid = $ticket_setting->help_topic;
        return $help_topicid;
    }

    public function getSystemDefaultSla() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->find(1);
        $sla = $ticket_setting->sla;
        return $sla;
    }

    public function getSystemDefaultPriority() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->find(1);
        $priority = $ticket_setting->priority;
        return $priority;
    }

    public function getSystemDefaultDepartment() {
        $systems = new \App\Model\helpdesk\Settings\System();
        $system = $systems->find(1);
        $department = $system->department;
        return $department;
    }

    public function findTicketFromTicketCreateUser($result = []) {
        $ticket_number = $this->checkArray('0', $result);
        if ($ticket_number !== "") {
            $tickets = new \App\Model\helpdesk\Ticket\Tickets();
            $ticket = $tickets->where('ticket_number', $ticket_number)->first();
            if ($ticket) {
                return $ticket;
            }
        }
    }

    public function findUserFromTicketCreateUserId($result = []) {
        $ticket = $this->findTicketFromTicketCreateUser($result);
        if ($ticket) {
            $userid = $ticket->user_id;
            return $userid;
        }
    }

    public function checkArray($key, $array) {
        $value = "";
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
        }
        return $value;
    }

    public function getAdmin() {
        $users = new \App\User();
        $admin = $users->where('role', 'admin')->first();
        return $admin;
    }

    public function attachmentSeperateOld($attach) {
        $attacment = [];
        if ($attach != null) {
            $size = count($attach);
            for ($i = 0; $i < $size; $i++) {
                $file_name = $attach[$i]->getClientOriginalName();
                $file_path = $attach[$i]->getRealPath();
                $mime = $attach[$i]->getClientMimeType();
                $attacment[$i]['file_name'] = $file_name;
                $attacment[$i]['file_path'] = $file_path;
                $attacment[$i]['mime'] = $mime;
            }
        }
        return $attacment;
    }

    public function attachmentSeperate($thread_id) {
        if ($thread_id) {
            $array = [];
            $attachment = new Ticket_attachments();
            $attachments = $attachment->where('thread_id', $thread_id)->get();
            if ($attachments->count() > 0) {
                foreach ($attachments as $key => $attach) {
                    $array[$key]['file_path'] = $attach->file;
                    $array[$key]['file_name'] = $attach->name;
                    $array[$key]['mime'] = $attach->type;
                    $array[$key]['mode'] = 'data';
                }
                return $array;
            }
        }
    }

    public static function getSubject($subject) {
        //$subject = $this->attributes['title'];
        $array = imap_mime_header_decode($subject);
        $title = "";
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $text) {
                $title .= $text->text;
            }
            return wordwrap($title, 70, "<br>\n");
        }
        return wordwrap($subject, 70, "<br>\n");
    }

    public function replyContent($content) {
        preg_match_all('/<img[^>]+>/i', $content, $result);
        $url = [];
        $encode = [];
        $img = array();
        foreach ($result as $key => $img_tag) {
            //dd($img_tag);
            preg_match_all('/(src)=("[^"]*")/i', $img_tag[$key], $img[$key]);
        }
        for ($i = 0; $i < count($img); $i++) {
            $url = $img[$i][2][0];
            $encode = $this->divideUrl($img[$i][2][0]);
        }

        return str_replace($url, $encode, $content);
    }

    public function divideUrl($url) {
        $baseurl = url('/');
        $trim = str_replace($baseurl, "", $url);
        $trim = str_replace('"', '', $trim);
        $trim = substr_replace($trim, "", 0, 1);
        $path = public_path($trim);
        return $this->fileContent($path);
    }

    public function fileContent($path) {
        $exist = \File::exists($path);
        $base64 = "";
        if ($exist) {
            $content = \File::get($path);
            $type = \File::extension($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);
        }
        return $base64;
    }

    public function overdue($slaid, $ticket_created, $ticket = "") {

        $sla = new \App\Http\Controllers\SLA\ApplySla();
        if ($ticket) {
            $sla->requester_id = $ticket->user_id;
            $sla->dept = $ticket->dept_id;
        }
        $responds_due = $sla->slaRespondsDue($slaid, $ticket_created, $ticket)->timezone('UTC');
        //$resolve_due = $sla->slaResolveDue($slaid, faveotime($ticket_created->timezone(timezone()), true, true, true))->timezone('UTC');
        if ($responds_due) {
            return $responds_due;
        } else {
            return $resolve_due;
        }
    }

    /**
     * This function is used to change the status of a ticket
     * @param string  $ticketIdsString    comma separated ids of tickets
     * @param int     $statusId           id of status to be set
     * @param int     $userId             Id of user to perform change status if not authenticated default null
     */
    public function changeStatus($ticketIdsString, $statusId, $userId = null) {

        $userModel = (Auth::check()) ? Auth::user() : User::find($userId);
        if(!$userModel) throw new \Exception('User not found');
        $statusModel = \App\Helper\Finder::status($statusId);
        return $this->changeStatusAfterPermissionCheck($statusModel, $userModel, $ticketIdsString);
    }

    /**
     * Function handles sending rating feedback requests automatically on changing
     * the status of the tickets.
     *
     * @deprecated
     *
     * @internal This method is a temporary method to achieve KPMG rating requirement
     * functionality within the deadline and must be removed during rewriting
     * the rating module.
     *
     * @todo remove this method and create a common method in Rating module classes to
     * achieve this functionality
     */
    private function sendRatingMail(Alert $alert, $user = null, $ticket = null, $status){
        try{
            $alerts = \App\Model\helpdesk\Settings\Alert::where('key', 'rating_mail_statuses')->pluck('value')->toArray();
            if(empty($alerts)) return false; //nothing much to do

            if(in_array($status->id, explode(',', $alerts[0]))){
                $this->sendRatingRequestMail($ticket);
            }
        } catch(\Exception $e) {
            \Log::info("Exception caught feedback mails was not sent due to : ".$e);
        }

    }

    /**
     *====================================================================
     *                 TEMPORARY JUGAAD
     *====================================================================
     * Function sends rating request mail alerts for given ticket
     *
     * @param  Tickets $ticket Ticket for which rating is being requested
     * @return \Illuminate\Http\JsonResponse
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     * @since v3.5.0
     * @deprecated
     *
     * @internal This method is a temporary method to achieve KPMG rating requirement
     * functionality within the deadline and must be removed during rewriting
     * the rating module.
     *
     * @todo remove this method and create a common method in Rating module classes to
     * achieve this functionality
     */
    public function sendRatingRequestMail(Tickets $ticket)
    {
        try{
            $notifications = [[
                'rating_feedback_alert' => [
                    'ticketid' => $ticket->id,
                    'from' => $this->PhpMailController->mailfrom('1', $ticket->dept_id),
                    'message' => ['subject' => 'Rating', 'scenario' => 'rating'],
                    'variable' => array_merge($ticket->ticketTemplateVariables(), ["ratings_icon_with_link" => (new \App\Http\Controllers\Admin\helpdesk\RatingsController)->getRatingsIconWithLink($ticket->id)])
                ]
            ]];
            $alert = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
            $alert->setDetails($notifications);

            return successResponse(trans('lang.feedback-request-sent'));
        } catch(\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function sendSurvey(Alert $alert, $ticket, $status){
        try{
            $alerts = \App\Model\helpdesk\Settings\Alert::where('key', 'lime_survey_mail_statuses')->pluck('value')->toArray();
            if(empty($alerts)) return false; //nothing much to do

            if(in_array($status->id, explode(',', $alerts[0]))) {
                $surveyLink = \App\Plugins\LimeSurvey\Model\LimeSurvey::first();
                $surveyLink = $surveyLink->survey_link."&ticket_number=".$ticket->ticket_number;//"&agent_name=".$agentFullName;

                $notifications =[[
                    'lime_survey_alert' => [
                        'ticketid' =>  $ticket->id,
                        'from' => $this->PhpMailController->mailfrom('1', $ticket->dept_id),
                        'message' => ['subject' => 'Quick Survey', 'scenario' => 'lime-survey'],
                        'variable' => array_merge($ticket->ticketTemplateVariables(),['survey_link' => $surveyLink ])
                    ]
                ]];
                $alert = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
                $alert->setDetails($notifications);
            }
        } catch(\Exception $e){
            \Log::info("Exception caught while sending survey : ".$e->getMessage());
        }
    }

//    public function haltDue($statusid, $ticket_id) {
//        $halt = new HaltController($ticket_id);
//        return $halt->changeStatus($statusid);
//    }

    public function groupUserCredentials($user_detail) {
        return [
            'to_user' => $user_detail->user_name,
            'to_email' => $user_detail->email
        ];
    }

    /**
     * This function is used autofill Department name.
     *
     * @return json
     */
    public function departmentAutofill(Request $request) {

       $deparments = ($request->term) ? Department::where('name', 'LIKE', '%' . $request->term . '%')->orderBy('name')->take(10)->get() :  Department::orderBy('name')->take(10)->get() ;
       $json = array();
        foreach ($deparments as $deparment) {
            $json[] = array(
            'value' => $deparment["name"],
            'label' => $deparment["name"],
         );
        }
       return json_encode($json);
     }

    /**
     *
     *
     */
    public function getDepartmentSign($id) {
        $sign = "";
        $dept = Department::select('department_sign')->where('id', '=', $id)->first();
        if ($dept) {
            $sign = $dept->department_sign;
        }
        return $sign;
    }

    public function sendNotification($ticket, $status, $field = 'email') {
        $senders_emails = $this->persons($ticket, $status, $field, ['email', 'user_name', 'first_name',
            'last_name', 'role', 'user_language']);
        $status_msg = $this->statusMsg($ticket, $status);
        $from = $this->PhpMailController->mailfrom('1', $ticket->dept_id);
        $client_name = '';
        $client_email = '';
        $client_contact = '';
        $agent_email = '';
        $agent_name = '';
        $agent_contact = '';
        $requester = $ticket->user;
        $assign_agent = $ticket->assigned;
        $ticket_due_date = "";
        $ticket_created_date = "";
        if ($ticket->duedate) {
            $ticket_due_date = $ticket->duedate->tz(timezone());
        }
        if ($ticket->created_at) {
            $ticket_created_date = $ticket->created_at->tz(timezone());
        }
        if ($requester) {
            $client_name = ($requester->first_name != '' || $requester->last_name != null) ? $requester->first_name . ' ' . $requester->last_name : $requester->user_name;
            $client_email = $requester->email;
            $client_contact = $requester->mobile;
        }
        if ($assign_agent) {
            $agent_email = $assign_agent->email;
            $agent_name = ($assign_agent->first_name != '' || $assign_agent->last_name != null) ? $assign_agent->first_name . ' ' . $assign_agent->last_name : $assign_agent->user_name;
            $agent_contact = $assign_agent->mobile;
        }
        $subject = title($ticket->id);
        $message = [
            'subject' => $subject . "[#" . $ticket->ticket_number . "]",
            'scenario' => 'status-update',
        ];
        $template_variables = [
            'ticket_subject' => title($ticket->id),
            'ticket_link' => faveoUrl('thread/' . $ticket->id),
            'ticket_number' => $ticket->ticket_number,
            'ticket_due_date' => $ticket_due_date,
            'ticket_created_at' => $ticket_created_date,
            'message_content' => $status_msg,
            'agent_name' => $agent_name,
            'agent_email' => $agent_email,
            'agent_contact' => $agent_contact,
            'client_email' => $client_email,
            'client_name' => $client_name,
            'client_contact' => $client_contact,
        ];
        foreach ($senders_emails as $sender) {
            $to = [
                'email' => $sender['email'],
                'name' => ($sender['first_name'] != '' || $sender['first_name'] != null) ? $sender['first_name'] . ' ' . $sender['last_name'] : $sender['user_name'],
                'role' => $sender['role'],
                'preferred_language' => $sender['user_language']
            ];
            $this->PhpMailController->sendmail($from, $to, $message, $template_variables);
        }
        $senders_sms = [];
        if ($status->send_sms == 1) {
            $senders_sms = $this->persons($ticket, $status, 'mobile', ['email', 'user_name', 'first_name', 'last_name',
                'role', 'user_language', 'mobile', 'country_code']);
        }
        if (count($senders_sms) > 0) {
            $notification = new Notifications\NotificationController();
            if ($notification->checkPluginSetup()) {
                foreach ($senders_sms as $sender) {
                    $sms_controller = new \App\Plugins\SMS\Controllers\MsgNotificationController;
                    $sms_controller->notifyBySMS($sender, $template_variables, $message, $ticket);
                }
            }
        }
    }

    public function persons($ticket, $status, $field, $collection_fields = '') {
        $send_person = $status->send_email;
        $notification = new Notifications\NotificationController();
        $notices_id = collect();
        foreach ($send_person as $person => $send) {
            if ($send == 1) {
                $notices_id->push($notification->getAgentIdByDependency($person, $ticket));
            }
        }
        $notices = $notices_id->flatten()->unique()->filter(function ($item) {
            return $item != null;
        });
        if (sizeof($collection_fields) > 0) {
            $senders = \App\User::whereNotNull($field)->whereIn('id', $notices)->select($collection_fields)->get()->toArray();
        } else {
            $senders = \App\User::whereNotNull($field)->whereIn('id', $notices)->pluck($field, 'first_name')->toArray();
        }
        return $senders;
    }

    public function statusMsg($ticket, $status) {
        $thread = $ticket->thread()
                ->where('is_internal', '1')
                ->select('user_id', 'id', 'ticket_id', 'body')
                ->orderBy('id', 'desc')
                ->first()
        ;
        $user_id = $thread->user_id;
        $user = User::find($user_id);
        $username = "System";
        if ($user) {
            $username = $user->first_name . " " . $user->last_name;
        }
        $message = str_replace('{!!$user!!}', $username, $status->message);
        return $message;
    }

    public function rule($panel = 'client') {
        $required = \App\Model\Custom\Required::
                where('form', 'ticket')
                ->select("$panel as panel", 'field', 'option')
                ->where(function($query)use($panel) {
                    return $query->whereNotNull($panel)
                            ->where($panel, '!=', '')
                    ;
                })
                ->get()
                ->transform(function($value) {
                    $option = $value->option;
                    if ($option) {
                        $option = "," . $value->option;
                    }
                    $request[$value->field] = $value->panel . $option;
                    return $request;
                })
                ->collapse()
                ->except(['requester', 'body', 'description'])
                ->toArray();
        return $required;
    }

    public function ticketOpenTime($ticket_id) {
        // NOT removing this method since its used by multiple methods/classes. Just changing the definition
        try {
            $ticket = Tickets::whereId($ticket_id)
                    ->first();
            if (!$ticket) {
                return 0;
            }
            $start_date = $ticket->created_at;
            $end_date = ($ticket->closed_at) ? $ticket->closed_at : \Carbon\Carbon::now();
            return (new SlaEnforcer($ticket))->getTimeDifferenceInBH($start_date, $end_date);
        } catch (\Exception $e) {
            return 0;
        }
    }


    /**
     * This method display type name base on help topic type linking
     * @param Request $request
     * @return type json
     */
    public function helptopicType(Request $request)
    {
        try {

            $changeTypeFrom = $request->ticket_create;

            if ($changeTypeFrom != 1) {
                $assignType = HeltopicAssignType::where('helptopic_id', '=', $request->helptopic_id)->pluck('type_id')->toArray();
                $tktTypes = ($assignType) ? Tickettype::whereIn('id', $assignType)->where('status', 1)->get() : Tickettype::where('id', '!=', 0)->where('status', 1)->get();
                $html = "";
                $html .= "<option value=' '>" . 'Select Type' . "</option>";
                $htmlValue = "";
                foreach ($tktTypes as $tktType) {
                    $htmlValue .= "<option value='" . $tktType->id . "'>" . $tktType->name . "</option>";
                }
                echo $html, $htmlValue;
            } else {
                $assignType = HeltopicAssignType::where('helptopic_id', '=', $request->helptopic)->pluck('type_id')->toArray();
                $tktTypes = ($assignType) ? Tickettype::whereIn('id', $assignType)->where('status', 1)->select('id', 'name as optionvalue')->get()->toJson() : Tickettype::where('id', '!=', 0)->where('status', 1)->select('id', 'name as optionvalue')->get()->toJson();
                return $tktTypes;
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

/**
 * This method return agent and team lists
 * @param Request $request
 * @return json
 * @deprecated since 27/10/2020 God bless this innocent soul. RIP
 * @todo remove this method permanently after the next two releases since the current date
 * (perform cremation ceremony)
 */

    public function assignTicketList(Request $request)
    {
        try {

            $ticketId = explode(",", $request->Input('ticket_id'));

            $searchQuery = $request->Input('search-query');

            $assignTo = $request->Input('assign-to');
            //if assign to team
            if($assignTo == 'team'){

                $teamLists = Teams::where('status', '1')->where(function($q) use($searchQuery) {
                      $q-> where('name', 'LIKE', '%'.$searchQuery.'%');})
                ->select('id', 'name')->orderBy('name')->take(10)->get();
                $outputData =['teams'=>$teamLists];

            return successResponse('',$outputData);
            }

            $deptIds = Tickets::whereIn('id', $ticketId)->groupBy('dept_id')->pluck('dept_id')->toArray();

            $multiDeptAgents = $this->getAgentId($deptIds);
            //get agent id based on global access permission
            $agentId = getAgentBasedOnPermission('global_access');

            $agentIdList = array_unique(array_merge($multiDeptAgents, $agentId));



            //using helper function
            $assignAgentLists = agentListAlphabeticOrder($agentIdList,$searchQuery);
            $outputData =['agents'=>$assignAgentLists];

            return successResponse('',$outputData);

        } catch (\Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method return agent id based on departmentIds (And Condition)
     * @param array $deptIds of department
     * @return array
     */
    public function getAgentId(array $deptIds = []): array
    {
        foreach ($deptIds as $key => $value) {
               $agentId[] = DepartmentAssignAgents::where('department_id',$value)->pluck('agent_id')->toArray();
            }

        $intersectedArray = (count($agentId) > 1) ? call_user_func_array('array_intersect',$agentId) : $agentId[0] ;
        return $intersectedArray;
    }

    /**
     *
     *
     *
     */
    public static function getOldTabWithRelation($tickets) {
        return \DataTables::eloquent($tickets)
            ->editColumn('id', function ($tickets) {
                $rep = ($tickets->poster == 'client') ? '#F39C12': '#000';
                return "<center><input type='checkbox' name='select_all[]' id='" . $tickets->id . "' onclick='someFunction(this.id)' class='selectval icheckbox_flat-blue " . $tickets->priority->priority_color . " " . $rep . "' value='" . $tickets->id . "'></input></center>";
            })
            ->editColumn('user.user_name', function ($ticket) {
                if ($ticket->user) {
                    $url  = route('user.show', $ticket->user->id);
                    if ($ticket->user->first_name) {
                        return  '<a href="'.$url.'" title="'.Lang::get('lang.click_to_see_profile', ['user' => $ticket->user->first_name.' '.$ticket->user->last_name]).'">'.$ticket->user->first_name.' '.$ticket->user->last_name.'</a>';
                    }
                    return '<a href="'.$url.'" title="'.Lang::get('lang.click_to_see_profile', ['user' => $ticket->user->user_name]).'">'.$ticket->user->user_name.'</a>';
                }
                return Lang::get('lang.not-available');
            })
            ->editColumn('assigned.user_name', function ($ticket) {
                if($ticket->assigned_to || $ticket->team_id) {
                    if ($ticket->assigned_to) {
                        $url = route('user.show', $ticket->assigned->id);
                        return ($ticket->assigned->first_name)? '<a href="'.$url.'" title="'.Lang::get('lang.click_to_see_profile', ['user' => $ticket->assigned->first_name.' '.$ticket->assigned->last_name]).'">'.$ticket->assigned->first_name.' '.$ticket->assigned->last_name.'</a>' : '<a href="'.$url.'" title="'.Lang::get('lang.click_to_see_profile', ['user' => $ticket->assigned->user_name]).'">'.$ticket->assigned->user_name.'</a>';
                    } else {
                        $url = route('teams.profile.show', $ticket->assignedTeam);
                        return '<a href="'.$url.'" title="'.Lang::get('lang.click_to_see_profile', ['user' => $ticket->assignedTeam->team_name]).'">'.$ticket->assignedTeam->team_name.'</a>';
                    }
                } else {
                    return '<span style="color:red; font-size:.9em">'.Lang::get('lang.Unassigned').'</span>';
                }
            })
            ->editColumn('ticket_number', function ($ticket) {
                return "<a href='" . route('ticket.thread', [$ticket->id]) . "' class='$" . ucfirst($ticket->priority->priority) . "*' title='" . Lang::get('lang.click-here-to-see-more-details') . "'>#" . $ticket->ticket_number . '</a>';
            })
            ->addColumn('title', function($ticket) {
                $due = '';
                $due_status = 0;
                $dueTodayLabel = '&nbsp;<span style="background-color: rgba(240, 173, 78, 0.67) !important" title="' . Lang::get("lang.going-overdue-today") . '" class="label label-warning">' . Lang::get('lang.duetoday') . '</span>';
                $overdueLabel = '&nbsp;<span style="background-color: rgba(221, 75, 57, 0.67) !important" title="' . Lang::get("lang.is_overdue") . '" class="label label-danger">' . Lang::get('lang.overdue') . '</span>';
                if($ticket->duedate != null) {
                    $now = strtotime(\Carbon\Carbon::now()->tz(timezone()));
                    $duedate = strtotime($ticket->duedate);
                    $check_due_time = $now;
                    $due_status = $duedate - $check_due_time;
                    if ($due_status < 0) {
                        $due = $overdueLabel;
                    } else {
                        if (date('Ymd', $duedate) == date('Ymd', $now)) {
                            $due = $dueTodayLabel;
                        }
                    }
                } else {
                    $due = '&nbsp;<span style="background-color: rgba(240, 173, 78, 0.67) !important" title="' . Lang::get("lang.sla-clock-is-paused") . '" class="label label-warning">' . Lang::get('lang.sla-halted') . '</span>';
                    if ($ticket->statuses->purpose_of_status == 2) {
                        $due =  ($ticket->is_resolution_sla == 1) ? '' : $overdueLabel;
                    }
                }
                $string = utfEncoding($ticket->threadSelectedFields->title);
                if (strlen($string) > 25) {
                    $string = str_limit($string, 30) . '...';
                }
                $thread_count = '(' . $ticket->thread_count . ')';
                if (Lang::getLocale() == "ar") {
                       $thread_count = '&rlm;(' . $ticket->thread_count . ')';
                }
                $source = '<span><i style="color:green" title="' . Lang::get('lang.ticket_created_source', ['source' => $ticket->sources->source]) .'" class="' . $ticket->sources->source_icon . '"></i></span>';
                $collab = ($ticket->collaborator_count != null) ? '&nbsp;<i class="fa fa-users" title="' . Lang::get('lang.ticket_has_collaborator') . '"></i>' : '';
                $attach = ($ticket->attachment_count != 0) ? '&nbsp;<i class="fa fa-paperclip" title="' . Lang::get('lang.ticket_has_attachments') . '"></i>' : '' ;
                $tooltip_script = self::tooltip($ticket->id);
                return '<div class="tooltip1" id="tool' . $ticket->id . '"><a href="'.route('ticket.thread', [$ticket->id]).'" title="' . Lang::get('lang.click-here-to-see-more-details') . '">'.$string.'</a>&nbsp;<span style="color:green">'. $thread_count .'</span>&nbsp;'.$source.'&nbsp;'.$attach.'&nbsp;'.$collab.'&nbsp;'.$due. $tooltip_script .
                                    '<span class="tooltiptext" id="tooltip' . $ticket->id . '" style="height:auto;width:300px;background-color:#fff;color:black;border-radius:3px;border:2px solid gainsboro;position:absolute;z-index:1;top:150%;left:50%;margin-left:-23px;word-wrap:break-word;">' . Lang::get('lang.loading') . '</span></div>';
            })
            ->editColumn('last_response', function(Tickets $ticket){
                return faveoDate($ticket->last_response,'','', false);
            })
            ->rawColumns(['id', 'ticket_number', 'user.user_name', 'assigned.user_name', 'title'])
            ->make();
    }

    /**
     * checks ticket status, if it is unapproved then checks for permission
     * @param   $ticket ticket with status
     * @return boolean true if ticket is unapproved and also user is not permitted to access that else false
     */
    private function ifUnapproved($ticket)
    {
        return $ticket->statuses->type->name == 'unapproved';
    }

    /**
     * @param   Object   $ticketBuilder  Ticket model builder
     * @param   Integer  $useId          Id of a user who replied on ticket via mail
     * @return  Object                   Ticket Object
     */
    protected function getTicketForEmailCheck($ticketBuilder, $userId)
    {
        $user = User::where('id', $userId)->first();
        $urerOrgs = ($user) ? $user->getUsersOrganisations()->pluck('org_id')->toArray() : [];
        $orgOfWhichUserIsManager = ($user) ? $user->getUsersOrganisations()->where('role', 'manager')->pluck('org_id')->toArray() : [];
        return $ticketBuilder->when($userId, function($q) use($userId, $urerOrgs, $orgOfWhichUserIsManager) {
            $q->where(function($q) use($userId, $urerOrgs, $orgOfWhichUserIsManager) {
                $q->where('user_id', $userId)
                ->orWhereHas('collaborator', function($q) use($userId) {
                    $q->where('user_id', $userId);
                })->orWhereHas('ticketOrganizations', function($q) use($orgOfWhichUserIsManager) {
                        $q->whereIn('org_id', $orgOfWhichUserIsManager);
                })->when(commonSettings('user_reply_org_ticket', '', 'status'), function($q) use($urerOrgs) {
                    $q->orWhereHas('ticketOrganizations', function($q) use($urerOrgs) {
                        $q->whereIn('org_id', $urerOrgs);
                    });
                });
            });
        })->first();

    }

    /**
     * Function to change status of tickets after checking permission
     * @param   Object    $statusModel
     * @param   Object    $userModel
     * @param   String    $ticketIdsString
     * @return  Mixed                        response or string
     */
    private function changeStatusAfterPermissionCheck($statusModel, $userModel, $ticketIdsString)
    {
        if(!$this->checkUserHasPermissionToChangeStatus($statusModel, $userModel)) {
            return errorResponse(Lang::get('lang.permission_denied'));
        }
        $ticketIds = $this->getTicketIdsAfterCheckingOpenedTasks($ticketIdsString);
        if (!$ticketIds) {
            return errorResponse(Lang::get('lang.selected_ticket_task_pending'));
        }
        return $this->performStatusChangeAction($ticketIds, $userModel, $statusModel);
    }

    private function performStatusChangeAction($ticketIds, $userModel, $statusModel)
    {
        foreach ($ticketIds as $key => $ticketId) {
            $ticket = $this->getTicketModelForStatusChange($ticketId, $userModel, $statusModel);
            if(!$ticket) {
                return errorResponse(Lang::get('lang.permission_denied'));
            }
            if($this->ifUnapproved($ticket)){
                return errorResponse(Lang::get('lang.unapproved_ticket_status_cant_be_changed'));
            }

            $ticket->status = $statusModel->id;
            $changed = $ticket->isDirty() ? $ticket->getDirty() : false;
            $ticket->save();
            if ($changed) {
                $this->doExtraThingsAfterStatusChange($changed, $ticket, $statusModel, $userModel);
            }
        }

        return successResponse(Lang::get('lang.ticket_status_changed_successfully'));
    }

    /**
     * Fucntion to perform extra action after changing status of the ticket for example sending notifiation for
     * status change and fire webhooks trigger etc
     * @param   Boolean   $changed
     * @param   Object    $ticket
     * @param   Object    $statusModel
     */
    private function doExtraThingsAfterStatusChange($changed, $ticket, $statusModel, $userModel)
    {
        if ($changed) {

            try {
                if(isPlugin('LimeSurvey')) $this->sendSurvey(new Alert, $ticket, $statusModel);
                $this->sendRatingMail(new Alert, $ticket->user_id, $ticket, $statusModel);
                event(new \App\Events\WebHookEvent($ticket,"ticket_status_updated"));
            } catch(\Exception $e){
                \Log::info("Webhook Exception Caught:  ".$e->getMessage());
            }
            $this->sendNotification($ticket, $statusModel);
            $this->handleCommentsOnStatusChange($ticket, $userModel);
        }
    }

    /**
     * Function to return ticket elequent object for status change method
     * @param  Integer   $ticketId   Id of a ticket
     * @param  Object    $userModel  model object of user
     * @param  Object    $statusModel  model object of status
     * @return Mixed     False or Object depending on user role and access over tickets
     */
    private function getTicketModelForStatusChange($ticketId, $userModel, $statusModel)
    {
        $ticket = Tickets::where('id', '=', $ticketId)->first();
        if ($userModel->role == 'user') {
            if ($statusModel->allow_client != 1 && !$userModel->isManagerOf()) {
                $ticket = false;
            }
        }
        return $ticket;
    }

    /**
     * Function to check user has permission to change status or not currently the function
     * just checks for closed, delete and unapproved purpose of statuses for persmission
     * @param  Object  $statusModel    model object of status to be updated
     * @param  Object  $userMmodel     model object of user who is performing status change action
     * @return Boolean                 true is user has correct permission otherwise false
     */
    private function checkUserHasPermissionToChangeStatus($statusModel, $userModel)
    {
        $statusPurpose = $statusModel->purpose;
        switch ($statusPurpose) {
            case 'closed':
                return User::has('close_ticket');
            case 'deleted':
                return User::has('delete_ticket');
            case 'unapproved':
                return !($userModel->role == 'agent');
            default:
                return true;
        }
    }

    /**
     * Function to filter out tickets which have opened tasks and return ticket ids array
     * @param   String   $ticketIdString  string which contains ticket id separated by comma
     * @return  Mixed                     Null or an Array integer elements for ticket id
     */
    private function getTicketIdsAfterCheckingOpenedTasks($ticketIdsString)
    {
        $ticketIds = explode(",", $ticketIdsString);
        if (isPlugin($plugin = 'Calendar')) {
            foreach ($ticketIds as $key => $ticketId) {
                if (\App\Plugins\Calendar\Model\Task::where('ticket_id', '=', $ticketId)->where('status', 'Open')->pluck('ticket_id')->first())
                    unset($ticketIds[$key]);
            }
        }
        return $ticketIds;
    }

    public function handleCommentsOnStatusChange($ticket, $userModel)
    {
        $body = nl2br(Input::get('comment'));
        if (!$body)  return false;
        $poster = 'support';
        $internalNote = false;

        if (!\Request::input('as-reply')) {
            $poster = $userModel->role;
            $internalNote = true;
        }

        $this->saveReply($ticket->id , $body, $userModel->id, false, [], [], true, $poster, [],'',[], false, false, $internalNote);
        return true;
    }

    /*
     * Function to check close ticket reply with waiting time
     *
     * @param  string  $ticketId
     * @return Boolean             true is create new ticket otherwise replay as ticket thread
     */

    public function checkReplyTime($ticketId)
    {
        $ticket = Tickets::find($ticketId);
        $purposeOfStatus = Ticket_Status::where('id', $ticket->status)->value('purpose_of_status');
        //$purposeOfStatus = 2 means close
        if ($purposeOfStatus == "2") {
            $checkWaitingTime = Ticket::where('id', 1)->value('waiting_time');
            $convertTime = $ticket->closed_at->addHours($checkWaitingTime);

          //checking status time with current time
            if (Carbon::now()->greaterThan($convertTime)) {

                return true;
            }
        }
        return false;
    }

    /**
     * Function updates Ticket_Collaborator to move collaborators of multiple tickets to
     * the given ticket and can be used while merging multiple tickets.
     * It ensures that parent ticket contains all unique CCs of all child tickets
     *
     * @param  Int    $pId           update ticket_id to $pId in Ticket_Collaborator
     * @param  Array  $childIds      update where ticket_id are in $childIds
     * in Ticket_Collaborator
     * @param  bool   $mergedStatus  shows merge operation status default true
     * @return void
     */
    private function mergeCollaborators(int $pId, Array $childIds, bool $mergedStatus = true)
    {
        if ($mergedStatus) {
            $parentCollaborators = Tickets::find($pId)->collaborator()->pluck('user_id')->toArray();
            //getting unique child id to update cc user
            $uniqueIds = array_keys(array_unique(Ticket_Collaborator::whereIn('ticket_id', $childIds)->pluck('user_id', 'id')->toArray()));
            Ticket_Collaborator::whereIn('id', $uniqueIds)->whereNotIn('user_id', $parentCollaborators)->update(['ticket_id' => $pId]);
        }
    }
}
