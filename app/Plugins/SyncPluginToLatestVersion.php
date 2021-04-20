<?php

namespace App\Plugins;

use Exception;
use Illuminate\Support\Facades\DB;
use Artisan;
use App\Model\helpdesk\Settings\Plugin;
use Logger;

/**
 * To be able to use this installer you must have your migration and seeder structure like this :
 * Plugin
 *    PluginName
 *      database
 *        migrations
 *        seeds
 *          v_1_0_0 // seeders for version 1.0.0
 *          v_1_0_1 // seeders for version 1.0.1
 *
 *    config.php
 *               // this file will return an associative array which contains this:
 *               //  return [
 *               //         'name' => 'name_of_the_plugin',
 *               //         'description' => 'description_of_the_plugin',
 *               //         'author' => 'author_of_the_plugin',
 *               //         'website' => 'website',
 *               //         'version' => '1.0.0', //version number in this format
 *               //       ];
 *
 *
 *
 *
 * HOW TO USE:
 * Simply make an object of this class, call configure method and pass plugin name
 *      (new SyncPluginToLatestVersion)->call('PluginName');
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class SyncPluginToLatestVersion
{
    private $log;

    /**
     * runs migration required for the plugin
     * @return null
     */
    public function sync($plugin)
    {
        try {
          // running required migrations
          // if version is not same then we run this command
          $config = require(app_path().DIRECTORY_SEPARATOR."Plugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."config.php");
          $latestVersion = $config['version'];

          Plugin::updateOrCreate(['name' => $plugin])->version;

          $olderVersion = Plugin::where('name', $plugin)->first()->version;
          $olderVersion = $olderVersion ? $olderVersion : '0.0.0';
          if($latestVersion > $olderVersion){
            $this->updateToLatestVersion($latestVersion, $olderVersion, $plugin);
          }
          return $this->log;

        } catch (Exception $ex) {
          $this->log = $this->log . "\n" . $ex->getMessage();
            Logger::exception($ex);
        }
    }

    /**
     * Updates the plugin to latest version
     * @param  string $latestVersion  latest version
     * @param  string $olderVersion   older version
     * @return null
     */
    private function updateToLatestVersion(string $latestVersion, string $olderVersion, string $plugin)
    {
        Artisan::call('migrate', ['--path' => "app/Plugins/$plugin/database/migrations",'--force'=>true]);
        $this->handleArtisanLogs();

        // getting seeder base path
        $seederBasePath = app_path().DIRECTORY_SEPARATOR."Plugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."database".DIRECTORY_SEPARATOR."seeds";

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
              Artisan::call('db:seed',['--class' => "App\Plugins\\$plugin\database\seeds\\$version\DatabaseSeeder", '--force' => true]);
              $this->handleArtisanLogs();
            }
          }
         
        }
        //moved here since the plugins that doesn't have seeders can also update.
        Plugin::where('name', $plugin)->update(['version' => $latestVersion]);
    }

    /**
     * converts version number to make it compatible with namespace
     * 1.0.0 will be converted to v_1_0_0
     * @param  string $version
     * @return string
     */
    private function namespaceCompatibleVersionFormat($version)
    {
        $formatterVersion = "v_".str_replace(".",'_',$version);
        return $formatterVersion;
    }

    /**
     * Updates log variable which can be used for displaying the output
     * @return null
     */
    private function handleArtisanLogs()
    {
        $this->log = $this->log . "\n" .Artisan::output();
    }
}
