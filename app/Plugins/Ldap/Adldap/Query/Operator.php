<?php

namespace App\Plugins\Ldap\Adldap\Query;

use ReflectionClass;

class Operator
{
    /**
     * The 'has' wildcard operator.
     *
     * @var string
     */
    public static $has = '*';

    /**
     * The custom `notHas` operator.
     *
     * @var string
     */
    public static $notHas = '!*';

    /**
     * The equals operator.
     *
     * @var string
     */
    public static $equals = '=';

    /**
     * The does not equal operator.
     *
     * @var string
     */
    public static $doesNotEqual = '!';

    /**
     * The greater than or equal to operator.
     *
     * @var string
     */
    public static $greaterThanOrEquals = '>=';

    /**
     * The less than or equal to operator.
     *
     * @var string
     */
    public static $lessThanOrEquals = '<=';

    /**
     * The approximately equal to operator.
     *
     * @var string
     */
    public static $approximatelyEquals = '~=';

    /**
     * The custom starts with operator.
     *
     * @var string
     */
    public static $startsWith = 'starts_with';

    /**
     * The custom not starts with operator.
     *
     * @var string
     */
    public static $notStartsWith = 'not_starts_with';

    /**
     * The custom ends with operator.
     *
     * @var string
     */
    public static $endsWith = 'ends_with';

    /**
     * The custom not ends with operator.
     *
     * @var string
     */
    public static $notEndsWith = 'not_ends_with';

    /**
     * The custom contains operator.
     *
     * @var string
     */
    public static $contains = 'contains';

    /**
     * The custom not contains operator.
     *
     * @var string
     */
    public static $notContains = 'not_contains';

    /**
     * Returns all available operators.
     *
     * @return array
     */
    public static function all()
    {
        return (new ReflectionClass(new static()))
            ->getStaticProperties();
    }
}
