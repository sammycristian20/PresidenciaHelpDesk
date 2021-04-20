import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateUserStatusSettings (data){

    const { email, mobile, modal } = data;

    let validatingData = {};
    
    if(modal === 'email_verified'){
       
      validatingData['email'] = [email,'isRequired','isEmail'];
       
    }

    if(modal === 'mobile_verified'){
       
      validatingData['mobile'] = [mobile,'isRequired'];
       
    }
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
}
