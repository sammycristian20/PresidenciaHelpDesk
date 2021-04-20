<?php

namespace App\Plugins\Whatsapp\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\Whatsapp\Controllers\WhatsappController;
use Logger;
use Carbon\Carbon;
use Lang;

class WhatsappProcess extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fetches tickets from whatsapp.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
        $whatsapp = new WhatsappController();

        $whatsapp->webhookProcess();
    }
}
