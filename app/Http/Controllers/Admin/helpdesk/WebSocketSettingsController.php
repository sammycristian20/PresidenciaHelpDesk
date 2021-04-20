<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\WebSocketRequest;
/**
 * Class provides functionality to update and set Websocket configuration settings 
 * in .env file
 *
 * @author Manish Verma<manish.verma@ladybirdweb.com>
 * @package App\Http\Controllers\Admin\helpdesk
 * @since v4.0.0
 */
class WebSocketSettingsController extends Controller
{
	/**
	 * The list of keys which this class can access and update in .env 
	 * @var array
	 */
	private static $relatedKeys;

	/**
	 * Constructor to intialize private properties and enforce middleware
	 */
	public function __construct()
	{
		$this->middleware(['role.admin', 'install']);
		self::$relatedKeys = self::getRelatedKeysForWebSockets();
	}

	/**
	 * Function to decide which ENV variable can be accessed by the class
	 * and return them as an array.
	 *
	 * @return  array  Array containing list of ENV variables which are allowed
	 *                 to access and update.
	 */
	private static function getRelatedKeysForWebSockets():array
	{
		return [
			'BROADCAST_DRIVER',
			'LARAVEL_WEBSOCKETS_ENABLED',
			'LARAVEL_WEBSOCKETS_PORT',
			'LARAVEL_WEBSOCKETS_HOST',
			'LARAVEL_WEBSOCKETS_SCHEME',
			'PUSHER_APP_ID',
			'PUSHER_APP_KEY',
			'PUSHER_APP_SECRET',
			'SOCKET_CLIENT_SSL_ENFORCEMENT'
			// 'PUSHER_APP_CLUSTER',
		];
	}

	/**
	 * Function returns allowed ENV variables as key-value pair in JSON response
	 * 
	 * @return  Illuminate\Http\JsonResponse
	 */
	public function getWebSocketSettings()
	{
		try {
			$webSocketConfigData = [];
			//read env file and iterate to prepare array containing allowed variables  
			foreach (file(base_path('.env')) as $value) {
				$envValues = explode("=", $value);
				if(in_array($envValues[0], self::$relatedKeys)) {
					$webSocketConfigData[$envValues[0]] = str_replace("\n", '', $envValues[1]);
				}
			}

			return successResponse('', $webSocketConfigData);
		} catch(\Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Function updates the value of allowed ENV variables in .env file
	 *
	 * @param   WebSocketRequest  $request
	 * @return  Illuminate\Http\JsonResponse
	 */
	public function updateWebSocketSettings(WebSocketRequest $request)
	{
		try {
			$content = file_get_contents(base_path('.env'));
			foreach($request->only(self::$relatedKeys) as $key => $value)
			{
				$content = preg_replace("/\b$key=.*/", "{$key}={$value}", $content);
			}
			file_put_contents(base_path('.env'), $content);

			return successResponse(trans('lang.updated_successfully'));
		} catch(\Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	public function showWebSockets()
	{
		return view('themes.default1.admin.helpdesk.settings.web-socket.web-socket-settings');
	}
}
