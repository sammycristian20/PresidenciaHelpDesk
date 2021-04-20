<?php

namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App\Facades\Attach;
use App\FileManager\Models\FileManagerAclRule;
use App\FileManager\Models\FileManagerAclRuleDepartment;
use App\Http\Controllers\Controller;
// requests
use App\Http\Requests\helpdesk\CompanyRequest;
use App\Http\Requests\helpdesk\EmailRequest;
use App\Http\Requests\helpdesk\FileSystemSettingsRequest;
use App\Http\Requests\helpdesk\RatingRequest;
use App\Http\Requests\helpdesk\RatingUpdateRequest;
use App\Http\Requests\helpdesk\StatusRequest;
use App\Http\Requests\helpdesk\SystemRequest;
use App\Http\Requests\helpdesk\RecaptchaRequest;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\Tickets;
// models
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Email\Template;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Notification\Notification;
use App\Model\helpdesk\Ratings\Rating;
use App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Settings\Email;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Utility\Date_format;
use App\Model\helpdesk\Utility\Date_time_format;
use App\Model\helpdesk\Utility\Time_format;
use App\Model\helpdesk\Utility\Timezones;
use App\Model\helpdesk\Workflow\WorkflowClose;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Settings\Ticket as TicketSettings;
use DateTime;
use App\Model\helpdesk\Theme\Portal;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
// classes
use DB;
use Exception;
use File;
use Illuminate\Http\Request;
use Input;
use Lang;
use App\Http\Requests\helpdesk\Job\TaskRequest;
use Carbon\Carbon;
use Cache;
use App\Http\Controllers\Update\SyncFaveoToLatestVersion;
use App\Http\Requests\helpdesk\TicketSettings\TicketSettingsRequest;

