<?php

namespace App\Plugins\Ldap\Adldap2\Auth;

use Throwable;
use Exception;
use App\Plugins\Ldap\Adldap2\Events\Auth\Bound;
use App\Plugins\Ldap\Adldap2\Events\Auth\Failed;
use App\Plugins\Ldap\Adldap2\Events\Auth\Passed;
use App\Plugins\Ldap\Adldap2\Events\Auth\Binding;
use App\Plugins\Ldap\Adldap2\Events\Auth\Attempting;
use App\Plugins\Ldap\Adldap2\Events\DispatcherInterface;
use App\Plugins\Ldap\Adldap2\Connections\ConnectionInterface;
use App\Plugins\Ldap\Adldap2\Configuration\DomainConfiguration;

/**
 * Class Guard
 *
 * Binds users to the current connection.
 *
 * @package App\Plugins\Ldap\Adldap2\Auth
 */
class Guard implements GuardInterface
{
    /**
     * The connection to bind to.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The domain configuration to utilize.
     *
     * @var DomainConfiguration
     */
    protected $configuration;

    /**
     * The event dispatcher.
     *
     * @var DispatcherInterface
     */
    protected $events;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConnectionInterface $connection, DomainConfiguration $configuration)
    {
        $this->connection = $connection;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function attempt($username, $password, $bindAsUser = false)
    {
        $this->validateCredentials($username, $password);

        $this->fireAttemptingEvent($username, $password);

        try {
            $this->bind(
                $this->applyPrefixAndSuffix($username),
                $password
            );

            $result = true;

            $this->firePassedEvent($username, $password);
        } catch (BindException $e) {
            // We'll catch the BindException here to allow
            // developers to use a simple if / else
            // using the attempt method.
            $result = false;
        }

        // If we're not allowed to bind as the user,
        // we'll rebind as administrator.
        if ($bindAsUser === false) {
            // We won't catch any BindException here so we can
            // catch rebind failures. However this shouldn't
            // occur if our credentials are correct
            // in the first place.
            $this->bindAsAdministrator();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function bind($username = null, $password = null)
    {
        $this->fireBindingEvent($username, $password);

        try {
            if ($this->connection->bind($username, $password)) {
                $this->fireBoundEvent($username, $password);
            } else {
                throw new Exception();
            }
        } catch (Throwable $e) {
            $this->fireFailedEvent($username, $password);

            throw new BindException($this->connection->getLastError(), $this->connection->errNo());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bindAsAdministrator()
    {
        $this->bind(
            $this->configuration->get('username'),
            $this->configuration->get('password')
        );
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return DispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->events;
    }

    /**
     * Sets the event dispatcher instance.
     *
     * @param DispatcherInterface $dispatcher
     *
     * @return void
     */
    public function setDispatcher(DispatcherInterface $dispatcher)
    {
        $this->events = $dispatcher;
    }

    /**
     * Applies the prefix and suffix to the given username.
     *
     * @param string $username
     *
     * @return string
     *
     * @throws \Adldap\Configuration\ConfigurationException If account_suffix or account_prefix do not
     *                                                      exist in the providers domain configuration
     */
    protected function applyPrefixAndSuffix($username)
    {
        $prefix = $this->configuration->get('account_prefix');
        $suffix = $this->configuration->get('account_suffix');

        return $prefix.$username.$suffix;
    }

    /**
     * Validates the specified username and password from being empty.
     *
     * @param string $username
     * @param string $password
     *
     * @throws PasswordRequiredException When the given password is empty.
     * @throws UsernameRequiredException When the given username is empty.
     */
    protected function validateCredentials($username, $password)
    {
        if (empty($username)) {
            // Check for an empty username.
            throw new UsernameRequiredException('A username must be specified.');
        }

        if (empty($password)) {
            // Check for an empty password.
            throw new PasswordRequiredException('A password must be specified.');
        }
    }

    /**
     * Fire the attempting event.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    protected function fireAttemptingEvent($username, $password)
    {
        if (isset($this->events)) {
            $this->events->fire(new Attempting($this->connection, $username, $password));
        }
    }

    /**
     * Fire the passed event.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    protected function firePassedEvent($username, $password)
    {
        if (isset($this->events)) {
            $this->events->fire(new Passed($this->connection, $username, $password));
        }
    }

    /**
     * Fire the failed event.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    protected function fireFailedEvent($username, $password)
    {
        if (isset($this->events)) {
            $this->events->fire(new Failed($this->connection, $username, $password));
        }
    }

    /**
     * Fire the binding event.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    protected function fireBindingEvent($username, $password)
    {
        if (isset($this->events)) {
            $this->events->fire(new Binding($this->connection, $username, $password));
        }
    }

    /**
     * Fire the bound event.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    protected function fireBoundEvent($username, $password)
    {
        if (isset($this->events)) {
            $this->events->fire(new Bound($this->connection, $username, $password));
        }
    }
}
