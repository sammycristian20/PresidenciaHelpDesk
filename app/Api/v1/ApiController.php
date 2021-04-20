<?php

namespace App\Api\v1;

use App\Http\Controllers\Agent\helpdesk\TicketController as CoreTicketController;
use App\Http\Controllers\Agent\helpdesk\TicketsWrite\TicketActionController;
use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\TicketRequest;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_attachments;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Ticket\Ticket_source;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Utility\CountryCode;
use App\Model\helpdesk\Utility\Priority;
use App\SatelliteHelpdesk\Model\SatelliteHelpdesk;
use App\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Input;
use Lang;
use Validator;
use App\Http\Requests\helpdesk\Ticket\AgentPanelTicketRequest;

/**
 * -----------------------------------------------------------------------------
 * Api Controller
 * -----------------------------------------------------------------------------.
 *
 *
 * @author Vijay Sebastian <vijay.sebastian@ladybirdweb.com>
 * @copyright (c) 2016, Ladybird Web Solution
 * @name Faveo HELPDESK
 *
 * @version v1
 */
class ApiController extends Controller
{

    public $user;
    public $request;
    public $ticket;
    public $model;
    public $thread;
    public $attach;
    public $ticketRequest;
    public $faveoUser;
    public $team;
    public $setting;
    public $helptopic;
    public $slaPlan;
    public $department;
    public $priority;
    public $source;
    public $helpdesk;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        /**
         * Note this are temporary changes made for make old create and edit api working
         * till mobile apps are using v3 API completely with new form structure for tickets.
         * 
         * @todo remove this code block as system needs to be converted to use oAuth token
         * generated via passport. Currently this block ensures if v3 is passed in request for
         * ticket creation or edit JWT token shoul not be checked.
         */
        $ignoreJWTAuth = ['createSatelliteUser'];
        if (strpos($request->url(), '/v3')) {
            $ignoreJWTAuth = array_merge($ignoreJWTAuth, ['createTicket', 'editTicket']);
        }
        $this->middleware('jwt.auth', ['except' => $ignoreJWTAuth]);
        /** temp block till here */

        $this->middleware('api', ['except' => 'GenerateApiKey']);
        $this->middleware('role.agent', ['except' => ['getCustomersWith', 'gethelpdesk', 'createSatelliteUser', 'createSatelliteTicket', 'getsatelliteTicketById', 'getTicketsAPI', 'ticketReplyWithDetails', 'userTicketList', 'ticketThreads', 'statusChange', 'getCustomer', 'dependency', 'getTicketById','searchTicket']]);

        $this->user = Auth::user();

        $ticket       = new TicketController();
        $this->ticket = $ticket;

        $model       = new Tickets();
        $this->model = $model;

        $thread       = new Ticket_Thread();
        $this->thread = $thread;

        $attach       = new Ticket_attachments();
        $this->attach = $attach;

        $ticketRequest       = new TicketRequest();
        $this->ticketRequest = $ticketRequest;

        $faveoUser       = new User();
        $this->faveoUser = $faveoUser;

        $team       = new Teams();
        $this->team = $team;

        $setting       = new System();
        $this->setting = $setting;

        $helptopic       = new Help_topic();
        $this->helptopic = $helptopic;

        $slaPlan       = new Sla_plan();
        $this->slaPlan = $slaPlan;

        $priority       = new Ticket_Priority();
        $this->priority = $priority;

        $department       = new Department();
        $this->department = $department;

        $source       = new Ticket_source();
        $this->source = $source;

