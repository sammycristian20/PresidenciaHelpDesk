<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SystemBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $type;
    protected $version;


    public function __construct($id, $type, $version)
    {
        $this->id = $id;
        $this->type = $type;
        $this->version = $version;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        $type = $this->type;
        set_time_limit(0);
        if ($type == 'system_backup') {
            \Artisan::call('backup:run');
        } elseif ($type == 'db_backup') {
            \Artisan::call('backup:run --only-db');
        } else {
            \Artisan::call('backup:run --only-files');
        }
        $version = $this->version;
        $model = new Backup();
        $model->create(['backup_type'=>$type,'user_id'=>$id,'version'=>$version]);
         // return successResponse(\Lang::get('lang.backup_created_successfully'));
    }
}
