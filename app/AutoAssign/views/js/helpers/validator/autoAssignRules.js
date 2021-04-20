import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateAutoAssignSettings(data){

    const { threshold, assign_department_option, department_list } = data;

    let validatingData = {};

    if(data.assign_department_option === 'specific'){

        validatingData['department_list'] = [data.department_list, 'isRequired']
    }
   
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};