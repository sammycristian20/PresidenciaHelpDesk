<?php

namespace App\Plugins\Ldap\Adldap2\Configuration\Validators;

use App\Plugins\Ldap\Adldap2\Configuration\ConfigurationException;

/**
 * Class BooleanValidator
 *
 * Validates that the configuration value is a boolean.
 *
 * @package App\Plugins\Ldap\Adldap2\Configuration\Validators
 */
class BooleanValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        if (!is_bool($this->value)) {
            throw new ConfigurationException("Option {$this->key} must be a boolean.");
        }

        return true;
    }
}
