<?php

namespace App\Http\Controllers\Client\helpdesk;

// controllers
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\ProfilePassword;
use App\Http\Requests\helpdesk\ProfileRequest;
use App\Http\Requests\helpdesk\TicketRequest;
use App\Http\Requests\helpdesk\Sys_userRequest;
// models
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Utility\CountryCode;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Http\Requests\helpdesk\OtpVerifyRequest;
use App\Model\helpdesk\Utility\Otp;
use App\User;
use Auth;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController as Notify;
use App\Model\helpdesk\Ticket\Ticket_Status;
// classes
use Exception;
use Hash;
use Illuminate\Http\Request;
use Input;
use Lang;
use DateTime;
use DB;
use Socialite;
use App\Http\Controllers\Auth\AuthController;
 

/**
 * GuestController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class GuestController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return type void
     */
    public function __construct(PhpMailController $PhpMailController)
    {
        $this->middleware('board');
        $this->PhpMailController = $PhpMailController;
        // checking authentication
        $this->middleware('auth');
    }

    /**
     * Get profile.
     *
     * @return type Response
     */
    public function getProfile(CountryCode $code)
    {
        $user = Auth::user();
        $country_code = "auto";
        $code = CountryCode::select('iso')->where("phonecode", "=", $user->country_code)->first();
        if ($code && $user->country_code != 0) {
            $country_code = $code->iso;
        }
        $aoption = getAccountActivationOptionValue();
        return view('themes.default1.client.helpdesk.profile', compact('user', 'aoption', 'country_code'));
    }

    /**
     * @deprecated
     * Save profile data.
     *
     * @param type                $id
     * @param type ProfileRequest $request
     *
     * @return type Response
     */
    public function postProfile(ProfileRequest $request)
    {
        //dd($request);
        try {
            // geet authenticated user details
            $user = Auth::user();
            if ($request->get('country_code') == '' && $request->get('mobile') != '') {
                return redirect()->back()->with(['fails' => Lang::get('lang.country-code-required-error'), 'country_code_error' => 1])->withInput();
            } else {
                $code = CountryCode::select('phonecode')->where('phonecode', '=', $request->get('country_code'))->get();
                if (!count($code)) {
                    return redirect()->back()->with(['fails' => Lang::get('lang.incorrect-country-code-error'), 'country_code_error' => 1])->withInput();
                }
                $user->country_code = $request->country_code;
            }
            $user->fill($request->except('profile_pic', 'mobile'));
            $user->gender = $request->input('gender');
            $user->save();
            if (Input::file('profile_pic')) {
                // fetching picture name
                $name = Input::file('profile_pic')->getClientOriginalName();
                // fetching upload destination path
                $destinationPath = public_path('uploads/profilepic');
                // adding a random value to profile picture filename
                $fileName = rand(0000, 9999) . '.' . str_replace(" ", "_", $name);
                // moving the picture to a destination folder
                Input::file('profile_pic')->move($destinationPath, $fileName);
                // saving filename to database
                $user->profile_pic = $fileName;
            }
            if ($request->get('mobile')) {
                $user->mobile = $request->get('mobile');
            } else {
                $user->mobile = null;
            }
            if ($user->save()) {
                return redirect('client-profile')->with('success', Lang::get('lang.Profile-Updated-sucessfully'));
            } else {
                return redirect()->back()->route('profile')->with('fails', Lang::get('lang.Profile-Updated-sucessfully'));
            }
        } catch (Exception $e) {
            return redirect()->back()->route('profile')->with('fails', $e->getMessage());
        }
    }

    /**
     * @category fucntion to check if mobile number is unqique or not
     * @param string $mobile
     * @return boolean true(if mobile exists in users table)/false (if mobile does not exist in user table)
     */
    public function checkMobile($mobile)
    {
        if ($mobile) {
            $check = User::where('mobile', '=', $mobile)
                    ->where('id', '<>', \Auth::user()->id)
                    ->first();
            if (count($check) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get Ticket page.
     *
     * @param type Help_topic $topic
     *
     * @return type Response
     */
    public function getTicket(Help_topic $topic)
    {
        $topics = $topic->get();

        return view('themes.default1.client.helpdesk.tickets.form', compact('topics'));
    }

    /**
     * getform.
     *
     * @param type Help_topic $topic
     *
     * @return type
     */
    public function getForm(Help_topic $topic)
    {
        if (\Config::get('database.install') == '%0%') {
            return \Redirect::route('licence');
        }
        if (System::first()->status == 1) {
            $topics = $topic->get();

            return view('themes.default1.client.helpdesk.form', compact('topics'));
        } else {
            return \Redirect::route('home');
        }
    }

    /**
     * Get my ticket.
     *
     * @param type Tickets       $tickets
     * @param type Ticket_Thread $thread
     * @param type User          $user
     *
     * @return type Response
     */
    public function getMyticket()
    {
        return view('themes.default1.client.helpdesk.mytickets');
    }

    /**
     * Get ticket-thread.
     *
     * @param type Ticket_Thread $thread
     * @param type Tickets       $tickets
     * @param type User          $user
     *
     * @return type Response
     */
    public function thread(Ticket_Thread $thread, Tickets $tickets, User $user)
    {
        $user_id = Auth::user()->id;
        //dd($user_id);
        /* get the ticket's id == ticket_id of thread  */
        $tickets = $tickets->where('user_id', '=', $user_id)->first();
        //dd($ticket);
        $thread = $thread->where('ticket_id', $tickets->id)->first();
        //dd($thread);
        // $tickets = $tickets->whereId($id)->first();
        return view('themes.default1.client.guest-user.view_ticket', compact('thread', 'tickets'));
    }

    /**
     * ticket Edit.
     *
     * @return
     */
    public function ticketEdit()
    {
        // why this function is empty?
    }

    /**
     * Post porfile password.
     *
     * @param type                 $id
     * @param type ProfilePassword $request
     *
     * @return type Response
     */
    public function postProfilePassword(ProfilePassword $request)
    {
        $user = Auth::user();
        //echo $user->password;
        if (Hash::check($request->input('old_password'), $user->getAuthPassword())) {
            $user->password = Hash::make($request->input('new_password'));
            try {
                $user->save();

                return redirect()->back()->with('success2', Lang::get('lang.password_updated_sucessfully'));
            } catch (Exception $e) {
                return redirect()->back()->with('fails2', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('fails2', Lang::get('lang.password_was_not_updated_incorrect_old_password'));
        }
    }

    /**
     * Ticekt reply.
     *
     * @param type Ticket_Thread $thread
     * @param type TicketRequest $request
     *
     * @return type Response
     */
    public function reply(Ticket_Thread $thread, TicketRequest $request)
    {
        $thread->ticket_id = $request->input('ticket_ID');
        $thread->title = $request->input('To');
        $thread->user_id = Auth::user()->id;
        $thread->body = $request->input('reply_content');
        $thread->poster = 'user';
        $thread->save();
        $ticket_id = $request->input('ticket_ID');
        $tickets = Tickets::where('id', '=', $ticket_id)->first();
        $thread = Ticket_Thread::where('ticket_id', '=', $ticket_id)->first();

        return Redirect('thread/' . $ticket_id);
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
     * Post Check ticket.
     *
     * @param type CheckTicket   $request
     * @param type User          $user
     * @param type Tickets       $ticket
     * @param type Ticket_Thread $thread
     *
     * @return type Response
     */
    public function PostCheckTicket(Request $request)
    {
        $validator = \Validator::make($request->all(), [
                    'email' => 'required|email',
                    'ticket_number' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput()
                            ->with('check', '1');
        }
        $Email = $request->input('email');
        $Ticket_number = $request->input('ticket_number');
        $ticket = Tickets::where('ticket_number', '=', $Ticket_number)->first();
        if ($ticket == null) {
            return \Redirect::route('form')->with('fails', Lang::get('lang.there_is_no_such_ticket_number'));
        } else {
            $userId = $ticket->user_id;
            $user = User::where('id', '=', $userId)->first();
            if ($user->role == 'user') {
                $username = $user->first_name;
            } else {
                $username = $user->first_name . ' ' . $user->last_name;
            }
            if ($user->email != $Email) {
                return \Redirect::route('form')->with('fails', Lang::get("lang.email_didn't_match_with_ticket_number"));
            } else {
                $code = $ticket->id;
                $code = \Crypt::encrypt($code);

                $company = $this->company();

                $this->PhpMailController->sendmail(
                    $from = $this->PhpMailController->mailfrom('1', '0'),
                    $to = ['name' => $username,
                    'email' => $user->email],
                    $message = ['subject' => 'Ticket link Request [' . $Ticket_number . ']', 'scenario' => 'check-ticket'],
                    $template_variables = [
                            'user' => $username,
                            'ticket_link' =>\URL::route('check_ticket', $code),
                            'ticket_subject' => title($ticket->id),
                            'ticket_number' => $ticket->ticket_number,
                            'ticket_due_date' => $ticket->duedate->tz(timezone()),
                            'ticket_created_at' => $ticket->created_at->tz(timezone()),
                            ]
                );

                return \Redirect::back()
                                ->with('success', Lang::get('lang.we_have_sent_you_a_link_by_email_please_click_on_that_link_to_view_ticket'));
            }
        }
    }

    /**
     * get ticket email.
     *
     * @param type $id
     *
     * @return type
     */
    public function getTicketEmail($id, CommonSettings $commonSettings)
    {
    	$tickets = \App\Model\helpdesk\Ticket\Tickets::where('id', '=', \Crypt::decrypt($id))->first();
    	if ($tickets) {
			$thread = \App\Model\helpdesk\Ticket\Ticket_Thread::where('ticket_id', '=', $tickets->id)->first();

	    	$optionArray = ['user_set_ticket_status', 'user_reply_org_ticket', 'user_show_org_ticket',
    		'allow_organization_dept_mngr_approve_tickets', 'allow_organization_mngr_approve_tickets'];
        	$tempArray = $commonSettings->select('option_name', 'status')->whereIn('option_name', $optionArray)->get()->toArray();
            $commonSettingArray = null;
        	foreach ($tempArray as $value) {
        		$commonSettingArray[$value['option_name']] = $value['status'];
        	}
            $userIsOrganizationManager = \Auth::user()->isManagerOf();
            if ($this->userCanViewTicket($tickets, !$commonSettingArray['user_show_org_ticket'])) {
                return view('themes.default1.client.helpdesk.ckeckticket', compact('id', 'commonSettingArray', 'thread',  'tickets', 'userIsOrganizationManager'));   
            }
            return redirect()->to('/')->with('fails', trans('lang.unauthorized_access'));
    	}
    }

    /**
     * get ticket status.
     *
     * @param type Tickets $ticket
     *
     * @return type
     */
    public function getTicketStat(Tickets $ticket)
    {
        return view('themes.default1.client.helpdesk.ckeckticket', compact('ticket'));
    }

    /**
     * get company.
     *
     * @return type
     */
    public function company()
    {
        $company = Company::Where('id', '=', '1')->first();
        if ($company->company_name == null) {
            $company = 'Support Center';
        } else {
            $company = $company->company_name;
        }

        return $company;
    }

    public function resendOTP(OtpVerifyRequest $request)
    {
        if (\Schema::hasTable('sms')) {
            $sms = DB::table('sms')->get();
            if (count($sms) > 0) {
                \Event::dispatch(new \App\Events\LoginEvent($request));
                return 1;
            }
        } else {
            return "Plugin has not been setup successfully.";
        }
    }

    public function verifyOTP()
    {
        $user = User::select('id', 'mobile', 'country_code', 'user_name', 'mobile_verify', 'updated_at')->where('id', '=', Input::get('u_id'))->first();
        $otp = Input::get('otp');
        if ($otp != null || $otp != '') {
            $otp_length = strlen(Input::get('otp'));
            if (($otp_length == 6 && !preg_match("/[a-z]/i", Input::get('otp')))) {
                $otp2 = Hash::make(Input::get('otp'));
                $date1 = date_format($user->updated_at, "Y-m-d h:i:sa");
                $date2 = date("Y-m-d h:i:sa");
                $time1 = new DateTime($date2);
                $time2 = new DateTime($date1);
                $interval = $time1->diff($time2);
                if ($interval->i > 10 || $interval->h > 0) {
                    $message = Lang::get('lang.otp-expired');
                    return $message;
                } else {
                    if (Hash::check(Input::get('otp'), $user->mobile_verify)) {
                        User::where('id', '=', $user->id)
                            ->update([
                                'mobile_verify' => '1',
                                'mobile' => Input::get('mobile'),
                                'country_code' => str_replace('+', '', Input::get('country_code'))
                            ]);
                        return 1;
                    } else {
                        $message = Lang::get('lang.otp-not-matched');
                        return $message;
                    }
                }
            } else {
                $message = Lang::get('lang.otp-invalid');
                return $message;
            }
        } else {
            $message = Lang::get('lang.otp-not-matched');
            return $message;
        }
    }

    public function sync()
    {
        try {
            $provider = $this->getProvider();
            $this->changeRedirect();
            $users = Socialite::driver($provider)->user();
            $this->forgetSession();
            $user['provider'] = $provider;
            $user['social_id'] = $users->id;
            $user['name'] = $users->name;
            $user['email'] = $users->email;
            $user['username'] = $users->nickname;
            $user['avatar'] = $users->avatar;
            return redirect('client-profile')->with('success', 'Additional informations fetched');
        } catch (Exception $ex) {
//            dd($ex);
            return redirect('client-profile')->with('fails', $ex->getMessage());
        }
    }

    public function getProvider()
    {
        $provider = \Session::get('provider');
        return $provider;
    }

    public function changeRedirect()
    {
        $provider = \Session::get('provider');
        $url = \Session::get($provider . 'redirect');
        \Config::set("services.$provider.redirect", $url);
    }

    public function forgetSession()
    {
        $provider = $this->getProvider();
        \Session::forget('provider');
        \Session::forget($provider . 'redirect');
    }

    public function checkArray($key, $array)
    {
        $value = "";
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
        }
        return $value;
    }

    public function updateUser($user = [])
    {
        $userid = \Auth::user()->id;
        $useremail = \Auth::user()->email;
        $email = $this->checkArray('email', $user); //$user['email'];
        if ($email !== "" && $email !== $useremail) {
            throw new Exception("Sorry! your current email and " . ucfirst($user['provider']) . " email is different so system can not sync");
        }
        $this->update($userid, $user);
    }

    public function update($userid, $user, $provider)
    {
        $email = $this->checkArray('email', $user);
        $this->deleteUser($userid, $user, $provider);
        $this->insertAdditional($userid, $provider, $user);
        $this->changeEmail($email);
    }

    public function deleteUser($userid, $user, $provider)
    {
        $info = new \App\UserAdditionalInfo();
        $infos = $info->where('owner', $userid)->where('service', $provider)->get();
        if ($infos->count() > 0 && count($user) > 0) {
            foreach ($infos as $key => $detail) {
                //if ($user[$key] !== $detail->$key) {
                $detail->delete();
                //}
            }
        }
    }

    public function insertAdditional($id, $provider, $user = [])
    {
        $info = new \App\UserAdditionalInfo();
        if (count($user) > 0) {
            foreach ($user as $key => $value) {
                $info->create([
                    'owner' => $id,
                    'service' => $provider,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
    }

    public function changeEmail($email)
    {
        $user = \Auth::user();
        if ($user && $email && !$user->email) {
            $user->email = $email;
            $user->save();
        }
    }


    /**
     * This method return manger list with details,user list with details of the organization
     * @param Organization $org
     * @return  json
     */
    public function clientOrgManagerData(Organization $org)
    {
        try {

           $userRole = User_org::where('user_id', Auth::user()->id)->where('org_id', $org->id)->value('role');

            if (!Auth::user()->isManagerOf() || ($userRole != 'manager')) {

                return errorResponse(Lang::get('lang.permission_denied'));
            }

            $organization = $org->where('id', $org->id)->with(['managers:users.id,first_name,last_name,email,user_name,mobile,profile_pic',
                'members:users.id,first_name,last_name,email,user_name,mobile,profile_pic'
                ])->select('id','name','phone','logo','domain')->first();

            $organization->logo = $org->logo ? url('/').$org->logo : NULL;
            
            $data=['organization'=>$organization];

            return SuccessResponse('',$data);
            /* To view page */
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }




    /**
 * This method return user list of particuler organization
 * @param type $orgId of Organization
 * @return type json
 */
    public function getOrgUserList(int $orgId ,Request $request)
    {
        try {
            $userId = User_org::where('org_id', $orgId)->pluck('user_id')->toArray();

            $pagination = ($request->input('limit')) ?  : 10;
            $sortBy = ($request->input('sort-field')) ? $request->input('sort-field') : 'id';
            $search = $request->input('search-query');
            $orderBy = ($request->input('sort-order')) ? $request->input('sort-order') : 'desc';


            $baseQuery = User::whereIn('id', $userId)->select('id', 'first_name', 'last_name', 'user_name','ext','phone_number',  'country_code','mobile', 'email', 'active','email_verify','mobile_verify','is_delete','updated_at')->where('is_delete',0)->where('active',1)->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('email_verify', 'LIKE', '%' . $search . '%')
                        ->orWhere('mobile_verify', 'LIKE', '%' . $search . '%')
                        ->orWhere('is_delete', 'LIKE', '%' . $search . '%')
                        ->orWhere('updated_at', 'LIKE', '%' . $search . '%')
                        ->orWhere('active', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone_number', 'LIKE', '%' . $search . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $search . '%');

                    })
                    ->paginate($pagination);
            return successResponse($searchQuery);

        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    public function managerLicensesDocs($org_id)
    {
        try {
            // $licenses_docs = new OrgAttachment();
            // $license_docs_list = $licenses_docs->select('id','org_id','file_name','created_at')->get();
            $license_docs_list = OrgAttachment::where('org_id', '=', $org_id)->select('id', 'org_id', 'file_name', 'created_at');


            return \Datatable::query($license_docs_list)
                            ->showColumns('file_name', 'created_at')
                            ->addColumn('action', function ($model) {
                                return "<a href=" . url('Client/download/' . $model->file_name) . " class='btn btn-info btn-xs'>download</a>";
                            })
                            ->searchColumns('file_name')
                            ->orderColumns('file_name')
                            ->make();
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

// public function clientDownloadDocs($file_name) {
//     // dd($file_name);
//     $file_path = public_path('uploads/organizations/'.$file_name);
//     // dd($file_path);
//     return response()->download($file_path);
//   }

/**
 * get infomation from user directery
 * @param type $id
 * @param CountryCode $code
 * @return type
 */
    public function orgEditUser($id, CountryCode $code)
    {
        try {
     
            $users = User::whereId($id)->select('first_name','last_name','user_name','email','active','internal_note','country_code','mobile','ext','phone_number','iso')->first();
            $organizationid = User_org::where('user_id',  $id)->pluck('org_id')->first();
            $data=['user'=>$users,'selectedorg'=>$organizationid];
            return successResponse('', $data);
             
        } catch (Exception $e) {
               return errorResponse($e->getMessage());       
        }
    }
/**
 * this method update user information 
 * @param type $userId of user
 * @param Sys_userRequest $request
 * @return type json
 */
    public function updateOrgUser(User $user,Sys_userRequest $request)
    {
       try {
            $checkRole=User_org::where('user_id',$user->id)->value('role');
            $authController = new AuthController;
            $userUpdatedObj = $authController->postRegister($user, $request);
            User_org::where('user_id',$user->id)->update(['org_id'=>$request->input('org_id'),'role'=>$checkRole]);
           /* redirect to Index page with Success Message */
            return successResponse(Lang::get('lang.User-profile-Updated-Successfully'));
         } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return errorResponse($e->getMessage());
        }
    }
/**
 * this method deactivated the user  
 * @param type $userId of user
 * @return type json
 */
    public function orgDeleteUser($userId)
    {
        try{
        $tickets = Tickets::where('user_id', $userId)->pluck('id')->toArray();
                if ($tickets) {
                    $status = Ticket_Status::select('id', 'icon', 'icon_color', 'name')->where('purpose_of_status',2)->orderBy('order')->min('id');

                    foreach ($tickets as $ticket) {
                        Tickets::where('id', $ticket)->update(['status' => $status]);
                        $userDetail = User::where('id',  Auth::user()->id)->first();
                        $assignee =$userDetail->name();

                        Ticket_Thread::create(['ticket_id'=>$ticket,'user_id'=>Auth::user()->id,'is_internal'=>1,'body'=>'This Ticket has been close by ' . $assignee]);
                    }
                }
            $users = User::where('id', $userId)->first();
        User_org::where('user_id',$userId)->update(['role'=>'members']);
        if ($users->role == 'user') {
             User::where('id', $userId)->update(['is_delete'=>1,'active'=>0]);
             return successResponse(Lang::get('lang.user_deactivated_successfully'));
        }
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return errorResponse($e->getMessage());
        }


    }

    // } catch (Exception $e) {
    /* redirect to Index page with Fails Message */
    // return redirect('user')->with('fails', $e->getMessage());
    // }
    // }

    public function managerCreateUser(CountryCode $code)
    {
        try {
            $aoption = getAccountActivationOptionValue();
            $email_mandatory = CommonSettings::select('status')->where('option_name', '=', 'email_mandatory')->first();
            return view('themes.default1.client.helpdesk.organization.create', compact('email_mandatory', 'aoption'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->errorInfo[2]);
        }
    }

    /**
     * Generate a random string for password.
     *
     * @param type $length
     *
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        // list of supported characters
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // character length checked
        $charactersLength = strlen($characters);
        // creating an empty variable for random string
        $randomString = '';
        // fetching random string
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        // return random string
        return $randomString;
    }

    public function storeUserOrgRelation($userid, $orgid)
    {
        $org_relations = new User_org();
        $org_relation = $org_relations->where('user_id', $userid)->first();
        if ($org_relation) {
            $org_relation->delete();
        }
        $org_relations->create([
            'user_id' => $userid,
            'org_id' => $orgid,
            'role' => 'members',
        ]);
    }

    public function storeUser(User $user, Sys_userRequest $request)
    {
        try {
                $authController = new AuthController;
                $userUpdatedObj = $authController->postRegister($user, $request);
                if($userUpdatedObj && $request->input('org_id', null)) {
                    $orgid = $request->input('org_id');
                    $this->storeUserOrgRelation($userUpdatedObj->id, $orgid);
                }
                return successResponse(Lang::get('lang.user_created_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return errorResponse($e->getMessage());
        }
    }

    public function managerViewUser($id)
    {
        try {
            $users = User::where('id', '=', $id)->first();
            if (count($users) > 0) {
                return view('themes.default1.client.helpdesk.organization.usershow', compact('users'));
            } else {
                return redirect()->back()->with('fails', Lang::get('lang.user-not-found'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Random Password Genetor for users
     *
     * @param type int  $id
     * @param type User $user
     *
     * @return type view
     */
    public function randomPassword()
    {
        try {
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890~!@#$%^&*(){}[]';
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 10; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }

            return implode($pass);
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Random Password Genetor for users
     *
     * @param type int  $id
     * @param type User $user
     *
     * @return type view
     */
    public function randomPostPassword($id, ChangepasswordRequest $request)
    {
        try {
            $changepassword = $request->change_password;
            $user = User::whereId($id)->first();
            $password = $request->change_password;
            $user->password = Hash::make($password);
            $user->save();
            $name = $user->first_name;
            $email = $user->email;

            $this->PhpMailController->sendmail($from = $this->PhpMailController
                    ->mailfrom('1', '0'), $to = ['name' => $name, 'email' => $email], $message = ['subject' => null, 'scenario' => 'reset_new_password'], $template_variables = ['user' => $name, 'user_password' => $password]);

            return redirect()->back()->with('success11', Lang::get('lang.password_change_successfully'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function restoreUser($id)
    {
        // dd($id);
        // $delete_all = Input::get('delete_all');
        $users = User::where('id', '=', $id)->first();
        $users->is_delete = 0;
        $users->active = 1;
        $users->save();
        return redirect()->back()->with('success11', Lang::get('lang.user_restore_successfully'));
    }

    public function system_mail()
    {
        $email = Email::where('id', '=', '1')->first();

        return $email->sys_email;
    }

    /**
     * function to Ticket Close.
     *
     * @param type         $id
     * @param type Tickets $ticket
     *
     * @return type string
     */
    public function close($id, Tickets $ticket)
    {
        $ticket = Tickets::where('id', '=', $id)->first();

        $approval = Approval::where('id', '=', 1)->first();
        //Admin can close direce
        // if (Auth::user()->role == 'admin' || Auth::user()->role == 'user') {

        if (Auth::user()->role == 'user') {
            // dd($id);
            // $ticket_status = $ticket->where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->first();
            $ticket_status = $ticket->where('id', '=', $id)->first();
        } else {
            $ticket_status = $ticket->where('id', '=', $id)->first();
        }
        // dd($ticket_status);
        // checking for unautherised access attempt on other than owner ticket id
        if ($ticket_status == null) {
            return redirect()->route('unauth');
        }
        $ticket_status->status = 3;
        $ticket_status->closed = 1;
        $ticket_status->closed_at = date('Y-m-d H:i:s');
        $ticket_status->save();

        // dd('pooppo');
        $ticket_thread = Ticket_Thread::where('ticket_id', '=', $ticket_status->id)->first();
        $ticket_subject = $ticket_thread->title;
        $ticket_status_message = Ticket_Status::where('id', '=', $ticket_status->status)->first();
        $thread = new Ticket_Thread();
        $thread->ticket_id = $ticket_status->id;
        $thread->user_id = Auth::user()->id;
        $thread->is_internal = 1;
        $thread->body = $ticket_status_message->message . ' ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $thread->save();

        $user_id = $ticket_status->user_id;
        $user = User::where('id', '=', $user_id)->first();
        $email = $user->email;
        $user_name = $user->user_name;
        $ticket_number = $ticket_status->ticket_number;

        $system_from = $this->company();
        $sending_emails = Emails::where('department', '=', $ticket_status->dept_id)->first();
        if ($sending_emails == null) {
            $from_email = $this->system_mail();
        } else {
            $from_email = $sending_emails->id;
        }
        try {
            $this->PhpMailController->sendmail($from = $this->PhpMailController->mailfrom('0', $ticket_status->dept_id), $to = ['name' => $user_name, 'email' => $email], $message = ['subject' => $ticket_subject . '[#' . $ticket_number . ']', 'scenario' => 'close-ticket'], $template_variables = ['ticket_number' => $ticket_number]);
        } catch (\Exception $e) {
            return 0;
        }
        $data = [
            'id' => $ticket_status->ticket_number,
            'status' => 'Closed',
            'first_name' => Auth::user()->first_name,
            'last_name' => Auth::user()->last_name,
        ];
        \Event::dispatch('change-status', [$data]);

        return 'your ticket' . $ticket_status->ticket_number . ' has been closed';
        // }




        if ($approval->status == 0) {
            $ticket_status = $ticket->where('id', '=', $id)->first();
            // checking for unautherised access attempt on other than owner ticket id
            if ($ticket_status == null) {
                return redirect()->route('unauth');
            }
            $ticket_status->status = 3;
            $ticket_status->closed = 1;
            $ticket_status->closed_at = date('Y-m-d H:i:s');
            $ticket_status->save();
            $ticket_thread = Ticket_Thread::where('ticket_id', '=', $ticket_status->id)->first();
            $ticket_subject = $ticket_thread->title;
            $ticket_status_message = Ticket_Status::where('id', '=', $ticket_status->status)->first();
            $thread = new Ticket_Thread();
            $thread->ticket_id = $ticket_status->id;
            $thread->user_id = Auth::user()->id;
            $thread->is_internal = 1;
            $thread->body = $ticket_status_message->message . ' ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $thread->save();

            $user_id = $ticket_status->user_id;
            $user = User::where('id', '=', $user_id)->first();
            $email = $user->email;
            $user_name = $user->user_name;
            $ticket_number = $ticket_status->ticket_number;

            $system_from = $this->company();
            $sending_emails = Emails::where('department', '=', $ticket_status->dept_id)->first();
            if ($sending_emails == null) {
                $from_email = $this->system_mail();
            } else {
                $from_email = $sending_emails->id;
            }
            try {
                $this->PhpMailController->sendmail($from = $this->PhpMailController->mailfrom('0', $ticket_status->dept_id), $to = ['name' => $user_name, 'email' => $email], $message = ['subject' => $ticket_subject . '[#' . $ticket_number . ']', 'scenario' => 'close-ticket'], $template_variables = ['ticket_number' => $ticket_number]);
            } catch (\Exception $e) {
                return 0;
            }
            $data = [
                'id' => $ticket_status->ticket_number,
                'status' => 'Closed',
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
            ];
            \Event::dispatch('change-status', [$data]);

            return 'your ticket' . $ticket_status->ticket_number . ' has been closed';
        }



        if ($approval->status == 1) {
            $ticket_status = $ticket->where('id', '=', $id)->first();
            // checking for unautherised access attempt on other than owner ticket id
            if ($ticket_status == null) {
                return redirect()->route('unauth');
            }

            $ticket_status->status = 7;
            $ticket_status->closed = 0;
            // $ticket_status->closed_at = date('Y-m-d H:i:s');
            $ticket_status->save();
            $ticket_thread = Ticket_Thread::where('ticket_id', '=', $ticket_status->id)->first();
            $ticket_subject = $ticket_thread->title;
            $ticket_status_message = Ticket_Status::where('id', '=', $ticket_status->status)->first();
            $thread = new Ticket_Thread();
            $thread->ticket_id = $ticket_status->id;
            $thread->user_id = Auth::user()->id;
            // $thread->is_internal = 1;
            $thread->body = $ticket_status_message->message . ' ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $thread->save();

            $user_id = $ticket_status->user_id;
            $user = User::where('id', '=', $user_id)->first();
            $email = $user->email;
            $user_name = $user->user_name;
            $ticket_number = $ticket_status->ticket_number;

            $system_from = $this->company();
            $sending_emails = Emails::where('department', '=', $ticket_status->dept_id)->first();
            if ($sending_emails == null) {
                $from_email = $this->system_mail();
            } else {
                $from_email = $sending_emails->id;
            }
            try {
                $this->PhpMailController->sendmail($from = $this->PhpMailController->mailfrom('0', $ticket_status->dept_id), $to = ['name' => $user_name, 'email' => $email], $message = ['subject' => $ticket_subject . '[#' . $ticket_number . ']', 'scenario' => 'close-ticket'], $template_variables = ['ticket_number' => $ticket_number]);
            } catch (\Exception $e) {
                return 0;
            }
            $data = [
                'id' => $ticket_status->ticket_number,
                'status' => 'Closed',
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
            ];

            \Event::dispatch('change-status', array($data));
            return 'your ticket' . $ticket_status->ticket_number . ' has been closed request';
        }
    }

    /**
     * function to Open Ticket.
     *
     * @param type         $id
     * @param type Tickets $ticket
     * @return type
     */
    public function open($id, Tickets $ticket)
    {
        if (Auth::user()->role == 'user') {
            $ticket_status = $ticket->where('id', '=', $id)->first();
        } else {
            $ticket_status = $ticket->where('id', '=', $id)->first();
        }
        // checking for unautherised access attempt on other than owner ticket id
        if ($ticket_status == null) {
            return redirect()->route('unauth');
        }
        $ticket_status->status = 1;
        $ticket_status->reopened_at = date('Y-m-d H:i:s');
        $ticket_status->save();
        $ticket_status_message = Ticket_Status::where('id', '=', $ticket_status->status)->first();
        $thread = new Ticket_Thread();
        $thread->ticket_id = $ticket_status->id;
        $thread->user_id = Auth::user()->id;
        $thread->is_internal = 1;
        $thread->body = $ticket_status_message->message . ' ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $thread->save();
        $data = [
            'id' => $ticket_status->ticket_number,
            'status' => 'Open',
            'first_name' => Auth::user()->first_name,
            'last_name' => Auth::user()->last_name,
        ];
        \Event::dispatch('change-status', array($data));
        return 'your ticket' . $ticket_status->ticket_number . ' has been opened';
    }

    /**
     * Function to delete ticket.
     *
     * @param type         $id
     * @param type Tickets $ticket
     *
     * @return type string
     */
    public function delete($id, Tickets $ticket)
    {
        $ticket_delete = $ticket->where('id', '=', $id)->first();
        if ($ticket_delete->status == 5) {
            $ticket_delete->delete();
            $ticket_threads = Ticket_Thread::where('ticket_id', '=', $id)->get();
            foreach ($ticket_threads as $ticket_thread) {
                $ticket_thread->delete();
            }
            $ticket_attachments = Ticket_attachments::where('ticket_id', '=', $id)->get();
            foreach ($ticket_attachments as $ticket_attachment) {
                $ticket_attachment->delete();
            }
            $data = [
                'id' => $ticket_delete->ticket_number,
                'status' => 'Deleted',
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
            ];
            \Event::dispatch('change-status', array($data));
            return 'your ticket has been delete';
        } else {
            $ticket_delete->is_deleted = 1;
            $ticket_delete->status = 5;
            $ticket_delete->save();
            $ticket_status_message = Ticket_Status::where('id', '=', $ticket_delete->status)->first();
            $thread = new Ticket_Thread();
            $thread->ticket_id = $ticket_delete->id;
            $thread->user_id = Auth::user()->id;
            $thread->is_internal = 1;
            $thread->body = $ticket_status_message->message . ' ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $thread->save();
            $data = [
                'id' => $ticket_delete->ticket_number,
                'status' => 'Deleted',
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
            ];
            \Event::dispatch('change-status', array($data));
            return 'your ticket' . $ticket_delete->ticket_number . ' has been delete';
        }
    }

    /**
     * select_all.
     *
     * @return type
     */
    public function select_all()
    {
        if (Input::has('select_all')) {
            $selectall = Input::get('select_all');

            $value = Input::get('submit');
            // dd( $value);
            foreach ($selectall as $delete) {
                $ticket = Tickets::whereId($delete)->first();
                if ($value == 'Delete') {
                    $this->delete($delete, new Tickets);
                } elseif ($value == 'Close') {
                    // dd('herer');
                    $this->close($delete, new Tickets);
                } elseif ($value == 'Open') {
                    $this->open($delete, new Tickets);
                } elseif ($value == 'Delete forever') {
                    $notification = Notification::select('id')->where('model_id', '=', $ticket->id)->get();
                    foreach ($notification as $id) {
                        $user_notification = UserNotification::where('notification_id', '=', $id->id);
                        $user_notification->delete();
                    }
                    $notification = Notification::select('id')->where('model_id', '=', $ticket->id);
                    $notification->delete();
                    $thread = Ticket_Thread::where('ticket_id', '=', $ticket->id)->get();
                    foreach ($thread as $th_id) {
                        // echo $th_id->id." ";
                        $attachment = Ticket_attachments::where('thread_id', '=', $th_id->id)->get();
                        if (count($attachment)) {
                            foreach ($attachment as $a_id) {
                                // echo $a_id->id . ' ';
                                $attachment = Ticket_attachments::find($a_id->id);
                                $attachment->delete();
                            }
                            // echo "<br>";
                        }
                        $thread = Ticket_Thread::find($th_id->id);
//                        dd($thread);
                        $thread->delete();
                    }
                    $collaborators = Ticket_Collaborator::where('ticket_id', '=', $ticket->id)->get();
                    if (count($collaborators)) {
                        foreach ($collaborators as $collab_id) {
                            // echo $collab_id->id;
                            $collab = Ticket_Collaborator::find($collab_id->id);
                            $collab->delete();
                        }
                    }
                    $tickets = Tickets::find($ticket->id);
                    $tickets->delete();
                    $data = ['id' => $ticket->id];
                    \Event::dispatch('ticket-permanent-delete', [$data]);
                }
            }
            if ($value == 'Delete') {
                return redirect()->back()->with('success12', lang::get('lang.moved_to_trash'));
            } elseif ($value == 'Close') {
                return redirect()->back()->with('success12', Lang::get('lang.tickets_have_been_closed'));
            } elseif ($value == 'Open') {
                return redirect()->back()->with('success12', Lang::get('lang.tickets_have_been_opened'));
            } else {
                return redirect()->back()->with('success12', Lang::get('lang.hard-delete-success-message'));
            }
        }

        return redirect()->back()->with('fails', 'None Selected!');
    }

    protected function userCanViewTicket($ticket, $visibility = false)
    {
        if ($ticket->user_id == \Auth::user()->id) {
            return true;
        }
        $ticketOwnersOrganizations = $ticket->user()->first()->orgManager()->pluck('org_id')->toArray();
        $authenticatedUsersOrganizations = \Auth::user()->orgManager()->pluck('org_id')->toArray();
        $userIsMemberOfOwnersOrganisation = count(array_intersect($ticketOwnersOrganizations, $authenticatedUsersOrganizations));
        $authenticatedUsersRolesOrganizations = \Auth::user()->orgManager()->pluck('role')->toArray();
        if ($userIsMemberOfOwnersOrganisation > 0 && $visibility) {
            return true;
        } elseif ($userIsMemberOfOwnersOrganisation > 0 && !$visibility) {
            if(in_array('manager', $authenticatedUsersRolesOrganizations)) {
                return true;
            }
        }
        return false;
    }

}
