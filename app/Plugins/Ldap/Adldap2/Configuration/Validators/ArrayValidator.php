<?php

namespace App\Plugins\Ldap\Adldap2\Configuration\Validators;

use App\Plugins\Ldap\Adldap2\Configuration\ConfigurationException;

/**
 * Class ArrayValidator
 *
 * Validates that the configuration value is an array.
 *
 * @package App\Plugins\Ldap\Adldap2\Configuration\Validators
 */
class ArrayValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        if (!is_array($this->value)) {
            throw new ConfigurationException("Option {$this->key} must be an array.");
        }

        return true;
    }
}
