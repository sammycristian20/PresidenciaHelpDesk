<?php

namespace App\Plugins\AzureActiveDirectory\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\Ldap\Controllers\ApiLdapController;
use App\Plugins\Ldap\Controllers\LdapConnector;

class SyncActiveDirectory extends LoggableCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ldap:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports users from LDAP server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {

    }
}
