<?php

namespace App\Http\Controllers\Installer\helpdesk;

// controllers
use DB;
use File;
// requests
use Hash;
use Illuminate\Support\Str;
use View;
use Cache;
use Crypt;
// models
use Input;
use Config;
use UnAuth;
use Artisan;
use Session;
// classes
use App\User;
use Redirect;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Utility\Timezones;
use Illuminate\Database\Schema\Blueprint;
use App\Http\Requests\helpdesk\DatabaseRequest;
use App\Http\Requests\helpdesk\InstallerRequest;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Utility\Date_time_format;
use App\Http\Controllers\Utility\LibraryController;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Http\Controllers\Update\SyncFaveoToLatestVersion;

/**
 * |=======================================================================
 * |Class: InstallController
 * |=======================================================================.
 *
 *  Class to perform the first install operation without this the database
 *  settings could not be started
 *
 *  @author     Ladybird <info@ladybirdweb.com>
 */
class InstallController extends Controller
{
  

    /**
     * Get License Agreement Page
     * @return type view
     */
    public function licAgreement(Request $request)
    {
         if (Session::has('fails')) {
            Session::flush();
         }
          $count = 0;
        //Check if any errors are being sent from probe.php
        $errorCount = $request->input('count');
        if($errorCount == "0" && $errorCount == 0) {
             if (Cache::get('getting-started') == 'getting-started') {
            return Redirect::route('getting-started');
        }
        // checking if the installation is running for the first time or not
        if (isInstall()) {
            return redirect('/auth/login');
        } else {
                \Cache::flush();
                Artisan::call('config:clear');
                \Cache::forever('pre-license', 'pre-license');
                 return Redirect::route('license-agreement');
         }
        } else {
            return redirect()->back();
        }
    }



    public function installLicenseScript($serial)
    {
        $url = url('/');
         // checking if the installation have been completed or not
        $GLOBALS["mysqli"]=mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE')); //establish connection to MySQL database
        $name = \Config::get('app.name');
        $version = \Config::get('app.tags');
        $license_notifications = aplInstallLicense($url,"",$serial,$GLOBALS["mysqli"]); //install personal (code-based) license using MySQL database
        return $license_notifications;


    }


    public function postSerialKey($result, $serial, $return = true)
    {
        if (Session::has('fails')) {
            Session::flush();
         }
        if ($result['notification_case']=="notification_license_ok") {
            Cache::forever('serial', 'serial');
            Cache::forever('serialkey', $serial);
            if ($return) {
                return Redirect::route('license-agreement');
                return Redirect::route('license-agreement');
            }
            return "success";
        } else {
            Session::put('fails','License Code verification Failed.'.$result['notification_text']);
            return view('themes/default1/installer/helpdesk/serialkey');
         }
    }



    /**
     * Get Licence (step 1).
     *
     * @return type view
     */
    public function licence(Request $request)
    {


        if (Cache::get('pre-license') == 'pre-license') {
        //    checking if the installation is running for the first time or not
            if (isInstall()) {
                return redirect('/auth/login');
            } else {
                return view('themes/default1/installer/helpdesk/view1');
            }
        } else {
            return redirect()->to('/probe.php');
        }
    }

    /**
     * Post Licencecheck.
     *
     * @return type view
     */
    public function licencecheck(Request $request)
    {
        // checking if the user have accepted the licence agreement
        $accept = (Input::has('accept1')) ? true : false;
        if ($accept == 'accept') {
            Cache::forever('license-agreement', 'license-agreement');
            return Redirect::route('prerequisites');
        } else {
            return Redirect::route('license-agreement')->with('fails', 'Failed! first accept the licence agreeement');
        }
    }

    /**
     * Get prerequisites (step 2).
     *
     * Checking the extensions enabled required for installing the faveo
     * without which the project cannot be executed properly
     *
     * @return type view
     */
    public function prerequisites(Request $request)
    {
        // checking if the installation is running for the first time or not
        if (Cache::get('license-agreement') == 'license-agreement') {
            return View::make('themes/default1/installer/helpdesk/view2');
        } else {
            return Redirect::route('licence');
        }
    }

