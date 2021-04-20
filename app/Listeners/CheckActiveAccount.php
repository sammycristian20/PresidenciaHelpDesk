<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Auth;
/**
 * Listener class for handling login event
 * @package App\Listeners
 * @since v4.0.0
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 */
class CheckActiveAccount
{
	function __construct()
	{
		# code...
	}

    /**
     * Method listens for successful login attempt and throws VerificationRequiredException
     * if users email is not verified, system has email configured to send outgoing mails and
     * email verification is required for user login.
     *
     * @param   Login     $login
     * @throws  Exception 
     */
	public function handle(Login $login)
    {
    	$accountNotActive = !$login->user->active;//true if user is not active else false
        $accountDeleted = $login->user->is_delete;//true if user is deleted else false
        //if any of $accountNotActive or $accountDeleted is true below check will pass
        if($accountNotActive+$accountDeleted>0) 
        {
            Auth::logout();
            throw new \Exception(trans('lang.account-is-deactivated'), FAVEO_ERROR_CODE);
        }
    }
}

