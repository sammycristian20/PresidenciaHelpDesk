import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateKbTagSettings(data){

    const { name } = data;

    let validatingData = {

        name: [name, 'isRequired', { 'max(20)' : 'The name should be less than 20 characters.'}]
    };

    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return {errors, isValid};
};