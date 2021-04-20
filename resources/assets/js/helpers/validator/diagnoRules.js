import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateDiagnoSettings(data){

    const { from, to, subject, message } = data;

    let validatingData = {
        
        from: [from, 'isRequired'],
        
        to: [to, 'isRequired', 'isEmail'],

        subject: [subject, 'isRequired'],

        message: [message, 'isRequired'],
    };
    
    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};