    /**
     * Post Prerequisitescheck
     * checking prerequisites.
     *
     * @return type view
     */
    public function prerequisitescheck(Request $request)
    {
        if(!$request->has('accept1')) {
            return redirect($request->root().'/probe.php');
        }
        $accept = (Input::has('accept1')) ? true : false;
        if ($accept == 'Continue') {
            Cache::forever('license-agreement', 'license-agreement');
            return Redirect::route('db-setup');
        } else {
             return Redirect::route('licence');
        }

    }

    /**
     * Get Localization (step 3)
     * Requesting user recomended settings for installation.
     *
     * @return type view
     */
    public function localization(Request $request)
    {
        // checking if the installation is running for the first time or not
        if (Cache::get('db-setup') == 'db-setup') {
            return View::make('themes/default1/installer/helpdesk/view3');
        } else {
            return Redirect::route('prerequisites');
        }
    }

    /**
     * Post localizationcheck
     * checking prerequisites.
     *
     * @return type view
     */
    public function localizationcheck(Request $request)
    {
        Cache::forever('config-check', 'config-check');

        $request->session()->put('config-check', 'config-check');
        $request->session()->put('language', Input::get('language'));
        $request->session()->put('timezone', Input::get('timezone'));
        $request->session()->put('date', Input::get('date'));
        $request->session()->put('datetime', Input::get('datetime'));

        return Redirect::route('configuration');
    }

    /**
     * Get Configuration (step 4)
     * checking prerequisites.
     *
     * @return type view
     */
    public function configuration(Request $request)
    {
        // checking if the installation is running for the first time or not
        if (Cache::get('license-agreement') == 'license-agreement') {
            return View::make('themes/default1/installer/helpdesk/view3');
        } else {
            return Redirect::route('license-agreement');
        }
    }

    /**
     * Post configurationcheck
     * checking prerequisites.
     *
     * @return type view
     */
    public function configurationcheck(Request $request)
    {
        Cache::forever('config-check', 'config-check');
        Session::put('default', 'mysql');
        Session::put('host', $request->input('host'));
        Session::put('databasename', $request->input('databasename'));
        Session::put('username', $request->input('username'));
        Session::put('password', $request->input('password'));
        Session::put('port', $request->input('port'));
        Cache::forever('dummy_data_installation', false);
        if ($request->has('dummy-data')) {
            Cache::forget('dummy_data_installation');
            Cache::forever('dummy_data_installation', true);
        }
        return Redirect::route('database');
    }

    /**
     * Get database
     * checking prerequisites.
     *
     * @return type view
     */
    public function database(Request $request)
    {
    // checking if the installation is running for the first time or not
        if (Cache::get('config-check') == 'config-check'){
            return View::make('themes/default1/installer/helpdesk/view4');
        } else{
            return Redirect::route('config');
        }
    }

    /**
     * Get account
     * checking prerequisites.
     *
     * @return type view
     */
    public function account(Request $request)
    {
        // checking if the installation is running for the first time or not
        if (Cache::get('config-check') == 'config-check') {
            Cache::put('timezone',$request['timezone']);
            $request->session()->put('getting-started', $request->input('getting-started'));
             return View::make('themes/default1/installer/helpdesk/view5');
        } else {
            return Redirect::route('license-agreement');
        }
    }

