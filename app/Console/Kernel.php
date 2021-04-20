<?php

namespace App\Console;


use App\Model\MailJob\Condition;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Inspire',
        'App\Console\Commands\SendReport',
        'App\Console\Commands\CloseWork',
        'App\Console\Commands\TicketFetch',
        'App\Console\Commands\SendEscalate',
        'App\Console\Commands\UpdateEncryption',
        \App\Console\Commands\DropTables::class,
        \App\Console\Commands\Install::class,
        \App\Console\Commands\InstallDB::class,
        \App\Console\Commands\TaskNotification::class,
        \App\Console\Commands\MaintenanceModeOn::class,
        \App\Console\Commands\RecurCommand::class,
        \App\Console\Commands\RemoveFormField::class,
        \App\Console\Commands\TaskAlertRepeat::class,
        \App\Console\Commands\SetupTestEnv::class,
        \App\Console\Commands\CheckUpdate::class,
        \App\Console\Commands\SyncDatabaseToLatestVersion::class,
        \App\Console\Commands\SnapshotCommand::class,
        \App\Console\Commands\Cdn::class,
        \App\Console\Commands\BackupEmail::class,
        \App\Console\Commands\ScheduleList::class,
    ];

    protected $condition;

    protected function initiateCondition()
    {
        $this->condition = new Condition();
    }

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (isInstall() && \App::runningInConsole()) {
            $this->initiateCondition();
            try {
                $jobs = $this->condition->checkALLActiveJob();
                if(!empty($jobs)) {
                    foreach ($jobs as $job => $command) {
                        $executionTime = $this->getTaskCommand($job);
                        $this->getCondition($schedule->command($command), $executionTime);
                    }
                }
            } catch (\Exception $ex) {
                loging('cron', $ex->getMessage());
            }
        }
    }

    public function getCurrentQueue() {
        $queue = 'database';
        $services = new \App\Model\MailJob\QueueService();
        $current = $services->where('status', 1)->first();
        if ($current) {
            $queue = $current->short_name;
        }
        return $queue;
    }

    public function getCondition($schedule, $command) {
        $condition = $command['condition'];
        $at = $command['at'];
        switch ($condition) {
            case "everyMinute":
                return $schedule->everyMinute();
            case "everyFiveMinutes":
                return $schedule->everyFiveMinutes();
            case "everyTenMinutes":
                return $schedule->everyTenMinutes();
            case "everyThirtyMinutes":
                return $schedule->everyThirtyMinutes();
            case "hourly":
                return $schedule->hourly();
            case "daily":
                return $schedule->daily();
            case "dailyAt":
                return $this->getConditionWithOption($schedule, $condition, $at);
            case "weekly":
                return $schedule->weekly();
            case "monthly":
                return $schedule->monthly();
            case "yearly":
                return $schedule->yearly();
            default :
                return $schedule->everyMinute();
        }
    }

    public function getConditionWithOption($schedule, $command, $at) {
        switch ($command) {
            case "dailyAt":
                return $schedule->timezone(timezone())->dailyAt($at);
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands() {
        require base_path('routes/console.php');
    }

    /**
     * Get job excution schedule from conditions table
     *
     * @var  Condition  $condition
     *
     * @return Array                 Array containing command execution schedule
     */
    protected function getTaskCommand($task)
    {
        return $this->condition->getConditionValue($task);
    }
}
