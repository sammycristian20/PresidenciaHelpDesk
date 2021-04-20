<?php

namespace App\Plugins\Ldap\Adldap2\Models;

/**
 * Class Contact
 *
 * Represents an LDAP contact.
 *
 * @package App\Plugins\Ldap\Adldap2\Models
 */
class Contact extends Entry
{
    use Concerns\HasMemberOf,
        Concerns\HasUserProperties;
}