    /**
     * Post accountcheck
     * checking prerequisites.
     *
     * @param type InstallerRequest $request
     *
     * @return type view
     */
    public function accountcheck(Request $request)
    {
        $validator = \Validator::make($request->all(), [
                    'firstname' => 'required|max:20',
                    'Lastname' => 'required|max:20',
                    'email' => 'required|max:50|email',
                    'username' => [
                    'required', 'required',
                    'regex:/^(?:[@A-Z\d][A-Z\d.@_-]{2,20}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,5})$/i',
                    'unique:users,user_name,'
                ],
                    'password' => 'required|min:6',
                            'confirm_password' => 'required|same:password',
                    'driver' => 'required|string',
                    'aws_access_key_id' => 'sometimes|nullable|required_if:driver,==,s3',
                    'aws_access_key' => 'sometimes|nullable|required_if:driver,==,s3',
                    'aws_default_region' => 'sometimes|nullable|required_if:driver,==,s3',
                    'aws_bucket' => 'sometimes|nullable|required_if:driver,==,s3',
                    'aws_endpoint' => 'sometimes|nullable|required_if:driver,==,s3'
                ],[
                     'username.regex' => 'Username should have minimum 3 and maximum 20 characters and can have only alphanumeric characters, spaces, underscores, hyphens, periods and @ symbol.',
                     '*.required_if' => ':attribute is required when the default storage driver is S3',
                     'driver.required' => 'Default storage driver is required field',
                ]);

        if ($validator->fails()) {
            return redirect('getting-started?timezone='.$request->input('timezone'))
                            ->withErrors($validator)
                            ->withInput();
        }
        
        // Set variables fetched from input request
        $firstname = $request->input('firstname');
        $lastname = $request->input('Lastname');
        $email = $request->input('email');
        Session::put('email', $email);
        $username = $request->input('username');
        $password = $request->input('password');

        $language = $request->input('language');
        $timezone = $request->input('timezone');
        $date = $request->input('date');
        $datetime = 'F j, Y, g:i a';
        $lang_path = base_path('resources/lang');

        $driver = $request->driver;

        FileSystemSettings::first()->update(['disk' => $driver]);

        ($request->aws_access_key_id) ? Cache::forever('aws_access_key_id', $request->aws_access_key_id) : null;
        ($request->aws_access_key) ? Cache::forever('aws_access_key', $request->aws_access_key) : null;
        ($request->aws_default_region) ?  Cache::forever('aws_default_region', $request->aws_default_region) : null;
        ($request->aws_bucket) ? Cache::forever('aws_bucket', $request->aws_bucket) : null;
        ($request->aws_endpoint)
            ? Cache::forever(
                'aws_endpoint',
                (! Str::startsWith($request->aws_endpoint, ['https://'])) ? "https://{$request->aws_endpoint}" : $request->aws_endpoint
            )
            : null;
        //check user input language package is available or not in the system
        if (array_key_exists($language, \Config::get('languages')) && in_array($language, scandir($lang_path))) {
            // do something here
        } else {
            return \Redirect::back()->with('fails', 'Invalid language');
        }

        $changed = UnAuth::changeLanguage($language);
        if (!$changed) {
            return \Redirect::back()->with('fails', 'Invalid language');
        }

        $system = System::where('id', '=', 1)->first();
        $system->status = 1;
        $system->department = 1;
        $system->date_time_format = $datetime; //$date_time_format->id;
        $timeZoneId = Timezones::where('name', $timezone)->value('id');
        $system->time_zone_id = $timeZoneId; //$timezones->id;
        $version = \Config::get('app.tags');
        // $version = explode(' ', $version);
        // $version = $version[1];
        $system->version = $version;
        $system->save();

        // updating business hours
        $bhours = BusinessHours::where('id', '=', 1)->first();
        $bhours->timezone = $timezone;
        $bhours->save();

        $admin_tzone = 14;
        if ($timeZoneId) {
            $admin_tzone = $timeZoneId;
        }
        // getting the dispatcher instance (needed to enable again the event observer later on)
        $dispatcher = User::getEventDispatcher();

        // disabling the events so that this user can be created even if the agent limit for Faveo is exceeded
        User::unsetEventDispatcher();
        // creating an user
        $user = User::updateOrCreate(['id' => 1], [
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'email' => $email,
                    'user_name' => $username,
                    'password' => Hash::make($password),
                    //'assign_group' => 1,
                    'agent_tzone' => $admin_tzone, //assuming
                    'primary_dpt' => 1,
                    'active' => 1,
                    'role' => 'admin',
                    'email_verify' => 1,
                    'mobile_verify'=> 0
        ]);

        // enabling the event dispatcher
        User::setEventDispatcher($dispatcher);

        // checking if the user have been created
        if ($user) {
            
            $dept_assign_agent = DepartmentAssignAgents::updateOrCreate(['id' => 1], [
                        'department_id' => 1,
                        'agent_id' => 1,
            ]);
            Cache::forever('getting-started', 'getting-started');
            Cache::forever('env', $request->input('environment'), 'production');
           
          return view('themes/default1/installer/helpdesk/serialkey');

        }
    }

