import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateKbSettings(data){

    const { pagination} = data;

    let validatingData = {

        pagination : [ pagination, 'isRequired', 'maxValue(15)','minValue(10)'],
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); 
  
    return {errors, isValid};
};

