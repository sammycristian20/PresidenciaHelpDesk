<?php

namespace App\Http\Controllers\Client\helpdesk;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Theme\Portal;
use App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\Company;
use App\Model\kb\Settings as KbSettings;
use App\Model\kb\Page;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Theme\Widgets;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Model\helpdesk\Agent_panel\Organization;
use Auth;





/**
 * get client layout data
 */
class ClientLayoutController extends Controller {

    /**
     * gets layout data for client panel
     */
    public function getLayoutData(){
        //TODO: cache implementation has to be done
        $data = [];
        $data['system'] = System::where('id',1)->first();
        $data['portal'] = $this->clientPanelColorSettings();
        $iconDetails = Portal::first(['icon','logo_icon_driver']);
        $data['icon'] = $iconDetails->icon;
        $data['is_enabled_breadcrumb'] = Portal::where('id', '1')->value('is_enabled_breadcrumb');

        $data['alert'] = Alert::whereIN('key',['browser_notification_status','api_id','browser-notification-status-inbuilt'])->select('key','value')->get();
        $data['company'] = Company::where('id', '=', '1')->select('company_name', 'website','logo','use_logo','logo_driver')->first();
        $data['kb_settings'] = KbSettings::where('id',1)->select('status','date_format')->first();
        $data['user_set_ticket_status'] = CommonSettings::where('option_name', '=', 'user_set_ticket_status')->select('status')->first();
        $data['user_registration'] = CommonSettings::where('option_name', '=', 'user_registration')->select('status')->first();
        $data['allow_users_to_create_ticket'] = CommonSettings::where('option_name', '=', 'allow_users_to_create_ticket')->select('status')->first();
        $data['pages']=(!Auth::guest() && Auth::user()->role != "user")? Page::where('status', '1')->get() : Page::where('status', '1')->where('visibility', '1')->get();
        // $data['pages'] = Page::where('status', '1')->where('visibility', '1')->select('name','description')->get();
        $data['social_widgets']=Widgets::select('name','title','value')->where('name','NOT LIKE',"%footer%")->where('value','!=',null)->where('value','!=',"")->get();
        $data['footer'] =  Widgets::select('name','title','value')->where('name','LIKE',"%footer%")->where('value','!=',null)->get();
        $data['cdn'] = $this->checkCdnStatus();
        $data['language'] = \App::getLocale();
        
        $data['copyright'] = (\Event::dispatch('helpdesk.apply.whitelabel')) ? null : "Faveo";
        $data['link'] = (\Event::dispatch('helpdesk.apply.whitelabel')) ? null : "https://www.faveohelpdesk.com/";
        \Event::dispatch('update_client_layout_data', [&$data]);
        if( !Auth::guest()){
             $checkId=User_org::where('user_id', Auth::user()->id)->where('role', 'manager')->pluck('org_id')->toArray();
                if(count($checkId)>0){
                     $data['organization'] = Organization::whereIn('id',$checkId)->select('name','id')->get();
                   }
            }
    
       return $data;
    }
    /**
     * get CDN status
     */
    private function checkCdnStatus()
    {
        $status = CommonSettings::where('option_name', 'cdn_settings')->value('option_value');
        return ($status == "0") ? false : true ;
    }
    /**
     * get Client panel color info
     */
    private function clientPanelColorSettings()
    {

        $portal = Portal::where('id', '1')->select('client_header_color', 'client_button_color', 'client_button_border_color', 'client_input_field_color')->first()->toArray();

        return $portal;
    }
 }
