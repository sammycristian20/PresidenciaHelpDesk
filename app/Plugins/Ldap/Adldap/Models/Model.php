<?php

namespace App\Plugins\Ldap\Adldap\Models;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Plugins\Ldap\Adldap\Query\Builder;
use App\Plugins\Ldap\Adldap\Objects\BatchModification;
use App\Plugins\Ldap\Adldap\Objects\DistinguishedName;
use App\Plugins\Ldap\Adldap\Exceptions\AdldapException;
use App\Plugins\Ldap\Adldap\Exceptions\ModelNotFoundException;
use App\Plugins\Ldap\Adldap\Exceptions\ModelDoesNotExistException;
use App\Plugins\Ldap\Adldap\Contracts\Schemas\SchemaInterface;

abstract class Model implements ArrayAccess, JsonSerializable
{
    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * The default output date format for all time related methods.
     *
     * Default format is suited for MySQL timestamps.
     *
     * @var string
     */
    public $dateFormat = 'Y-m-d H:i:s';

    /**
     * The models distinguished name.
     *
     * @var string
     */
    protected $dn;

    /**
     * The current query builder instance.
     *
     * @var Builder
     */
    protected $query;

    /**
     * The current LDAP attribute schema.
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * Contains the models attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Contains the models original attributes.
     *
     * @var array
     */
    protected $original = [];

    /**
     * Contains the models modifications.
     *
     * @var array
     */
    protected $modifications = [];

    /**
     * The format that is used to convert AD timestamps to unix timestamps.
     *
     * @var string
     */
    protected $timestampFormat = 'YmdHis.0Z';

    /**
     * Constructor.
     *
     * @param array   $attributes
     * @param Builder $builder
     */
    public function __construct(array $attributes, Builder $builder)
    {
        $this->setQuery($builder)
            ->setSchema($builder->getSchema())
            ->fill($attributes);
    }

