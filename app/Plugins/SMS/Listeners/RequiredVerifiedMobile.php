<?php

namespace App\Plugins\SMS\Listeners;

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
class RequiredVerifiedMobile
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
        /**
         * dummy listener handler for custom plugin.
         * Will update the option to handle it later.
         */
    }
}
