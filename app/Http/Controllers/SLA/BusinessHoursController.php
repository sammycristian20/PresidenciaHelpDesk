<?php

namespace App\Http\Controllers\SLA;

// controllers
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\BusinessRequest;
use App\Http\Requests\helpdesk\BusinessUpdateRequest;
use Illuminate\Http\Request;
// models
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Manage\Sla\BusinessHoursSchedules;
use App\Model\helpdesk\Manage\Sla\BusinessHoursSchedulesCustomTime;
use App\Model\helpdesk\Manage\Sla\BusinessHoliday;
use App\Model\helpdesk\Utility\Timezones;
use App\Model\helpdesk\Manage\Sla\SlaTargets;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Agent\Department;
//classes
use DB;
use Exception;
use Lang;

/**
 * SlaController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class BusinessHoursController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return type void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles');
    }

    /**
     * 
     * @return type
     */
    public function index()
    {
        return view('themes.default1.admin.helpdesk.manage.businesshours.index');
    }

    /**
     * 
     * @param Request $request
     * @return type json
     */
    public function getindex(Request $request)
    {
        try {

            $pagination = ($request->input('limit')) ? $request->input('limit') : 10;

            $sortBy = ($request->input('sort')) ? $request->input('sort') : 'id';
            $search = $request->input('search');
            $orderBy = ($request->input('order')) ? $request->input('order') : 'id';


            $baseQuery = BusinessHours::select('id', 'name', 'status', 'is_default')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('status', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);
            return successResponse($searchQuery);
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function create()
    {
        return view('themes.default1.admin.helpdesk.manage.businesshours.create');
    }

    /**
     * 
     * @return type
     */
    public function createApi()
    {

        $timezones = timezoneFormat();
        foreach ($timezones as $key => $value) {
            $displayTimezone[] = (['id' => $key, 'name' => $value]);
        }
        return successResponse('', ['data' => $displayTimezone]);
    }

    /**
     * 
     * @return type
     */
    public function edit()
    {
        return view('themes.default1.admin.helpdesk.manage.businesshours.edit');
    }

    /**
     * 
     * @param BusinessRequest $request
     * @return type string
     */
    public function store(BusinessRequest $request)
    {
        try {
            $businessHoursId = ($request->id) ? $request->id : null;

            if($businessHoursId){
               return $this->update($businessHoursId,$request);
            }

            $timezoneName = Timezones::where('id',$request->time_zone)->value('name');
            $businessHours = new BusinessHours;
            $businessHours->name = $request->name;
            $businessHours->description = $request->description;
            $businessHours->timezone = $timezoneName;
            $businessHours->status = $request->status;
            $businessHours->save();
            $day = '';

            if ($request->hours == 1) {
                for ($i = 0; $i < 7; $i++) {
                    $schedules = new BusinessHoursSchedules;
                    $schedules->business_hours_id = $businessHours->id;
                    $schedules->days = \Lang::get('lang.day' . $i);
                    $schedules->status = is_array($request->input('type' . $i)) ? "Open_custom" : $request->input('type' . $i);
                    $schedules->save();

                    if ($schedules->status == "Open_custom") {
                        $customValues = $request->input('type' . $i);
                        $openCustomTime = new BusinessHoursSchedulesCustomTime;
                        $openCustomTime->business_schedule_id = $schedules->id;
                        $openCustomTime->open_time = $customValues['from'];
                        $openCustomTime->close_time = $customValues['to'];
                        $openCustomTime->save();
                    }
                }
            } else {
                for ($i = 0; $i < 7; $i++) {
                    $schedules = new BusinessHoursSchedules;
                    $schedules->business_hours_id = $businessHours->id;
                    $schedules->days = \Lang::get('lang.day' . $i);
                    $schedules->status = 'Open_fixed';
                    $schedules->save();
                }
            }


            $holidayLists = $request->holiday;

            if ($holidayLists) {
                foreach ($holidayLists as $holidayList) {
                    $holiday = new BusinessHoliday;
                    $holiday->date = $holidayList['date'];
                    $holiday->name = $holidayList['description'];
                    $holiday->business_hours_id = $businessHours->id;
                    $holiday->save();
                }
            }

            if ($request->input('default_business_hours') == 'on') {
                BusinessHours::where('is_default', '>', 0)
                        ->update(['is_default' => 0]);
                BusinessHours::where('id', '=', $businessHours->id)
                        ->update(['is_default' => 1]);
            }

            /* redirect to Index page with Success Message */
            return successResponse(Lang::get('lang.business_hour_saved_successfully'));
        } catch (Exception $ex) {

            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * 
     * @param string $id 
     * @return type json
     */
    public function editApi($id)
    {
        try {

            $businessHours = BusinessHours::where('id', $id)->first();
            $schedulesbusinessHours = BusinessHoursSchedules::where('business_hours_id', $id)->orderBy('id', 'ASC')->get();
            foreach ($schedulesbusinessHours as $value) {
                $checkId = BusinessHoursSchedulesCustomTime::where('business_schedule_id', $value['id'])->value('id');
                $arrayCustom = ($checkId) ? BusinessHoursSchedulesCustomTime::where('id', $checkId)->select('open_time', 'close_time')->first()->toArray() : NULL;
                $workingHours[] = ($value['status'] != "Open_fixed") ? 1 : 0;
                $openTime = ($arrayCustom ) ? $arrayCustom['open_time'] : null;
                $closeTime = ($arrayCustom ) ? $arrayCustom['close_time'] : null;
                $displayDateWithTime[] = (['days' => $value['days'], 'status' => $value['status'], 'open_time' => $openTime, 'close_time' => $closeTime]);
            }
            $businessHolidays = BusinessHoliday::where('business_hours_id', $id)->select('name as description', 'date')->get()->toArray();
            $displayHours = ((count(array_unique($workingHours)) != 2) && array_unique($workingHours)[0] == 0) ? 0 : 1;
            $timezones[] = Timezones::where('name',$businessHours->timezone)->first();

            foreach ($timezones as $timezone) {
                //get list of timezone
                 $timeZonesList = timezoneFormat();
                 $displayTimezone = (['id' => $timezone->id, 'name'=>$timeZonesList[$timezone->id]]);
            }

            $default = ($businessHours->is_default == 1) ? "on" : "off";
            $displayData = (['name' => $businessHours->name, 'description' => strip_tags($businessHours->description), 'timezone' => $displayTimezone, 'status' => $businessHours->status, 'hours' => $displayHours, 'dateWithTime' => $displayDateWithTime, 'holidays' => $businessHolidays, 'default' => $default]);

            return successResponse('', $displayData);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method Update BusinessHours
     *
     * @param type int       $id
     * @param type SlaUpdate $request
     *
     * @return type Response string
     */
    public function update($id, Request $request)
    {
        try {
            $slaScheduleInfos = BusinessHoursSchedules::where('business_hours_id', $id)->get();
            if ($slaScheduleInfos) {
                foreach ($slaScheduleInfos as $slaScheduleInfo) {
                    BusinessHoursSchedulesCustomTime::wherebusiness_schedule_id($slaScheduleInfo->id)->delete();
                }
            }
            BusinessHoliday::where('business_hours_id', $id)->delete();
            BusinessHoursSchedules::where('business_hours_id', $id)->delete();
            $timezoneName = Timezones::where('id',$request->time_zone)->value('name');
            $businessHours = BusinessHours::where('id', $id)->first();
            $businessHours->name = $request->name;
            $businessHours->description = $request->description;
            $businessHours->timezone = $timezoneName;
            $businessHours->status = $request->status;
            $businessHours->save();
            $day = '';

            if ($request->hours == 1) {
                for ($i = 0; $i < 7; $i++) {
                    $schedules = new BusinessHoursSchedules;
                    $schedules->business_hours_id = $businessHours->id;
                    $schedules->days = \Lang::get('lang.day' . $i);
                    $schedules->status = is_array($request->input('type' . $i)) ? "Open_custom" : $request->input('type' . $i);
                    $schedules->save();


                    if ($schedules->status == "Open_custom") {
                        $customValues = $request->input('type' . $i);
                        $openCustomTime = new BusinessHoursSchedulesCustomTime;
                        $openCustomTime->business_schedule_id = $schedules->id;
                        $openCustomTime->open_time = $customValues['from'];
                        $openCustomTime->close_time = $customValues['to'];
                        $openCustomTime->save();
                    }
                }
            } else {
                for ($i = 0; $i < 7; $i++) {
                    $schedules = new BusinessHoursSchedules;
                    $schedules->business_hours_id = $businessHours->id;
                    $schedules->days = \Lang::get('lang.day' . $i);
                    $schedules->status = 'Open_fixed';
                    $schedules->save();
                }
            }
            $holidayLists = $request->holiday;
            if ($holidayLists) {
                foreach ($holidayLists as $holidayList) {
                    $holiday = new BusinessHoliday;
                    $holiday->date = $holidayList['date'];
                    $holiday->name = $holidayList['description'];
                    $holiday->business_hours_id = $businessHours->id;
                    $holiday->save();
                }
            }

            if ($request->input('default_business_hours') == 'on') {
                BusinessHours::where('is_default', '>', 0)
                        ->update(['is_default' => 0]);
                BusinessHours::where('id', '=', $businessHours->id)
                        ->update(['is_default' => 1]);
            }

            /* redirect to Index page with Success Message */
            return successResponse(Lang::get('lang.business_hours_edit_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('sla.business.hours.index')->with('fails', Lang::get('lang.sla_plan_can_not_update') . '<li>' . $e->getMessage() . '</li>');
        }
    }

    /**
     * This method deleting BusinessHours  
     * @param type $id of  business_hours
     * @return type  
     */
    public function businessHoursDelete($id)
    {
        try {
            OrganizationDepartment::where('business_hours_id', $id)->update(['business_hours_id' => null]);
            Department::where('business_hour', $id)->update(['business_hour' => 0]);
            $defaultBusinessHours = BusinessHours::where('is_default', 1)->first();
            $slaTarget = SlaTargets::where('business_hour_id', $id)->get();

            SlaTargets::where('business_hour_id', $id)->update(['business_hour_id' => $defaultBusinessHours->id]);
            BusinessHoliday::where('business_hours_id', $id)->delete();
            $slaScheduleInfos = BusinessHoursSchedules::where('business_hours_id', $id)->get();
            foreach ($slaScheduleInfos as $slaScheduleInfo) {
                if ($slaScheduleInfo->status == 'Open_custom') {
                    BusinessHoursSchedulesCustomTime::where('business_schedule_id', $slaScheduleInfo->id)->delete();
                }
            }
            BusinessHoursSchedules::where('business_hours_id', $id)->delete();
            BusinessHours::where('id', $id)->first()->delete();
            if ($slaTarget->count() > 0) {
                $Infomassage = $slaTarget->count() . ' ' . Lang::get('lang.sla_plan_moved_to_default_business_hours');


                return successResponse(Lang::get('lang.business_hours_delete_successfully'));
            } else {

                return successResponse(Lang::get('lang.business_hours_delete_successfully'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

}
