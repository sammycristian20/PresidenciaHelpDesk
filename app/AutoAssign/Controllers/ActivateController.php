<?php

namespace App\AutoAssign\Controllers;

use App\Http\Controllers\Controller;
use Artisan;

/**
 * Activating Auto assign module
 * 
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name ActivateController
 * 
 */
class ActivateController extends Controller {

    public function __construct() {
        $this->middleware('install');
    }
    /**
     * 
     * Activating the module
     * 
     * @name activate
     * @return string 
     */
    public function activate() {
        try {
            if (!\Schema::hasColumn('users', 'is_login')) {
                $this->migrate();
            }
        } catch (Exception $ex) {
//            dd($ex);
        }
    }
    /**
     * 
     * Migration of auto assign
     * 
     * @name migrate
     * @return string
     */
    public function migrate() {
        try {
            $path = "app" . DIRECTORY_SEPARATOR . "AutoAssign" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
        } catch (Exception $ex) {
//            dd($ex);
        }
    }

}
