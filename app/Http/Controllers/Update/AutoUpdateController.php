<?php

namespace App\Http\Controllers\Update;

use File;
use Artisan;
use Updater; //self Updater facede
use Exception;
use Schema;
use App\Backup_path;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Database\Connection;
use Illuminate\Pagination\Paginator;
use Symfony\Component\Finder\Finder;
use App\Http\Controllers\Controller;
use App\Model\Update\BarNotification;
use App\Model\helpdesk\Settings\System;
use Codedge\Updater\Events\UpdateFailed;
use App\Http\Controllers\Utility\LibraryController;
use App\Http\Controllers\Update\SyncFaveoToLatestVersion;
use App\Http\Controllers\Dependency\FaveoDependencyController;
use Logger;
/**
 * ---------------------------------------------------
 * AutoUpdateController
 * ---------------------------------------------------
 * This controller handles all the auto update functions
 *
 * @author      Ladybird <info@ladybirdweb.com>,  Abhishek Gurung <abhishek.gurung@ladybirdweb.com>
 * @author      Ladybird <info@ladybirdweb.com>,  Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
 */

class AutoUpdateController extends Controller
{
    const NEW_VERSION_FILE = 'self-updater-new-version';

    protected $updater;
    protected $system;
    protected $client;

    public function __construct(Updater $updater, Client $client)
    {
        require_once(public_path('script/update_core_configuration.php'));
        require_once(public_path('script/update_core_functions.php'));
        $this->client = $client; //initialize guzzle client
        $this->updater = $updater; // initialize updater
        $this->system = System::first();
        $this->url = 'https://billing.faveohelpdesk.com';//Url where all the api related to auto update are sent
    }


    public function viewUpdates()
    {
        return view('themes.default1.admin.helpdesk.auto-updates.index');
    }


    public function viewDbNotUpdated()
    {
        if(\Config::get('app.tags') == System::first()->value('version')) {
            return redirect('auth/login');
        }
        return view('themes.default1.admin.helpdesk.auto-updates.db-not-updated');
    }

