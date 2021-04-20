import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateMergeTicketSettings(data){


    const { title, parentTicket} = data;

    let validatingData = {
        parentTicket: [parentTicket, 'isRequired'],
        title: [title, 'isRequired'],
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
}