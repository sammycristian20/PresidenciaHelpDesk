<?php

namespace App\Http\Controllers\Admin\helpdesk\SocialMedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use App\Model\helpdesk\Settings\SocialMedia;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Http\Requests\helpdesk\ExternalLoginRequest;
use Lang;

class SocialMediaController extends Controller {

    public function __construct() {
        $this->middleware(['auth', 'roles'], ['except' => ['configService']]);
    }

    public function settings($provider) {
        try {
            $social = new SocialMedia();
            return view('themes.default1.admin.helpdesk.settings.social-media.settings', compact('social', 'provider'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function postSettings($provider, Request $request) {
        $this->validate($request, [
            'client_id' => 'required',
            'client_secret' => 'required',
            'redirect' => 'required|url',
        ]);
        try {
            $requests = $request->except('_token');
            $this->insertProvider($provider, $requests);
           // return redirect()->back()->with('success',Lang::get('lang.saved_successfully'));
            return \Redirect::route('social')->with('success', Lang::get('lang.saved_successfully'));
            
        } catch (Exception $ex) {
            // dd($ex);
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function deleteProvider($provider, $requests) {
        $social = new SocialMedia();
        $socials = $social->where('provider', $provider)->get();
        if ($socials->count() > 0) {
            foreach ($socials as $media) {
                if (array_key_exists($media->key,$requests)) {
                    $media->delete();
                }
            }
        }
    }

    public function insertProvider($provider, $requests = []) {
        $this->deleteProvider($provider, $requests);
        $social = new SocialMedia();
        foreach ($requests as $key => $value) {
            $social->create([
                'provider' => $provider,
                'key' => $key,
                'value' => $value,
            ]);
        }
    }

    public function index() {
        try {
            $social = new SocialMedia();
            return view('themes.default1.admin.helpdesk.settings.social-media.index', compact('social'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function configService() {
        $social = new SocialMedia();
        $services = $this->services();
        foreach ($services as $service) {
            \Config::set("services.$service.client_id", $social->getvalueByKey($service, 'client_id'));
            \Config::set("services.$service.client_secret", $social->getvalueByKey($service, 'client_secret'));
            \Config::set("services.$service.redirect", $social->getvalueByKey($service, 'redirect'));
        }
        // dd(\Config::get('services'));
    }

    public function services() {
        return [
            'facebook',
            'google',
            'github',
            'twitter',
            'linkedin',
            'bitbucket',
            'Custom'
        ];
    }

    public function getExternalSettings()
    {
        try{
            $external_login_settings = CommonSettings::whereIn('option_name', [
            'allow_external_login',
            'allow_users_to_access_system_url',
            'redirect_unauthenticated_users_to',
            'validate_token_api',
            'validate_api_parameter'
            ])->select('option_name', 'option_value', 'status')->get()->toArray();
            if (count($external_login_settings) != 5) {
                $external_login_settings = $this->getExternalLoginSettingsFirstTime();
            }
            $external_login_settings = collect($external_login_settings)->keyBy('option_name');
            // dd($external_login_settings);
            return view('themes.default1.admin.helpdesk.settings.social-media.external', compact('external_login_settings'));            
        } catch (\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function postExternalSettings(ExternalLoginRequest $request)
    {
        try {
            CommonSettings::updateOrCreate(['option_name' => 'allow_external_login'],['option_name' => 'allow_external_login', 'status' => $request->get('allow_external_login')]);
            CommonSettings::updateOrCreate(['option_name' => 'allow_users_to_access_system_url'],['option_name' => 'allow_users_to_access_system_url', 'status' => $request->get('allow_users_to_access_system_url')]);
            CommonSettings::updateOrCreate(['option_name' => 'redirect_unauthenticated_users_to'],['option_name' => 'redirect_unauthenticated_users_to', 'option_value' => $request->get('redirect_unauthenticated_users_to')]);
            CommonSettings::updateOrCreate(['option_name' => 'validate_token_api'],['option_name' => 'validate_token_api', 'option_value' => $request->get('validate_token_api')]);
            CommonSettings::updateOrCreate(['option_name' => 'validate_api_parameter'],['option_name' => 'validate_api_parameter', 'option_value' => $request->get('validate_api_parameter')]);
            return redirect()->back()->with('success', Lang::get('lang.updated_successfully'));  
        } catch (\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
        
    }

    public function getExternalLoginSettingsFirstTime()
    {
        return [[  
                "option_name" => "allow_external_login",
                "option_value" => "",
                "status" => 0,
            ],
            [  
                "option_name" => "allow_users_to_access_system_url",
                "option_value" => "",
                "status" => 1,
            ],
            [  
                "option_name" => "redirect_unauthenticated_users_to",
                "option_value" => "",
                "status" => "",
            ],
            [  
                "option_name" => "validate_token_api",
                "option_value" => "",
                "status" => "",
            ],
            [  
                "option_name" => "validate_api_parameter",
                "option_value" => "",
                "status" => "",
            ]
        ];
    }
}
