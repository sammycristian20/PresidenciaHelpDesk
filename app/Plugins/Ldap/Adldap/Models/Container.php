<?php

namespace App\Plugins\Ldap\Adldap\Models;

use App\Plugins\Ldap\Adldap\Models\Traits\HasDescriptionTrait;
use App\Plugins\Ldap\Adldap\Models\Traits\HasCriticalSystemObjectTrait;

class Container extends Entry
{
    use HasDescriptionTrait, HasCriticalSystemObjectTrait;

    /**
     * Returns the containers system flags integer.
     *
     * https://msdn.microsoft.com/en-us/library/ms680022(v=vs.85).aspx
     *
     * @return string
     */
    public function getSystemFlags()
    {
        return $this->getAttribute($this->schema->systemFlags(), 0);
    }
}
