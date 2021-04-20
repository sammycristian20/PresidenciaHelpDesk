<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Exceptions\VerificationRequiredException;
use Auth;
use App\Model\helpdesk\Email\Emails;
use App\Traits\UserVerificationHelper;
/**
 * Listener class for handling login event
 * @package App\Listeners
 * @since v4.0.0
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 */
class RequiredVerifiedMail
{
	use UserVerificationHelper;

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
     * @throws  VerificationRequiredException 
     */
	public function handle(Login $login)
    {
    	//will be true if email is required else false
    	$emailRequired = !(strpos(commonsettings('login_restrictions', ''), "email")===false);
    	//will be true if user has not verified the email else true
    	$emailNotVerifyed = !$login->user->email_verified;
    	//will be true if at least one email is setup for sending emails out
        $canSendEmails = (bool)Emails::where('sending_status', 1)->count();
        //If all of the above variables are true only then throw  VerificationRequiredException
        if ($emailRequired+$emailNotVerifyed+$canSendEmails==3) {
            $this->sendVerificationEmail($login->user);
        	Auth::logout();
        	throw new VerificationRequiredException("verify-email", "email", $login->user);
        }
    }
}
