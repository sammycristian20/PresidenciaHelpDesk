<?php

namespace App\Console\Commands;


use App\Console\LoggableCommand;
use Updater;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Admin\Helpdesk\BackupController;
use App\Http\Controllers\Update\AutoUpdateController;


class BackupEmail extends LoggableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:email {--email=} {--ver=} {--autoUpdate=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send backup completed mail after System backup is finished';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PhpMailController $PhpMailController)
    {
        parent::__construct();

        $this->PhpMailController = $PhpMailController;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
        $email = $this->option('email');
        $version = $this->option('ver');
        if($this->option('autoUpdate') == true) {
            $cont = new AutoUpdateController(new Updater(),new Client());
            $cont->update(new Request());
            $this->PhpMailController->sendmail($this->PhpMailController
                    ->mailfrom('1', '0'), ['name' => '', 'email' => $email],['subject' => null, 'scenario' => 'backup-completed'],['version' => $version]);
        } else {
             $this->PhpMailController->sendmail($this->PhpMailController
                    ->mailfrom('1', '0'), ['name' => '', 'email' => $email],['subject' => null, 'scenario' => 'backup-completed'],['version' => $version]);
        }
       
    }
}
