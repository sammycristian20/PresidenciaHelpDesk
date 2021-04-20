import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateTicketSettings(data){

    const { ticket_number_prefix } = data;

    let validatingData = {

        ticket_number_prefix: [ticket_number_prefix, 'isRequired']
    };
    
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};