    /**
     * Dynamically retrieve attributes on the object.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the object.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this
     */
    public function __set($key, $value)
    {
        return $this->setAttribute($key, $value);
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
     * Returns the current query builder.
     *
     * @return Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns a new query builder instance.
     *
     * @return Builder
     */
    public function newQuery()
    {
        return $this->query->newInstance();
    }

    /**
     * Returns a new collection with the specified items.
     *
     * @param mixed $items
     *
     * @return \Illuminate\Support\Collection
     */
    public function newCollection($items)
    {
        return new Collection($items);
    }

    /**
     * Sets the current model schema.
     *
     * @param SchemaInterface $schema
     *
     * @return $this
     */
    public function setSchema(SchemaInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Returns the current model schema.
     *
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Determine if the given offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    /**
     * Get the value for a given offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * Set the value at the given offset.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * Unset the value at the given offset.
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        // We need to remove the object SID and GUID from
        // being serialized as these attributes contain
        // characters that cannot be serialized.
        return Arr::except($this->getAttributes(), [
            $this->schema->objectSid(),
            $this->schema->objectGuid(),
        ]);
    }

    /**
     * Synchronizes the models original attributes
     * with the model's current attributes.
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    /**
     * Synchronizes the models attributes with the server values.
     *
     * @return bool
     */
    public function syncRaw()
    {
        $model = $this->query->newInstance()->findByDn($this->getDn());

        if ($model instanceof self) {
            $this->setRawAttributes($model->getAttributes());

            return true;
        }

        return false;
    }

    /**
     * Returns the models attribute with the specified key.
     *
     * If a sub-key is specified, it will try and
     * retrieve it from the parent keys array.
     *
     * @param int|string $key
     * @param int|string $subKey
     *
     * @return mixed
     */
    public function getAttribute($key, $subKey = null)
    {
        if (is_null($subKey) && $this->hasAttribute($key)) {
            return $this->attributes[$key];
        } elseif ($this->hasAttribute($key, $subKey)) {
            return $this->attributes[$key][$subKey];
        }
    }

    /**
     * Returns all of the models attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Fills the entry with the supplied attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Sets attributes on the current entry.
     *
     * @param int|string $key
     * @param mixed      $value
     * @param int|string $subKey
     *
     * @return $this
     */
    public function setAttribute($key, $value, $subKey = null)
    {
        // Normalize key.
        $key = $this->normalizeAttributeKey($key);

        // If the key is equal to 'dn', we'll automatically
        // change it to the full attribute name.
        $key = ($key == 'dn' ? $this->schema->distinguishedName() : $key);

        if (is_null($subKey)) {
            $this->attributes[$key] = (is_array($value) ? $value : [$value]);
        } else {
            $this->attributes[$key][$subKey] = $value;
        }

        return $this;
    }

    /**
     * Sets the attributes property.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setRawAttributes(array $attributes = [])
    {
        $this->attributes = $this->filterRawAttributes($attributes);

        if (Arr::has($attributes, 'dn')) {
            $this->setDn($attributes['dn']);
        }

        $this->syncOriginal();

        // Set exists to true since raw attributes are only
        // set in the case of attributes being loaded by
        // query results.
        $this->exists = true;

        return $this;
    }

    /**
     * Filters the count key recursively from raw LDAP attributes.
     *
     * @param array        $attributes
     * @param array|string $keys
     *
     * @return array
     */
    public function filterRawAttributes(array $attributes = [], $keys = ['count', 'dn'])
    {
        $attributes = Arr::except($attributes, $keys);

        array_walk($attributes, function (&$value) use ($keys) {
            $value = is_array($value) ?
                $this->filterRawAttributes($value, $keys) :
                $value;
        });

        return $attributes;
    }

    /**
     * Returns true / false if the specified attribute
     * exists in the attributes array.
     *
     * @param int|string $key
     * @param int|string $subKey
     *
     * @return bool
     */
    public function hasAttribute($key, $subKey = null)
    {
        if (is_null($subKey)) {
            return Arr::has($this->attributes, $key);
        }

        return Arr::has($this->attributes, "$key.$subKey");
    }

    /**
     * Returns the number of attributes inside
     * the attributes property.
     *
     * @return int
     */
    public function countAttributes()
    {
        return count($this->getAttributes());
    }

    /**
     * Returns the models original attributes.
     *
     * @return array
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } elseif ($value !== $this->original[$key] &&
                !$this->originalIsNumericallyEquivalent($key)) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Sets and returns the models modifications.
     *
     * @return array
     */
    public function getModifications()
    {
        foreach ($this->getDirty() as $attribute => $values) {
            // Make sure values is always an array.
            $values = (is_array($values) ? $values : [$values]);

            // Create a new modification.
            $modification = new BatchModification($attribute, null, $values);

            if (array_key_exists($attribute, $this->original)) {
                // If the attribute we're modifying has an original value, we'll give the
                // BatchModification object its values to automatically determine
                // which type of LDAP operation we need to perform.
                $modification->setOriginal($this->original[$attribute]);
            }

            // Finally, we'll add the modification to the model.
            $this->addModification($modification->build());
        }

        return $this->modifications;
    }

    /**
     * Sets the models modifications array.
     *
     * @param array $modifications
     *
     * @return $this
     */
    public function setModifications(array $modifications = [])
    {
        $this->modifications = $modifications;

        return $this;
    }

    /**
     * Adds a modification to the models modifications array.
     *
     * @param BatchModification $modification
     *
     * @return $this
     */
    public function addModification(BatchModification $modification)
    {
        $batch = $modification->get();

        if (is_array($batch)) {
            $this->modifications[] = $batch;
        }

        return $this;
    }

    /**
     * Returns the model's distinguished name string.
     *
     * https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getDistinguishedName()
    {
        return $this->dn;
    }

    /**
     * Sets the model's distinguished name attribute.
     *
     * @param string|DistinguishedName $dn
     *
     * @return $this
     */
    public function setDistinguishedName($dn)
    {
        $this->dn = (string) $dn;

        return $this;
    }

    /**
     * Returns the model's distinguished name string.
     *
     * (Alias for getDistinguishedName())
     *
     * https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string|null
     */
    public function getDn()
    {
        return $this->getDistinguishedName();
    }

    /**
     * Returns a DistinguishedName object for modifying the current models DN.
     *
     * @return DistinguishedName
     */
    public function getDnBuilder()
    {
        return $this->getNewDnBuilder($this->getDistinguishedName());
    }

    /**
     * Returns a new DistinguishedName object for building onto.
     *
     * @param string $baseDn
     *
     * @return DistinguishedName
     */
    public function getNewDnBuilder($baseDn = '')
    {
        return new DistinguishedName($baseDn, $this->schema);
    }

    /**
     *  Sets the model's distinguished name attribute.
     *
     * (Alias for setDistinguishedName())
     *
     * @param string $dn
     *
     * @return $this
     */
    public function setDn($dn)
    {
        return $this->setDistinguishedName($dn);
    }

    /**
     * Returns the model's common name.
     *
     * https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function getCommonName()
    {
        return $this->getAttribute($this->schema->commonName(), 0);
    }

    /**
     * Sets the model's common name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setCommonName($name)
    {
        return $this->setAttribute($this->schema->commonName(), $name, 0);
    }

    /**
     * Saves the changes to LDAP and returns the results.
     *
     * @return bool
     */
    public function save()
    {
        return $this->exists ? $this->update() : $this->create();
    }

    /**
     * Updates the model.
     *
     * @return bool
     */
    public function update()
    {
        $modifications = $this->getModifications();

        if (count($modifications) > 0) {
            // Push the update.
            if ($this->query->getConnection()->modifyBatch($this->getDn(), $modifications)) {
                // Re-sync attributes.
                $this->syncRaw();

                return true;
            }

            // Modification failed, return false.
            return false;
        }

        // We need to return true here because modify batch will
        // return false if no modifications are made
        // but this may not always be the case.
        return true;
    }

    /**
     * Creates the entry in LDAP.
     *
     * @return bool
     */
    public function create()
    {
        if (empty($this->getDn())) {
            // If the model doesn't currently have a DN,
            // we'll create a new one automatically.
            $dn = $this->getDnBuilder();

            // We'll set the base of the DN to the query's base DN.
            $dn->setBase($this->query->getDn());

            // Then we'll add the entry's common name attribute.
            $dn->addCn($this->getCommonName());

            // Set the new DN.
            $this->setDn($dn);
        }

        // Get the model attributes without its distinguished name.
        $attributes = Arr::except($this->getAttributes(), [$this->schema->distinguishedName()]);

        // Create the entry.
        $created = $this->query->getConnection()->add($this->getDn(), $attributes);

        if ($created) {
            // If the entry was created we'll re-sync
            // the models attributes from AD.
            $this->syncRaw();

            return true;
        }

        return false;
    }

    /**
     * Creates an attribute on the current model.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function createAttribute($attribute, $value)
    {
        if ($this->exists) {
            return $this->query->getConnection()->modAdd($this->getDn(), [$attribute => $value]);
        }

        return false;
    }

    /**
     * Updates the specified attribute with the specified value.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function updateAttribute($attribute, $value)
    {
        if (
            $this->exists &&
            $this->query->getConnection()->modReplace($this->getDn(), [$attribute => $value])
        ) {
            // If the models attribute was successfully updated,
            // we'll re-sync the models attributes.
            $this->syncRaw();

            return true;
        }

        return false;
    }

    /**
     * Deletes an attribute on the current entry.
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function deleteAttribute($attribute)
    {
        // We need to pass in an empty array as the value
        // for the attribute so AD knows to remove it.
        if (
            $this->exists &&
            $this->query->getConnection()->modDelete($this->getDn(), [$attribute => []])
        ) {
            // If the models attribute was successfully deleted, we'll
            // resynchronize the models raw attributes.
            $this->syncRaw();

            return true;
        }

        return false;
    }

    /**
     * Deletes the current entry.
     *
     * @throws ModelNotFoundException
     * @throws AdldapException
     *
     * @return bool
     */
    public function delete()
    {
        $dn = $this->getDn();

        if ($this->exists === false) {
            // Make sure the record exists before we can delete it.
            // Otherwise, we'll throw an exception.
            throw (new ModelDoesNotExistException())->setModel(get_class($this));
        }

        if (empty($dn)) {
            // If the record exists but the DN attribute does
            // not exist, we can't process a delete.
            throw new AdldapException('Unable to delete. The current model does not have a distinguished name.');
        }

        if ($this->query->getConnection()->delete($dn)) {
            // We'll set the exists property to false on delete
            // so the dev can run create operations.
            $this->exists = false;

            return true;
        }

        return false;
    }

    /**
     * Moves the current model to a new RDN and new parent.
     *
     * @param string      $rdn
     * @param string|null $newParentDn
     * @param bool|true   $deleteOldRdn
     *
     * @return bool
     */
    public function move($rdn, $newParentDn = null, $deleteOldRdn = true)
    {
        $moved = $this->query->getConnection()->rename($this->getDn(), $rdn, $newParentDn, $deleteOldRdn);

        if ($moved) {
            // If the model was successfully moved, we'll set its
            // new DN so we can sync it's attributes properly.
            $this->setDn("{$rdn},{$newParentDn}");

            $this->syncRaw();

            return true;
        }

        return false;
    }

    /**
     * Alias for the move method.
     *
     * @param string $rdn
     *
     * @return bool
     */
    public function rename($rdn)
    {
        return $this->move($rdn);
    }

    /**
     * Returns a normalized attribute key.
     *
     * @param string $key
     *
     * @return string
     */
    protected function normalizeAttributeKey($key)
    {
        return strtolower($key);
    }
    
    /**
     * Determine if the new and old values for a given key are numerically equivalent.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function originalIsNumericallyEquivalent($key)
    {
        $current = $this->attributes[$key];

        $original = $this->original[$key];

        return  is_numeric($current) &&
                is_numeric($original) &&
                strcmp((string) $current, (string) $original) === 0 ||
            count(array_diff($current, $original)) === 0;
    }

    /**
     * Converts the inserted string boolean to a PHP boolean.
     *
     * @param string $bool
     *
     * @return null|bool
     */
    protected function convertStringToBool($bool)
    {
        $bool = strtoupper($bool);

        if ($bool === strtoupper($this->schema->false())) {
            return false;
        } elseif ($bool === strtoupper($this->schema->true())) {
            return true;
        } else {
            return;
        }
    }
}
