<?php

namespace App\Plugins\Ldap\Adldap2\Events\Auth;

use App\Plugins\Ldap\Adldap2\Connections\ConnectionInterface;

class Event
{
    /**
     * The connection that the username and password is being bound on.
     *
     * @var ConnectionInterface
     */
    public $connection;

    /**
     * The username that is being used for binding.
     *
     * @var string
     */
    public $username;

    /**
     * The password that is being used for binding.
     *
     * @var string
     */
    public $password;

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param string              $username
     * @param string              $password
     */
    public function __construct(ConnectionInterface $connection, $username, $password)
    {
        $this->connection = $connection;
        $this->username = $username;
        $this->password = $password;
    }
}
