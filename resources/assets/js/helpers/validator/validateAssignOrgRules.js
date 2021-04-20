import {store} from "../../../store/store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateAssignOrgSettings(data){
    
    const { organizations } = data;
    
    let validatingData = {
       
        organizations: [organizations, 'isRequired'],
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
}