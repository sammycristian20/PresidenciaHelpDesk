<?php

namespace App\Plugins\Ldap\Controllers;

use App\Plugins\Ldap\Adldap\Contracts\Schemas\SchemaInterface;
use App\Plugins\Ldap\Adldap\Models\User as LdapUser;
use App\Plugins\Ldap\Adldap2\Adldap;
use App\Plugins\Ldap\Adldap2\Schemas\ActiveDirectory;
use App\Plugins\Ldap\Adldap2\Schemas\FreeIPA;
use App\Plugins\Ldap\Adldap2\Schemas\OpenLDAP;
use Illuminate\Support\Collection;

/**
 * Contains all the basic ldap-specific functions for eg. connecting to ldap, getting users etc.
 * NOTE: if the package is updated, only this class has to be modified without changing any controller
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class LdapConnector
{
    /**
     * Attributes which can be used to log into
     * @var array
     */
    public static $LOGINABLE_AD_ATTRIBUTES = ['FAVEO DEFAULT', 'samaccountname', 'userprincipalname', 'distinguishedname'];
    
    /**
     * General attributes which are allowed in ldap
     * @var string[]
     */
    public static $AD_ATTRIBUTES = [ 'company', 'contact', 'c', 'department', 'displayname',
        'mail', 'mailnickname', 'employeeid', 'employeenumber', 'employeetype', 'givenname', 'grouptype', 'homepostaladdress','initials', 'location', 'streetaddress', 'telephonenumber', 'title', 'l'];
    
    /**
     * Domain of Ldap server
     * @var string
     */
    private $domain;
    /**
     * Username of Ldap server
     * @var string
     */
    private $username;
    /**
     * Password of Ldap server
     * @var string
     */
    private $password;
    /**
     * port via which connection has to be made
     * @var int
     */
    private $port;
    /**
     * encryption which will be used for the communication with the server
     * @var string
     */
    private $encryption;
    /**
     * Directory service schema. 'open_ldap','active_directory','free_ipa'
     * @var string
     */
    private $schema;

    /**
     * Sets Ldap configuration
     * @param string $domain Domain of Ldap server
     * @param string $username Username of Ldap server
     * @param string $password Password of Ldap server
     */
    public function setLdapConfig(string $domain = null, string $username = null, string $password = null,
                                  $port = null, string $encryption = null, $schema = 'active_directory')
    {
        $this->domain = $domain;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->encryption = $encryption;
        $this->schema = $this->getSchema($schema);
    }

    /**
     * gets schema instance base on schema string
     * @param string $schema
     * @return SchemaInterface
     */
    public function getSchema(string $schema): string
    {
        switch ($schema) {

            case 'open_ldap':
                return OpenLDAP::class;

            case 'free_ipa':
                return FreeIPA::class;

            default:
                return ActiveDirectory::class;
        }
    }

    /**
     * Checks if user is autheticated on Ldap server
     * @param string $username username at ldap server
     * @param string $password password at ldap server
     * @return boolean
     */
    public function isValidCredentials(string $username, string $password): bool
    {
        //getting the ldap class instance
        $ldapInstance = $this->getLdapConnection();

        //checking if these credentials are present on ldap server
        return $ldapInstance->auth()->attempt($username, $password, true);
    }

    /**
     * Connects to ldap server and returns an instance
     * @param string $searchBase
     * @return  Adldap
     */
    private function getLdapConnection(string $searchBase = ''): Adldap
    {
        $respond = false;
        $ad = new Adldap;
        $config = [
            'hosts' => [$this->domain],
            'base_dn' => $searchBase,
            'username' => $this->username,
            'password' => $this->password,
            'schema' => $this->schema,
        ];


        //if port is present
        if ($this->port) {
            $config['port'] = $this->port;
        }

        //if encryption is provided, it will make true only that encryption which is provided (ssl/tls)
        if ($this->encryption) {
            $key = $this->encryption == 'ssl' ? 'use_ssl' : 'use_tls';
            $config[$key] = true;
        }

        $provider = $ad->addProvider($config);

        $ad->connect();

        return $ad;
    }

    /**
     * If username contains any escape character, it will break that down into domain and username
     * and return username
     * NOTE: in can of distingushName and userPrincipalName breakdown is not required
     * @return string
     */
    public function getFormattedUsername($username)
    {
        if (strpos($username, '\\') !== false) {
            // if a username is 'domain\username', it will make it just username
            $username = explode('\\', $username)[1];
        }

        return $username;
    }

    /**
     * Gets Ldap user list based on passed search base
     * @param string $searchBase
     * @param string $filter ldap search filter
     * @return object  array of ldap users
     */
    public function getLdapUsers(string $searchBase, string $filter = null): Collection
    {
        $ldapInstance = $this->getLdapConnection($searchBase);

        $ldapQuery = $ldapInstance->search()->users();

        if ($filter) {
            $ldapQuery->rawFilter($filter);
        }

        return $ldapQuery->get();
    }

    /**
     * Checks if passed configuration is valid ldap credentials
     * @return boolean  true if valid else false
     */
    public function isValidLdapConfig(): bool
    {
        $ldapInstance = $this->getLdapConnection();

        if ($ldapInstance) {
            return true;
        }
        return false;
    }


