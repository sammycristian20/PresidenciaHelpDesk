<?php

namespace App\MicroOrganization\Controllers\MicroOrganization;



use App\Model\helpdesk\Agent\Department;

use Exception;
use Illuminate\Http\Request;
use Lang;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
//use Exception;
// use App\Location\Models\Location;
use Auth;
use DB;
// use Illuminate\Http\Request;
// use App\Location\Requests\LocationRequest;
// use App\Location\Requests\LocationUpdateRequest;
class MicroOrganizationController extends Controller {


    /**
     * 
     * @return type
     */
    public function index() {
        try {
           $micro_org_settings=CommonSettings::where('option_name','=', 'micro_organization_status')->first();

           if($micro_org_settings){
             $micro_org_settings= $micro_org_settings->status;
           }
           else{
            $micro_org_settings= 0;
           }
           // dd( $locations);
            return view('MicroOrganization::MicroOrganization.index',compact('micro_org_settings'));

        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

     /**
     * Show the Inbox ticket list page.
     *
     * @return type response
     */


    public function settings(Request $request) {
          try {
        $micro_organization_status= $request->micro_organization_settings;
        // dd($micro_organization_status);
        $check_option_name=CommonSettings::where('option_name','=', 'micro_organization_status')->first();

        if($check_option_name){
             CommonSettings::where('option_name','=', 'micro_organization_status')->update(['status' => $micro_organization_status]);
        }
        else{
            $new_common_settings= new CommonSettings();
            $new_common_settings->option_name ='micro_organization_status';
            $new_common_settings->status = $micro_organization_status;
            $new_common_settings->save();
        }

        // updateOrCreate
        // CommonSettings::where('option_name','=', 'micro_organization_status')->update(['status' => $micro_organization_status]);
     return Lang::get('lang.your_status_updated_successfully');
         } catch (Exception $e) {
            return Redirect()->back()->with('fails', $e->getMessage());
        }
    }
   

}
