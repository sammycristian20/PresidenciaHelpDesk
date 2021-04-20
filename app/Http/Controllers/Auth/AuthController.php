<?php

namespace App\Http\Controllers\Auth;

// controllers
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Common\SettingsController;
use App\Http\Controllers\Controller;
use Response;
// requests
use App\Http\Requests\helpdesk\LoginRequest;
use App\Http\Requests\helpdesk\OtpVerifyRequest;
use App\Model\helpdesk\Settings\Security;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Utility\CountryCode;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\kb\Timezone;
use App\Model\helpdesk\Settings\Plugin;
use App\Model\helpdesk\Settings\SocialMedia;
use App\Model\helpdesk\Form\CustomFormValue;

// classes
use App\User;
use App\Model\helpdesk\Utility\Otp;
use Schema;
use Google2FA;
use Crypt;
use Auth;
use DB;
use Hash;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Lang;
use DateTime;
use Input;
use Socialite;
use App\Http\Controllers\Admin\helpdesk\SocialMedia\SocialMediaController;
use Illuminate\Http\Request;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Form\FormFieldOption;
use Lcobucci\JWT\Parser;

use Cache;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Requests\helpdesk\ValidateSecretRequest;
use Illuminate\Support\Str;
use App\Exceptions\VerificationRequiredException;
use App\Traits\UserVerificationHelper;
use Event;