    /**
     * Get finalize
     * checking prerequisites.
     *
     * @return type view
     */
    public function finalize(Request $request)
    {
        $serial = $request->input('first') . $request->input('second') . $request->input('third') . $request->input('forth');
        $result = $this->installLicenseScript($serial);
        if ($result['notification_case']=="notification_license_ok") {
            Cache::forever('license-code', 'license-code');
            Cache::forever('serialkey', $serial);
            $language = Cache::get('language');
            $environment = Cache::get('env');
            $aws_secret_access_key = Cache::get('aws_access_key');
            $aws_access_key_id = Cache::get('aws_access_key_id');
            $aws_default_region = Cache::get('aws_default_region');
            $aws_bucket = Cache::get('aws_bucket');
            $aws_endpoint = Cache::get('aws_endpoint');
            try {
                \Cache::flush();
                \Cache::forever('language', $language);
                $this->updateInstalEnv($environment, array_filter(compact('aws_secret_access_key','aws_access_key_id','aws_default_region','aws_bucket','aws_endpoint')));
                Session::flush();
                return View::make('themes/default1/installer/helpdesk/view6');
            } catch (Exception $e) {
                return Redirect::route('getting-started')->with('fails', $e->getMessage());
            }
            } else {
            Session::put('fails', 'License Code verification Failed.'.$result['notification_text']);
            return view('themes/default1/installer/helpdesk/serialkey');
        }
     }

    /**
     * Post finalcheck
     * checking prerequisites.
     *
     * @return type view
     */
    public function finalcheck()
    {
        try {
            $this->updateInstalEnv('production');
            return redirect('/auth/login');
        } catch (Exception $e) {
            return redirect('/auth/login')->with('fails', $e->getMessage());
        }
    }

    public function changeFilePermission()
    {
        $path1 = base_path() . DIRECTORY_SEPARATOR . '.env';
        if (chmod($path1, 0644)) {
            $f1 = substr(sprintf('%o', fileperms($path1)), -3);
            if ($f1 >= '644') {
                return Redirect::back();
            } else {
                return Redirect::back()->with(
                    'fail_to_change',
                    'We are unable to change file permission on your server please try to change permission manually.'
                );
            }
        } else {
            return Redirect::back()->with(
                'fail_to_change',
                'We are unable to change file permission on your server please try to change permission manually.'
            );
        }
    }

    public function jsDisabled()
    {
        return view('themes/default1/installer/helpdesk/check-js')->with('url', 'license-agreement');
    }

    /**
     * function to save pro version key and details
     * @param type $order_no
     * @param type $serial
     * @return boolean
     */
    public function saveKey($serial)
    {
        if (Cache::get('serial') == 'serial') {
            \Schema::create('pro_serial_key', function (Blueprint $table) {
                $table->increments('id');
                $table->text('serial_key');
                $table->timestamps();
            });

            \DB::table('pro_serial_key')->insert(['serial_key' => Crypt::encrypt($serial)
            ]);
            return true;
        } else {
            return false;
        }
    }

