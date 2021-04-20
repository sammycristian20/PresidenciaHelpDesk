<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Base model to which all model should extend
 *
 * @author Avinash Kumar <avinash.kumar@ladybirdweb.com>
 */
class BaseModel extends Model
{
    /**
     * NOTE: for html purification to work, child model should create a property called `htmlAble` and
     * add the attribute which requires purification
     * @inheritDoc
     */
    public function setAttribute($key, $value)
    {
        if($this->htmlAble && in_array($key, $this->htmlAble)){
            $value = clean($value);

            // updating attribute in case no attribute is defined in child class
            $this->attributes[$key] = $value;
        }

        // passing updated value to child class attribute method for further processing
        parent::setAttribute($key, $value);
    }

    /**
     * Saves without firing an event (useful when we want to avoid execution of workflow/listener/SLA)
     * @param array $options
     * @return mixed
     */
    public function saveQuietly()
    {
        return static::withoutEvents(function () {
            // NOTE: save method has already been spoiled by Ticket_Thread model by overriding it,
            // so calling parent save method directly
            return parent::save($this->getDirty());
        });
    }
}
