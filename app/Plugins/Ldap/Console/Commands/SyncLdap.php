<?php

namespace App\Plugins\Ldap\Console\Commands;

use App\Console\LoggableCommand;
use App\Plugins\Ldap\Controllers\ApiLdapController;
use App\Plugins\Ldap\Controllers\LdapConnector;
use App\Plugins\Ldap\Model\Ldap;

class SyncLdap extends LoggableCommand
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
        foreach (Ldap::get() as $ldapSetting) {
            (new ApiLdapController(new LdapConnector))->importByCurrentConfiguration($ldapSetting);
        }
    }
}