    public function createEnv($api = true)
    {
        try {
            if (Input::get('default')) {
                $default = Input::get('default');
            } else {
                $default = Session::get('default');
            }
            if (Input::get('host')) {
                $host = Input::get('host');
            } else {
                $host = Session::get('host');
            }
            if (Input::get('databasename')) {
                $database = Input::get('databasename');
            } else {
                $database = Session::get('databasename');
            }
            if (Input::get('username')) {
                $dbusername = Input::get('username');
            } else {
                $dbusername = Session::get('username');
            }
            if (Input::get('password')) {
                $dbpassword = Input::get('password');
            } else {
                $dbpassword = Session::get('password');
            }
            if (Input::get('port')) {
                $port = Input::get('port');
            } else {
                $port = Session::get('port');
            }
            $this->env($default, $host, $port, $database, $dbusername, $dbpassword);
        } catch (Exception $ex) {
            $result = ['error' => $ex->getMessage()];
            return response()->json(compact('result'), 500);
        }
        if ($api) {
            Cache::forever('databasename', $database);
            $url = url('preinstall/check');
            $result = ['success' => 'Environment configuration file has been created successfully', 'next' => 'Running pre migration test', 'api' => $url];
            return response()->json(compact('result'));
        }
    }

    public function env($default, $host, $port, $database, $dbusername, $dbpassword, $appUrl = null)
    {
        $ENV['APP_NAME'] = 'Faveo:'.md5(uniqid());
        $ENV['APP_DEBUG'] = 'false';
        $ENV['APP_BUGSNAG'] = 'true';
        $ENV['APP_URL'] = $appUrl ? $appUrl : url('/'); // for CLI installation
        $ENV['APP_KEY'] = "base64:h3KjrHeVxyE+j6c8whTAs2YI+7goylGZ/e2vElgXT6I=";
        $ENV['DB_TYPE'] = $default;
        $ENV['DB_HOST'] = '"'.$host.'"';
        $ENV['DB_PORT'] = '"'.$port.'"';
        $ENV['DB_DATABASE'] = '"'.$database.'"';
        $ENV['DB_USERNAME'] = '"'.$dbusername.'"';
        $ENV['DB_PASSWORD'] = '"'.str_replace('"', '\"', $dbpassword).'"';
        $ENV['DB_ENGINE']   = 'MyISAM'; //must be removed after fixing code for foreign key contraints for InnoDB
        $ENV['MAIL_DRIVER'] = 'smtp';
        $ENV['MAIL_HOST'] = 'mailtrap.io';
        $ENV['MAIL_PORT'] = '2525';
        $ENV['MAIL_USERNAME'] = 'null';
        $ENV['MAIL_PASSWORD'] = 'null';
        $ENV['CACHE_DRIVER'] = 'file';
        $ENV['SESSION_DRIVER'] = 'file';
        $ENV['SESSION_COOKIE_NAME'] = 'faveo_'.  rand(0, 10000);
        $ENV['QUEUE_DRIVER'] = 'sync';
        $ENV['FCM_SERVER_KEY'] = 'AIzaSyBJNRvyub-_-DnOAiIJfuNOYMnffO2sfw4';
        $ENV['FCM_SENDER_ID'] = '505298756081';
        $ENV['PROBE_PASS_PHRASE'] = md5(uniqid());
        $ENV['REDIS_DATABASE'] = '0';
        $ENV['BROADCAST_DRIVER']='pusher';
        $ENV['LARAVEL_WEBSOCKETS_ENABLED']='false';
        $ENV['LARAVEL_WEBSOCKETS_PORT']=6001;
        $ENV['LARAVEL_WEBSOCKETS_HOST'] ='127.0.0.1';
        $ENV['LARAVEL_WEBSOCKETS_SCHEME'] ='http';
        $ENV['PUSHER_APP_ID'] = str_random(16);
        $ENV['PUSHER_APP_KEY'] = md5(uniqid());
        $ENV['PUSHER_APP_SECRET'] = md5(uniqid());
        $ENV['PUSHER_APP_CLUSTER'] = 'mt1';
        $ENV['MIX_PUSHER_APP_KEY']='"${PUSHER_APP_KEY}"';
        $ENV['MIX_PUSHER_APP_CLUSTER']='"${PUSHER_APP_CLUSTER}"';
        $ENV['SOCKET_CLIENT_SSL_ENFORCEMENT'] = 'false';
        $ENV['LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT'] = 'null';
        $ENV['LARAVEL_WEBSOCKETS_SSL_LOCAL_PK'] = 'null';
        $ENV['LARAVEL_WEBSOCKETS_SSL_PASSPHRASE'] = 'null';
        $config = '';
        foreach ($ENV as $key => $val) {
            $config .= "{$key}={$val}\n";
        }
        if (is_file(base_path() . DIRECTORY_SEPARATOR . '.env')) {
            unlink(base_path() . DIRECTORY_SEPARATOR . '.env');
        }
        if (!is_file(base_path() . DIRECTORY_SEPARATOR . 'example.env')) {
            fopen(base_path() . DIRECTORY_SEPARATOR . 'example.env', "w");
        }

        // Write environment file
        $fp = fopen(base_path() . DIRECTORY_SEPARATOR . 'example.env', 'w');
        fwrite($fp, $config);
        fclose($fp);
        rename(base_path() . DIRECTORY_SEPARATOR . 'example.env', base_path() . DIRECTORY_SEPARATOR . '.env');
    }

