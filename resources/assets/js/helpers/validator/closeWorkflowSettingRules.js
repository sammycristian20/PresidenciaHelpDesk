import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateCloseWorkflowSettings(data){

    const { days, ticket_status, status } = data;

    let validatingData = {
        
        days : [days, 'isRequired'],
        
        ticket_status : [ticket_status, 'isRequired'],

        status : [status, 'isRequired']
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};
