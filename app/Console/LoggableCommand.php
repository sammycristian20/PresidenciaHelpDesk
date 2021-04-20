<?php


namespace App\Console;

use Illuminate\Console\Command;
use Logger;
use Exception;

/**
 * This is a class extending which, will cause commands to log in cron logs.
 * INSTRUCTIONS: create a `handleAndLog` method in your Command class
 */
abstract class LoggableCommand extends Command
{

    /**
     * Instance of Log
     * @var Logger
     */
    protected $log;

    final public function handle()
    {
        // declaring it final so that it cannot be overridden if extended
        // check if handleAndLog exists or not
        // if not display error that that method is required
        if(!method_exists($this, "handleAndLog")){
            throw new Exception("handleAndLog method not found in the class ".get_called_class().". Please read the manual of doc-block App\Console\LoggableCommand");
        }

        try {
            $this->info("$this->signature command started");

            $this->log = Logger::cron($this->signature, $this->description);

            $this->laravel->call([$this, 'handleAndLog']);

            // Adding if so that case where we are migrating from old version to this version(v3.2.0), database structure of
            //  cron-logs will be different than now. In that case Logger::cron() will return null. In that case Logger::cronCompleted($this->log->id), will throw
            //  an exception, which again will be catch in cath part and again it will fail because $log is null (that's why another if in catch)
            //  NOTE: these if's are not required v3.2.0 onwards
            if($this->log){
                Logger::cronCompleted($this->log->id);
            }

            $this->info("$this->signature finished without any errors");

        } catch (Exception $e){
            if($this->log){
                Logger::cronFailed($this->log->id, $e);
            }

            $this->error("\n$this->signature finished with error :". $e->getMessage().
                "\n\nfile:".$e->getFile()."(".$e->getLine().")". "\n\ntrace: ".$e->getTraceAsString());
        }
    }
}