/**
 * SettingsController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class SettingsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->smtp();
        $this->middleware('auth');
        $this->middleware('roles');
    }

   /**
     *
     * get the form for company setting page
     *
     * @return html
     */
    public function getCompanyPage()
    {
        try {
            /* Direct to Company Settings Page */
            return view('themes.default1.admin.helpdesk.settings.company');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method return company details
     *
     * @return Response json
     */

    public function getCompanyDetails()
    {
        try {
            $company = Company::select('company_name', 'website', 'phone', 'address', 'logo as clientlogo', 'use_logo as uselogo', 'logo_driver')
                ->first();

            $company['uselogo'] = (bool)$company->uselogo;

            $company['clientlogo'] = $company->clientlogo ?: null;

            $portal = Portal::select('admin_header_color', 'agent_header_color', 'client_header_color', 'client_button_color', 'client_button_border_color', 'client_input_field_color', 'logo', 'icon','logo_icon_driver','is_enabled_breadcrumb')->first();
            
            $portal['defaultlogo'] = !$portal->getOriginal('logo');

            $portal['defaulticon'] = !$portal->getOriginal('icon');
           
 
            $outputDataArray = array_merge($company->toArray(), $portal->toArray());

            return successResponse($outputDataArray);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method Update company details
     *
     * @param type Company        $company
     * @param type CompanyRequest $request
     *
     * @return Response
     */
    public function postCompany(Company $company, CompanyRequest $request)
    {
        /* fetch the values of company request  */
        $company = $company->first();
        $portal = Portal::first();
        if (Input::file('icon')) {
            $path = Attach::put('company_logos/icons', Input::file('icon'), null, null, true, 'public');
            $iconFileName = Attach::getUrlForPath($path, null, 'public');
        } else {
            $iconFileName = $request->icon ? $portal->getOriginal('icon') : null;
        }

        if (Input::file('logo_admin_agent')) {
            $path = Attach::put('company_logos/logos', Input::file('logo_admin_agent'), null, null, true, 'public');
            $logoFileName = Attach::getUrlForPath($path, null, 'public');
        } else {
            $logoFileName = $request->logo_admin_agent ?$portal->getOriginal('logo') : null;
        }

        $portal = Portal::where('id', 1)->update(['admin_header_color' => $request->admin_header_color, 'agent_header_color' =>
            $request->agent_header_color, 'client_header_color' => $request->client_header_color, 'client_button_color' =>
            $request->client_button_color, 'client_button_border_color' => $request->client_button_border_color, 'client_input_field_color' => $request->client_input_field_color, 'logo' => $logoFileName, 'icon' => $iconFileName, 'is_enabled_breadcrumb' => $request->is_enabled_breadcrumb]);

        /* Check whether function success or not */
        try {
            $company->use_logo =  $request->uselogo;

            if (Input::file('logo')) {
                $path = Attach::put('company_logos/logos', Input::file('logo'), null, null, true, 'public');
                $company->logo = Attach::getUrlForPath($path, null, 'public');
            }

            $company->fill($request->except(['logo']))->save();
            /* redirect to Index page with Success Message */
            return successResponse(Lang::get('lang.company_settings_saved_successfully'));
        } catch (Exception $ex) {
            /* redirect to Index page with Fails Message */
            return errorResponse($ex->getMessage());
        }
    }
    /**
     * function to delete system logo.
     *
     *  @return type string
     */
    public function deleteLogo(Request $request)
    {
        $path = $request->input('data1'); //get file path of logo image
        if (!unlink($path)) {
            return 'false';
        } else {
            $companys = Company::where('id', '=', 1)->first();
            $companys->logo = null;
            $companys->use_logo = '0';
            $companys->save();

            return 'true';
        }
        // return $res;
    }

    /**
     * get the form for Ticket setting page.
     *
     * @param type Ticket     $ticket
     * @param type Sla_plan   $sla
     * @param type Help_topic $topic
     * @param type Priority   $priority
     *
     * @return type Response
     */
    public function getticket(Ticket $ticket, Help_topic $topic)
    {
        try {
            /* fetch the values of ticket from ticket table */
            $tickets = $ticket->whereId('1')->first();
            /* Fetch the values from SLA Plan table */
            /* Fetch the values from Help_topic table */
            $topics = $topic->get();
            $status = new \App\Model\helpdesk\Ticket\Ticket_Status;

            $customTicketEnforcements = Ticket::whereId('1')->value('custom_field_name');

            /* Direct to Ticket Settings Page */
            return view('themes.default1.admin.helpdesk.settings.ticket', compact('tickets', 'topics', 'status', 'customTicketEnforcements'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * method to update ticket settings data.
     *
     * @param Ticket  $ticket
     * @param TicketSettingsRequest $request
     *
     * @return Response
     */
    public function postticket(Ticket $ticket, TicketSettingsRequest $request)
    {   
        $ticket = $ticket->whereId('1')->first();
        $extraField=$request->custom_field_value;

        $customField=null;
        if ($extraField) {
            foreach ($extraField as $key) {
                $field[]=$key;
            }
            $customField=implode(",", $field);
        }
        $ticket->custom_field_name = $customField;

        /* fill the values to coompany table */
        $ticket->fill($request->except('captcha', 'claim_response', 'assigned_ticket', 'answered_ticket', 'agent_mask', 'html', 'client_update'))->save();
        /* insert checkbox to Database  */
        $ticket->captcha = $request->input('captcha');
        $ticket->claim_response = $request->input('claim_response');
        $ticket->assigned_ticket = $request->input('assigned_ticket');
        $ticket->answered_ticket = $request->input('answered_ticket');
        $ticket->agent_mask = $request->input('agent_mask');
        $ticket->html = $request->input('html');
        $ticket->client_update = $request->input('client_update');
        $ticket->collision_avoid = $request->input('collision_avoid');

        $ticket->count_internal = $request->input('count_internal');
        $ticket->show_status_date = $request->input('show_status_date');
        $ticket->show_org_details = $request->input('show_org_details');
        $ticket->waiting_time = $request->input('waiting_time');
        $ticket->ticket_number_prefix = $request->input('ticket_number_prefix');
        /* Check whether function success or not */
        $ticket->save();

        //clearing the cache
        Cache::forget('settings_ticket');

        /* redirect to Index page with Success Message */
        if ($request->has('record_per_page')) {
            if ($request->get('record_per_page') != '' || $request->get('record_per_page') != null) {
                \Cache::forever('ticket_per_page', $request->get('record_per_page'));
            }
        }
        return successResponse(Lang::get('lang.ticket_settings_saved_successfully'));
    }

    /**
     * method to get Ticket setting details.
     * @return Response
     */
    public function getTicketSettingsDetails()
    {   
        $ticket = Ticket::whereId('1')->with(['status:id,name'])->select('id','collision_avoid','lock_ticket_frequency','status','count_internal','show_status_date','show_org_details','waiting_time','ticket_number_prefix','custom_field_name','show_user_location')->first()->toArray();
        $ticket['record_per_page'] = Cache::get('ticket_per_page') ? : 10;

        return successResponse('', ['ticket' => $ticket]);
    }

    /**
     * get the form for Email setting page.
     *
     * @param type Email    $email
     * @param type Template $template
     * @param type Emails   $email1
     *
     * @return type Response
     */
    public function getemail(Email $email, Template $template, Emails $email1)
    {
        try {
            /* fetch the values of email from Email table */
            $emails = $email->whereId('1')->first();
            /* Fetch the values from Template table */
            $templates = $template->get();
            /* Fetch the values from Emails table */
            $emails1 = $email1->get();
            /* Direct to Email Settings Page */
            return view('themes.default1.admin.helpdesk.settings.email', compact('emails', 'templates', 'emails1'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Update the specified email setting in storage.
     *
     * @param type int          $id
     * @param type Email        $email
     * @param type EmailRequest $request
     *
     * @return type Response
     */
    public function postemail($id, Email $email, EmailRequest $request)
    {
        try {
            /* fetch the values of email request  */
            $emails = $email->whereId('1')->first();
            /* fill the values to email table */
            $emails->fill($request->except('email_fetching', 'all_emails', 'email_collaborator', 'strip', 'attachment'))->save();
            /* insert checkboxes  to database */
            // $emails->email_fetching = $request->input('email_fetching');
            // $emails->notification_cron = $request->input('notification_cron');
            $emails->all_emails = $request->input('all_emails');
            $emails->email_collaborator = $request->input('email_collaborator');
            $emails->strip = $request->input('strip');
            $emails->attachment = $request->input('attachment');
            /* Check whether function success or not */
            $emails->save();
            /* redirect to Index page with Success Message */
            return redirect('getemail')->with('success', Lang::get('lang.email_settings_saved_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('getemail')->with('fails', Lang::get('lang.email_can_not_updated') . '<li>' . $e->getMessage() . '</li>');
        }
    }


    /**
     * get the form for Alert setting page.
     *
     * @param type Alert $alert
     *
     * @return type Response
     */
    public function getalert(Alert $alerts)
    {
        $browser_notification_settings = null;
        try {
            $browser_notification_settings = $this->browserNotification();
            // dd($browser_notification_settings);
            return view('themes.default1.admin.helpdesk.settings.alert', compact('alerts', 'browser_notification_settings'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Update the specified alert in storage.
     *
     * @param type         $id
     * @param type Alert   $alert
     * @param type Request $request
     *
     * @return type Response
     */
    public function postalert(Alert $alert, Request $request)
    {
        try {
            $requests = $request->except(['_token', '_method']);
            Alert::truncate();
            foreach ($requests as $key => $value) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                Alert::create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }

            if ($request->has('browser_notification_status') && $request->browser_notification_status == 1) {
                $browser_notification_settings = $request->only('api_id', 'rest_api_key');
                $this->browserNotificationsetting($browser_notification_settings);
            }

            if ($request->has('request_from')) {
                switch ($request->request_from) {
                    case 'calendar-settings':
                        return redirect('calendar/settings/alert')->with('success', Lang::get('lang.alert_&_notices_saved_successfully'));
                    break;
                }
            }

            return redirect('alert')->with('success', Lang::get('lang.alert_&_notices_saved_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('alert')->with('fails', $e->getMessage());
        }
    }

    /**
     *  Generate Api key.
     *
     *  @return type json
     */
    public function generateApiKey()
    {
        $key = str_random(32);

        return $key;
    }

    /**
     * Main Settings Page.
     *
     * @return type view
     */
    public function settings()
    {
        return view('themes.default1.admin.helpdesk.setting');
    }

    /**
     * @param int $id
     * @param $compant instance of company table
     *
     * get the form for company setting page
     *
     * @return Response
     */
    public function getStatuses()
    {
        try {
            /* fetch the values of company from company table */
            $statuss = \DB::table('ticket_status')->get();
            /* Direct to Company Settings Page */
            return view('themes.default1.admin.helpdesk.settings.status', compact('statuss'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @param $compant instance of company table
     *
     * get the form for company setting page
     *
     * @return Response
     */
    public function getEditStatuses($id)
    {
        try {
            /* fetch the values of company from company table */
            $status = \DB::table('ticket_status')->where('id', '=', $id)->first();
            /* Direct to Company Settings Page */
            return view('themes.default1.admin.helpdesk.settings.status-edit', compact('status'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @param $compant instance of company table
     *
     * get the form for company setting page
     *
     * @return Response
     */
    public function editStatuses($id, StatusRequest $request)
    {
        try {
            /* fetch the values of company from company table */
            $statuss = \App\Model\helpdesk\Ticket\Ticket_Status::whereId($id)->first();
            $statuss->name = $request->input('name');
            $statuss->icon_class = $request->input('icon_class');
            $statuss->email_user = $request->input('email_user');
            $statuss->sort = $request->input('sort');
            $delete = $request->input('deleted');
            if ($delete == 'yes') {
                $statuss->state = 'delete';
            } else {
                $statuss->state = $request->input('state');
            }
            $statuss->sort = $request->input('sort');
            $statuss->save();
            /* Direct to Company Settings Page */
            return redirect()->back()->with('success', Lang::get('lang.status_has_been_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * create a status.
     *
     * @param \App\Model\helpdesk\Ticket\Ticket_Status  $statuss
     * @param \App\Http\Requests\helpdesk\StatusRequest $request
     *
     * @return type redirect
     */
    public function createStatuses(\App\Model\helpdesk\Ticket\Ticket_Status $statuss, StatusRequest $request)
    {
        try {
            /* fetch the values of company from company table */
            $statuss->name = $request->input('name');
            $statuss->icon_class = $request->input('icon_class');
            $statuss->email_user = $request->input('email_user');
            $statuss->sort = $request->input('sort');
            $delete = $request->input('delete');
            if ($delete == 'yes') {
                $statuss->state = 'deleted';
            } else {
                $statuss->state = $request->input('state');
            }
            $statuss->sort = $request->input('sort');
            $statuss->save();
            /* Direct to Company Settings Page */
            return redirect()->back()->with('success', Lang::get('lang.status_has_been_created_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * delete a status.
     *
     * @param type $id
     *
     * @return type redirect
     */
    public function deleteStatuses($id)
    {
        try {
            if ($id > 5) {
                /* fetch the values of company from company table */
                \App\Model\helpdesk\Ticket\Ticket_Status::whereId($id)->delete();
                /* Direct to Company Settings Page */
                return redirect()->back()->with('success', Lang::get('lang.status_has_been_deleted'));
            } else {
                return redirect()->back()->with('failed', Lang::get('lang.you_cannot_delete_this_status'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * get the page of notification settings.
     *
     * @return type view
     */
    public function notificationSettings()
    {
        return view('themes.default1.admin.helpdesk.settings.notification');
    }

    /**
     * delete a notification.
     *
     * @return type redirect
     */
    public function deleteReadNoti()
    {
        Notification::where('seen', '=', 1)->delete();
        return redirect()->back()->with('success', Lang::get('lang.you_have_deleted_all_the_read_notifications'));
    }

    /**
     * delete a notification log.
     *
     * @return type redirect
     */
    public function deleteNotificationLog()
    {
        $days = Input::get('no_of_days');
        if ($days == null) {
            return redirect()->back()->with('fails', 'Please enter valid no of days');
        }
        $now = \Carbon\Carbon::now();
        $days_before = \Carbon\Carbon::now()->subDay($days);
        Notification::whereBetween('created_at', [$days_before, $now])->delete();

        return redirect()->back()->with('success', Lang::get('lang.you_have_deleted_all_the_notification_records_since') . $days . ' days.');
    }

    /**
     *  To display the list of ratings in the system.
     *
     *  @return type View
     */
    public function RatingSettings()
    {
        try {
            $ratings = Rating::orderBy('display_order', 'asc')->get();

            return view('themes.default1.admin.helpdesk.settings.ratings', compact('ratings'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * edit a rating.
     *
     * @param type $id
     *
     * @return type view
     */
    public function editRatingSettings($id)
    {
        try {
            $rating = Rating::whereId($id)->first();
            $department = Department::pluck('name', 'id')->toArray();
            // dd($rating->restrict, $department);
            $restrict = Department::where('name', '=', $rating->restrict)->pluck('id')->toArray();
            if (count($restrict) == 0) {
                $restrict[0] = null;
            }
            return view('themes.default1.admin.helpdesk.settings.edit-ratings', compact('rating', 'department', 'restrict'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     *  To store rating data.
     *
     *  @return type Redirect
     */
    public function PostRatingSettings($id, Rating $ratings, RatingUpdateRequest $request)
    {
        try {
            // dd($request->all());
            $rating = $ratings->whereId($id)->first();
            $rating->name = $request->input('name');
            $rating->display_order = $request->input('display_order');
            $rating->allow_modification = $request->input('allow_modification');
            $rating->rating_scale = $request->input('rating_scale');
            $rating->rating_icon = ($request->filled('rating_icon')) ? $request->get('rating_icon') : null;
//            $rating->rating_area = $request->input('rating_area');
            // dd($request->input('restrict'));
            $check_dept_name = Department::where('id', '=', $request->input('restrict'))->select('name')->first();
            if ($check_dept_name) {
                $dept_name = $check_dept_name->name;
            } else {
                $dept_name = $request->input('restrict');
            }
            $rating->restrict = $dept_name;
            // $rating->restrict = $request->input('restrict');
            $rating->save();
            // ratings.index
            // return redirect()->route('ratings.index')
            return redirect()->route('ratings.index')->with('success', Lang::get('lang.ratings_updated_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * get the create rating page.
     *
     * @return type redirect
     */
    public function createRating()
    {
        try {
            $department = Department::pluck('name', 'id')->toArray();
            // dd($department);
            return view('themes.default1.admin.helpdesk.settings.create-ratings', compact('department'));
        } catch (Exception $ex) {
            return redirect('getratings')->with('fails', Lang::get('lang.ratings_can_not_be_created') . '<li>' . $ex->getMessage() . '</li>');
        }
    }

    /**
     * store a rating value.
     *
     * @param \App\Model\helpdesk\Ratings\Rating        $rating
     * @param \App\Model\helpdesk\Ratings\RatingRef     $ratingrefs
     * @param \App\Http\Requests\helpdesk\RatingRequest $request
     *
     * @return type redirect
     */
    public function storeRating(Rating $rating, \App\Model\helpdesk\Ratings\RatingRef $ratingrefs, \App\Http\Requests\helpdesk\RatingRequest $request)
    {
        $rating->name = $request->input('name');
        $rating->display_order = $request->input('display_order');
        $rating->allow_modification = $request->input('allow_modification');
        $rating->rating_scale = $request->input('rating_scale');
        $rating->rating_area = $request->input('rating_area');

        $check_dept_name = Department::where('id', '=', $request->input('restrict'))->select('name')->first();
        if ($check_dept_name) {
            $dept_name = $check_dept_name->name;
        } else {
            $dept_name = $request->input('restrict');
        }
        $rating->restrict = $dept_name;

        $rating->save();
        $ratingrefs->rating_id = $rating->id;
        $ratingrefs->save();

        //return redirect()->back('getratings')->with('success', Lang::get('lang.ratings_created_successfully'));
        return \Redirect::route('ratings.index')->with('success', Lang::get('lang.ratings_saved_successfully'));
    }

    /**
     *  To delete a type of rating.
     *
     *  @return type Redirect
     */
    public function RatingDelete($slug, \App\Model\helpdesk\Ratings\RatingRef $ratingrefs)
    {
        if ($slug == 1) {
            return redirect()->back()->with('fails', Lang::get('lang.rating_can_not_be_deleted'));
        }
        $ratingrefs->where('rating_id', '=', $slug)->delete();
        Rating::whereId($slug)->delete();

        return redirect()->back()->with('success', Lang::get('lang.rating_deleted_successfully'));
    }

    /**
     *
     * @param type $command
     * @param type $daily_at
     * @return type
     */
    public function getCommand($command, $daily_at)
    {
        if ($command == 'dailyAt') {
            $command = "dailyAt,$daily_at";
        }
        return $command;
    }

    /**
     * @category function to return clean data view
     * @param null
     * @return respone/view
     */
    public function getCleanUpView()
    {
        $system_check = CommonSettings::select('status')->where('option_name', '=', 'dummy_data_installation')->first();
        if ($system_check) {
            if ($system_check->status == 1 || $system_check->status == '1') {
                return View('themes.default1.admin.helpdesk.settings.cleandata');
            }
        }
        return redirect()->to('404')->with('fails', Lang::get('lang.no-dummy-data'));
    }

    /**
     * @category function to clean dummy database and reseed tables with default options
     * @param null
     * @return
     * Very dangerous function should be call by admin only
     */
    private function cleanDatabase()
    {
        try {
            $user = \App\User::select(
                'user_name',
                'first_name',
                'last_name',
                'email',
                'password',
                'agent_tzone',
                'profile_pic'
            )->where('id', '=', 1)->first();
            $system = System::where('id', '=', 1)->first();

            $fileSystemSettings = FileSystemSettings::first();

            $fileManagerEntries = FileManagerAclRule::all();

            $fileManagerEntriesWithDepartments = FileManagerAclRuleDepartment::all();

            $tableNames = \Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
            foreach ($tableNames as $name) {
                //if you don't want to truncate migrations
                if (in_array($name, ['migrations', 'faveo_license', 'file_manager_acl_rules', 'file_manager_acl_rules_departments'])) {
                    continue;
                }
                DB::table($name)->truncate();
            }
            DB::commit();
            config(['database.install' => 0]);
            (new SyncFaveoToLatestVersion)->sync();
            $user2 = \App\User::updateOrCreate(['id' => 1], [
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'user_name' => $user->user_name,
                        'password' => $user->password,
                        'assign_group' => 1,
                        'primary_dpt' => 1,
                        'active' => 1,
                        'user_language' => $user->user_language,
                        'agent_tzone' => $user->agent_tzone,
                        'role' => 'admin',
                         'profile_pic' => $user->getOriginal('profile_pic')
            ]);
            $system2 = System::find(1);
            $system2->time_zone_id = $system->time_zone_id;
            $system2->date_time_format = $system->date_time_format;
            $system2->content = $system->content;
            $system2->save();

            // updating business hours
            $bhours = BusinessHours::where('id', '=', 1)->first();
            $bhours->timezone = System::first()->time_zone;
            $bhours->save();

            FileSystemSettings::updateOrCreate(
                ['id' => $fileSystemSettings->id],
                ['disk' => $fileSystemSettings->disk, 'allowed_files' => $fileSystemSettings->allowed_files, 'files_moved_from_old_private_disk' => $fileSystemSettings->files_moved_from_old_private_disk]
            );

            foreach ($fileManagerEntries as $fileManagerEntry) {
                FileManagerAclRule::updateOrCreate(['path' => $fileManagerEntry->path], [
                    'user_id' => $fileManagerEntry->user_id,'disk' => $fileManagerEntry->disk,
                    'path' => $fileManagerEntry->path,'access' => $fileManagerEntry->access,
                    'type' => $fileManagerEntry->type,'dirname' => $fileManagerEntry->dirname,
                    'basename' => $fileManagerEntry->basename,'hidden' => $fileManagerEntry->hidden
                ]);
            }

            foreach ($fileManagerEntriesWithDepartments as $fileManagerEntriesWithDepartment) {
                FileManagerAclRuleDepartment::updateOrCreate(
                    ['rule_id' => $fileManagerEntriesWithDepartment->rule_id],
                    [
                        'rule_id' => $fileManagerEntriesWithDepartments->rule_id,
                        'department_id' => $fileManagerEntriesWithDepartments->department_id
                    ]
                );
            }

            $response = 'success';
            return $response;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return $error;
        }
    }

    /**
     * @category function to handle clean dummy data ajax request
     * @param null
     * @return json
     */
    public function postCleanDummyData(Request $request)
    {
        $result = 'failed';
        $system_check = CommonSettings::select('status')->where('option_name', '=', 'dummy_data_installation')->first();
        if ($system_check->status == 1 || $system_check->status == '1') {
            $result = self::cleanDatabase();
        }
        return response()->json(compact('result'));
    }

    public function browserNotification()
    {
        $data = json_decode(\File::get(public_path() . '/manifest.json'));
        $data->app_id = \Config::get('onesignal.app_id');
        $data->api_key = \Config::get('onesignal.rest_api_key');
        $data->enabled = \Config::get('onesignal.enabled');
        // dd($data);
        return $data;
        return view('themes.default1.admin.helpdesk.settings.browser-notifications.notification-settings', compact('data'));
    }

    /**
     *
     * @param Request $request
     */
    public function browserNotificationsetting($request)
    {
        // dd($request);
        config(['onesignal.app_id' => $request['api_id']]);
        config(['onesignal.rest_api_key' => $request['rest_api_key']]);
        $fp = fopen(base_path() . '/config/onesignal.php', 'w');
        fwrite($fp, '<?php return ' . var_export(config('onesignal'), true) . ';');
        fclose($fp);
        //$data = file_get_contents(base_path().'/config/one-signal-setting');
    }

    /**
     * @category function to show dashboard statistic settings page to admin
     * @param
     * @return view
     */
    public function statisticsSettings()
    {
        try {
            return view('themes.default1.admin.helpdesk.settings.dashboard-settings');
        } catch (\Exception $e) {
            return redirect()->to('/')->with('fails', $e->getMessage());
        }
    }

    /**
     * @category function to save users preferenece for dashboard statistics
     * @param object $request
     * @return resposne
     */
    public function postStatisticsSettings(Request $request)
    {
        try {
            $default = 'departments,agents,teams';
            $options_str = ($request->has('options')) ? implode(',', $request->get('options')) : '';
            $options = ($options_str != '') ? $default.','.$options_str: $default;
            CommonSettings::where('option_name', '=', 'dashboard-statistics')
            ->update([
                'option_value' => $options
            ]);
            return redirect()->to('dashboard-settings')->with('success', Lang::get('lang.default_tabs_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->to('dashboard-settings')->with('fails', $e->getMessage());
        }
    }

    public function getBackUp(System $system)
    {
        $path = base_path().DIRECTORY_SEPARATOR.'back-up';
        if (is_dir($path)) {
            if (count(glob($path.DIRECTORY_SEPARATOR."*")) == 0) {
                return view('themes.default1.admin.helpdesk.settings.backup')->with('error', "Your back is empty");
            }

            $back_up = stat($path);
            $back_up['ctime'] = Carbon::createFromTimestamp($back_up['ctime'])->setTimezone($system->first()->time_zone);
            $back_up['created_at'] = $back_up['ctime']->format($system->first()->date_time_format);
            return view('themes.default1.admin.helpdesk.settings.backup', compact('back_up'));
        }
        return view('themes.default1.admin.helpdesk.settings.backup')->with('error', "Your back is empty");
    }

    /**
     * Function to save/update user options settings in common_settings table
     * @param   string   $option_name   name of the option to update value
     * @param   boolean  $status        value of status column
     * @param   string   $option_value  value of option value column
     * @return  boolean                 true or false
     */
    protected function updateCommonSettingsTable($option_name, $columnName, $columnValue)
    {
        return CommonSettings::where('option_name', '=', $option_name)
            ->update([$columnName =>  $columnValue]);
    }


    /**
     * This method update cdn settings
     * @param Request $request
     * @return type string
     */
    public function cdnSettings($cdnStatus = null)
    {
        /**
         * The statement below will only update CommonSettings when $cdnStatus
         * is a non null value and return the updated value. If $cdnStatus is null then
         * it will return old value. Doing this allows this method to be called from
         * other places to update link.php with or without $cdnStatus value to retain
         * user's preferred settings.
         */
        $cdnStatus = (int)CommonSettings::where('option_name', 'cdn_settings')->when($cdnStatus !== null, function ($query) use ($cdnStatus) {
            $query->updateOrCreate(
                ['option_name' => 'cdn_settings'],
                ['option_name' => 'cdn_settings', 'option_value' => $cdnStatus]
            );
        })->value('option_value');
        $source  = ($cdnStatus) ? config_path('cdn.php') : config_path('noncdn.php');
        copy($source, config_path('link.php'));
        $message = Lang::get('lang.cdn_settings_updated_successfully');
        return $message;
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     * @return string
     */
    public function getDateByFormat()
    {
        return faveoDate("", \Input::get('format'), \Input::get('tz'));
    }

    /**
     * To view recaptcha settings page
     * @return view
     */
    public function recaptcha()
    {
        try {
            return view('themes.default1.admin.helpdesk.settings.recaptcha');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * This method return recaptcha settings information
     * @return json
     */
    public function getRecaptcha()
    {

        try {

            $recaptchaSettings = CommonSettings::where('option_name','google')->pluck('option_value','optional_field')->toArray();

            $recaptchaOptions = [
                [
                    "value" => 'login_page', "name" => 'Login Page',
                ],[
                    "value" => 'comments_page', "name" => 'KB Comment Page',
                ],[
                    "value" => 'ticket_create_agent', "name" => 'Agent Panel Create Ticket',
                ],[
                    "value" => 'ticket_create_client', "name" => 'Client Panel Create Ticket',
                ],[
                    "value" => 'ticket_edit_agent', "name" => 'Agent Panel Edit Ticket',
                ],[
                    "value" => 'ticket_fork_agent', "name" => 'Agent Panel Fork Ticket',
                ],[
                    "value" => 'user_create_agent', "name" => 'Agent Panel Create User',
                ],[
                    "value" => 'user_create_client', "name" => 'Client Panel Create User',
                ],[
                    "value" => 'user_edit_agent', "name" => 'Agent Panel Edit User',
                ],[
                    "value" => 'organisation_create_agent', "name" => 'Agent Panel Create Organisation',
                ],[
                    "value" => 'organisation_edit_agent', "name" => 'Agent Panel Edit Organisation',
                ],[
                    "value" => 'ticket_recur_agent', "name" => 'Agent Panel Recur',
                ],[
                    "value" => 'ticket_recur_admin', "name" => 'Admin Panel Recur',
                ]
            ];

            //when captcha settings is not there in database
            if(!$recaptchaSettings){

                return successResponse('',['recaptchaOptions'=> $recaptchaOptions]);
            }

            
            $recaptchaApply = $recaptchaSettings['recaptcha_apply_for'] != "" ? explode(',', $recaptchaSettings['recaptcha_apply_for']) : [];


            $outputDataArray = ['googleServiceKey' => $recaptchaSettings['service_key'], 'googleSecretKey' => $recaptchaSettings['secret_key'], 'type' => $recaptchaSettings['recaptcha_type']  , 'status' => (bool) $recaptchaSettings['recaptcha_status'], 'applyfor' => $recaptchaApply, 'recaptchaOptions'=> $recaptchaOptions];

            return successResponse('', $outputDataArray);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method will store recaptcha settings info
     * @param Request $request
     * @return type message
     */
    public function postRecaptcha(RecaptchaRequest $request)
    {
        try {

           $requestParams = $request->all();

           $requestParams['apply_for'] = implode(',', $requestParams['apply_for']);

           $fields = ['recaptcha_status' => 'status','recaptcha_type' => 'type' ,'service_key' =>'google_service_key','secret_key' => 'google_secret_key','recaptcha_apply_for' => 'apply_for'];

           foreach ($fields as $key => $value) {

            CommonSettings::updateOrCreate(['option_name' => 'google', 'optional_field' => $key], ['option_value' => $requestParams[$value]]);
            }

          return successResponse(Lang::get('lang.updated_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Gets ticket number by integer id
     * @param int $ticketId
     * @return string
     */
    public function getTicketNumberById(int $ticketId)
    {
        $ticketNumberPrefix = $this->getTicketNumberPrefix();
        $defaultTicketNumber = $ticketNumberPrefix . '-AAAA-0000';

        // get last ticket number along with it id
        $ticketObject = Tickets::orderBy('id', 'desc')->where('ticket_number', '!=', '')->select('id', 'ticket_number')->first();

        $oldTicketId = isset($ticketObject->id) ? $ticketObject->id : 0;

        $oldTicketNumber = isset($ticketObject->ticket_number) ? $ticketObject->ticket_number : $defaultTicketNumber;

        // remove all '-' from old ticket number and take only last 2, so that prefix can be attached independently
        $oldTicketNumberArray = explode('-', $oldTicketNumber);

        // this is for migrating clients with old ticket number pattern to new
        // this method will no longer be needed once a ticket is created with new ticket number pattern
        $oldTicketNumberArray = $this->getValidOldTicketNumber($oldTicketNumberArray);

        return $this->getUniqueTicketNumber($oldTicketId, $oldTicketNumberArray, $ticketId);
    }

    /**
     * Makes old ticket number valid
     * @param $oldTicketNumberArray
     * @return string[]
     */
    private function getValidOldTicketNumber($oldTicketNumberArray)
    {
        // in cases of migrations, old ticket number might be invalid. That has to be converted to a valid number
        if (count($oldTicketNumberArray) != 3) {
            return [$this->getTicketNumberPrefix(), 'AAAA', '0000'];
        }

        if (strlen($oldTicketNumberArray[1]) < 4 || !preg_match("/^[A-Z]+$/", $oldTicketNumberArray[1])) {
            // pad it to make 4
            return [$this->getTicketNumberPrefix(), 'AAAA', '0000'];
        }

        if (strlen($oldTicketNumberArray[2]) < 4 || !preg_match("/^[0-9]+$/", $oldTicketNumberArray[2])) {
            // pad it to make 4
            return [$this->getTicketNumberPrefix(), 'AAAA', '0000'];
        }


        return $oldTicketNumberArray;
    }

    /**
     * Gets a unique ticket number
     *
     * @internal Sometimes when 2 tickets are getting created in parallel, they will encounter same ticket number for
     * the old ticket, which might result in duplicate ticket number. But generating the ticket number based on
     * the ID of the ticket will always unique.
     *
     * Now, we can increment an alphabet by using a "++" operator. If lets say a ticket ID is 10000, we need to
     * run iteration 10000 times, it becomes highly inefficient when number of tickets reaches a big number.
     * To counter that we will start adding up "++" from the last ticket created and will keep on incrementing to
     * the current ticket number.
     *
     * For eg. lets say last ticket id and ticket number is 1000, AAAA-AAAA-1111 and new ticket Id is 1005
     * now, we will calculate the diff bw both IDs (n = 1005 - 1000 = 5) and increment the old ticket number
     * that (n) many times. So resultant will be AAAA-AAAA-1116 (incremented AAAA-AAAA-1111 5 times)
     *
     * If a duplcate ticket number is encountered (for old clients), it is going to increment first variable component
     * of ticket number and regenerate the ticket number using the same algorithm.
     * For eg. if a ticket number is generated AAAA-AAAA-0001, now this ticket number already exists, it will start
     * generating ticket number from AAAA-AAAB-0000 and the new ticket genrated will be AAAA-AAAB-0001.
     *
     * @author avinash kumar <avinash.kumar@ladybirdweb.com>
     *
     * @param int $oldTicketId
     * @param string[] $oldTicketNumberArray
     * @param int $newTicketId
     * @return string
     */
    private function getUniqueTicketNumber($oldTicketId, $oldTicketNumberArray, $newTicketId, $alreadyIncremented = false)
    {
        $sanitizedOldTicketNumber = $oldTicketNumberArray[1].$oldTicketNumberArray[2];

        if(!$alreadyIncremented){
            for ($i = $oldTicketId; $i < $newTicketId; $i++) {
                ++$sanitizedOldTicketNumber;
            }
        }

        // just for readability
        $newTicketNumber = $sanitizedOldTicketNumber;

        $formattedTicketNumber = $this->getFormattedTicketNumber($newTicketNumber);

        // if $sanitizedOldTicketNumber already exists, increment $oldTicketNumberArray[1] by one and replay the algorithm
        if (Tickets::where('ticket_number', $formattedTicketNumber)->count()) {
            ++$newTicketNumber;
            $oldTicketNumberArray = explode('-',$this->getFormattedTicketNumber($newTicketNumber));

            // regenerate the ticket number
            return $this->getUniqueTicketNumber($oldTicketId, $oldTicketNumberArray, $newTicketId, true);
        }

        return $formattedTicketNumber;
    }

    /**
     * Gets formatted ticket number after appending prefix and '-'
     * @param $variableTicketNumberComponent
     * @return string
     */
    private function getFormattedTicketNumber($variableTicketNumberComponent)
    {
        return $this->getTicketNumberPrefix().'-'.substr($variableTicketNumberComponent, 0, -4)
            .'-'.substr($variableTicketNumberComponent, -4);
    }

    /**
     * Gets ticket number prefix
     * @return mixed
     */
    private function getTicketNumberPrefix()
    {
        // first check in cache, if ticket_number_prefix is present. If not, get from DB
        if ($ticketNumberPrefix = getFromQuickCache('ticket_number_prefix')) {
            return strtoupper($ticketNumberPrefix);
        }
        return strtoupper(Ticket::value('ticket_number_prefix'));
    }

    /**
     * Returns the view for Admin Panel > Settings > File Systems
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fileSystemSettingsPage()
    {
        return view('themes.default1.admin.helpdesk.settings.filesystemsettings');
    }

    /**
     * Gets the default filesystem settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileSystemSettingsValues()
    {
        $fileSystemSettings = new FileSystemSettings();

        if (!$fileSystemSettings->count()) {
            return errorResponse(trans('lang.setting_file_system_no_settings_found'));
        }

        $fileSystemSettingsValues = $fileSystemSettings->first();

        return successResponse('', [
            'allowed_files' => $fileSystemSettingsValues->allowed_files,
            'show_public_folder_with_default_disk' => (bool) $fileSystemSettingsValues->show_public_folder_with_default_disk,
        ]);
    }

    /**
     * Updates the filesystem settings
     *
     * @param FileSystemSettingsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFileSystemSettings(FileSystemSettingsRequest $request)
    {
        $affectedRows = null;

        $fileSystemSettings = new FileSystemSettings();

        $payload = [
            'allowed_files' => $request->allowedFiles,
            'show_public_folder_with_default_disk' => $request->showPublicDiskWithDefaultDisk,
        ];

        if ($fileSystemSettings->count()) {
            $settingsModel = $fileSystemSettings->first();

            $affectedRows = $settingsModel->update($payload);
        } else {
            $affectedRows = $fileSystemSettings->create($payload);
        }

        return ($affectedRows)
            ? successResponse(trans('lang.setting_file_system_save_success'))
            : errorResponse(trans('lang.setting_file_system_save_failure'));
    }

}
