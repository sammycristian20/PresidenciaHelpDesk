<?php

namespace App\Http\Controllers\Common;

// controllers
use DB;
// requests
use App;
use Lang;
use Crypt;
// models
use Input;
use Config;
use Schema;
use Artisan;

// classes
use Exception;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Model\Common\Template;
use App\Model\Common\TemplateType;
use App\Model\helpdesk\Email\Smtp;
use Logger;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Model\helpdesk\Theme\Widgets;
use App\Model\helpdesk\Settings\Plugin;
use Illuminate\Database\Schema\Blueprint;
use App\Http\Requests\helpdesk\SmtpRequest;
use App\Http\Requests\PluginsPageRequest;
use App\Model\helpdesk\Utility\Version_Check;
use App\Model\helpdesk\Settings\CommonSettings;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ***************************
 * Settings Controllers
 * ***************************
 * Controller to keep smtp details and fetch where ever needed.
 */
class SettingsController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return type void
     */
    public function __construct() {
        $this->middleware('auth')->except(['getSystemSettings']);
        $this->middleware('roles')->except(['getSystemSettings']);
    }

    /**
     * get SMTP.
     *
     * @return type view
     */
    public function getsmtp() {
        $settings = Smtp::where('id', '=', '1')->first();
        return view('themes.default1.admin.helpdesk.emails.smtp', compact('settings'));
    }

    /**
     * POST SMTP.
     *
     * @return type view
     */
    public function postsmtp(SmtpRequest $request) {
        $data = Smtp::where('id', '=', 1)->first();
        $data->driver = $request->input('driver');
        $data->host = $request->input('host');
        $data->port = $request->input('port');
        $data->encryption = $request->input('encryption');
        $data->name = $request->input('name');
        $data->email = $request->input('email');
        $data->password = Crypt::encrypt($request->input('password'));
        try {
            $data->save();
            return \Redirect::route('getsmtp')->with('success', 'success');
        } catch (Exception $e) {
            return \Redirect::route('getsmtp')->with('fails', $e->errorInfo[2]);
        }
    }

    /**
     * Post settings.
     *
     * @param type Settings $set
     * @param type Request  $request
     *
     * @return type view
     */
    public function PostSettings(Settings $set, Request $request) {
        $settings = $set->where('id', '1')->first();
        $pass = $request->input('password');
        $password = Crypt::encrypt($pass);
        $settings->password = $password;
        try {
            $settings->save();
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->errorInfo[2]);
        }
        if (Input::file('logo')) {
            $name = Input::file('logo')->getClientOriginalName();
            $destinationPath = 'dist/logo';
            $fileName = rand(0000, 9999) . '.' . $name;
            Input::file('logo')->move($destinationPath, $fileName);
            $settings->logo = $fileName;
            $settings->save();
        }
        try {
            $settings->fill($request->except('logo', 'password'))->save();

            return redirect()->back()->with('success', 'Settings updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->errorInfo[2]);
        }
    }

    public function plugins(CommonSettings $common) {
        $common = $common->select('status')->where('option_name', '=', 'dummy_data_installation')->first();
        if ($common) {
            if($common->status == 1 || $common->status == '1') {
                $message = Lang::get('lang.plugin-with-dummy-data-error-message').' <a href="'.route('clean-database').'">'.Lang::get('lang.click').'</a> '.Lang::get('lang.clear-dummy-data');
                return redirect()->back()->with('fails', $message);
            }
        }
        return view('themes.default1.admin.helpdesk.settings.plugins');
    }

    /**
    * Paginates the collection
    * @param array|Collection $items
    * @param int $perPage
    * @param int $page
    * @param array $options
    *
    * @return LengthAwarePaginator
    */  
    private function paginateCollection($items, $perPage = 10, $page = null, $options = [])
    {

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $lap = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

        return [
            'current_page' => $lap->currentPage(),
            'data' => $lap ->values(),
            'first_page_url' => $lap ->url(1),
            'from' => $lap->firstItem(),
            'last_page' => $lap->lastPage(),
            'last_page_url' => $lap->url($lap->lastPage()),
            'next_page_url' => $lap->nextPageUrl(),
            'per_page' => $lap->perPage(),
            'prev_page_url' => $lap->previousPageUrl(),
            'to' => $lap->lastItem(),
            'total' => $lap->total(),
        ];
    }

    private function searchForName($name, $array) 
    {
        $keys = [];
        foreach ($array as $key => $val) {
            if (strpos(strtolower($val['name']), strtolower($name)) !== false) {
                array_push($keys,$key);
            }
        }
        return $keys;
    }

    public function getPlugin(Request $request) 
    {
        $plugins = $this->fetchConfig();
        if($request->sort_order && $request->sort_field) {
            usort($plugins, function ($item1, $item2) use ($request) {
                return ($request->sort_order == 'asc') 
                ? $item1[$request->sort_field] <=> $item2[$request->sort_field]
                : $item2[$request->sort_field] <=> $item1[$request->sort_field];
            });
        }

        if($request->search_query) {
            $keys = $this->searchForName($request->search_query,$plugins);
            $plugins = array_filter(
                $plugins,
                function ($key) use ($keys) {
                    return in_array($key, $keys);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        return $this->paginateCollection($plugins,($request->limit)?:10);

    }

    /**
     * Reading the Filedirectory.
     *
     * @return type
     */
    public function ReadPlugins() {
        $dir = app_path() . DIRECTORY_SEPARATOR . 'Plugins';
        $plugins = array_diff(scandir($dir), ['.', '..']);

        return $plugins;
    }

    /**
     * Delete the directory.
     *
     * @param type $dir
     *
     * @return bool
     */
    public function deleteDirectory($dir) 
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            chmod($dir . DIRECTORY_SEPARATOR . $item, 0777);
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        chmod($dir, 0777);

        return rmdir($dir);
    }

    public function ReadConfigs() 
    {
        $dir = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR;
        $directories = scandir($dir);
        $files = [];
        foreach ($directories as $key => $file) {
            if ($file === '.' or $file === '..') {
                continue;
            }

            if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                $files[$key] = $file;
            }
        }
        //dd($files);
        $config = [];
        $plugins = [];
        if (count($files) > 0) {
            foreach ($files as $key => $file) {
                $plugin = $dir . $file;
                $plugins[$key] = array_diff(scandir($plugin), ['.', '..', 'ServiceProvider.php']);
                $plugins[$key]['file'] = $plugin;
            }
            foreach ($plugins as $plugin) {
                $dir = $plugin['file'];
                //opendir($dir);
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file == 'config.php') {
                            $config[] = $dir . DIRECTORY_SEPARATOR . $file;
                        }
                    }
                    closedir($dh);
                }
            }

            return $config;
        } else {
            return 'null';
        }
    }

    public function fetchConfig() 
    {
        $configs = $this->ReadConfigs();
        $plugs = new Plugin();
        $fields = [];
        $attributes = [];
        if ($configs != 'null') {
            foreach ($configs as $key => $config) {

                $fields[$key] = include $config;
            }
        }
        if (count($fields) > 0) {
            foreach ($fields as $key => $field) {
                $plug = $plugs->where('name', $field['name'])->select('path', 'status')->orderBy('name')->get()->toArray();
                if ($plug) {
                    foreach ($plug as $i => $value) {
                        $attributes[$key]['path'] = $plug[$i]['path'];
                        $attributes[$key]['status'] = $plug[$i]['status'];
                    }
                } else {
                    $attributes[$key]['path'] = $field['name'];
                    $attributes[$key]['status'] = 0;
                }
                $attributes[$key]['name'] = $field['name'];
                $attributes[$key]['settings'] = $field['settings'];
                $attributes[$key]['description'] = $field['description'];
                $attributes[$key]['website'] = $field['website'];
                $attributes[$key]['version'] = $field['version'];
                $attributes[$key]['author'] = $field['author'];
            }
        }
        return $attributes;
    }

    /**
     * Function checks if given plugin's directory exist or not in the system
     *
     * @param String $plugin name of the plugin
     * @return bool  true if exits, false otherwise
     */
    private function doesPluginExist(string $plugin)
    {
        $pluginFolder = app_path('Plugins');
        $availablePlugins = glob($pluginFolder.DIRECTORY_SEPARATOR.'*');
        return in_array($pluginFolder.DIRECTORY_SEPARATOR.$plugin, $availablePlugins);
    }

    public function StatusPlugin($slug) 
    {
        \Event::dispatch('lime-survey.plugin.activated');
        if(!$this->doesPluginExist($slug)) {
            return errorResponse(trans('lang.not_found'));
        }
        $plugs = new Plugin();

        $plug = $plugs->where('name', $slug)->first();

        $status = 0;
        if (!$plug) {
            $status = 1;
        }elseif($plug->status==0){
            $status = 1;
        }

        $plugs->updateOrCreate(['name' => $slug, 'path' => $slug],
                [ 'status' => $status]);

        if($slug == "Calendar" && $status == 0){
            $templates = new Template;
            $templates->whereIn( 'name', ['task-update','task-reminder','task-created','task-status','task-assigned', 'task-deleted', 'task-assigned-owner'])->delete();
        }

        if($slug == "LimeSurvey" && $status == 1){
            // \Event::dispatch('lime-survey.plugin.activated');
        }

        \Event::dispatch('plugin.status.change',[['name'=>$slug,'status'=>$status]]);

        return successResponse(trans('lang.plugin_updated_successfully'));

    }

    /**
     *
     * @param CommonSettings $Common
     * @return type
     */
    public function modules(CommonSettings $Common) 
    {

        return view('themes.default1.admin.helpdesk.modules.all-module');
    }

    /**
     *
     * @return type
     */
    public function Getmodules() 
    {
        try {
            $moduleQuery = CommonSettings::select('option_name', 'status')->where('option_name', 'helptopic_link_with_type')->orwhere('option_name','micro_organization_status')->orwhere('option_name','batch_tickets')->orwhere('option_name', 'time_track');

            $satellite_path = base_path('app/SatelliteHelpdesk');
            $checkValue = CommonSettings::where('option_name', 'satellite_helpdesk')->first();
            if (is_dir($satellite_path) && !$checkValue) {
                \Event::dispatch('helpdesk.prasent.satellitehelpdesk');
                $moduleQuery = $moduleQuery->orwhere('option_name', 'satellite_helpdesk');
            } elseif (!is_dir($satellite_path) && $checkValue) {

                CommonSettings::where('option_name', 'satellite_helpdesk')->delete();
            } else {
                $moduleQuery = $moduleQuery->orwhere('option_name', 'satellite_helpdesk');
            }

          return \DataTables::of($moduleQuery->get())
                        ->editColumn('name', function ($moduleQuery) {
                            if ($moduleQuery->option_name == 'helptopic_link_with_type') {
                                $name = 'Helptopic link with type';
                                $name_with_tooltip = ' <a class="right" title="" data-placement="right" data-toggle="tooltip" href="#" data-original-title="' . Lang::get('HelptopicType::lang.if_enabled_on_the_ticket_create_edit_page_only_those_type_will_show_which_are_linked_to_selected_helptopic_if_none_linked_all_ticket_type_will_show') . '">' . $name . ' </a>';
                            } elseif ($moduleQuery->option_name == 'micro_organization_status') {
                                $name = 'Organization Department';
                                $name_with_tooltip = ' <a class="right" title="" data-placement="right" data-toggle="tooltip" href="#" data-original-title="' . Lang::get('lang.organization_department_add_ability_to_setup_department_for_user_organization_and_this_can_be_used_for_assigning_SLA') . '">' . $name . ' </a>';

                            } elseif ($moduleQuery->option_name == 'satellite_helpdesk') {
                                $name = 'Satellite Helpdesk';
                                $name_with_tooltip = ' <a class="right" title="" data-placement="right" data-toggle="tooltip" href="#" data-original-title="' . Lang::get('lang.satellite_helpdesk') . '">' . $name . ' </a>';
                            }
                            elseif ($moduleQuery->option_name == 'batch_tickets') {
                                $name = ucwords(__('lang.batch-ticket'));
                                $name_with_tooltip = ' <a class="right" title="" data-placement="right" data-toggle="tooltip" href="#" data-original-title="' . __('lang.batch-ticket-description') . '">' . $name . ' </a>';

                            }elseif ($moduleQuery->option_name == 'time_track') {
                                $name = ucwords(__('lang.time-track'));
                                $name_with_tooltip = ' <a class="right" title="" data-placement="right" data-toggle="tooltip" href="#" data-original-title="' . __('lang.time-track-description') . '">' . $name . ' </a>';
                            }

                            return $name_with_tooltip;
                        })
                        ->editColumn('version', function ($moduleQuery) {

                            return '1.0.0';
                        })
                        ->addColumn('action', function ($moduleQuery) {

                            if ($moduleQuery->status == 1) {
                                return'<label class="switch toggle_event_editing">
                            <input type="hidden" name="module_name" class="module_name" value="' . $moduleQuery->option_name . '" >
                         <input type="checkbox" name="modules_settings" checked value="'.$moduleQuery->status.'"  class="modules_settings_value">
                          <span class="slider round"></span>
                        </label>';
                            } else {
                                return'<label class="switch toggle_event_editing">
                             <input type="hidden" name="module_name" class="module_name" value="' . $moduleQuery->option_name . '" >
                         <input type="checkbox" name="modules_settings" value="'.$moduleQuery->status.'" class="modules_settings_value">
                          <span class="slider round"></span>
                        </label>';
                            }
                        })
                        ->rawColumns(['name', 'version', 'action'])
                        ->make();

        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->with('fails', $ex->getMessage());
        }

    }

    /**
     *
     * @param Request $request
     * @return mixed Errors or Success message
     */
    public function ChangemoduleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_module_name'   => 'required',
            'current_module_status' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            $all_errors['current_module_name']   = $errors->first('current_module_name');
            $all_errors['current_module_status'] = $errors->first('current_module_status');

            return errorResponse($all_errors, 422);
        }

        try {
            $update = CommonSettings::where('option_name', $request->current_module_name)->first();
            $status=($request->current_module_status==1)?0:1;
            $update->status = $status;
            $update->save();

            if($request->current_module_name == 'satellite_helpdesk'){
               if (env('DB_INSTALL') == 1 && !Schema::hasTable('satellite_helpdesk')) {
                    $path = "app" . DIRECTORY_SEPARATOR . "SatelliteHelpdesk" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";
                    Artisan::call('migrate', [
                        '--path' => $path,
                        '--force' => true,
                     ]);
                }
            }

            // disable time track additional feature
            if ($update->option_name == 'time_track' && $request->current_module_status == 1) {
                $timeTrack = CommonSettings::where('option_name', 'time_track_option')->first();

                if ($timeTrack) {
                     $timeTrack->status = 0;
                    $timeTrack->save();
                }
            }

           return Lang::get('lang.your_status_updated_successfully');
        } catch (Exception $e) {
            return Redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Gets system settings related data for frontend to handle
     * NOTE: currently only header color is added, but more things will be added as admin panel grows
     * @return Response
     */
    public function getSystemSettings()
    {
      $visualSettings = DB::table('system_portal')->select('agent_header_color','admin_header_color')->first();
      $visualSettings->agent_header_color = Config::get("theme.header-color.$visualSettings->agent_header_color");
      $visualSettings->admin_header_color = Config::get("theme.header-color.$visualSettings->admin_header_color");

      // get currently selected language
      $visualSettings->language = App::getLocale();

      // get if it is RTL or not
      $visualSettings->is_rtl = $visualSettings->language == 'ar' ? true : false;

      return successResponse('', $visualSettings);
    }
    
}
