<?php

namespace App\FileManager\Jobs;

use App\FileManager\Helpers\PasteHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PasteFilesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var PasteHelper
     */
    protected $helper;

    /**
     * Create a new job instance.
     *
     * @param PasteHelper $helper
     */
    public function __construct(PasteHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->helper->pasteFilesAndFolders();
    }
}
