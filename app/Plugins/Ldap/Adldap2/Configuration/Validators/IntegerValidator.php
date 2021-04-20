<?php

namespace App\Plugins\Ldap\Adldap2\Configuration\Validators;

use App\Plugins\Ldap\Adldap2\Configuration\ConfigurationException;

/**
 * Class IntegerValidator
 *
 * Validates that the configuration value is an integer / number.
 *
 * @package App\Plugins\Ldap\Adldap2\Configuration\Validators
 */
class IntegerValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        if (!is_numeric($this->value)) {
            throw new ConfigurationException("Option {$this->key} must be an integer.");
        }

        return true;
    }
}
