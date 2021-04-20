<?php

namespace App\Http\Controllers\Client\helpdesk;

use Auth;
use Lang;
use Input;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Controllers\Agent\helpdesk\TicketWorkflowController;
use App\Http\Requests\helpdesk\ClientReplyRequest; 

class ClientTicketController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return type response
     */
    public function __construct()
    {
        $this->TicketWorkflowController = new TicketWorkflowController;

        $this->middleware('auth', ['except' => 'haveATicket']);
        $this->middleware('board', ['except' => 'haveATicket']);
    }

    /**
     * Get Checked ticket.
     *
     * @param type Tickets $ticket
     * @param type User    $user
     *
     * @return type response
     */
    public function getCheckTicket(Tickets $ticket, User $user)
    {
        return view('themes.default1.client.helpdesk.guest-user.newticket', compact('ticket'));
    }

    /**
     * handles a client reply
     * @param integer $id ticketId  id of the ticket to which client is replying
     * @param object $request       Request
     * @deprecated since v3.4.0
     * @internal mobile apps as still using this API call which is handled by this method. We will
     * remove it after updating clients and mobile apps.
     * @return type json            Success message if success else failure
     */
    public function reply($id, ClientReplyRequest $request)
    {
        try {
            $ticket = Tickets::find($id);
            $thread = Ticket_Thread::where('ticket_id', $ticket->id)->first();

            $subject = $thread->title . '[#' . $ticket->ticket_number . ']';
            $body = $request->input('reply');
            $fromaddress = Auth::user()->email;
            $fromname = Auth::user()->user_name;
            $phone = '';
            $phonecode = '';
            $mobile_number = '';

            $helptopic = $ticket->help_topic_id;
            $sla = $ticket->sla;
            $priority = $ticket->priority_id;
            $source = $ticket->source;
            $collaborators = $request->input('cc', []);
            $dept = $ticket->dept_id;
            $assign = $ticket->assigned_to;
            $form_data = [];
            $team_assign = null;

            $syncCollaborators = Auth::user()->id == $ticket->user_id;
            /////////////////////For CKEditor Copy/Paste Image feature//////////////////////////
            //////////////////////////////////////////////////////////////////////////Phaniraj K


            //parsing HTML String to get image source
            $doc = new \DOMDocument();
            /*
            Here Error is purposefully suppressed using "@" because,PHP 7+
            throws Warning eventhough it Successfully parse the new HTML5 
            elements (In our case <figure> element). 
            */
            @$doc->loadHTML($body); 
            $images = iterator_to_array($doc->getElementsByTagName("img"));
            if($images) {
                foreach($images as $img) {
                    if($imgSource = $img->getAttribute("src")) {
    
                        $imagePath = substr($imgSource,strpos($imgSource,"ckeditor"));
                        $imagePathArray = explode("/",$imagePath);
                        $up = new UploadedFile(
    
                            $imagePath,
                            end($imagePathArray),
                            null,
                            filesize($imagePath),
                            0,
                            false
    
                        );
    
                        
                        
                        //adding copied image as inline attachment to request
                        if($request->has("inline") && !empty($request->inline)) {
                            /*
                            
                            If request has already a key named "inline" that maybe added
                            in previous iterations,we have to store previous value,remove
                            and re-add with extra images as we cannot manipulate directly 
                            a key in request object otherwise PHP Will throw a Fatal Error.
    
                            */
                            $previousInline = $request['inline'];
                            $request->request->remove('inline');
                            array_push($previousInline,$up);
                            $request->request->add(["inline" => $previousInline]);
    
                        } else {
                            $request->request->add(["inline" => array($up)]);
                        }
    
    
                        /*
                        stripping full path of file by replacing just the image filename
                        instead of whole path.
                        */
                        $img->setAttribute("src",end($imagePathArray));
                        
                        $body = $doc->saveHTML();
    
                    }
                } 
            }

            
            ////////////////////////////////////////END//////////////////////////////////////////
            ///////////////////////////////////////////////////////////////////////////Phaniraj K


            //purpose of status 7 corresponds to unapproved status, which should not change is client is replying
            $ticket_status = ($ticket->statuses->purpose_of_status == 7) ? $ticket->status : null;
            $auto_response = 0;
            $type = "";
            $attachment = Input::hasFile('attachment') ? Input::file('attachment') : "";
            $inline = $request->has("inline") ? $request->inline : []; //Phaniraj K
            $formattedCollaborators = [];
            array_map(function($collaborator) use (&$formattedCollaborators){
              $formattedCollaborators[$collaborator] = $collaborator;
            }, $collaborators);
            /**
             * Saving the day by using saveReply method of TicketController instead
             * of $this->TicketWorkflowController->workflow() which was being used previously.
             * !!! THANK ME LATER !!!
             *
             * Jugaad No 119999
             * Not syncing the collaborators from here due to time constraint as this
             * class needs to be improved completely 
             */
            $ticketControllerObj = new TicketController;
            if($ticketControllerObj->checkReplyTime($id)) throw new \Exception(trans('lang.can_not_reply_on_inactive_ticket'), 1);
            
            $thread = $ticketControllerObj->saveReply($id, $body, Auth::user()->id, true, $attachment, $inline, true, 'client', [], $formattedCollaborators, [], $syncCollaborators, true);

            $result = ["success" => Lang::get('lang.successfully_replied')];

            return response()->json(compact('result'));         
        } catch (\Exception $e) {

            return errorResponse($e->getMessage(), 400);
        }
    }

    /**

     * Get tickets info for particular user or get tickets info for particular organization
     * @return json
     */
    public function myTickets(Request $request)
    {

        try {

            $ticketStatus = $request->ticket_status;
            
            if (!$ticketStatus) {
                return errorResponse(Lang::get('lang.please_select_ticket_status'));
            }


            $userId = $request->user_id;
            $checkTicketFor = 'others';

            if(!$userId)
            {
                $userId = Auth::user()->id;
                $checkTicketFor = 'self';
            }

            $role = User::where('id', $userId)->value('role');
            
            $viewAs = $request->view_as;

            if (Auth::user()->role == 'user') {

                $allowedTickets = $this->handleClientPanelTickets(Auth::user()->id, $request->org_id);
            } else {
                

                //if organization id passed that time variable will store all organization members ticket ids
                $allowedTickets = ($request->org_id) ? $this->allowedTicketsForOrganization($request->org_id) : $this->allowedTicketsForUser($userId, $role, $checkTicketFor,$viewAs);
            }

            $allowOrgManagerViewTicket = CommonSettings::where('option_name', 'allow_organization_mngr_approve_tickets')->value('status');
            $checkOrgUserRole = User_org::where('user_id', Auth::user()->id)->pluck('role')->toArray();

            $customTicketStatus = (in_array('manager', $checkOrgUserRole) && !$allowOrgManagerViewTicket) ? 'open,approval,unapproved' : 'open,approval';

            $ticketCondition = ($ticketStatus == "open") ? $customTicketStatus : $ticketStatus;

            return successResponse($this->getMyTicketInbox($this->getTicketsByStatus($allowedTickets, $ticketCondition), $request));
            
        } catch (\Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }
/**
 *
 * @param array $ticketId
 * @param Request $request
 * @return type json
 */
    public function getMyTicketInbox($ticketId, Request $request)
    {
        try {

            $pagination = ($request->input('limit')) ?: 10;

            $sortBy = ($request->input('sort-field')) ? $request->input('sort-field') : 'id';
            $search = $request->input('search-query');
            $orderBy = ($request->input('sort-order')) ? $request->input('sort-order') : 'desc';

            $baseQuery = Tickets::whereIn('id', $ticketId)->with([
                'firstThread' => function ($q) { //for getting title of the thread/ticket
                    $q->select('id', 'ticket_id', 'user_id', 'title', 'updated_at');
                },
                'lastThread' => function ($q) { //for getting last repliers name
                    $q->select('id', 'ticket_id', 'user_id', 'title', 'updated_at')->with(['user' => function ($q1) {
                        $q1->select('id', 'first_name', 'last_name','user_name','email');
                    }]);
                }])
                ->with(['user' => function ($q2) {
                    $q2->select('id', 'first_name', 'last_name','user_name','email');
                }])
                ->with('priority:priority_id,priority')

                ->with([

                    'assigned' => function ($q) {
                        return $q->select('id', 'email', 'first_name', 'last_name', 'user_name', 'profile_pic');
                    },
                    'assignedTeam' => function ($q) {
                        return $q->select('id', 'name', 'status');
                    },
                ])
                ->with('collaborator:ticket_id,user_id')
                ->select('tickets.id', 'tickets.user_id', 'tickets.ticket_number', 'tickets.updated_at', 'tickets.priority_id', 'tickets.assigned_to', 'tickets.team_id')
                ->orderBy($sortBy, $orderBy);

            $searchQuery = $baseQuery->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('updated_at', 'LIKE', '%' . $search . '%');
            })
                ->paginate($pagination);
            return $searchQuery;
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Get tickets counts for particular user or get tickets count for particular organization
     * @return json
     */
    public function myTicketsCounts(Request $request)
    {
        try {

            $userId = $request->user_id ? $request->user_id : Auth::user()->id;

            $checkTicketFor = $request->user_id ? 'others' : 'self';

            $role = User::where('id', $userId)->value('role');

            $viewAs = $request->view_as;
            
            $allowOrgManagerViewTicket = CommonSettings::where('option_name', 'allow_organization_mngr_approve_tickets')->value('status');

            $checkOrgUserRole = User_org::where('user_id', Auth::user()->id)->pluck('role')->toArray();

            if (Auth::user()->role == 'user') {

                $allowedTickets = $this->handleClientPanelTickets(Auth::user()->id, $request->org_id);
            } else {

                //if organization id passed that time variable will store all organization members ticket ids
                $allowedTickets = ($request->org_id) ? $this->allowedTicketsForOrganization($request->org_id) : $this->allowedTicketsForUser($userId, $role, $checkTicketFor,$viewAs);
            }

            //get close tickets
            $closeTickets = $this->getTicketsByStatus($allowedTickets, 'closed');
            // get unapproved tickets
            $unapprovedTickets = $this->getTicketsByStatus($allowedTickets, 'unapproved');
            // get delete tickets
            $deleteTickets = $this->getTicketsByStatus($allowedTickets, 'deleted');

            if (($role == 'user') && !$allowOrgManagerViewTicket && (in_array('manager', $checkOrgUserRole))) {

                // get open tickets
                $openTickets = $this->getTicketsByStatus($allowedTickets, 'open,approval,unapproved');

                return successResponse('', ["open" => count($openTickets), "closed" => count($closeTickets), 'deleted' => count($deleteTickets)]);
            }

            // get open tickets
            $openTickets = $this->getTicketsByStatus($allowedTickets, 'open,approval');

            return successResponse('', ["open" => count($openTickets), "closed" => count($closeTickets), 'unapproved' => count($unapprovedTickets), 'deleted' => count($deleteTickets)]);

        } catch (\Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     *
     * @param  array $allowedTickets (ticket id)
     * @param  string $ticketStatus (ex:-'open,approval,unapproved ,close,deleted)
     * @return type json
     */
    public function getTicketsByStatus($allowedTickets, $ticketStatus = 'open')
    {
        $baseTicketQuery = $this->queryBuilderForMyTickets($this->getTickets($allowedTickets, $ticketStatus));

        $ticketId = $baseTicketQuery->pluck('id')->toArray();

        return $ticketId;
    }

    /**
     *
     * @param type $allowedTickets
     * @param type $ticketStatus
     * @return type
     */
    public function getTickets($allowedTickets, $ticketStatus)
    {
        $tickets = new Tickets();
        $ticketQuery = $tickets->orderBy('updated_at', 'desc')->whereIN('id', $allowedTickets)->where(function ($query) use ($ticketStatus) {
            foreach (explode(",", $ticketStatus) as $ticketStatus) {
                $query->orwhereIN('tickets.status', getStatusArray($ticketStatus))->get();
            }
        });
        return $ticketQuery;
    }

    /**
     * helper function for myTickets method
     * build and return query results
     *
     * @return array
     */
    private function queryBuilderForMyTickets($q)
    {
        $tickets = $q->with([
            'firstThread' => function ($q) { //for getting title of the thread/ticket
                $q->select('id', 'ticket_id', 'user_id', 'title', 'updated_at');
            },
            'lastThread' => function ($q) { //for getting last repliers name
                $q->select('id', 'ticket_id', 'user_id', 'title', 'updated_at')->with(['user' => function ($q1) {
                    $q1->select('id', 'first_name', 'last_name');
                }]);
            }])
            ->with('priority:priority_id,priority')
            ->with('collaborator:ticket_id,user_id')
            ->select('tickets.id', 'tickets.ticket_number', 'tickets.updated_at', 'tickets.priority_id')
            ->orderBy('id', 'DESC')
            ->get();
        return $this->formatMyTickets($tickets);
    }

    /**
     * helper function for myTickets method
     * unset unnecessary elements and returns the formatted array
     *
     * @return array
     */
    private function formatMyTickets($tickets)
    {
        foreach ($tickets as $element) {
            $element['encrypt_id'] = Crypt::encryptString($element['id']);
            $element['last_replier'] = $element['lastThread']['user'];
            $element['title'] = $element['firstThread']['title'];
            $priority = $element['priority']['priority'];
            unset($element->priority);
            $element['priority'] = $priority;
            unset($element->firstThread, $element->lastThread, $element->priority_id);
        }
        return $tickets;
    }

    /**
     * gets list of statuses from the Db
     * @return json
     */
    public function getStatusList(Ticket_Status $status, CommonSettings $commonSettings)
    {

        try {
            $isAllowedToSetStatus = $commonSettings->where('option_name', 'user_set_ticket_status')->first();

            if ($isAllowedToSetStatus->status) {

                $statuses = $status->where('visibility_for_client', 1)->where('allow_client', 1)->where('purpose_of_status', '!=', 5)->get();
            } else {
                $statuses = [];
            }
            return successResponse('', ['statuses' => $statuses]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line = $e->getLine();
            $file = $e->getFile();
            return errorResponse($e->getMessage());
        }
    }

    /**
     * This method return ticket id as a array based on user role (may be user as a admin, agent or normal user)
     * @param int $userId of user
     * @param string $role of user
     * @param string $checkTicketFor maybe self/others
     * @param string $viewAs as user/NULL
     * 
     * @return array  (ticket id)
     */
    private function allowedTicketsForUser(int $userId, $role, $checkTicketFor,$viewAs)
    {

        //if view from agent panel that time whatever ticket created by user/agent that will display there
        $column = ($role == 'user' || $checkTicketFor == 'self' || $viewAs == 'user') ? 'user_id' : 'assigned_to';


        $ticketIds = Tickets::where($column, $userId)->pluck('id');
        
        return $ticketIds->toArray();
    }

    /**
     * This method return ticket id as a array based on organization
     * @param int $orgId of Organization
     * @return array  (ticket id)
     */
    private function allowedTicketsForOrganization($orgId)
    {
        $orgMembers = User_org::where('org_id', $orgId)->pluck('user_id')->toArray();

        $ticketId = Tickets::whereIN('user_id', $orgMembers)->pluck('id');

        return $ticketId->toArray();
    }

    /**
     * This method return ticket id as a array based on user type (may be user as a organization manager or organization members or normal user)
     * @param int $userId of user
     * @param string $orgId of Of organization
     * @return type array  (ticket id)
     */
    public function handleClientPanelTickets(int $userId, $orgId = null)
    {
        //get organization id when auth user as a organization manager
        $organizationIds = User_org::where('user_id', $userId)->where('role', 'manager')->pluck('org_id')->toArray();

        $orgMembers = User_org::whereIN('org_id', $organizationIds)->pluck('user_id')->toArray();

        $orgId = ($orgId) ? User_org::where('user_id', $userId)->where('org_id', $orgId)->pluck('org_id')->toArray() : User_org::where('user_id', $userId)->pluck('org_id')->toArray();

        $orgMembersArray = User_org::whereIN('org_id', $orgId)->pluck('user_id')->toArray();

        $orgMembers = array_unique(array_merge($orgMembers, $orgMembersArray));

        //check whether user is allowed to view all the organization tickets
        $showOrgTicketsToUser = CommonSettings::where('option_name', 'user_show_org_ticket')->first();

        //if (belongs to an Get tickets info for particular user or get ticket for particular organization and is allowed to view organization's tickets) else (tickets that user owns)
        $idsOfAllowedTicket = ($orgId && $showOrgTicketsToUser->status == 1 || ($orgId && $organizationIds)) ?

        Tickets::whereIN('user_id', $orgMembers) :
        Tickets::where('user_id', $userId);
        $idsOfAllowedTicket = $idsOfAllowedTicket->when((int)commonSettings('user_show_cc_ticket', '', 'status'), function($query) use ($userId){
            $query->orWhereHas('collaborator', function ($query2) use ($userId) {
                $query2->whereIn('user_id', [$userId]);
            });
        });
        return $idsOfAllowedTicket->pluck('id')->toArray();
    }

    /**
     * ticket details including all the threads
     * @param $id ticketId in encrypted form
     * @param $tickets an instance of Ticket
     * @return json
     */
    public function checkTicket($id, Request $request)
    {
        try {

            $currentPage = $request->input('page') ? $request->input('page') : 1;

            $uploadFileSize = parse_size(ini_get('post_max_size'));
            $uploadSingleFileSize = parse_size(ini_get('upload_max_filesize'));
            $uploadFilecount = parse_size(ini_get('max_file_uploads'));
            $id = Crypt::decryptString($id);

            $replyButton = (new TicketController())->checkReplyTime($id) ? 0 : 1;

            $ticket = $this->queryBuilderForCheckTicket($id, $currentPage);
            $allowedTickets = $this->handleClientPanelTickets(Auth::user()->id);

            $orgTicketReply = CommonSettings::where('option_name', 'user_reply_org_ticket')->value('status');
            $orgMemberReply = ($orgTicketReply == "1");
            if (in_array($ticket['id'], $allowedTickets)) {
                /**
                 * Reaching here means either the ticket
                 * - user belongs to organisation of the owner of the ticket
                 * - user is a collaborator on the ticket
                 *
                 * In second case user should be able to reply on ticket as collaborating
                 * on tickets is allowed
                 */
                $orgMemberReply = $this->isCollaboratingTicket($orgMemberReply, $ticket['collaborators']);
                return successResponse('', ['ticket' => $ticket, 'org_member_reply' => $orgMemberReply, 'uploadfilesize' => $uploadFileSize, 'uploadSingleFileSize' => $uploadSingleFileSize, 'uploadFilecount' => $uploadFilecount, 'replybutton' => $replyButton]);
            }

            return errorResponse(Lang::get('lang.access_denied'), FAVEO_ACCESS_DENIED_CODE);
        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }
    }

    /**
     * Method sets orgMemberReply value as true if authenticated user is
     * collaborating on the ticket
     *
     * @param   bool   $orgMemberReply  Existing value of orgMemberReply
     * @param   array  $collaborators   Array of list containing ticket's collaborators  
     *
     * @return  bool                    true if auth user is collaborating
     *                                  else same value as $orgMemberReply
     */
    private function isCollaboratingTicket(bool $orgMemberReply, array $collaborators=[]):bool
    {
        foreach ($collaborators as $value) {
            if(Auth::user()->id == checkArray('id', $value))
                return true;
        }
        return $orgMemberReply;
    }
    /**
     * Can be used by outside classes to get the same structure of the ticket without a logged in user
     * @param int $ticketId   id of the ticket
     * @param int $currentPage  page number (each page contains 10 tickets)
     * @return Tickets
     */
    public function getTicketById($ticketId, $currentPage)
    {
        return $this->queryBuilderForCheckTicket($ticketId, $currentPage);
    }

    /**
     * Gets data for tickets by applying all the required relationships(specific to checkTicket functionality. Should not be used by other methods)
     * @param $id ticketId
     * @return array
     */
    private function queryBuilderForCheckTicket($id, $currentPage)
    {
        $recordsPerPage = 10;
        $offset = ($currentPage - 1) * $recordsPerPage;
        $ticket = Tickets::where('tickets.id', $id)
            ->select('id', 'duedate', 'dept_id', 'ticket_number', 'help_topic_id', 'status', 'user_id', 'priority_id', 'created_at')
            ->with(['ratings' => function ($q) {
                $q->select('id', 'rating_id', 'rating_value', 'ticket_id')
                    ->where('thread_id', null)->orWhere('thread_id', 0)->orWhere('thread_id', "");
            }])
            ->with(['thread' => function ($q) use ($recordsPerPage, $offset) {
                $q->with('ratings:id,rating_id,rating_value,thread_id')
                    ->orderBy('id', 'desc')
                    ->where('is_internal', '!=', 1)
                    ->select('id', 'user_id', 'title', 'body', 'created_at', 'updated_at', 'ticket_id')
                    ->with('user:id,first_name,last_name,profile_pic,email,role')
                    ->with('attach')->skip($offset)->take($recordsPerPage);
            }])->with('user:id,first_name,last_name,profile_pic,email')
        //if user role is non-user, ratings should not show
            ->with('helptopic:id,topic,department')
            ->with('statuses.secondaryStatus:id,name')
            ->with('priority:priority_id,priority as name')
            ->with(['collaborator' => function ($q) {
                  $q->select('id', 'ticket_id', 'user_id')->with('userBelongs:id,first_name,last_name,user_name,email');
              }])
            ->first()
            ->toArray();


        return $this->formatCheckTicket($ticket);
    }

    /** formats ticket in a way that front-end expects (specific to checkTicket functionality. Should not be used by other methods)
     * @param $ticket Ticket in
     * @return array
     */
    private function formatCheckTicket($ticket)
    {
        $ticket['help_topic'] = $ticket['helptopic']['topic'];
        $threads = [];
        $collaborators =[];
        foreach ($ticket['thread'] as $thread) {

            //extracting title out of thread
            if ($thread['title']) {
                $ticket['title'] = $thread['title'];
            }

            if ($thread['user']['role'] == 'user') {
                unset($thread['ratings']);
            }

            array_push($threads, $thread);
        }
        foreach ($ticket['collaborator'] as $collaborator) {
            $collaborator['user_belongs']['name'] = $collaborator['user_belongs']['meta_name'];
            $collaborator['userBelongs']['collaborator_id'] = $collaborator['id'];
            array_push($collaborators, $collaborator['user_belongs']);
        }
        $ticket['collaborators'] = $collaborators;
        $ticket['threads'] = $threads;
        unset($ticket['thread']);
        $departmentId = $ticket['dept_id'];
        $ticket['department'] = Department::where('id', $departmentId)->select('name')->first()->name;
        $ticket['status'] = isset($ticket['statuses']['secondary_status']) ? $ticket['statuses']['secondary_status']['name'] : $ticket['statuses']['name'];
        $priority = $ticket['priority']['name'];
        unset($ticket['priority'], $ticket['priority_id']);
        $ticket['priority'] = $priority;
        unset($ticket['helptopic'], $ticket['statuses'], $ticket['help_topic_id'], $ticket['user_id'],$ticket['collaborator']);
        return $ticket;
    }
    /**
     * This method return ticket details including all the threads
     * @param $ticketId in encrypted form
     * @return json
     */
    public function haveATicket($ticketId, Request $request)
    {
        try {

            $currentPage = $request->input('page') ? $request->input('page') : 1;
            $ticketId = Crypt::decryptString($ticketId);

            $ticket = $this->queryBuilderForCheckTicket($ticketId, $currentPage);

            return successResponse('', ['ticket' => $ticket]);

        } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }
}
