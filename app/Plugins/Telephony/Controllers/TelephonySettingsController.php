<?php
namespace App\Plugins\Telephony\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\SyncPluginToLatestVersion;
use App\Plugins\Telephony\Model\TelephonyProvider;
use App\Plugins\Telephony\Request\TelephonyProviderRequest;
use App\Model\helpdesk\Utility\CountryCode;
use Logger;
use Auth;

/**
 * Basic settings class which handles CRUD functionality of telephony settings 
 * options.
 *
 * @author Manish Verma<manish.verma@ladybirdweb.com>
 * @package App\Plugins\Telephony\Controllers
 * @since v3.0.0
 */
class TelephonySettingsController extends Controller
{
	function __construct()
	{
		(new SyncPluginToLatestVersion)->sync('Telephony');
	}

	/**
	 * Function returns telephone setting view page
	 *
	 */
	public function showSettings()
	{
		return view('telephone::settings');
	}

	/**
	 * Function to get telephony service provider list
	 * @param  Request           $request
	 * @param  TelephonyProvider $providers
	 * @return JSON response 
	 */
	public function getProviderList(Request $request, TelephonyProvider $providers)
	{
		try {
			$paginator = $providers->select('name', 'id', 'short')->orderBy('name', 'asc')->paginate(5);
			$paginator->getCollection()->transform(function ($value) {
    			$value->base_url = url('/')."/telephone/".$value->short."/pass";
    			return $value;
			});
			return successResponse("", $paginator);
		} catch(\Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Function to get provider settings details
	 * @param  string  $provider
	 * @return JSON response
	 */
	public function getProviderDetails(TelephonyProvider $provider)
	{
		try {
			$data = [];
			foreach ($provider->toArray() as $key => $value) {
				if($key == 'iso') $value = ['iso'=> $value, 'name' => CountryCode::where('iso', $value)->value('name')];
				array_push($data, ['key'=>$key, 'value'=>$value]);
			}

			return successResponse('', $data);
		} catch(\Exception $e) {
			Logger::exception($e);
			errorResponse('sorry_something_went_wrong');
		}
	}

	/**
	 * Function to update provider settings details
	 * @param  Request  $provider
	 * @return JSON response
	 */
	public function updateProviderDetails(TelephonyProvider $provider, TelephonyProviderRequest $request)
	{
		try{
			$provider->update($request->except(['name', 'short']));

			return successResponse(trans('lang.updated_successfully'));
		} catch(\Exception $e) {
			Logger::exception($e);
			errorResponse('sorry_something_went_wrong');
		}
	}

	/**
	 * Function to get region list
	 * @param  string  $provider
	 * @return JSON response
	 */
	public function getCountriesWithIsoCode(Request $request)
	{
		try {
			$search = $request->input('search-query', '');
			$limit = $request->limit ?: 10;
			$regions = CountryCode::when($search, function($q) use($search) {
				$q->where('name', 'LIKE' ,"%$search%")->orWhere('iso','LIKE', "%$search%");
			})->select('name', 'iso')->simplePaginate($limit);
			return successResponse('', $regions);
		} catch (\Exception $e) {
			return errorResponse($e->getMessage());
		}
	}

	/**
	 * Function to handle script listeners for rendering telephony.js
	 */
	public function echoTelephonyScript()
	{
		if(env('LARAVEL_WEBSOCKETS_ENABLED') && Auth::user()) {
			$userId = Auth::user()->id;
			echo "<div id=\"telephony-settings\">
                <telephone-alert :user=\"$userId\"></telephone-alert>
            </div>
            <script src=".bundleLink('js/telephony.js')."></script>";
		}
	}
}
