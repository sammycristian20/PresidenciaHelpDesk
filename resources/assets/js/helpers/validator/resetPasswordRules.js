import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateResetPasswordSettings(data){

    const { password, repeat } = data;

    let validatingData = {
        
        password: [password, 'isRequired','min(6)','max(20)'],

        repeat: [repeat, 'isRequired','min(6)','max(20)'],
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};