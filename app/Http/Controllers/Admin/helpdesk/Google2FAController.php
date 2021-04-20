<?php

namespace App\Http\Controllers\Admin\helpdesk;

use Crypt;
use Google2FA;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use ParagonIE\ConstantTime\Base32;
use Lang;
use App\User;
use App\Model\helpdesk\Settings\System;

/**
 * This class handles Google 2FA functionality using Authenticator App for Faveo
 *
 * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
 */
class Google2FAController extends Controller
{
    use ValidatesRequests;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('web');
    }




    /**
     * Generates Security Key and the Bar code in base 64 format  
     * @param \Illuminate\Http\Request $request
     * @return json \Illuminate\Http\Response 
     */
    public function enableTwoFactor(Request $request)
    {
        //generate new secret
        $secret = $this->generateSecret();
        //get user
        $user = $request->user();

        //encrypt and then save secret
        $user->google2fa_secret = Crypt::encrypt($secret);
        $user->save();

        //generate image for QR barcode
        $imageDataUri = Google2FA::getQRCodeInline(
            System::first()->value('name'),
            $user->email,
            $secret,
            200
        );
        return successResponse('', ['image' => $imageDataUri,
            'secret' => $secret]);
        
    }

    /**
     * Disables 2FA for a user/agent, wipes out all the details related to 2FA from the Database.
     * 
     * @param \Illuminate\Http\Request $request
     * @return json \Illuminate\Http\Response
     */
    public function disableTwoFactor(Request $request, $userId= null)
    {
        if(\Auth::user()->role !== 'admin') {
            if($userId && $userId != \Auth::user()->id)
            {
                return errorResponse(trans('lang.permission_denied_action'));
            }
        } 
        $user = $userId != null ? User::where('id',$userId)->first() : $request->user();
        //make secret column blank
        $user->google2fa_secret = null;
        $user->google2fa_activation_date = null;
        $user->is_2fa_enabled = 0;
        $user->save();

        return successResponse(Lang::get('lang.2fa_disabled'));
    }

    /**
     * Generate a secret key in Base32 format
     *
     * @return string
     */
    private function generateSecret()
    {
        $randomBytes = random_bytes(10);

        return Base32::encodeUpper($randomBytes) ;
    }


    public function showVerifyPasswordPopup()
    {
        try {
            return successResponse('','password_confirmation_not_required');
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

     /**
     * Verify the user password is correct or not
     *
     * @return json
     */
    public function verifyPassword(Request $request)
    {
        try {
             $passwordVerified = false;
            \Event::dispatch('verify-user-credential',['password'=>$request->input('password'), &$passwordVerified]);
            // get authenticated user
           $user = \Auth::user();
           if ($passwordVerified == false && \Hash::check($request->input('password'), $user->getAuthPassword())) {
            \Session::forget('auth.password_confirmed_at'); 
            \Session::put('auth.password_confirmed_at', time()); 
            return successResponse('password_verified');
           } elseif($passwordVerified == true) {
            \Session::forget('auth.password_confirmed_at'); 
            \Session::put('auth.password_confirmed_at', time()); 
            return successResponse('password_verified');
           }
            return errorResponse('password_incorrect');
        } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
       
    }
}