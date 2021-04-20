<?php

namespace App\Location\Controllers\Location;

use App\Model\helpdesk\Agent\Department;
use Illuminate\Http\Request;
use Lang;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use Exception;
use App\Location\Models\Location;
use Auth;
use DB;
use App\Location\Requests\LocationRequest;
use App\Location\Requests\LocationUpdateRequest;
use App\Model\helpdesk\Ticket\Ticket_source;
use App\Model\helpdesk\Ticket\Ticket_Form_Data;
use App\User;
use App\Model\helpdesk\TicketRecur\RecureContent;
use App\Model\helpdesk\Ticket\Tickets;

class LocationController extends Controller {

    /**
     * 
     * @return type
     */
    public function index()
    {
        try {
            $locations = Location::all();
            return view('location::location.index', compact('locations'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function getLocation()
    {
        try {
            $location = Location::all();


            return \Datatable::Collection($locationcategory)
                            ->showColumns('title', 'email', 'phone', 'address')
                            ->addColumn('action', function($model) {
                                return "<a href=" . url('helpdesk/location-types/' . $model->id . '/edit') . " class='btn btn-primary btn-xs'> <i class='fa fa-edit' style='color:white;'>&nbsp;&nbsp;Edit</a> 


                                <a href=" . url('helpdesk/location-types/' . $model->id . '/show') . " class='btn btn-primary btn-xs'><i class='fa fa-eye' style='color:white;'>&nbsp;&nbsp;View</a>";
                            })
                            ->searchColumns('title', 'email', 'phone', 'address')
                            ->orderColumns('title', 'email', 'phone', 'address')
                            ->make();
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function create()
    {
        try {
            $departments = Department::all(array('id', 'name'));

            $organizations = \App\Model\helpdesk\Agent_panel\Organization::pluck('name', 'id')->toArray();
            return view('location::location.create', compact('departments', 'organizations'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param CreateLocationRequest $request
     * @return type
     */
    public function handleCreate(LocationRequest $request)
    {

        try {

            $hdLocation = new Location;
            $hdLocation->title = $request->title;
            $hdLocation->email = $request->email;
            $hdLocation->phone = $request->phone;
            $hdLocation->address = $request->address;
            $hdLocation->save();

            if ($request->input('default_location') == "1") {
                Location::where('is_default', '>', 0)
                        ->update(['is_default' => 0]);
                Location::where('id', $hdLocation->id)
                        ->update(['is_default' => 1]);
            }
            return \Redirect::route('helpdesk.location.index')->with('message', Lang::get('lang.location_created_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function edit($id)
    {
        try {

            $hdLocation = Location::findOrFail($id);

            return view('location::location.edit', compact('hdLocation'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param CreateLocationRequest $request
     * @return type
     */
    public function handleEdit($id, LocationUpdateRequest $request)
    {
        try {

            $hdLocation = Location::findOrFail($id);
            $hdLocation->email = $request->email;
            $hdLocation->title = $request->title;
            $hdLocation->phone = $request->phone;
            $hdLocation->address = $request->address;
            $hdLocation->save();
            if ($request->input('default_location') == 'on') {
                Location::where('is_default', '>', 0)
                        ->update(['is_default' => 0]);
                Location::where('id', $id)
                        ->update(['is_default' => 1]);
            }
            return \Redirect::route('helpdesk.location.index')->with('message', Lang::get('lang.location_updated_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function handledelete($id)
    {
        try {
            $locationName = Location::where('id', $id)->value('title');
            User::where('location', $locationName)->update(['location' => '']);
            RecureContent::where('value', $locationName)->where('option', 'location')->update(['value' => '']);
            Tickets::where('location_id', $id)->update(['location_id' => null]);
            Location::where('id', $id)->delete();
            return \Redirect::route('helpdesk.location.index')->with('message', Lang::get('lang.location_deleted_successfully'));
        } catch (Exception $ex) {

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $locations = new SdLocations();
            $location = $locations->find($id);
            if ($location) {
                return view('service::location.show', compact('location'));
            } else {
                throw new \Exception('Sorry we can not find your request');
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function getLocationsForForm(Request $request)
    {
        $html = "<option value=''>Select</option>";
        $orgid = $request->input('org');
        $location = $this->getLocationsByOrg($orgid);
        $locations = $location->pluck('title', 'id')->toArray();
        if (count($locations) > 0) {
            foreach ($locations as $key => $value) {
                $html .= "<option value='" . $key . "'>" . $value . "</option>";
            }
        }
        return $html;
    }

    public function getLocationsByOrg($orgid)
    {
        $location = new SdLocations();
        $locations = $location->where('organization', $orgid);
        return $locations;
    }

}
