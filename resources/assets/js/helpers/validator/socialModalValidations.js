/**
 * This file contains all the validation rules specific to form -- Create Update Agent.
 */
 
import { store } from "store";
import { Validator } from 'easy-validator-js';
import { lang } from 'helpers/extraLogics';

export function validateSocialModalFormFields(data){ 

    const { value } = data;

    //rules has to apply only after checking conditions
    let validatingData = {
        value: [value,  'isUrl'],
        
    };
    
    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); 
  
    return {errors, isValid};
}