/**
 * ---------------------------------------------------
 * AuthController
 * ---------------------------------------------------
 * This controller handles the registration of new users, as well as the
 * authentication of existing users. By default, this controller uses
 * a simple trait to add these behaviors. Why don't you explore it?
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class AuthController extends Controller
{
    use UserVerificationHelper;
    //use AuthenticatesAndRegistersUsers;
    /* to redirect after login */

    // if auth is agent
    protected $redirectTo = '/dashboard';
    // if auth is user
    protected $redirectToUser = '/profile';
    /* Direct After Logout */
    protected $redirectAfterLogout = '/';
    protected $loginPath = '/auth/login';
    protected $social;

    /**
     * Create a new authentication controller instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard     $auth
     * @param \Illuminate\Contracts\Auth\Registrar $registrar
     *
     * @return void
     */
    public function __construct()
    {
        $this->PhpMailController = new PhpMailController();
        $social = new SocialMediaController();
        $social->configService();
        $this->middleware('guest', ['except' => ['postLogout', 'verifyOTP', 'resendOTP', 'redirectToProvider', 'resendActivationLink','postRegister','getLoggedInUserInfo','licenseVerification','postSetupValidateToken']]);
        $this->middleware('board', ['only' => ['getRegister']]);
        $this->middleware('limit.exceeded', ['only' => ['postRegister']]);
       

    }

    /**
     * ---------------------------------------------------
     *          New clean code block 
     * ---------------------------------------------------
     */

    /**
     * Method to handle user registration request and user account update request.
     *
     * Note: This method is being called from the methods/controllers which handle API
     * call for user registration, user creation/update, agent creation/update and
     * from user creation process of ticket creation and add collaborators. So
     *
     * 1: This method handles updating/creating record in user table with basic required
     * data and updates custom field data too.
     *
     * 2: This method also handles user create notification alert while creating
     * new account.
     *
     * 3: This method assumes the request has been validated for required validation rule
     * in the method from where it's being called.
     *
     * @param   User     $user                  User to create or update
     * @param   Request  $request               $request data containing values for
     *                                          creating/updating user record
     * @param   string   $role                  role of a user agent/admin/user
     *
     * @var     array    $data                  Array containing user table's column and
     *                                          value as key => value pair
     * @var     mixed    $sendRegistrationAlert integer id of user or null
     * @return  User      
     *
     * @throws  Exception
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    public function postRegister(User $user, Request $request, string $role='user'):User
    {
        try{
            $copyOfUser = $user;
            $userExists = $user->id;
            $data = $this->getDataArrayOfUser($user, $request, $role);
            $user = $user->updateOrCreate(['id' => $user->id], $data);
            $this->saveUserCustomFormData($user, $request);
            if(!$userExists) {
                $this->sendRegistrationAlert($user, $data);
            }
            $this->modifyVerificationEntities($copyOfUser, $user);
            return $user;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * Function sets entities which require verification as unverified when the user
     * is updated. Such entities are mobile/email etc so while updating if these entities
     * are updated we set them again as unverified.
     * 
     * @param   User  $userBeforeUpdate  user model before updating
     * @param   User  $userAfterUpdate   user model after updating
     *
     * @var     array $columnsRequireVerfication  name of columns for entities which require
     *                                            verification
     * @var     array $updatedColumns             name of the updated columns for user
     * @var     array $toModify                   name of columns which needs to be updted for
     *                                            setting entities as unverified. 
     *
     * @return  void
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function modifyVerificationEntities(User $userBeforeUpdate, User $userAfterUpdate):void
    {
        $columnsRequireVerfication = ['email', 'mobile'];
        $updatedColumns = array_keys(array_diff_assoc($userBeforeUpdate->toArray(), $userAfterUpdate->toArray()));
        $toModify = array_map(function($value){
            return $value."_verify";
        },array_intersect($columnsRequireVerfication, $updatedColumns));
        $this->setEntitiesUnverifiedByColumnNames($userAfterUpdate, $toModify);
    }

    /**
     * Method returns an array containing user table's column name and their values
     * as key => value pair. This method sets the value for basic columns of user
     * record based on $user and $request as if user is an existing record then it's
     * password/email and other values should not be updated without actually updating
     * them in the request call.
     *
     * @param   User     $user     User to create or update
     * @param   Request  $request  $request data containing values for
     *                             creating/updating user record
     * @param   string   $role     Role of a user agent/admin/user
     *
     * @return  Array              Filtered array containing column
     *                             and its value                       
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function getDataArrayOfUser(User $user, Request $request, string $role):array
    {   
        $passwordString = str_random(8);
        $password = $user->password?:Hash::make($passwordString);
        $username = $this->getUserNameOfUser($user, $request);
        $name = $this->getFullNameOfUser($user, $request);
        $nameArray = explode(" ", $name);
        $firstName = $nameArray[0];
        $lastName = count($nameArray)>1?$nameArray[1]:null;
        return [
            'user_name' => $username,
            'password' => $password,
            'passwordString' => $passwordString,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $name,
            'email' => $this->getUserNullableFieldValue($request, $user, 'email'),
            'role' => $role,
            'mobile' => $this->getUserNullableFieldValue($request, $user, 'mobile'),
            'phone_number' => $this->getUserNullableFieldValue($request, $user, 'phone_number'),
            'iso'  => $request->iso,
            'country_code' => $request->input('code', $user->country_code),
            'agent_tzone' => $user->agent_tzone?:getGmt(true),
            'email_verify' => ($user->email_verify)?:'x'.str_random(59),
            'mobile_verify'=> ($user->mobile_verify)?:'x'.str_random(59),
            'ext' => $request->input('ext', $user->ext),
            'internal_note' => $request->input('internal_note', $user->internal_note),
        ];
    }

    /**
     * Function returns the value of field which should have some value or null except empty
     * strings based on the value available in the request or in database
     *
     * @param   User     $user     User to create or update
     * @param   Request  $request  $request data containing values for
     *                             creating/updating user record
     * @param   string   $field    field name in the request <=> column name in user table
     * @return  Mixed              string value of the column or null
     *
     */
    private function getUserNullableFieldValue(Request $request, User $user, string $field): ?string
    {
        return $request->input($field, $user->{$field})?:null;
    }

    /**
     * Method to return user name string for user record based on existing user
     * record and request data
     * User name preference are as below
     * 1: If request has user_name then return requests user_name
     * 2: If user is existing return user's existing user name
     * 3: If request has email then return email in request as username
     * 4: If request has mobile then return mobile in request as username
     *
     * Assumption: Request must have at-least one out of user_name, email and moblile
     *
     * @param   User     $user     User to create or update
     * @param   Request  $request  $request data containing values for
     *                             creating/updating user record
     * @return  string   $username username of the user
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function getUserNameOfUser(User $user, Request $request):string
    {
        $username = $request->input('user_name', $user->user_name);
        if(!$username) {
            $username = $request->input('email', $request->input('mobile'));
        }

        return $username;
    }

    /**
     * Method to return full name of a user record based on existing user
     * record and request data
     *
     * Assumption: Request will have either first_name or full_name
     *
     * @param   User     $user     User to create or update
     * @param   Request  $request  $request data containing values for
     *                             creating/updating user record
     *
     * @return  string             full name of the user
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function getFullNameOfUser($user, $request):string
    {
        $firstName = $request->input('first_name', $user->first_name);
        $lastName = $request->input('last_name', $user->last_name);
        return $request->input('full_name', $firstName." ".$lastName);
    }

    /**
     * Method to save custom form fields data for $user record
     * 
     * @param   User     $user    user whose custom data needs to be updated
     * @param   Request  $request request containing data to update
     *
     * @var     Array    $default array containing default column names for user
     *                            record
     * @var     Array    $extra   array containing extra request data of custom fields
     *
     * @return  void
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function saveUserCustomFormData(User $user, Request $request):void
    {
        $default = [
            "user_name", "full_name", "password", "password_confirmation",
            "code", "first_name", "last_name", "user_name", "phone_number",
            "organisation", "phone", "company", "email", "mobile","work_phone",
            "mobile_phone", "address", "department_name", "_token", "org_id", "internal_note", "ext"
        ];
        $extra = $request->except($default);
        //updating custom fields
        CustomFormValue::updateOrCreateCustomFields($extra, $user);
    }

    private function sendRegistrationAlert($user, $data)
    {
        $alert = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
        $registerNotifications['registration_notification_alert'] = [
            'userid'=>$user->id,
            'from' => $this->PhpMailController->mailfrom('1', '0'),
            'message' => ['subject' => null, 'scenario' => 'registration-notification'],
            'variable' => ['new_user_name' => $data['name'], 'new_user_email' => $user->email, 'user_password' => $data['passwordString']]
        ];
        //alert for agents and admin for new user registration
        $registerNotifications['new_user_alert'] = [
            'model'=>$user,
            'userid'=>$user->id,
            'from' => $this->PhpMailController->mailfrom('1', '0'),
            'message' => ['subject' => null, 'scenario' => 'new-user'],
            'variable' => ['new_user_name' => $data['name'], 'new_user_email' => $user->email, 'user_profile_link' =>faveoUrl('user/' . $user->id)]
        ];
        $notifications[]=$registerNotifications;
        $alert->setDetails($notifications);
    }

    /**
     * Method handles post login requests and login attempts.
     *
     * @param  LoginRequest   $request           
     *
     * @return Illuminate\Http\JsonResponse
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    public function postLogin(LoginRequest $request)
    {
        try{
            $this->checkSystemIsOnline();
            $validLdap2faCredential = false;
            $usernameinput = $request->email ?  $request->email :  $request->user_name;
            $password = $request->input('password');
            $this->handleLoginSessionAttempts($usernameinput);
            \Event::dispatch('login-data-submitting', [$request->all(),&$validLdap2faCredential]); //added 5/5/2016
            if($validLdap2faCredential == true) {
                return $this->handle2FactorLogin();
            }
            /**
             * If Auth is set means user has logged in via LDAP or other Login platforms
             * listening for login submission
             */
            if(Auth::check()) return $this->returnLoginSuccessResponse();
            
            // Finally attempt login at Faveo application level
            return $this->handleFaveoLoginAttempt($request, $usernameinput, $password);
        } catch(\Exception $e) {
            return $this->handleLoginExceptions($e);
        }
    }

    /**
     * Method handles login attempts for Faveo with application's database
     *
     * @param   LoginRequest   $request
     * @param   string         $username
     * @param   string         $password
     *
     * @return  Illuminate\Http\JsonResponse   login success and error response
     * @throws  VerificationRequiredException
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function handleFaveoLoginAttempt(LoginRequest $request, string $usernameinput, string $password)
    {
        $user = $this->checkAndReturnUserByUsernameIfExists($usernameinput);
        if (Auth::Attempt(['id' => $user->id, 'password' => $password], $request->input('remember'))) {
            if (Auth::user()->google2fa_secret && Auth::user()->is_2fa_enabled ==1) {

                return $this->handle2FactorLogin();
            }

            return $this->returnLoginSuccessResponse();
        }

        return errorResponse(Lang::get('lang.login_error_message'));
    }

    /**
     * Method returns success response for successful login attempt.
     *
     * @return Illuminate\Http\JsonResponse
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function returnLoginSuccessResponse()
    {
        \Event::dispatch('user.login', []);
        return successResponse('', ['redirect_url' => $this->redirectPath()]);
    }

    /**
     * Method checks if the user with given username exists then return that user.
     * 
     * @param   string    $username  username to search record in users table
     * @return  User      $user      user with given username
     *
     * @throws  Exception            throws exception if user with username does not exist
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function checkAndReturnUserByUsernameIfExists($username):User
    {
        $user = User::where('email', $username)->orwhere('user_name', $username)->first();
        if (!$user)
            throw new \Exception(Lang::get('lang.login_error_message'), FAVEO_ERROR_CODE);

        return $user;
    }

    /**
     * Method to handle and return response for the exceptions caught by
     * postLogin method.
     *
     * If exception is instance of VerificationRequiredException front-end is
     * expecting success response with redirect_url to show verification page.
     * 
     * This method handles VerificationRequiredException which can be thrown by
     * different login event listeners which can decide if something must be verified
     * before a user can actually login to the system. For example if email is set
     * required verified for login then we can write an event listener for login and
     * check if user's email is verified or not. If not verified then we will throw
     * VerificationRequiredException from the listener which will be handled here.
     *
     * @param  Exception    $e              exception caught in postLogin
     * @return Illuminate\Http\JsonResponse
     *
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function handleLoginExceptions(\Exception $e)
    {
        if (!$e instanceof VerificationRequiredException)
            return errorResponse($e->getMessage(), $e->getCode());

        return successResponse(trans('verification_required_for_login',['entity' => $e->getRequiredEntity()]), ['redirect_url' => $e->getRedirectURL(),
                            'email' => $e->getLoginUser()->email]);
    }

    /**
     * Method check and throws exception if the system is offline
     *
     * @return void
     * @throws Exception
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function checkSystemIsOnline():void
    {
        if (!System::where('id', 1)->value('status'))
            throw new \Exception(Lang::get('lang.system_currently_offline'), FAVEO_ERROR_CODE);
    }

    /**
     * Method handles login attempts in session and throws exception for failed login
     * attempts based on security settings.
     *
     * Note: This method is just a replica of code snippet removed from
     * old postLogin method except returning an error response it throws an
     * exception which gets caught in postLogin method.
     *
     * @param  string  $usernameinput
     *
     * @throws Exception
     * @since v4.0.0
     * @author Manish Verma <manish.verma@ladybirdweb.com>
     */
    private function handleLoginSessionAttempts($usernameinput):void
    {
        // try login
        $loginAttempts = 1;
        $value = $_SERVER['REMOTE_ADDR'];
        // If attempts > 3 and time < 30 minutes
        $security = Security::whereId('1')->first();
        // If session has login attempts, retrieve attempts counter and attempts time
        if (\Session::has('loginAttempts')) {
            $loginAttempts = \Session::get('loginAttempts');
            $loginAttemptTime = \Session::get('loginAttemptTime');

            // If attempts > 3 and time < 10 minutes
            if ($security && $loginAttempts > $security->backlist_threshold && (time() - $loginAttemptTime <= ($security->lockout_period * 60))) {
                    throw new \Exception($security->lockout_message, FAVEO_ERROR_CODE);
                    
            }
            // If time > 10 minutes, reset attempts counter and time in session
            if ($security && (time() - $loginAttemptTime) > ($security->lockout_period * 60)) {
                \Session::put('loginAttempts', 1);
                \Session::put('loginAttemptTime', time());
            }
        } else { // If no login attempts stored, init login attempts and time
            \Session::put('loginAttempts', $loginAttempts);
            \Session::put('loginAttemptTime', time());
            $this->clearLoginAttempts($value, $usernameinput);
        }
        // If auth ok, redirect to restricted area
        \Session::put('loginAttempts', $loginAttempts + 1);
    }

    /**
     * ---------------------------------------------------
     *          Old messy code block 
     * ---------------------------------------------------
     */
    public function licenseVerification(Request $request)
    {
        $host = \Config::get('database.connections.mysql.host');
        $username = \Config::get('database.connections.mysql.username');
        $password = \Config::get('database.connections.mysql.password');
        $database = \Config::get('database.connections.mysql.database');
        $code = $request->input('first') . $request->input('second') . $request->input('third') . $request->input('forth');
        //verify license (Auto PHP Licenser will determine when connection to your server is needed)
        $GLOBALS["mysqli"] = @mysqli_connect($host, $username, $password, $database);
         if (Schema::hasTable('faveo_license')) {
          //establish connection to MySQL database
            $license_notifications_array=  aplVerifyLicense($GLOBALS["mysqli"]);
        } else {
                Schema::create('faveo_license', function ($table) {
                    $table->increments('SETTING_ID');
                });
            \DB::table('faveo_license')->insert(['SETTING_ID'=>'1']);//create table and insert a row to pass table as array in next step for verification
            $license_notifications_array=  aplVerifyLicense($GLOBALS["mysqli"]);
         }
        if ($license_notifications_array['notification_case']=="notification_license_ok")//'notification_license_ok' case returned-operation succeeded
        {
         return successResponse(\Lang::get('lang.license_verified'));// do nothing (so user can continue using his script)
        } else {
        $url = url('/');
        if(\Schema::hasTable('faveo_license') && !\Schema::hasColumn('faveo_license','LICENSE_CODE') || $license_notifications_array['notification_case']!="notification_license_ok") {
            \Schema::drop('faveo_license');
        }
        $license_notifications_array=aplInstallLicense($url,"",$code, $GLOBALS["mysqli"]); //install personal (code-based) license using MySQL database
        if($license_notifications_array['notification_case'] == "notification_license_ok")
        {
            return successResponse(\Lang::get('lang.license_verified'));
        } else {
            return errorResponse($license_notifications_array['notification_text']);
        }
      }
    }

    public function redirectToProvider($provider, $redirect = '')
    {

        if ($redirect !== '') {
            $this->setSession($provider, $redirect);
        }
        //dd(\Config::get('services'));
        $s = Socialite::driver($provider)->redirect();
        //dd('dscd');
        return $s;
    }

    public function handleProviderCallback($provider)
    {
        try {
            //notice we are not doing any validation, you should do it
            $this->changeRedirect();

            $user = Socialite::driver($provider)->user();
            if ($user) {
                // stroing data to our use table and logging them in
                $username = $user->getEmail();
                $first_name = $user->getName();
                if ($user->nickname) {
                    $username = $user->nickname;
                }

                if (!$first_name) {
                    $first_name = $username;
                }

                $data = [
                    'first_name' => $first_name,
                    'email' => $user->getEmail(),
                    'user_name' => $username,
                    'role' => 'user',
                ];
                $this->userEmailIsSystemEmail($data['email']);
                $user = User::where('email', $data['email'])->orWhere('user_name', $data['user_name'])->first();
                if (!$user) {
                    $user =  User::firstOrCreate(array_merge($data,['is_delete' => 0, 'active' => 1]));
                    $this->setEntitiesUnverifiedByModel($user);
                }
                $this->setEntitiesVerifiedByColumnNames($user, ['email_verify']);
                Auth::login($user);
                return successResponse(trans('lang.successful'), ['redirect_url' => $this->redirectPath()]);
            }
            //after login redirecting to home page
            return redirect('/');
        } catch (\Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Function checks if the email is already in use as system email or not
     * @throws Exception
     * @since  v4.0.0
     * @author Manish Verma<manish.verma@ladybirdweb.com>
     * @return void 
     */
    private function userEmailIsSystemEmail(string $email):void
    {
        if((bool) DB::table('emails')->where('email_address', $email)->count()) {
            throw new \Exception(Lang::get('lang.email-moble-already-taken'), FAVEO_ERROR_CODE);
        }
    }

    /**
     * Get the form for registration.
     *
     * @return type Response
     */
    public function getRegister(CommonSettings $settings)
    {
        if ($settings->where('option_name', 'user_registration')->value('status') == '1') {
            return redirect()->to('404');
        }
        $email_mandatory = $settings->select('status')->where('option_name', '=', 'email_mandatory')->first();
        $aoption = $settings->select('option_value')->where('option_name', '=', 'login_restrictions')->first();
        \Event::dispatch(new \App\Events\FormRegisterEvent());
        if (Auth::user()) {
            if (Auth::user()->role == 'admin' || Auth::user()->role == 'agent') {
                return \Redirect::route('dashboard');
            } elseif (Auth::user()->role == 'user') {
                // return view('auth.register');
            }
        } else {
            return view('auth.register', compact('email_mandatory', 'aoption'));
        }
    }

    /**
     * Function to activate account.
     *
     * @param type $token
     *
     * @return type redirect
     */
    public function accountActivate($token)
    {
        $user = User::where('email_verify', '=', $token)->first();
        $login_restrictions = explode(',', getAccountActivationOptionValue());
        $active = 1;
        if (in_array('mobile', $login_restrictions)) {
            $active = 0;
        }

        if ($user) {
            $user->active = $active;
            $user->email_verify = 1;
            $user->save();
            // $this->openTicketAfterVerification($user->id);
            return redirect('/auth/login')->with('status', 'Account activated. Login to start');
        } else {
            return redirect('/auth/login')->with('fails', 'Invalid Token');
        }
    }

    /**
     * Get login page.
     *
     * @return type Response
     */
    public function getLogin()
    {
        $directory = base_path();
        if (file_exists($directory . DIRECTORY_SEPARATOR . '.env')) {
            if (Auth::user()) {
                if (Auth::user()->role == 'admin' || Auth::user()->role == 'agent') {
                    return \Redirect::route('dashboard');
                } elseif (Auth::user()->role == 'user') {
                    return \Redirect::route('home');
                }
            } else {
                return view('auth.login');
            }
        } else {
            return Redirect::route('licence');
        }
    }

    /**
     * Allows a user to log in and get access token
     * @param  LoginRequest $request
     * @return Response
     */
    public function postLoginAndGetToken(LoginRequest $request)
    {
      // Before this route file loads, we dynamically are changing auth guard to api
      // and forcing it to use
      // login cannot be done with `api` guard, since that guard is only build for
      // checking API token
      // https://github.com/laravel/passport/issues/665
      // so changing the guard to web again before performing login attempt
      \Config::set('auth.defaults.guard', 'web');
      $response = $this->postLogin($request);
      return $this->returnApiV3LoginResponse($response);
    }

    /**
     *
     * @return json \Illuminate\Http\Response
     */
    public function getValidateToken()
    {
    if (session('2fa:user:id')) {
           return successResponse('', ['redirect_url' => 'verify-2fa']);
        }

    }

    /**
     * Validates the Authenticator code entered is correct or not.
     * @param  App\Http\Requests\helpdesk\ValidateSecretRequest $request
     * @return json \Illuminate\Http\Response
     */
    public function postLoginValidateToken(ValidateSecretRequest $request)
    {
        try {
            // Before this route file loads, we dynamically are changing auth guard to api
            // and forcing it to use
            // login cannot be done with `api` guard, since that guard is only build for
            // checking API token
            // https://github.com/laravel/passport/issues/665
            // so changing the guard to web again before performing login attempt
            \Config::set('auth.defaults.guard', 'web');
            //get user id and create cache key

            //NOTE: we only need key of PPAuth value is just an illusion
            $ppAuth = $request->input('PPAuth');
            $key = array_keys($ppAuth)[0];

            if(!Cache::has($key)) return errorResponse('Session expired login again', FAVEO_TOKEN_EXPIRED_CODE);
  
            $user = User::findOrFail(Crypt::decrypt(cache($key)));
            $secret = Crypt::decrypt($user->google2fa_secret);
  
            if(!Google2FA::verifyKey($secret, $request->input('totp'))) {
                //persists PPAuth for next 120 second after wrong attempt 
                cache([$key => cache($key)], 120);
                return response(['totp' =>['Not a valid code']],FAVEO_VALIDATION_ERROR_CODE);// errorResponse('Not a valid code', FAVEO_VALIDATION_ERROR_CODE);
            }

            Auth::loginUsingId($user->id, $request->input('remember', false));
            //Remove PPAuth used for authenticatin while login
            Cache::forget($key);

            return $this->returnApiV3LoginResponse(successResponse('', ['redirect_url' => $this->redirectTo]));
        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }
       
    }

    public function postSetupValidateToken(Request $request)
    {
        $user = $request->user();
        $secret = Crypt::decrypt($user->google2fa_secret);
        $checkValidPasscode =  Google2FA::verifyKey($secret, $request->totp);
        if($checkValidPasscode == true) {
            $user->is_2fa_enabled = 1;
            $user->google2fa_activation_date = \Carbon\Carbon::now();
            $user->save();
            return successResponse(Lang::get('lang.valid_passcode'));
        }
        return errorResponse(Lang::get('lang.invalid_passcode'));
    }

     /**
     * If the url is for version 3 APIs (if it has v3 as prefix, it will be)
     * @return boolean
     */
    private function isV3Api() : bool
    {
       // check if url has v3 in it, it should be subjected to api middleware,
       // else web middleware
       $relativeUrl = str_replace(\Request::root()."/", '', \URL::current());

       return strpos($relativeUrl, 'v3/') !== false;
    }



    /**
     * Clear login attempt.
     *
     * @param type IPaddress $value
     *
     * @return type Response
     */
    public function clearLoginAttempts($value, $field)
    {
        $data = DB::table('login_attempts')->where('IP', '=', $value)->orWhere('User', '=', $field)->update(['attempts' => '0']);

        return $data;
    }

    /**
     * Get Failed login message.
     *
     * @return type string
     */
    protected function getFailedLoginMessage()
    {
        return Lang::get('lang.this_field_do_not_match_our_records');
    }

    /**
     *@category function to show verify OTP page
     *@param null
     *@return response|view
     */
    public function getVerifyOTP()
    {
        if (\Session::has('values')) {
            return view('auth.otp-verify');
        } else {
            return redirect('auth/login');
        }
    }

    /**
     *@category function to verify OTP
     *@param $request
     *@return int|string
     */
    public function verifyOTP(LoginRequest $request)
    {
        $user = User::select('id', 'mobile', 'country_code', 'user_name', 'mobile_verify', 'updated_at')->where('email', '=', $request->input('email'))
            ->orWhere('user_name', '=', $request->input('email'))->first();
        $otp_length = strlen($request->input('otp'));
        if (($otp_length == 6 && !preg_match("/[a-z]/i", $request->input('otp')))) {
            $otp2 = Hash::make($request->input('otp'));
            $date1 = date_format($user->updated_at, "Y-m-d h:i:sa");
            $date2 = date("Y-m-d h:i:sa");
            $time1 = new DateTime($date2);
            $time2 = new DateTime($date1);
            $interval = $time1->diff($time2);
            if ($interval->i > 30 || $interval->h > 0) {
                $message = Lang::get('lang.otp-expired');
            } else {
                if (Hash::check($request->input('otp'), $user->mobile_verify)) {
                    User::where('id', '=', $user->id)
                            ->update([
                                'mobile_verify' => '1',
                                'mobile' => $request->input('mobile'),
                                'active' => 1,
                                'country_code' => str_replace('+', '', $request->input('country_code'))
                            ]);
                    $this->openTicketAfterVerification($user->id);
                    return $this->postLogin($request);
                } else {
                    $message = Lang::get('lang.otp-not-matched');
                }
            }
        } else {
            $message = Lang::get('lang.otp-invalid');
        }
        $country_code = "auto";
        $code = CountryCode::select('iso')->where('phonecode', '=', str_replace('+', '', $request->input('country_code')))->first();
        if ($code) {
            $country_code = $code->iso;
        }
        return \Redirect::route('otp-verification')
                        ->withInput($request->input())
                        ->with(['values' => $request->input(),
                            'number' => $request->input('number'),
                            'name' => $user->user_name,
                            'user_id' => $user->id,
                            'code'  => $country_code,
                            'fails' => $message]);
    }

    public function resendOTP(OtpVerifyRequest $request)
    {
        try {
            $controller = $alert = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
            if ($controller->checkPluginSetup()) {
                $user_number_exists = User::select('id')->where('mobile', '=', $request->get('mobile'))->where('id', '!=', $request->get('user_id'))->count();
                if ($user_number_exists == 0) {
                    $user_details = User::select('email', 'first_name', 'last_name', 'user_name', 'role', 'user_language')->where('id', '=', $request->get('user_id'))->first()->toArray();
                    $otp = mt_rand(100000, 999999);
                    $otp_hash =  Hash::make($otp);
                    $user_details['mobile'] = $request->get('mobile');
                    $user_details['country_code'] = $request->get('code');
                    $message = [
                        'scenario' => 'otp-verification',
                    ];
                    User::where('id', '=', $request->get('user_id'))
                        ->update([
                            'mobile_verify' => $otp_hash
                        ]);
                    $variables = [
                       'otp_code' => $otp
                    ];
                    try {
                        $sms_controller = new \App\Plugins\SMS\Controllers\MsgNotificationController;
                        $sms_controller->notifyBySMS($user_details, $variables, $message, []);
                        return 1;
                    } catch (\Exception $e) {
                        $message = Lang::get('lang.otp-can-not-be-verified');
                        return response()->json(['error'=> $message]);
                    }
                } else {
                    $message = Lang::get('lang.mobile_number_already_verified');
                    return response()->json(['error' => $message]);
                }
            } else {
                $message = Lang::get('lang.error-sms-service-not-active');
                return response()->json(['error' => $message]);
            }
        } catch (\Exception $e) {
            $message = Lang::get('lang.otp-can-not-be-verified');
            return response()->json(['error' => $message]);
        }
    }

    /**
     * @category function to change ticket status when user verifies his account
     * @param int $id => user_id
     * @return null
     * @author manish.verma@ladybirdweb.com
     */
    public function openTicketAfterVerification($id)
    {
        // dd($id);
        $ticket = Tickets::select('id')
                ->where(['user_id' => $id, 'status' => 6])
                ->get();
        Tickets::where(['user_id' => $id, 'status' => 6])
                ->update(['status' => 1]);
        if ($ticket != null) {
            foreach ($ticket as $value) {
                $ticket_id = $value->id;
                Ticket_Thread::where('ticket_id', '=', $ticket_id)
                    ->update(["updated_at" => date('Y-m-d H:i:s')]);
            }
        }
    }


    public function changeRedirect()
    {
        $provider = \Session::get('provider');
        $url = \Session::get($provider . 'redirect');
        \Config::set("services.$provider.redirect", $url);
    }

    public function setSession($provider, $redirect)
    {
        $url = url($redirect);
        \Session::put('provider', $provider);
        \Session::put($provider . 'redirect', $url);
        $this->changeRedirect();
    }
      /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogout(Request $request)
    {
    try{

        \Event::dispatch('user.logout', []);
        if($request->has('notification_token')){
            $request->user()->notificationTokens()->where('token', $request->get('notification_token'))->update(['active' => 0]);
        }
        if(strpos($request->url(), '/v3/')) {
            /**
             * if API v3 is beig used we need to revoke the access token for the
             * authenticatd user. When auth gaurd middleware is not web Auth::
             * returns RequestGaurd which does not have "logout" method  so we just
             * need to revoke token instead of trying logout Auth user in session.
             */
            return $this->revokeAccessToken($request);
        }

        $login = new LoginController();
        $login->logout($request);
        return successResponse('',url('/'));

        } catch (\Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Function revokes access token linked to the bearer token provided in
     * the request.
     *
     * Assumption: The function handles the requests guarded by api:auth to
     * ensure bearer token is available in the request.
     *
     * General approach of revoking token is like below
     * $request->user()->token()->revoke();
     * Reason we are not using it to keep access tokens of different devices/apps alive i.e
     * if user uses the app in iPad and iPhone when he logs out from iPad he will still
     * be logged in iPhone using the acess token granted for iPhone.
     */
    private function revokeAccessToken(Request $request)
    {
        try {
            $value = $request->bearerToken();
            $token= (new Parser())->parse($value)->getHeader('jti');
            $token= $request->user()->tokens->find($token);
            $token->revoke();
            return successResponse('',url('/'));
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function redirectPath()
    {
        $auth = Auth::user();
        $sessionUrl = \Session::get('redirecturl');

        if ($auth && $auth->role!='user') {

         return $sessionUrl ? $sessionUrl : "dashboard";

        } else {
         return $sessionUrl ? $sessionUrl : "/";
        }
    }

    /**
     *
     *
     *
     */
    public function resendActivationLink(Request $request)
    {
        try {

            $validator = \Validator::make($request->all(), [
             'email'         =>  'required|unique:users,email|unique:emails,email_address',
            ]);
          if (!$validator->passes()) {
             $message = $validator->errors()->all();
             return ['response' => 'error','message' => $message[0]];
            }
           $user = User::where('id', '=', \Input::get('user_id'))->first();
            $code = str_random(60);
            $user->email_verify = $code;
            if (\Input::get('change') == "yes") {
                $user->email = \Input::get('email');
            }

            $user->save();
            $notifications[]=[
                'registration_alert'=>[
                    'userid'=> $user->id,
                    'from'=>$this->PhpMailController->mailfrom('1', '0'),
                    'message'=>['subject' => null, 'scenario' => 'registration'],
                    'variable'=>['new_user_name' => $user->name(), 'new_user_email' => $user->email, 'account_activation_link' => faveoUrl('account/activate/' . $code)],
                ]
            ];
            $alert = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
            $alert->setDetails($notifications);
            return ['response' => 'success', 'message' => Lang::get('lang.verify-email-message', ['email' => substr_replace($user->email, '***', strpos($user->email, '@')-3, 3)])];
        } catch (\Exception $e) {
            return ['response' => 'error', 'message' => $e->getMessage()];
        }
    }


    /**
     * made for vue frontend to work with user-data
     * NOTE: must be deleted once login functionality is implemented in vue
     * @return Response     Success with user data if logged in else failure
     */
    public function getLoggedInUserInfo(){
        $user = $this->getLoggedInClientInfo();

        return successResponse('',$user);
    }

    /**
     * made for vue frontend to work with user-data
     * NOTE: must be deleted once login functionality is implemented in vue
     * @return array     Success with user data if logged in else failure
     */
    public function getLoggedInClientInfo(){
        $user = [];
        $system = System::first();
        $user['date_format'] = dateTimeFormat();
       //for guest user
        if(!\Auth::check()){
            $user['timezone']  = $system->time_zone;//system timezone
            $user['user_data'] = [];
        }
        //for logged in user
        if(\Auth::check() && \Auth::user()->role == 'user'){
            $user['timezone']  = $system->time_zone;//system timezone
            $user['user_data'] = Auth::user();
        }
        //for admin or agent
        if(\Auth::check() && \Auth::user()->role != 'user'){
           $user['timezone'] = Timezone::where('id', \Auth::user()->agent_tzone)->first()->name;
           $user['date_format'] = dateTimeFormat();
           $user['user_data'] = Auth::user();
        }

        $status = CommonSettings::where('option_name', 'cdn_settings')->value('option_value');
        $user['base_url']= ($status == "0") ? url('/') : "https://cdn.faveohelpdesk.com" ;
        $user['system_url']=  url('/') ;

        return $user;
    }

            /**
     * made for vue frontend to work with user-data
     * NOTE: must be deleted once login functionality is implemented in vue
     * @return \Illuminate\Http\JsonResponse     Success with user data if logged in else failure
     */

        public function getActiveSocialLoginsList()
        {
            $socialLoginActiveProviders = SocialMedia::where('key','status')->where('value','1')->pluck('provider');

            $data = ['providers'=>$socialLoginActiveProviders];
            // emitting an event which can be used by any plugin to inject any data as service provider
            Event::dispatch('social-login-provider-dispatch', [&$data]);

            return successResponse('', $data);
        }

    /**
     * Function stores PPAuth key-value in Cache for 5 minutes, logs out the current authenticated
     * user and sends response back with redirect URL and PPAuth data for api and v3 api
     * 
     * @return Response
     */
    private function handle2FactorLogin()
    {
        $key = Str::random(64);
        $value = Crypt::encrypt($key);
        //setting random key and encrypted User id in Cache for 5 minutes(just in case users kept their phone in another room)
        cache([$key => Crypt::encrypt(Auth::user()->id)], 300);
        Auth::logout();
        return successResponse('', ['redirect_url' => "verify-2fa", "PPAuth" => [$key => $value]]);
    }

    /**
     * Function returns modified response(if required) for login when called via v3 api
     * 
     * @param Response $reponse default response to be sent if user is not authenticated
     *
     * @return Response
     */
    private function returnApiV3LoginResponse($response)
    {
        // if user is logged in, we will send the token, else send the whatever is the response
        if(Auth::user() && $this->isV3Api()){
            $token = Auth::user()->createToken('faveo')->accessToken;
            $userInfo = Auth::user()->only(['id','first_name','last_name','email','user_name']);
            $userInfo['token'] = $token;
            return successResponse('', $userInfo);
        }

        return $response;
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     */
    public function loginView()
    {
        return redirect()->to('auth/login');
    }
}
