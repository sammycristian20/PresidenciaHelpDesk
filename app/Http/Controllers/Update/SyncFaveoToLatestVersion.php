<?php

namespace App\Http\Controllers\Update;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Artisan;
use App\Model\helpdesk\Settings\System;
use Config;
use Logger;
use Schema;
use App\Model\Update\BarNotification;
use App\Http\Controllers\Admin\helpdesk\SettingsController;
/**
 *
 * NOTE: it will run sql files till v1.9.47, after that it will run migration and seeder
 * To be able to use this installer you must have your migration and seeder structure like this :
 *
 *
 *   database
 *     migrations
 *     seeds
 *       v_1_0_0 // seeders for version 1.0.0
 *       v_1_0_1 // seeders for version 1.0.1
 *
 *
 *
 * HOW TO USE:
 * Simply make an object of this class, call configure method and pass plugin name
 *      (new SyncToLatestVersion)->call('PluginName');
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class SyncFaveoToLatestVersion
{
    /**
     * Writing all the artisan logs to this varible so that it could be returned in the end
     * @var string
     */
    private $log = '';

    /**
     * runs migration required for the plugin
     * @return null
     * @throws Exception
     */
    public function sync()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);

        // in case where isInstall is false(in case of new install) version number should be zero
        $latestVersion = Config::get('app.tags');
        $olderVersion = $this->getOlderVersion();

        try {

            if($latestVersion > $olderVersion){
                $this->updateToLatestVersion($latestVersion, $olderVersion);
                $this->syncLinkFile();
                $this->restartQueueProcessing();
                $this->clearViewCache();
                $this->clearConfig();
                $this->clearUpdateNotification();
                $this->updateSystemLatestVersionInFaveoBilling();
            }

        } catch (Exception $ex) {
            if(!isInstall()){
               //if system is not installed chances are logs tables are not present
               throw $ex;
            }
            
            $this->log = $this->log . "\n" . $ex->getMessage();
            Logger::exception($ex);
        }

        System::first()->update(['version'=> $latestVersion]);
        $this->cacheDbVersion();
        
        return $this->log;
    }

    private function cacheDbVersion()
    {
        $filesystemVersion = \Config::get('app.tags');
        \Cache::forget($filesystemVersion);
        $dbversion = \Cache::remember($filesystemVersion, 3600, function () use($filesystemVersion)  {//Caching version for 1 hr
            return System::first()->value('version');
        });
    }

    /**
     * gets older version from the DB. If DB is not migrated, it gives v0.0.0
     * @return string
     */
    private function getOlderVersion() : string
    {
      if(!isInstall()){
        return 'v0.0.0';
      }

      $olderVersion = System::first()->version;
      $olderVersion = $olderVersion ? $olderVersion : 'v0.0.0';
      return $olderVersion;
    }

    /**
     * Updates the plugin to latest version
     * @param  string $latestVersion  latest version
     * @param  string $olderVersion   older version
     * @return null
     */
    private function updateToLatestVersion(string $latestVersion, string $olderVersion)
    {
        $this->handleOlderVersionUpdate($olderVersion);

        $this->updateMigrationTableTill47($olderVersion);

        // after older version is updated, update to the latest version in which seeder versioning is implemented
        Artisan::call('migrate', ['--force'=>true]);

        $this->handleArtisanLogs();

        // getting seeder base path
        $seederBasePath = base_path().DIRECTORY_SEPARATOR."database".DIRECTORY_SEPARATOR."seeds";

        // get all directories inside seeder folder
        // sort versions from oldest to latest
        if(file_exists($seederBasePath)){
          $seederVersions = scandir($seederBasePath);

          ksort($seederVersions);

          // convert older and newer version into underscore format
          $formattedOlderVersion = $this->namespaceCompatibleVersionFormat($olderVersion);
          foreach ($seederVersions as $version) {
            if($version > $formattedOlderVersion){
              // scan for $version directory and get file names
              $this->log = $this->log . "\n" . "Running Seeder for version $version";

              Artisan::call('db:seed',['--class' => "database\seeds\\$version\DatabaseSeeder", '--force' => true]);
              $this->handleArtisanLogs();
            }
          }
        }
    }

    /**
     * converts version number to make it compatible with namespace
     * 1.0.0 will be converted to v_1_0_0
     * @param  string $version
     * @return string
     */
    public function namespaceCompatibleVersionFormat($version)
    {
        if(strpos($version,'v') !== false){
          $version = explode('v', $version)[1];
        }

        // the version that it will recieve will have a v in it
        // if v already exists don't append that
        $formatterVersion = "v_".str_replace(".",'_',$version);
        return $formatterVersion;
    }

    /**
     * Runs sql files for versions older than v1.9.47
     * NOTE: it was implemented in a way that it had folder with one version older than the latest version
     * So if latest version is 1.
     * @return null
     */
    private function handleOlderVersionUpdate(string &$olderVersion)
    {
        if($olderVersion == 'v0.0.0'){
          return true;
        }

        // scan for version numbers in DB/mysql directory and run all of that which starts after the current version till the end
        $basePathOfSqlFiles = base_path().DIRECTORY_SEPARATOR.'DB'.DIRECTORY_SEPARATOR.'mysql';
        $sqlFileVersions = scandir($basePathOfSqlFiles);

        foreach ($sqlFileVersions as $sqlFileVersion) {
          if($sqlFileVersion > $olderVersion){

            // only run these sql files
            $this->log = $this->log . "\n" . "Runnng SQL file for version $sqlFileVersion";
            $pathToSqlFile = $basePathOfSqlFiles.DIRECTORY_SEPARATOR.$sqlFileVersion.DIRECTORY_SEPARATOR.'updatedatabase.sql';
            if(file_exists($pathToSqlFile)){
              DB::unprepared(file_get_contents($pathToSqlFile));
            }
          }
        }

        DB::statement('SET AUTOCOMMIT = 1');

        $olderVersion = $this->getOlderVersion();
    }

    /**
     * Updates log variable which can be used for displaying the output
     * @return null
     */
    private function handleArtisanLogs()
    {
        $this->log = $this->log . "\n\n" .Artisan::output();
    }

    /**
     * Updates migration table till 1.9.47 migrations
     * @return null
     */
    private function updateMigrationTableTill47(string $olderVersion)
    {
      if($olderVersion != 'v0.0.0'){
        $oldMigrationPath = base_path().DIRECTORY_SEPARATOR."DB".DIRECTORY_SEPARATOR."OldMigrationList.php";
        $oldMigrations = include($oldMigrationPath);

        foreach ($oldMigrations as $migration) {
          if(!DB::table('migrations')->where('migration',$migration['migration'])->count()){
            DB::table('migrations')->insert(['migration' => $migration['migration'], 'batch'=>$migration['batch']]);
          }
        }
      }
    }

    /**
     * Function retains or updates the link.php files according to 
     * user's preference for CDN/NON-CDN settings
     *
     */
    private function syncLinkFile()
    {
        $this->log = $this->log . "\n\n" .(new SettingsController)->cdnSettings(). "\n\n";
    }

    private function restartQueueProcessing()
    {
      Artisan::call('queue:restart');
      $this->handleArtisanLogs();
    }
    
    /*
     * Clears the view cache while update
     */
    private function clearViewCache()
    {
        Artisan::call('view:clear');
        $this->handleArtisanLogs();
    }

    /**
     * Clears the config cache
     */
    private function clearConfig()
    {
      Artisan::call('config:clear');
      $this->handleArtisanLogs();
    }

    /**
     * Deletes row from bar_notification table due to which up new update available notification shows up.
     *
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     *
     * @date   2020-03-05T10:36:56+0530
     *
     * @return void
     */
    public function clearUpdateNotification()
    {
        $notify = BarNotification::where('key', 'new-version')->delete();
    }

    /**
     * As sson as the database update completes updates the latest versin in billing for this installation
     */
    private function updateSystemLatestVersionInFaveoBilling()
    {
        if (Schema::hasTable('faveo_license')) {
            $currentVersion = \Config::get('app.tags');
            $licenseCode = \DB::table('faveo_license')->pluck('LICENSE_CODE')->first();
            if($licenseCode) {
            $client = new Client([]);
            $clientUrl  = url('/');
            
            $source = "https://billing.faveohelpdesk.com/update-latest-version?version=".$currentVersion."&licenseCode=".$licenseCode."&url=".$clientUrl;
           
            $response = $client->request('POST', $source);
        }
        
        }
    }
}
