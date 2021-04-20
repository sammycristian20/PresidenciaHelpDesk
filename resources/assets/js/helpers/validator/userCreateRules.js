import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateUserCreateSettings(data){

    const { first_name, user_name, email, organization, ext, phone_number } = data;

    let validatingData = {

        first_name : [first_name, 'isRequired'],
        
        user_name : [user_name, 'isRequired'],

        organization : [organization, 'isRequired'],

        email : [email, 'isRequired', 'isEmail'],

        ext : [ext, 'max(5)'],

        phone_number : [phone_number, 'max(15)'], 
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};