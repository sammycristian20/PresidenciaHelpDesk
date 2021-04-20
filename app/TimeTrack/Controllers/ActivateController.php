<?php

namespace App\TimeTrack\Controllers;

use App\Http\Controllers\Controller;
use Artisan;
use Exception;

/**
 * Time track activation controller
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
        $this->migrate();
    }

    /**
     * publishing the module in laravel way
     */
    public function publish()
    {
        try {
            $publish  = 'vendor:publish';
            $provider = 'App\TimeTrack\BillServiceProvider';
            $tag      = "migrations";
            $r        = Artisan::call($publish, ['--provider' => $provider, '--tag' => [$tag]]);
        } catch (Exception $e) {
            throw new Exception("Failed to publish while activating Time Track Module. Exception: " . $e->getMessage());
        }
    }

    /**
     * Running migration for time track
     */
    protected function migrate()
    {
        try {
            if (!\Schema::hasTable('time_tracks')) {
                $path = "app" . DIRECTORY_SEPARATOR . "TimeTrack" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";
                Artisan::call('migrate', [
                    '--path'  => $path,
                    '--force' => true,
                ]);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to run migration while activating Time Track Module. Exception: " . $e->getMessage());
        }
    }

}
