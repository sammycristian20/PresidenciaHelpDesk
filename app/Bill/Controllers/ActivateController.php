<?php

namespace App\Bill\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Artisan;

/**
 * Billing activation controller
 * 
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name ActivateController
 * 
 */
class ActivateController extends Controller
{
    /**
     * Activating the billing module
     */
    public function activate()
    {
        try {
            $this->migrate();
        } catch (Exception $ex) {

        }
    }
    /**
     * publishing the module in laravel way
     */
    public function publish()
    {
        try {
            $publish  = 'vendor:publish';
            $provider = 'App\Bill\BillServiceProvider';
            $tag      = "migrations";
            $r        = Artisan::call($publish, ['--provider' => $provider, '--tag' => [$tag]]);
            //dd($r);
        } catch (Exception $ex) {
            // dd($ex);
        }
    }
    /**
     * Running migration for bill
     */
    public function migrate()
    {
        try {
            $path = "app" . DIRECTORY_SEPARATOR . "Bill" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";
             Artisan::call('migrate', [
                '--path'  => $path,
                '--force' => true,
            ]);
            $this->seed();
        } catch (Exception $ex) {
        }
    }
    /**
     * 
     * Run seeding for bill
     * 
     * @return int
     */
    public function seed()
    {
        try {
            if(version_compare(commonSettings('bill', 'version'), config('bill.version')) < 0) {
                $version = str_replace(".", "_", config('bill.version'));
                Artisan::call('db:seed',['--class' => "App\Bill\database\seeds\\$version\DatabaseSeeder", '--force' => true]);
            }
        } catch (Exception $ex) {
            
        }
    }
}
