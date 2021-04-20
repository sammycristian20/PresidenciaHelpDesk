/**
 * This file contains all the validation rules specific to form.
 *
 * RULES : method name for the form should be 'validateFormName'
 * */

import {store} from "store";
import {Validator} from 'easy-validator-js';
import {lang} from 'helpers/extraLogics';

/**
 * @param {object} data      emailSettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
export function validateTemplateCreate(data){


    const { description, name,order} = data;

    //rules has to apply only after checking conditions
    let validatingData = {
        description: [description, 'isRequired'],
        name: [name, 'isRequired'],
    };
    
    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
}

