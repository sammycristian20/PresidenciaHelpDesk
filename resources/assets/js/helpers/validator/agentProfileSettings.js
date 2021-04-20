import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateAgentProfileSettings(data){

    const { first_name } = data;

    let validatingData = {

        first_name: [first_name, 'isRequired']
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};