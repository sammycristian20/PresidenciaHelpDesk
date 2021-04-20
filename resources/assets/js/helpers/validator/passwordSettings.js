import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validatePasswordSettings(data){

    const { old_password, new_password, confirm_password } = data;

    let validatingData = {

        old_password: [old_password, 'isRequired'],

        // new_password: [new_password, 'isRequired','min(6)','max(20)','shouldNotMatch("'+ old_password+'")'],
        
        // confirm_password: [confirm_password, 'isRequired','min(6)','max(20)' , 'passwordMatch("'+ new_password+'")'],
        
        new_password: [new_password, 'isRequired','min(6)','max(20)'],

        confirm_password: [confirm_password, 'isRequired','min(6)','max(20)'],
        

    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};