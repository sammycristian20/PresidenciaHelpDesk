<?php

namespace App\Plugins\Ldap\Adldap\Connections;

use App\Plugins\Ldap\Adldap\Exceptions\AdldapException;
use App\Plugins\Ldap\Adldap\Contracts\Connections\ConnectionInterface;

class Ldap implements ConnectionInterface
{
    use LdapFunctionSupportTrait;

    /**
     * The active LDAP connection.
     *
     * @var resource
     */
    protected $connection;

    /**
     * Stores the bool whether or not
     * the current connection is bound.
     *
     * @var bool
     */
    protected $bound = false;

    /**
     * Stores the bool to tell the connection
     * whether or not to use SSL.
     *
     * To use SSL, your server must support LDAP over SSL.
     * http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl
     *
     * @var bool
     */
    protected $useSSL = false;

    /**
     * Stores the bool to tell the connection
     * whether or not to use TLS.
     *
     * If you wish to use TLS you should ensure that $useSSL is set to false and vice-versa
     *
     * @var bool
     */
    protected $useTLS = false;

    /**
     * {@inheritdoc}
     */
    public function isUsingSSL()
    {
        return $this->useSSL;
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingTLS()
    {
        return $this->useTLS;
    }

    /**
     * {@inheritdoc}
     */
    public function isBound()
    {
        return $this->bound;
    }

    /**
     * {@inheritdoc}
     */
    public function canChangePasswords()
    {
        return $this->isUsingSSL() || $this->isUsingTLS();
    }

    /**
     * {@inheritdoc}
     */
    public function useSSL()
    {
        $this->useSSL = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function useTLS()
    {
        $this->useTLS = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntries($searchResults)
    {
        return ldap_get_entries($this->getConnection(), $searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstEntry($searchResults)
    {
        return ldap_first_entry($this->getConnection(), $searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextEntry($entry)
    {
        return ldap_next_entry($this->getConnection(), $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($entry)
    {
        return ldap_get_attributes($this->getConnection(), $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function countEntries($searchResults)
    {
        return ldap_count_entries($this->getConnection(), $searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function compare($dn, $attribute, $value)
    {
        return ldap_compare($this->getConnection(), $dn, $attribute, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastError()
    {
        return ldap_error($this->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    public function getValuesLen($entry, $attribute)
    {
        return ldap_get_values_len($this->getConnection(), $entry, $attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($option, $value)
    {
        return ldap_set_option($this->getConnection(), $option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setRebindCallback(callable $callback)
    {
        return ldap_set_rebind_proc($this->getConnection(), $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function startTLS()
    {
        return ldap_start_tls($this->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    public function connect($hostname = [], $port = '389')
    {
        $host = $this->getHostname($hostname, $this->getProtocol());

        return $this->connection = ldap_connect("{$host}:{$port}");
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $connection = $this->getConnection();

        return is_resource($connection) ? ldap_close($connection) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function search($dn, $filter, array $fields)
    {
        return ldap_search($this->getConnection(), $dn, $filter, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function listing($dn, $filter, array $attributes)
    {
        return ldap_list($this->getConnection(), $dn, $filter, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function read($dn, $filter, array $fields)
    {
        return ldap_read($this->getConnection(), $dn, $filter, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($username, $password, $sasl = false)
    {
        if ($this->isUsingTLS()) {
            $this->startTLS();
        }

        if ($sasl) {
            return $this->bound = ldap_sasl_bind($this->getConnection(), null, null, 'GSSAPI');
        }

        return $this->bound = ldap_bind($this->getConnection(), $username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function add($dn, array $entry)
    {
        return ldap_add($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($dn)
    {
        return ldap_delete($this->getConnection(), $dn);
    }

    /**
     * {@inheritdoc}
     */
    public function rename($dn, $newRdn, $newParent, $deleteOldRdn = false)
    {
        return ldap_rename($this->getConnection(), $dn, $newRdn, $newParent, $deleteOldRdn);
    }

    /**
     * {@inheritdoc}
     */
    public function modify($dn, array $entry)
    {
        return ldap_modify($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyBatch($dn, array $values)
    {
        return ldap_modify_batch($this->getConnection(), $dn, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function modAdd($dn, array $entry)
    {
        return ldap_mod_add($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modReplace($dn, array $entry)
    {
        return ldap_mod_replace($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function modDelete($dn, array $entry)
    {
        return ldap_mod_del($this->getConnection(), $dn, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function controlPagedResult($pageSize = 1000, $isCritical = false, $cookie = '')
    {
        if ($this->isPagingSupported()) {
            return ldap_control_paged_result($this->getConnection(), $pageSize, $isCritical, $cookie);
        }

        $message = 'LDAP Pagination is not supported on your current PHP installation.';

        throw new AdldapException($message);
    }

    /**
     * {@inheritdoc}
     */
    public function controlPagedResultResponse($result, &$cookie)
    {
        if ($this->isPagingSupported()) {
            return ldap_control_paged_result_response($this->getConnection(), $result, $cookie);
        }

        $message = 'LDAP Pagination is not supported on your current PHP installation.';

        throw new AdldapException($message);
    }

    /**
     * {@inheritdoc}
     */
    public function errNo()
    {
        return ldap_errno($this->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedError()
    {
        return $this->getDiagnosticMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedErrorCode()
    {
        return $this->extractDiagnosticCode($this->getExtendedError());
    }

    /**
     * {@inheritdoc}
     */
    public function err2Str($number)
    {
        return ldap_err2str($number);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiagnosticMessage()
    {
        ldap_get_option($this->getConnection(), LDAP_OPT_ERROR_STRING, $diagnosticMessage);

        return $diagnosticMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function extractDiagnosticCode($message)
    {
        preg_match('/^([\da-fA-F]+):/', $message, $matches);

        return isset($matches[1]) ? $matches[1] : false;
    }

    /**
     * Returns the LDAP protocol to utilize for the current connection.
     *
     * @return string
     */
    protected function getProtocol()
    {
        return $this->isUsingSSL() ? $this::PROTOCOL_SSL : $this::PROTOCOL;
    }

    /**
     * Returns a compiled hostname compatible with ldap_connect().
     *
     * @param array  $hostname
     * @param string $protocol
     *
     * @return string
     */
    protected function getHostname($hostname = [], $protocol = '')
    {
        return is_array($hostname) ? $protocol.implode(' '.$protocol, $hostname) : $hostname;
    }
}
