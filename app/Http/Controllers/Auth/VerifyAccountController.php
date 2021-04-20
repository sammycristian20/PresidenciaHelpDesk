<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\helpdesk\Common\UpdateEmailRequest;
use App\Http\Requests\helpdesk\Common\ResendEmailVerifyRequest;
use Lang;
use Validator;
use Auth;
use Hash;
use App\Traits\UserVerificationHelper;


/**
 * This controller handles the verification of email for new users.
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class VerifyAccountController extends Controller
{
    use UserVerificationHelper;
    /**
     *post updateEmail by the passed token
     *@param $request Request (oldEmail, newEmail)
     *@return Response
    */
    public function postUpdateEmailVerification(UpdateEmailRequest $request)
    {
        try {
            $user = User::where('email',  $request->oldEmail)->first();
            if(Hash::check($request->password, $user->password)) {
                $user->email = $request->email_address;
                $user->save();
                $this->sendVerificationEmail($user);
                return successResponse(Lang::get('lang.verify-email-message'));
            }
            
            return errorResponse(trans('lang.invalid_attempt'));
        } catch(\Exception $e) {

            return errorResponse(trans('lang.invalid_attempt'));
        }
    }

    public function sendEmailVeirifcation(ResendEmailVerifyRequest $request)
    {
        try {
            $user = User::where('email',  $request->email)->first();
            if($user && !$user->email_verified) {
                $this->sendVerificationEmail($user);
            }

            return successResponse(Lang::get('lang.verify-email-message'));
        } catch(\Exception $e) {

            return errorResponse(trans('lang.invalid_attempt'));
        }
    }

    /**
     *post verifyEmail by passed token
     *@param string $token
     *@return Response
    */
    public function postAccountActivate($token)
    {   
        $auth = Auth::user();
        $user = User::where('email_verify', $token)->first();
        
        if (!is_null($auth)) {
            if (($auth->active == 1) && ($auth->role != 'user')) {
                return errorResponse(['redirect_to' => faveoUrl('dashboard')], 302);
            }
            if (($auth->active == 1) && ($auth->role == 'user')) {
                return errorResponse(['redirect_to' => faveoUrl('')], 302);
            }
        }

        if ($token == 1) {
            return errorResponse(Lang::get('lang.sorry_you_are_not_allowed_token_expired'));
        }

        if (!$user) {
            return errorResponse(Lang::get('lang.user_not_found'));
        }
    
        $user->update(['active'=>1, 'email_verify'=>1]);

        return successResponse('Your email: '.$user->email. ' is verified and account activated.');

    }
}