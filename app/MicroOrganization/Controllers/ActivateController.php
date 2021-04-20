<?php

namespace App\MicroOrganization\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Artisan;
/**
 * MicroOrganization activation controller
 * 
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name ActivateController
 * 
 */

class ActivateController extends Controller {
    /**
     * Activating the billing module
     */
    public function activate() {
        try {
            if (!\Schema::hasTable('MicroOrganization')) {
                $this->migrate();
            }
            //$this->seed();
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    /**
     * publishing the module in laravel way
     */
    public function publish() {
        try {
            $publish = 'vendor:publish';
            $provider = 'App\MicroOrganization\MicroOrganizationServiceProvider';
            $tag = "migrations";
            $r = Artisan::call($publish, ['--provider' => $provider, '--tag' => [$tag]]);
            //dd($r);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    /**
     * Running migration for MicroOrganization
     */
    public function migrate() {
        try {
            $path = "app" . DIRECTORY_SEPARATOR . "MicroOrganization" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    // /**
    //  * 
    //  * Run seeding for bill
    //  * 
    //  * @return int
    //  */
    // public function seed() {
    //     try {
    //         $controller = new BillSeeder();
    //         $controller->run();
    //         return 1;
    //     } catch (Exception $ex) {
    //         dd($ex);
    //     }
    // }

}
