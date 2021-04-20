<?php

namespace App\Api\v1;

use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Lang;
use App\Http\Controllers\Admin\helpdesk\SocialMedia\SocialMediaController;
use App\Http\Controllers\Auth\AuthController;
use App\Model\helpdesk\Settings\System;

/**
 * -----------------------------------------------------------------------------
 * Token authenticate Controller
 * -----------------------------------------------------------------------------.
 *
 *
 * @author Vijay Sebastian <vijay.sebastian@ladybirdweb.com>
 * @copyright (c) 2016, Ladybird Web Solution
 * @name Faveo HELPDESK
 *
 * @version v1
 */
class TokenAuthController extends Controller {

    public $PhpMailController;

    public function __construct()
    {
        $this->middleware('api');

        $PhpMailController = new PhpMailController();
        $this->PhpMailController = $PhpMailController;
    }

    /**
     * Authenticating user with username and password and retuen token.
     *
     * @param Request $request
     *
     * @return type json
     */
    public function authenticate(Request $request)
    {   
        $system = System::where('id',1)->value('status');
        $usernameinput = $request->input('username');
        $password = $request->input('password');
        $field = filter_var($usernameinput, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
        try {
            if (!$token = JWTAuth::attempt([$field => $usernameinput, 'password' => $password])) {
                return errorResponse(Lang::get('lang.invalid_credentials'), 401);
            }
        } catch (JWTException $e) {
            return errorResponse(Lang::get('lang.could_not_create_token'));
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
        $userid = \Auth::user()->id;
        $user = User::whereId($userid)->select('id', 'user_name', 'first_name', 'last_name', 'email', 'role', 'profile_pic')->first();

        if (!\Auth::user()->active) { //check if user exists or not
            return errorResponse(Lang::get('lang.this_account_is_currently_inactive'),401);
        }
        if($system == 0 && \Auth::user()->role != "admin"){
            return errorResponse(Lang::get('lang.system_currently_offline'));
        }
        if (\Auth::user()->is_delete) { // check is user account is deactivated( deleted)
               
            return errorResponse(Lang::get('lang.account-is-deactivated'),403);
        }
           
        return successResponse('', compact('token', 'user'));
    }

    /**
     * This methods  for logout Auth user
     * @param Request $request
     * @return type json
     */
    public function logout(Request $request)
    {

        try {
            $token = $request->input('token');
            JWTAuth::setToken($token);
            if (!JWTAuth::invalidate())
                return errorResponse(Lang::get('lang.invalid_token'));
        } catch (JWTException $e) {
            $error = $e->getMessage();
            return errorResponse($error);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return errorResponse($error);
        }
        return successResponse(Lang::get('lang.logged_out_successfully'));
    }

    /**
     * Get the user details from token.
     *
     * @return type json
     */
    public function getAuthenticatedUser()
    {
        //dd(JWTAuth::parseToken()->authenticate());
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found', 404]);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired', $e->getStatusCode()]);
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid', $e->getStatusCode()]);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent', $e->getStatusCode()]);
        } catch (\Exception $e) {
            $error = $e->getMessage();

            return response()->json(compact('error'));
        }

        return response()->json(compact('user'));
    }

    /**
     * Register a user with username and password.
     *
     * @param Request $request
     *
     * @return type json
     */
    public function register(\App\Http\Requests\helpdesk\RegisterRequest $request)
    {
        try {
            $user = new User();
            $PhpMailController = new PhpMailController();
            $social = new SocialMediaController();
            $authController = new AuthController($PhpMailController, $social);
            $result = $authController->postRegister($user, $request, true);
            return response()->json(compact('result'));
        } catch (Exception $ex) {
            $error = $ex->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * verify the url is existing or not.
     *
     * @return type json
     */
    public function checkUrl(Request $request)
    {
        try {
            $v = \Validator::make($request->all(), [
                        'url' => 'required|url',
            ]);
            if ($v->fails()) {
                $error = $v->errors();
                return response()->json(compact('error'));
            }

            $url = $this->request->input('url');
            $url = $url . '/api/v1/helpdesk/check-url';
        } catch (Exception $ex) {
            $error = $ex->getMessage();

            return response()->json(compact('error'));
        }
    }

    /**
     * to get forgot password link
     * @param Request $request
     * @return json
     */
    public function forgotPassword(Request $request)
    {
        try {
            $v = \Validator::make($request->all(), [
                        'email' => 'required|email|exists:users,email',
            ]);
            if ($v->fails()) {
                $error = $v->errors();

                return response()->json(compact('error'));
            }

            $date = date('Y-m-d H:i:s');
            $user = User::where('email', '=', $request->all('email'))->first();
            if (isset($user)) {
                $user1 = $user->email;
                //gen new code and pass
                $code = str_random(60);
                $password_reset_table = \DB::table('password_resets')->where('email', '=', $user->email)->first();
                if (isset($password_reset_table)) {
                    $password_reset_table = \DB::table('password_resets')->where('email', '=', $user->email)->update(['token' => $code, 'created_at' => $date]);
                } else {
                    $create_password_reset = \DB::table('password_resets')->insert(['email' => $user->email, 'token' => $code, 'created_at' => $date]);
                }

                $this->PhpMailController->sendmail($from = $this->PhpMailController->mailfrom('1', '0'), $to = ['name' => $user->user_name, 'email' => $user->email], $message = ['subject' => 'Your Password Reset Link', 'scenario' => 'reset-password'], $template_variables = ['user' => $user->user_name, 'email_address' => $user->email, 'password_reset_link' => url('password/reset/' . $code)]);
                $result = 'We have e-mailed your password reset link!';

                return response()->json(compact('result'));
            }
        } catch (Exception $ex) {
            $error = $ex->getMessage();

            return response()->json(compact('error'));
        }
    }

    public function socialLogins()
    {
        try {
            return \App\Model\helpdesk\Settings\SocialMedia::select('provider', 'key', 'value')
                            ->get()
                            ->toJson();
        } catch (Exception $ex) {
            $error = $ex->getMessage();
            return response()->json(compact('error'));
        }
    }

}
