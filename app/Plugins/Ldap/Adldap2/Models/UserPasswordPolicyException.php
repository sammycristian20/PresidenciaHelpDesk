<?php

namespace App\Plugins\Ldap\Adldap2\Models;

use App\Plugins\Ldap\Adldap2\AdldapException;

/**
 * Class UserPasswordPolicyException
 *
 * Thrown when a users password is being changed but their new password
 * does not conform to the LDAP servers password policy.
 *
 * @package App\Plugins\Ldap\Adldap2\Models
 */
class UserPasswordPolicyException extends AdldapException
{
    //
}
