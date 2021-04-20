<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use App\Http\Controllers\Common\Mail\FetchMailController;
use Carbon\Carbon;
use Event;
use Illuminate\Console\Command;
use Lang;
use Logger;

class TicketFetch extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches emails from configured mails and creates/updates related tickets';

    public function handleAndLog()
    {
        (new FetchMailController)->fetchMail($this->log);

        Event::dispatch('ticket.fetch', ['event' => '']);
    }
}
