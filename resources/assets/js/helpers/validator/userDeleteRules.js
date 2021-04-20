import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateUserDeleteSettings (data){

    const { assign, condition } = data;

    let validatingData = {};
    
    if(data.condition == 'change_owner'){
       
      validatingData['assign'] = [data.assign,'isRequired'];
       
    }

    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};