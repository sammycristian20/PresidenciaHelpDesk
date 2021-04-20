/**
 * This file contains all the validation rules specific to form.
 *
 * */

import {store} from "store";
import {Validator} from 'easy-validator-js';
import {lang} from 'helpers/extraLogics';

export function validateFormGroup(data){

    const { formNameObj } = data;

    //rules has to apply only after checking conditions
    let validatingData = {
        name: [formNameObj.value, 'isRequired', 'max(50)', 'min(3)'],
    };
    
    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};