<?php

namespace App\Plugins\Ldap\Adldap2\Models;

use App\Plugins\Ldap\Adldap2\Query\Builder;
use App\Plugins\Ldap\Adldap2\Schemas\ActiveDirectory;
use App\Plugins\Ldap\Adldap2\Schemas\SchemaInterface;

/**
 * Class Factory
 *
 * Constructs and scopes LDAP queries.
 *
 * @package App\Plugins\Ldap\Adldap2\Models
 */
class Factory
{
    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * Constructor.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->setQuery($builder)
            ->setSchema($builder->getSchema());
    }

    /**
     * Sets the current query builder.
     *
     * @param Builder $builder
     *
     * @return $this
     */
    public function setQuery(Builder $builder)
    {
        $this->query = $builder;

        return $this;
    }

    /**
     * Sets the current schema.
     *
     * @param SchemaInterface|null $schema
     *
     * @return $this
     */
    public function setSchema(SchemaInterface $schema = null)
    {
        $this->schema = $schema ?: new ActiveDirectory();

        return $this;
    }

    /**
     * Creates a new generic LDAP entry instance.
     *
     * @param array $attributes
     *
     * @return Entry
     */
    public function entry(array $attributes = [])
    {
        $model = $this->schema->entryModel();

        return (new $model($attributes, $this->query));
    }

    /**
     * Creates a new user instance.
     *
     * @param array $attributes
     *
     * @return User
     */
    public function user(array $attributes = [])
    {
        $model = $this->schema->userModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->person(),
                $this->schema->organizationalPerson(),
                $this->schema->objectClassUser(),
            ]);
    }

    /**
     * Creates a new organizational unit instance.
     *
     * @param array $attributes
     *
     * @return OrganizationalUnit
     */
    public function ou(array $attributes = [])
    {
        $model = $this->schema->organizationalUnitModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->organizationalUnit(),
            ]);
    }

    /**
     * Creates a new group instance.
     *
     * @param array $attributes
     *
     * @return Group
     */
    public function group(array $attributes = [])
    {
        $model = $this->schema->groupModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->objectCategoryGroup(),
            ]);
    }

    /**
     * Creates a new organizational unit instance.
     *
     * @param array $attributes
     *
     * @return Container
     */
    public function container(array $attributes = [])
    {
        $model = $this->schema->containerModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), $this->schema->organizationalUnit());
    }

    /**
     * Creates a new user instance as a contact.
     *
     * @param array $attributes
     *
     * @return User
     */
    public function contact(array $attributes = [])
    {
        $model = $this->schema->contactModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->person(),
                $this->schema->organizationalPerson(),
                $this->schema->contact(),
            ]);
    }

    /**
     * Creates a new computer instance.
     *
     * @param array $attributes
     *
     * @return Computer
     */
    public function computer(array $attributes = [])
    {
        $model = $this->schema->computerModel();

        return (new $model($attributes, $this->query))
            ->setAttribute($this->schema->objectClass(), [
                $this->schema->top(),
                $this->schema->person(),
                $this->schema->organizationalPerson(),
                $this->schema->user(),
                $this->schema->computer(),
            ]);
    }
}
