<?php

namespace App\Plugins\Ldap\Adldap2\Query;

use App\Plugins\Ldap\Adldap2\Models\RootDse;
use App\Plugins\Ldap\Adldap2\Schemas\ActiveDirectory;
use App\Plugins\Ldap\Adldap2\Schemas\SchemaInterface;
use App\Plugins\Ldap\Adldap2\Connections\ConnectionInterface;

/**
 * Adldap2 Search Factory.
 *
 * Constructs new LDAP queries.
 *
 * @package App\Plugins\Ldap\Adldap2\Search
 *
 * @mixin Builder
 */
class Factory
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * Stores the current schema instance.
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * The base DN to use for the search.
     *
     * @var string|null
     */
    protected $base;

    /**
     * Constructor.
     *
     * @param ConnectionInterface  $connection The connection to use when constructing a new query.
     * @param SchemaInterface|null $schema The schema to use for the query and models located.
     * @param string               $baseDn The base DN to use for all searches.
     */
    public function __construct(ConnectionInterface $connection, SchemaInterface $schema = null, $baseDn = '')
    {
        $this->setConnection($connection)
            ->setSchema($schema)
            ->setBaseDn($baseDn);
    }

    /**
     * Sets the connection property.
     *
     * @param ConnectionInterface $connection
     *
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Sets the schema property.
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
     * Sets the base distinguished name to perform searches upon.
     *
     * @param string $base
     *
     * @return $this
     */
    public function setBaseDn($base = '')
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Returns a new query builder instance.
     *
     * @return Builder
     */
    public function newQuery()
    {
        return $this->newBuilder()->in($this->base);
    }

    /**
     * Performs a global 'all' search query on the current
     * connection by performing a search for all entries
     * that contain a common name attribute.
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function get()
    {
        return $this->newQuery()->whereHas($this->schema->commonName())->get();
    }

    /**
     * Returns a query builder scoped to users.
     *
     * @return Builder
     */
    public function users()
    {
        return $this->where([
            [$this->schema->objectClass(), Operator::$equals, $this->schema->objectClassUser()],
            [$this->schema->objectCategory(), Operator::$equals, $this->schema->objectCategoryPerson()],
            [$this->schema->objectClass(), Operator::$doesNotEqual, $this->schema->objectClassContact()],
        ]);
    }

    /**
     * Returns a query builder scoped to printers.
     *
     * @return Builder
     */
    public function printers()
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassPrinter(),
        ]);
    }

    /**
     * Returns a query builder scoped to organizational units.
     *
     * @return Builder
     */
    public function ous()
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassOu(),
        ]);
    }

    /**
     * Returns a query builder scoped to groups.
     *
     * @return Builder
     */
    public function groups()
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassGroup(),
        ]);
    }

    /**
     * Returns a query builder scoped to containers.
     *
     * @return Builder
     */
    public function containers()
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassContainer(),
        ]);
    }

    /**
     * Returns a query builder scoped to contacts.
     *
     * @return Builder
     */
    public function contacts()
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassContact(),
        ]);
    }

    /**
     * Returns a query builder scoped to computers.
     *
     * @return Builder
     */
    public function computers()
    {
        return $this->where([
            $this->schema->objectClass() => $this->schema->objectClassComputer(),
        ]);
    }

    /**
     * Returns the root DSE record.
     *
     * @return RootDse|null
     */
    public function getRootDse()
    {
        $query = $this->newQuery();

        $root = $query->in('')->read()->whereHas($this->schema->objectClass())->first();

        if ($root) {
            return (new RootDse([], $query))
                ->setRawAttributes($root->getAttributes());
        }
    }

    /**
     * Handle dynamic method calls on the query builder object.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->newQuery(), $method], $parameters);
    }

    /**
     * Returns a new query grammar instance.
     *
     * @return Grammar
     */
    protected function newGrammar()
    {
        return new Grammar();
    }

    /**
     * Returns a new query builder instance.
     *
     * @return Builder
     */
    protected function newBuilder()
    {
        return new Builder($this->connection, $this->newGrammar(), $this->schema);
    }
}
