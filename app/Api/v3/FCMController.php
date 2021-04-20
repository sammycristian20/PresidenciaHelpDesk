<?php

namespace App\Api\v3;

use Illuminate\Http\Request;
use App\Http\Controllers\Common\PushNotificationController;

/**
 * @package App\Api
 * @since v2.1.7
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @copyright Ladybird Web Solutions| admin@ladybirdweb.com
 */
class FCMController extends PushNotificationController
{
	/**
	 * Function uses legacy method of adding device token form FCM
	 * to send notifications to different devices using Push Notification
	 *
	 * @deprecated
	 */
	public function addDeviceTokenOld(Request $request)
	{
		return (new PushNotificationController)->fcmToken($request);
	}
}
