<?php

namespace App\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\TicketRequest;
//use Illuminate\Support\Facades\Request as Value;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Ticket\Ticket_attachments;
use App\Model\helpdesk\Ticket\Ticket_source;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Utility\Priority;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Http\Controllers\Agent\helpdesk\TicketController as CoreTicketController;
use Lang;
use Auth;
use Hash;

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
 * @version v2
 */
class ApiControllerVersion extends Controller
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

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->middleware('jwt.auth');
        $this->middleware('api', ['except' => 'GenerateApiKey']);
        try {
            $user       = \JWTAuth::parseToken()->authenticate();
            $this->user = $user;
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            
        }

        $ticket       = new CoreTicketController();
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

//        $faveoUser = new User();
//        $this->user = $faveoUser;

        $team       = new Teams();
        $this->team = $team;

        $setting       = new System();
        $this->setting = $setting;

        $helptopic       = new Help_topic();
        $this->helptopic = $helptopic;

        $slaPlan       = new Sla_plan();
        $this->slaPlan = $slaPlan;

        $priority       = new Priority();
        $this->priority = $priority;

        $department       = new Department();
        $this->department = $department;

        $source       = new Ticket_source();
        $this->source = $source;
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
    public function createTicket(\App\Model\helpdesk\Utility\CountryCode $code)
    {
        $v = \Validator::make($this->request->all(), [
                    'user_id'   => 'required|exists:users,id',
                    'subject'   => 'required',
                    'body'      => 'required',
                    'helptopic' => 'required',
        ]);
        if ($v->fails()) {
            $error = $v->errors();
            return response()->json(compact('error'));
        }

        try {
            $user_id                = $this->request->input('user_id');
            $user                   = User::whereId($user_id)->select('email', 'first_name', 'last_name', 'mobile', 'country_code')->first()->toArray();
            $all                    = $this->request->input() + ['Requester' => $user_id];
            $merged                 = array_merge($user, $all);
            $request                = new \App\Http\Requests\helpdesk\CreateTicketRequest();
            $request->replace($merged);
            \Route::dispatch($request);
            $PhpMailController      = new \App\Http\Controllers\Common\PhpMailController();
            $NotificationController = new \App\Http\Controllers\Common\NotificationController();
            $core                   = new CoreTicketController($PhpMailController, $NotificationController);
            $response               = $core->post_newticket($request, $code, true);
            return response()->json(compact('response'));
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
    public function getCustomForm($type = 'ticket')
    {
        try {
            $form_controller = new \App\Http\Controllers\Utility\FormController();
            $json            = $form_controller->getTicketFormJson($type, true);
            $array           = json_decode($json, true);
            return response()->json($array);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $line  = $e->getLine();
            $file  = $e->getFile();
            return response()->json(compact('error', 'file', 'line'));
        }
    }
     public function userEdit($id, Request $request)
    {
        $this->validate($request, [
            'user_name'  => 'required|unique:users,user_name,' . $id,
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email',
            'mobile' => 'numeric',
            'code' => 'numeric',
            'phone'=>'numeric',
        ]);
        try {
            if (($this->request->has('phone') && $this->request->get('phone') != '') || ($this->request->has('mobile') && $this->request->get('mobile') != '')) {
                if (!$this->request->has('code') || $this->request->get('code') == '') {
                    return errorResponse(Lang::get('lang.the_code_feild_is_required'));
                }
            }
            $user = User::whereId($id)->whereRole('user')->first();
            if (!$user) {
                throw new Exception('user not found');
            }
            $user->fill($request->all())->save();
            $message = ['message' => Lang::get('lang.updated_successfully'), 'user' => $user->where('id',$id)->select('id', 'user_name', 'first_name', 'last_name', 'email', 'role', 'profile_pic')->first()];

            return successResponse('', $message);
        } catch (Exception $ex) {
            $message = ['error' => $ex->getMessage()];
        }

        return response()->json($message);
    }



    public function editUser(Request $request)
    {
         $id=Auth::user()->id;
         $v = \Validator::make($this->request->all(), [
                   'user_name' => 'required|unique:users,user_name,' . $id,
                    'first_name' => 'required',
                    'last_name' => 'required',
                    
        ]);
        if ($v->fails()) {
            return errorResponse($v->errors());
        }
        try {
            $user = User::whereId($id)->whereRole('user')->first();
            if (!$user) {
                throw new Exception( Lang::get('lang.user_not_found'));
            }
            $user->fill($request->all())->save();
            $message = ['message' => Lang::get('lang.updated_successfully'), 'user' => $user->where('id',$id)->select('id', 'user_name', 'first_name', 'last_name', 'email', 'role', 'profile_pic')->first()];

            return successResponse('', $message);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
        return response()->json($message);
    }

    public function changePassword(Request $request)
    {
        $v = \Validator::make($this->request->all(), [
                    'old_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
        ]);
        if ($v->fails()) {
            return errorResponse($v->errors());
        }
        try {
            $user = Auth::user();

             if (Auth::user()->role != 'user') {
                return errorResponse(Lang::get('lang.user_not_found'));
             }
            if (Hash::check($request->input('old_password'), $user->getAuthPassword())) {
                $user->password = Hash::make($request->input('new_password'));
                    $user->save();
                    $message = Lang::get('lang.password_updated_sucessfully');
                    return successResponse($message);
               
            } else {
                return errorResponse(Lang::get('lang.password_was_not_updated_incorrect_old_password'));
            }
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    public function userStatus($id, Request $request)
    {
        $this->validate($request, [
            'status' => [
                'required',
                \Illuminate\Validation\Rule::in([0, 1]),
            ],
        ]);
        try {
            if (!User::has('account_activate')) {

                return errorResponse(Lang::get('lang.permission_denied'), 403);
            }
            $user = User::whereId($id)->whereRole('user')->first();
            if (!$user) {
                throw new Exception('user not found');
            }
            $user->active  = $request->input('status');
            $user->save();
            $message         = ['message' => 'changed', 'user' => $user->toArray()];
        } catch (Exception $ex) {
            $message = ['error' => $ex->getMessage()];
        }

        return response()->json($message);
    }
    public function userDetails(Request $request)
    {
        $this->validate($request, [
            'active'  => [
                \Illuminate\Validation\Rule::in([0, 1]),
            ],
            'role'    => [
                \Illuminate\Validation\Rule::in(['admin', 'agent', 'user']),
            ],
            'deleted' => [
                \Illuminate\Validation\Rule::in([0, 1]),
            ],
        ]);
        try {
            $active  = $request->input('active');
            $role    = $request->input('role');
            $deleted = $request->input('deleted');
            return User::when(isset($active), function($q)use($active) {
                                $q->where('active', $active);
                            })
                            ->when($role, function($q)use($role) {
                                $q->where('role', $role);
                            })
                            ->when($deleted, function($q)use($deleted) {
                                $q->where('is_delete', $deleted);
                            })
                            ->select('id', 'first_name', 'last_name', 'email', 'user_name', 'active', 'role', 'is_delete', 'profile_pic', 'mobile', 'ext', 'phone_number')
                            ->paginate(10)
            ;
        } catch (Exception $ex) {
            $message = ['error' => $ex->getMessage()];
        }

        return response()->json($message);
    }
    public function ticketDelete()
    {
        $this->validate($this->request, [
            'id'=>'required|exists:tickets,id|array',
        ]);
        try{
            $ids = $this->request->input('id');
            $tickets = Tickets::whereIn('id',$ids)->get();
            foreach($tickets as $ticket){
                $ticket->delete();
            }
            $message = ['success'=>'deleted successfully'];
        } catch (Exception $ex) {
            $message = ['error' => $ex->getMessage()];
        }
        return response()->json($message);
    }
    
    public function ticketAssignment(){


         $v = \Validator::make($this->request->all(), [
                        'id'=>'required|exists:tickets,id|array',
                        'assign_id'=>'required|int|exists:users,id',
            ]);
            if ($v->fails()) {
               return errorResponse($v->errors(), $responseCode = 401);
          }

        try{
            if (!User::has('assign_ticket')) {
               return errorResponse(Lang::get('lang.permission_denied'),403);
            }
            $type = $this->request->input('type','user');
            $assign_id = $this->request->input('assign_id');
            $assign_to = $type."_".$assign_id;
            $ticket_id = implode(',',$this->request->input('id'));

            $this->request->replace(['assign_to'=>$assign_to,'ticket_id'=>$ticket_id]);
            
            $assignTicket=$this->ticket->assign($this->request, true);

            if(isset(json_decode($assignTicket->content())->message->error)){
                $massage=json_decode($assignTicket->content())->message->error;
                return errorResponse($massage, 403);
            }

            $assignTicket=$assignTicket->content();
            return successResponse(json_decode($assignTicket)->message->success);
                       
        }  catch (\Exception $ex){
            return errorResponse($ex->getMessage(), $responseCode = 400);
            
        }

        
    }
}