//NOTE: Small helpers methods has been written for fetching LDAP attributes so that ADLdap package
// related methods can be limited to this class.(It will be easy to change the package by limiting the
// method usage of Adldap package to only this class)

    /**
     * gets the email of the ldap user
     * @param LdapUser $user Ldap user instance from the ADLdap package
     * @return string|null
     */
    public function getEmail($user)
    {
        return $user->getEmail();
    }

    /**
     * gets the email of the ldap user
     * @param LdapUser $user Ldap user instance from the ADLdap package
     * @return string|null
     */
    public function getUserName($user)
    {
        return $user->getUserPrincipalName();
    }

    /**
     * gets the email of the ldap user
     * @param LdapUser $user Ldap user instance from the ADLdap package
     * @return string|null
     */
    public function getFirstName($user)
    {
        return $user->getFirstName() ? $user->getFirstName() : "";
    }

    /**
     * gets the email of the ldap user
     * @param LdapUser $user Ldap user instance from the ADLdap package
     * @return string|null
     */
    public function getLastName($user)
    {
        return $user->getLastName() ? $user->getLastName() : "";
    }

    /**
     * gets the email of the ldap user
     * @param LdapUser $user Ldap user instance from the ADLdap package
     * @return string|null
     */
    public function getPhoneNumber($user)
    {
        return $user->getTelephoneNumber() ? $user->getTelephoneNumber() : "";
    }

    /**
     * gets the email of the ldap user
     * @param LdapUser $user Ldap user instance from the ADLdap package
     * @return string|null
     */
    public function getGuid($user)
    {
        return $user->getConvertedGuid();
    }

    /**
     * gets custom attribute from LDAP user object
     * @param string $attributeName
     * @param object $user
     * @return string|null
     */
    public function getAttribute($attributeName, $user)
    {
        // make all attributes in smalls before passing
        $attributeName = strtolower($attributeName);

        // for objectsid and objectguid it will be different as those are sent in binary,
        // for dn and distinguishedName, we need to call methods because they come as an object protected property, which cannot be acccessed directly
        // for others it will be normal
        switch ($attributeName) {
            case 'objectguid':
                return $user->getConvertedGuid();

            case 'objectsid':
                return $user->getConvertedSid();

            case 'dn':
                return $user->getDn();

            case 'distinguishedname':
                return $user->getDistinguishedName();

            default:
                return $user->getFirstAttribute($attributeName);
        }

    }

    /**
     * If ldap extension is enabled in configuration file
     * @return boolean
     */
    public function isLdapExtensionEnabled()
    {
        return extension_loaded('ldap');
    }

    /**
     * Returns the user location
     * @param $user
     * @return mixed|string
     */
    public function getUserLocation($user)
    {
        return $user->getLocale() ?: '';
    }
}
