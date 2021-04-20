<?php

namespace App\Jobs;

use App\ImportProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $processor;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ImportProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->processor->importHandler();
    }
}
