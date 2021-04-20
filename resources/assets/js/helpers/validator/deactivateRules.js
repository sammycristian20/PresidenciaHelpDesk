import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateDeactivateSettings (data){

    const { assign, condition, req_condition, req_assign } = data;

    let validatingData = {};
    
    if(data.condition == 'change_assignee'){
       
      validatingData['assign'] = [data.assign,'isRequired'];
       
    }

    if(data.req_condition == 'change_owner'){
       
      validatingData['req_assign'] = [data.req_assign,'isRequired'];
       
    }

    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};