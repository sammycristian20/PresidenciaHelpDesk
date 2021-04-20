<?php

namespace App\Plugins\Ldap\Adldap2\Models\Concerns;

trait HasCriticalSystemObject
{
    /**
     * Returns true / false if the entry is a critical system object.
     *
     * @return null|bool
     */
    public function isCriticalSystemObject()
    {
        $attribute = $this->getFirstAttribute($this->schema->isCriticalSystemObject());

        return $this->convertStringToBool($attribute);
    }
}
