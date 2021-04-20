<?php

namespace App\Console\Commands;

use Laravel\Horizon\Lock;
use Illuminate\Console\Command;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Console\SnapshotCommand as ParentSnapshotCommand;

/**
 * Extends artisan command class SnapshotCommand of Horizon's metric snapshot. As
 * Horizon only supports "redis" as queue driver we must ensure that the command
 * only executes when system's queue driver is set as redis.
 *
 * NOTE: this is not the ideal way of executing artisan commands based on conditions
 * instead truth constraint closure can be used while scheduling the command in Kernal.php.
 * because running schedule:run will still try to execute "horizon:snapshot" command.
 * But as our application stores the artisan commands in the database to provide power to the
 * end users to decide whether they want to execute certain commands or not and the frequency
 * of execution of all the commands individually. Also, it prevents further modification in
 * kernal.php by different developers(except adding new command class in $command array). So
 * we have to check the constraint in handle method of the command class itself. However
 * for custom module and plugin development the authors are advised to use truth constraint
 * of task scheduler in module's/plugin's Kerner.php.
 * @link https://laravel.com/docs/5.8/scheduling
 *
 * @category Artisan Command
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @since v2.1.2
 * 
 * @todo monitor Laravel Horizon package for any modification in the command class and
 * update this class accordingly. 
 */
class SnapshotCommand extends ParentSnapshotCommand
{
    public function handle(Lock $lock, MetricsRepository $metrics)
    {
    	if(!extension_loaded('redis')) {
    		$this->info(trans('lang.extension_required_error', ['extension' => 'redis']));
    		return false;
    	}
        $queue = getActiveQueue();
        if ($queue != "redis") {
            $this->info("Metrics snapshot can not be stored when queue driver is $queue");
            return false;
        }
        parent::handle($lock, $metrics);
    }
}
