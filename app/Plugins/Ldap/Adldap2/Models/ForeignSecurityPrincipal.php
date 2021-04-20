<?php
namespace App\Plugins\Ldap\Adldap2\Models;

use App\Plugins\Ldap\Adldap2\Models\Concerns\HasMemberOf;

/**
 * Class ForeignSecurityPrincipal
 *
 * Represents an LDAP ForeignSecurityPrincipal.
 *
 * @package App\Plugins\Ldap\Adldap2\Models
 */
class ForeignSecurityPrincipal extends Entry
{
    use HasMemberOf;
}
