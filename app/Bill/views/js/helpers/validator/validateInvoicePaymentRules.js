
import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';


export function validateInvoicePaymentSettings(data){

    const { gateway, transaction_id, amount } = data;

    let validatingData = {

        gateway: [gateway, 'isRequired'],
        
        transaction_id: [transaction_id, 'isRequired'],

        amount: [amount, 'isRequired'],
    };
   
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};

