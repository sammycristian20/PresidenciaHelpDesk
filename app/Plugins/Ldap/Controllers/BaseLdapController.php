<?php

namespace App\Plugins\Ldap\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Ldap\Model\Ldap;
use App\Traits\UserImport;
use App\Plugins\Ldap\Model\LdapSearchBase;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;

/**
 * Contains base methods for LdapContoller to work
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class BaseLdapController extends Controller
{
    use UserImport;

    /**
     * Ldap connector object
     * @var \App\Plugins\Ldap\Controllers\LdapConnector
     */
    protected $ldapConnector;

    /**
     * current search base
     * @var LdapSearchBase
     */
    protected $searchBase;

    /**
     * Ldap config
     * @var Ldap
     */
    protected $ldapConfig;

    /**
     * Checks if a user already exists. If no, then creates a new user
     * @param LdapSearchBase $searchBase
     * @param object $users array of ldap users
     * @return null
     */
    protected function createValidUsers(LdapSearchBase $searchBase, $users)
    {
        $this->searchBase = $searchBase;

        $this->handleBulk($users);
    }

    /**
     * Imports user based on search base department
     * @param LdapSearchBase $searchBase
     * @param int $importedUsersCount
     * @return LdapSearchBase $searchBase
     */
    protected function importBySearchBasis(LdapSearchBase $searchBase, int &$importedUsersCount)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);

        //gets ldap connection based on the passed query
        $users = $this->ldapConnector->getLdapUsers($searchBase->search_base, $searchBase->filter);
        $importedUsersCount = $importedUsersCount + count($users);
        return $users;
    }

    /**
     * Takes faveo attribute and returns back AD attribute
     * @param $faveoAttribute
     * @return string
     */
    protected function getThirdPartyAttributeByFaveoAttribute(string $faveoAttribute) : string
    {
        $ldapAttributes = LdapFaveoAttribute::where('name', $faveoAttribute)
            ->where('ldap_id', $this->ldapConfig->id)->first();

        if ($ldapAttributes && $ldapAttributes->adAttribute) {
            return $ldapAttributes->adAttribute->name;
        }

        return '';
    }

    /**
     * gets default attributes values
     * @param  string $faveoAttribute
     * @param  object $user  Ldap User object(not faveo user)
     * @return string|array
     */
    private function getDefaultAttributeValue($faveoAttribute, $user)
    {
        switch ($faveoAttribute) {
            case 'user_name':
                return $this->ldapConnector->getUserName($user);

            case 'email':
                return $this->ldapConnector->getEmail($user);

            case 'first_name':
                return $this->ldapConnector->getFirstName($user);

            case 'last_name':
                return $this->ldapConnector->getLastName($user);

            case 'phone_number':
                return $this->ldapConnector->getPhoneNumber($user);

            case 'import_identifier':
                return $this->ldapConnector->getGuid($user);

            case 'role':
                return $this->searchBase->user_type;

            case 'department':
                return $this->searchBase->department_ids;

            case 'organization':
                return $this->searchBase->organization_ids;

            case 'org_dept':
                return [];

            case 'location':
                return $this->ldapConnector->getUserLocation($user);

            default:
                return '';
        }
    }

    /**
     * checks if overwrite is allowed for a given faveo attrbute
     * @param  string $faveoAttribute  faveo attribute string
     * @return bool
     */
    protected function isOverwriteAllowed(string $faveoAttribute) : bool
    {
        return (bool) LdapFaveoAttribute::where('name', $faveoAttribute)
            ->where('ldap_id', $this->ldapConfig->id)
            ->value('overwrite');
    }

    /**
     * Gets value of passed attribute value
     * @param  string $faveoAttribute
     * @param  object $user           * LDAP USER INSTANCE (Not faveo) *
     * @return string|array
     */
    protected function getAttributeValue(string $faveoAttribute, $user)
    {
        $adAttribute = $this->getThirdPartyAttributeByFaveoAttribute($faveoAttribute);

        if ($adAttribute == 'FAVEO DEFAULT') {
            return $this->getDefaultAttributeValue($faveoAttribute, $user);
        }

        $attributeValue = $this->ldapConnector->getAttribute($adAttribute, $user);

        //handle organization, department and org_dept seperately
        if ($faveoAttribute == 'department') {
            return (array)$this->createOrUpdateDepartment($attributeValue);
        }

        if ($faveoAttribute == 'organization') {
            return (array)$this->createOrUpdateOrganization($attributeValue);
        }

        // if attribute name is role but $attribute value is something other than `user`, `admin` or `agent`
        if ($faveoAttribute == 'role' && !in_array(strtolower((string)$attributeValue), ['user','admin','agent'])) {
            $attributeValue = 'user';
        }

        return $attributeValue;
    }
}
