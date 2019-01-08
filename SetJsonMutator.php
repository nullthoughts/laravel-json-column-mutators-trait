<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SetJsonMutator
{
    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // the model, such as "json_encoding" an listing of data for storage.
        if ($this->hasSetMutator($key)) {
            return $this->setMutatedAttributeValue($key, $value);
        }

        // If an attribute is listed as a "date", we'll convert it from a DateTime
        // instance into a form proper for storage on the database tables using
        // the connection grammar's date format. We will auto set the values.
        elseif ($value && $this->isDateAttribute($key)) {
            $value = $this->fromDateTime($value);
        }

        if ($this->isJsonCastable($key) && ! is_null($value)) {
            $value = $this->castAttributeAsJson($key, $value);
        }

        // If this attribute contains a JSON ->, we'll set the proper value in the
        // attribute's underlying array. This takes care of properly nesting an
        // attribute in the array's value in the case of deeply nested items.
        if (Str::contains($key, '->')) {
            $value = $this->getJsonValue($key, $value);

            return $this->fillJsonAttribute($key, $value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Get value for JSON column.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function getJsonValue($key, $value)
    {
        // Check for presence of JSON mutator (ex. the mutator for 'price->msrp'
        // would be SetPriceMsrpAttribute). If mutator exists, return 
        if ($jsonKey = $this->hasJsonSetMutator($key)) {
            $value = $this->setMutatedAttributeValue($jsonKey, $value);
        }

        return $value;
    }

    /**
     * Determine if a JSON set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return string|bool
     */
    protected function hasJsonSetMutator($key)
    {
        $jsonKey = str_replace('->', '_', $key);
        
        return $this->hasSetMutator($jsonKey) ? $jsonKey : false;
    }
}
