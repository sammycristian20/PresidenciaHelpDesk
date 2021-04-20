<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Lang;

trait FormFieldValidator
{
    /**
     * Validates form fields
     * @param  array $formField
     * @return null
     */
    private function validateFormField($formField)
    {
        // loop over it and see if any label is empty
        foreach ($this->getValueFromArray($formField, 'labels_for_form_field') as $label) {
            $this->validateLabel($this->getValueFromArray($label, 'label', 'string'));
        }

        // loop over each option's node and back to
        foreach ($this->getValueFromArray($formField, 'options') as $option) {

            // inside options there are labels which cannot be empty
            foreach ($this->getValueFromArray($option, 'labels') as $label) {
                $this->validateLabel($this->getValueFromArray($label, 'label', 'string'), 'option');
            }

            foreach ($this->getValueFromArray($option, 'nodes') as $node) {
                $this->validateFormField($node);
            }
        }
    }

    /**
     * Validates label, if it is empty
     * @param  string $label type can be a label or option
     * @return null
     */
    private function validateLabel($label, $type = 'label')
    {
        $label = trim($label);
        if($label == ''){
            throw new HttpResponseException(errorResponse(Lang::get('lang.'.$type.'_cannot_be_empty'), 400));
        }
    }

    /**
     * gets value from the array based on key and the type of output needed by checking if the key exists
     * NOTE: this method has been made to handle the case where keys in the array is not set, to avoid
     * unwanted exception
     * @param  array  $array the haystack array
     * @param  string $key   key whose value has to be extracted from array
     * @param  string $type  `string`|`array`. If passed as array, it will return array else string ( useful in case where key is not set)
     * @return string|array
     */
    private function getValueFromArray(array $array, string $key = '', string $type = 'array')
    {
        if(!isset($array[$key])){
            if($type == 'array'){
                return [];
            }
            return '';
        }

        return $array[$key];
    }

}