    public function checkPreInstall()
    {
        try {
            $check_for_pre_installation = System::select('id')->first();
            if ($check_for_pre_installation) {
                throw new Exception('This database already has tables and data. Please provide fresh database', 100);
            }
        } catch (Exception $ex) {
            if ($ex->getCode() == 100) {
                Artisan::call('droptables');
                $this->createEnv(false);
            }
        }
        Artisan::call('key:generate', ['--force' => true]);

        $url = url('migrate');
        $result = ['success' => 'Pre migration has been tested successfully', 'next' => 'Migrating tables in database', 'api' => $url];
        return response()->json(compact('result'));
    }

    public function migrate()
    {
        $db_install_method = '';
        try {
            if(Cache::get('databasename') != env('DB_DATABASE')) {
                throw new Exception("Database connection did not update.", 500);
            }
            $tableNames = \Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
            //allowing migrations table in db as it does not get removed on "migrate:reset"
            $tableNames = array_unique(array_merge(['migrations'], $tableNames ));
            if (count($tableNames) === 1) {
                (new SyncFaveoToLatestVersion)->sync();

                if (Cache::get('dummy_data_installation')) {
                    $path = base_path().DIRECTORY_SEPARATOR.'DB'.DIRECTORY_SEPARATOR.'dummy-data.sql';
                    \DB::unprepared(file_get_contents($path));
                }
            }
        } catch (Exception $ex) {
            $this->rollBackMigration();
            $result = ['error' => $ex->getMessage()];
            return response()->json(compact('result'), 500);
        }

        $message = 'Database has been setup successfully.';
        $result = ['success' => $message, 'next' => 'Seeding pre configurations data'];
        return response()->json(compact('result'));
    }

    public function rollBackMigration()
    {
        try {
            Artisan::call('migrate:reset', ['--force' => true]);
        } catch (Exception $ex) {
            $result = ['error' => $ex->getMessage()];
            return response()->json(compact('result'), 500);
        }
    }

    public function updateInstalEnv(string $environment, $awsCredentials = [])
    {
        $env = base_path() . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($env)) {
            throw new Exception('.env not found');            
        }
        $txt = "DB_INSTALL=1";
        $txt1 = "APP_ENV=$environment";
        file_put_contents($env, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents($env, $txt1 . PHP_EOL, FILE_APPEND | LOCK_EX);

        foreach ($awsCredentials as $key => $value) {
            $line = strtoupper($key) . '=' . $value . PHP_EOL;
            file_put_contents($env, $line, FILE_APPEND | LOCK_EX);
        }

        Artisan::call('jwt:secret', ['--force' => true]);
    }
}