        $satellite_path = base_path('app/SatelliteHelpdesk');
        if (is_dir($satellite_path)) {
            $helpdesk       = new SatelliteHelpdesk();
            $this->helpdesk = $helpdesk;
        }
    }

    /**
     * Create Tickets.
     *
     * @method POST
     *
     * @param user_id,subject,body,helptopic,sla,priority,dept
     *
     * @return json
     */
    public function createTicket(CountryCode $code)
    {
       $v = Validator::make($this->request->all(), [
            'subject'    => 'required',
            'priority'   => 'required|exists:ticket_priority,priority_id',
            'body'       => 'required',
            'help_topic' => 'required|exists:help_topic,id',
            'email'      => 'email|required',
            'first_name' => 'required',
        ]);
        if ($v->fails()) {
            $error = $v->errors();
            return response()->json(compact('error'));
        }
        $ticketRequester = User::select('id')->where('user_name', $this->request->get('email'))->orWhere('email', $this->request->get('email'))->first();
        if(!$ticketRequester) {
            return errorResponse('Make sure to register the user before creating tickets');
        }

        if (($this->request->has('phone') && $this->request->get('phone') != '') || ($this->request->has('mobile') && $this->request->get('mobile') != '')) {
            if (!$this->request->has('code') || $this->request->get('code') == '') {
                return response()->json(['error' => ['code' => ['The code feild is required.']]]);
            }
        }
        if ($this->request->has('assigned') && !is_numeric($this->request->get('assigned'))) {
            return response()->json(['error' => ['assigned' => ['The assigned feild must me an integer value.']]]);
        } elseif ($this->request->has('assigned') && is_numeric($this->request->get('assigned'))) {
            if (!User::has('assign_ticket')) {
                return response()->json(['message' => Lang::get('lang.permission_denied')], 403);
            }
        }

        $this->formatTicketCreateRequest($this->request);
        $this->request->merge(['requester' => $ticketRequester->id]);
        try {
            $PhpMailController      = new \App\Http\Controllers\Common\PhpMailController();
            $NotificationController = new \App\Http\Controllers\Common\NotificationController();
            $core                   = new CoreTicketController($PhpMailController, $NotificationController);
            $request                = new AgentPanelTicketRequest;
            $this->request->merge(['body' => preg_replace('/[ ](?=[^>]*(?:<|$))/', '&nbsp;', nl2br($this->request->get('body')))]);
            if ($this->request->has('username') && !$this->request->has('email')) {
                $user_id = User::select('id')->where('user_name', '=', $this->request->get('username'))->first();
                if (!$user_id) {
                    return response()->json(['error' => ['code' => ['No user not found with this username.']]]);
                }
                $this->request->request->add(['requester' => $user_id->id]);
            }
            $request->replace($this->request->except('token', 'api_key', 'ip'));
            $response        = $core->post_newticket($request, $code, true);
            $response_status = 200;
            $data = [];
            if (property_exists(json_decode($response->content()), 'data')) {
                $data = json_decode($response->content())->data;
            }

            if (!is_array($response)) {
                $formatted_response = $this->formatResponseFromJsonResponse($response);
                $response           = $formatted_response[0];
                $response_status    = $formatted_response[1];
            }
            $response['data'] = $data;
            if(strpos($this->request->url(), '/v1')) {
                $response['message']= "Ticket created successfully!";
                $response['ticket_id'] = (!empty($data)) ? $data->id : null;
                unset($response['data']);
            }
            return response()->json(compact('response'), $response_status);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'))
                ->header('Authenticate: xBasic realm', 'fake');
        }
    }

    /**
     * JUGAAD method
     * This method is a Jugaad method to keep API v1 alive and working with new 
     * structure of ticket request in post_net_ticket() method of TicketController.
     * @deprecated
     * This method is marked as deprecated as system will no longer support v1 API and
     * will only be avaialble untill v3 is published and users are migrated to work with
     * new API
     */
    private function formatTicketCreateRequest(&$request)
    {
        $requestData = $request->all();
        $formatData['requester'] = checkArray('email', $requestData);
        $formatData['assigned_id'] = checkArray('assigned', $requestData);
        $formatData['priority_id'] = (checkArray('priority', $requestData))?:checkArray('ticket_priority', $requestData);
        $formatData['source_id'] = checkArray('ticket_source', $requestData);
        $formatData['help_topic_id'] = checkArray('help_topic', $requestData);
        $formatData['type_id']  = checkArray('ticket_type', $requestData);
        $formatData['department_id'] = checkArray('dept', $requestData);
        $formatData = array_filter($formatData);
        $request->merge($formatData);
    }

    /**
     * Reply for the ticket.
     *
     * @param TicketRequest $request
     *
     * @return json
     */
    public function ticketReply()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'ticket_id'     => 'required|exists:tickets,id',
                'reply_content' => 'required',
            ]);
            if ($v->fails()) {

                return errorResponse($v->errors(), $responseCode = 400);
            }
            $attachments = $this->request['media_attachment'];
            $inlines     = $this->request['inline'];

            $this->request->merge(['reply_content' => preg_replace('/[ ](?=[^>]*(?:<|$))/', '&nbsp;', nl2br($this->request->get('reply_content')))]);

            $result = $this->ticket->reply($this->thread, $this->request, 'support', $attachments, $inlines);

            $ticket = $result->join('users', 'ticket_thread.user_id', '=', 'users.id')
                ->select('ticket_thread.*', 'users.first_name as first_name')
                ->orderBy('ticket_thread.id', 'desc')
                ->first();
            $message = Lang::get('lang.successfully_replied');
            return successResponse($message);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'), $responseCode = 400);
        } catch (\TokenExpiredException $e) {

            return errorResponse($e->getMessage(), $responseCode = 400);
        }
    }

    /**
     * Edit a ticket.
     *
     * @return json
     */
    public function editTicket()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'ticket_id'       => 'required|exists:tickets,id',
                'subject'         => 'required',
                'help_topic'      => 'required|int|exists:help_topic,id',
                'ticket_source'   => 'required|int|exists:ticket_source,id',
                'ticket_priority' => 'required|int|exists:ticket_priority,priority_id',
                'ticket_type'     => 'int|exists:ticket_type,id',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }

            if ($this->request->has('assigned') && !is_numeric($this->request->get('assigned'))) {
                return response()->json(['error' => ['assigned' => ['The assigned feild must me an integer value.']]]);
            } elseif ($this->request->has('assigned') && is_numeric($this->request->get('assigned'))) {
                if (!User::has('assign_ticket')) {
                    return response()->json(['message' => Lang::get('lang.permission_denied')], 403);
                }
            }
            $this->formatTicketCreateRequest($this->request);
            $ticket_id              = $this->request->input('ticket_id');
            $request                = new \App\Http\Requests\helpdesk\Ticket\TicketEditRequest();
            $request->replace($this->request->except('token', 'api_key', 'ip'));
            $result = (new TicketActionController)->editTicket($ticket_id, $request);
            if (strpos($this->request->url(), '/v1') && $result->getData()->success) {
                return response()->json(['result' => ['success' => trans('lang.edited_successfully')]]);
            }
            return $result;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Delete The Ticket.
     *
     * @return json
     */
    public function deleteTicket()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'ticket_id' => 'required|exists:tickets,id',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }
            $id     = $this->request->input('ticket_id');
            $result = $this->ticket->delete(explode(',', $id), $this->model);

            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get all opened tickets.
     *
     * @return json
     */
    public function openedTickets()
    {
        try {

            $result = $this->user->join('tickets', function ($join) {
                $join->on('users.id', '=', 'tickets.user_id')
                    ->where('isanswered', '=', 0)->where('status', '=', 1)->whereNull('assigned_to');
            })
                ->join('department', 'department.id', '=', 'tickets.dept_id')
                ->join('ticket_priority', 'ticket_priority.priority_id', '=', 'tickets.priority_id')
                ->join('sla_plan', 'sla_plan.id', '=', 'tickets.sla')
                ->join('help_topic', 'help_topic.id', '=', 'tickets.help_topic_id')
                ->join('ticket_status', 'ticket_status.id', '=', 'tickets.status')
                ->join('ticket_thread', function ($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id')
                        ->whereNotNull('title');
                })
                ->select('first_name', 'last_name', 'email', 'profile_pic', 'ticket_number', 'tickets.id', 'title', 'tickets.created_at', 'department.name as department_name', 'ticket_priority.priority as priotity_name', 'sla_plan.name as sla_plan_name', 'help_topic.topic as help_topic_name', 'ticket_status.name as ticket_status_name')
                ->orderBy('ticket_thread.updated_at', 'desc')
                ->groupby('tickets.id')
                ->distinct()
                ->paginate(10)
                ->toJson();

            return $result;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get Unsigned Tickets.
     *
     * @return json
     */
    public function unassignedTickets()
    {
        try {
            $user       = \JWTAuth::parseToken()->authenticate();
            $unassigned = $this->user->join('tickets', function ($join) {
                $join->on('users.id', '=', 'tickets.user_id')
                    ->whereNull('assigned_to');
            })
                ->leftjoin('department', function ($join) {
                    $join->on('department.id', '=', 'tickets.dept_id');
                })
                ->join('ticket_status', 'ticket_status.id', '=', 'tickets.status')
                ->join('ticket_status_type', 'ticket_status.purpose_of_status', '=', 'ticket_status_type.id')
                ->where('ticket_status_type.name', 'open')
                ->leftJoin('ticket_priority', 'ticket_priority.priority_id', '=', 'tickets.priority_id')
                ->leftJoin('sla_plan', 'sla_plan.id', '=', 'tickets.sla')
                ->leftJoin('help_topic', 'help_topic.id', '=', 'tickets.help_topic_id')
                ->leftJoin('ticket_thread', function ($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id');
                })
                ->leftJoin('ticket_attachment', 'ticket_attachment.thread_id', '=', 'ticket_thread.id');
            if ($user->role == 'agent') {
                $id         = $user->id;
                $dept       = \DB::table('department_assign_agents')->where('agent_id', '=', $id)->pluck('department_id')->toArray();
                $unassigned = $unassigned->where(function ($query) use ($dept, $id) {
                    $query->whereIn('tickets.dept_id', $dept)
                        ->orWhere('assigned_to', '=', $id);
                });
            }
            $unassigned = $unassigned->select('ticket_priority.priority_color as priority_color', \DB::raw('substring_index(group_concat(ticket_thread.title order by ticket_thread.id asc) , ",", 1) as title'), 'tickets.duedate as overdue_date', \DB::raw('count(ticket_attachment.id) as attachment'), \DB::raw('max(ticket_thread.updated_at) as updated_at'), 'user_name', 'first_name', 'last_name', 'email', 'profile_pic', 'ticket_number', 'tickets.id', 'tickets.created_at', 'department.name as department_name', 'ticket_priority.priority as priotity_name', 'sla_plan.name as sla_plan_name', 'help_topic.topic as help_topic_name', 'ticket_status.name as ticket_status_name')
                ->orderBy('updated_at', 'desc')
                ->groupby('tickets.id')
                ->distinct()
                ->paginate(10)
                ->toJson();

            return $unassigned;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get closed Tickets.
     *
     * @return json
     */
    public function closeTickets()
    {
        try {
            $user   = \JWTAuth::parseToken()->authenticate();
            $result = $this->user->join('tickets', function ($join) {
                $join->on('users.id', '=', 'tickets.user_id');
            })
                ->leftjoin('department', function ($join) {
                    $join->on('department.id', '=', 'tickets.dept_id');
                })
                ->join('ticket_status', 'ticket_status.id', '=', 'tickets.status')
                ->join('ticket_status_type', 'ticket_status.purpose_of_status', '=', 'ticket_status_type.id')
                ->where('ticket_status_type.name', 'closed')
                ->leftJoin('ticket_priority', 'ticket_priority.priority_id', '=', 'tickets.priority_id')
                ->leftJoin('sla_plan', 'sla_plan.id', '=', 'tickets.sla')
                ->leftJoin('help_topic', 'help_topic.id', '=', 'tickets.help_topic_id')
                ->leftJoin('ticket_thread', function ($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id');
                })
                ->leftJoin('ticket_attachment', 'ticket_attachment.thread_id', '=', 'ticket_thread.id');
            if ($user->role == 'agent') {
                $id     = $user->id;
                $dept   = \DB::table('department_assign_agents')->where('agent_id', '=', $id)->pluck('department_id')->toArray();
                $result = $result->where(function ($query) use ($dept, $id) {
                    $query->whereIn('tickets.dept_id', $dept)
                        ->orWhere('assigned_to', '=', $id);
                });
            }
            $result = $result->select('tickets.duedate as overdue_date', 'ticket_priority.priority_color as priority_color', \DB::raw('substring_index(group_concat(ticket_thread.title order by ticket_thread.id asc) , ",", 1) as title'), \DB::raw('count(ticket_attachment.id) as attachment'), \DB::raw('max(ticket_thread.updated_at) as updated_at'), 'user_name', 'first_name', 'last_name', 'email', 'profile_pic', 'ticket_number', 'tickets.id', 'tickets.created_at', 'department.name as department_name', 'ticket_priority.priority as priotity_name', 'sla_plan.name as sla_plan_name', 'help_topic.topic as help_topic_name', 'ticket_status.name as ticket_status_name')
                ->orderBy('updated_at', 'desc')
                ->groupby('tickets.id')
                ->distinct()
                ->paginate(10)
                ->toJson();

            return $result;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get All agents.
     *
     * @return json
     */
    public function getAgents()
    {
        try {
            $result = $this->faveoUser->where('role', 'agent')->orWhere('role', 'admin')->where('active', 1)->get();

            return response()->json(compact('result'));
        } catch (Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get All Teams.
     *
     * @return json
     */
    public function getTeams()
    {
        try {
            $result = $this->team->get();

            return response()->json(compact('result'));
        } catch (Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * To assign a ticket.
     *
     * @return json
     */
    public function assignTicket()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'ticket_id' => 'required',
                'user_id'   => 'required',
            ]);
            if ($v->fails()) {
                return errorResponse($v->errors(), $responseCode = 400);
            }
            $id       = $this->request->input('ticket_id');
            $response = $this->ticket->assign($id);
            if ($response == 1) {
                $result = 'success';

                $message = Lang::get('lang.successfully_assigned');
                return successResponse($message);
            } else {
                return errorResponse($response, $responseCode = 400);
                return response()->json(compact('response'));
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'), $responseCode = 400);
        } catch (\TokenExpiredException $e) {

            return errorResponse($v->getMessage(), $responseCode = 400);
        }
    }

    /**
     * Get all customers.
     *
     * @return json
     */
    public function getCustomers()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'search' => 'required',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }
            $search = $this->request->input('search');
            $result = $this->faveoUser->where('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%')->orWhere('user_name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%')->get();

            return response()->json(compact('result'))
                ->header('X-Header-One', 'Header Value');
        } catch (Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'))
                ->header('X-Header-One', 'Header Value');
        }
    }

    /**
     * Get all customers having client_id, client_picture, client_name, client_email, client_phone.
     *
     * @return json
     */
    public function getCustomersWith()
    {
        try {
            $users = Auth::user()
                ->leftJoin('user_assign_organization', 'user_assign_organization.user_id', '=', 'users.id')
                ->leftJoin('organization', 'organization.id', '=', 'user_assign_organization.org_id')
                ->where('users.role', 'user')
                ->select('users.id', 'users.user_name', 'users.first_name', 'users.last_name', 'users.email', 'users.phone_number', 'users.profile_pic', 'organization.name AS company', 'users.active', 'users.ext as telephone_extension', 'users.mobile', 'users.phone_number as telephone', 'users.country_code as mobile_code', 'users.role', 'users.is_delete')
                ->paginate(10)
                ->toJson();

            return $users;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'))
                ->header('Authenticate: xBasic realm', 'fake');
        }
    }

    /**
     * Get a customer by id.
     *
     * @return json
     */
    public function getCustomer()
    {

        try {
            $v = \Validator::make($this->request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);
            if ($v->fails()) {
                return errorResponse($v->errors());
            }
            $id = $this->request->input('user_id');

            $user = User::whereId($id)->whereRole('user')->first();
            if (!$user) {

                throw new Exception(Lang::get('lang.user_not_found'));
            }
            $result = $this->faveoUser->where('id', $id)->where('role', 'user')->select('id', 'user_name', 'first_name', 'last_name', 'email', 'role', 'profile_pic')->first();

            return successResponse('', $result);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {

            return errorResponse($e->getMessage());
        }
    }

    /**
     * Search tickets.
     *
     * @return json
     */
    public function searchTicket()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'search' => 'required',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }
            $search = $this->request->input('search');
            $user   = \JWTAuth::parseToken()->authenticate();

            $tickets = Tickets::
                where(function ($query) use ($search) {
                $query->whereHas('thread', function ($thread) use ($search) {
                    $thread->whereNotNull('title')->where('is_internal', 0)
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhere('body', 'like', '%' . $search . '%')
                    ;
                })
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('user_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('mobile', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('assigned', function ($user) use ($search) {
                        $user->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('user_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('mobile', 'like', '%' . $search . '%');
                    })
                    ->orWhere('ticket_number', 'like', '%' . $search . '%')
                ;
            })
                ->leftJoin('ticket_collaborator', function ($q) {
                    $q->on('tickets.id', '=', 'ticket_collaborator.ticket_id')
                        ->where('isactive', 1);
                })
                ->leftJoin('ticket_thread', 'tickets.id', '=', 'ticket_thread.ticket_id')
                ->leftJoin('ticket_attachment', 'ticket_thread.id', '=', 'ticket_attachment.thread_id')
                ->with(['departments' => function ($department) use ($user) {
                    return $department->select('id', 'name')
                        ->with(['assignAgent' => function ($assign_agent) use ($user) {
                            if ($user->role == 'agent') {
                                return $assign_agent->select('department_id', 'agent_id')
                                    ->where('agent_id', $user->id)
                                    ->with(['agent' => function ($agent) {
                                        return $agent->select('id', 'user_name', 'email', 'first_name', 'last_name', 'profile_pic');
                                    }]);
                            }
                            return $assign_agent->select('department_id', 'agent_id');
                        }])
                    ;
                }, 'user' => function ($user) {
                    return $user->select('id', 'user_name', 'email', 'first_name', 'last_name', 'profile_pic');
                }, 'assigned' => function ($assigned) {
                    return $assigned->select('id', 'user_name', 'email', 'first_name', 'last_name', 'profile_pic');
                }, 'thread' => function ($thread) {
                    return $thread->whereNotNull('title')->where('is_internal', 0)->select('id', 'ticket_id', 'title');
                }, 'statuses' => function ($status) {
                    return $status->select('id', 'name');
                }, 'priority' => function ($priority) {
                    return $priority->select('priority_id', 'priority', 'priority_color');
                }, 'types' => function ($type) {
                    return $type->select('id', 'name');
                }, 'filter' => function ($filter) {
                    return $filter->select('ticket_id', 'key', 'value');
                }, 'sources' => function ($filter) {
                    return $filter->select('id', 'name');
                }])
                ->select('tickets.id', 'ticket_number', 'duedate', 'tickets.created_at', 'assigned_to', 'tickets.user_id', 'dept_id', 'status', 'priority_id', 'tickets.type', 'tickets.updated_at', \DB::raw('count(ticket_collaborator.id) as collaborator_count'), \DB::raw('count(ticket_attachment.id) as attachment_count'), \DB::raw('count(ticket_thread.id) as thread_count'), 'tickets.source', \DB::raw('substring_index(group_concat(if(`ticket_thread`.`is_internal` = 0, `ticket_thread`.`poster`,null)ORDER By ticket_thread.id desc) , ",", 1) as last_replier'))
                ->distinct()
                ->groupBy('tickets.id')
                ->paginate(10)
            ;
            return ['result' => $tickets];
        } catch (Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();
            return response()->json(compact('error'));
        }
    }

    /**
     * Get threads of a ticket.
     *
     * @return json
     */
    public function ticketThreads()
    {

        try {
            $v = Validator::make($this->request->all(), [
                'id' => 'required|int|exists:tickets,id',
            ]);
            if ($v->fails()) {
                return errorResponse($v->errors(), $responseCode = 401);
            }
            $id     = $this->request->input('id');
            $result = Auth::user()
                ->rightjoin('ticket_thread', 'ticket_thread.user_id', '=', 'users.id')
                ->select('ticket_thread.id', 'ticket_id', 'user_id', 'poster', 'source', 'title', 'body', 'is_internal', 'format', 'ip_address', 'ticket_thread.created_at', 'ticket_thread.updated_at', 'users.first_name', 'users.last_name', 'users.user_name', 'users.email', 'users.profile_pic')
                ->where('ticket_id', $id)
                ->orderBy('ticket_thread.id', 'desc')
                ->get();

            $threadIds        = array_column($result->toArray(), 'id');
            $filesByThreadIds = $this->getFilesByThreadIds($threadIds);

            foreach ($result as $element) {
                $key              = $element['id'];
                $element['files'] = array_key_exists($element['id'], $filesByThreadIds) ? $filesByThreadIds[$element['id']] : [];
            }

            $whereCondition = [['ticket_id', $id]];
            if(Auth::user()->role == 'user'){
                $whereCondition[] = ['is_internal', 0];
            }

            $threads = Ticket_Thread::select('id', 'user_id', 'title', 'body', 'created_at', 'updated_at', 'ticket_id', 'is_internal')->with(['user' => function ($q) {
                    $q->select('id', 'user_name', 'first_name', 'last_name', 'email', 'profile_pic');
                }, 'attach'])
            ->where($whereCondition)
            ->orderBy('id', 'desc')->get();
            if (!$threads->count()) {
                return errorResponse(Lang::get('lang.ticket_not_found'), $responseCode = 400);
            }

            foreach ($threads as $thread) {
                unset($thread['user_id']);
            }

            return successResponse('', ['threads' => $threads]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'), $responseCode = 400);
        } catch (\TokenExpiredException $e) {

            return errorResponse($e->getMessage(), $responseCode = 400);
        }
    }

    /**
     * Gets attachmnents and inline images as threadIds as key
     *
     * @return array
     */
    protected function getFilesByThreadIds($threadIds)
    {
        $ticketAttachments = new \App\Model\helpdesk\Ticket\Ticket_attachments;
        $threadFiles       = $ticketAttachments->whereIn('thread_id', $threadIds)->get();

        $filesForOneThread = [];
        $filesByThreadIds  = [];

        foreach ($threadFiles as $key => $threadFile) {

            //array filesByThreadIds[$threadFile->thread_id] doesn't exist intially.
            if (!array_key_exists($threadFile->thread_id, $filesByThreadIds)) {
                $filesByThreadIds[$threadFile->thread_id] = [];
            }

            array_push($filesForOneThread, $threadFile);

            //set attribute takes care of fetching from local
            array_push($filesByThreadIds[$threadFile->thread_id], $filesForOneThread);

            $filesForOneThread = [];
        }

        return $filesByThreadIds;
    }

    /**
     * Check the url is valid or not.
     *
     * @return json
     */
    public function checkUrl()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'url' => 'required|url',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }

            $url = $this->request->input('url');
            if (!str_is('*/', $url)) {
                $url = str_finish($url, '/');
            }

            $url    = $url . '/api/v1/helpdesk/check-url?api_key=' . $this->request->input('api_key') . '&token=' . \Config::get('app.token');
            $result = $this->CallGetApi($url);
            return response()->json(compact('result'));
        } catch (\Exception $ex) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Success for currect url.
     *
     * @return string
     */
    public function urlResult()
    {
        return 'success';
    }

    /**
     * Call curl function for Get Method.
     *
     * @param type $url
     *
     * @return type int|string|json
     */
    public function callGetApi($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'error:' . curl_error($curl);
        }

        return $response;
        curl_close($curl);
    }

    /**
     * Call curl function for POST Method.
     *
     * @param type $url
     * @param type $data
     *
     * @return type int|string|json
     */
    public function callPostApi($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'error:' . curl_error($curl);
        }

        return $response;
        curl_close($curl);
    }

    /**
     * To generate api string.
     *
     * @return type | json
     */
    public function generateApiKey()
    {
        try {
            $set = $this->setting->where('id', '1')->first();
            if ($set->api_enable == 1) {
                $key          = str_random(32);
                $set->api_key = $key;
                $set->save();
                $result = $set->api_key;

                return response()->json(compact('result'));
            } else {
                $result = 'please enable api';

                return response()->json(compact('result'));
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get help topics.
     *
     * @return json
     */
    public function getHelpTopic()
    {
        try {
            $result = $this->helptopic->get();

            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get Sla plans.
     *
     * @return json
     */
    public function getSlaPlan()
    {
        try {
            $result = $this->slaPlan->get();

            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get priorities.
     *
     * @return json
     */
    public function getPriority()
    {
        try {
            $result = $this->priority->get();

            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get departments.
     *
     * @return json
     */
    public function getDepartment()
    {
        try {
            $result = $this->department->get();

            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Getting the tickets.
     *
     * @return type json
     */
    public function getTickets()
    {
        try {
            $tickets = $this->model->orderBy('created_at', 'desc')->paginate(10);
            $tickets->toJson();

            return $tickets;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get helpdesk.
     *
     * @return json
     */
    public function gethelpdesk()
    {
        try {
            $result = $this->helpdesk->get();

            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Fetching the Inbox details.
     *
     * @return type json
     */
    public function inbox()
    {
        try {
            $user  = \JWTAuth::parseToken()->authenticate();
            $inbox = $this->user->join('tickets', function ($join) {
                $join->on('users.id', '=', 'tickets.user_id');
            })
                ->leftjoin('department', function ($join) {
                    $join->on('department.id', '=', 'tickets.dept_id');
                })
                ->join('ticket_status', 'ticket_status.id', '=', 'tickets.status')
                ->join('ticket_status_type', 'ticket_status.purpose_of_status', '=', 'ticket_status_type.id')
                ->leftJoin('ticket_priority', 'ticket_priority.priority_id', '=', 'tickets.priority_id')
                ->leftJoin('sla_plan', 'sla_plan.id', '=', 'tickets.sla')
                ->leftJoin('help_topic', 'help_topic.id', '=', 'tickets.help_topic_id')
                ->leftJoin('ticket_thread', function ($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id');
                })
                ->leftJoin('ticket_attachment', 'ticket_attachment.thread_id', '=', 'ticket_thread.id')
                ->where('ticket_status_type.name', 'open');
            if ($user->role == 'agent') {
                $id    = $user->id;
                $dept  = \DB::table('department_assign_agents')->where('agent_id', '=', $id)->pluck('department_id')->toArray();
                $inbox = $inbox->where(function ($query) use ($dept, $id) {
                    $query->whereIn('tickets.dept_id', $dept)
                        ->orWhere('assigned_to', '=', $id);
                });
            }
            $inbox = $inbox->select(\DB::raw('max(ticket_thread.updated_at) as updated_at'), 'user_name', 'first_name', 'last_name', 'email', 'profile_pic', 'ticket_number', 'tickets.id', \DB::raw('substring_index(group_concat(ticket_thread.title order by ticket_thread.id asc) , ",", 1) as title'), 'tickets.created_at', 'department.name as department_name', 'ticket_priority.priority as priotity_name', 'ticket_priority.priority_color as priority_color', 'sla_plan.name as sla_plan_name', 'help_topic.topic as help_topic_name', 'ticket_status.name as ticket_status_name', 'department.id as department_id', 'users.primary_dpt as user_dpt', \DB::raw('count(ticket_attachment.id) as attachment'), 'tickets.duedate as overdue_date')
                ->orderBy('updated_at', 'desc')
                ->groupby('tickets.id')
                ->distinct()
                ->paginate(10)
                ->toJson();
            return $inbox;
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $line  = $ex->getLine();
            $file  = $ex->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Create internal note.
     *
     * @return type json
     */
    public function internalNote()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'user_id'   => 'required|exists:users,id',
                'ticket_id' => 'required|exists:tickets,id',
                'body'      => 'required',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }
            $userid   = $this->request->input('user_id');
            $ticketid = $this->request->input('ticket_id');

            $body   = preg_replace('/[ ](?=[^>]*(?:<|$))/', '&nbsp;', nl2br($this->request->input('body')));
            $thread = $this->thread->create(['ticket_id' => $ticketid, 'user_id' => $userid, 'is_internal' => 1, 'body' => $body]);

            return response()->json(compact('thread'));
        } catch (\Exception $ex) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * Get the trashed tickets
     * @return json
     */
    public function getTrash()
    {
        try {
            $user  = \JWTAuth::parseToken()->authenticate();
            $trash = $this->user->join('tickets', function ($join) {
                $join->on('users.id', '=', 'tickets.user_id');
            })
                ->leftjoin('department', function ($join) {
                    $join->on('department.id', '=', 'tickets.dept_id');
                })
                ->join('ticket_status', 'ticket_status.id', '=', 'tickets.status')
                ->join('ticket_status_type', 'ticket_status.purpose_of_status', '=', 'ticket_status_type.id')
                ->where('ticket_status_type.name', 'deleted')
                ->leftJoin('ticket_priority', 'ticket_priority.priority_id', '=', 'tickets.priority_id')
                ->leftJoin('sla_plan', 'sla_plan.id', '=', 'tickets.sla')
                ->leftJoin('help_topic', 'help_topic.id', '=', 'tickets.help_topic_id')
                ->leftJoin('ticket_thread', function ($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id');
                })
                ->leftJoin('ticket_attachment', 'ticket_attachment.thread_id', '=', 'ticket_thread.id');
            if ($user->role == 'agent') {
                $id    = $user->id;
                $dept  = \DB::table('department_assign_agents')->where('agent_id', '=', $id)->pluck('department_id')->toArray();
                $trash = $trash->where(function ($query) use ($dept, $id) {
                    $query->whereIn('tickets.dept_id', $dept)
                        ->orWhere('assigned_to', '=', $id);
                });
            }
            $trash = $trash->select('ticket_priority.priority_color as priority_color', \DB::raw('substring_index(group_concat(ticket_thread.title order by ticket_thread.id asc) , ",", 1) as title'), 'tickets.duedate as overdue_date', \DB::raw('count(ticket_attachment.id) as attachment'), \DB::raw('max(ticket_thread.updated_at) as updated_at'), 'user_name', 'first_name', 'last_name', 'email', 'profile_pic', 'ticket_number', 'tickets.id', 'tickets.created_at', 'department.name as department_name', 'ticket_priority.priority as priotity_name', 'sla_plan.name as sla_plan_name', 'help_topic.topic as help_topic_name', 'ticket_status.name as ticket_status_name')
                ->orderBy('updated_at', 'desc')
                ->groupby('tickets.id')
                ->distinct()
                ->paginate(10)
                ->toJson();

            return $trash;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * get the logged in agent's ticket
     * @return json
     */
    public function getMyTicketsAgent()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }
            $id = $this->request->input('user_id');
            if ($this->user->where('id', $id)->first()->role == 'user') {
                $error = 'This user is not an Agent or Admin';

                return response()->json(compact('error'));
            }

            $result = $this->user->join('tickets', function ($join) use ($id) {
                $join->on('users.id', '=', 'tickets.assigned_to')
                    ->where('status', '=', 1);
            })
                ->join('users as client', 'tickets.user_id', '=', 'client.id')
                ->leftJoin('department', 'department.id', '=', 'tickets.dept_id')
                ->leftJoin('ticket_priority', 'ticket_priority.priority_id', '=', 'tickets.priority_id')
                ->leftJoin('sla_plan', 'sla_plan.id', '=', 'tickets.sla')
                ->leftJoin('help_topic', 'help_topic.id', '=', 'tickets.help_topic_id')
                ->leftJoin('ticket_status', 'ticket_status.id', '=', 'tickets.status')
                ->leftJoin('ticket_thread', function ($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id');
                })
                ->leftJoin('ticket_attachment', 'ticket_attachment.thread_id', '=', 'ticket_thread.id')
                ->where('users.id', $id)
                ->select(
                    'ticket_priority.priority_color as priority_color', \DB::raw('substring_index(group_concat(ticket_thread.title order by ticket_thread.id asc) , ",", 1) as title'), 'tickets.duedate as overdue_date', \DB::raw('count(ticket_attachment.id) as attachment'), \DB::raw('max(ticket_thread.updated_at) as updated_at'), 'client.user_name', 'client.first_name', 'client.last_name', 'client.email', 'client.profile_pic', 'ticket_number', 'tickets.id', 'tickets.created_at', 'department.name as department_name', 'ticket_priority.priority as priotity_name', 'sla_plan.name as sla_plan_name', 'help_topic.topic as help_topic_name', 'ticket_status.name as ticket_status_name'
                )
                ->orderBy('updated_at', 'desc')
                ->groupby('tickets.id')
                ->distinct()
                ->paginate(10)
                ->toJson();

            return $result;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * get logged user's tickets
     * @return json
     */
    public function getMyTicketsUser()
    {
        try {
            $v = Validator::make($this->request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }
            $id = $this->request->input('user_id');
            if (Auth::user()->where('id', $id)->first()->role == 'admin' || Auth::user()->where('id', $id)->first()->role == 'agent') {
                $error = 'This is not a client';

                return response()->json(compact('error'));
            }
            $user = User::where('users.id', $id)
                ->leftJoin('user_assign_organization', 'users.id', '=', 'user_assign_organization.user_id')
                ->leftJoin('organization', 'user_assign_organization.org_id', '=', 'organization.id')
                ->select(
                    'users.first_name', 'users.last_name', 'users.user_name', 'users.email', 'users.id', 'users.profile_pic', 'users.active', 'users.is_delete', 'users.phone_number', 'users.ext', 'users.country_code', 'users.mobile', 'organization.name as company'
                )->first()->toArray();

            $result = Auth::user()->join('tickets', function ($join) use ($id) {
                $join->on('users.id', '=', 'tickets.user_id')
                    ->where('user_id', '=', $id);
            })
                ->join('department', 'department.id', '=', 'tickets.dept_id')
                ->join('ticket_priority', 'ticket_priority.priority_id', '=', 'tickets.priority_id')
                ->join('sla_plan', 'sla_plan.id', '=', 'tickets.sla')
                ->join('help_topic', 'help_topic.id', '=', 'tickets.help_topic_id')
                ->join('ticket_status', 'ticket_status.id', '=', 'tickets.status')
                ->join('ticket_thread', function ($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id')
                        ->whereNotNull('title');
                })
                ->select('ticket_number', 'tickets.id', 'title', 'ticket_status.name as ticket_status_name')
                ->orderBy('ticket_thread.updated_at', 'desc')
                ->groupby('tickets.id')
                ->distinct()
                ->get()
                ->toArray()
            ;

            return response()->json(['tickets' => $result, 'requester' => $user]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * get info about ticket
     * @return json
     */
    public function getTicketById()
    {

        try {
            $v = Validator::make($this->request->all(), [
                'id' => 'required|exists:tickets,id',
            ]);
            if ($v->fails()) {
                return errorResponse($v->errors(), $responseCode = 400);
            }
            $id = $this->request->input('id');
            if (!$this->model->where('id', $id)->first()) {
                $error = 'There is no Ticket as ticket id: ' . $id;

                return response()->json(compact('error'));
            }
            $query = Auth::user()->join('tickets', function ($join) use ($id) {
                $join->on('users.id', '=', 'tickets.user_id')
                    ->where('tickets.id', '=', $id);
            });
            $satelliteModule = CommonSettings::where('option_name', 'satellite_helpdesk')->select('status')->first();
            $satellite_path  = base_path('app/SatelliteHelpdesk');
            $response        = $this->differenciateHelpTopic($query)
                ->leftJoin('ticket_thread', function ($join) {
                    $join->on('ticket_thread.ticket_id', '=', 'tickets.id')
                        ->where('is_internal', '!=', '1')
                        ->where(function ($query) {
                            return $query->whereNotNull('title')
                                ->orWhere('title', '=', '');
                        })
                    ;
                })
                ->leftJoin('users as assignee', 'tickets.assigned_to', '=', "assignee.id")
                ->leftJoin('department', 'tickets.dept_id', '=', "department.id")
                ->leftJoin('ticket_priority', 'tickets.priority_id', '=', 'ticket_priority.priority_id')
                ->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
                ->leftJoin('sla_plan', 'tickets.sla', '=', 'sla_plan.id')
                ->leftJoin('ticket_source', 'tickets.source', '=', 'ticket_source.id')
                ->leftJoin('help_topic', 'tickets.help_topic_id', '=', 'help_topic.id')
                ->leftJoin('ticket_type', 'tickets.type', '=', 'ticket_type.id');

            if ($satelliteModule && $satelliteModule->status == 1 && is_dir($satellite_path)) {
                $response = $response->leftJoin('satellite_helpdesk', 'tickets.domain_id', '=', 'satellite_helpdesk.id');
            }

            $result = $response->addSelect(
                'users.email', 'users.user_name', 'users.first_name', 'users.last_name', 'tickets.id', 'ticket_number', 'tickets.user_id', 'ticket_priority.priority_id', 'ticket_priority.priority as priority_name', 'priority_color', 'department.name as dept_name', 'ticket_status.name as status_name', 'sla_plan.name as sla_name', 'ticket_source.name as source_name', 'sla_plan.id as sla', 'ticket_status.id as status', 'lock_by', 'lock_at', 'ticket_source.id as source', 'isoverdue', 'reopened', 'isanswered', 'is_deleted', 'closed', 'reopened_at', 'duedate', 'closed_at', 'tickets.created_at', 'tickets.updated_at', 'help_topic.id as helptopic_id', 'help_topic.topic as helptopic_name', 'ticket_thread.title as title', 'ticket_type.id as type', 'ticket_type.name as type_name', 'assignee.id as assignee_id', 'assignee.first_name as assignee_first_name', 'assignee.last_name as assignee_last_name', 'assignee.user_name as assignee_user_name', 'assignee.email as assignee_email'
            )->first();

            if ($satelliteModule && $satelliteModule->status == 1 && is_dir($satellite_path)) {
                $response->addSelect('satellite_helpdesk.name as domain_name');
            }

            $result['from'] = ['id' => $result['user_id'], 'first_name' => $result['first_name'], 'last_name' => $result['last_name'], 'user_name' => $result['user_name'], 'email' => $result['email']];

            unset($result['user_id'], $result['first_name'], $result['last_name'], $result['email'], $result['isoverdue'], $result['sla'], $result['lock_by'], $result['lock_at'], $result['reopened_at'], $result['isanswered'], $result['is_deleted'], $result['sla_name'], $result['dept_id'], $result['user_name'], $result['priority_id'], $result['status'], $result['reopened'], $result['closed'], $result['helptopic_id'], $result['type']);

            if ($result['assignee_id'] == null) {
                $result['assignee'] = null;
            } else {
                $result['assignee'] = ['id' => $result['assignee_id'], 'first_name' => $result['assignee_first_name'], 'last_name' => $result['assignee_last_name'], 'user_name' => $result['assignee_user_name'], 'email' => $result['assignee_email']];
            }

            unset($result['assignee_id'], $result['assignee_first_name'], $result['assignee_last_name'], $result['assignee_user_name'], $result['assignee_email']);

            return successResponse('', ['ticket' => $result]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'), $responseCode = 400);
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();
            return errorResponse(compact('error'), $responseCode = 400);
        }
    }

    /**
     * create a the custom pagination
     * @param array $array
     * @param integer $perPage
     * @return LengthAwarePaginator
     */
    public function createPagination($array, $perPage)
    {
        try {
            //Get current page form url e.g. &page=6
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            //Create a new Laravel collection from the array data
            $collection = new Collection($array);

            //Slice the collection to get the items to display in current page
            $currentPageSearchResults = $collection->slice($currentPage * $perPage, $perPage)->all();

            //Create our paginator and pass it to the view
            $paginatedResults = new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);

            return $paginatedResults;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * get the users for collaborate
     * @return json
     */
    public function collaboratorSearch()
    {
        $this->validate($this->request, ['term' => 'required']);
        try {
            $term  = $this->request->input('term');
            $users = User::where(function ($query) use ($term) {
                $query->where('email', 'LIKE', '%' . $term . '%')
                    ->orWhere('first_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('user_name', 'LIKE', '%' . $term . '%');
            })
                ->where('active', 1)
                ->where('is_delete', 0)
                ->select('id', 'first_name', 'last_name', 'user_name', 'email', 'profile_pic', 'active', 'role', 'mobile', 'country_code')
                ->get();
            return response()->json(compact('users'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        }
    }

    /**
     * get the avatar of user
     * @param string $email
     * @return json
     * @throws Exception
     */
    public function avatarUrl($email)
    {
        try {
            $user = new User();
            $user = $user->where('email', $email)->first();
            if ($user->profile_pic) {
                $url = url('uploads/profilepic/' . $user->profile_pic);
            } else {
                $url = \Gravatar::src($email);
            }

            return $url;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * create a user in system
     * @return json
     */
    public function createUser()
    {


        $v = Validator::make($this->request->all(), [
            'first_name' => 'required|max:30|alpha',
            'last_name'  => 'required|max:20|alpha',
            'email'      => 'required|unique:users',
            'mobile'     => 'numeric',
            'code'       => 'numeric',
        ]);
        if ($v->fails()) {
            return errorResponse($v->errors());
        }

        if (!$this->request->has('first_name')) {
            return response()->json(['error' => ['first_name' => ['First name is required to register user.']]]);
        }
        if (!$this->request->has('last_name')) {
            return response()->json(['error' => ['last_name' => ['Last name is required to register user.']]]);
        }

        try {
            if (($this->request->has('phone') && $this->request->get('phone') != '') || ($this->request->has('mobile') && $this->request->get('mobile') != '')) {
                if (!$this->request->has('code') || $this->request->get('code') == '') {
                    return errorResponse(Lang::get('lang.the_code_feild_is_required'));
                }
            }
            $str     = str_random(8);
            $array   = ['password' => $str, 'password_confirmation' => $str, 'email' => $this->request->input('email')];
            $all     = $this->request->input();
            $merged  = $array + $all;
            $request = new \App\Http\Requests\helpdesk\RegisterRequest();
            $request->replace($merged);
            if ($request->has('username')) {
                $request->merge(['user_name' => $request->get('username')]);
            }

            // \Route::dispatch($request);
            $auth     = new \App\Http\Controllers\Auth\AuthController();
            $user     = new User();
            $register = $auth->postRegister($user, $request, true);
            if ($register) {
                return response()->json([$register]);
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        }
    }

    /**
     * add collaborate to ticket
     * @return json
     */
    public function addCollaboratorForTicket()
    {
        try {
            $v = Validator::make(Input::get(), [
                'user_id'   => 'required|exists:users,id',
                'ticket_id' => 'required',
            ]
            );
            if ($v->fails()) {
                $error = $v->messages();

                return response()->json(compact('error'));
            }
            $collaborator = \App\Model\helpdesk\Ticket\Ticket_Collaborator::updateOrCreate([
                'ticket_id' => Input::get('ticket_id'),
                'user_id'   => Input::get('user_id'),
            ], ['role' => 'ccc',
                'isactive' => 1]);

            return response()->json(compact('collaborator'));
        } catch (Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $ex) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        }
    }

    /**
     * get collaborator for ticket
     * @return json
     */
    public function getCollaboratorForTicket()
    {
        try {
            $v = \Validator::make(\Input::get(), [
                'ticket_id' => 'required',
            ]
            );
            if ($v->fails()) {
                $error = $v->messages();

                return response()->json(compact('error'));
            }
            $collaborator = $this->ticket->getCollaboratorForTicket();

            return response()->json(compact('collaborator'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $ex) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        }
    }

    /**
     * delete collaborator
     * @return json
     */
    public function deleteCollaborator()
    {
        try {
            $v = \Validator::make(\Input::get(), [
                'ticket_id' => 'required',
                'email'     => 'required',
            ]
            );
            if ($v->fails()) {
                $result = $v->messages();

                return response()->json(compact('result'));
            }
            $collaborator = $this->ticket->userremove();

            return response()->json(compact('collaborator'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();

            return response()->json(compact('error', 'file', 'line'));
        }
    }

    /**
     * get all dependencies for a ticket
     * @return json
     */
    public function dependency()
    {
        try {
            $user    = Auth::user();
            $tickets = \DB::table('tickets');
            if (Auth::user()->role == 'agent') {
                $id      = $user->id;
                $dept    = DepartmentAssignAgents::where('agent_id', '=', $id)->pluck('department_id')->toArray();
                $tickets = $tickets->whereIn('tickets.dept_id', $dept)->orWhere('assigned_to', '=', $user->id);
            }
            $department = $this->department->select('name', 'id')->get()->toArray();
            $sla        = $this->slaPlan->select('name', 'id')->get()->toArray();
            $staff      = Auth::user()->where('role', '!=', 'user')
                ->where('active', 1)
                ->where('is_delete', '!=', 1)
                ->select('email', 'id', 'first_name', 'last_name', 'user_name')
                ->get()->toArray();
            $team     = $this->team->select('name', 'id')->get()->toArray();
            $priority = \DB::table('ticket_priority')
                ->where('status', '=', 1)
                ->select('priority', 'priority_id')->get();
            $helptopic = $this->helptopic
                ->where('status', '=', 1)
                ->select('topic', 'id')->get()->toArray();
            if (Auth::user()->role == 'admin') {
                $status = Ticket_Status::where('purpose_of_status', '!=', 3)->where('purpose_of_status', '!=', 5)->get();
            } elseif (Auth::user()->role == 'agent') {
                $status = Ticket_Status::where('purpose_of_status', '!=', 3)->get();
            } else {
                $status = Ticket_Status::where('visibility_for_client', 1)->where('allow_client', 1)->wherein('purpose_of_status', [1, 2, 4])->get();
            }
            $source = \DB::table('ticket_source')->select('name', 'id')->get();
            $type   = \DB::table('ticket_type')
                ->where('status', '=', 1)
                ->select('name', 'id')->get();

            $satelliteModule = CommonSettings::where('option_name', 'satellite_helpdesk')->select('status')->first();
            $satellite_path  = base_path('app/SatelliteHelpdesk');

            if ($satelliteModule && $satelliteModule->status == 1 && is_dir($satellite_path)) {
                $helpdesk = \DB::table('satellite_helpdesk')
                    ->where('status', '=', 1)
                    ->select('name', 'id')->get();
            } else {
                $helpdesk = [];
            }

            $statuses = collect($tickets
                    ->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
                    ->leftJoin('ticket_status_type', 'ticket_status_type.id', '=', 'ticket_status.purpose_of_status')
                    ->select('ticket_status_type.name as status', \DB::raw('COUNT(tickets.id) as count'))
                    ->groupBy('ticket_status.purpose_of_status')
                    ->get())->transform(function ($item) {
                return ['name' => ucfirst($item->status), 'count' => $item->count];
            });
            $unassigned = Auth::user()->leftJoin('tickets', function ($join) {
                $join->on('users.id', '=', 'tickets.user_id')
                    ->whereNull('assigned_to')->where('status', '=', 1);
            })
                ->where(function ($query) use ($user) {
                    if ($user->role != 'admin') {
                        $query->where('tickets.dept_id', '=', $user->primary_dpt);
                    }
                })
                ->select(\DB::raw('COUNT(tickets.id) as unassined'))
                ->value('unassined');
            $mytickets = Auth::user()->leftJoin('tickets', function ($join) use ($user) {
                $join->on('users.id', '=', 'tickets.assigned_to')
                    ->where('tickets.assigned_to', '=', $user->id)->where('status', '=', 1);
            })
                ->where(function ($query) use ($user) {
                    if ($user->role != 'admin') {
                        $query->where('tickets.dept_id', '=', $user->primary_dpt)->orWhere('assigned_to', '=', $user->id);
                    }
                })
                ->select(\DB::raw('COUNT(tickets.id) as my_ticket'))
                ->value('my_ticket');
            $depend     = collect([['name' => 'unassigned', 'count' => $unassigned], ['name' => 'mytickets', 'count' => $mytickets]]);
            $collection = $statuses->merge($depend);
            $result     = ['departments' => $department, 'sla'      => $sla, 'staffs'       => $staff, 'teams'    => $team,
                'priorities'                 => $priority, 'helptopics' => $helptopic, 'status' => $status, 'sources' => $source,
                'tickets_count'              => $collection, 'type'     => $type, 'helpdesk'    => $helpdesk];
            return successResponse('', $result);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'), $responseCode = 400);

        }
    }

    /**
     * get the default value
     * @param string $query
     * @return json
     */
    public function differenciateHelpTopic($query)
    {
        $ticket = $query->first();
        $check  = 'department';
        if ($ticket) {
            if ($ticket->dept_id && $ticket->help_topic_id) {
                return $this->getSystem($check, $query);
            }
            if (!$ticket->dept_id && $ticket->help_topic_id) {
                return $query->select('tickets.help_topic_id');
            }
            if ($ticket->dept_id && !$ticket->help_topic_id) {
                return $query->select('tickets.dept_id');
            }
        }
        return $query;
    }

    /**
     * get system default values
     * @param string $check
     * @param string $query
     * @return type
     */
    public function getSystem($check, $query)
    {
        switch ($check) {
            case 'department':
                return $query->select('tickets.dept_id');
            case 'helpTopic':
                return $query->select('tickets.help_topic_id');
            default:
                return $query->select('tickets.dept_id');
        }
    }

    /**
     * Register a user with username and password.
     *
     * @param Request $request
     *
     * @return type json
     */
    public function register(Request $request)
    {
        try {
            $v = \Validator::make($request->all(), [
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }
            $auth     = $this->user;
            $email    = $request->input('email');
            $username = $request->input('email');
            $password = \Hash::make($request->input('password'));
            $role     = $request->input('role');
            if ($auth->role == 'agent') {
                $role = "user";
            }
            $user            = new User();
            $user->password  = $password;
            $user->user_name = $username;
            $user->email     = $email;
            $user->role      = $role;
            $user->save();
            return response()->json(compact('user'));
        } catch (\Exception $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }
    }

    public function notification()
    {
        try {
            $requester_id = Auth::user()->id;
            $notification = new \App\Http\Controllers\Agent\helpdesk\Notifications\Notification();
            return $notification->appNotification($requester_id);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(compact('error'));
        }
    }

    /**
     * update notification to seen
     * @return json
     */
    public function notificationSeen()
    {
        $this->validate($this->request, [
            'notification_id' => 'required',
        ]);
        try {
            $requester_id = Auth::user()->id;
            $notification = new \App\Http\Controllers\Agent\helpdesk\Notifications\Notification();
            $update       = $notification->notificationSeen($requester_id, $this->request);
            if ($update == 1) {
                $result = ['status' => 'success', 'message' => 'Successfully Updated'];
            } else {
                $result = ['status' => 'fails', 'message' => 'Sorry We can\'t Update'];
            }
            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(compact('error'));
        }
    }

    /**
     * get all organisation
     * @return json
     */
    public function organizations()
    {
        try {
            $result = \App\Model\helpdesk\Agent_panel\Organization::select('id', 'name', 'website', 'address', 'phone')
                ->paginate(10)
            ;
            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(compact('error'));
        }
    }

    /**
     * search an organization
     * @return json
     */
    public function organizationSearch()
    {
        $this->validate($this->request, [
            'term' => 'required',
        ]);
        try {
            $term   = $this->request->input('term');
            $result = \App\Model\helpdesk\Agent_panel\Organization::
                select('id', 'name', 'website', 'address', 'phone')
                ->where(function ($query) use ($term) {
                    $query->where('name', 'LIKE', '%' . $term . '%')
                        ->orWhere('website', 'LIKE', '%' . $term . '%')
                        ->orWhere('address', 'LIKE', '%' . $term . '%')
                        ->orWhere('phone', 'LIKE', '%' . $term . '%');
                })
                ->paginate(10)
            ;
            return response()->json(compact('result'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(compact('error'));
        }
    }

    /**
     * get all organization ticket
     * @return json
     */
    public function organizationTickets()
    {
        $this->validate($this->request, [
            'id' => 'required|exists:organization,id',
        ]);
        try {
            $tickets      = null;
            $id           = $this->request->input('id');
            $organization = \App\Model\helpdesk\Agent_panel\Organization::whereId($id)
                ->with([
                    'userRelation' => function ($relation) use (&$tickets, $id) {
                        $relation->select('org_id', 'user_id')
                            ->with([
                                'tickets' => function ($ticket) use (&$tickets, $id) {
                                    $ticket->select('id', 'user_id', 'created_at', 'duedate', \DB::raw('created_at as created_utc'))
                                        ->with(['thread' => function ($thread) {
                                            $thread->select('title', 'id', 'ticket_id')
                                                ->whereNotNull('title')
                                                ->where('is_internal', 0);
                                        }])
                                        ->orderBy('created_at', 'DESC');
                                    $tickets = $ticket->paginate(10)->appends(['id' => $id]);
                                },
                            ]);
                    },
                ])
                ->get();
            return response()->json(compact('tickets'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(compact('error'));
        }
    }

    /**
     * get all organization users
     * @return json
     */
    public function organizationUsers()
    {
        $this->validate($this->request, [
            'id' => 'required|exists:organization,id',
        ]);
        try {
            $users        = null;
            $id           = $this->request->input('id');
            $organization = \App\Model\helpdesk\Agent_panel\Organization::whereId($id)
                ->with([
                    'userRelation' => function ($relation) use (&$users, $id) {
                        $relation->select('org_id', 'user_id')
                            ->with(['user' => function ($user) use (&$users, $id) {
                                $users = $user->select('id', 'first_name', 'last_name', 'user_name', 'email', 'profile_pic')
                                    ->orderBy('created_at', 'DESC');
                                $users = $users->paginate(10)->appends(['id' => $id]);
                            }]);
                    },
                ])
                ->get();
            return response()->json(compact('users'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(compact('error'));
        }
    }

    /**
     * filter tickets
     * @param Request $request
     * @return json
     */
    public function getTicketsAPI(Request $request)
    {

        try {
            $user  = \JWTAuth::parseToken()->authenticate();
            $input = [];
            if ($request->has('api') && $request->has('show') && $request->has('departments')) {
                if ($request->get('api') != '' || $request->get('show') != '' || $request->get('departments') != '') {
                    if ($request->get('api') == '1') {
                        $tickets = new \App\Http\Controllers\Agent\helpdesk\Filter\FilterController($request);
                        $result  = $tickets->getFilter($request);
                        if ($request->has('sort-by') && in_array($request->get('sort-by'), ['id', 'ticket_title', 'updated_at', 'created_at', 'due', 'ticket_number', 'priority'])) {
                            if ($request->has('order') && in_array($request->get('order'), ['asc', 'ASC', 'desc', 'DESC'])) {
                                $result = $result->orderBy($request->get('sort-by'), $request->get('order'));
                            } else {
                                $result = $result->orderBy($request->get('sort-by'), 'ASC');
                            }
                        } else {
                            $result = $result->orderBy('updated_at', 'DESC');
                        }

                        $fetch = 10;
                        if ($request->has('records_per_page') && (int) $request->get('records_per_page') != 0 && ($request->get('records_per_page') >= 0 && $request->get('records_per_page') < 500)) {
                            $fetch = $request->get('records_per_page');
                        } elseif ($request->has('records_per_page') && $request->get('records_per_page') > 500) {
                            $fetch = 500;
                        }
                        if ($request->has('created') ||
                            $request->has('updated') ||
                            $request->has('due-on') ||
                            $request->has('last-response-by')) {
                            $result = $result->simplePaginate($fetch);
                        } else {
                            $result = $result->paginate($fetch);
                        }

                        foreach ($result as $value) {

                            if ($value->profile_pic == '') {
                                $value->profile_pic = \Gravatar::src($value->c_email);
                            } else {
                                $value->profile_pic = url('/') . '/uploads/profilepic/' . $value->profile_pic;
                            }

                            $value['from'] = ['id' => $value['c_uid'], 'first_name' => $value['c_fname'], 'last_name' => $value['c_lname'], 'user_name' => $value['c_uname'], 'email' => $value['c_email'], 'profile_pic' => $value['profile_pic']];

                            $value['assignee'] = ['id' => $value['a_uid'], 'first_name' => $value['a_fname'], 'last_name' => $value['a_lname'], 'user_name' => $value['a_uname'], 'email' => $value['a_email']];
                            $value['priority'] = ['name' => $value['priority'], 'color' => $value['color']];

                            $value['thread_count'] = $value['countthread'];
                            $value['status']       = $value['ticket_status_name'];
                            $value['title']        = $value['ticket_title'];
                            $value['department']   = ['id' => $value['dept_id'], 'name' => $value['dept_name']];
                            unset($value['c_uid'], $value['c_fname'], $value['c_lname'], $value['c_uname'], $value['c_email'], $value['a_uid'], $value['a_fname'], $value['a_lname'], $value['a_uname'], $value['a_email']);
                            unset($value['assigned_to'], $value['profile_pic'], $value['created_at2'], $value['updated_at2'], $value['dept_id'], $value['css'], $value['name'], $value['color'], $value['due'], $value['countattachment'], $value['countthread'], $value['ticket_status_name'], $value['ticket_title'], $value['dept_id'], $value['dept_name']);
                        }

                        return successResponse('', $result);
                    } else {
                        $error = Lang::get('lang.invalid_value_for_api_in_parameters');
                    }
                } else {
                    $error = Lang::get('lang.required_parameters_can_not_be_empty');
                }
            } else {
                $error = Lang::get('lang.missing_requre_parameters');
            }
            return errorResponse($error, $responseCode = 400);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $line  = $ex->getLine();
            $file  = $ex->getFile();
            return errorResponse(compact('error', 'file', 'line'), $responseCode = 400);
        } catch (\TokenExpiredException $e) {
            return errorResponse($e->getMessage(), $responseCode = 400);
        }
    }

    /**
     * to change the status
     * @return json
     */
    public function statusChange()
    {
        $v = \Validator::make($this->request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'status_id' => 'required|exists:ticket_status,id',
        ]);
        if ($v->fails()) {
            return errorResponse($v->errors());
        }
        try {
            $user_id   = Auth::user()->id;
            $ticket_id = $this->request->ticket_id;
            $status_id = $this->request->status_id;
            $core      = new CoreTicketController();
            $status    = $core->changeStatus($ticket_id, $status_id, $user_id);
            if (is_string($status)) {
                $response_status = 200;
                $response        = ['message' => 'Status changed to ' . $status];
            } else {
                $formatted_response = $this->formatResponseFromJsonResponse($status);
                $response           = $formatted_response[0];
                $response_status    = $formatted_response[1];
            }
            return successResponse($response["message"]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();
            return errorResponse(compact('error', 'file', 'line'));
        }
    }

    /**
     * @category function to format response to send api call received in jsonfromat from core controllers
     * @param Json Object
     * @var $result
     * @return array $result with message and status
     */
    public function formatResponseFromJsonResponse($response)
    {
        $result = [];
        if (property_exists(json_decode($response->content()), 'message')) {
            $result[0] = ['message' => json_decode($response->content())->message];
        } else {
            $result[0] = ['message' => json_decode($response->content())->error];
        }
        $result[1] = $response->status();
        return $result;
    }

    public function merge(Request $request)
    {
        $this->validate($request, [
            'p_id' => 'required|exists:tickets,id',
            't_id' => 'required|array',
        ]);
        try {
            $id = "";
            if (!$request->filled('data1')) {
                $merge_id = array_merge($request->input('t_id'), [$request->input('p_id')]);
                $request->replace([
                    'p_id'   => $request->input('p_id'),
                    't_id'   => $request->input('t_id'),
                    'data1'  => $merge_id,
                    'title'  => $request->input('title'),
                    'reason' => $request->input('reason'),
                ]);
                return \Route::dispatch($request);
            }
            $check = $this->ticketController()->checkMergeTickets($id);
            if ($check == 2) {
                throw new \Exception('tickets from different users');
            }
            $result  = $this->ticketController()->mergeTickets($id);

            $message = ['message' => 'can not merge'];
            if ($result == 1) {
                $response = ['message' => 'merged successfully'];
                return successResponse($response);
            }
             // dd($result);
        } catch (\Exception $e) {
            $message = ['message' => $e->getMessage()];
            return errorResponse($message);
        }
        $response = ['message' => $message];
        return successResponse($response);

    }

    public function ticketController()
    {
        return new CoreTicketController();
    }

    /**
     * Create Tickets.
     *
     * @method POST
     *
     * @param user_id,subject,body,helptopic,sla,priority,dept
     *
     * @return json
     */
    public function createSatelliteTicket(\App\Model\helpdesk\Utility\CountryCode $code, Request $request)
    {

        $v = \Validator::make($this->request->all(), [
            'subject'    => 'required',
            'priority'   => 'required|exists:ticket_priority,priority_id',
            'body'       => 'required',
            'help_topic' => 'required|exists:help_topic,id',
            'dept_id'    => 'exists:department,id',
            'sla'        => 'exists:sla_plan,id',
            'email'      => 'email',
            'first_name' => 'required',
            'assigned'   => 'numeric|exists:users,id',
            'department' => 'exists:department,id',
        ]);

        if ($v->fails()) {
            return errorResponse($v->errors());
        }

        $defaultValues = [
            'subject', 'priority', 'help_topic', 'body',
            'email', 'first_name', 'sla',
            'media_attachment', 'domain_id', 'token', 'api_key', 'ip', 'department', 'assigned'];

        $formData = $request->except($defaultValues);

        if (count($formData) > 0) {
            return errorResponse(Lang::get('lang.please_remove_unwanted_field'));
        }
        if ($request->assigned) {
            $user = User::where('id', $request->assigned)->select('role')->first();

            if ($user && $user->role == 'user') {
                return errorResponse(Lang::get('lang.please_select_assigner_as_admin_or_agent'));
            }

            $assigntoAgent = User::where('id', $request->assigned)->where('is_delete', '!=', 1)->where('active', '=', 1)->first();
            if (!$assigntoAgent) {
                return errorResponse(Lang::get('lang.selected_assigner_not_active'));
            }
        }

        if (($this->request->has('phone') && $this->request->get('phone') != '') || ($this->request->has('mobile') && $this->request->get('mobile') != '')) {
            if (!$this->request->has('code') || $this->request->get('code') == '') {
                return errorResponse(Lang::get('lang.the_code_feild_is_required'));
            }
        }
        if ($this->request->has('assigned') && !is_numeric($this->request->get('assigned'))) {
            $error = "The assigned feild must me an integer value.";
            return errorResponse($error, $responseCode = 400);
        } elseif ($this->request->has('assigned') && is_numeric($this->request->get('assigned'))) {

            if (!User::has('assign_ticket')) {
                $error = Lang::get('lang.permission_denied');
                return errorResponse($error);
            }
        }
        if (!$this->request->has('email')) {
            return errorResponse(Lang::get('lang.username_or_email_is_requred_to_create_ticket'));
        }
        
        if (Auth::user() && Auth::user()->role == 'agent' && !$ticket_policy->create()) {
            $error = Lang::get('lang.permission_denied');
            return errorResponse($error);
        }
        try {
            $PhpMailController      = new \App\Http\Controllers\Common\PhpMailController();
            $NotificationController = new \App\Http\Controllers\Common\NotificationController();
            $core                   = new CoreTicketController($PhpMailController, $NotificationController);
            $request                = new TicketRequest();
            $this->request->merge(['body' => preg_replace('/[ ](?=[^>]*(?:<|$))/', '&nbsp;', nl2br($this->request->get('body')))]);
            if ($this->request->has('email')) {
                $user_id = User::select('id')->where('email', '=', $this->request->get('email'))->first();
                if (!$user_id) {
                    return errorResponse(Lang::get('lang.user_not_found_with_this_email'), $responseCode = 400);
                }
                $this->request->request->add(['requester' => $user_id->id]);
            }
            $request->replace($this->request->except('token', 'api_key', 'ip'));
            $response = $core->post_newticket($request, $code, true);

            $response_status = 200;
            if (!is_array($response)) {
                $formatted_response = $this->formatResponseFromJsonResponse($response);
                $response           = $formatted_response[0];
                $response_status    = $formatted_response[1];
            }
            $message = $response["message"];
            unset($response["message"]);

            $result          = $this->ticketDetailsWithThread($response["ticket_id"]);
            $result['body']  = $result['thread'][0]['body'];
            $result['files'] = $result['thread'][0]['files'];
            unset($result['thread']);
            return successResponse($message, ['ticket' => $result]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'), $responseCode = 400);
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();
            return response()->json(compact('error'))
                ->header('Authenticate: xBasic realm', 'fake');
        }
    }

    /**
     * get info about ticket
     * @return json
     */
    public function getsatelliteTicketById()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'id' => 'required|exists:tickets,id',
            ]);
            if ($v->fails()) {
                return errorResponse($v->errors());
            }
            $id     = $this->request->input('id');
            $result = $this->ticketDetailsWithThread($id);

            return successResponse('', ['ticket' => $result]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return errorResponse(compact('error', 'file', 'line'));
        } catch (\TokenExpiredException $e) {
            $error = $e->getMessage();
            return errorResponse(compact('error'));
        }
    }

    /**
     * create a user in system
     * @return json
     */
    public function createSatelliteUser(Request $request)
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'first_name' => 'required|max:30|alpha',
                'last_name'  => 'required|max:20|alpha',
                'email'      => 'required|unique:users',
                'password'   => 'required|min:8',
                'mobile'     => 'numeric',
                'code'       => 'numeric',
            ]);
            if ($v->fails()) {
                return errorResponse($v->errors());
            }
            if (!$this->request->has('first_name')) {
                return response()->json(['error' => ['first_name' => ['First name is required to register user.']]]);
            }
            if (!$this->request->has('last_name')) {
                return response()->json(['error' => ['last_name' => ['Last name is required to register user.']]]);
            }

            if (($this->request->has('phone') && $this->request->get('phone') != '') || ($this->request->has('mobile') && $this->request->get('mobile') != '')) {
                if (!$this->request->has('code') || $this->request->get('code') == '') {
                    return errorResponse(Lang::get('lang.the_code_feild_is_required'));
                }
            }

            $array   = ['password' => $this->request->input('password'), 'password_confirmation' => $this->request->input('password'), 'email' => $this->request->input('email')];
            $all     = $this->request->input();
            $merged  = $array + $all;
            $request = new \App\Http\Requests\helpdesk\RegisterRequest();
            $request->replace($merged);
            if ($request->has('username')) {
                $request->merge(['user_name' => $request->get('username')]);
            }
            \Route::dispatch($request);
            $auth     = new \App\Http\Controllers\Auth\AuthController();
            $user     = new User();
            $register = $auth->postRegister($user, $request, true);
            $update   = User::where('id', $user->id)->update(['active' => 1, 'email_verify' => 1]);

            if ($register) {
                unset($register["user"]['email_verify'], $register["user"]['mobile_verify'], $register["user"]['agent_tzone']);
                return successResponse($register["message"], $register["user"]);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return response()->json(compact('error', 'file', 'line'));
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return response()->json(compact('error', 'file', 'line'));
        }
    }

    /**
     * filter tickets
     * @param Request $request
     * @return json
     */
    public function getTicketsAPIfilter(Request $request)
    {

        try {

            $user  = \JWTAuth::parseToken()->authenticate();
            $input = [];
            if ($request->has('api') && $request->has('show') && $request->has('departments')) {
                if ($request->get('api') != '' || $request->get('show') != '' || $request->get('departments') != '') {
                    if ($request->get('api') == '1') {
                        $tickets = new \App\Http\Controllers\Agent\helpdesk\Filter\FilterController($request);
                        $result  = $tickets->getFilter($request);
                        if ($request->has('sort-by') && in_array($request->get('sort-by'), ['id', 'ticket_title', 'updated_at', 'created_at', 'due', 'ticket_number', 'priority'])) {
                            if ($request->has('order') && in_array($request->get('order'), ['asc', 'ASC', 'desc', 'DESC'])) {
                                $result = $result->orderBy($request->get('sort-by'), $request->get('order'));
                            } else {
                                $result = $result->orderBy($request->get('sort-by'), 'ASC');
                            }
                        } else {
                            $result = $result->orderBy('updated_at', 'DESC');
                        }
                        $fetch = 10;
                        if ($request->has('records_per_page') && (int) $request->get('records_per_page') != 0 && ($request->get('records_per_page') >= 0 && $request->get('records_per_page') < 500)) {
                            $fetch = $request->get('records_per_page');
                        } elseif ($request->has('records_per_page') && $request->get('records_per_page') > 500) {
                            $fetch = 500;
                        }
                        if ($request->has('created') ||
                            $request->has('updated') ||
                            $request->has('due-on') ||
                            $request->has('last-response-by')) {
                            $result = $result->simplePaginate($fetch);
                        } else {
                            $result = $result->paginate($fetch)->toJson();
                        }

                        $result = json_decode($result);
                        foreach ($result->data as $value) {
                            if ($value->profile_pic == '') {
                                $value->profile_pic = \Gravatar::src($value->c_email);
                            } else {
                                $value->profile_pic = url('/') . '/uploads/profilepic/' . $value->profile_pic;
                            }
                        }

                        return $result;
                    } else {
                        $error = Lang::get('lang.invalid_value_for_api_in_parameters');
                    }
                } else {
                    $error = Lang::get('lang.required_parameters_can_not_be_empty');
                }
            } else {
                $error = Lang::get('lang.missing_requre_parameters');
            }
            return errorResponse($error, $responseCode = 400);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $line  = $ex->getLine();
            $file  = $ex->getFile();
            return errorResponse(compact('error', 'file', 'line'), $responseCode = 400);
        } catch (\TokenExpiredException $e) {
            return errorResponse($e->getMessage(), $responseCode = 400);
        }
    }

    /**
     *
     * @param type $id
     * @return type array
     * Return ticket details with thread by Id
     */
    public function ticketDetailsWithThread($id)
    {

        try {
            $query = Auth::user()->join('tickets', function ($join) use ($id) {
                $join->on('users.id', '=', 'tickets.user_id')
                    ->where('tickets.id', '=', $id);
            });

            $response = $this->differenciateHelpTopic($query)
                ->leftJoin('ticket_thread', function ($join) {
                    $join->on('ticket_thread.ticket_id', '=', 'tickets.id')
                        ->where('is_internal', '!=', '1')
                        ->where(function ($query) {
                            return $query->whereNotNull('title')
                                ->orWhere('title', '=', '');
                        })
                    ;
                })
                ->leftJoin('users as assignee', 'tickets.assigned_to', '=', "assignee.id")
                ->leftJoin('department', 'tickets.dept_id', '=', "department.id")
                ->leftJoin('ticket_priority', 'tickets.priority_id', '=', 'ticket_priority.priority_id')
                ->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
                ->leftJoin('sla_plan', 'tickets.sla', '=', 'sla_plan.id')
                ->leftJoin('ticket_source', 'tickets.source', '=', 'ticket_source.id')
                ->leftJoin('help_topic', 'tickets.help_topic_id', '=', 'help_topic.id')
                ->leftJoin('ticket_type', 'tickets.type', '=', 'ticket_type.id');
            $satelliteModule = CommonSettings::where('option_name', 'satellite_helpdesk')->select('status')->first();
            $satellite_path  = base_path('app/SatelliteHelpdesk');
            if ($satelliteModule && $satelliteModule->status == 1 && is_dir($satellite_path)) {
                $response = $response->leftJoin('satellite_helpdesk', 'tickets.domain_id', '=', 'satellite_helpdesk.id');
            }

            $thread = Auth::user()
                ->rightjoin('ticket_thread', 'ticket_thread.user_id', '=', 'users.id')
                ->select('ticket_thread.id', 'ticket_id', 'user_id', 'poster', 'source', 'title', 'body', 'is_internal', 'format', 'ip_address', 'ticket_thread.created_at', 'ticket_thread.updated_at', 'users.first_name', 'users.last_name', 'users.user_name', 'users.email', 'users.profile_pic')
                ->where('ticket_id', $id)
                ->orderBy('ticket_thread.id', 'desc')
                ->get();

            $threadIds        = array_column($thread->toArray(), 'id');
            $filesByThreadIds = $this->getFilesByThreadIds($threadIds);

            foreach ($thread as $element) {
                $key              = $element['id'];
                $element['files'] = array_key_exists($element['id'], $filesByThreadIds) ? $filesByThreadIds[$element['id']] : [];
            }
            $result = $response->addSelect(
                'users.email', 'users.user_name', 'users.first_name', 'users.last_name', 'tickets.id', 'ticket_number', 'tickets.user_id', 'ticket_priority.priority_id', 'ticket_priority.priority as priority_name', 'department.name as dept_name', 'ticket_status.name as status_name', 'sla_plan.name as sla_name', 'ticket_source.name as source_name', 'sla_plan.id as sla', 'ticket_status.id as status', 'lock_by', 'lock_at', 'ticket_source.id as source', 'isoverdue', 'reopened', 'isanswered', 'is_deleted', 'closed', 'reopened_at', 'duedate', 'closed_at', 'tickets.created_at', 'tickets.updated_at', 'help_topic.id as helptopic_id', 'help_topic.topic as helptopic_name', 'ticket_thread.title as title', 'ticket_type.id as type', 'ticket_type.name as type_name', 'assignee.id as assignee_id', 'assignee.first_name as assignee_first_name', 'assignee.last_name as assignee_last_name', 'assignee.user_name as assignee_user_name', 'assignee.email as assignee_email'
            )->first();

            if ($satelliteModule && $satelliteModule->status == 1 && is_dir($satellite_path)) {
                $response->addSelect('satellite_helpdesk.name as domain_name');
            }
            $result['from'] = ['id' => $result['user_id'], 'first_name' => $result['first_name'], 'last_name' => $result['last_name'], 'user_name' => $result['user_name'], 'email' => $result['email']];

            unset($result['user_id'], $result['first_name'], $result['last_name'], $result['email'], $result['isoverdue'], $result['sla'], $result['lock_by'], $result['lock_at'], $result['reopened_at'], $result['isanswered'], $result['is_deleted'], $result['sla_name'], $result['dept_id'], $result['user_name'], $result['priority_id'], $result['status'], $result['source'], $result['reopened'], $result['closed'], $result['helptopic_id'], $result['type']);

            if ($result['assignee_id'] == null) {
                $result['assignee'] = null;
            } else {
                $result['assignee'] = ['id' => $result['assignee_id'], 'first_name' => $result['assignee_first_name'], 'last_name' => $result['assignee_last_name'], 'user_name' => $result['assignee_user_name'], 'email' => $result['assignee_email']];
            }

            unset($result['assignee_id'], $result['assignee_first_name'], $result['assignee_last_name'], $result['assignee_user_name'], $result['assignee_email']);
            $result['thread'] = $thread;
            return $result;
        } catch (\TokenExpiredException $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Reply for the ticket and ticket details.
     *
     * @param TicketRequest $request
     *
     * @return json
     */
    public function ticketReplyWithDetails()
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'ticket_id'     => 'required|exists:tickets,id',
                'reply_content' => 'required',
            ]);
            if ($v->fails()) {
                return errorResponse($v->errors());
            }
            $checkAuthUser = Tickets::where('id',$this->request['ticket_id'])->value('user_id');

            if($checkAuthUser != Auth::user()->id){
                return errorResponse(Lang::get('lang.wrong_ticket_id'));
            }
            $attachments = $this->request['media_attachment'];
            $inlines     = $this->request['inline'];
            $this->request->merge(['reply_content' => preg_replace('/[ ](?=[^>]*(?:<|$))/', '&nbsp;', nl2br($this->request->get('reply_content')))]);
            $result = $this->ticket->reply($this->thread, $this->request, 'client', $attachments, $inlines);
            $thread = $this->getLastThread($this->request['ticket_id']);
            $ticket = $result->join('users', 'ticket_thread.user_id', '=', 'users.id')
                ->select('ticket_thread.*', 'users.first_name as first_name')
                ->orderBy('ticket_thread.id', 'desc')->first();
            return successResponse(Lang::get('lang.successfully_replied'), $thread);
        } catch (\TokenExpiredException $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * this method return lase response of the ticket
     * @param type $ticketId
     * @return type array
     */
    private function getLastThread($ticketId)
    {
        $result = Ticket_Thread::where('ticket_id', $ticketId)->where('is_internal', '!=', '1')->orderBy('id', 'DESC')->first();
        unset($result['thread_type'], $result['source'], $result['reply_rating'], $result['rating_count'], $result['is_internal'], $result['title'], $result['format'], $result['ip_address'], $result['response_time']);
        return $result;
    }

    /**
     * this method return ticket list of auth user
     * @return type array
     */
    public function userTicketList(Request $request)
    {

        if (Auth::user()->role != 'user') {
            return errorResponse(Lang::get('lang.user_not_found'));
        }

        $userId = Auth::user()->id;

        // Allowed status
        $allowedStatus = ['open', 'closed'];

        // Filter by status
        if ($request->has('status')) {
            if (in_array($request->status, $allowedStatus)) {
                $statusType[] = $request->status;
            } else {
                return errorResponse('invalid status requested');
            }
        } else {
            $statusType = $allowedStatus;
        }

        $tickets_status = Ticket_Status::where('allow_client', 1)
            ->whereHas('type', function ($query) use ($statusType) {
                $query->whereIn('name', $statusType);
            })->pluck('id')->toArray();

        $ticketId = Tickets::where('user_id', $userId)
            ->whereIn('status', $tickets_status)
            ->latest('updated_at')
            ->pluck('id')->toArray();

        if (count($ticketId) == 0) {

            return successResponse('', ['ticket' => Lang::get('lang.tickets_not_available')]);
        }

        $satelliteModule = CommonSettings::where('option_name', 'satellite_helpdesk')->select('status')->first();
        $satellite_path  = base_path('app/SatelliteHelpdesk');

        foreach ($ticketId as $id) {

            $query = Auth::user()->join('tickets', function ($join) use ($id) {
                $join->on('users.id', '=', 'tickets.user_id')
                    ->where('tickets.id', '=', $id);
            });
            $response = $this->differenciateHelpTopic($query)
                ->leftJoin('ticket_thread', function ($join) {
                    $join->on('ticket_thread.ticket_id', '=', 'tickets.id')
                        ->where('is_internal', '!=', '1')
                        ->where(function ($query) {
                            return $query->whereNotNull('title')
                                ->orWhere('title', '=', '');
                        });
                })
                ->leftJoin('users as assignee', 'tickets.assigned_to', '=', "assignee.id")
                ->leftJoin('department', 'tickets.dept_id', '=', "department.id")
                ->leftJoin('ticket_priority', 'tickets.priority_id', '=', 'ticket_priority.priority_id')
                ->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
                ->leftJoin('sla_plan', 'tickets.sla', '=', 'sla_plan.id')
                ->leftJoin('ticket_source', 'tickets.source', '=', 'ticket_source.id')
                ->leftJoin('help_topic', 'tickets.help_topic_id', '=', 'help_topic.id')
                ->leftJoin('ticket_type', 'tickets.type', '=', 'ticket_type.id')
                ->leftJoin('ticket_collaborator', function ($q) {
                    $q->on('tickets.id', '=', 'ticket_collaborator.ticket_id')
                        ->where('isactive', 1);
                })
                ->leftJoin('ticket_attachment', 'ticket_thread.id', '=', 'ticket_attachment.thread_id');

            if ($satelliteModule && $satelliteModule->status == 1 && is_dir($satellite_path)) {
                $response = $response->leftJoin('satellite_helpdesk', 'tickets.domain_id', '=', 'satellite_helpdesk.id');
            }
            $result[] = $response->addSelect(
                'users.email', 'users.user_name', 'users.first_name', 'users.last_name', 'tickets.id', 'ticket_number', 'tickets.user_id', 'ticket_priority.priority_id', 'ticket_priority.priority as priority_name', 'ticket_priority.priority_color as priority_color', 'department.name as dept_name', 'ticket_status.name as status_name', 'sla_plan.name as sla_name', 'ticket_source.name as source_name', 'sla_plan.id as sla', 'ticket_status.id as status', 'lock_by', 'lock_at', 'ticket_source.id as source', 'isoverdue', 'reopened', 'isanswered', 'is_deleted', 'closed', 'reopened_at', 'duedate', 'closed_at', 'tickets.created_at', 'tickets.updated_at', 'help_topic.id as helptopic_id', 'help_topic.topic as helptopic_name', 'ticket_thread.title as title', 'ticket_type.id as type', 'ticket_type.name as type_name', 'assignee.id as assignee_id', 'assignee.first_name as assignee_first_name', 'assignee.last_name as assignee_last_name', 'assignee.user_name as assignee_user_name', 'assignee.email as assignee_email', \DB::raw('count(ticket_collaborator.id) as collaborator_count'), \DB::raw('count(ticket_attachment.id) as attachment_count'), \DB::raw('count(ticket_thread.id) as thread_count')
            )->first();

            if ($satelliteModule && $satelliteModule->status == 1 && is_dir($satellite_path)) {
                $response->addSelect('satellite_helpdesk.name as domain_name');
            }
        }
        foreach ($result as $result) {
            $result['from'] = ['first_name' => $result['first_name'], 'last_name' => $result['last_name'], 'user_name' => $result['user_name'], 'email' => $result['email']];
            unset($result['user_id'], $result['first_name'], $result['last_name'], $result['email'], $result['isoverdue'], $result['sla'], $result['lock_by'], $result['lock_at'], $result['reopened_at'], $result['isanswered'], $result['is_deleted'], $result['sla_name'], $result['dept_id'], $result['user_name'], $result['priority_id'], $result['status'], $result['source'], $result['reopened'], $result['closed'], $result['helptopic_id'], $result['type']);

            if ($result['assignee_id'] == null) {
                $result['assignee'] = null;
            } else {
                $result['assignee'] = ['first_name' => $result['assignee_first_name'], 'last_name' => $result['assignee_last_name'], 'user_name' => $result['assignee_user_name'], 'email' => $result['assignee_email']];
            }
            unset($result['closed_at'], $result['assignee_id'], $result['assignee_first_name'], $result['assignee_last_name'], $result['assignee_user_name'], $result['assignee_email']);
            $ticketList[] = $result;
        }


        return successResponse('', ['ticket' => $ticketList]);
    }

    public function sentmail(Request $request)
    {
        try {
            $v = \Validator::make($this->request->all(), [
                'help_email'   => 'required',
                'help_subject' => 'required',
                'help_massage' => 'required',
            ]);
            if ($v->fails()) {
                return errorResponse($v->errors());
            }
            $name     = 'Helpsection';
            $email    = $request->help_email;
            $subject  = $request->help_subject;
            $massage  = $request->help_massage;
            $helpdocs = $request->helpdocs;
            $scenario = null;

            if (\Input::hasFile('helpdocs')) {
                $attach = \Input::file('helpdocs');
            } else {
                $attach = "";
            }
            $files = [];
            if ($attach != "") {
                foreach ($attach as $attachment) {
                    if (is_object($attachment)) {
                        $pathToFile      = $attachment->getClientOriginalName();
                        $destinationPath = public_path('uploads/helpsection/');
                        $files[]         = $attachment->move($destinationPath, $pathToFile);
                    }
                }
            }
            $PhpMailController = new \App\Http\Controllers\Common\PhpMailController();
            $PhpMailController->sendEmail($from = $PhpMailController->mailfrom('1', '0'), $to = ['email' => $email], $message = ['subject' => $subject, 'body' => $massage, 'attachments' => $files, 'scenario' => $scenario], $template_variables = [null]);

            if ($attach != "") {
                $success = File::cleanDirectory($destinationPath);
            }

            return successResponse(Lang::get('lang.message_Sent!_thanks_for_reaching_out!_someone_from_our_team_will_get_back_to_you_soon'));

        } catch (Exception $e) {
            return errorResponse($ex->getMessage());

        }
    }
    /**
     * tThis method retun agent list based on ticket department 
     * @param Request $request
     * @return type json
     */
    public function getAgentList(Request $request)
    {
        $ticketId = explode(",", $request->Input('ticket_id'));
        $deptIds = Tickets::whereIn('id', $ticketId)->groupBy('dept_id')->pluck('dept_id')->toArray();
        $multiDeptAgents = (new CoreTicketController)->getAgentId($deptIds);
        $agentId = getAgentbasedonPermission('global_access');
        $agentIdList = array_unique(array_merge($multiDeptAgents, $agentId));
        //using helper function
        $assigntoAgents = agentListAlphabeticorder($agentIdList);
        asort($assigntoAgents);
        foreach ($assigntoAgents as $key => $value) {
            $displayResponse[] = (['id' => $key, 'name' => $value]);
        }

        return successResponse('', $displayResponse);
    }

}