    public function updateDatabaseFromMiddleware()
    {
        try {
            \Artisan::call('database:sync');
            return successResponse(\Lang::get('lang.database_updated_success'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }


    /**
     * check for updates.
     *
     * checks for new updates on billing server
     * Updates dependency files based on version dependencies
     * checks for all the dependencies to run Faveo
     *
     */
    protected function checkForUpdates(Request $request)
    {
      try {
            FaveoDependencyController::checkForFaveoDependenciesFile();
            $this->checkEnvironment();
            $this->checkForFaveoLicenseTable();
            $data = $this->getLicenseCode();
            //Get the details of release
            $versionDetails = $this->getReleaseDetails();
            $latest = $this->getLatestVersionAndUpdateDependency($versionDetails);
            
            $versionDetails = $this->getVersionDetails($versionDetails, $latest);
            $response = $this->checkUpdatesExpiry($data);
            $result =json_decode($response->getBody()->getContents());
            \Storage::put(static::NEW_VERSION_FILE, $latest);
            return successResponse($versionDetails,$result);
        } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }


    /**
     * Get the latest version from the version details sent in the parameter
     * Check for Faveo dependencies before updating
     * @param  collection $versionDetails The details of all the version sent from billing
     */
    private function getLatestVersionAndUpdateDependency($versionDetails)
    {
        $latest = '';
        foreach ($versionDetails['version'] as $version) {
                if(!$version->is_private) {//If the release is private, it should not be displayed
                    $path = storage_path('faveo-dependencies.json');
                    if($version->is_restricted) {//If release is restricted, first make it as latest release so that client updates upto that version first
                    $latest = $version->version;
                    $this->updateDependencyFile($version->dependencies);
                    
                    break;
                    } else{
                        $latest = $version->version;
                        $this->updateDependencyFile($version->dependencies);
                    }
                } 
            }
            if(!$latest) {
                BarNotification::where('key', 'new-version')->delete();
                throw new \Exception(\Lang::get('lang.already_on_latest_version'));
            }
            return $latest;
            
    }

    private function getLicenseCode()
    {
        return Schema::hasTable('faveo_license') ? ['license_code' => \DB::table('faveo_license')->pluck('LICENSE_CODE')->first(),
        ] : ['license_code' => $this->system->serial_key];
    }

    /**
     * Checks if faveo is in production environment before auto update
     */
    private function checkEnvironment()
    {
        if(\App::environment() != 'production') {//If in production dont allow auto update
            $faveoNotInProduction = true;
            throw new Exception(\Lang::get('lang.faveoNotInProduction'));
            
        }
    }

    /**
     * Check if faveo license table exists or not before update
     */
    private function checkForFaveoLicenseTable()
    {
         if (!$this->system->serial_key && !Schema::hasTable('faveo_license')) {
            throw new Exception(\Lang::get('lang.license_not_installed'));
        }
    }

    private function getVersionDetails(&$versionDetails, $latest)
    {
        $cont = new \App\Http\Controllers\Admin\helpdesk\BackupController();
        $sumOfDbAndFilesystem = intval($cont->getDatabaseAndFileSystemSize());//Getting only integer part of the sum
        $model = new \App\Backup_path();
        $path  = $model->pluck('backup_path')->first();
        $isWhiteLabelEnabled = isWhiteLabelEnabled();
        $filesystem_space = getSize($sumOfDbAndFilesystem * 1024 *1024);
        $versionDetails['latest'] = $latest;
        $versionDetails['current'] = \Config::get('app.tags');
        $versionDetails['filesystem_space'] = $filesystem_space;
        $versionDetails['backup_path'] = $path;
        $versionDetails['white_label'] = $isWhiteLabelEnabled;
        return $versionDetails;
    }

    private function updateDependencyFile($dependencies)
    {
        try {
            if($dependencies) {
            $cont = new \App\Http\Controllers\Dependency\FaveoDependencyController('auto-update');
            file_put_contents(storage_path('faveo-dependencies.json'), $dependencies);
            $cont->validatePHPExtensions();
            $cont->validateRequisites();
            $cont->validateDirectory(base_path());
            }
            
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        
    }


    public function getLatestRelease()
    {
        $client = new Client([]);
        $source = $this->url.'/new-version-available?title='.\Config::get('self-update.app_name').'&version='.\Config::get('app.tags');
         $response = $client->request(
            'GET',
            $source
        );
        $releaseCollection = collect(\GuzzleHttp\json_decode($response->getBody()));
        if($releaseCollection['status'] == true) {
            BarNotification::updateOrCreate(['key' => 'new-version'],[
                "key" => "new-version",
                "value" => "New version(s) available. Please <a href='".url('check-updates')."'> click here </a> to update your system."
            ]);
        }
        

    }


    private function checkUpdatesExpiry($data)
    {
        try {//Check from Billing if the Auto Updates have  expired
            $expiryCheck = $this->client->request(
                'POST',
                $this->url.'/v1/checkUpdatesExpiry',
                [
                'form_params' => $data
                ]
            );
            return $expiryCheck;
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }


    public function update(Request $request)
    {
        set_time_limit(0);
        \Config::set('app.debug', true);
         
        try {
           Artisan::call('down');
           $version = \Storage::get(static::NEW_VERSION_FILE);
            
            $response=ausDownloadFile('version_upgrade_file',$version);//Download ann extract the files here
            
            if ($response['notification_case']=="notification_operation_ok") { //'notification_operation_ok' case returned - operation succeeded
               
                //Clear bootstrap/cache after new Files are replaced 
                 $files = glob(base_path().'/bootstrap/cache/*'); // get all file names
                    foreach($files as $file){ // iterate files
                      if(is_file($file))
                        unlink($file); // delete file
                    }
             //   System::first()->update(['version'=> $request->input('update_version')]);
               Artisan::call('up');
               return successResponse($response['notification_text']);
            } else {
               Artisan::call('up');
               return errorResponse($response['notification_text']);
               
            }
        } catch (\Exception $e) {
            Artisan::call('up');
            return errorResponse($e->getMessage());
        }
    }

    public function getOrderDetails()
    {
        return view('themes.default1.admin.helpdesk.auto-updates.order-details');
    }


    public function updateOrderDetails(Request $request)
    {
        $data = $request->except('_token');
        if ($this->system->update($data)) {
            return redirect('check-updates');
        }
    }


    /**
     * Get the details of all the new release from billing
     */
    private function getReleaseDetails()
    {
        $client = new Client([]);
        $source = $this->url.'/version/latest?title='.\Config::get('self-update.app_name').'&version='.\Config::get('app.tags');
         $response = $client->request(
            'GET',
            $source
        );
        $releaseCollection = collect(\GuzzleHttp\json_decode($response->getBody()));
        if(!array_key_exists('error', $releaseCollection->toArray()) && $releaseCollection['version']) {
            return $releaseCollection;
        } else {
            throw new \Exception(\Lang::get('lang.already_on_latest_version'));
        }
        
    }
        

    protected function updateDatabase()
    {
        try {
            $latestConfigVersion = \Config::get('app.tags');
            $latestVersion = trim(\Storage::get('self-updater-new-version'));
            if ($latestConfigVersion == $latestVersion) {
                (new SyncFaveoToLatestVersion)->sync();
                 return successResponse('Operation successful. Your installation has been updated successfully');
            }
            $message  = "Could not update the database due to version mismatch. Latest version: ".$latestVersion." and latest version in config: ".$latestConfigVersion.". Try reloading the page to rerun database update or contact support.";
            throw new Exception($message, FAVEO_ERROR_CODE);
            
           
        } catch (\Exception $ex) {
            Logger::exception($ex);
            return errorResponse($ex->getMessage());
        }
        
    }
}
