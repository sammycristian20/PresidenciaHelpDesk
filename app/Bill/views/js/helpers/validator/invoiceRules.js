
import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';


export function validateInvoiceSettings(data){

    const { packages } = data;

    let validatingData = {};

    if(data.packages){

        validatingData['price'] = [data.packages.price, 'isRequired']
    }
   
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};

