import {store} from "../../../store/store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateUserRoleSettings(data){
    
    const { departments } = data;
    
    let validatingData = {
       
        departments: [departments, 'isRequired'],
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
}