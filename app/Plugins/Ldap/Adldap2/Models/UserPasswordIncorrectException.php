<?php

namespace App\Plugins\Ldap\Adldap2\Models;

use App\Plugins\Ldap\Adldap2\AdldapException;

/**
 * Class UserPasswordIncorrectException
 *
 * Thrown when a users password is being changed
 * and their current password given is incorrect.
 *
 * @package App\Plugins\Ldap\Adldap2\Models
 */
class UserPasswordIncorrectException extends AdldapException
{
    //
}
