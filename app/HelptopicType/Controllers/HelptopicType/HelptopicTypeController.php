<?php

namespace App\HelptopicType\Controllers\HelptopicType;



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
class HelptopicTypeController extends Controller {


    /**
     * 
     * @return type
     */
    public function index() {
        try {
           $check_settings=CommonSettings::where('option_name','=', 'helptopic_link_with_type')->first();

           if($check_settings){
             $settings= $check_settings->status;
           }
           else{
            $settings= 0;
           }
         return view('HelptopicType::HelptopicType.index',compact('settings'));

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
        $helptopic_link_with_type_status= $request->current_settings;
        // dd($micro_organization_status);
        $check_option_name=CommonSettings::where('option_name','=', 'helptopic_link_with_type')->first();

        if($check_option_name){
             CommonSettings::where('option_name','=', 'helptopic_link_with_type')->update(['status' => $helptopic_link_with_type_status]);
        }
        else{
            $new_common_settings= new CommonSettings();
            $new_common_settings->option_name ='helptopic_link_with_type';
            $new_common_settings->status = $helptopic_link_with_type_status;
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
